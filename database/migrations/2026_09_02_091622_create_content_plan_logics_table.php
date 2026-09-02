<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Holds the "<Month> Logic" sheet of the content plan workbook: the per-product
     * post allocation plus the methodology notes that explain it.
     *
     * Figures stay as strings because the sheet carries formatted values
     * ("3,950", "50.8%", "+4.8 pts YoY", "~800-950") that are meaningful as written.
     */
    public function up(): void
    {
        Schema::create('content_plan_logics', function (Blueprint $table) {
            $table->id();
            $table->string('period')->index();            // e.g. "September 2026"
            $table->string('row_type')->default('allocation'); // allocation | note | source
            $table->string('product')->nullable();
            $table->string('units')->nullable();          // prior month units sold
            $table->string('share')->nullable();          // prior month share
            $table->string('share_shift')->nullable();    // 12-month share shift
            $table->string('previous_retail')->nullable();
            $table->string('forecast')->nullable();
            $table->unsignedInteger('posts_planned')->nullable();
            $table->string('pillar_split')->nullable();
            $table->text('rationale')->nullable();        // "Why This Allocation", or the note body
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['period', 'row_type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_plan_logics');
    }
};
