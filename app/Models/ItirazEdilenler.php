<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItirazEdilenler extends Model
{
    protected $table = 'itiraz_edilenler';

    protected $fillable = [
        'tesisat_no',
        'abone_tesis_no',
        'fatura_no',
        'hesap_adi',
        'donem',
        'import_log_id',
        'sira_no',
        'pmum_id',
        'sayac_seri_no',
        'carpan',
        'adres',
        'ilce',
        'dagitim',
        't1_ilk_endeks',
        't1_son_endeks',
        't2_ilk_endeks',
        't2_son_endeks',
        't3_ilk_endeks',
        't3_son_endeks',
        't0_ilk_endeks',
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
        'yillik_tuketim',
        'fatura_edilecek_toplam_tuketim_kwh',
        'tarife',
        'tarife_2',
        'ilk_okuma',
        'son_okuma',
        'son_odeme_tarihi',
        'birim_fiyat',
        'dagitim_birim_fiyat',
        'aktif_tuketim_tl',
        'dagitim_bedeli',
        'reaktif_tl',
        'kdv',
        'genel_toplam',
        'payload',
        'itiraz_edildi',
        'itiraz_aciklamasi',
        'user_id',
        'durum',
        'sonuc_notu',
        'sonuclayan_user_id',
        'sonuclanma_tarihi',
    ];

    protected $casts = [
        'son_odeme_tarihi' => 'date',
        'ilk_okuma' => 'date',
        'son_okuma' => 'date',
        'payload' => 'array',
        'itiraz_edildi' => 'boolean',
        'sonuclanma_tarihi' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }
}
