<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsiveSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_render_with_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        foreach (['platform' => 'Facebook', 'format' => 'Reel', 'aipe_pillar' => 'Awareness', 'product' => 'FZS V4'] as $category => $value) {
            MasterData::create(['category' => $category, 'value' => $value, 'is_active' => true]);
        }

        $product = CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'product_team',
            'event_date' => now()->addDays(2)->toDateString(),
            'shoot_date' => now()->addDay()->toDateString(),
            'content_title' => 'Lifestyle Review',
            'content_objective' => 'Drive awareness for the new model',
            'aipe_pillar' => 'Awareness',
            'platform' => 'Facebook',
            'format' => 'Reel',
            'product' => 'FZS V4',
            'boosting_budget' => '5000',
            'drive_link' => 'https://example.com/assets',
        ]);

        $digital = CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'digital_team',
            'event_date' => now()->addDays(3)->toDateString(),
            'post_no' => '12',
            'content_objective' => 'Engagement post',
            'platform' => 'Instagram',
            'format' => 'Static',
        ]);

        CalendarEvent::create([
            'user_id' => $admin->id,
            'team_type' => 'global_team',
            'event_date' => now()->addDays(5)->toDateString(),
            'content_title' => 'World Tourism Day',
        ]);

        $routes = [
            '/dashboard',
            '/admin/events/product',
            '/admin/events/digital',
            '/admin/events/global',
            '/admin/master-data',
            '/admin/users',
            '/admin/bulk-upload',
            '/admin/settings',
            '/events/create',
            '/profile',
            '/events/' . $product->id,
            '/events/' . $product->id . '/edit',
            '/events/' . $digital->id,
            '/events/' . $digital->id . '/edit',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $this->assertSame(200, $response->status(), "Route {$route} failed to render");
        }

        // The mobile drawer + responsive shell must be present on every app page
        $dashboard = $this->actingAs($admin)->get('/dashboard');
        $dashboard->assertSee('sidebarOpen', false);
        $dashboard->assertSee('md:hidden', false);
        $dashboard->assertSee('listMonth', false);
    }

    public function test_member_pages_render(): void
    {
        $member = User::factory()->create([
            'role' => 'digital_team',
            'email_verified_at' => now(),
        ]);

        foreach (['/dashboard', '/my-events', '/events/create', '/profile'] as $route) {
            $response = $this->actingAs($member)->get($route);
            $this->assertSame(200, $response->status(), "Route {$route} failed to render");
        }
    }

    public function test_guest_pages_render(): void
    {
        foreach (['/', '/login', '/register'] as $route) {
            $this->get($route)->assertStatus(200);
        }
    }
}
