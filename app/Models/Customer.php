<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile_number',
    ];

    public function jobCards()
    {
        return $this->belongsToMany(JobCard::class, 'job_card_customer')->withTimestamps();
    }
}