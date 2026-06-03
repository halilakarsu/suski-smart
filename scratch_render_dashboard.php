<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

// Mock user
$user = \App\Models\User::first();
Auth::login($user);

try {
    echo View::make('frontend.home.index', [
        'stats' => [
            'kesinlesen' => 0,
            'import' => 0,
            'staging' => 0,
            'itiraz' => 0,
            'reaktif' => 0,
            'abone' => 0,
        ],
        'bolgeDagilim' => collect(),
        'recentLogs' => collect(),
    ])->render();
} catch (\Exception $e) {
    echo $e->getMessage();
}
