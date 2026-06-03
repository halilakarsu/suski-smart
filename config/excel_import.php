<?php

/**
 * Excel Fatura İçe Aktarma — Sütun Yapılandırması
 *
 * Kaynak: 4 örnek dosya analizi (202511 – 202602 dönemi DEDAŞ faturaları)
 *
 * ┌─────────────────────────────────────────────────────────────────┐
 * │  zorunlu_sutunlar   → Eksikse import anında reddedilir          │
 * │  kabul_edilen        → Whitelist; dışından gelen sütun → red    │
 * │  yoksayilan          → Payload'a alınmaz, DB'ye yazılmaz        │
 * └─────────────────────────────────────────────────────────────────┘
 *
 * Yeni bir dağıtım şirketi şablonu geldiğinde YALNIZCA bu dosyayı
 * güncellemek yeterlidir — servis sınıfına dokunmaya gerek yoktur.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Zorunlu Sütunlar
    |--------------------------------------------------------------------------
    | Bu sütunların tamamı Excel'de BULUNMALIDır. Biri bile eksikse import
    | işlemi başlatılmadan hata döner.
    */
    'zorunlu_sutunlar' => [
        'FATURA NO',
        'Tesisat',
        'TAHAKKUK',
        'T1_ILK_ENDEKS',
        'T1_SON_ENDEKS',
        'T1_FARK',
        'FATURA_TUTAR',
        'TOPLAM TUTAR',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kabul Edilen Sütunlar (Whitelist)
    |--------------------------------------------------------------------------
    | Bu listenin DIŞINDA bir sütun başlığı gelirse import reddedilir.
    | Karşılaştırma büyük/küçük harf ve boşluk duyarsız yapılır.
    */
    'kabul_edilen' => [
        // ── Kimlik / Konum ────────────────────────────────────────────
        'IL', 'BOLGE_ADI', 'BASLIK', 'SR',
        'FATURA NO', 'FT', 'Tesisat', 'A D R E S',
        'MYIL', 'MUTA NO',

        // ── Okuma Tarihleri ───────────────────────────────────────────
        'İLK OKUMA', 'OKUMA', 'TAHAKKUK',

        // ── Durum / Akıbet ────────────────────────────────────────────
        'İPTAL', 'IPTAL', 'TANZIM', 'SON ÖDEME', 'BAGLANTI_DUR', 'AKIBET',

        // ── Abone Bilgileri ───────────────────────────────────────────
        'Ü N V A N', 'TRF', 'BLD', 'CEKILEN_GUC',

        // ── Ödeme / Vezne ─────────────────────────────────────────────
        'VEZNE', 'VEZNEDAR', 'VEZNE_ADI', 'KART_', 'TAKSIT',

        // ── Tüketim ───────────────────────────────────────────────────
        'AKTIF KWH', 'AKTIF_MIKTAR', 'REAKTIF_MIKTAR',
        'MUHT. ILAVE', 'MUHT. TENZIL',

        // ── Bedeller (TL) ─────────────────────────────────────────────
        'AKTİF TÜKETİM', 'REAKTİF TÜKETİM',
        'GUC BEDELI', 'GUC ASIMI BEDELI',
        'SAYAC_AYAR_BEDELI', 'SAYAC_BEDELI', 'SAYAC_MONTAJ_BEDELI',
        'ACMA_KAPAMA_BEDELI',
        'EE_FONU', 'TRT_PAYI', 'BELEDİYE VERGİSİ', 'K.D.V.',
        'DEVIR_YUVARLAMA', 'DEVIR_GECIKME',
        'FATURA_TUTAR', 'TOPLAM TUTAR',
        'DAGITIM BEDELI',
        'PSH_BEDELI', 'ILETIM_BEDELI', 'OKUMA_BEDELI', 'KAYIP_BEDELI',
        'DAGITIM_BEDELI_TENZIL', 'SISKUL_BEDELI',

        // ── İkincil Kimlik / Vergi ────────────────────────────────────
        'UNIPED', 'VDKod', 'VergiNo', 'TC.KimlikNo',
        'SÖZL. GÜCÜ', 'HESAP_KODU', 'Fn',
        'MAHSUP_ILAVE', 'MAHSUP_TENZIL',
        'BLG', 'F_BOLGE_KODU', 'OKUYUCU_KODU', 'OkumaSaat',
        'F_F', 'F_FATURA_NO',
        'OG_DUR', 'KURULU GÜÇ', 'SÖZLEŞME', 'YUVARLAMA',

        // ── KC Alanları ───────────────────────────────────────────────
        'KC_AKTIF_TUKETIM', 'KC_DAGITIM_BEDELI', 'KC_PSH_BEDELI',
        'KC_ILETIM_BEDELI', 'KC_KAYIP_BEDELI', 'KC_OKUMA_BEDELI',

        // ── Abone Grubu / Tarife ──────────────────────────────────────
        'ALT_ISLETME_ADI', 'ABONE_GRUP_ADI', 'HESAP_ADI',

        // ── Birim Fiyatlar ────────────────────────────────────────────
        'GUC_BIRIM_FIYAT', 'GUC_ASIMI_BIRIM_FIYAT', 'DAGITIM_BIRIM_FIYAT',
        'PSH_BIRIM_FIYAT', 'ILETIM_BIRIM_FIYAT', 'OKUMA_BIRIM_FIYAT',
        'KAYIP_BIRIM_FIYAT', 'SISKUL_BIRIM_FIYAT', 'KACAK_CEZA_BIRIM_FIYAT',
        'BIRIM_FIYAT',

        // ── Sayaç / Çarpan ────────────────────────────────────────────
        'SAYAC_NO', 'MAR', 'CARPAN',
        'EFKS_FATURA_ID', 'KARNE', 'KARNE_KULLANIM',

        // ── Endeks Değerleri (T1–T5) ──────────────────────────────────
        'T1_ILK_ENDEKS', 'T1_SON_ENDEKS', 'T1_FARK', 'T1_TK_KWH', 'T1_ILAVE_KWH',
        'T2_ILK_ENDEKS', 'T2_SON_ENDEKS', 'T2_FARK', 'T2_TK_KWH', 'T2_ILAVE_KWH',
        'T3_ILK_ENDEKS', 'T3_SON_ENDEKS', 'T3_FARK', 'T3_TK_KWH', 'T3_ILAVE_KWH',
        'T4_ILK_ENDEKS', 'T4_SON_ENDEKS', 'T4_FARK', 'T4_TK_KWH', 'T4_ILAVE_KWH',
        'T5_ILK_ENDEKS', 'T5_SON_ENDEKS', 'T5_FARK', 'T5_TK_KWH', 'T5_ILAVE_KWH',
        'T0_ILK_ENDEKS', 'T0_SON_ENDEKS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Yoksayılan Sütunlar
    |--------------------------------------------------------------------------
    | Whitelist'te olup payload'a ALINMAYAN sütunlar.
    | Bu sütunlar Excel'de bulunabilir ama DB'ye yazılmaz (gereksiz/gürültülü veri).
    */
    'yoksayilan' => [
        'IL', 'BASLIK', 'MYIL', 'MUTA NO', 'İPTAL', 'IPTAL',
        'CEKILEN_GUC', 'KART_', 'TAKSIT',
        'MUHT. ILAVE', 'MUHT. TENZIL',
        'GUC BEDELI', 'GUC ASIMI BEDELI',
        'SAYAC_AYAR_BEDELI', 'SAYAC_BEDELI', 'SAYAC_MONTAJ_BEDELI',
        'VergiNo', 'TC.KimlikNo',
        'MAHSUP_ILAVE', 'MAHSUP_TENZIL',
        'PSH_BEDELI', 'ILETIM_BEDELI', 'OKUMA_BEDELI', 'KAYIP_BEDELI',
        'DAGITIM_BEDELI_TENZIL', 'SISKUL_BEDELI', 'BAGLANTI_DUR',
        'KC_AKTIF_TUKETIM', 'KC_DAGITIM_BEDELI', 'KC_PSH_BEDELI',
        'KC_ILETIM_BEDELI', 'KC_KAYIP_BEDELI', 'KC_OKUMA_BEDELI',
        'GUC_BIRIM_FIYAT', 'GUC_ASIMI_BIRIM_FIYAT',
        'PSH_BIRIM_FIYAT', 'ILETIM_BIRIM_FIYAT', 'OKUMA_BIRIM_FIYAT',
        'KAYIP_BIRIM_FIYAT', 'SISKUL_BIRIM_FIYAT', 'KACAK_CEZA_BIRIM_FIYAT',
        'AKIBET',
    ],

];
