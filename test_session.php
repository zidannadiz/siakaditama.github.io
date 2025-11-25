<?php

/**
 * Script untuk test session
 * Jalankan: php test_session.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

echo "=== Test Session Configuration ===\n\n";

// Test 1: Check session driver
$driver = config('session.driver');
echo "1. Session Driver: $driver\n";

// Test 2: Check session path
$path = config('session.files');
echo "2. Session Path: $path\n";
echo "   Exists: " . (is_dir($path) ? "YES" : "NO") . "\n";
echo "   Writable: " . (is_writable($path) ? "YES" : "NO") . "\n";

// Test 3: Check session files
$files = glob($path . '/*');
echo "3. Session Files Count: " . count($files) . "\n";

// Test 4: Check database sessions (if using database driver)
if ($driver === 'database') {
    $table = config('session.table');
    echo "4. Session Table: $table\n";
    try {
        $count = DB::table($table)->count();
        echo "   Records: $count\n";
    } catch (\Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n";
    }
}

// Test 5: Check cookie settings
echo "\n5. Cookie Settings:\n";
echo "   Name: " . config('session.cookie') . "\n";
echo "   Path: " . config('session.path') . "\n";
echo "   Domain: " . (config('session.domain') ?: 'null') . "\n";
echo "   Secure: " . (config('session.secure') ? 'YES' : 'NO') . "\n";
echo "   HttpOnly: " . (config('session.http_only') ? 'YES' : 'NO') . "\n";

// Test 6: Check APP_URL
echo "\n6. APP_URL: " . config('app.url') . "\n";

echo "\n=== Test Complete ===\n";

