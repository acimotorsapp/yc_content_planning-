<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->string('source_key')->nullable()->after('status');
            $table->string('source_sheet')->nullable()->after('source_key');
            $table->unique('source_key');
        });
    }

    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropUnique(['source_key']);
            $table->dropColumn(['source_key', 'source_sheet']);
        });
    }
};
