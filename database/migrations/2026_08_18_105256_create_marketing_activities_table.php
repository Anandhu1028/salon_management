<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_activities', function (Blueprint $table) {
            $table->id();

            $table->date('activity_date');

            $table->time('activity_time');

            $table->string('marketing_type', 100);

            $table->string('location', 150);

            $table->unsignedInteger('count')->default(1);

            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('activity_date');
            $table->index('marketing_type');
            $table->index('location');
            $table->index('staff_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_activities');
    }
};