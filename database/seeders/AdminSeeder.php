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
        $roles = [
            'admin_pt' => 'Admin PT',
            'admin_biku' => 'Admin Biku',
            'admin_biak' => 'Admin BiAk',
            'kaprodi' => 'Kaprodi',
            'admin_prodi' => 'Admin Prodi',
            'dosen' => 'Dosen',
            'mahasiswa' => 'Mahasiswa',
        ];

        foreach ($roles as $role => $name) {
            User::create([
                'name' => $name,
                'email' => $role . '@siakad.com',
                'password' => Hash::make('password'),
                'role' => $role,
            ]);
            $this->command->info("User $name created successfully! Email: $role@siakad.com | Password: password");
        }
    }
}

