<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('events:notify', function (\App\Services\EventNotificationService $service) {
    $summary = $service->sendTodayNotifications();
    
    foreach ($summary['details'] as $detail) {
        if ($detail['status'] === 'sent') {
            $this->info("Notification sent to {$detail['email']} for {$detail['events_count']} event(s).");
        } elseif (str_starts_with($detail['status'], 'skipped')) {
            $this->warn("Skipped dummy test email: {$detail['email']} ({$detail['events_count']} event(s))");
        } else {
            $this->error("Failed to send to {$detail['email']}: " . ($detail['error'] ?? 'Unknown error'));
        }
    }
    
    $this->info('Event notifications processed.');
})->purpose('Send notifications to users for their events scheduled for today');

use Illuminate\Support\Facades\Schedule;
// Run the notification command daily at 12:00 AM (midnight)
Schedule::command('events:notify')->dailyAt('00:00');
