<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportLog extends Model
{
    protected $fillable = [
        'user_id',
        'dosya_adi',
        'orijinal_adi',
        'donem',
        'disk',
        'yol',
        'dosya_hash',       // ← Mükerrer yükleme kontrolü için zorunlu
        'toplam_satir',
        'islenen_satir',
        'hata_sayisi',
        'durum',
        'notlar',
        'sutun_eslestirme',
        'isleme_basladi',
        'isleme_bitti',
    ];

    protected $casts = [
        'sutun_eslestirme' => 'array',
        'isleme_basladi'   => 'datetime',
        'isleme_bitti'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hamVeriler(): HasMany
    {
        return $this->hasMany(Hamveri::class, 'import_log_id');
    }

    public function stagingInvoices(): HasMany
    {
        return $this->hasMany(BeklemeKontrolHavuzu::class, 'import_log_id');
    }

    public function kesinlesenFaturalar(): HasMany
    {
        return $this->hasMany(KesinlesenFatura::class, 'import_log_id');
    }

    public function getDurumRenkAttribute(): string
    {
        return match ($this->durum) {
            'bekleniyor' => 'yellow',
            'isleniyor'  => 'blue',
            'tamamlandi' => 'green',
            'hata'       => 'red',
            default      => 'gray',
        };
    }
}
