<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure job_card_services table has the correct structure
        if (Schema::hasTable('job_card_services')) {
            Schema::table('job_card_services', function (Blueprint $table) {
                // Check if price_at_time exists and rename it to amount
                $columns = Schema::getColumnListing('job_card_services');
                if (in_array('price_at_time', $columns) && !in_array('amount', $columns)) {
                    $table->renameColumn('price_at_time', 'amount');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_card_services')) {
            Schema::table('job_card_services', function (Blueprint $table) {
                $columns = Schema::getColumnListing('job_card_services');
                if (in_array('amount', $columns) && !in_array('price_at_time', $columns)) {
                    $table->renameColumn('amount', 'price_at_time');
                }
            });
        }
    }
};
