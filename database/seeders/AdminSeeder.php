<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin GNAI',
            'email' => 'admin@gnai.com',
            'password' => Hash::make('admin'), 
            'is_admin' => true,
        ]);
    }
}
