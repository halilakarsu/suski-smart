<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aboneler extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saving(function ($abone) {
            // Normalize names for comparison
            $cleanName = $abone->BOLGE_ADI ? trim(mb_strtoupper($abone->BOLGE_ADI, 'UTF-8')) : null;

            // Fallback resolution for Şanlıurfa / Şanlıurfa Özel (31 / 91)
            if (in_array((string)$abone->BOLGE_KODU, ['31', '91']) || in_array($cleanName, ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])) {
                $historicalFatura = \App\Models\KesinlesenFatura::where('tesisat_no', $abone->ABONE_TESIS_NO)
                    ->whereNotNull('ilce_kodu')
                    ->whereNotIn('ilce_kodu', ['31', '91'])
                    ->orderBy('id', 'desc')
                    ->first();
                if ($historicalFatura) {
                    $abone->BOLGE_KODU = $historicalFatura->ilce_kodu;
                    $abone->BOLGE_ADI = $historicalFatura->ilce;
                    $cleanName = trim(mb_strtoupper($abone->BOLGE_ADI, 'UTF-8'));
                }
            }

            // 1. Try to find code from name if code is missing
            if (empty($abone->BOLGE_KODU) && $cleanName) {
                $bolge = \App\Models\Bolgeler::whereRaw('UPPER(bolge_adi) = ?', [$cleanName])->first();
                if ($bolge) {
                    $abone->BOLGE_KODU = $bolge->bolge_kodu;
                    $abone->BOLGE_ADI = $bolge->bolge_adi; // Sync name case
                }
            }

            // 2. Try to find name from code if name is missing or inconsistent
            if (!empty($abone->BOLGE_KODU)) {
                $bolge = \App\Models\Bolgeler::where('bolge_kodu', $abone->BOLGE_KODU)->first();
                if ($bolge) {
                    $abone->BOLGE_ADI = $bolge->bolge_adi;
                    $cleanName = trim(mb_strtoupper($abone->BOLGE_ADI, 'UTF-8'));
                }
            }

            // 3. Prevent saving if BOLGE_KODU is still missing or remains a dummy region
            if (empty($abone->BOLGE_KODU) || in_array((string)$abone->BOLGE_KODU, ['31', '91']) || in_array($cleanName, ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])) {
                // If we are in a web request, this will result in a 500 error unless handled.
                // However, for data integrity, it's better to stop here.
                throw new \Exception("Bölge (İlçe) kodu bulunamadı veya eşleştirilemedi ya da geçersiz Şanlıurfa/Özel bölgesi girildi. Lütfen geçerli bir bölge seçiniz.");
            }
        });
    }

    protected $table = 'aboneler';
    
    // Kullanıcı girdisinden yazılabilecek alanlar (sistem bayrakları hariç)
    protected $fillable = [
        'ABONE_TESIS_NO',
        'UNVAN',
        'BOLGE_ADI',
        'BOLGE_KODU',
        'ADRES',
        'SAYAC_SERI_NO',
        'PMUM',
        'baglanti_grubu',
        'hesap_adi',
        'OG_durumu',
        'dagitim_merkezi',
        'carpan',
        'abone_grubu',
        'tarife',
        'tesis_cinsi',
        'is_new',
        'is_updated',
        'prev_adres',
        'prev_sayac_seri_no',
        'prev_abone_grubu',
        'prev_baglanti_grubu',
        'prev_hesap_adi',
        'prev_tarife',
        'prev_tesis_cinsi',
        'prev_OG_durumu',
        'guncelleme_tarihi',
        'guncelleme_detay',
        'created_via',
        'import_log_id',
        'notlar',
        'is_active',
        'passive_reason',
        'last_invoice_date',
        'yerlesim_turu',
    ];

    protected $casts = [
        'is_new'     => 'boolean',
        'is_updated' => 'boolean',
        'is_active'  => 'boolean',
        'OG_durumu' => 'boolean',
        'prev_OG_durumu' => 'boolean',
        'sayac_gecmisi' => 'array',
        'sayac_guncelleme_tarihi' => 'datetime',
        'guncelleme_tarihi' => 'datetime',
        'guncelleme_detay' => 'array',
        'carpan' => 'float',
        'last_invoice_date' => 'date',
    ];

    protected $appends = [
        'guncelleme_tarihi_formatted',
        'guncelleme_tarihi_saat_formatted',
        'sayac_guncelleme_tarihi_formatted',
        'sayac_guncelleme_tarihi_saat_formatted',
    ];

    /**
     * Sadece aktif aboneler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Sadece pasif aboneler
     */
    public function scopePassive($query)
    {
        return $query->where('is_active', false);
    }

    public function bolge()
    {
        return $this->belongsTo(Bolgeler::class, 'BOLGE_KODU', 'bolge_kodu');
    }

    public function kesinlesenFaturalar()
    {
        return $this->hasMany(KesinlesenFatura::class, 'tesisat_no', 'ABONE_TESIS_NO');
    }

    /**
     * Tarih formatını özelleştir (DD.MM.YYYY)
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('d.m.Y H:i:s');
    }

    /**
     * Sayaç numarasını güncelle ve geçmişi sakla
     */
    public function updateSayacWithHistory($newSayacNo, $tarih = null)
    {
        $eskiSayac = $this->SAYAC_SERI_NO;
        
        // Aynı sayaçsa işlem yapma
        if ($eskiSayac === $newSayacNo) {
            return;
        }

        // Geçmiş kayıtlarını al (varsa)
        $gecmis = $this->sayac_gecmisi ?? [];
        
        // Eski sayacı geçmişin başına ekle (güncel olan en üstte dursun)
        if (!empty($eskiSayac)) {
            array_unshift($gecmis, [
                'sayac' => $eskiSayac,
                'guncelleme_tarihi' => $tarih ?? now()->toDateTimeString(),
                'updated_from' => 'manual'
            ]);
        }

        // Yeni sayacı ata
        $this->SAYAC_SERI_NO = $newSayacNo;
        $this->sayac_gecmisi = $gecmis;
        $this->sayac_guncelleme_tarihi = $tarih ?? now();
        $this->save();
    }

    /**
     * Bilgileri güncelle ve eski değerleri tut (hesap adı, bağlantı türü, OG durumu)
     */
    public function updateWithHistory($data, $tarih = null)
    {
        $degisiklikler = [];
        
        // hesap_adi
        if (isset($data['hesap_adi']) && $data['hesap_adi'] !== $this->hesap_adi) {
            $degisiklikler['hesap_adi'] = [
                'eski' => $this->hesap_adi,
                'yeni' => $data['hesap_adi'],
                'tarih' => $tarih ?? now()->toDateTimeString()
            ];
            $this->prev_hesap_adi = $this->hesap_adi;
            $this->hesap_adi = $data['hesap_adi'];
        }
        
        // baglanti_grubu
        if (isset($data['baglanti_grubu']) && $data['baglanti_grubu'] !== $this->baglanti_grubu) {
            $degisiklikler['baglanti_grubu'] = [
                'eski' => $this->baglanti_grubu,
                'yeni' => $data['baglanti_grubu'],
                'tarih' => $tarih ?? now()->toDateTimeString()
            ];
            $this->prev_baglanti_grubu = $this->baglanti_grubu;
            $this->baglanti_grubu = $data['baglanti_grubu'];
        }
        
        // OG_durumu
        if (isset($data['OG_durumu']) && $data['OG_durumu'] !== $this->OG_durumu) {
            $degisiklikler['OG_durumu'] = [
                'eski' => $this->OG_durumu ? 'OG' : 'AG',
                'yeni' => $data['OG_durumu'] ? 'OG' : 'AG',
                'tarih' => $tarih ?? now()->toDateTimeString()
            ];
            $this->prev_OG_durumu = $this->OG_durumu;
            $this->OG_durumu = $data['OG_durumu'];
        }
        
        // ADRES
        if (isset($data['ADRES']) && $data['ADRES'] !== $this->ADRES) {
            $degisiklikler['ADRES'] = [
                'eski' => $this->ADRES,
                'yeni' => $data['ADRES'],
                'tarih' => $tarih ?? now()->toDateTimeString()
            ];
            $this->prev_adres = $this->ADRES;
            $this->ADRES = $data['ADRES'];
        }

        // abone_grubu
        if (isset($data['abone_grubu']) && $data['abone_grubu'] !== $this->abone_grubu) {
            $degisiklikler['abone_grubu'] = [
                'eski' => $this->abone_grubu,
                'yeni' => $data['abone_grubu'],
                'tarih' => $tarih ?? now()->toDateTimeString()
            ];
            $this->prev_abone_grubu = $this->abone_grubu;
            $this->abone_grubu = $data['abone_grubu'];
        }

        // tarife
        if (isset($data['tarife']) && $data['tarife'] !== $this->tarife) {
            $degisiklikler['tarife'] = [
                'eski' => $this->tarife,
                'yeni' => $data['tarife'],
                'tarih' => $tarih ?? now()->toDateTimeString()
            ];
            $this->prev_tarife = $this->tarife;
            $this->tarife = $data['tarife'];
        }

        // carpan
        if (isset($data['carpan']) && (float)$data['carpan'] !== (float)$this->carpan) {
            $degisiklikler['carpan'] = [
                'eski' => $this->carpan,
                'yeni' => $data['carpan'],
                'tarih' => $tarih ?? now()->toDateTimeString()
            ];
            $this->carpan = $data['carpan'];
        }

        // BOLGE_ADI
        if (isset($data['BOLGE_ADI']) && $data['BOLGE_ADI'] !== $this->BOLGE_ADI) {
            $degisiklikler['BOLGE_ADI'] = [
                'eski' => $this->BOLGE_ADI,
                'yeni' => $data['BOLGE_ADI'],
                'tarih' => $tarih ?? now()->toDateTimeString()
            ];
            $this->BOLGE_ADI = $data['BOLGE_ADI'];
        }
        
        // Geçmiş kaydı güncelle
        if (!empty($degisiklikler)) {
            $gecmis = $this->guncelleme_detay ?? [];
            $gecmis[] = [
                'degisiklikler' => $degisiklikler,
                'tarih' => $tarih ?? now()->toDateTimeString(),
                'tip' => 'manual'
            ];
            $this->guncelleme_detay = $gecmis;
            $this->guncelleme_tarihi = $tarih ?? now();
        }
        
        // Diğer alanlar
        foreach ($data as $key => $value) {
            if (in_array($key, ['PMUM', 'dagitim_merkezi'])) {
                $this->$key = $value;
            }
        }
        
        $this->save();
        
        return $degisiklikler;
    }

    /**
     * Güncelleme geçmişini formatted olarak döndür
     */
    public function getGuncellemeTarihceFormatted()
    {
        $tarihce = [];
        
        // Güncel bilgiler
        $guncel = [
            'tarih' => $this->guncelleme_tarihi ? $this->guncelleme_tarihi->format('d.m.Y H:i') : $this->updated_at->format('d.m.Y H:i'),
            'hesap_adi' => $this->hesap_adi ?: '–',
            'baglanti_grubu' => $this->baglanti_grubu ?: '–',
            'OG_durumu' => $this->OG_durumu ? 'OG' : 'AG',
            'ADRES' => $this->ADRES ?: '–',
            'tip' => 'Güncel',
            'durum' => 'aktif'
        ];
        $tarihce[] = $guncel;
        
        // Geçmiş kaydları
        if ($this->guncelleme_detay && is_array($this->guncelleme_detay)) {
            foreach ($this->guncelleme_detay as $record) {
                if (isset($record['degisiklikler'])) {
                    $degisiklik = [];
                    foreach ($record['degisiklikler'] as $field => $change) {
                        $degisiklik[] = "$field: {$change['eski']} → {$change['yeni']}";
                    }
                    $tarihce[] = [
                        'tarih' => $record['tarih'] ?? '–',
                        'degisiklikler' => implode(', ', $degisiklik),
                        'tip' => 'Geçmiş',
                        'durum' => 'pasif'
                    ];
                }
            }
        }
        
        // Eski bilgiler varsa ekle
        if ($this->prev_hesap_adi || $this->prev_baglanti_grubu || $this->prev_OG_durumu !== null || $this->prev_adres) {
            $eski = [];
            if ($this->prev_hesap_adi) $eski[] = "Eski Hesap Adı: {$this->prev_hesap_adi}";
            if ($this->prev_baglanti_grubu) $eski[] = "Eski Bağlantı Türü: {$this->prev_baglanti_grubu}";
            if ($this->prev_OG_durumu !== null) $eski[] = "Eski OG Durumu: " . ($this->prev_OG_durumu ? 'OG' : 'AG');
            if ($this->prev_adres) $eski[] = "Eski Adres: {$this->prev_adres}";
            
            if (!empty($eski)) {
                $tarihce[] = [
                    'tarih' => '–',
                    'degisiklikler' => implode(' | ', $eski),
                    'tip' => 'Referans',
                    'durum' => 'referans'
                ];
            }
        }
        
        return $tarihce;
    }

    /**
     * Guncelleme tarihi - Formatted (DD.MM.YYYY)
     */
    public function getGuncellemeTarihiFormattedAttribute()
    {
        if ($this->guncelleme_tarihi) {
            return $this->guncelleme_tarihi->format('d.m.Y');
        }
        return $this->updated_at ? $this->updated_at->format('d.m.Y') : '–';
    }

    /**
     * Guncelleme tarihi - Formatted Saat (DD.MM.YYYY H:i)
     */
    public function getGuncellemeTarihiSaatFormattedAttribute()
    {
        if ($this->guncelleme_tarihi) {
            return $this->guncelleme_tarihi->format('d.m.Y H:i');
        }
        return $this->updated_at ? $this->updated_at->format('d.m.Y H:i') : '–';
    }

    /**
     * Sayac guncelleme tarihi - Formatted (DD.MM.YYYY)
     */
    public function getSayacGuncellemeTarihiFormattedAttribute()
    {
        if ($this->sayac_guncelleme_tarihi) {
            return $this->sayac_guncelleme_tarihi->format('d.m.Y');
        }
        return $this->updated_at ? $this->updated_at->format('d.m.Y') : '–';
    }

    /**
     * Sayac guncelleme tarihi - Formatted Saat (DD.MM.YYYY H:i)
     */
    public function getSayacGuncellemeTarihiSaatFormattedAttribute()
    {
        if ($this->sayac_guncelleme_tarihi) {
            return $this->sayac_guncelleme_tarihi->format('d.m.Y H:i');
        }
        return $this->updated_at ? $this->updated_at->format('d.m.Y H:i') : '–';
    }

    public function getSayacGecmisiFormatted()
    {
        $gecmis = [];
        
        // Güncel sayaç
        $gecmis[] = [
            'sayac' => $this->SAYAC_SERI_NO,
            'tip' => 'Güncel',
            'guncelleme_tarihi' => $this->sayac_guncelleme_tarihi ? $this->sayac_guncelleme_tarihi->format('d.m.Y H:i') : $this->updated_at->format('d.m.Y H:i'),
            'durum' => 'aktif'
        ];

        // Geçmiş kayıtları
        if ($this->sayac_gecmisi && is_array($this->sayac_gecmisi)) {
            foreach ($this->sayac_gecmisi as $record) {
                $gecmis[] = [
                    'sayac' => $record['sayac'],
                    'tip' => 'Geçmiş',
                    'guncelleme_tarihi' => $record['guncelleme_tarihi'] ?? '–',
                    'durum' => 'pasif'
                ];
            }
        }

        return $gecmis;
    }
}
