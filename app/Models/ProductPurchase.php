<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPurchase extends Model
{
    protected $fillable = [
        'purchase_number',
        'customer_id',
        'purchase_date',
        'payment_type_id',
        'total_amount',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductPurchaseItem::class);
    }

    /**
     * Kept for backward compatibility with any existing code/views that
     * still reference $purchase->product directly (pre-multi-item). Since
     * product_id no longer lives on this table, this resolves to the
     * product of the purchase's first item.
     */
    public function product(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Product::class,
            ProductPurchaseItem::class,
            'product_purchase_id',
            'id',
            'id',
            'product_id'
        );
    }
}
