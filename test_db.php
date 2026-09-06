<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$templates = \App\Models\TemplateKrsKhs::all();
foreach ($templates as $t) {
    echo "ID: " . $t->id . " | Jenis: " . $t->jenis . " | Active: " . $t->is_active . " | Path: " . $t->file_path . PHP_EOL;
}
