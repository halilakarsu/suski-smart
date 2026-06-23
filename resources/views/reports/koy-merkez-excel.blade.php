<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Rapor</title></head>
<body>
    <h2>Köy ve Merkeze Yönelik Özet Bilgiler</h2>
    <p>
        Bölge: {{ empty($filters['bolge']) ? 'Tümü' : (is_array($filters['bolge']) ? implode(', ', $filters['bolge']) : $filters['bolge']) }} |
        Dönem: {{ empty($filters['start_period']) ? 'Tümü' : $filters['start_period'] }}{{ !empty($filters['end_period']) ? ' - '.$filters['end_period'] : '' }} |
        Tarife: {{ empty($filters['tarife']) ? 'Tümü' : (is_array($filters['tarife']) ? implode(', ', $filters['tarife']) : $filters['tarife']) }}
    </p>
    <table>
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">DÖNEM</th>
                <th rowspan="2">İLÇE / BÖLGE</th>
                <th colspan="2">MERKEZ</th>
                <th colspan="2">KÖY</th>
                <th colspan="2">GENEL TOPLAM</th>
            </tr>
            <tr>
                <th>Tüketim (kWh)</th>
                <th>Tutar (₺)</th>
                <th>Tüketim (kWh)</th>
                <th>Tutar (₺)</th>
                <th>Tüketim (kWh)</th>
                <th>Tutar (₺)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $i => $row)
            @php
                $genelTuketim = $row->merkez_tuketim + $row->koy_tuketim;
                $genelTutar   = $row->merkez_tutar + $row->koy_tutar;
            @endphp
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td style="text-align:center;">{{ $row->donem }}</td>
                <td>{{ $row->bolge ?? 'Tümü' }}</td>
                <td style="text-align:right;">{{ number_format($row->merkez_tuketim, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($row->merkez_tutar, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($row->koy_tuketim, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($row->koy_tutar, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($genelTuketim, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($genelTutar, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:center;font-weight:700;">GENEL TOPLAM</td>
                <td style="text-align:right;">{{ number_format($totals['merkez_tuketim'], 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($totals['merkez_tutar'], 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($totals['koy_tuketim'], 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($totals['koy_tutar'], 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($totals['merkez_tuketim'] + $totals['koy_tuketim'], 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($totals['merkez_tutar'] + $totals['koy_tutar'], 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
