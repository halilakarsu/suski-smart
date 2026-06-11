<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body>
    <table>
        <!-- 1. ve 2. Satır: Başlıklar -->
        <tr>
            <td colspan="5"
                style="font-weight:bold; font-size:16pt; color:#1a73e8; text-align:center; vertical-align:bottom;">
                ŞUSKİ GENEL MÜDÜRLÜĞÜ
            </td>
        </tr>
        <tr>
            <td colspan="5"
                style="font-weight:bold; font-size:14pt; color:#1a73e8; text-align:center; vertical-align:top;">
                ELEKTRİK FATURALARI YÖNETİM SİSTEMİ
            </td>
        </tr>
        <!-- 3. Satır: İstatistikler -->
        <tr>
            <td colspan="2" style="font-weight:bold; color:#495057; border-bottom:2px solid #1a73e8;">
                Genel Toplam Tutar: {{ number_format((float) $totalAmount, 2, ',', '.') }} ₺
            </td>
            <td colspan="2" style="font-weight:bold; color:#495057; border-bottom:2px solid #1a73e8; text-align:right;">
                Toplam Tüketim: {{ number_format((float) $totalKWH, 2, ',', '.') }} kWh
            </td>
            <td colspan="1" style="font-weight:bold; color:#495057; border-bottom:2px solid #1a73e8; text-align:right;">
                Dönem : {{ $donem}}
            </td>
        </tr>
        <tr>
            <th style="font-weight:bold; background-color:#1a73e8; color:#ffffff; border:1px solid #1a73e8;">SIRA</th>
            <th style="font-weight:bold; background-color:#1a73e8; color:#ffffff; border:1px solid #1a73e8;">ADRES</th>
            <th style="font-weight:bold; background-color:#1a73e8; color:#ffffff; border:1px solid #1a73e8;">TESİSAT NO
            </th>
            <th
                style="font-weight:bold; background-color:#1a73e8; color:#ffffff; border:1px solid #1a73e8; text-align:right;">
                TÜKETİM (KWH)</th>
            <th
                style="font-weight:bold; background-color:#1a73e8; color:#ffffff; border:1px solid #1a73e8; text-align:right;">
                TOPLAM TUTAR</th>
        </tr>
        @foreach($faturalar as $index => $fatura)
            <tr>
                <td style="text-align:center;">{{ $loop->iteration }}</td>
                <td style="text-align:left;">{{ $fatura->adres }}</td>
                <td style="text-align:center;">{{ $fatura->abone_tesis_no ?? $fatura->tesisat_no }}</td>
                <td style="text-align:right;">{{ (float) $fatura->fatura_edilecek_toplam_tuketim_kwh }}</td>
                <td style="text-align:right;">{{ (float) $fatura->tutar_toplam }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3"
                style="font-weight:bold; text-align:right; background-color:#eef4ff; color:#1a73e8; border-top:2px solid #1a73e8;">
                GENEL TOPLAM :</td>
            <td
                style="font-weight:bold; text-align:right; background-color:#eef4ff; color:#1a73e8; border-top:2px solid #1a73e8;">
                {{ (float) $totalKWH }}
            </td>
            <td
                style="font-weight:bold; text-align:right; background-color:#eef4ff; color:#1a73e8; border-top:2px solid #1a73e8;">
                {{ (float) $totalAmount }}
            </td>
        </tr>
    </table>
</body>

</html>