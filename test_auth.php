<?php

/**
 * Script untuk test authentication
 * Jalankan: php test_auth.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

echo "=== Test Authentication ===\n\n";

// Test 1: Check session driver
echo "1. Session Driver: " . config('session.driver') . "\n";

// Test 2: Check session path
$path = config('session.files');
echo "2. Session Path: $path\n";
echo "   Exists: " . (is_dir($path) ? "YES" : "NO") . "\n";
echo "   Writable: " . (is_writable($path) ? "YES" : "NO") . "\n";

// Test 3: Try to login
$user = \App\Models\User::where('email', 'admin@test.com')->first();
if ($user) {
    echo "3. User found: {$user->email}\n";
    echo "   Role: {$user->role}\n";
    
    // Try to authenticate
    if (Auth::loginUsingId($user->id)) {
        echo "   ✅ Auth successful!\n";
        echo "   Auth check: " . (Auth::check() ? "YES" : "NO") . "\n";
        echo "   User ID: " . Auth::id() . "\n";
        
        // Test session
        Session::put('test', 'value');
        Session::save();
        echo "   Session saved: " . (Session::has('test') ? "YES" : "NO") . "\n";
    } else {
        echo "   ❌ Auth failed!\n";
    }
} else {
    echo "3. ❌ User not found!\n";
}

echo "\n=== Test Complete ===\n";

