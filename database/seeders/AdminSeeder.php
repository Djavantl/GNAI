<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Primeiro Admin
        User::create([
            'name' => 'Admin GNAI',
            'email' => 'admin@gnai.com',
            'password' => Hash::make('admin'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Segundo Admin',
            'email' => 'admin2@gnai.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
        ]);
    }
}
