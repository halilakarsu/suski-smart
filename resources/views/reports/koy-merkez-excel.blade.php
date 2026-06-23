<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Köy/Merkez Özet Raporu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8pt; margin: 10px; }
        h2 { font-size: 11pt; margin-bottom: 4px; }
        p { font-size: 7pt; color: #6b7280; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1E293B; color: #fff; padding: 4px 6px; font-size: 7pt; font-weight: 700; border: 1px solid #000; text-align: center; }
        th.merkez { background: #2563EB; }
        th.koy    { background: #16A34A; }
        th.genel  { background: #B45309; }
        td { padding: 3px 6px; font-size: 7pt; border: 1px solid #d1d5db; }
        .r { text-align: right; }
        .c { text-align: center; }
        .b { font-weight: 700; }
        .merkez-val { color: #1d4ed8; font-weight: 700; }
        .koy-val    { color: #15803d; font-weight: 700; }
        .genel-val  { color: #b45309; font-weight: 700; }
        .total-row  { background: #1E293B; color: #fff; font-weight: 700; }
    </style>
</head>
<body>
    <h2>ŞUSKİ GENEL MÜDÜRLÜĞÜ — KÖY/MERKEZ ÖZET PİVOT RAPORU</h2>
    <p>
        Bölge: {{ empty($filters['bolge']) ? 'Tümü' : (is_array($filters['bolge']) ? implode(', ', $filters['bolge']) : $filters['bolge']) }} |
        Dönem: {{ empty($filters['start_period']) ? 'Tümü' : $filters['start_period'] }}{{ !empty($filters['end_period']) ? ' - '.$filters['end_period'] : '' }} |
        Tarife: {{ empty($filters['tarife']) ? 'Tümü' : (is_array($filters['tarife']) ? implode(', ', $filters['tarife']) : $filters['tarife']) }}
    </p>
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="c">#</th>
                <th rowspan="2" class="c">DÖNEM</th>
                <th rowspan="2" class="c">İLÇE / BÖLGE</th>
                <th colspan="2" class="merkez">MERKEZ</th>
                <th colspan="2" class="koy">KÖY</th>
                <th colspan="2" class="genel">GENEL TOPLAM</th>
            </tr>
            <tr>
                <th class="merkez r">Tüketim (kWh)</th>
                <th class="merkez r">Tutar (₺)</th>
                <th class="koy r">Tüketim (kWh)</th>
                <th class="koy r">Tutar (₺)</th>
                <th class="genel r">Tüketim (kWh)</th>
                <th class="genel r">Tutar (₺)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $i => $row)
            @php
                $genelTuketim = $row->merkez_tuketim + $row->koy_tuketim;
                $genelTutar   = $row->merkez_tutar + $row->koy_tutar;
            @endphp
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td class="c">{{ $row->donem }}</td>
                <td>{{ $row->bolge ?? 'Tümü' }}</td>
                <td class="r">{{ number_format($row->merkez_tuketim, 2, ',', '.') }}</td>
                <td class="r merkez-val">{{ number_format($row->merkez_tutar, 2, ',', '.') }}</td>
                <td class="r">{{ number_format($row->koy_tuketim, 2, ',', '.') }}</td>
                <td class="r koy-val">{{ number_format($row->koy_tutar, 2, ',', '.') }}</td>
                <td class="r b">{{ number_format($genelTuketim, 2, ',', '.') }}</td>
                <td class="r genel-val">{{ number_format($genelTutar, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="c">GENEL TOPLAM</td>
                <td class="r">{{ number_format($totals['merkez_tuketim'], 2, ',', '.') }}</td>
                <td class="r">{{ number_format($totals['merkez_tutar'], 2, ',', '.') }}</td>
                <td class="r">{{ number_format($totals['koy_tuketim'], 2, ',', '.') }}</td>
                <td class="r">{{ number_format($totals['koy_tutar'], 2, ',', '.') }}</td>
                <td class="r">{{ number_format($totals['merkez_tuketim'] + $totals['koy_tuketim'], 2, ',', '.') }}</td>
                <td class="r">{{ number_format($totals['merkez_tutar'] + $totals['koy_tutar'], 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
