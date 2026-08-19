<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_card_services')) {
            Schema::table('job_card_services', function (Blueprint $table) {
                if (!Schema::hasColumn('job_card_services', 'subcategory')) {
                    $table->string('subcategory')->nullable()->after('service_id');
                }
                if (!Schema::hasColumn('job_card_services', 'amount')) {
                    $table->decimal('amount', 10, 2)->default(0)->after('subcategory');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_card_services')) {
            Schema::table('job_card_services', function (Blueprint $table) {
                if (Schema::hasColumn('job_card_services', 'subcategory')) {
                    $table->dropColumn('subcategory');
                }
            });
        }
    }
};
