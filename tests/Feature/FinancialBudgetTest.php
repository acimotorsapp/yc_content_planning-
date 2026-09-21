<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialBudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product_event_with_financial_and_boosting_budget(): void
    {
        $user = User::factory()->create([
            'role' => 'product_team',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('events.product.store'), [
            'event_date' => '2026-10-15',
            'content_title' => 'Product Video Shoot',
            'financial_budget' => '45000',
            'boosting_budget' => '12000',
            'drive_link' => 'https://drive.google.com/test',
            'remarks' => 'Q4 campaign',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasNoErrors();

        $event = CalendarEvent::whereDate('event_date', '2026-10-15')->first();
        $this->assertNotNull($event);
        $this->assertSame('45000', $event->financial_budget);
        $this->assertSame('12000', $event->boosting_budget);
        $this->assertSame('product_team', $event->team_type);
    }

    public function test_can_create_digital_event_with_financial_and_boosting_budget(): void
    {
        $user = User::factory()->create([
            'role' => 'digital_team',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('events.digital.store'), [
            'event_date' => '2026-10-16',
            'post_no' => '5',
            'product_focus' => 'FZ V4',
            'financial_budget' => '30000',
            'boosting_budget' => '8000',
            'remarks' => 'Social boost',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasNoErrors();

        $event = CalendarEvent::whereDate('event_date', '2026-10-16')->first();
        $this->assertNotNull($event);
        $this->assertSame('30000', $event->financial_budget);
        $this->assertSame('8000', $event->boosting_budget);
        $this->assertSame('digital_team', $event->team_type);
    }

    public function test_can_create_brand_event(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('events.brand.store'), [
            'event_date' => '2026-10-20',
            'content_title' => 'Yamaha Brand Day',
            'financial_budget' => '80000',
            'boosting_budget' => '15000',
            'content_objective' => 'Brand awareness',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasNoErrors();

        $event = CalendarEvent::whereDate('event_date', '2026-10-20')->first();
        $this->assertNotNull($event);
        $this->assertSame('brand_team', $event->team_type);
        $this->assertSame('Yamaha Brand Day', $event->content_title);
        $this->assertSame('Brand', $event->teamLabel());
    }

    public function test_can_create_service_event(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('events.service.store'), [
            'event_date' => '2026-10-21',
            'content_title' => 'Free Service Camp',
            'financial_budget' => '25000',
            'boosting_budget' => '5000',
            'content_objective' => 'After-sales service awareness',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasNoErrors();

        $event = CalendarEvent::whereDate('event_date', '2026-10-21')->first();
        $this->assertNotNull($event);
        $this->assertSame('service_team', $event->team_type);
        $this->assertSame('Free Service Camp', $event->content_title);
        $this->assertSame('Service', $event->teamLabel());
    }

    public function test_can_update_event_financial_budget(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $event = CalendarEvent::create([
            'user_id' => $user->id,
            'team_type' => 'product_team',
            'event_date' => '2026-10-17',
            'content_title' => 'Initial Title',
            'financial_budget' => '20000',
            'boosting_budget' => '5000',
        ]);

        $this->actingAs($user);

        $response = $this->put(route('events.update', $event), [
            'event_date' => '2026-10-17',
            'content_title' => 'Updated Title',
            'financial_budget' => '60000',
            'boosting_budget' => '15000',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasNoErrors();

        $event->refresh();
        $this->assertSame('60000', $event->financial_budget);
        $this->assertSame('15000', $event->boosting_budget);
    }

    public function test_event_show_page_renders_financial_budget(): void
    {
        $user = User::factory()->create();

        $event = CalendarEvent::create([
            'user_id' => $user->id,
            'team_type' => 'product_team',
            'event_date' => '2026-10-18',
            'content_title' => 'Grand Reveal',
            'financial_budget' => '99000',
            'boosting_budget' => '25000',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('events.show', $event));
        $response->assertOk();
        $response->assertSee('Financial Budget');
        $response->assertSee('99000');
        $response->assertSee('Boosting Budget');
        $response->assertSee('25000');
    }
}
