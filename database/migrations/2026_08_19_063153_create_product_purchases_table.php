<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->date('purchase_date');

            $table->unsignedInteger('quantity');

            $table->decimal('purchase_price', 10, 2);

            $table->timestamps();

            $table->index([
                'product_id',
                'purchase_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_purchases');
    }
};