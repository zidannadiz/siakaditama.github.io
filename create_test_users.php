<?php

/**
 * Script untuk membuat test users
 * Jalankan: php create_test_users.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;

echo "=== Membuat Test Users ===\n\n";

// Buat Prodi jika belum ada
$prodi = Prodi::firstOrCreate(
    ['kode_prodi' => 'TI'],
    ['nama_prodi' => 'Teknik Informatika']
);
echo "✅ Prodi: {$prodi->nama_prodi}\n";

// Buat Admin
$admin = User::firstOrCreate(
    ['email' => 'admin@test.com'],
    [
        'name' => 'Admin Test',
        'password' => Hash::make('password'),
        'role' => 'admin'
    ]
);
echo "✅ Admin: {$admin->email} (password: password)\n";

// Buat Dosen
$dosenUser = User::firstOrCreate(
    ['email' => 'dosen@test.com'],
    [
        'name' => 'Dosen Test',
        'password' => Hash::make('password'),
        'role' => 'dosen'
    ]
);

$dosen = Dosen::firstOrCreate(
    ['user_id' => $dosenUser->id],
    [
        'nidn' => '1234567890',
        'nama' => 'Dosen Test',
        'email' => 'dosen@test.com',
        'jenis_kelamin' => 'L',
        'status' => 'aktif'
    ]
);
echo "✅ Dosen: {$dosenUser->email} (password: password)\n";

// Buat Mahasiswa
$mahasiswaUser = User::firstOrCreate(
    ['email' => 'mahasiswa@test.com'],
    [
        'name' => 'Mahasiswa Test',
        'password' => Hash::make('password'),
        'role' => 'mahasiswa'
    ]
);

$mahasiswa = Mahasiswa::firstOrCreate(
    ['user_id' => $mahasiswaUser->id],
    [
        'nim' => '1234567890',
        'nama' => 'Mahasiswa Test',
        'prodi_id' => $prodi->id,
        'jenis_kelamin' => 'L',
        'semester' => 3,
        'status' => 'aktif'
    ]
);
echo "✅ Mahasiswa: {$mahasiswaUser->email} (password: password)\n";

echo "\n=== Selesai! ===\n";
echo "\nTest dengan:\n";
echo "Admin: admin@test.com / password\n";
echo "Dosen: dosen@test.com / password\n";
echo "Mahasiswa: mahasiswa@test.com / password\n";

