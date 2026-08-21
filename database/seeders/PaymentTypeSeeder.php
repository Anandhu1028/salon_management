<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Cash', 'UPI', 'Card', 'EC'] as $name) {
            PaymentType::updateOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
