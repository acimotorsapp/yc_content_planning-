<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\User;
use App\Mail\EventNotificationMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventNotificationService
{
    /**
     * Process and send daily event notifications to users who have events today.
     *
     * @return array
     */
    public function sendTodayNotifications(): array
    {
        $today = Carbon::today()->toDateString();
        $events = CalendarEvent::whereDate('event_date', $today)->get();
        $eventsByUser = $events->groupBy('user_id');

        $results = [];
        $sentCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        foreach ($eventsByUser as $userId => $userEvents) {
            $user = User::find($userId);
            if (!$user || empty($user->email)) {
                continue;
            }

            // Skip dummy development placeholder emails to avoid SMTP 550 bounces
            if (str_ends_with($user->email, '@test.com') || str_ends_with($user->email, '@example.com')) {
                $skippedCount++;
                $results[] = [
                    'user' => $user->name,
                    'email' => $user->email,
                    'events_count' => $userEvents->count(),
                    'status' => 'skipped (dummy email)',
                ];
                continue;
            }

            try {
                Mail::to($user->email)->send(new EventNotificationMail($user, $userEvents));
                $sentCount++;
                $results[] = [
                    'user' => $user->name,
                    'email' => $user->email,
                    'events_count' => $userEvents->count(),
                    'status' => 'sent',
                ];
            } catch (\Throwable $e) {
                $failedCount++;
                Log::error("Failed to send event notification to {$user->email}: " . $e->getMessage());
                $results[] = [
                    'user' => $user->name,
                    'email' => $user->email,
                    'events_count' => $userEvents->count(),
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'date' => $today,
            'total_events' => $events->count(),
            'total_users' => count($eventsByUser),
            'sent_count' => $sentCount,
            'skipped_count' => $skippedCount,
            'failed_count' => $failedCount,
            'details' => $results,
        ];
    }
}
