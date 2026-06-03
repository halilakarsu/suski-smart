<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aboneler;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportAboneler extends Command
{
    protected $signature = 'import:aboneler';
    protected $description = 'Import subscribers from public/ornek faturalar/konttrol-havuzu.xls';

    public function handle()
    {
        $inputFileName = public_path('ornek faturalar/konttrol-havuzu.xls');
        
        if (!file_exists($inputFileName)) {
            $this->error("Dosya bulunamadı: $inputFileName");
            return;
        }

        $this->info('Dosya yükleniyor...');
        $spreadsheet = IOFactory::load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);
        
        $header = array_shift($rows);
        
        // Sütun eşleştirme
        $mapping = [
            'ABONE_TESIS_NO' => 'G', // Tesisat
            'UNVAN'          => 'R', // Ü N V A N
            'BOLGE_ADI'      => 'B', // BOLGE_ADI
            'ADRES'          => 'H', // A D R E S
            'SAYAC_SERI_NO'  => 'CP', // SAYAC_NO (veya CP hücresindeki değer)
            'KUL_NO'         => 'J', // MUTA NO
        ];

        $this->info('Aktarım başlıyor...');
        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $tesisatNo = trim($row[$mapping['ABONE_TESIS_NO']]);
            
            if (empty($tesisatNo)) {
                $bar->advance();
                continue;
            }

            Aboneler::updateOrCreate(
                ['ABONE_TESIS_NO' => $tesisatNo],
                [
                    'UNVAN'         => $row[$mapping['UNVAN']] ?? null,
                    'BOLGE_ADI'     => $row[$mapping['BOLGE_ADI']] ?? null,
                    'ADRES'         => $row[$mapping['ADRES']] ?? null,
                    'SAYAC_SERI_NO' => $row[$mapping['SAYAC_SERI_NO']] ?? null,
                    'KUL_NO'        => $row[$mapping['KUL_NO']] ?? null,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Aktarım tamamlandı!');
    }
}
