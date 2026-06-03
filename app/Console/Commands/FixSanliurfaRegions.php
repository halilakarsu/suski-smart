<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aboneler;
use App\Models\Bolgeler;
use App\Models\KesinlesenFatura;
use Illuminate\Support\Facades\DB;

class FixSanliurfaRegions extends Command
{
    protected $signature = 'fix:sanliurfa-regions {--dry-run : Sadece yapılacakları gösterir, değişiklik yapmaz}';
    protected $description = 'Şanlıurfa ve Şanlıurfa Özel bölgelerindeki aboneleri ilçe kodlarına göre rezerve eder.';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $mappingPath = base_path('abone_bolge_mapping.json');

        if (!file_exists($mappingPath)) {
            $this->error("Mapping dosyası bulunamadı: $mappingPath");
            return 1;
        }

        $mapping = json_decode(file_get_contents($mappingPath), true);
        $this->info(count($mapping) . " adet abone mappingi yüklendi.");

        $bolgeler = Bolgeler::all()->pluck('bolge_adi', 'bolge_kodu')->toArray();
        $this->info("Mevcut bölgeler yüklendi.");

        // Hedef aboneleri bul (31: ŞANLIURFA, 91: ŞANLIURFA ÖZEL)
        $aboneler = Aboneler::whereIn('BOLGE_KODU', ['31', '91'])->get();
        $this->info($aboneler->count() . " adet Şanlıurfa/Özel abonesi bulundu.");

        $updatedCount = 0;
        $notFoundCount = 0;

        foreach ($aboneler as $abone) {
            $tesisatNo = (string)$abone->ABONE_TESIS_NO;
            $newCode = null;
            $newName = null;

            if (isset($mapping[$tesisatNo])) {
                $newCode = $mapping[$tesisatNo];
                $newName = $bolgeler[$newCode] ?? null;
            } else {
                // Mappingde yoksa BOLGE_ADI'na bak
                $currentName = trim($abone->BOLGE_ADI);
                $nameToCode = array_flip($bolgeler);
                if (isset($nameToCode[$currentName])) {
                    $newCode = $nameToCode[$currentName];
                    $newName = $currentName;
                    $this->info("Abone {$tesisatNo} mappingde yok ama '{$currentName}' bölgesi mevcut. Kod: {$newCode}");
                }
            }

            if ($newCode && $newName) {
                $this->line("Abone {$tesisatNo}: {$abone->BOLGE_ADI} ({$abone->BOLGE_KODU}) -> {$newName} ({$newCode})");

                if (!$dryRun) {
                    $abone->update([
                        'BOLGE_KODU' => $newCode,
                        'BOLGE_ADI'  => $newName
                    ]);
                    
                    KesinlesenFatura::where('abone_tesis_no', $tesisatNo)
                        ->where(function($q) {
                            $q->whereIn('ilce_kodu', ['31', '91', 'ŞANLIURFA', 'ŞANLIURFA ÖZEL'])
                              ->orWhereIn('ilce', ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])
                              ->orWhereNull('ilce_kodu');
                        })
                        ->update([
                            'ilce_kodu' => $newCode,
                            'ilce'      => $newName
                        ]);
                }
                $updatedCount++;
            } else {
                $notFoundCount++;
                $this->warn("Abone {$tesisatNo} mapping dosyasında bulunamadı ve geçerli bir bölge adı yok.");
            }
        }

        $this->info("İşlem tamamlandı.");
        $this->info("Güncellenen: $updatedCount");
        $this->info("Mappingde bulunamayan: $notFoundCount");

        if (!$dryRun && $updatedCount > 0) {
            $remaining = Aboneler::whereIn('BOLGE_KODU', ['31', '91'])->count();
            if ($remaining == 0) {
                $this->info("Bölgelerde abone kalmadı, bölgeler siliniyor...");
                Bolgeler::whereIn('bolge_kodu', ['31', '91'])->delete();
                $this->info("Şanlıurfa ve Şanlıurfa Özel bölgeleri silindi.");
            } else {
                $this->warn("Bölgelerde hala $remaining abone var, bölgeler silinmedi.");
            }
        }

        return 0;
    }

}
