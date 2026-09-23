<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->hasEventDateIndex()) {
            return;
        }

        Schema::table('calendar_events', function (Blueprint $table) {
            $table->index('event_date', 'calendar_events_event_date_index');
        });
    }

    public function down(): void
    {
        if (! $this->hasNamedEventDateIndex()) {
            return;
        }

        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropIndex('calendar_events_event_date_index');
        });
    }

    private function hasEventDateIndex(): bool
    {
        return collect(Schema::getIndexes('calendar_events'))
            ->contains(fn (array $index) => in_array('event_date', $index['columns'] ?? [], true));
    }

    private function hasNamedEventDateIndex(): bool
    {
        return collect(Schema::getIndexes('calendar_events'))
            ->contains(fn (array $index) => ($index['name'] ?? null) === 'calendar_events_event_date_index');
    }
};
