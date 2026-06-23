<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Bolgeler;
use App\Models\KesinlesenFatura;

/**
 * 'ŞANLIURFA' ve 'ŞANLIURFA ÖZEL' ilce değerlerini
 * ilce_kodu → bolgeler.bolge_adi üzerinden gerçek ilçe adına
 * normalize eden paylaşımlı controller trait.
 *
 * Kullanım: controller class'ında `use NormalizesIlce;`
 */
trait NormalizesIlce
{
    /**
     * 'ŞANLIURFA' ve 'ŞANLIURFA ÖZEL' kayıtlarını ilce_kodu üzerinden
     * bolgeler tablosundaki gerçek bolge_adi'na normalize eden SQL ifadesi.
     * Bu metodun çalışması için sorguda applyNormalizesIlceJoin() çağrılmış olmalıdır.
     *
     * @param  string  $tableAlias  kesinlesen_faturalar tablosu alias'ı (varsayılan: 'kesinlesen_faturalar')
     */
    protected function normalizedIlceExpr(string $tableAlias = 'kesinlesen_faturalar'): string
    {
        return "CASE
                    WHEN {$tableAlias}.ilce IN ('ŞANLIURFA', 'ŞANLIURFA ÖZEL')
                    THEN COALESCE(bolgeler_norm.bolge_adi, {$tableAlias}.ilce)
                    ELSE {$tableAlias}.ilce
                END";
    }

    /**
     * normalizedIlceExpr() için gerekli olan LEFT JOIN işlemini sorguya ekler.
     */
    protected function applyNormalizesIlceJoin($query, string $tableAlias = 'kesinlesen_faturalar'): void
    {
        // Eğer sorguda join zaten yapılmışsa tekrar yapmamak için kontrol edebiliriz
        // ancak basit kullanımda doğrudan leftJoin ekliyoruz.
        $query->leftJoin('bolgeler as bolgeler_norm', "bolgeler_norm.bolge_kodu", '=', "{$tableAlias}.ilce_kodu");
    }

    /**
     * Bölge filtresi uygular. Hem normal ilçeleri hem de
     * 'ŞANLIURFA'/'ŞANLIURFA ÖZEL' kayıtlarını ilce_kodu eşleşmesiyle kapsar.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array   $bolgeList   Seçilen bolge_adi listesi
     * @param  string  $ilceColumn  Tablo prefix (varsayılan: 'ilce')
     */
    protected function applyBolgeFilter($query, array $bolgeList, string $ilceColumn = 'ilce'): void
    {
        // Seçilen bölge adlarına karşılık gelen bölge kodlarını bul
        $bolgeCodes = Bolgeler::whereIn('bolge_adi', $bolgeList)
            ->pluck('bolge_kodu')
            ->toArray();

        $query->where(function ($q) use ($bolgeList, $bolgeCodes, $ilceColumn) {
            if (!empty($bolgeCodes)) {
                // Bölge koduna göre arama yap (ilçe alanı boş olsa bile ilçe kodundan yakalar)
                // Ayrıca geriye dönük uyumluluk veya kodu olmayanlar için isme göre (ilce) fallback yap.
                $q->whereIn('ilce_kodu', $bolgeCodes)
                  ->orWhereIn($ilceColumn, $bolgeList);
            } else {
                // Sadece isme göre eşleştir
                $q->whereIn($ilceColumn, $bolgeList);
            }
        });
    }

    /**
     * Normalize edilmiş bölge (ilce) listesini döndürür.
     * 'ŞANLIURFA'/'ŞANLIURFA ÖZEL' değerlerini ilce_kodu → bolgeler.bolge_adi
     * üzerinden gerçek ilçe adına çevirir ve listeye dahil eder.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getBolgelerList()
    {
        // 1) Özel değerler dışındaki normal ilçeler
        $normal = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->whereNotNull('ilce')
            ->where('ilce', '!=', '')
            ->where('ilce', 'not like', '=%')
            ->where('ilce', 'not like', '#%')
            ->whereNotIn('ilce', ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])
            ->distinct()
            ->pluck('ilce');

        // 2) ŞANLIURFA / ŞANLIURFA ÖZEL → ilce_kodu üzerinden gerçek bolge_adi
        $special = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->whereIn('ilce', ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])
            ->whereNotNull('ilce_kodu')
            ->where('ilce_kodu', '!=', '')
            ->join('bolgeler', 'bolgeler.bolge_kodu', '=', 'kesinlesen_faturalar.ilce_kodu')
            ->distinct()
            ->pluck('bolgeler.bolge_adi')
            ->filter();

        return $normal->merge($special)->unique()->sort()->values();
    }
}
