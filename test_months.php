<?php
$aylar = ['', 'Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];

$sonDonem = '2026-05';
$parts = explode('-', $sonDonem);
$yil = (int)$parts[0];
$ay = (int)$parts[1];

$labels = [];
$tuketimMap = [
    '2026-05' => 120,
    '2026-03' => 80
];
$values = [];

for ($i = 11; $i >= 0; $i--) {
    $curAy = $ay - $i;
    $curYil = $yil;
    while ($curAy <= 0) {
        $curAy += 12;
        $curYil--;
    }
    
    $donemKey = sprintf("%04d-%02d", $curYil, $curAy);
    $labels[] = $aylar[$curAy] . ' ' . $curYil;
    $values[] = $tuketimMap[$donemKey] ?? 0;
}

print_r($labels);
print_r($values);
