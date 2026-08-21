<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_name',
        'icon',
        'category',
        'subcategory',
        'status',
    ];

    public function jobCards()
    {
        return $this->hasMany(JobCard::class);
    }

    public function jobCardServices()
    {
        return $this->hasMany(JobCardService::class);
    }
}