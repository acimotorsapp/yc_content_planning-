<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
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

    /**
     * assertSee dumps the whole rendered page when it fails, which is unusable
     * for these pages. Assert on a boolean instead so failures stay readable.
     */
    private function assertHtmlContains(TestResponse $response, string $needle, string $message): void
    {
        $this->assertTrue(str_contains($response->getContent(), $needle), $message);
    }

    private function assertHtmlMissing(TestResponse $response, string $needle, string $message): void
    {
        $this->assertFalse(str_contains($response->getContent(), $needle), $message);
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
        $this->assertSame('Event 1', $table->first()->content_title);
        $this->assertSame('Event 10', $table->last()->content_title);

        // The calendar still receives the full, unpaginated set
        $this->assertCount(25, $page1->viewData('events'));

        $page2 = $this->actingAs($admin)->get('/dashboard?page=2');
        $page2->assertStatus(200);
        $table2 = $page2->viewData('tableEvents');
        $this->assertSame(2, $table2->currentPage());
        $this->assertSame('Event 11', $table2->first()->content_title);
        $this->assertSame('Event 20', $table2->last()->content_title);

        // Last page holds the remainder
        $page3 = $this->actingAs($admin)->get('/dashboard?page=3');
        $table3 = $page3->viewData('tableEvents');
        $this->assertSame(5, $table3->count());
        $this->assertSame('Event 25', $table3->last()->content_title);
    }

    public function test_pagination_links_render_and_carry_the_fragment(): void
    {
        $admin = $this->admin();
        $this->seedEvents($admin, 25);

        $response = $this->actingAs($admin)->get('/dashboard');
        $this->assertHtmlContains($response, 'Pagination Navigation', 'Paginator markup should render');
        $this->assertHtmlContains($response, 'page=2#schedule', 'Page links should jump back to the table');
    }

    public function test_filtered_dashboards_and_create_page_paginate(): void
    {
        $admin = $this->admin();
        $this->seedEvents($admin, 25);

        foreach (['/admin/events/product', '/events/create', '/my-events'] as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
            $this->assertSame(10, $response->viewData('tableEvents')->count(), "{$route} should page its table");
            $this->assertSame(25, $response->viewData('tableEvents')->total(), "{$route} should count every event");
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
        $this->assertHtmlContains($response, 'Pagination Navigation', 'Users table should render a paginator');

        $second = $this->actingAs($admin)->get('/admin/users?page=2');
        $this->assertSame(6, $second->viewData('users')->count());
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
        $this->assertHtmlContains($response, 'page=2#events-list', 'Events list links should jump back to the table');
    }

    public function test_no_pagination_markup_when_everything_fits_on_one_page(): void
    {
        $admin = $this->admin();
        $this->seedEvents($admin, 4);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $this->assertHtmlMissing($response, 'Pagination Navigation', 'No paginator when a single page covers everything');
    }

    public function test_topbar_account_menu_exposes_logout(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        $this->assertHtmlContains($response, 'userMenu', 'Account dropdown state should be present');
        $this->assertHtmlContains($response, 'Log Out', 'Dropdown should offer a logout action');
        $this->assertHtmlContains($response, route('logout'), 'Dropdown should post to the logout route');
    }
}
