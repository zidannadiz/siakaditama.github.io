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

        $defaultProdi = \App\Models\Prodi::firstOrCreate(
            ['kode_prodi' => 'SI'],
            ['nama_prodi' => 'Sistem Informasi', 'deskripsi' => 'Program Studi Sistem Informasi']
        );

        foreach ($roles as $role => $name) {
            User::firstOrCreate(
                ['email' => $role . '@siakad.com'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'prodi_id' => in_array($role, ['kaprodi', 'admin_prodi']) ? $defaultProdi->id : null,
                ]
            );
            $this->command->info("User $name ready! Email: $role@siakad.com | Password: password");
        }
    }
}

