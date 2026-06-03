<?php

require __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Aboneler;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PhpOffice\PhpSpreadsheet\IOFactory;

echo "Starting Desktop Subscriber Import...\n";

// 1. Database Schema Update
if (!Schema::hasColumn('aboneler', 'tesis_cinsi')) {
    echo "Adding tesis_cinsi columns to database...\n";
    Schema::table('aboneler', function (Blueprint $table) {
        $table->string('tesis_cinsi')->nullable()->after('tarife');
        $table->string('prev_tesis_cinsi')->nullable()->after('prev_tarife');
    });
    echo "Columns added.\n";
}

$file = '/Users/akarsu/Desktop/aboneler.xls';
if (!file_exists($file)) {
    die("File not found: $file\n");
}

try {
    $reader = IOFactory::createReaderForFile($file);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, false);

    echo "Processing " . count($rows) . " rows...\n";

    $stats = ['added' => 0, 'updated' => 0, 'skipped' => 0];

    // Headers are in Row 2 (index 1)
    // Data starts from Row 3 (index 2)
    foreach (array_slice($rows, 2) as $row) {
        $tesisatNo = trim((string)($row[3] ?? ''));
        if (empty($tesisatNo)) {
            $stats['skipped']++;
            continue;
        }

        $data = [
            'BOLGE_ADI'     => trim((string)($row[2] ?? '')),
            'ABONE_TESIS_NO'=> $tesisatNo,
            'SAYAC_SERI_NO' => trim((string)($row[5] ?? '')),
            'ADRES'         => trim((string)($row[7] ?? '')),
            'tarife'        => trim((string)($row[8] ?? '')),
            'tesis_cinsi'   => trim((string)($row[9] ?? '')),
            'is_active'     => true,
            'created_via'   => 'desktop_import'
        ];

        $abone = Aboneler::where('ABONE_TESIS_NO', $tesisatNo)->first();

        if ($abone) {
            // Update if tesis_cinsi is missing or info has changed
            $changed = false;
            if (empty($abone->tesis_cinsi) && !empty($data['tesis_cinsi'])) {
                $abone->tesis_cinsi = $data['tesis_cinsi'];
                $changed = true;
            }
            
            // Other logic for "logical match" if needed, e.g. update address if empty
            if (empty($abone->ADRES) && !empty($data['ADRES'])) {
                $abone->ADRES = $data['ADRES'];
                $changed = true;
            }

            if ($changed) {
                $abone->save();
                $stats['updated']++;
            } else {
                $stats['skipped']++;
            }
        } else {
            // Add missing subscriber
            $data['is_new'] = true;
            Aboneler::create($data);
            $stats['added']++;
        }
    }

    echo "\nResults:\n";
    echo "Total Rows: " . count($rows) . "\n";
    echo "Added: " . $stats['added'] . "\n";
    echo "Updated: " . $stats['updated'] . "\n";
    echo "Skipped/No Change: " . $stats['skipped'] . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
