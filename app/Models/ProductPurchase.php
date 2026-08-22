<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPurchase extends Model
{
    protected $fillable = [
        'purchase_number',
        'product_id',
        'purchase_date',
        'quantity',
        'unit_price',
        'total_amount',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity'      => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
