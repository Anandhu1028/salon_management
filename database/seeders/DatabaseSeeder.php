<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(CountryCodeSeeder::class);
        $this->call(ComplaintTypeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(DemoDataSeeder::class);
    }
}
