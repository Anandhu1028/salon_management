<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $this->call(RoleSeeder::class);

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        $this->call(UserSeeder::class);

        /*
        |--------------------------------------------------------------------------
        | Demo Data
        |--------------------------------------------------------------------------
        */

        $this->call(DemoDataSeeder::class);
    }
}