<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $administrator = Role::where('name', 'administrator')->firstOrFail();
        $manager = Role::where('name', 'manager')->firstOrFail();
        $staff = Role::where('name', 'staff')->firstOrFail();

        User::updateOrCreate(
            [
                'email' => 'admin@salonpro.com',
            ],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'role_id' => $administrator->id,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'manager@salonpro.com',
            ],
            [
                'name' => 'Salon Manager',
                'password' => 'password',
                'role_id' => $manager->id,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'staff@salonpro.com',
            ],
            [
                'name' => 'Salon Staff',
                'password' => 'password',
                'role_id' => $staff->id,
            ]
        );
    }
}