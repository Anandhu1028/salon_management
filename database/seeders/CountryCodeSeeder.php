<?php

namespace Database\Seeders;

use App\Models\CountryCode;
use Illuminate\Database\Seeder;

class CountryCodeSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'India', 'iso_code' => 'IN', 'dial_code' => '+91', 'is_default' => true],
            ['name' => 'United Arab Emirates', 'iso_code' => 'AE', 'dial_code' => '+971', 'is_default' => false],
            ['name' => 'Saudi Arabia', 'iso_code' => 'SA', 'dial_code' => '+966', 'is_default' => false],
            ['name' => 'Qatar', 'iso_code' => 'QA', 'dial_code' => '+974', 'is_default' => false],
            ['name' => 'Oman', 'iso_code' => 'OM', 'dial_code' => '+968', 'is_default' => false],
            ['name' => 'Kuwait', 'iso_code' => 'KW', 'dial_code' => '+965', 'is_default' => false],
            ['name' => 'Bahrain', 'iso_code' => 'BH', 'dial_code' => '+973', 'is_default' => false],
            ['name' => 'United States', 'iso_code' => 'US', 'dial_code' => '+1', 'is_default' => false],
            ['name' => 'United Kingdom', 'iso_code' => 'GB', 'dial_code' => '+44', 'is_default' => false],
            ['name' => 'Singapore', 'iso_code' => 'SG', 'dial_code' => '+65', 'is_default' => false],
            ['name' => 'Malaysia', 'iso_code' => 'MY', 'dial_code' => '+60', 'is_default' => false],
            ['name' => 'Australia', 'iso_code' => 'AU', 'dial_code' => '+61', 'is_default' => false],
            ['name' => 'Germany', 'iso_code' => 'DE', 'dial_code' => '+49', 'is_default' => false],
            ['name' => 'France', 'iso_code' => 'FR', 'dial_code' => '+33', 'is_default' => false],
            ['name' => 'Canada', 'iso_code' => 'CA', 'dial_code' => '+1', 'is_default' => false],
            ['name' => 'Bangladesh', 'iso_code' => 'BD', 'dial_code' => '+880', 'is_default' => false],
            ['name' => 'Sri Lanka', 'iso_code' => 'LK', 'dial_code' => '+94', 'is_default' => false],
            ['name' => 'Nepal', 'iso_code' => 'NP', 'dial_code' => '+977', 'is_default' => false],
            ['name' => 'Pakistan', 'iso_code' => 'PK', 'dial_code' => '+92', 'is_default' => false],
        ];

        foreach ($countries as $country) {
            CountryCode::firstOrCreate(
                ['dial_code' => $country['dial_code'], 'name' => $country['name']],
                $country
            );
        }
    }
}
