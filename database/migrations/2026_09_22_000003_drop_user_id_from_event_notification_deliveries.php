<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('event_notification_deliveries') || ! Schema::hasColumn('event_notification_deliveries', 'user_id')) {
            return;
        }

        if (DB::getDriverName() !== 'sqlite') {
            if ($this->foreignKeyExists('event_notification_deliveries_user_id_foreign')) {
                Schema::table('event_notification_deliveries', function (Blueprint $table) {
                    $table->dropForeign('event_notification_deliveries_user_id_foreign');
                });
            }

            foreach ($this->indexesForColumn('user_id') as $indexName) {
                Schema::table('event_notification_deliveries', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }
        }

        Schema::table('event_notification_deliveries', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        //
    }

    private function foreignKeyExists(string $constraintName): bool
    {
        return DB::table('information_schema.key_column_usage')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'event_notification_deliveries')
            ->where('constraint_name', $constraintName)
            ->exists();
    }

    private function indexesForColumn(string $column): array
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'event_notification_deliveries')
            ->where('column_name', $column)
            ->where('index_name', '<>', 'PRIMARY')
            ->distinct()
            ->pluck('index_name')
            ->all();
    }
};
