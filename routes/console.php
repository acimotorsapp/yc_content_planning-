<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('events:notify', function () {
    $today = \Carbon\Carbon::today()->toDateString();
    
    // Find events scheduled for today
    $events = \App\Models\CalendarEvent::whereDate('event_date', $today)->get();
    
    // Group by user
    $eventsByUser = $events->groupBy('user_id');
    
    foreach ($eventsByUser as $userId => $userEvents) {
        $user = \App\Models\User::find($userId);
        if ($user) {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\EventNotificationMail($user, $userEvents));
            $this->info("Notification sent to {$user->email} for " . $userEvents->count() . " event(s).");
        }
    }
    
    $this->info('Event notifications processed.');
})->purpose('Send notifications to users for their events scheduled for today');

use Illuminate\Support\Facades\Schedule;
// Run the notification command daily at 12:00 AM (midnight)
Schedule::command('events:notify')->dailyAt('00:00');
