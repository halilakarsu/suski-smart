<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class KesinlesenFatura extends Model
{
    protected $table = 'kesinlesen_faturalar';

    protected $fillable = [
        'hamveri_id',
        'import_log_id',
        'kayit_durumu',
        'kontrol_edildi',
        'kontrol_tarihi',
        'fatura_no',
        'tesisat_no',
        'hesap_adi',
        'abone_tesis_no',
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
        'donem',
        'aktarim_yapan_id',
        'odeme_durumu',
        'itiraz_edildi',
        'itiraz_aciklamasi',
        'ceza_bedeli',
    ];

    protected $casts = [
        'son_odeme_tarihi' => 'date',
        'ilk_okuma' => 'date',
        'son_okuma' => 'date',
        'tutar_toplam'     => 'decimal:2',
        'kontrol_edildi'   => 'boolean',
        'kontrol_tarihi'   => 'datetime',
        'payload'          => 'array',
        'itiraz_edildi'    => 'boolean',
        
        'birim_fiyat'         => 'decimal:5',
        'dagitim_birim_fiyat' => 'decimal:5',
        'aktif_tuketim_tl'    => 'decimal:2',
        'dagitim_bedeli'      => 'decimal:2',
        'dagitim_bedeli_ek'   => 'decimal:2',
        'enerji_fonu'         => 'decimal:2',
        'reaktif_tl'          => 'decimal:2',
        'acma_kapama_bedeli'  => 'decimal:2',
        'gecikme_tutari'      => 'decimal:2',
        'trt_fonu'            => 'decimal:2',
        'btv'                 => 'decimal:2',
        'fatura_tutari'       => 'decimal:2',
        'fatura_tutari_ek'    => 'decimal:2',
        'kdv'                 => 'decimal:2',
        'genel_toplam'        => 'decimal:2',
        'btv_orani'           => 'decimal:2',
        'ceza_bedeli'         => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aktarim_yapan_id');
    }

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class, 'import_log_id');
    }

    protected function genelToplam(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
        );
    }

    protected function tutarToplam(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
        );
    }

    protected function aktifTuketimTl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
        );
    }

    protected function kdv(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($this->created_at && $this->created_at->lt('2026-04-20 15:05:00')) ? $value / 100 : $value,
        );
    }

    protected function dagitimBedeli(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($this->created_at && $this->created_at->lt('2026-04-20 15:05:00')) ? $value / 100 : $value,
        );
    }

    protected function enerjiFonu(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($this->created_at && $this->created_at->lt('2026-04-20 15:05:00')) ? $value / 100 : $value,
        );
    }

    protected function trtFonu(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($this->created_at && $this->created_at->lt('2026-04-20 15:05:00')) ? $value / 100 : $value,
        );
    }

    protected function btv(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($this->created_at && $this->created_at->lt('2026-04-20 15:05:00')) ? $value / 100 : $value,
        );
    }

    protected function faturaTutari(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($this->created_at && $this->created_at->lt('2026-04-20 15:05:00')) ? $value / 100 : $value,
        );
    }

    protected function reaktifTl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($this->created_at && $this->created_at->lt('2026-04-20 15:05:00')) ? $value / 100 : $value,
        );
    }

    public function abone(): BelongsTo
    {
        return $this->belongsTo(Aboneler::class, 'abone_tesis_no', 'ABONE_TESIS_NO');
    }

    public function anormalFatura()
    {
        return $this->hasOne(AnormalFatura::class, 'kesinlesen_fatura_id');
    }
}
