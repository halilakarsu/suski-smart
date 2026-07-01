<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Yıllık Arıza Raporu</title>
    <style>
        @page { size: A4 landscape; margin: 5mm 4mm 5mm 4mm; }
        body { font-family: 'DejaVu Sans', sans-serif !important; color: #212529; line-height: 0.9; margin: 0; padding: 0; background-color: #ffffff; }
        table { width: 100%; border-collapse: collapse; font-size: 6pt; }
        th { background-color: #dc2626; color: #FFFFFF; font-weight: bold; padding: 3px 3px; border: 1px solid #dc2626; text-transform: uppercase; }
        td { padding: 2px 3px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
        tr:nth-child(even) td { background-color: #fef2f2; }
        .total-row td { background-color: #fee2e2 !important; color: #991b1b; font-weight: bold; font-size: 7pt; border-top: 1px solid #dc2626; border-bottom: 1px solid #dc2626; }
        .total-label { text-align: right; color: #dc2626 !important; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .footer { margin-top: 5px; font-size: 5pt; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 3px; }
    </style>
</head>
<body>

@php
    $filters = $filters ?? [];
    $bolge = !empty($filters['bolge']) ? (is_array($filters['bolge']) ? implode(', ', $filters['bolge']) : $filters['bolge']) : 'Tümü';
    $yil = $filters['yil'] ?? 'Tümü';
    $toplam = $results->sum('toplam');
@endphp

    <table style="width: 100%; border: none; margin-bottom: 6px;">
        <tr>
            <td style="width: 25%; border: none; padding: 0; vertical-align: middle; text-align: left;">
                <img src="{{ public_path('images/logo.png') }}" style="width:130px; height: auto; max-width: 100%; object-fit: contain;" alt="Logo">
            </td>
            <td style="width: 50%; border: none; padding: 0; vertical-align: middle; text-align: center;">
                <div style="font-size: 10pt; font-weight: bold; color: #dc2626; margin-bottom: 4px; line-height: 1.2;">
                    ŞUSKİ GENEL MÜDÜRLÜĞÜ<br>TESİS BİLGİ YÖNETİM SİSTEMİ
                </div>
                <div style="font-size: 12pt; font-weight: bold; color: #212529; text-transform: uppercase;">
                    YILLIK ARIZA RAPORU
                </div>
            </td>
            <td style="width: 25%; border: none; padding: 0; vertical-align: bottom; text-align: right;">
                <div style="font-size: 6pt; color: #6c757d;">Tarih: {{ now()->format('d.m.Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <div style="border-bottom: 1px solid #dc2626; margin-bottom: 3px;"></div>

    <table style="width: 100%; border: none; margin: 0; font-size: 6pt; margin-bottom: 3px;">
        <tr>
            <td style="border: none; padding: 1px 0; width: 50%;"><span style="font-weight: bold; color: #495057;">Bölge:</span> <span style="color: #212529;">{{ $bolge }}</span></td>
            <td style="border: none; padding: 1px 0; width: 50%;"><span style="font-weight: bold; color: #495057;">Yıl:</span> <span style="color: #212529;">{{ $yil }}</span></td>
        </tr>
    </table>

    <table style="margin-top: 3px;">
        <thead>
            <tr>
                <th width="6%" class="text-center">SIRA</th>
                <th width="12%" class="text-center">YIL</th>
                <th width="40%" class="text-left">İLÇE</th>
                <th width="12%" class="text-center">ARIZA SAYISI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center" style="font-weight: bold; color: #dc2626;">{{ $row->yil }}</td>
                <td class="text-left" style="font-weight: bold;">{{ $row->ilce ?? '—' }}</td>
                <td class="text-center" style="font-weight: bold;">{{ number_format($row->toplam, 0, '', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="padding:10px;">Kriterlere uygun kayıt bulunamadı.</td>
            </tr>
            @endforelse
        </tbody>
        @if($results->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                <td class="text-center">{{ number_format($toplam, 0, '', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <table style="width:100%; border:none; margin:0;">
            <tr>
                <td style="border:none; text-align:left; padding:0; background:transparent;">Smart ŞUSKİ - Tesis Bilgi Yönetim Sistemi</td>
                <td style="border:none; text-align:right; padding:0; background:transparent;">Bu belge elektronik ortamda üretilmiştir.</td>
            </tr>
        </table>
    </div>

</body>
</html>
