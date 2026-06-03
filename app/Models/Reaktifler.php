<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaktifler extends Model
{
    protected $table = 'reaktifler';

    protected $fillable = [
        'tesisat_no',
        'abone_tesis_no',
        'fatura_no',
        'hesap_adi',
        'donem',
        'sira_no',
        'pmum_id',
        'sayac_seri_no',
        'carpan',
        'adres',
        'dagitim',
        't1_ilk_endeks',
        't2_ilk_endeks',
        't3_ilk_endeks',
        't0_ilk_endeks',
        't1_son_endeks',
        't2_son_endeks',
        't3_son_endeks',
        't0_son_endeks',
        'ri_ilk_endeks',
        'ri_son_endeks',
        'ri_fark_endeks',
        'rc_ilk_endeks',
        'rc_son_endeks',
        'rc_fark_endeks',
        't1_tuketim',
        't2_tuketim',
        't3_tuketim',
        'trafo_kaybi_kwh',
        'ek_tuketim',
        'ilce',
        'yillik_tuketim',
        'serbest_tuketici',
        'fatura_edilecek_toplam_tuketim_kwh',
        'tarife',
        'tarife_2',
        'ilk_okuma',
        'son_okuma',
        'birim_fiyat',
        'dagitim_birim_fiyat',
        'aktif_tuketim_tl',
        'dagitim_bedeli',
        'dagitim_bedeli_ek',
        'enerji_fonu',
        'reaktif_tl',
        'acma_kapama_bedeli',
        'gecikme_tutari',
        'trt_fonu',
        'btv',
        'fatura_tutari',
        'fatura_tutari_ek',
        'kdv',
        'genel_toplam',
        'btv_orani',
        'gunluk_ortalama_tuketim',
        'baglanti_grubu',
        'ilce_kodu',
        'son_odeme_tarihi',
        'tutar_toplam',
        'current_row_hash',
        'payload',
        'import_log_id',
        'hamveri_id',
        'aktarim_yapan_id',
        'durum',
        'itiraz_edildi',
        'itiraz_aciklamasi',
    ];

    protected $casts = [
        'son_odeme_tarihi' => 'date',
        'ilk_okuma' => 'date',
        'son_okuma' => 'date',
        'serbest_tuketici' => 'boolean',
        'itiraz_edildi' => 'boolean',
        'payload' => 'array',
    ];

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }

    public function aktarimYapan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aktarim_yapan_id');
    }
}
