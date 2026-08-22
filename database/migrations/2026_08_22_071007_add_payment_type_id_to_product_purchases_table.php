<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add payment type relationship to product purchases.
     */
    public function up(): void
    {
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->foreignId('payment_type_id')
                ->nullable()
                ->after('customer_id')
                ->constrained('payment_types')
                ->nullOnDelete();
        });
    }

    /**
     * Remove payment type relationship from product purchases.
     */
    public function down(): void
    {
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_type_id');
        });
    }
};