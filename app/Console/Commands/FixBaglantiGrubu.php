<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aboneler;

class FixBaglantiGrubu extends Command
{
    protected $signature = 'app:fix-baglanti-grubu';
    protected $description = 'Baglanti_grubu alanını OG_durumu\'na göre düzelt';

    public function handle()
    {
        $this->info('');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('  BAGLANTI_GRUBU DÜZELTME');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('');

        // Hatalı baglanti_grubu değerleri bul
        $hatalı = Aboneler::whereNotIn('baglanti_grubu', ['OG', 'AG', null])
            ->orWhereNull('baglanti_grubu')
            ->get();

        $this->line("Hatalı/Boş baglanti_grubu: " . $hatalı->count());
        $this->info('');

        // Düzelt
        foreach ($hatalı as $abone) {
            $yeniBaglanti = $abone->OG_durumu ? 'OG' : 'AG';
            $this->line("  • {$abone->ABONE_TESIS_NO}: '{$abone->baglanti_grubu}' → '{$yeniBaglanti}'");
            
            $abone->update(['baglanti_grubu' => $yeniBaglanti]);
        }

        $this->info('');
        $this->info('════════════════════════════════════════════════════════');
        $this->line("✓ Düzeltme tamamlandı! {$hatalı->count()} abone güncellendi.");
        $this->info('════════════════════════════════════════════════════════');

        return 0;
    }
}
