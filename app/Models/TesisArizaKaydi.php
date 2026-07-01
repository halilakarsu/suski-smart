<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TesisArizaKaydi extends Model
{
    use HasFactory;

    protected $table = 'tesis_ariza_kayitlari';

    protected $fillable = [
        'abone_id', 'sira_no', 'kuyu_no', 'tutanak_no', 'ekip',
        'tarih', 'abone_no', 'sayac_no', 'ilce', 'mahalle',
        'sokak', 'cbs_x', 'cbs_y', 'ariza_turu', 'durum', 'aciklama',
    ];

    protected $casts = [
        'tarih' => 'date',
        'cbs_x' => 'decimal:8',
        'cbs_y' => 'decimal:8',
    ];

    public function abone(): BelongsTo
    {
        return $this->belongsTo(Aboneler::class, 'abone_id', 'id');
    }

    public function scopeIlce($query, $ilce)
    {
        return $query->where('ilce', $ilce);
    }

    public function scopeArizaTuru($query, $tur)
    {
        return $query->where('ariza_turu', $tur);
    }

    public function scopeTarihBetween($query, $baslangic, $bitis)
    {
        return $query->whereBetween('tarih', [$baslangic, $bitis]);
    }
}
