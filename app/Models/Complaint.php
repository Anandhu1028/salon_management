<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'complaints';

    protected $fillable = [
        'job_card_id',
        'staff_id',
        'service_id',
        'category',
        'subcategory',
        'reason',
        'action_taken',
        'compensation',
        'complaint_date',
        'status',
        'complainant_staff_id',
        'complaint_type_id',
        'complaint_type_text',
        'subject',
        'description',
        'date_of_complaint',
        'evidence_path',
    ];

    protected $casts = [
        'complaint_date' => 'date',
        'date_of_complaint' => 'date',
        'compensation' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class, 'job_card_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function complainantStaff()
    {
        return $this->belongsTo(Staff::class, 'complainant_staff_id');
    }

    public function complaintType()
    {
        return $this->belongsTo(ComplaintType::class, 'complaint_type_id');
    }
}
