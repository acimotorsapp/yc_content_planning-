<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\User;
use App\Mail\EventNotificationMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventNotificationService
{
    /**
     * Process and send event notifications for events scheduled X days in advance (default 5 days).
     *
     * @param int $daysAhead
     * @param string|null $specificDate
     * @param bool $force Bypass the duplicate protection and resend
     * @return array
     */
    public function sendNotifications(int $daysAhead = 5, ?string $specificDate = null, bool $force = false): array
    {
        $targetDate = $specificDate ? Carbon::parse($specificDate)->toDateString() : Carbon::today()->addDays($daysAhead)->toDateString();
        $actualDaysAhead = (int) Carbon::today()->diffInDays(Carbon::parse($targetDate), false);

        // Duplicate protection: one successful notification run per target date,
        // so repeated endpoint hits (or cron + manual hit) don't email users twice.
        $sentMarkerKey = 'events_notify_sent_' . $targetDate;
        if (!$force && Cache::has($sentMarkerKey)) {
            return [
                'run_date' => Carbon::today()->toDateString(),
                'target_date' => $targetDate,
                'days_ahead' => $actualDaysAhead,
                'already_sent' => true,
                'sent_at' => Cache::get($sentMarkerKey),
                'cc_recipients_count' => count(EventNotificationMail::getDefaultCcRecipients()),
                'cc_recipients' => EventNotificationMail::getDefaultCcRecipients(),
                'total_events' => 0,
                'total_users' => 0,
                'sent_count' => 0,
                'skipped_count' => 0,
                'failed_count' => 0,
                'details' => [],
                'message' => 'Notifications for this target date were already sent. Duplicate sending prevented (use ?force=1 to resend).',
            ];
        }

        $events = CalendarEvent::whereDate('event_date', $targetDate)
            ->where(function ($query) {
                $query->where('status', '!=', 'done')->orWhereNull('status');
            })
            ->get();
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
                $mailable = new EventNotificationMail($user, $userEvents, $targetDate, $actualDaysAhead, true);
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

        // Only mark as sent when at least one email went out and nothing failed,
        // so a retry (next hit) is still possible after partial failures.
        if ($sentCount > 0 && $failedCount === 0) {
            Cache::forever($sentMarkerKey, Carbon::now()->toDateTimeString());
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
