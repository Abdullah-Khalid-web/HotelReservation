<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        $superAdmin->syncRoles(['superadmin']);


        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Hotel Owner',
                'password' => Hash::make('password'),
            ]
        );

        $admin->syncRoles(['admin']);


        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Simple User',
                'password' => Hash::make('password'),
            ]
        );

        $user->syncRoles(['user']);
    }
}
