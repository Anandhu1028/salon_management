<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'name',
        'email',
        'mobile_number',
        'status',
    ];

    public function jobCards()
    {
        return $this->hasMany(JobCard::class);
    }
}
