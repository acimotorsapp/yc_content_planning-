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
        $this->mergeDuplicateRows();
        $this->dropUniqueIfExists('event_notification_deliveries_unique');

        if (! $this->indexExists('event_notification_deliveries_unique')) {
            Schema::table('event_notification_deliveries', function (Blueprint $table) {
                $table->unique(['target_date', 'days_ahead'], 'event_notification_deliveries_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('event_notification_deliveries')) {
            return;
        }

        $this->dropUniqueIfExists('event_notification_deliveries_unique');

        if (! $this->indexExists('event_notification_deliveries_unique')) {
            Schema::table('event_notification_deliveries', function (Blueprint $table) {
                $table->unique(['target_date', 'days_ahead'], 'event_notification_deliveries_unique');
            });
        }
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
            $userIds = $this->mergedUserIds($rows);
            $sentAt = $rows->first(fn ($row) => $row->sent_at !== null)?->sent_at;
            $processingStartedAt = $rows
                ->filter(fn ($row) => $row->processing_started_at !== null)
                ->max('processing_started_at');

            DB::table('event_notification_deliveries')
                ->where('id', $canonical->id)
                ->update([
                    'status' => $status,
                    'user_ids' => json_encode($userIds),
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

    private function mergedUserIds($rows): array
    {
        return $rows
            ->flatMap(function ($row) {
                $ids = [];

                if (property_exists($row, 'user_id') && $row->user_id !== null) {
                    $ids[] = $row->user_id;
                }

                if (isset($row->user_ids) && $row->user_ids !== null) {
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

    private function dropUniqueIfExists(string $indexName): void
    {
        if (! $this->indexExists($indexName)) {
            return;
        }

        if (Schema::hasColumn('event_notification_deliveries', 'user_id') && ! $this->indexExists('event_notification_deliveries_user_id_index')) {
            Schema::table('event_notification_deliveries', function (Blueprint $table) {
                $table->index('user_id', 'event_notification_deliveries_user_id_index');
            });
        }

        Schema::table('event_notification_deliveries', function (Blueprint $table) use ($indexName) {
            $table->dropUnique($indexName);
        });
    }

    private function indexExists(string $indexName): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return collect(DB::select("PRAGMA index_list('event_notification_deliveries')"))
                ->contains(fn ($index) => $index->name === $indexName);
        }

        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'event_notification_deliveries')
            ->where('index_name', $indexName)
            ->exists();
    }
};
