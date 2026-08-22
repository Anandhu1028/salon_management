<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * By the time this runs, migration 000003 has already copied every
     * existing product_id/quantity/unit_price row into
     * product_purchase_items. total_amount and notes are kept on
     * product_purchases (total_amount is still the purchase-level total;
     * notes is left untouched for backward compatibility even though the
     * UI no longer exposes it).
     */
    public function up(): void
    {
        // Only drop the FK if one actually exists — the original table
        // (per the existing project's schema) may or may not have had a
        // named foreign key constraint on product_id.
        $foreignKey = DB::selectOne(<<<'SQL'
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'product_purchases'
              AND COLUMN_NAME = 'product_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        SQL);

        if ($foreignKey) {
            Schema::table('product_purchases', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            });
        }

        Schema::table('product_purchases', function (Blueprint $table) {
            $table->dropColumn(['product_id', 'quantity', 'unit_price']);
        });
    }

    public function down(): void
    {
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('customer_id')->constrained('products')->nullOnDelete();
            $table->unsignedInteger('quantity')->nullable()->after('purchase_date');
            $table->decimal('unit_price', 12, 2)->nullable()->after('quantity');
        });
    }
};
