<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = ['admin_pt', 'admin_biak', 'admin_biku', 'admin_prodi', 'kaprodi', 'dosen', 'mahasiswa'];

echo "--- DAFTAR SAMPLE USER UNTUK TESTING ROLE ---\n\n";

foreach ($roles as $role) {
    $user = \App\Models\User::where('role', $role)->first();
    if ($user) {
        echo "Role: " . strtoupper($role) . "\n";
        echo "ID User : " . $user->id . "\n";
        echo "Nama    : " . $user->name . "\n";
        echo "Email   : " . $user->email . "\n";
        echo "----------------------------------------\n";
    } else {
        echo "Role: " . strtoupper($role) . " (Tidak ditemukan user dengan role ini)\n";
        echo "----------------------------------------\n";
    }
}
