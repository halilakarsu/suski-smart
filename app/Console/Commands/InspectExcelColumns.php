<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InspectExcelColumns extends Command
{
    protected $signature = 'app:inspect-excel-columns';
    protected $description = 'Excel dosyasının kolon yapısını inceleme';

    public function handle()
    {
        $excelFile = base_path('aboneler.xls');
        
        if (!file_exists($excelFile)) {
            $this->error("Excel dosyası bulunamadı: $excelFile");
            return 1;
        }

        try {
            $spreadsheet = IOFactory::load($excelFile);
            $sheet = $spreadsheet->getActiveSheet();
            
            $this->info('');
            $this->info('════════════════════════════════════════════════════════');
            $this->info('  EXCEL KOLON YAPISI ANALIZI');
            $this->info('════════════════════════════════════════════════════════');
            $this->info('');
            
            // Header satırını bulma
            $this->line('Header satırlarını ara...');
            for ($row = 1; $row <= 10; $row++) {
                $rowData = [];
                for ($col = 1; $col <= 15; $col++) {
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $value = trim((string)$cell->getValue());
                    if ($value) {
                        $rowData[$col] = $value;
                    }
                }
                
                if (!empty($rowData)) {
                    $this->line("Satır $row: " . json_encode($rowData, JSON_UNESCAPED_UNICODE));
                }
            }
            
            $this->info('');
            $this->line('Örnek veri satırları (5-7):');
            $this->info('');
            
            for ($row = 5; $row <= 7; $row++) {
                $this->line("Satır $row:");
                for ($col = 1; $col <= 8; $col++) {
                    $cell = $sheet->getCellByColumnAndRow($col, $row);
                    $value = trim((string)$cell->getValue());
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $this->line("   [$colLetter$row]: $value");
                }
                $this->line('');
            }
            
            $this->info('════════════════════════════════════════════════════════');

        } catch (\Exception $e) {
            $this->error("Hata: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
