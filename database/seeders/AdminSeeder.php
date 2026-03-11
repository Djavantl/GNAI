<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gnai.com'],
            [
                'name' => 'Admin GNAI',
                'password' => Hash::make('admin'),
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin2@gnai.com'],
            [
                'name' => 'Segundo Admin',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
    }
}
