<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('team_type'); // 'product_team' or 'digital_team'
            $table->date('event_date'); // Removed unique constraint to allow multiple events per day
            
            // Common Fields
            $table->string('content_title')->nullable(); // For Product: 'Content', For Digital: implicitly derived or empty
            $table->string('aipe_pillar')->nullable();
            $table->text('content_objective')->nullable();
            $table->string('format')->nullable();
            $table->text('remarks')->nullable();
            $table->string('boosting_budget')->nullable();
            $table->string('drive_link')->nullable(); 

            // Product Team Fields
            $table->date('shoot_date')->nullable();
            $table->string('color_concern')->nullable();
            $table->string('platform')->nullable();
            $table->string('product')->nullable();

            // Digital Team Fields
            $table->string('post_no')->nullable();
            $table->string('product_focus')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
