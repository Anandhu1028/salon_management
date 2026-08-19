<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_attendances', function (Blueprint $table) {
            $table->unsignedSmallInteger('present_days')
                ->default(0)
                ->after('total_working_days');

            $table->unsignedSmallInteger('absent_days')
                ->default(0)
                ->after('present_days');
        });
    }

    public function down(): void
    {
        Schema::table('staff_attendances', function (Blueprint $table) {
            $table->dropColumn([
                'present_days',
                'absent_days',
            ]);
        });
    }
};