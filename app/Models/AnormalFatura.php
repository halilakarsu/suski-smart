<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnormalFatura extends Model
{
    protected $table = 'anormal_faturalar';

    protected $fillable = [
        'kesinlesen_fatura_id',
        'user_id',
        'durum',
        'islem_notu',
        'tesisat_no',
        'abone_tesis_no',
        'fatura_no',
        'hesap_adi',
        'donem',
        'ilce',
        'ilce_kodu',
        'baglanti_grubu',
        'yerlesim_turu',
        'tarife',
        'fatura_edilecek_toplam_tuketim_kwh',
        'tutar_toplam',
        'ilk_okuma',
        'son_okuma',
        'anomali_payload',
    ];

    protected $casts = [
        'ilk_okuma' => 'date',
        'son_okuma' => 'date',
        'fatura_edilecek_toplam_tuketim_kwh' => 'decimal:6',
        'tutar_toplam' => 'decimal:2',
        'anomali_payload' => 'array',
    ];

    public function fatura(): BelongsTo
    {
        return $this->belongsTo(KesinlesenFatura::class, 'kesinlesen_fatura_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
