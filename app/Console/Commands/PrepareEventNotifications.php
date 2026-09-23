<?php

namespace App\Console\Commands;

use App\Models\CalendarEvent;
use App\Models\EventNotificationDelivery;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;

class PrepareEventNotifications extends Command
{
    protected $signature = 'events:prepare-notifications';

    protected $description = 'Create pending event reminder delivery records for users with events due soon';

    public function handle(): int
    {
        $daysAhead = (int) config('event_notifications.days_ahead', 5);
        $timezone = config('event_notifications.timezone', 'Asia/Dhaka');
        $targetDate = Carbon::now($timezone)->startOfDay()->addDays($daysAhead)->toDateString();

        $matchingEvents = CalendarEvent::whereDate('event_date', $targetDate)->count();

        $userIds = CalendarEvent::query()
            ->whereDate('event_date', $targetDate)
            ->whereHas('user', function ($query) {
                $query->whereNotNull('email')->where('email', '<>', '');
            })
            ->distinct()
            ->orderBy('user_id')
            ->pluck('user_id');

        $created = 0;
        $existing = 0;

        foreach ($userIds as $userId) {
            try {
                $delivery = EventNotificationDelivery::query()
                    ->whereDate('target_date', $targetDate)
                    ->where('days_ahead', $daysAhead)
                    ->first();

                if (! $delivery) {
                    $delivery = EventNotificationDelivery::firstOrCreate(
                        [
                            'target_date' => $targetDate,
                            'days_ahead' => $daysAhead,
                        ],
                        [
                            'user_ids' => [$userId],
                            'status' => EventNotificationDelivery::STATUS_PENDING,
                        ]
                    );
                }

                $this->appendUserId($delivery, (int) $userId);

                $delivery->wasRecentlyCreated ? $created++ : $existing++;
            } catch (UniqueConstraintViolationException) {
                $delivery = EventNotificationDelivery::query()
                    ->where('target_date', $targetDate)
                    ->where('days_ahead', $daysAhead)
                    ->first();

                if ($delivery) {
                    $this->appendUserId($delivery, (int) $userId);
                }

                $existing++;
            }
        }

        $this->line("Target date: {$targetDate}");
        $this->line("Matching events: {$matchingEvents}");
        $this->line('Users: '.$userIds->count());
        $this->line("Pending created: {$created}");
        $this->line("Already existed: {$existing}");

        return self::SUCCESS;
    }

    private function appendUserId(EventNotificationDelivery $delivery, int $userId): void
    {
        $delivery->refresh();

        $userIds = collect($delivery->user_ids ?? [])
            ->push($userId)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $delivery->update(['user_ids' => $userIds]);
    }
}
