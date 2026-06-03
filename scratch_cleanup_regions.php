<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config(['database.connections.mysql.host' => '127.0.0.1']);

$affected = \DB::table('kesinlesen_faturalar')
    ->where('ilce', 'like', '=%')
    ->orWhere('ilce', 'like', '#%')
    ->update(['ilce' => null]);

echo "Cleared $affected corrupted region values.\n";
