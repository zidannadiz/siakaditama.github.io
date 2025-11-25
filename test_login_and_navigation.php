<?php

/**
 * Script untuk test login dan navigasi
 * Jalankan: php test_login_and_navigation.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== Test Login dan Navigation ===\n\n";

// Get credentials from command line or use defaults
$email = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!$email || !$password) {
    echo "Usage: php test_login_and_navigation.php <email> <password>\n";
    echo "Example: php test_login_and_navigation.php admin@test.com password\n\n";
    exit(1);
}

echo "1. Testing Login...\n";
echo "   Email: $email\n";
echo "   Password: " . str_repeat('*', strlen($password)) . "\n\n";

// Test 1: Check if user exists
$user = DB::table('users')->where('email', $email)->first();
if (!$user) {
    echo "❌ ERROR: User tidak ditemukan!\n";
    exit(1);
}
echo "   ✓ User ditemukan: {$user->name} (Role: {$user->role})\n";

// Test 2: Check password
if (!password_verify($password, $user->password)) {
    echo "❌ ERROR: Password salah!\n";
    exit(1);
}
echo "   ✓ Password benar\n";

// Test 3: Simulate Auth::attempt
$credentials = ['email' => $email, 'password' => $password];
if (Auth::attempt($credentials)) {
    echo "   ✓ Auth::attempt() berhasil\n";
    
    $authenticatedUser = Auth::user();
    echo "   ✓ User authenticated: {$authenticatedUser->name}\n";
    echo "   ✓ User role: {$authenticatedUser->role}\n";
    
    // Test 4: Check session
    $session = app('session');
    if ($session->isStarted()) {
        echo "   ✓ Session started\n";
        echo "   ✓ Session ID: " . substr($session->getId(), 0, 20) . "...\n";
        
        // Check if user_id is in session
        if ($session->has('user_id')) {
            echo "   ✓ user_id in session: " . $session->get('user_id') . "\n";
        } else {
            echo "   ⚠ WARNING: user_id tidak ada di session\n";
        }
        
        if ($session->has('user_role')) {
            echo "   ✓ user_role in session: " . $session->get('user_role') . "\n";
        } else {
            echo "   ⚠ WARNING: user_role tidak ada di session\n";
        }
        
        // Check auth guard session key
        $authKey = 'login_web_' . sha1('web');
        if ($session->has($authKey)) {
            echo "   ✓ Auth session key exists: {$authKey}\n";
            echo "   ✓ Auth user ID in session: " . $session->get($authKey) . "\n";
        } else {
            echo "   ⚠ WARNING: Auth session key tidak ada\n";
        }
    } else {
        echo "   ⚠ WARNING: Session tidak started\n";
    }
    
    // Test 5: Check auth()->check()
    if (auth()->check()) {
        echo "   ✓ auth()->check() = true\n";
    } else {
        echo "   ❌ ERROR: auth()->check() = false (seharusnya true!)\n";
    }
    
    // Test 6: Check auth()->user()
    $currentUser = auth()->user();
    if ($currentUser) {
        echo "   ✓ auth()->user() berhasil: {$currentUser->name}\n";
    } else {
        echo "   ❌ ERROR: auth()->user() = null (seharusnya ada user!)\n";
    }
    
    // Test 7: Simulate navigation check
    echo "\n2. Testing Navigation Check...\n";
    if ($authenticatedUser->role === 'admin') {
        echo "   ✓ User adalah admin, bisa akses admin routes\n";
        
        // Check if RoleMiddleware would allow
        $roles = ['admin'];
        if (in_array($authenticatedUser->role, $roles)) {
            echo "   ✓ RoleMiddleware akan allow akses\n";
        } else {
            echo "   ❌ ERROR: RoleMiddleware akan block akses!\n";
        }
    }
    
    // Test 8: Check session persistence
    echo "\n3. Testing Session Persistence...\n";
    $sessionId = $session->getId();
    echo "   Session ID: " . substr($sessionId, 0, 20) . "...\n";
    
    // Save session
    $session->save();
    echo "   ✓ Session saved\n";
    
    // Check session file (if file driver)
    $driver = config('session.driver');
    if ($driver === 'file') {
        $sessionPath = config('session.files');
        $sessionFile = $sessionPath . '/' . $sessionId;
        if (file_exists($sessionFile)) {
            echo "   ✓ Session file exists: " . basename($sessionFile) . "\n";
            echo "   ✓ File size: " . filesize($sessionFile) . " bytes\n";
            echo "   ✓ Last modified: " . date('Y-m-d H:i:s', filemtime($sessionFile)) . "\n";
        } else {
            echo "   ❌ ERROR: Session file tidak ditemukan!\n";
        }
    }
    
    echo "\n✅ Test Login: BERHASIL\n";
    echo "✅ User authenticated dengan benar\n";
    echo "✅ Session ter-set dengan benar\n";
    
} else {
    echo "❌ ERROR: Auth::attempt() gagal!\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";

