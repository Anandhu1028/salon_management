<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintType extends Model
{
    protected $table = 'complaint_types';

    protected $fillable = [
        'name',
        'icon',
        'color',
        'description',
    ];

    public function complaints()
    {
        return $this->hasMany(
            Complaint::class,
            'complaint_type_id'
        );
    }
}