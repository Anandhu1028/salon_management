<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCardService extends Model
{
    protected $table = 'job_card_services';

    protected $fillable = [
        'job_card_id',
        'service_id',
        'subcategory',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship to JobCard
     */
    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }

    /**
     * Relationship to Service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relationship to Staff (many-to-many through pivot table)
     */
    public function staff()
    {
        return $this->belongsToMany(
            Staff::class,
            'job_card_service_staff',
            'job_card_service_id',
            'staff_id'
        )->withTimestamps();
    }
}
