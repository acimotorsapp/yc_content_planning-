<?php

namespace App\Console\Commands;

use App\Mail\EventNotificationMail;
use App\Models\CalendarEvent;
use App\Models\EventNotificationDelivery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProcessEventNotifications extends Command
{
    protected $signature = 'events:process-notifications';

    protected $description = 'Process one pending event reminder delivery record';

    public function handle(): int
    {
        $this->recoverStaleProcessingDeliveries();

        $notification = DB::transaction(function () {
            $delivery = EventNotificationDelivery::pending()
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $delivery) {
                return null;
            }

            $delivery->update([
                'status' => EventNotificationDelivery::STATUS_PROCESSING,
                'processing_started_at' => now(),
                'attempts' => $delivery->attempts + 1,
            ]);

            return $delivery->fresh();
        });

        if (! $notification) {
            $this->line('No pending event notifications found.');

            return self::SUCCESS;
        }

        $targetDate = $notification->target_date->toDateString();
        $daysAhead = $notification->days_ahead;
        $email = EventNotificationMail::primaryToRecipient();

        $this->line("Processing notification #{$notification->id}");
        $this->line("Recipient: {$email}");
        $this->line("Target date: {$targetDate}");

        $events = CalendarEvent::query()
            ->with('user')
            ->whereDate('event_date', $targetDate)
            ->orderBy('id')
            ->get();

        if ($events->isEmpty()) {
            return $this->failDelivery($notification, 'No matching calendar events found for target date.');
        }

        $user = $events->first()->user;
        if (! $user) {
            return $this->failDelivery($notification, 'User not found.');
        }

        $ccRecipients = EventNotificationMail::getDefaultCcRecipients($email);
        $fromAddress = (string) config('mail.from.address');

        $this->line("Notification ID: {$notification->id}");
        $this->line('Related users: '.$events->pluck('user_id')->unique()->count());
        $this->line('Events included in email: '.$events->count());
        $this->line("From: {$fromAddress}");
        $this->line("To: {$email}");
        $this->line('CC:');
        foreach ($ccRecipients as $ccRecipient) {
            $this->line("- {$ccRecipient}");
        }

        try {
            Mail::to($email)->send(new EventNotificationMail($user, $events, $targetDate, $daysAhead));
        } catch (Throwable $exception) {
            Log::error('Failed to send scheduled event notification.', [
                'delivery_id' => $notification->id,
                'to' => $email,
                'target_date' => $targetDate,
                'error' => $exception->getMessage(),
            ]);

            return $this->failDelivery($notification, $this->safeErrorMessage($exception));
        }

        Log::info('Scheduled event reminder SMTP submission.', [
            'delivery_id' => $notification->id,
            'target_date' => $targetDate,
            'to' => $email,
            'cc' => $ccRecipients,
            'mailer' => config('mail.default'),
            'from' => $fromAddress,
        ]);

        $notification->update([
            'status' => EventNotificationDelivery::STATUS_SENT,
            'sent_at' => now(),
            'last_error' => null,
        ]);

        $this->line('Status: submitted-to-smtp');

        return self::SUCCESS;
    }

    private function recoverStaleProcessingDeliveries(): void
    {
        $minutes = (int) config('event_notifications.stale_processing_minutes', 15);
        $threshold = now()->subMinutes($minutes);

        EventNotificationDelivery::query()
            ->where('status', EventNotificationDelivery::STATUS_PROCESSING)
            ->where('processing_started_at', '<', $threshold)
            ->update([
                'status' => EventNotificationDelivery::STATUS_PENDING,
                'processing_started_at' => null,
            ]);
    }

    private function failDelivery(EventNotificationDelivery $notification, string $reason): int
    {
        $notification->update([
            'status' => EventNotificationDelivery::STATUS_FAILED,
            'last_error' => $reason,
            'sent_at' => null,
        ]);

        $this->line('Status: failed');
        $this->line("Reason: {$reason}");

        return self::SUCCESS;
    }

    private function safeErrorMessage(Throwable $exception): string
    {
        return str($exception->getMessage())
            ->replaceMatches('/password\s*=\s*[^;\s]+/i', 'password=[redacted]')
            ->replaceMatches('/(MAIL_PASSWORD=)[^\s]+/i', '$1[redacted]')
            ->limit(1000)
            ->toString();
    }
}
