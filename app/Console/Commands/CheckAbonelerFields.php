<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAbonelerFields extends Command
{
    protected $signature = 'app:check-fields';
    protected $description = 'Abone alanlarını kontrol et';

    public function handle()
    {
        $this->info('');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('  ABONE ALANLARI KONTROL');
        $this->info('════════════════════════════════════════════════════════');
        $this->info('');

        // Istatistikler
        $toplamAbone = DB::table('aboneler')->count();
        $hesapAdiNull = DB::table('aboneler')->whereNull('hesap_adi')->count();
        $ogDurumuNull = DB::table('aboneler')->whereNull('OG_durumu')->count();
        $bgrupuNull = DB::table('aboneler')->whereNull('baglanti_grubu')->count();
        $agrupuNull = DB::table('aboneler')->whereNull('abone_grubu')->count();
        $tarifeNull = DB::table('aboneler')->whereNull('tarife')->count();

        $this->line("Toplam abone: $toplamAbone");
        $this->line("Boş hesap_adi: $hesapAdiNull");
        $this->line("Boş OG_durumu: $ogDurumuNull");
        $this->line("Boş baglanti_grubu: $bgrupuNull");
        $this->line("Boş abone_grubu: $agrupuNull");
        $this->line("Boş tarife: $tarifeNull");

        $this->info('');
        $this->line('Örnek 3 abone:');
        $this->info('');

        $ornekler = DB::table('aboneler')->limit(3)->get();
        foreach ($ornekler as $abone) {
            $this->line("• Tesis No: {$abone->ABONE_TESIS_NO}");
            $this->line("  Hesap Adı: " . ($abone->hesap_adi ?: '[ BOŞ ]'));
            $this->line("  Tarife: " . ($abone->tarife ?: '[ BOŞ ]'));
            $this->line("  Tesis Cinsi: " . ($abone->tesis_cinsi ?: '[ BOŞ ]'));
            $this->line("  OG Durumu: {$abone->OG_durumu}");
            $this->line("  Bağlantı Grubu: " . ($abone->baglanti_grubu ?: '[ BOŞ ]'));
            $this->line("  Abone Grubu: " . ($abone->abone_grubu ?: '[ BOŞ ]'));
            $this->line('');
        }

        $this->info('════════════════════════════════════════════════════════');
    }
}
