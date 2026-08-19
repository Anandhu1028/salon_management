<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {

            $table->dropForeign([
                'against_staff_id'
            ]);

            $table->dropColumn([
                'against_staff_id',
                'priority',
                'department',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {

            $table->foreignId('against_staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->enum(
                'priority',
                ['Low', 'Medium', 'High']
            )->default('Medium');

            $table->string('department')
                ->nullable();

            $table->enum(
                'status',
                ['Open', 'Under Review', 'Resolved', 'Closed']
            )->default('Open');
        });
    }
};