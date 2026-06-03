<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bolgeler;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\File;

class ImportBolgeler extends Command
{
    protected $signature = 'import:bolgeler';
    protected $description = 'Import unique regions from all example invoices in public/ornek faturalar';

    public function handle()
    {
        $directory = public_path('ornek faturalar');
        $files = File::files($directory);
        
        $uniqueBolgeler = [];

        foreach ($files as $file) {
            $path = $file->getRealPath();
            $extension = $file->getExtension();
            
            if (!in_array($extension, ['xlsx', 'xls'])) continue;

            $this->info("Dosya inceleniyor: " . $file->getFilename());
            
            try {
                $spreadsheet = IOFactory::load($path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, true);
                array_shift($rows); // Header

                foreach ($rows as $row) {
                    $name = trim($row['B'] ?? '');  // BOLGE_ADI
                    $code = trim($row['BF'] ?? ''); // BLG (Code)
                    
                    // Eğer BF boşsa BG'ye bak (F_BOLGE_KODU)
                    if (empty($code)) {
                        $code = trim($row['BG'] ?? '');
                    }

                    if (empty($name) || empty($code) || $name == 'BOLGE_ADI') continue;

                    // İsmi temizle (Örn: "ŞANLIURFA ÖZEL" yerine daha anlamlı bir isim varsa onu tercih et)
                    if (!isset($uniqueBolgeler[$code]) || (strlen($name) > 2 && $uniqueBolgeler[$code] == 'ŞANLIURFA ÖZEL')) {
                        $uniqueBolgeler[$code] = $name;
                    }
                }
            } catch (\Exception $e) {
                $this->error("Hata: " . $e->getMessage());
            }
        }

        $this->info(count($uniqueBolgeler) . ' benzersiz bölge bulundu. Aktarım başlıyor...');

        foreach ($uniqueBolgeler as $code => $name) {
            Bolgeler::updateOrCreate(
                ['bolge_kodu' => $code],
                ['bolge_adi'  => $name]
            );
            $this->line("Kaydedildi: $code - $name");
        }

        $this->info('Bölge aktarımı başarıyla tamamlandı!');
    }
}
