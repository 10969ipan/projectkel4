<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Irfan',
            'email' => 'irfan@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'store_role' => null,
        ]);

        User::create([
            'name' => 'naura',
            'email' => 'naura@gmail.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'store_role' => null,
        ]);

        User::create([
            'name' => 'nadarul',
            'email' => 'nadarul@gmail.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'store_role' => null,
        ]);

        User::create([
            'name' => 'olip',
            'email' => 'olip@gmail.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'store_role' => null,
        ]);

        User::create([
            'name' => 'ummu',
            'email' => 'ummu@gmail.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'store_role' => null,
        ]);
    }
}