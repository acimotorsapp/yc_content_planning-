<?php

use App\Models\EventNotificationDelivery;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('event_notification_deliveries')) {
            return;
        }

        $this->ensureUserIdsColumn();
        $this->backfillUserIds();
        $this->mergeDuplicateRows();
    }

    public function down(): void
    {
        if (! Schema::hasTable('event_notification_deliveries') || ! Schema::hasColumn('event_notification_deliveries', 'user_ids')) {
            return;
        }

        Schema::table('event_notification_deliveries', function (Blueprint $table) {
            $table->dropColumn('user_ids');
        });
    }

    private function ensureUserIdsColumn(): void
    {
        if (Schema::hasColumn('event_notification_deliveries', 'user_ids')) {
            return;
        }

        Schema::table('event_notification_deliveries', function (Blueprint $table) {
            $column = $table->json('user_ids')->nullable();

            if (Schema::hasColumn('event_notification_deliveries', 'user_id')) {
                $column->after('user_id');
            }
        });
    }

    private function backfillUserIds(): void
    {
        if (! Schema::hasColumn('event_notification_deliveries', 'user_id')) {
            return;
        }

        DB::table('event_notification_deliveries')
            ->whereNull('user_ids')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(100, function ($deliveries) {
                foreach ($deliveries as $delivery) {
                    DB::table('event_notification_deliveries')
                        ->where('id', $delivery->id)
                        ->update(['user_ids' => json_encode([(int) $delivery->user_id])]);
                }
            });
    }

    private function mergeDuplicateRows(): void
    {
        $groups = DB::table('event_notification_deliveries')
            ->select('target_date', 'days_ahead', DB::raw('COUNT(*) as row_count'))
            ->groupBy('target_date', 'days_ahead')
            ->having('row_count', '>', 1)
            ->get();

        foreach ($groups as $group) {
            $rows = DB::table('event_notification_deliveries')
                ->where('target_date', $group->target_date)
                ->where('days_ahead', $group->days_ahead)
                ->orderBy('id')
                ->get();

            $canonical = $rows->first();
            $status = $this->mergedStatus($rows);
            $sentAt = $rows->first(fn ($row) => $row->sent_at !== null)?->sent_at;
            $processingStartedAt = $rows
                ->filter(fn ($row) => $row->processing_started_at !== null)
                ->max('processing_started_at');

            DB::table('event_notification_deliveries')
                ->where('id', $canonical->id)
                ->update([
                    'user_ids' => json_encode($this->mergedUserIds($rows)),
                    'status' => $status,
                    'attempts' => (int) $rows->max('attempts'),
                    'last_error' => $status === EventNotificationDelivery::STATUS_SENT
                        ? null
                        : $rows->reverse()->first(fn ($row) => $row->last_error !== null)?->last_error,
                    'processing_started_at' => $status === EventNotificationDelivery::STATUS_SENT
                        ? null
                        : $processingStartedAt,
                    'sent_at' => $status === EventNotificationDelivery::STATUS_SENT ? $sentAt : null,
                    'updated_at' => $rows->max('updated_at') ?? now(),
                ]);

            DB::table('event_notification_deliveries')
                ->whereIn('id', $rows->pluck('id')->skip(1)->all())
                ->delete();
        }
    }

    private function mergedUserIds($rows): array
    {
        return $rows
            ->flatMap(function ($row) {
                $ids = [];

                if (property_exists($row, 'user_id') && $row->user_id !== null) {
                    $ids[] = $row->user_id;
                }

                if ($row->user_ids !== null) {
                    $decoded = json_decode($row->user_ids, true);
                    if (is_array($decoded)) {
                        $ids = array_merge($ids, $decoded);
                    }
                }

                return $ids;
            })
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function mergedStatus($rows): string
    {
        if ($rows->contains(fn ($row) => $row->status === EventNotificationDelivery::STATUS_SENT)) {
            return EventNotificationDelivery::STATUS_SENT;
        }

        if ($rows->contains(fn ($row) => $row->status === EventNotificationDelivery::STATUS_PROCESSING)) {
            return EventNotificationDelivery::STATUS_PROCESSING;
        }

        if ($rows->contains(fn ($row) => $row->status === EventNotificationDelivery::STATUS_PENDING)) {
            return EventNotificationDelivery::STATUS_PENDING;
        }

        return EventNotificationDelivery::STATUS_FAILED;
    }
};
