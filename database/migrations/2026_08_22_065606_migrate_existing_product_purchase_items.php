<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * One-time data migration. Every existing product_purchases row still
     * carries its own product_id / quantity / unit_price / total_amount.
     * Before those columns are dropped (next migration) we copy each row
     * into the new product_purchase_items table so no historical purchase
     * loses its product/quantity/price detail.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $purchases = DB::table('product_purchases')
                ->whereNotNull('product_id')
                ->get();

            foreach ($purchases as $purchase) {
                // Skip if this purchase somehow already has items (re-run safety).
                $alreadyMigrated = DB::table('product_purchase_items')
                    ->where('product_purchase_id', $purchase->id)
                    ->exists();

                if ($alreadyMigrated) {
                    continue;
                }

                $quantity = (int) ($purchase->quantity ?? 1) ?: 1;
                $unitPrice = (float) ($purchase->unit_price ?? 0);
                $lineTotal = (float) ($purchase->total_amount ?? ($quantity * $unitPrice));

                DB::table('product_purchase_items')->insert([
                    'product_purchase_id' => $purchase->id,
                    'product_id' => $purchase->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => $lineTotal,
                    'created_at' => $purchase->created_at,
                    'updated_at' => $purchase->updated_at,
                ]);
            }
        });
    }

    public function down(): void
    {
        // Intentionally left as a no-op. Reversing would mean re-writing
        // product_id/quantity/unit_price back onto product_purchases, but
        // those columns are dropped by the next migration and restored
        // there — so the down() of that migration handles recreating the
        // columns; this migration's down() just leaves the items rows in
        // place (they are harmless and still linked correctly).
    }
};
