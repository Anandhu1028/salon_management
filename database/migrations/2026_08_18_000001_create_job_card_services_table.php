<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create job_card_services table to store individual service items
        if (!Schema::hasTable('job_card_services')) {
            Schema::create('job_card_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_card_id')->constrained('job_cards')->cascadeOnDelete();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->string('subcategory')->nullable();
                $table->decimal('amount', 10, 2)->default(0);
                $table->timestamps();

                $table->index('job_card_id');
                $table->index('service_id');
            });
        }

        // Create job_card_service_staff table for staff assignments per service item
        if (!Schema::hasTable('job_card_service_staff')) {
            Schema::create('job_card_service_staff', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_card_service_id')->constrained('job_card_services')->cascadeOnDelete();
                $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['job_card_service_id', 'staff_id']);
                $table->index('job_card_service_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_card_service_staff');
        Schema::dropIfExists('job_card_services');
    }
};
