<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiEventCapacityTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiple_product_and_digital_events_can_be_scheduled_on_same_date_up_to_six(): void
    {
        $testDate = '2026-11-20';
        CalendarEvent::where('event_date', $testDate)->delete();

        $user = User::factory()->create([
            'role' => 'super_admin'
        ]);

        $this->actingAs($user);

        // 1. Post first Product event on testDate
        $response1 = $this->post(route('events.product.store'), [
            'event_date' => $testDate,
            'content_title' => 'Product Event 1',
            'boosting_budget' => '1000'
        ]);
        if (session('errors')) {
            dump(session('errors')->all());
        }
        dump('Events in DB: ' . CalendarEvent::count());
        $response1->assertSessionHasNoErrors();
        $this->assertEquals(1, CalendarEvent::whereDate('event_date', $testDate)->count());

        // 2. Post Digital event on SAME testDate
        $response2 = $this->post(route('events.digital.store'), [
            'event_date' => $testDate,
            'content_title' => 'Digital Event 2',
            'post_no' => '1',
            'boosting_budget' => '2000'
        ]);
        $response2->assertSessionHasNoErrors();
        $this->assertEquals(2, CalendarEvent::where('event_date', $testDate)->count());

        // 3. Post Global event on SAME testDate
        $response3 = $this->post(route('events.global.store'), [
            'event_date' => $testDate,
            'content_title' => 'Global Event 3'
        ]);
        $response3->assertSessionHasNoErrors();
        $this->assertEquals(3, CalendarEvent::where('event_date', $testDate)->count());

        // 4. Post 3 more events on SAME testDate to reach 6
        for ($i = 4; $i <= 6; $i++) {
            $route = $i % 2 === 0 ? route('events.product.store') : route('events.digital.store');
            $res = $this->post($route, [
                'event_date' => $testDate,
                'content_title' => "Event {$i}",
                'post_no' => "{$i}",
                'boosting_budget' => '1500'
            ]);
            $res->assertSessionHasNoErrors();
        }

        $this->assertEquals(6, CalendarEvent::where('event_date', $testDate)->count());

        // 5. Attempt to add 7th event on SAME testDate (Should be REJECTED)
        $response7 = $this->post(route('events.product.store'), [
            'event_date' => $testDate,
            'content_title' => '7th Event Beyond Capacity',
            'boosting_budget' => '1000'
        ]);
        $response7->assertSessionHasErrors('event_date');
        $this->assertEquals(6, CalendarEvent::where('event_date', $testDate)->count());

        // 6. Test Dashboard page loads and shares dateCounts
        $dashRes = $this->get(route('dashboard'));
        $dashRes->assertOk();
        $dashRes->assertViewHas('dateCounts', function ($dateCounts) use ($testDate) {
            return isset($dateCounts[$testDate]) && $dateCounts[$testDate] === 6;
        });
        $dashRes->assertViewHas('fullyBookedDates', function ($fullyBookedDates) use ($testDate) {
            return in_array($testDate, $fullyBookedDates);
        });

        // 7. Test Updating an existing event on the full date does NOT trigger false limit
        $existingEvent = CalendarEvent::where('event_date', $testDate)->first();
        $updateRes = $this->put(route('events.update', $existingEvent), [
            'event_date' => $testDate,
            'content_title' => 'Updated Event Title'
        ]);
        $updateRes->assertSessionHasNoErrors();

        // Clean up
        CalendarEvent::where('event_date', $testDate)->delete();
        $user->delete();
    }
}
