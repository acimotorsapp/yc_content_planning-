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
     * Process and send event notifications for events scheduled X days in advance (default 5 days).
     *
     * @param int $daysAhead
     * @param string|null $specificDate
     * @return array
     */
    public function sendNotifications(int $daysAhead = 5, ?string $specificDate = null): array
    {
        $targetDate = $specificDate ? Carbon::parse($specificDate)->toDateString() : Carbon::today()->addDays($daysAhead)->toDateString();
        $actualDaysAhead = Carbon::today()->diffInDays(Carbon::parse($targetDate), false);

        $events = CalendarEvent::whereDate('event_date', $targetDate)->get();
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
                    'cc' => EventNotificationMail::getDefaultCcRecipients(),
                    'events_count' => $userEvents->count(),
                    'target_date' => $targetDate,
                    'days_ahead' => $actualDaysAhead,
                    'status' => 'skipped (dummy email)',
                ];
                continue;
            }

            try {
                $mailable = new EventNotificationMail($user, $userEvents, $targetDate, $actualDaysAhead);
                Mail::to($user->email)->send($mailable);
                $sentCount++;
                $results[] = [
                    'user' => $user->name,
                    'email' => $user->email,
                    'cc' => array_map(fn($addr) => is_string($addr) ? $addr : $addr->address, $mailable->envelope()->cc),
                    'events_count' => $userEvents->count(),
                    'target_date' => $targetDate,
                    'days_ahead' => $actualDaysAhead,
                    'status' => 'sent',
                ];
            } catch (\Throwable $e) {
                $failedCount++;
                Log::error("Failed to send event notification to {$user->email}: " . $e->getMessage());
                $results[] = [
                    'user' => $user->name,
                    'email' => $user->email,
                    'events_count' => $userEvents->count(),
                    'target_date' => $targetDate,
                    'days_ahead' => $actualDaysAhead,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'run_date' => Carbon::today()->toDateString(),
            'target_date' => $targetDate,
            'days_ahead' => $actualDaysAhead,
            'cc_recipients_count' => count(EventNotificationMail::getDefaultCcRecipients()),
            'cc_recipients' => EventNotificationMail::getDefaultCcRecipients(),
            'total_events' => $events->count(),
            'total_users' => count($eventsByUser),
            'sent_count' => $sentCount,
            'skipped_count' => $skippedCount,
            'failed_count' => $failedCount,
            'details' => $results,
        ];
    }

    /**
     * Backward-compatible alias (defaults to 5 days advance reminder).
     *
     * @return array
     */
    public function sendTodayNotifications(): array
    {
        return $this->sendNotifications(5);
    }
}
