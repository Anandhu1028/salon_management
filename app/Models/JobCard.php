<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_card_number',
        'job_card_name',
        'customer_id',
        'service_id',
        'staff_id',
        'subcategory',
        'status',
        'discount_amount',
        'subtotal',
        'total',
        'payment_method',
        'job_card_date',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'job_card_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'job_card_customer')->withTimestamps();
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'job_card_staff')->withTimestamps();
    }

    public function primaryStaff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Relationship to JobCardServices (new structure)
     */
    public function serviceItems()
    {
        return $this->hasMany(JobCardService::class);
    }

    public function jobCardServices()
    {
        return $this->hasMany(JobCardService::class);
    }

    /**
     * Calculate total amount from all service items
     */
    public function getSubtotalAmount(): float
    {
        if ($this->subtotal !== null && (float) $this->subtotal > 0) return (float) $this->subtotal;
        return (float) ($this->relationLoaded('serviceItems')
            ? $this->serviceItems->sum('amount')
            : $this->serviceItems()->sum('amount'));
    }

    public function getDiscountAmount(): float
    {
        return (float) ($this->discount_amount ?? 0);
    }

    public function getTotalAmount(): float
    {
        if ($this->total !== null && (float) $this->total > 0) return (float) $this->total;
        $subtotal = $this->getSubtotalAmount();
        $discount = $this->getDiscountAmount();

        return max(0, $subtotal - $discount);
    }

    public function getFinalAmount(): float
    {
        return $this->getTotalAmount();
    }
}
