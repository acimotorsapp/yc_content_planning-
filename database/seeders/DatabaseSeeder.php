<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $adminUser = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $productUser = User::factory()->create([
            'name' => 'Product Team User',
            'email' => 'product@test.com',
            'password' => bcrypt('password'),
            'role' => 'product_team',
        ]);

        $digitalUser = User::factory()->create([
            'name' => 'Digital Team User',
            'email' => 'digital@test.com',
            'password' => bcrypt('password'),
            'role' => 'digital_team',
        ]);

        // Seed Product Team Events
        \App\Models\CalendarEvent::create([
            'user_id' => $productUser->id,
            'team_type' => 'product_team',
            'event_date' => now()->addDays(2)->format('Y-m-d'),
            'content_title' => 'Life Style Review',
            'aipe_pillar' => 'Interest',
            'content_objective' => 'To showcase an authentic customer experience with Yamaha FZS Version 2',
            'shoot_date' => now()->subDays(5)->format('Y-m-d'),
            'color_concern' => 'Dark night',
            'format' => 'Product Review',
            'boosting_budget' => '15,000',
            'platform' => 'Facebook',
            'product' => 'FZS V2',
        ]);

        \App\Models\CalendarEvent::create([
            'user_id' => $productUser->id,
            'team_type' => 'product_team',
            'event_date' => now()->addDays(7)->format('Y-m-d'),
            'content_title' => 'Customer Review',
            'aipe_pillar' => 'Interest+Experience',
            'content_objective' => 'Showcase real customer experience',
            'shoot_date' => now()->format('Y-m-d'),
            'color_concern' => 'Racing Blue',
            'format' => 'Product Review',
            'platform' => 'Facebook',
            'product' => 'R15',
        ]);

        // Seed Digital Team Events
        // Create test events
        \App\Models\CalendarEvent::create([
            'user_id' => $productUser->id,
            'team_type' => 'product_team',
            'event_date' => now()->addDays(4)->format('Y-m-d'),
            'content_title' => 'Life Style Review',
            'aipe_pillar' => 'Interest',
            'content_objective' => 'Increase product engagement.',
            'format' => 'Product Review',
            'product' => 'MT',
        ]);

        \App\Models\CalendarEvent::create([
            'user_id' => $digitalUser->id,
            'team_type' => 'digital_team',
            'event_date' => now()->addDays(9)->format('Y-m-d'),
            'post_no' => '2',
            'aipe_pillar' => 'Purchase Generation',
            'product_focus' => 'FZS Hybrid',
            'content_objective' => 'Offer video presenting the priority range.',
            'format' => 'Reel',
        ]);

        // Seed Global Events
        $globalEventsRaw = [
            '2026-08-27' => 'World Lake Day',
            '2026-08-30' => 'Aragon GP',
            '2026-09-05' => 'International Day of Charity',
            '2026-09-06' => 'San Marino GP',
            '2026-09-07' => 'Madhu Purnima and Mahalaya',
            '2026-09-12' => 'Saluto UBS Launching',
            '2026-09-15' => 'World Engineers\' Day',
            '2026-10-01' => 'Ashtami',
            '2026-10-04' => 'Japanese GP',
            '2026-10-18' => 'Durga Puja',
            '2026-11-01' => 'Halloween',
            '2026-11-17' => 'World Diabetes Day',
            '2026-12-05' => 'World AIDS Day',
            '2026-12-25' => 'Christmas Day',
        ];

        foreach ($globalEventsRaw as $date => $title) {
            \App\Models\CalendarEvent::create([
                'user_id' => $adminUser->id,
                'team_type' => 'global_team',
                'event_date' => $date,
                'content_title' => $title,
                'content_objective' => 'Global observance',
            ]);
        }
    }
}
