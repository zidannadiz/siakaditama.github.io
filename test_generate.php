<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $template = \App\Models\TemplateKrsKhs::first();
    $mahasiswa = \App\Models\Mahasiswa::first();
    
    if (!$template || !$mahasiswa) {
        echo 'Missing template or mahasiswa data';
        exit;
    }
    
    echo 'Using Template ID: ' . $template->id . PHP_EOL;
    echo 'Using Mahasiswa ID: ' . $mahasiswa->id . PHP_EOL;
    
    $service = app(\App\Services\WordTemplateService::class);
    $result = $service->generateDocument($template->id, $mahasiswa->id, null);
    
    echo 'Success! Output path: ' . $result['path'] . PHP_EOL;
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
