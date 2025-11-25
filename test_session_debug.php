<?php

/**
 * Script untuk debug session issue
 * Jalankan: php test_session_debug.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

echo "=== Session Debug Info ===\n\n";

// Check session driver
$driver = config('session.driver');
echo "1. Session Driver: $driver\n";

// Check session files (if file driver)
if ($driver === 'file') {
    $path = config('session.files');
    echo "2. Session Path: $path\n";
    echo "   Exists: " . (is_dir($path) ? "YES" : "NO") . "\n";
    echo "   Writable: " . (is_writable($path) ? "YES" : "NO") . "\n";
    
    $files = glob($path . '/*');
    echo "   Files Count: " . count($files) . "\n";
    if (count($files) > 0) {
        $latest = array_reduce($files, function($a, $b) {
            return filemtime($a) > filemtime($b) ? $a : $b;
        });
        echo "   Latest File: " . basename($latest) . "\n";
        echo "   Last Modified: " . date('Y-m-d H:i:s', filemtime($latest)) . "\n";
    }
}

// Check database sessions (if database driver)
if ($driver === 'database') {
    $table = config('session.table');
    echo "2. Session Table: $table\n";
    try {
        $count = DB::table($table)->count();
        echo "   Records: $count\n";
        if ($count > 0) {
            $latest = DB::table($table)->orderBy('last_activity', 'desc')->first();
            echo "   Latest Session ID: " . substr($latest->id, 0, 20) . "...\n";
            echo "   Latest User ID: " . ($latest->user_id ?? 'null') . "\n";
            echo "   Last Activity: " . date('Y-m-d H:i:s', $latest->last_activity) . "\n";
        }
    } catch (\Exception $e) {
        echo "   Error: " . $e->getMessage() . "\n";
    }
}

// Check cookie settings
echo "\n3. Cookie Settings:\n";
echo "   Name: " . config('session.cookie') . "\n";
echo "   Path: " . config('session.path') . "\n";
echo "   Domain: " . (config('session.domain') ?: 'null') . "\n";
echo "   Secure: " . (config('session.secure') ? 'YES' : 'NO') . "\n";
echo "   HttpOnly: " . (config('session.http_only') ? 'YES' : 'NO') . "\n";
echo "   SameSite: " . config('session.same_site') . "\n";
echo "   Lifetime: " . config('session.lifetime') . " minutes\n";

// Check APP_URL
echo "\n4. APP_URL: " . config('app.url') . "\n";

echo "\n=== Debug Complete ===\n";

