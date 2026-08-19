<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $table = 'staff_attendances';

    protected $fillable = [
        'staff_id',
        'year',
        'month',
        'total_working_days',
        'present_days',
        'absent_days',
    ];

    protected $casts = [
        'staff_id'           => 'integer',
        'year'               => 'integer',
        'month'              => 'integer',
        'total_working_days' => 'integer',
        'present_days'       => 'integer',
        'absent_days'        => 'integer',
    ];

    /**
     * Staff member.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get month name.
     */
    public function getMonthNameAttribute(): string
    {
        return \DateTime::createFromFormat(
            '!m',
            $this->month
        )->format('F');
    }

    /**
     * Remaining attendance days.
     */
    public function getRemainingDaysAttribute(): int
    {
        return max(
            0,
            $this->total_working_days
            - $this->present_days
            - $this->absent_days
        );
    }
}