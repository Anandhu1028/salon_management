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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complainant_staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('against_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->foreignId('complaint_type_id')->constrained('complaint_types')->onDelete('restrict');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->string('department')->nullable();
            $table->string('subject');
            $table->longText('description');
            $table->date('date_of_complaint');
            $table->enum('status', ['Open', 'Under Review', 'Resolved', 'Closed'])->default('Open');
            $table->string('evidence_path')->nullable();
            $table->timestamps();

            $table->index('complainant_staff_id');
            $table->index('against_staff_id');
            $table->index('complaint_type_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
