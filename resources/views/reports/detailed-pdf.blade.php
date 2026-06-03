<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Detaylı Fatura Raporu</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm 4mm 5mm 4mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif !important;
            color: #212529;
            line-height: 0.9;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 4.5pt; /* Kucuk font, cunku sutun sayisi cok fazla */
        }

        th {
            background-color: #1a73e8;
            color: #FFFFFF;
            font-weight: bold;
            padding: 2px 2px;
            border: 1px solid #1a73e8;
            border-bottom: 1px solid #155bb5;
            text-transform: uppercase;
        }

        td {
            padding: 2px 2px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        .total-row td {
            background-color: #eef4ff !important;
            color: #1a73e8;
            font-weight: bold;
            font-size: 5pt;
            border-top: 1px solid #1a73e8;
            border-bottom: 1px solid #1a73e8;
        }

        .total-label {
            text-align: right;
            color: #1a73e8 !important;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .footer {
            margin-top: 5px;
            font-size: 5pt;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 3px;
        }
    </style>
</head>
<body>

@php
    $filters = $filters ?? [];
    $bolge = !empty($filters['bolge']) ? (is_array($filters['bolge']) ? implode(', ', $filters['bolge']) : $filters['bolge']) : 'Tümü';
    $startD = $filters['start_period'] ?? null;
    $endD   = $filters['end_period']   ?? null;
    $donem  = $startD && $endD ? "{$startD} - {$endD}" : ($startD ?? ($endD ?? 'Tümü'));
    
    $yerlesimRaw = $filters['yerlesim_tipi'] ?? null;
    $yerlesim    = match($yerlesimRaw) { 'koy' => 'Köy', 'merkez' => 'Merkez', default => 'Tümü' };
    $baglanti = !empty($filters['baglanti_grubu']) ? $filters['baglanti_grubu'] : 'Tümü';
    $tarife = !empty($filters['tarife']) ? (is_array($filters['tarife']) ? implode(', ', $filters['tarife']) : $filters['tarife']) : 'Tümü';

    $tK = 0; $tT = 0;
    foreach($results as $row) {
        $valK = $row->fatura_edilecek_toplam_tuketim_kwh ?: ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim + $row->ek_tuketim);
        $tK += (float)$valK;
        $tT += (float)($row->tutar_toplam ?? ($row->toplam_tutar ?? 0));
    }
@endphp

    <!-- Üst Header Alanı -->
    <table style="width: 100%; border: none; margin-bottom: 2px;">
        <tr>
            <!-- Logo Alanı -->
            <td style="width: 30%; border: none; padding: 0; vertical-align: bottom;">
                <img src="{{ public_path('images/logo.png') }}" style="width:140px; height: auto; max-width: 100%; object-fit: contain;" alt="Logo">
            </td>

            <!-- Başlık Alanı -->
            <td style="width: 70%; border: none; padding: 0; vertical-align: bottom; text-align: right;">
                <div style="font-size: 9pt; font-weight: bold; color: #1a73e8; margin-bottom: 1px; line-height: 1.0;">
                    ŞUSKİ GENEL MÜDÜRLÜĞÜ<br>ELEKTRİK BİLGİ YÖNETİM SİSTEMİ
                </div>
                <div style="font-size: 7pt; color: #6c757d; margin-top: 2px;">DETAYLI FATURA RAPORU</div>
            </td>
        </tr>
    </table>

    <div style="border-bottom: 1px solid #1a73e8; margin-bottom: 3px;"></div>

    <!-- Rapor Bilgileri Kutusu -->
    <table style="width: 100%; border: none; margin: 0; font-size: 6pt; margin-bottom: 3px;">
        <tr>
            <td style="border: none; padding: 1px 0; width: 33%;">
                <span style="font-weight: bold; color: #495057;">Bölge:</span> <span style="color: #212529;">{{ $bolge }}</span>
            </td>
            <td style="border: none; padding: 1px 0; width: 33%;">
                <span style="font-weight: bold; color: #495057;">Dönem:</span> <span style="color: #212529;">{{ $donem }}</span>
            </td>
            <td style="border: none; padding: 1px 0; width: 34%;">
                <span style="font-weight: bold; color: #495057;">Yerleşim Yeri:</span> <span style="color: #212529;">{{ $yerlesim }}</span>
            </td>
        </tr>
        <tr>
            <td style="border: none; padding: 1px 0;">
                <span style="font-weight: bold; color: #495057;">Abone Bağlantı:</span> <span style="color: #212529;">{{ $baglanti }}</span>
            </td>
            <td style="border: none; padding: 1px 0;">
                <span style="font-weight: bold; color: #495057;">Tarife:</span> <span style="color: #212529;">{{ $tarife }}</span>
            </td>
            <td style="border: none; padding: 1px 0;">
                <span style="font-weight: bold; color: #495057;">Kayıt Sayısı:</span> <span style="color: #212529;">{{ number_format($results->count(), 0, '', '.') }}</span>
            </td>
        </tr>
    </table>

    <table style="margin-top: 3px;">
        <thead>
            <tr>
                <th width="4%" class="text-center">SIRA</th>
                <th width="9%" class="text-center">DÖNEM</th>
                <th width="12%" class="text-center">TESİSAT NO</th>
                <th width="10%" class="text-center">İLK OKUMA</th>
                <th width="10%" class="text-center">SON OKUMA</th>
                <th width="10%" class="text-right">İLK ENDEKS</th>
                <th width="10%" class="text-right">SON ENDEKS</th>
                <th width="7%" class="text-right">ÇARPAN</th>
                <th width="14%" class="text-right">TÜKETİM (KWH)</th>
                <th width="14%" class="text-right">TUTAR (₺)</th>
            </tr>
        </thead>
        
        <tbody>
            @forelse($results as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row->donem }}</td>
                <td class="text-center">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</td>
                <td class="text-center">{{ $row->ilk_okuma ? \Carbon\Carbon::parse($row->ilk_okuma)->format('d.m.Y') : '—' }}</td>
                <td class="text-center">{{ $row->son_okuma ? \Carbon\Carbon::parse($row->son_okuma)->format('d.m.Y') : '—' }}</td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($row->t0_ilk_endeks, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold;">{{ number_format($row->t0_son_endeks, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->carpan, 2, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold;">{{ number_format(($row->fatura_edilecek_toplam_tuketim_kwh ?: ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim + $row->ek_tuketim)), 2, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold; color: #059669;">{{ number_format($row->tutar_toplam, 2, ',', '.') }} ₺</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding:10px;">Kriterlere uygun kayıt bulunamadı.</td>
            </tr>
            @endforelse
        </tbody>

        @if($results->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                <td class="text-right">{{ number_format($tK, 2, ',', '.') }} kWh</td>
                <td class="text-right">{{ number_format($tT, 2, ',', '.') }} ₺</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <table style="width:100%; border:none; margin:0;">
            <tr>
                <td style="border:none; text-align:left; padding:0; background:transparent;">Smart ŞUSKİ - Akıllı Raporlama Sistemi</td>
                <td style="border:none; text-align:right; padding:0; background:transparent;">Bu belge elektronik ortamda üretilmiştir.</td>
            </tr>
        </table>
    </div>

</body>
</html>
