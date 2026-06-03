<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$h = \App\Models\KesinlesenFatura::orderBy('id', 'desc')->first();
if ($h) {
    echo "Tesisat: {$h->tesisat_no}\n";
    echo "Genel Toplam: {$h->genel_toplam}\n";
    echo "Tutar Toplam: {$h->tutar_toplam}\n";
    echo "Fatura Tutari: {$h->fatura_tutari}\n";
    $payload = is_string($h->payload) ? json_decode($h->payload, true) : $h->payload;
    if ($payload) {
        foreach ($payload as $k => $v) {
            if (strpos(mb_strtolower($k), 'tutar') !== false || strpos(mb_strtolower($k), 'toplam') !== false || strpos(mb_strtolower($k), 'aktif') !== false) {
                echo "$k => $v\n";
            }
        }
    } else {
        echo "Payload boş veya json parse edilemedi.\n";
    }
} else {
    echo "Kayıt bulunamadı.\n";
}
