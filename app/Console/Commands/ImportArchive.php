<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KesinlesenFatura;
use App\Models\ImportLog;
use App\Services\ExcelImportService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportArchive extends Command
{
    protected $signature = 'import:archive {file} {donem} {status=bekliyor}';
    protected $description = 'Import an archive Excel file directly to KesinlesenFatura';

    public function handle()
    {
        $filePath = $this->argument('file');
        $donem = $this->argument('donem');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return;
        }

        $this->info("Importing $filePath for period $donem...");

        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            
            $sheetCount = $spreadsheet->getSheetCount();
            $this->info("File has $sheetCount sheets.");
            foreach($spreadsheet->getSheetNames() as $i => $name) {
                $this->info("Sheet $i: $name");
            }

            $sheet = $spreadsheet->getActiveSheet();
            $this->info("Active sheet: " . $sheet->getTitle());
            
            // INCREASE MEMORY LIMIT FOR BIG ARCHIVES
            ini_set('memory_limit', '1024M');

            $cleanStr = function($str) {
                $str = (string)$str;
                $str = str_replace(["\r", "\n", "\t"], ' ', $str);
                $str = preg_replace('/\s+/', ' ', $str); // COLLAPSE SPACES
                $str = mb_strtolower(trim($str), 'UTF-8');
                // Remove combining dot above (common in Turkish İ normalization)
                $str = str_replace("\xCC\x87", "", $str); 
                
                $trMap = [
                    'i' => 'i', 'ı' => 'i', 'ş' => 's', 'ğ' => 'g', 'ü' => 'u', 'ö' => 'o',
                    'ç' => 'c', ' ' => '_', '.' => '', '/' => '_', '-' => '_'
                ];
                $str = str_replace(array_keys($trMap), array_values($trMap), $str);
                return preg_replace('/_+/', '_', trim($str, '_'));
            };

            // Find header row via ITERATOR
            $headerRowIndex = -1;
            $found = false;
            $headers = [];
            
            $rIdx = 0;
            foreach ($sheet->getRowIterator() as $row) {
                if ($rIdx > 50) break;
                
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $currentRow = [];
                $matches = 0;
                
                foreach ($cellIterator as $cell) {
                    $val = $cell->getValue();
                    $currentRow[] = (string)$val;
                    $normCell = $cleanStr($val);
                    if (str_contains($normCell, 'tesisat') || str_contains($normCell, 'fatura') || str_contains($normCell, 'tutar') || str_contains($normCell, 'abone')) {
                        $matches++;
                    }
                }
                
                if ($matches >= 2) {
                    $headerRowIndex = $rIdx;
                    $found = true;
                    $headers = $currentRow;
                    $this->info("Found header row at index $rIdx with $matches matches.");
                    break;
                }
                $rIdx++;
            }

            if (!$found) {
                $this->error("Header row not found.");
                return;
            }

            $headerInfo = [];
            foreach($headers as $idx => $h) {
                if ($h !== null && $h !== '') $headerInfo[] = "[$idx]: $h";
            }
            $this->info("Headers: " . implode(' | ', $headerInfo));
            
            // Mapping logic
            $targetColumns = Schema::getColumnListing('kesinlesen_faturalar');
            $columnKeys = array_flip($targetColumns);

            // Manual mappings
            $manualMap = [
                $cleanStr('TESİSAT') => 'tesisat_no',
                $cleanStr('TESİSAT NO') => 'tesisat_no',
                $cleanStr('FATURA NO') => 'fatura_no',
                $cleanStr('PMUM ID') => 'fatura_no', 
                $cleanStr('FATURA TUTARI') => 'fatura_tutari',
                $cleanStr('GENEL TOPLAM') => 'genel_toplam',
                $cleanStr('K.D.V.') => 'kdv',
                $cleanStr('DAGITIM BEDELI') => 'dagitim_bedeli',
                $cleanStr('REAKTİF TL') => 'reaktif_tl',
                $cleanStr('ABONE ADI') => 'hesap_adi',
                $cleanStr('UNVAN') => 'hesap_adi',
                $cleanStr('ADRES') => 'adres',
                $cleanStr('İLÇE') => 'ilce',
                $cleanStr('CARPAN') => 'carpan',
                $cleanStr('SIRA NO') => 'sira_no',
                $cleanStr('SAYAÇ SERİ NO') => 'sayac_seri_no',
                $cleanStr('İLK OKUMA') => 'ilk_okuma',
                $cleanStr('SON OKUMA') => 'son_okuma',
                $cleanStr('BİRİM FİYAT') => 'birim_fiyat',
                $cleanStr('DAĞITIM BİRİM FİYAT') => 'dagitim_birim_fiyat',
                $cleanStr('ENERJİ FONU') => 'enerji_fonu',
                $cleanStr('AÇMA/ KAPAMA BEDELİ') => 'acma_kapama_bedeli',
                $cleanStr('GECİKME TUTARI') => 'gecikme_tutari',
                $cleanStr('TRT FONU') => 'trt_fonu',
                $cleanStr('BTV') => 'btv',
                $cleanStr('BAĞLANTI GURUBU') => 'baglanti_grubu',
                $cleanStr('GÜNLÜK ORTALAMA TÜKETİM') => 'gunluk_ortalama_tuketim',
                $cleanStr('T1 İLK ENDEKS') => 't1_ilk_endeks',
                $cleanStr('T2 İLK ENDEKS') => 't2_ilk_endeks',
                $cleanStr('T3 İLK ENDEKS') => 't3_ilk_endeks',
                $cleanStr('T1 SON ENDEKS') => 't1_son_endeks',
                $cleanStr('T2 SON ENDEKS') => 't2_son_endeks',
                $cleanStr('T3 SON ENDEKS') => 't3_son_endeks',
            ];

            $headerMap = [];
            foreach ($headers as $colIndex => $h) {
                $cleanH = $cleanStr($h);
                $dbCol = null;

                if (isset($manualMap[$cleanH])) {
                    $dbCol = $manualMap[$cleanH];
                } else {
                    // Try to see if this matches a DB column directly (underscored)
                    $potential = str_replace(' ', '_', $cleanH);
                    if (isset($columnKeys[$potential])) {
                        $dbCol = $potential;
                    }
                }

                if ($dbCol && isset($columnKeys[$dbCol])) {
                    $headerMap[$dbCol] = ['index' => $colIndex];
                }
            }

            $numericColumns = [
                'birim_fiyat', 'dagitim_birim_fiyat', 'aktif_tuketim_tl', 'dagitim_bedeli',
                'dagitim_bedeli_ek', 'enerji_fonu', 'reaktif_tl', 'acma_kapama_bedeli',
                'gecikme_tutari', 'trt_fonu', 'btv', 'fatura_tutari', 'fatura_tutari_ek',
                'kdv', 'genel_toplam', 'btv_orani', 'ceza_bedeli', 'tutar_toplam',
                't1_ilk_endeks', 't2_ilk_endeks', 't3_ilk_endeks', 't0_ilk_endeks',
                't1_son_endeks', 't2_son_endeks', 't3_son_endeks', 'to_son_endeks',
                'ri_ilk_endeks', 'ri_son_endeks', 'ri_fark_endeks', 'rc_ilk_endeks',
                'rc_son_endeks', 'rc_fark_endeks', 't1_tuketim', 't2_tuketim', 't3_tuketim',
                'trafo_kaybi_kwh', 'ek_tuketim', 'yillik_tuketim', 'fatura_edilecek_toplam_tuketim_kwh',
                'gunluk_ortalama_tuketim', 'carpan', 'itiraz_edildi', 'kontrol_edildi', 'serbest_tuketici', 'tutar_toplam', 'ceza_bedeli'
            ];
            $numericKeys = array_flip($numericColumns);
            $targetColumns = Schema::getColumnListing('kesinlesen_faturalar');

            $batchSize = 250;
            $batchItems = [];
            $importedCount = 0;

            // Iterate data rows starting after header
            foreach ($sheet->getRowIterator($headerRowIndex + 2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                if (empty(array_filter($rowData))) continue;

                $finalItem = array_fill_keys($targetColumns, null);
                unset($finalItem['id']);

                foreach ($headerMap as $dbCol => $info) {
                    $colIndex = $info['index'];
                    if (isset($rowData[$colIndex])) {
                        $val = $rowData[$colIndex];
                        
                        // Numeric parsing
                        if (isset($numericKeys[$dbCol])) {
                            if (is_string($val)) {
                                $val = trim($val);
                                if ($val !== '') {
                                    $lastComma = mb_strrpos($val, ',');
                                    $lastDot = mb_strrpos($val, '.');
                                    if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
                                        $val = str_replace('.', '', $val);
                                        $val = str_replace(',', '.', $val);
                                    } else {
                                        $val = str_replace(',', '', $val);
                                    }
                                }
                            }
                            $val = is_numeric($val) ? (float)$val : 0;
                        }
                        
                        // Date parsing
                        if (in_array($dbCol, ['ilk_okuma', 'son_okuma', 'son_odeme_tarihi'])) {
                            if (is_numeric($val) && $val > 20000) {
                                try {
                                    $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                                    $val = $dt->format('Y-m-d');
                                } catch (\Exception $e) { $val = null; }
                            } elseif (is_string($val) && str_contains($val, '/')) {
                                try {
                                    $val = \Carbon\Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d');
                                } catch (\Exception $e) { $val = null; }
                            }
                        }

                        if ($dbCol === 'fatura_no' && is_numeric($val)) {
                            $val = (string)$val;
                        }

                        $finalItem[$dbCol] = $val;
                    }
                }

                // Fallbacks and defaults
                foreach ($numericColumns as $nk) {
                    if ($finalItem[$nk] === null) $finalItem[$nk] = 0;
                }

                if ($finalItem['abone_tesis_no'] === null && $finalItem['tesisat_no'] !== null) {
                    $finalItem['abone_tesis_no'] = $finalItem['tesisat_no'];
                }

                $finalItem['donem'] = $donem;
                $finalItem['odeme_durumu'] = $this->argument('status');
                $finalItem['kayit_durumu'] = 'yeni';
                $finalItem['aktarim_yapan_id'] = 1;
                $finalItem['itiraz_edildi'] = $finalItem['itiraz_edildi'] ?? 0;
                $finalItem['kontrol_edildi'] = $finalItem['kontrol_edildi'] ?? 0;
                $finalItem['created_at'] = now();
                $finalItem['updated_at'] = now();
                $finalItem['payload'] = json_encode([], JSON_UNESCAPED_UNICODE);

                if (empty($finalItem['tesisat_no']) && empty($finalItem['fatura_no'])) continue;

                $batchItems[] = $finalItem;
                if (count($batchItems) >= $batchSize) {
                    DB::table('kesinlesen_faturalar')->insert($batchItems);
                    $importedCount += count($batchItems);
                    $this->info("Processed $importedCount rows...");
                    $batchItems = [];
                }
            }

            if (!empty($batchItems)) {
                DB::table('kesinlesen_faturalar')->insert($batchItems);
                $importedCount += count($batchItems);
            }

            $this->info("Imported $importedCount rows successfully into KesinlesenFatura table.");

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->info($e->getTraceAsString());
        }
    }
}
