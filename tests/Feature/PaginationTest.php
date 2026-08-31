<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);
    }

    private function seedEvents(User $user, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            CalendarEvent::create([
                'user_id' => $user->id,
                'team_type' => 'product_team',
                'event_date' => now()->addDays($i)->toDateString(),
                'content_title' => "Event {$i}",
            ]);
        }
    }

    public function test_dashboard_table_pages_but_calendar_keeps_every_event(): void
    {
        $admin = $this->admin();
        $this->seedEvents($admin, 25);

        $page1 = $this->actingAs($admin)->get('/dashboard');
        $page1->assertStatus(200);

        $table = $page1->viewData('tableEvents');
        $this->assertSame(10, $table->count(), 'First page should hold 10 rows');
        $this->assertSame(25, $table->total());
        $this->assertSame(3, $table->lastPage());

        // The calendar still receives the full set
        $this->assertCount(25, $page1->viewData('events'));

        // Rows 11-20 only exist on page two
        $page1->assertSee('Event 1<', false);
        $page1->assertDontSee('Event 15<', false);

        $page2 = $this->actingAs($admin)->get('/dashboard?page=2');
        $page2->assertStatus(200);
        $this->assertSame(2, $page2->viewData('tableEvents')->currentPage());
        $page2->assertSee('Event 15<', false);
        $page2->assertDontSee('Event 1<', false);

        // Last page holds the remainder
        $page3 = $this->actingAs($admin)->get('/dashboard?page=3');
        $this->assertSame(5, $page3->viewData('tableEvents')->count());
    }

    public function test_pagination_links_render_and_carry_the_fragment(): void
    {
        $admin = $this->admin();
        $this->seedEvents($admin, 25);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertSee('Pagination Navigation', false);
        $response->assertSee('page=2#schedule', false);
    }

    public function test_filtered_dashboards_and_create_page_paginate(): void
    {
        $admin = $this->admin();
        $this->seedEvents($admin, 25);

        foreach (['/admin/events/product', '/events/create', '/my-events'] as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
            $this->assertSame(10, $response->viewData('tableEvents')->count(), "{$route} should page its table");
        }
    }

    public function test_users_table_paginates(): void
    {
        $admin = $this->admin();
        User::factory()->count(20)->create();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);

        $users = $response->viewData('users');
        $this->assertSame(15, $users->count());
        $this->assertSame(21, $users->total());
        $response->assertSee('Pagination Navigation', false);
    }

    public function test_master_data_events_table_paginates(): void
    {
        $admin = $this->admin();
        $this->seedEvents($admin, 25);
        MasterData::create(['category' => 'platform', 'value' => 'Facebook', 'is_active' => true]);

        $response = $this->actingAs($admin)->get('/admin/master-data');
        $response->assertStatus(200);

        $events = $response->viewData('events');
        $this->assertSame(15, $events->count());
        $this->assertSame(25, $events->total());
        $response->assertSee('page=2#events-list', false);
    }

    public function test_no_pagination_markup_when_everything_fits_on_one_page(): void
    {
        $admin = $this->admin();
        $this->seedEvents($admin, 4);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Pagination Navigation', false);
    }
}
