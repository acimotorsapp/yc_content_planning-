<?php

namespace Tests\Feature;

use App\Mail\EventNotificationMail;
use App\Mail\ManualEmailMail;
use App\Models\CalendarEvent;
use App\Models\EventNotificationDelivery;
use App\Models\Setting;
use App\Models\User;
use App\Providers\AppServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use RuntimeException;
use Tests\TestCase;

class EventNotificationSchedulerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_prepare_command_creates_one_pending_delivery_per_date_rule_for_events_five_days_ahead(): void
    {
        $this->assertFalse(Schema::hasColumn('event_notification_deliveries', 'user_id'));
        $this->assertTrue(Schema::hasColumn('event_notification_deliveries', 'user_ids'));

        Carbon::setTestNow(Carbon::parse('2026-09-21 09:00:00', 'Asia/Dhaka'));

        $userA = User::factory()->create(['email' => 'planner.a@aci-bd.com']);
        $userB = User::factory()->create(['email' => 'planner.b@aci-bd.com']);
        $blankEmailUser = User::factory()->create(['email' => 'blank@example.org']);
        $blankEmailUser->forceFill(['email' => ''])->save();

        $this->eventFor($userA, '2026-09-26', 'A first event');
        $this->eventFor($userA, '2026-09-26', 'A second event');
        $this->eventFor($userB, '2026-09-26', 'B event');
        $this->eventFor($blankEmailUser, '2026-09-26', 'No email event');
        $this->eventFor($userB, '2026-09-27', 'Wrong date event');

        $this->artisan('events:prepare-notifications')
            ->expectsOutput('Target date: 2026-09-26')
            ->expectsOutput('Matching events: 4')
            ->expectsOutput('Users: 2')
            ->expectsOutput('Pending created: 1')
            ->expectsOutput('Already existed: 1')
            ->assertSuccessful();

        $this->assertSame(1, EventNotificationDelivery::whereDate('target_date', '2026-09-26')->where('days_ahead', 5)->count());
        $this->assertSame(1, EventNotificationDelivery::count());
        $delivery = EventNotificationDelivery::first();
        $this->assertSame([$userA->id, $userB->id], $delivery->user_ids);

        $this->artisan('events:prepare-notifications')
            ->expectsOutput('Pending created: 0')
            ->expectsOutput('Already existed: 2')
            ->assertSuccessful();

        $this->assertSame(1, EventNotificationDelivery::count());
        $this->assertSame([$userA->id, $userB->id], $delivery->fresh()->user_ids);

        Carbon::setTestNow(Carbon::parse('2026-09-22 09:00:00', 'Asia/Dhaka'));

        $this->artisan('events:prepare-notifications')
            ->expectsOutput('Target date: 2026-09-27')
            ->expectsOutput('Pending created: 1')
            ->assertSuccessful();

        $this->assertSame(2, EventNotificationDelivery::count());
        $this->assertSame(
            [$userB->id],
            EventNotificationDelivery::whereDate('target_date', '2026-09-27')->first()->user_ids
        );
    }

    public function test_process_command_sends_only_one_pending_delivery_and_reuses_event_notification_mail(): void
    {
        Mail::fake();
        Queue::fake();

        config([
            'mail.default' => 'array',
            'mail.from.address' => 'configured.sender@example.test',
            'mail.from.name' => 'Configured Sender',
        ]);

        Setting::create(['key' => 'MAIL_CC_ADDRESS', 'value' => 'cc.one@aci-bd.com, cc.two@aci-bd.com']);

        $firstUser = User::factory()->create(['email' => 'first.user@aci-bd.com']);
        $secondUser = User::factory()->create(['email' => 'second.user@aci-bd.com']);

        $firstEvent = $this->eventFor($firstUser, '2026-09-26', 'Launch teaser');
        $secondEvent = $this->eventFor($firstUser, '2026-09-26', 'Launch reminder');
        $thirdEvent = $this->eventFor($secondUser, '2026-09-26', 'Second user event');
        $fromAddress = (string) config('mail.from.address');

        $firstDelivery = EventNotificationDelivery::create([
            'user_ids' => [$firstUser->id, $secondUser->id],
            'target_date' => '2026-09-26',
            'days_ahead' => 5,
            'status' => 'pending',
        ]);
        $this->artisan('events:process-notifications')
            ->expectsOutput("Processing notification #{$firstDelivery->id}")
            ->expectsOutput('Recipient: sultana.nishi@aci-bd.com')
            ->expectsOutput('Target date: 2026-09-26')
            ->expectsOutput("Notification ID: {$firstDelivery->id}")
            ->expectsOutput('Related users: 2')
            ->expectsOutput('Events included in email: 3')
            ->expectsOutput("From: {$fromAddress}")
            ->expectsOutput('To: sultana.nishi@aci-bd.com')
            ->expectsOutput('CC:')
            ->expectsOutput('- cc.one@aci-bd.com')
            ->expectsOutput('- cc.two@aci-bd.com')
            ->expectsOutput('Status: submitted-to-smtp')
            ->assertSuccessful();

        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $firstDelivery->id,
            'status' => 'sent',
            'attempts' => 1,
            'last_error' => null,
        ]);
        $this->assertSame(1, EventNotificationDelivery::count());

        Mail::assertSent(EventNotificationMail::class, function (EventNotificationMail $mail) use ($firstUser, $firstEvent, $secondEvent, $thirdEvent) {
            $eventIds = $mail->events->pluck('id')->all();
            $html = $mail->render();

            return $mail->hasTo('sultana.nishi@aci-bd.com')
                && ! $mail->hasTo('first.user@aci-bd.com')
                && $mail->user->is($firstUser)
                && $mail->targetDate === '2026-09-26'
                && $mail->daysAhead === 5
                && $mail->content()->view === 'emails.event_notification'
                && $mail->hasCc('cc.one@aci-bd.com')
                && $mail->hasCc('cc.two@aci-bd.com')
                && $mail->events->count() === 3
                && in_array($firstEvent->id, $eventIds, true)
                && in_array($secondEvent->id, $eventIds, true)
                && in_array($thirdEvent->id, $eventIds, true)
                && str_contains($html, 'Launch teaser')
                && str_contains($html, 'Launch reminder')
                && str_contains($html, 'Second user event')
                && str_contains($html, '>3</strong> content event(s)');
        });
        Mail::assertNotSent(EventNotificationMail::class, fn (EventNotificationMail $mail) => $mail->hasTo('second.user@aci-bd.com'));
        Queue::assertNothingPushed();
    }

    public function test_scheduled_notification_to_is_fixed_and_cc_is_preserved(): void
    {
        Mail::fake();

        Setting::create(['key' => 'MAIL_CC_ADDRESS', 'value' => 'cc.one@aci-bd.com, cc.two@aci-bd.com']);

        $user = User::factory()->create(['email' => 'different.user@aci-bd.com']);
        $this->eventFor($user, '2026-09-26', 'Fixed recipient event');

        EventNotificationDelivery::create([
            'user_ids' => [$user->id],
            'target_date' => '2026-09-26',
            'days_ahead' => 5,
            'status' => 'pending',
        ]);

        $this->artisan('events:process-notifications')
            ->expectsOutput('Recipient: sultana.nishi@aci-bd.com')
            ->expectsOutput('To: sultana.nishi@aci-bd.com')
            ->expectsOutput('- cc.one@aci-bd.com')
            ->expectsOutput('- cc.two@aci-bd.com')
            ->assertSuccessful();

        Mail::assertSent(EventNotificationMail::class, function (EventNotificationMail $mail) {
            return $mail->hasTo('sultana.nishi@aci-bd.com')
                && ! $mail->hasTo('different.user@aci-bd.com')
                && $mail->hasCc('cc.one@aci-bd.com')
                && $mail->hasCc('cc.two@aci-bd.com');
        });
    }

    public function test_duplicate_delivery_rows_are_merged_before_unique_date_rule_constraint(): void
    {
        Mail::fake();

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP INDEX event_notification_deliveries_unique');
        } else {
            DB::statement('ALTER TABLE event_notification_deliveries DROP INDEX event_notification_deliveries_unique');
        }

        $firstUser = User::factory()->create(['email' => 'merge.one@aci-bd.com']);
        $secondUser = User::factory()->create(['email' => 'merge.two@aci-bd.com']);
        $sentAt = now()->subMinute();

        $sent = EventNotificationDelivery::create([
            'user_ids' => [$firstUser->id],
            'target_date' => '2026-09-27',
            'days_ahead' => 5,
            'status' => 'sent',
            'attempts' => 1,
            'sent_at' => $sentAt,
        ]);
        EventNotificationDelivery::create([
            'user_ids' => [$secondUser->id, $secondUser->id, $firstUser->id],
            'target_date' => '2026-09-27',
            'days_ahead' => 5,
            'status' => 'processing',
            'attempts' => 3,
            'processing_started_at' => now(),
            'last_error' => 'Old processing row',
        ]);

        $migration = include database_path('migrations/2026_09_22_000001_dedupe_event_notification_deliveries_by_date_rule.php');
        $migration->up();

        $this->assertSame(1, EventNotificationDelivery::whereDate('target_date', '2026-09-27')->where('days_ahead', 5)->count());
        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $sent->id,
            'days_ahead' => 5,
            'status' => 'sent',
            'attempts' => 3,
            'last_error' => null,
        ]);
        $this->assertNotNull(EventNotificationDelivery::find($sent->id)->sent_at);
        $this->assertSame([$firstUser->id, $secondUser->id], EventNotificationDelivery::find($sent->id)->user_ids);
        Mail::assertNothingSent();
    }

    public function test_database_smtp_settings_do_not_override_laravel_mail_config(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'env.smtp.example.test',
            'mail.mailers.smtp.port' => 465,
            'mail.mailers.smtp.username' => 'env-user@example.test',
            'mail.mailers.smtp.password' => 'env-secret',
            'mail.mailers.smtp.scheme' => 'smtps',
            'mail.from.address' => 'configured.sender@example.test',
            'mail.from.name' => 'Configured Sender',
        ]);

        foreach ([
            'MAIL_MAILER' => 'log',
            'MAIL_HOST' => 'database.smtp.example.test',
            'MAIL_PORT' => '2525',
            'MAIL_USERNAME' => 'database-user@example.test',
            'MAIL_PASSWORD' => 'database-secret',
            'MAIL_SCHEME' => 'smtp',
            'MAIL_FROM_ADDRESS' => 'planning@yrc-bd.com',
            'MAIL_FROM_NAME' => 'Database Sender',
        ] as $key => $value) {
            Setting::create(compact('key', 'value'));
        }

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('env.smtp.example.test', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('env-user@example.test', config('mail.mailers.smtp.username'));
        $this->assertSame('env-secret', config('mail.mailers.smtp.password'));
        $this->assertSame('smtps', config('mail.mailers.smtp.scheme'));
        $this->assertSame('configured.sender@example.test', config('mail.from.address'));
        $this->assertSame('Configured Sender', config('mail.from.name'));
    }

    public function test_manual_mail_uses_global_from_and_ignores_database_from_setting(): void
    {
        Mail::fake();

        config([
            'mail.from.address' => 'configured.sender@example.test',
            'mail.from.name' => 'Configured Sender',
        ]);

        Setting::create(['key' => 'MAIL_FROM_ADDRESS', 'value' => 'planning@yrc-bd.com']);
        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->post(route('admin.send-email.send'), [
                'to' => 'recipient@example.test',
                'subject' => 'Manual configured sender',
                'body' => 'Manual email body',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Mail::assertSent(ManualEmailMail::class, function (ManualEmailMail $mail) {
            return $mail->hasTo('recipient@example.test')
                && $mail->envelope()->from === null
                && config('mail.from.address') === 'configured.sender@example.test'
                && config('mail.from.address') !== 'planning@yrc-bd.com';
        });
    }

    public function test_event_notification_mail_uses_global_from_and_settings_cc(): void
    {
        config([
            'mail.from.address' => 'configured.sender@example.test',
            'mail.from.name' => 'Configured Sender',
        ]);

        Setting::create(['key' => 'MAIL_FROM_ADDRESS', 'value' => 'planning@yrc-bd.com']);
        Setting::create(['key' => 'MAIL_CC_ADDRESS', 'value' => 'cc.one@example.test, sultana.nishi@aci-bd.com']);

        $user = User::factory()->create(['email' => 'user@example.test']);
        $event = $this->eventFor($user, '2026-09-26', 'Configured sender event');
        $mail = new EventNotificationMail($user, CalendarEvent::whereKey($event->id)->get(), '2026-09-26', 5);

        $this->assertNull($mail->envelope()->from);
        $this->assertTrue($mail->hasCc('cc.one@example.test'));
        $this->assertFalse($mail->hasCc('sultana.nishi@aci-bd.com'));
        $this->assertSame('configured.sender@example.test', config('mail.from.address'));
        $this->assertNotSame('planning@yrc-bd.com', config('mail.from.address'));
    }

    public function test_cron_events_notify_uses_event_notification_mail_with_global_sender(): void
    {
        Mail::fake();
        Carbon::setTestNow(Carbon::parse('2026-09-21 09:00:00', 'Asia/Dhaka'));

        config([
            'mail.from.address' => 'configured.sender@example.test',
            'mail.from.name' => 'Configured Sender',
            'event_notifications.cron_token' => null,
        ]);

        Setting::create(['key' => 'MAIL_FROM_ADDRESS', 'value' => 'planning@yrc-bd.com']);
        Setting::create(['key' => 'MAIL_CC_ADDRESS', 'value' => 'cc.one@example.test']);

        $user = User::factory()->create(['email' => 'cron.user@example.net']);
        $this->eventFor($user, '2026-09-26', 'Cron notification event');

        $this->get(route('cron.events.notify', ['force' => '1']))
            ->assertOk()
            ->assertJsonPath('data.sent_count', 1);

        Mail::assertSent(EventNotificationMail::class, function (EventNotificationMail $mail) {
            return $mail->hasTo('sultana.nishi@aci-bd.com')
                && ! $mail->hasTo('cron.user@example.net')
                && $mail->hasCc('cc.one@example.test')
                && $mail->envelope()->from === null
                && config('mail.from.address') === 'configured.sender@example.test';
        });
    }

    public function test_settings_page_only_edits_cc_and_does_not_expose_smtp_secrets(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        Setting::create(['key' => 'MAIL_PASSWORD', 'value' => 'database-secret-password']);
        Setting::create(['key' => 'MAIL_CC_ADDRESS', 'value' => 'cc.one@example.test']);

        $this->actingAs($admin)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('SMTP server and sender configuration are managed from the application environment (.env).')
            ->assertSee('name="MAIL_CC_ADDRESS"', false)
            ->assertDontSee('name="MAIL_PASSWORD"', false)
            ->assertDontSee('database-secret-password')
            ->assertDontSee('name="MAIL_HOST"', false)
            ->assertDontSee('name="MAIL_USERNAME"', false)
            ->assertDontSee('name="MAIL_FROM_ADDRESS"', false);
    }

    public function test_settings_update_only_changes_mail_cc_address(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        Setting::create(['key' => 'MAIL_HOST', 'value' => 'database.smtp.example.test']);
        Setting::create(['key' => 'MAIL_PASSWORD', 'value' => 'database-secret-password']);

        $this->actingAs($admin)
            ->post(route('admin.settings.update'), [
                'MAIL_HOST' => 'posted.smtp.example.test',
                'MAIL_PASSWORD' => 'posted-secret-password',
                'MAIL_FROM_ADDRESS' => 'planning@yrc-bd.com',
                'MAIL_CC_ADDRESS' => 'cc.updated@example.test',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('settings', ['key' => 'MAIL_CC_ADDRESS', 'value' => 'cc.updated@example.test']);
        $this->assertDatabaseHas('settings', ['key' => 'MAIL_HOST', 'value' => 'database.smtp.example.test']);
        $this->assertDatabaseHas('settings', ['key' => 'MAIL_PASSWORD', 'value' => 'database-secret-password']);
        $this->assertDatabaseMissing('settings', ['key' => 'MAIL_FROM_ADDRESS', 'value' => 'planning@yrc-bd.com']);
    }

    public function test_event_notification_mail_removes_primary_to_recipient_from_settings_cc(): void
    {
        Setting::create([
            'key' => 'MAIL_CC_ADDRESS',
            'value' => 'sultana.nishi@aci-bd.com, rusdaislamtoma@gmail.com',
        ]);

        $user = User::factory()->create(['email' => 'oshin@aci-bd.com']);
        $event = $this->eventFor($user, '2026-09-26', 'Recipient filter event');
        $mail = new EventNotificationMail($user, CalendarEvent::whereKey($event->id)->get(), '2026-09-26', 5);

        $this->assertFalse($mail->hasCc('sultana.nishi@aci-bd.com'));
        $this->assertTrue($mail->hasCc('rusdaislamtoma@gmail.com'));
        $this->assertSame(['rusdaislamtoma@gmail.com'], EventNotificationMail::getDefaultCcRecipients('sultana.nishi@aci-bd.com'));
    }

    public function test_event_notification_mail_keeps_different_to_recipient_in_settings_cc(): void
    {
        Setting::create([
            'key' => 'MAIL_CC_ADDRESS',
            'value' => 'oshin@aci-bd.com, rusdaislamtoma@gmail.com',
        ]);

        $user = User::factory()->create(['email' => 'Daief@aci-bd.com']);
        $event = $this->eventFor($user, '2026-09-26', 'Different recipient event');
        $mail = new EventNotificationMail($user, CalendarEvent::whereKey($event->id)->get(), '2026-09-26', 5);

        $this->assertTrue($mail->hasCc('oshin@aci-bd.com'));
        $this->assertTrue($mail->hasCc('rusdaislamtoma@gmail.com'));
        $this->assertSame(
            ['oshin@aci-bd.com', 'rusdaislamtoma@gmail.com'],
            EventNotificationMail::getDefaultCcRecipients('Daief@aci-bd.com')
        );
    }

    public function test_event_notification_mail_removes_duplicate_cc_case_insensitively(): void
    {
        Setting::create([
            'key' => 'MAIL_CC_ADDRESS',
            'value' => 'OSHIN@aci-bd.com, oshin@aci-bd.com, rusdaislamtoma@gmail.com, RUSDAISLAMTOMA@gmail.com, invalid-address',
        ]);

        $this->assertSame(
            ['rusdaislamtoma@gmail.com'],
            EventNotificationMail::getDefaultCcRecipients('oshin@aci-bd.com')
        );

        $this->assertSame(
            ['OSHIN@aci-bd.com', 'rusdaislamtoma@gmail.com'],
            EventNotificationMail::getDefaultCcRecipients('Daief@aci-bd.com')
        );
    }

    public function test_manual_send_email_still_sends_with_normalized_cc(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->post(route('admin.send-email.send'), [
                'to' => 'oshin@aci-bd.com',
                'cc' => [
                    'oshin@aci-bd.com',
                    ' rusdaislamtoma@gmail.com ',
                    'RUSDAISLAMTOMA@gmail.com',
                    '',
                ],
                'subject' => 'Manual check',
                'body' => 'Manual email body',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Mail::assertSent(ManualEmailMail::class, function (ManualEmailMail $mail) {
            return $mail->hasTo('oshin@aci-bd.com')
                && ! $mail->hasCc('oshin@aci-bd.com')
                && $mail->hasCc('rusdaislamtoma@gmail.com')
                && ! $mail->hasCc('RUSDAISLAMTOMA@gmail.com');
        });
    }

    public function test_event_notification_mail_uses_existing_event_show_route_for_details_button(): void
    {
        config(['app.url' => 'http://localhost/laravel/YRCPlan/public']);
        URL::forceRootUrl(config('app.url'));

        $user = User::factory()->create(['email' => 'route.user@aci-bd.com']);
        $this->eventFor(User::factory()->create(), '2026-09-25', 'Different event');
        $event = $this->eventFor($user, '2026-09-26', 'Route check event');
        $expectedUrl = route('events.show', ['event' => $event->id]);

        $html = (new EventNotificationMail($user, CalendarEvent::whereKey($event->id)->get(), '2026-09-26', 5))->render();

        $this->assertSame("http://localhost/laravel/YRCPlan/public/events/{$event->id}", $expectedUrl);
        $this->assertStringContainsString('View Event Details', $html);
        $this->assertStringContainsString('href="'.$expectedUrl.'"', $html);
        $this->assertStringNotContainsString("/events/{$user->id}\"", $html);

        URL::forceRootUrl(null);

        $this->actingAs($user)
            ->get("/events/{$event->id}")
            ->assertOk()
            ->assertViewIs('events.show');
    }

    public function test_process_command_marks_mail_exceptions_as_failed_with_last_error(): void
    {
        $user = User::factory()->create(['email' => 'failure.user@aci-bd.com']);
        $this->eventFor($user, '2026-09-26', 'Failure event');

        $delivery = EventNotificationDelivery::create([
            'user_ids' => [$user->id],
            'target_date' => '2026-09-26',
            'days_ahead' => 5,
            'status' => 'pending',
        ]);

        Mail::shouldReceive('to')
            ->once()
            ->with('sultana.nishi@aci-bd.com')
            ->andReturnSelf();
        Mail::shouldReceive('send')
            ->once()
            ->andThrow(new RuntimeException('SMTP recipient rejected'));

        $this->artisan('events:process-notifications')
            ->expectsOutput("Processing notification #{$delivery->id}")
            ->expectsOutput('Status: failed')
            ->expectsOutput('Reason: SMTP recipient rejected')
            ->assertSuccessful();

        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $delivery->id,
            'status' => 'failed',
            'attempts' => 1,
            'last_error' => 'SMTP recipient rejected',
            'sent_at' => null,
        ]);
    }

    public function test_process_command_uses_fixed_recipient_and_does_not_automatically_retry_failed_records(): void
    {
        Mail::fake();

        $dummyUser = User::factory()->create(['email' => 'dummy@example.com']);
        $failedUser = User::factory()->create(['email' => 'failed.user@aci-bd.com']);
        $pendingUser = User::factory()->create(['email' => 'pending.user@aci-bd.com']);

        $this->eventFor($dummyUser, '2026-09-26', 'Dummy event');
        $this->eventFor($failedUser, '2026-09-27', 'Failed event');
        $this->eventFor($pendingUser, '2026-09-28', 'Pending event');

        $dummyDelivery = EventNotificationDelivery::create([
            'user_ids' => [$dummyUser->id],
            'target_date' => '2026-09-26',
            'days_ahead' => 5,
            'status' => 'pending',
        ]);
        $failedDelivery = EventNotificationDelivery::create([
            'user_ids' => [$failedUser->id],
            'target_date' => '2026-09-27',
            'days_ahead' => 5,
            'status' => 'failed',
            'attempts' => 3,
            'last_error' => 'Previous SMTP error',
        ]);
        EventNotificationDelivery::create([
            'user_ids' => [$pendingUser->id],
            'target_date' => '2026-09-28',
            'days_ahead' => 5,
            'status' => 'pending',
        ]);

        $this->artisan('events:process-notifications')
            ->expectsOutput("Processing notification #{$dummyDelivery->id}")
            ->expectsOutput('Recipient: sultana.nishi@aci-bd.com')
            ->expectsOutput('Status: submitted-to-smtp')
            ->assertSuccessful();

        Mail::assertSent(EventNotificationMail::class, function (EventNotificationMail $mail) {
            return $mail->hasTo('sultana.nishi@aci-bd.com')
                && ! $mail->hasTo('dummy@example.com');
        });
        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $dummyDelivery->id,
            'status' => 'sent',
            'last_error' => null,
        ]);
        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $failedDelivery->id,
            'status' => 'failed',
            'attempts' => 3,
            'last_error' => 'Previous SMTP error',
        ]);

        $this->artisan('events:process-notifications')->assertSuccessful();

        Mail::assertSent(EventNotificationMail::class, 2);
        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $failedDelivery->id,
            'status' => 'failed',
            'attempts' => 3,
        ]);
    }

    public function test_notification_log_retry_only_resets_failed_records_to_pending(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create(['email' => 'retry.user@aci-bd.com']);

        $failed = EventNotificationDelivery::create([
            'user_ids' => [$user->id],
            'target_date' => '2026-09-26',
            'days_ahead' => 5,
            'status' => 'failed',
            'attempts' => 2,
            'last_error' => 'SMTP failed',
        ]);
        $sent = EventNotificationDelivery::create([
            'user_ids' => [$user->id],
            'target_date' => '2026-09-27',
            'days_ahead' => 5,
            'status' => 'sent',
            'attempts' => 1,
            'sent_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.notification-logs.retry', $failed))
            ->assertRedirect();

        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $failed->id,
            'status' => 'pending',
            'attempts' => 2,
            'last_error' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.notification-logs.retry', $sent))
            ->assertRedirect();

        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $sent->id,
            'status' => 'sent',
            'attempts' => 1,
        ]);
    }

    public function test_stale_processing_records_are_recovered_before_claiming_next_delivery(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'stale.user@aci-bd.com']);
        $this->eventFor($user, '2026-09-26', 'Recovered event');

        $delivery = EventNotificationDelivery::create([
            'user_ids' => [$user->id],
            'target_date' => '2026-09-26',
            'days_ahead' => 5,
            'status' => 'processing',
            'attempts' => 1,
            'processing_started_at' => now()->subMinutes(20),
        ]);

        $this->artisan('events:process-notifications')
            ->expectsOutput("Processing notification #{$delivery->id}")
            ->expectsOutput('Status: submitted-to-smtp')
            ->assertSuccessful();

        $this->assertDatabaseHas('event_notification_deliveries', [
            'id' => $delivery->id,
            'status' => 'sent',
            'attempts' => 2,
        ]);
        Mail::assertSent(EventNotificationMail::class, 1);
    }

    public function test_repeated_process_executions_do_not_resend_an_already_sent_delivery(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'single.user@aci-bd.com']);
        $this->eventFor($user, '2026-09-26', 'Single event');

        EventNotificationDelivery::create([
            'user_ids' => [$user->id],
            'target_date' => '2026-09-26',
            'days_ahead' => 5,
            'status' => 'pending',
        ]);

        $this->artisan('events:process-notifications')->assertSuccessful();
        $this->artisan('events:process-notifications')
            ->expectsOutput('No pending event notifications found.')
            ->assertSuccessful();

        Mail::assertSent(EventNotificationMail::class, 1);
    }

    private function eventFor(User $user, string $date, string $title): CalendarEvent
    {
        return CalendarEvent::create([
            'user_id' => $user->id,
            'team_type' => 'product_team',
            'event_date' => $date,
            'content_title' => $title,
            'content_objective' => "Objective for {$title}",
            'format' => 'Static',
        ]);
    }
}
