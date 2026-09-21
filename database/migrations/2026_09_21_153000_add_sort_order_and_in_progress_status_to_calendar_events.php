<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('calendar_events', 'sort_order')) {
            Schema::table('calendar_events', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('status');
            });
        }

        $this->expandStatusColumn();

        $grouped = DB::table('calendar_events')
            ->orderBy('event_date')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($row) => $row->status ?: 'not_done');

        foreach ($grouped as $rows) {
            foreach ($rows->values() as $index => $row) {
                DB::table('calendar_events')->where('id', $row->id)->update([
                    'sort_order' => $index,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('calendar_events')->where('status', 'in_progress')->update(['status' => 'not_done']);

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE calendar_events MODIFY status ENUM('done', 'not_done') NOT NULL DEFAULT 'not_done'");
        }

        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }

    private function expandStatusColumn(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE calendar_events MODIFY status ENUM('done', 'not_done', 'in_progress') NOT NULL DEFAULT 'not_done'");
            return;
        }

        if ($driver !== 'sqlite') {
            return;
        }

        $statuses = DB::table('calendar_events')->pluck('status', 'id');

        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('calendar_events', function (Blueprint $table) {
            $table->string('status', 20)->default('not_done');
        });

        foreach ($statuses as $id => $status) {
            DB::table('calendar_events')->where('id', $id)->update([
                'status' => in_array($status, ['done', 'not_done', 'in_progress'], true) ? $status : 'not_done',
            ]);
        }
    }
};
