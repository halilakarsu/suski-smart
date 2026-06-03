<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aboneler;

class FillAboneGrubu extends Command
{
    protected $signature = 'app:fill-abone-grubu';
    protected $description = 'Abone_grubu alanını tarife\'den doldur';

    public function handle()
    {
        $this->info('');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('  ABONE GRUBU DOLDURMA');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('');

        // Boş abone_grubu olanları bul
        $bosAboneler = Aboneler::whereNull('abone_grubu')->get();

        $this->line("Boş abone_grubu: " . $bosAboneler->count());
        $this->info('');

        $guncellenecekCount = 0;

        // Doldur
        foreach ($bosAboneler as $abone) {
            // Tarife varsa onu abone_grubu olarak kullan
            if (!empty($abone->tarife)) {
                $this->line("  • {$abone->ABONE_TESIS_NO}: NULL → '{$abone->tarife}'");
                $abone->update(['abone_grubu' => $abone->tarife]);
                $guncellenecekCount++;
            }
        }

        $this->info('');
        $this->info('════════════════════════════════════════════════════════');
        $this->line("✓ Doldurma tamamlandı! {$guncellenecekCount} abone güncellendi.");
        $this->info('════════════════════════════════════════════════════════');

        return 0;
    }
}
