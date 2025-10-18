<?php

namespace Database\Seeders;

use App\Models\Roles\Roles;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Roles::insert([
            ['name' => 'Admin', 'slug' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'slug' => 'manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'User', 'slug' => 'user', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
