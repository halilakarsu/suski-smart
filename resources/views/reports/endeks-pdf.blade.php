<!DOCTYPE html>
<html lang="tr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Endeks Analiz ve Denetim Raporu</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 12mm 12mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif !important;
            color: #212529;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        th {
            background-color: #1a73e8;
            color: #FFFFFF;
            font-weight: bold;
            padding: 8px 4px;
            border: 1px solid #1a73e8;
            text-transform: uppercase;
        }

        td {
            padding: 6px 4px;
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
            font-size: 9pt;
            border-top: 2px solid #1a73e8;
            border-bottom: 2px solid #1a73e8;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .header-table td { border: none; background: none; }
        
        .footer {
            margin-top: 20px;
            font-size: 8pt;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <!-- Üst Header Alanı -->
    <table class="header-table" style="margin-bottom: 5px;">
        <tr>
            <td style="width: 25%; vertical-align: middle;">
                <img src="{{ public_path('images/logo.png') }}" style="width:180px; height: auto;" alt="Logo">
            </td>
            <td style="width: 75%; text-align: right; vertical-align: middle;">
                <div style="font-size: 16pt; font-weight: bold; color: #1a73e8; line-height: 1.2;">
                    ŞUSKİ GENEL MÜDÜRLÜĞÜ<br>ENDEKS ANALİZ VE DENETİM RAPORU
                </div>
            </td>
        </tr>
    </table>

    <div style="border-bottom: 3px solid #1a73e8; margin-bottom: 10px;"></div>

    <!-- Özet Bilgiler -->
    <table class="header-table" style="margin-bottom: 10px; font-size: 9pt;">
        <tr>
            <td style="width: 33%;"><strong>Dönem:</strong> {{ $filters['start_period'] ?? '-' }} @if(!empty($filters['end_period']))- {{ $filters['end_period'] }}@endif</td>
            <td style="width: 33%; text-align: center;"><strong>Toplam Tüketim:</strong> {{ number_format($totalKWH, 2, ',', '.') }} kWh</td>
            <td style="width: 33%; text-align: right;"><strong>Toplam Tutar:</strong> {{ number_format($totalAmount, 2, ',', '.') }} ₺</td>
        </tr>
    </table>

    <!-- Veri Tablosu -->
    <table>
        <thead>
            <tr>
                <th width="4%" class="text-center">#</th>
                <th width="8%" class="text-center">DÖNEM</th>
                <th width="15%" class="text-center">TESİSAT NO</th>
                <th width="15%" class="text-right">T0 İLK ENDEKS</th>
                <th width="15%" class="text-right">T0 SON ENDEKS</th>
                <th width="12%" class="text-right">T0 FARK</th>
                <th width="15%" class="text-right">TÜKETİM (kWh)</th>
                <th width="16%" class="text-right">TOPLAM TUTAR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $i => $row)
                @php
                    $t1Ilk = (float)str_replace(',', '.', $row->t1_ilk_endeks);
                    $t1Son = (float)str_replace(',', '.', $row->t1_son_endeks);
                    $t2Ilk = (float)str_replace(',', '.', $row->t2_ilk_endeks);
                    $t2Son = (float)str_replace(',', '.', $row->t2_son_endeks);
                    $t3Ilk = (float)str_replace(',', '.', $row->t3_ilk_endeks);
                    $t3Son = (float)str_replace(',', '.', $row->t3_son_endeks);
                    
                    $hasTariff = ($t1Ilk + $t2Ilk + $t3Ilk) > 0;
                    $t0Ilk = $hasTariff ? ($t1Ilk + $t2Ilk + $t3Ilk) : (float)str_replace(',', '.', $row->t0_ilk_endeks);
                    $t0Son = $hasTariff ? ($t1Son + $t2Son + $t3Son) : (float)str_replace(',', '.', $row->t0_son_endeks);
                    $t0Fark = $t0Son - $t0Ilk;
                    
                    $tuketim = (float)($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim);
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $row->donem }}</td>
                    <td class="text-center">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</td>
                    <td class="text-right">{{ number_format($t0Ilk, 3, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($t0Son, 3, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($t0Fark, 3, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($tuketim, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format((float)$row->tutar_toplam, 2, ',', '.') }} ₺</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right" style="padding-right: 15px;">GENEL TOPLAM :</td>
                <td class="text-right">{{ number_format($totalKWH, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalAmount, 2, ',', '.') }} ₺</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <table style="width:100%; border:none; margin:0;">
            <tr>
                <td style="border:none; text-align:left; padding:0; background:transparent;">Smart ŞUSKİ - Akıllı Denetim Sistemi</td>
                <td style="border:none; text-align:right; padding:0; background:transparent;">Bu rapor elektronik ortamda üretilmiştir. Sayfa: {PAGENO}</td>
            </tr>
        </table>
    </div>
</body>

</html>
