<!DOCTYPE html>
<html lang="tr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kesinleşen Faturalar Raporu</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0mm 4mm 2mm 4mm;
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
            font-size: 5pt;
        }

        th {
            background-color: #1a73e8;
            color: #FFFFFF;
            font-weight: bold;
            padding: 1px 2px;
            border: 1px solid #1a73e8;
            border-bottom: 1px solid #155bb5;
            text-transform: uppercase;
        }

        td {
            padding: 1px 2px;
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
            font-size: 6pt;
            border-top: 1px solid #1a73e8;
            border-bottom: 1px solid #1a73e8;
        }

        .total-label {
            text-align: right;
            color: #1a73e8 !important;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

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

    <!-- Üst Header Alanı -->
    <table style="width: 100%; border: none; margin-bottom: 2px;">
        <tr>
            <!-- Logo Alanı -->
            <td style="width: 30%; border: none; padding: 0; vertical-align: bottom;">
                <img src="{{ public_path('images/logo.png') }}"
                    style="width:140px; height: auto; max-width: 100%; object-fit: contain;" alt="Logo">
            </td>

            <!-- Başlık Alanı -->
            <td style="width: 70%; border: none; padding: 0; vertical-align: bottom; text-align: right;">
                <div style="font-size: 9pt; font-weight: bold; color: #1a73e8; margin-bottom: 1px; line-height: 1.0;">
                    ŞUSKİ GENEL MÜDÜRLÜĞÜ<br>ELEKTRİK FATURALARI YÖNETİM SİSTEMİ
                </div>
            </td>
        </tr>
    </table>

    <!-- Kalın Mavi Çizgi -->
    <div style="border-bottom: 1px solid #1a73e8; margin-bottom: 3px;"></div>

    <!-- Rapor Bilgileri Kutusu -->

    <table style="width: 100%; border: none; margin: 0; font-size: 6pt; margin-bottom: 3px;">
        <tr>
            <td style="border: none; padding: 1px 0; width: 50%;">
                <span style="font-weight: bold; color: #495057;">Genel Toplam Tutar:</span> <span
                    style="color: #212529;">{{ number_format((float) $totalAmount, 2, ',', '.') }} ₺ </span>
            </td>
            <td style="border: none; padding: 1px 0; width: 50%;">
                <span style="font-weight: bold; color: #495057;"> Toplam Tüketim :</span> <span
                    style="color: #212529;">{{ number_format((float) $totalKWH, 2, ',', '.') }} kWh </span>
            </td>
        </tr>
        <tr>
            <td style="border: none; padding: 1px 0;">
                <span style="font-weight: bold; color: #495057;">Dönem:</span> <span
                    style="color: #212529;">{{ $periodTitle }}</span>
            </td>
            <td style="border: none; padding: 1px 0;">
                <span style="font-weight: bold; color: #495057;">Kayıt Sayısı:</span> <span
                    style="color: #212529;">{{ number_format($faturalar->count(), 0, '', '.') }}</span>
            </td>
        </tr>
    </table>
    </div>

    <!-- Veri Tablosu -->
    <table>
        <thead>
            <tr>
                <th width="8%" class="text-center">SIRA</th>
                <th width="32%" class="text-left">ADRES</th>
                <th width="20%" class="text-center">TESİSAT NO</th>
                <th width="20%" class="text-right">TÜKETİM (KWH)</th>
                <th width="20%" class="text-right">TOPLAM TUTAR</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($faturalar as $f)
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td class="text-left">{{ $f->adres }}</td>
                    <td class="text-center">{{ $f->abone_tesis_no ?? $f->tesisat_no }}</td>
                    <td class="text-right">{{ number_format((float) $f->fatura_edilecek_toplam_tuketim_kwh, 2, ',', '.') }}
                    </td>
                    <td class="text-right">{{ number_format((float) $f->tutar_toplam, 2, ',', '.') }} ₺</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                <td class="text-right">{{ number_format((float) $totalKWH, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format((float) $totalAmount, 2, ',', '.') }} ₺</td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width:100%; border:none; margin:0;">
            <tr>
                <td style="border:none; text-align:left; padding:0; background:transparent;">Smart ŞUSKİ - Akıllı
                    Raporlama Sistemi</td>
                <td style="border:none; text-align:right; padding:0; background:transparent;">Bu belge elektronik
                    ortamda üretilmiştir.</td>
            </tr>
        </table>
    </div>
</body>

</html>