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
}
