<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'f_name' => 'Admin',
                'l_name' => 'User',
                'email' => 'admin@atinhardware.com',
                'role' => 'Administrator',
                'is_active' => true,
                'password' => Hash::make('admin123'),
                'password_changed' => false,
            ]
        );
        
        User::updateOrCreate(
            ['username' => 'cashier'],
            [
                'f_name' => 'Cashier',
                'l_name' => 'User',
                'email' => 'cashier@atinhardware.com',
                'role' => 'cashier',
                'is_active' => true,
                'password' => Hash::make('cash1234'),
                'password_changed' => false,
            ]
        );
    }
}