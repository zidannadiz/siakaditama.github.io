<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$middleware = new App\Http\Middleware\RoleMiddleware();
$req = Illuminate\Http\Request::create('/');
// Mock auth
$app['auth']->shouldUse('web');
// We just want to see how Laravel resolves the parameters.
// Actually, let's just make a web request to a test route that prints the roles array.
