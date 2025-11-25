<?php

/**
 * Script untuk test HTTP session dengan simulasi request
 * Jalankan: php test_http_session.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;

echo "=== Test HTTP Session dengan Simulasi Request ===\n\n";

$email = 'noer@gmail.com';
$password = 'zidanlangut14';

// Simulate login request
echo "1. Simulating Login Request...\n";

// Create a request to login
$loginRequest = Request::create('/login', 'POST', [
    'email' => $email,
    'password' => $password,
    '_token' => csrf_token(),
]);

// Add session to request
$session = app('session');
$session->start();
$loginRequest->setLaravelSession($session);

echo "   ✓ Session started\n";
echo "   ✓ Session ID: " . substr($session->getId(), 0, 20) . "...\n";

// Handle login request
$response = $kernel->handle($loginRequest);

echo "   Response status: " . $response->getStatusCode() . "\n";

// Check if redirected
if ($response->isRedirection()) {
    $location = $response->headers->get('Location');
    echo "   ✓ Redirected to: $location\n";
} else {
    echo "   ⚠ Not redirected\n";
}

// Check session after login
echo "\n2. Checking Session After Login...\n";
$session->save();
echo "   ✓ Session saved\n";

// Check auth in session
$authKey = 'login_web_' . sha1('web');
if ($session->has($authKey)) {
    $userId = $session->get($authKey);
    echo "   ✓ Auth key exists: $authKey\n";
    echo "   ✓ User ID in session: $userId\n";
} else {
    echo "   ❌ ERROR: Auth key tidak ada di session!\n";
    echo "   Session keys: " . implode(', ', array_keys($session->all())) . "\n";
}

// Check user_id and user_role
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

// Check session file
$driver = config('session.driver');
if ($driver === 'file') {
    $sessionPath = config('session.files');
    $sessionId = $session->getId();
    $sessionFile = $sessionPath . '/' . $sessionId;
    
    echo "\n3. Checking Session File...\n";
    if (file_exists($sessionFile)) {
        echo "   ✓ Session file exists: " . basename($sessionFile) . "\n";
        echo "   ✓ File size: " . filesize($sessionFile) . " bytes\n";
        echo "   ✓ Last modified: " . date('Y-m-d H:i:s', filemtime($sessionFile)) . "\n";
        
        // Read session file content (decoded)
        $content = file_get_contents($sessionFile);
        echo "   ✓ File content length: " . strlen($content) . " bytes\n";
    } else {
        echo "   ❌ ERROR: Session file tidak ditemukan!\n";
        echo "   Expected path: $sessionFile\n";
    }
}

// Now simulate a second request (like clicking a menu)
echo "\n4. Simulating Second Request (Navigation)...\n";

// Create new request to admin route
$navRequest = Request::create('/admin/prodi', 'GET');
$navRequest->setLaravelSession($session);

// Check if we can still authenticate
echo "   Session ID: " . substr($session->getId(), 0, 20) . "...\n";

// Try to get user from session
if ($session->has($authKey)) {
    $userId = $session->get($authKey);
    echo "   ✓ Auth key masih ada: $userId\n";
    
    // Try to authenticate user
    $user = \App\Models\User::find($userId);
    if ($user) {
        echo "   ✓ User ditemukan dari session: {$user->name}\n";
        \Illuminate\Support\Facades\Auth::login($user);
        echo "   ✓ User re-authenticated\n";
    } else {
        echo "   ❌ ERROR: User tidak ditemukan dari session!\n";
    }
} else {
    echo "   ❌ ERROR: Auth key hilang dari session!\n";
    echo "   Session keys: " . implode(', ', array_keys($session->all())) . "\n";
}

// Check auth()->check()
if (\Illuminate\Support\Facades\Auth::check()) {
    echo "   ✓ auth()->check() = true\n";
} else {
    echo "   ❌ ERROR: auth()->check() = false\n";
}

echo "\n=== Test Complete ===\n";

