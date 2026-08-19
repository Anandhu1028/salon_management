<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'name',
        'mobile_country_code',
        'mobile_number',
        'whatsapp_country_code',
        'whatsapp_number',
        'status',
    ];

    public function jobCards()
    {
        return $this->belongsToMany(JobCard::class, 'job_card_staff')->withTimestamps();
    }

    /**
     * Relationship to JobCardServices (new structure)
     */
    public function jobCardServices()
    {
        return $this->belongsToMany(
            JobCardService::class,
            'job_card_service_staff',
            'staff_id',
            'job_card_service_id'
        )->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(StaffAttendance::class);
    }
}
