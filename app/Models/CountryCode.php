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

    public function getFlagAttribute(): string
    {
        if (empty($this->iso_code) || strlen($this->iso_code) < 2) {
            return '🌐';
        }
        $code = strtoupper($this->iso_code);
        return mb_chr(127397 + ord($code[0])) . mb_chr(127397 + ord($code[1]));
    }

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
}
