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
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
    public function jobCards()
    {
        return $this->hasMany(JobCard::class);
    }
}