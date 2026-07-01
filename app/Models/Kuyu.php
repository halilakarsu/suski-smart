<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuyu extends Model
{
    use HasFactory;

    protected $table = 'kuyular';

    protected $fillable = [
        'kuyu_no',
        'abone_no',
        'ilce',
        'adres',
        'cbs_x',
        'cbs_y',
        'demontaj_derinligi',
        'montaj_derinligi',
        'depo_bilgisi',
        'boru_tipi',
        'kablo',
        'motor',
        'pompa',
        'debi',
        'aciklama',
        'durum',
        'olusturulma_tarihi',
        'guncellenme_tarihi',
    ];

    protected $casts = [
        'demontaj_derinligi' => 'decimal:2',
        'montaj_derinligi' => 'decimal:2',
        'olusturulma_tarihi' => 'datetime',
        'guncellenme_tarihi' => 'datetime',
    ];

    public function getDurumBadgeClassAttribute(): string
    {
        return match (strtolower($this->durum)) {
            'aktif' => 'badge-aktif',
            'pasif' => 'badge-pasif',
            default => 'badge-pasif',
        };
    }

    public function getDurumLabelAttribute(): string
    {
        return match (strtolower($this->durum)) {
            'aktif' => 'Aktif',
            'pasif' => 'Pasif',
            default => $this->durum ?? '-',
        };
    }

    public function scopeAktif($query)
    {
        return $query->where('durum', 'aktif');
    }

    public function scopePasif($query)
    {
        return $query->where('durum', 'pasif');
    }

    public function arizaKaydi()
    {
        return $this->hasOne(TesisArizaKaydi::class, 'kuyu_no', 'kuyu_no')->latest('id');
    }
}
