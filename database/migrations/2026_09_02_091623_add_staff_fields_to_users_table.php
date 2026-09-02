<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The workbook's "Staff ID & Designation" sheet carries an employee number and
     * a job title alongside the name and email, so users gain a home for both.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('staff_id')->nullable()->unique()->after('email');
            $table->string('designation')->nullable()->after('staff_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['staff_id']);
            $table->dropColumn(['staff_id', 'designation']);
        });
    }
};
