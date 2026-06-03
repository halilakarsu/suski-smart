<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aboneler;

class CheckAboneMismatch extends Command
{
    protected $signature = 'app:check-mismatch';
    protected $description = 'Hatalı veri eşleştirmesini kontrol et';

    public function handle()
    {
        // Baglanti_grubu değeri tesis_cinsi gibi gözüken aboneleri ara
        $aboneler = Aboneler::where('baglanti_grubu', '!=', 'OG')
            ->where('baglanti_grubu', '!=', 'AG')
            ->where('baglanti_grubu', '!=', null)
            ->limit(5)
            ->get();

        $this->info('Hatalı baglanti_grubu değerleri:');
        $this->info('');

        foreach ($aboneler as $abone) {
            $this->line("Tesis No: {$abone->ABONE_TESIS_NO}");
            $this->line("  baglanti_grubu: {$abone->baglanti_grubu}");
            $this->line("  tesis_cinsi: {$abone->tesis_cinsi}");
            $this->line("  tarife: {$abone->tarife}");
            $this->line("  OG_durumu: {$abone->OG_durumu}");
            $this->line('');
        }

        if ($aboneler->isEmpty()) {
            $this->info('Hatalı baglanti_grubu yok! ✓');
        }
    }
}
