<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingActivity extends Model
{
    protected $table = 'marketing_activities';

    protected $fillable = [
        'activity_date',
        'marketing_type',
        'location',
        'count',
        'staff_id',
        'notes',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'count' => 'integer',
    ];

    public function staff()
    {
        return $this->belongsTo(
            Staff::class,
            'staff_id'
        );
    }
}