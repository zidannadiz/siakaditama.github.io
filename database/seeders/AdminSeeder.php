<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@siakad.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@siakad.com');
        $this->command->info('Password: password');
    }
}

