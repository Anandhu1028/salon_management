<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'dial_code',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get active country codes ordered with default first, then by name.
     */
    public static function getActiveCodes()
    {
        return static::active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the configured default dial code.
     */
    public static function getDefaultCode()
    {
        return static::active()
            ->where('is_default', true)
            ->value('dial_code') ?? '+91';
    }
}
