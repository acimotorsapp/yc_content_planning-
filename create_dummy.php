<?php
use App\Models\User;
use App\Models\CalendarEvent;
use Carbon\Carbon;

$user = User::firstOrCreate(
    ['email' => 'acijubairislamdaief@gmail.com'],
    ['name' => 'Daief', 'password' => bcrypt('password')]
);

CalendarEvent::create([
    'user_id' => $user->id,
    'title' => 'Test Event for Email Dispatch',
    'event_date' => Carbon::today()->addDays(5)->toDateString(),
    'platform' => 'Facebook',
    'post_type' => 'Image',
    'content' => 'This is a test content for the email notification check.',
    'status' => 'Pending'
]);

echo "Dummy data created for user {$user->email}!\n";
