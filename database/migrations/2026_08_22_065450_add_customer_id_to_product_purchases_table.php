<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullable because historical purchases were never linked to a
        // customer. New purchases will always require customer_id at the
        // application/validation layer.
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('purchase_number')
                ->constrained('customers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
