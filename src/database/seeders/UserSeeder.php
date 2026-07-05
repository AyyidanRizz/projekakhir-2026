<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );
        $user->assignRole('super_admin');

        $user = User::firstOrCreate(
            ['email' => 'user@admin.com'],
            ['name' => 'User Account', 'password' => Hash::make('password')]
        );
        $user->assignRole('user');

        $customer1 = User::firstOrCreate(
            ['email' => 'ucupGans@gmail.com'], // Email untuk login
            ['name' => 'Ucup Sarucup', 'password' => Hash::make('rahasia123')] // Password-nya: rahasia123
        );
        $customer1->assignRole('user'); // Tetap berikan role 'user' agar hak aksesnya sama
    }
}