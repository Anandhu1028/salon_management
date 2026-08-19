<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPurchase extends Model
{
    protected $fillable = [
        'product_id',
        'purchase_date',
        'quantity',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity'      => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Total amount for this purchase row = product price × quantity.
     * Requires the product relation to be loaded.
     */
    public function getTotalAmountAttribute(): float
    {
        return (float) ($this->product?->price ?? 0) * (float) $this->quantity;
    }
}