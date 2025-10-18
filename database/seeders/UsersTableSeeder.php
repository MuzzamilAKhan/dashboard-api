<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Roles\Roles;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $adminRole = Roles::where('name', 'Admin')->first();
        $managerRole = Roles::where('name', 'Manager')->first();
        $userRole = Roles::where('name', 'User')->first();

        // Seed dummy users
        $users = [
            [
                'name' => 'System Admin',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'dial_code' => '+92',
                'mobile_number' => '3001234567',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'role_id_name' => $adminRole?->name,
                'photo_path' => null,
                'address' => 'Karachi, Pakistan',
                'status' => 1,
                'user_websocket_is_active' => 0,
                'user_websocket_timestamp' => now(),
                'created_by' => null,
                'updated_by' => null,
                'email_otp_verified' => 1,
                'mobile_otp_verified' => 1,
            ],
            [
                'name' => 'Project Manager',
                'username' => 'manager',
                'email' => 'manager@example.com',
                'dial_code' => '+92',
                'mobile_number' => '3339876543',
                'password' => Hash::make('password'),
                'role_id' => $managerRole?->id,
                'role_id_name' => $managerRole?->name,
                'photo_path' => null,
                'address' => 'Lahore, Pakistan',
                'status' => 1,
                'user_websocket_is_active' => 0,
                'user_websocket_timestamp' => now(),
                'created_by' => 1,
                'updated_by' => null,
                'email_otp_verified' => 1,
                'mobile_otp_verified' => 0,
            ],
            [
                'name' => 'Test User',
                'username' => 'user',
                'email' => 'user@example.com',
                'dial_code' => '+92',
                'mobile_number' => '3211122334',
                'password' => Hash::make('password'),
                'role_id' => $userRole?->id,
                'role_id_name' => $userRole?->name,
                'photo_path' => null,
                'address' => 'Islamabad, Pakistan',
                'status' => 1,
                'user_websocket_is_active' => 0,
                'user_websocket_timestamp' => now(),
                'created_by' => 1,
                'updated_by' => null,
                'email_otp_verified' => 0,
                'mobile_otp_verified' => 0,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
