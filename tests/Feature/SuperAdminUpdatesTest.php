<?php

namespace Tests\Feature;

use App\Mail\EventNotificationMail;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Services\EventNotificationService;
use App\Services\GoogleSheetsSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SuperAdminUpdatesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'email' => 'admin@example.org',
            'email_verified_at' => now(),
        ]);
    }

    public function test_dashboard_kpis_and_tracker_follow_the_month_filter(): void
    {
        $admin = $this->admin();

        CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'digital_team',
            'event_date' => '2026-09-10',
            'content_title' => 'September Post',
            'status' => 'done',
        ]);
        CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'product_team',
            'event_date' => '2026-10-04',
            'content_title' => 'October Shoot',
            'status' => 'not_done',
        ]);

        $september = $this->actingAs($admin)->get('/dashboard?month=2026-09');
        $september->assertOk();
        $september->assertSee('Month filter');
        $september->assertSee('Budget Provision');
        $september->assertSee('(Sep)');
        $this->assertSame(1, $september->viewData('monthEvents')->count());
        $this->assertSame('September Post', $september->viewData('tableEvents')->first()->content_title);
        $this->assertCount(2, $september->viewData('events'));

        $october = $this->actingAs($admin)->get('/dashboard?month=2026-10');
        $october->assertOk();
        $october->assertSee('(Oct)');
        $this->assertSame(1, $october->viewData('monthEvents')->count());
        $this->assertSame('October Shoot', $october->viewData('tableEvents')->first()->content_title);
        $this->assertStringContainsString('October Shoot', $october->getContent());
        $this->assertStringNotContainsString('September Post', $october->viewData('tableEvents')->pluck('content_title')->implode(' '));
    }

    public function test_super_admin_can_edit_a_content_title_inline(): void
    {
        $admin = $this->admin();
        $event = CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'digital_team',
            'event_date' => now()->toDateString(),
            'content_title' => 'Old Title',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson(route('events.update_title', $event), [
                'content_title' => 'New Campaign Title',
            ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'content_title' => 'New Campaign Title',
        ]);
        $this->assertSame('New Campaign Title', $event->fresh()->content_title);
    }

    public function test_deleting_from_the_show_page_returns_to_the_dashboard(): void
    {
        $admin = $this->admin();
        $event = CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'product_team',
            'event_date' => now()->toDateString(),
            'content_title' => 'Delete Me',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('events.show', $event))
            ->delete(route('events.destroy', $event));

        $response->assertRedirect(route('dashboard'));
        $this->assertNull(CalendarEvent::find($event->id));

        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertDontSee('Delete Me');
    }

    public function test_budget_provision_page_sorts_by_amount(): void
    {
        $admin = $this->admin();

        CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'digital_team',
            'event_date' => '2026-09-02',
            'content_title' => 'Cheap Boost',
            'financial_budget' => '1000',
            'boosting_budget' => '$70.00',
        ]);
        CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'product_team',
            'event_date' => '2026-09-03',
            'content_title' => 'Expensive Shoot',
            'financial_budget' => '150,000',
            'boosting_budget' => '0',
        ]);

        $high = $this->actingAs($admin)->get(route('admin.budget.index', ['sort' => 'high']));
        $high->assertOk()->assertSee('Budget Provision');
        $orderedHigh = $high->viewData('events')->pluck('content_title')->all();
        $this->assertSame(['Expensive Shoot', 'Cheap Boost'], $orderedHigh);

        $low = $this->actingAs($admin)->get(route('admin.budget.index', ['sort' => 'low']));
        $orderedLow = $low->viewData('events')->pluck('content_title')->all();
        $this->assertSame(['Cheap Boost', 'Expensive Shoot'], $orderedLow);
    }

    public function test_google_sheet_sync_upserts_all_recognised_tabs(): void
    {
        $this->admin();

        Http::fake(function ($request) {
            if (str_contains($request->url(), 'values:batchGet')) {
                return Http::response([
                    'valueRanges' => [[
                        'range' => 'Product Team!A1:Z',
                        'values' => [
                            ['Date', 'Day', 'Content', 'Publish Date', 'Budget', 'Product'],
                            ['September', '', 'Sheet Synced Review', '2026-09-22', '25000', 'FZ25'],
                        ],
                    ]],
                ], 200);
            }

            return Http::response([
                'sheets' => [
                    ['properties' => ['title' => 'Product Team']],
                ],
            ], 200);
        });

        $summary = app(GoogleSheetsSyncService::class)->sync(true);

        $this->assertTrue($summary['synced']);
        $event = CalendarEvent::where('content_title', 'Sheet Synced Review')->first();
        $this->assertNotNull($event);
        $this->assertSame('25000', $event->financial_budget);
        $this->assertSame('FZ25', $event->product);
        $this->assertNotEmpty($event->source_key);
    }

    public function test_submission_reminder_is_sent_to_the_assignee_for_today(): void
    {
        Mail::fake();

        $assignee = User::factory()->create([
            'role' => 'digital_team',
            'name' => 'Assigned Editor',
            'email' => 'assignee@aci-bd.com',
            'email_verified_at' => now(),
        ]);

        CalendarEvent::create([
            'user_id' => $assignee->id,
            'team_type' => 'digital_team',
            'event_date' => now()->toDateString(),
            'content_title' => 'Submit This Today',
            'status' => 'not_done',
        ]);

        $summary = app(EventNotificationService::class)->sendNotifications(0);

        $this->assertSame(1, $summary['sent_count']);
        Mail::assertSent(EventNotificationMail::class, function (EventNotificationMail $mail) use ($assignee) {
            return $mail->hasTo($assignee->email)
                && str_contains($mail->envelope()->subject, 'Content Submission Reminder');
        });
    }
}
