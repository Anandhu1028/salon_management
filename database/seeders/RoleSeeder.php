<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'administrator'],
            [
                'description' => 'Full access to the salon management system.',
            ]
        );

        Role::updateOrCreate(
            ['name' => 'manager'],
            [
                'description' => 'Manage salon operations, staff and business activities.',
            ]
        );

        Role::updateOrCreate(
            ['name' => 'staff'],
            [
                'description' => 'Access daily salon operations.',
            ]
        );
    }
}