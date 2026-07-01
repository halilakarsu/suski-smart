<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tesis extends Model
{
    use HasFactory;

    protected $table = 'tesisler';

    protected $fillable = [
        'abone_id',
        'sira_no',
        'durum',
        'ilce',
        'mahalle',
        'sokak',
        'kuyu_no',
        'cbs_x',
        'cbs_y',
        'tesis_kurulma_tarihi',
        'hibe_tarihi',
        'abone_tipi',
        'abone_tarihi',
        'sayac_no',
        'abone_no',
        'abone_iptali_yazildi_mi',
        'abone_iptal_edildi_mi',
        'kacak_elektrik_kullanimi_var_mi',
        'kacak_borcu_var_mi',
        'toplam_fatura_tutari',
        'trafo_gucu',
        'trafo_seri_no',
        'trafo_cbs',
        'enh_durumu',
        'kesif_durumu',
        'demontaj_tarihi',
        'demontaj_yapilan_malzemeler',
        'gelir',
        'gider',
    ];

    protected $casts = [
        'cbs_x' => 'decimal:8',
        'cbs_y' => 'decimal:8',
        'tesis_kurulma_tarihi' => 'date',
        'hibe_tarihi' => 'date',
        'abone_tarihi' => 'date',
        'demontaj_tarihi' => 'date',
        'toplam_fatura_tutari' => 'decimal:2',
        'gelir' => 'decimal:2',
        'gider' => 'decimal:2',
    ];

    public function abone(): BelongsTo
    {
        return $this->belongsTo(Aboneler::class, 'abone_id', 'id');
    }

    public function scopeAktif($query)
    {
        return $query->where('durum', 'aktif');
    }

    public function scopePasif($query)
    {
        return $query->where('durum', 'pasif');
    }
}
