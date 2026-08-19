<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop purchase_price — the total is always derived from
        // products.price × product_purchases.quantity at query time.
        if (Schema::hasColumn('product_purchases', 'purchase_price')) {
            Schema::table('product_purchases', function (Blueprint $table) {
                $table->dropColumn('purchase_price');
            });
        }
    }

    public function down(): void
    {
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->after('quantity')->default(0);
        });
    }
};
