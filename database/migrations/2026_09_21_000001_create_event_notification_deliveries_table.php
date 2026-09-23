<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_notification_deliveries', function (Blueprint $table) {
            $table->id();
            $table->json('user_ids')->nullable();
            $table->date('target_date');
            $table->unsignedInteger('days_ahead')->default(5);
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['target_date', 'days_ahead'], 'event_notification_deliveries_unique');
            $table->index('target_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_notification_deliveries');
    }
};
