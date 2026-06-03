<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillBolgeKodu extends Command
{
    protected $signature = 'app:fill-bolge-kodu';
    protected $description = 'Boş BOLGE_KODU alanını bölgeler tablosundan doldur';

    public function handle()
    {
        $this->info('');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('  BÖLGE KODU DOLDURMA');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('');

        // Bölgeler mapping'i al
        $bolgeler = DB::table('bolgeler')
            ->select('bolge_adi', 'bolge_kodu')
            ->get()
            ->keyBy('bolge_adi');

        $this->line("Bölgeler tablosu: {$bolgeler->count()} kaydı yüklendi");
        $this->info('');

        // Boş BOLGE_KODU olanları bul
        $bosAboneler = DB::table('aboneler')
            ->whereNull('BOLGE_KODU')
            ->select('id', 'ABONE_TESIS_NO', 'BOLGE_ADI')
            ->get();

        $this->line("Boş BOLGE_KODU: {$bosAboneler->count()} abone");
        $this->info('');

        $guncellenecekCount = 0;
        $hataCount = 0;

        // Doldur
        foreach ($bosAboneler as $abone) {
            $bolgeAdi = trim($abone->BOLGE_ADI);
            
            if (isset($bolgeler[$bolgeAdi])) {
                $bolgeKodu = $bolgeler[$bolgeAdi]->bolge_kodu;
                $this->line("  ✓ {$abone->ABONE_TESIS_NO}: '{$bolgeAdi}' → Kod: {$bolgeKodu}");
                
                DB::table('aboneler')
                    ->where('id', $abone->id)
                    ->update(['BOLGE_KODU' => $bolgeKodu]);
                    
                $guncellenecekCount++;
            } else {
                $this->line("  ✗ {$abone->ABONE_TESIS_NO}: '{$bolgeAdi}' → Bölge bulunamadı!");
                $hataCount++;
            }
        }

        $this->info('');
        $this->info('════════════════════════════════════════════════════════');
        $this->line("✓ Doldurma tamamlandı!");
        $this->line("  • Güncellenen: {$guncellenecekCount}");
        if ($hataCount > 0) {
            $this->line("  • Hata: {$hataCount}");
        }
        $this->info('════════════════════════════════════════════════════════');

        return 0;
    }
}
