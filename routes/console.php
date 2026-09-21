<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('events:notify {--days=5 : Days ahead to send reminder for (default: 5)} {--date= : Specific event date to send reminder for}', function (\App\Services\EventNotificationService $service) {
    $daysAhead = (int) ($this->option('days') ?? 5);
    $targetDate = $this->option('date');

    $summary = $service->sendNotifications($daysAhead, $targetDate);
    
    $this->info("Processing reminders for target date: {$summary['target_date']} ({$summary['days_ahead']} days in advance).");

    foreach ($summary['details'] as $detail) {
        if ($detail['status'] === 'sent') {
            $ccCount = isset($detail['cc']) ? count($detail['cc']) : 0;
            $this->info("Notification sent to {$detail['email']} (CC: {$ccCount} recipients) for {$detail['events_count']} event(s).");
        } elseif (str_starts_with($detail['status'], 'skipped')) {
            $this->warn("Skipped dummy test email: {$detail['email']} ({$detail['events_count']} event(s))");
        } else {
            $this->error("Failed to send to {$detail['email']}: " . ($detail['error'] ?? 'Unknown error'));
        }
    }
    
    $this->info('Event reminder notifications completed.');
})->purpose('Send advance reminder notifications to users for their events scheduled 5 days in advance');

use Illuminate\Support\Facades\Schedule;
// Assignee reminder: 11:00 AM Bangladesh time, 5 days before the content deadline.
Schedule::command('events:notify --days=5')->dailyAt('11:00')->timezone('Asia/Dhaka');
