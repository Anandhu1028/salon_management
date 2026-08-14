<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE users
            INNER JOIN roles
                ON roles.name = users.role
            SET users.role_id = roles.id
            WHERE users.role IS NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE users
            INNER JOIN roles
                ON roles.id = users.role_id
            SET users.role = roles.name
        ");
    }
};