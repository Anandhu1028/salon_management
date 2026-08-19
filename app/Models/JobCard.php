<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_card_name',
        'customer_id',
        'service_id',
        'staff_id',
        'subcategory',
        'status',
        'discount_amount',
        'payment_type_id',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
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

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    /**
     * Relationship to JobCardServices (new structure)
     */
    public function serviceItems()
    {
        return $this->hasMany(JobCardService::class);
    }

    /**
     * Calculate total amount from all service items
     */
    public function getSubtotalAmount(): float
    {
        return (float) $this->serviceItems()->sum('amount');
    }

    public function getTotalAmount(): float
    {
        $subtotal = $this->getSubtotalAmount();

        $discount = (float) ($this->discount_amount ?? 0);

        return max(0, $subtotal - $discount);
    }
}
