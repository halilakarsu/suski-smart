<?php

use App\Models\KesinlesenFatura;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Repairing KesinlesenFatura tutar_toplam...\n";

$affected = KesinlesenFatura::where('tutar_toplam', 0)
    ->where('genel_toplam', '>', 0)
    ->get();

echo "Found " . $affected->count() . " records with 0 tutar_toplam but > 0 genel_toplam.\n";

foreach ($affected as $fatura) {
    $fatura->update(['tutar_toplam' => $fatura->genel_toplam]);
    echo "Fixed ID: {$fatura->id} - Fatura No: {$fatura->fatura_no}\n";
}

echo "Done.\n";
