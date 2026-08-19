<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('total_working_days')->default(0);
            $table->unsignedSmallInteger('present_days')->default(0);
            $table->unsignedSmallInteger('absent_days')->default(0);
            $table->unsignedSmallInteger('leave_days')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'year', 'month']);
            $table->index(['year', 'month']);
            $table->index('staff_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
