<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('job_card_id')->nullable()->after('id')->constrained('job_cards')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->after('staff_id')->constrained('services')->nullOnDelete();
            $table->string('category')->nullable()->after('service_id');
            $table->string('subcategory')->nullable()->after('category');
            $table->string('complaint_type_text')->nullable()->change();
            $table->text('action_taken')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_card_id');
            $table->dropConstrainedForeignId('service_id');
            $table->dropColumn(['category', 'subcategory']);
        });
    }
};
