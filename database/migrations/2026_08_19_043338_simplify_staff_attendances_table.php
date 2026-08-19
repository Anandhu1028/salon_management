<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('staff_attendances', 'present_days')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->dropColumn('present_days');
            });
        }

        if (Schema::hasColumn('staff_attendances', 'absent_days')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->dropColumn('absent_days');
            });
        }

        if (Schema::hasColumn('staff_attendances', 'leave_days')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->dropColumn('leave_days');
            });
        }

        if (Schema::hasColumn('staff_attendances', 'notes')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('staff_attendances', 'present_days')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->unsignedSmallInteger('present_days')->default(0);
            });
        }

        if (!Schema::hasColumn('staff_attendances', 'absent_days')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->unsignedSmallInteger('absent_days')->default(0);
            });
        }

        if (!Schema::hasColumn('staff_attendances', 'leave_days')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->unsignedSmallInteger('leave_days')->default(0);
            });
        }

        if (!Schema::hasColumn('staff_attendances', 'notes')) {
            Schema::table('staff_attendances', function (Blueprint $table) {
                $table->text('notes')->nullable();
            });
        }
    }
};