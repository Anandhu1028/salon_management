<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'complaints';

    protected $fillable = [
        'complainant_staff_id',
        'complaint_type_id',
        'subject',
        'description',
        'date_of_complaint',
        'status',
        'evidence_path',
    ];

    protected $casts = [
        'date_of_complaint' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function complainantStaff()
    {
        return $this->belongsTo(
            Staff::class,
            'complainant_staff_id'
        );
    }

    public function complaintType()
    {
        return $this->belongsTo(
            ComplaintType::class,
            'complaint_type_id'
        );
    }
}