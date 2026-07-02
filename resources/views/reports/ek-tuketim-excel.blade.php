<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Rapor</title></head>
<body>
    <h2>Ek Tüketim Raporu</h2>
    <p>
        Dönem: {{ empty($filters['start_period']) ? 'Tümü' : $filters['start_period'] }}{{ !empty($filters['end_period']) ? ' - '.$filters['end_period'] : '' }}
    </p>
    <table>
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">DÖNEM</th>
                <th rowspan="2">TESİSAT NO</th>
                <th rowspan="2">İLK OKUMA</th>
                <th rowspan="2">SON OKUMA</th>
                <th colspan="4">İLAVE TÜKETİM</th>
            </tr>
            <tr>
                <th>T1 İlave (kWh)</th>
                <th>T2 İlave (kWh)</th>
                <th>T3 İlave (kWh)</th>
                <th>Top. İlave (kWh)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $i => $row)
            @php
                $payload = $row->payload;
                $t1Ilave = $t2Ilave = $t3Ilave = 0;
                if ($payload) {
                    foreach (['T1_ILAVE_KWH' => &$t1Ilave, 'T2_ILAVE_KWH' => &$t2Ilave, 'T3_ILAVE_KWH' => &$t3Ilave] as $key => &$ref) {
                        $val = $payload[$key] ?? 0;
                        $ref = ($val !== '' && $val !== ' ' && $val !== null) ? (float) str_replace(',', '.', $val) : 0;
                    }
                    unset($ref);
                }
                $ilaveToplam = $t1Ilave + $t2Ilave + $t3Ilave;
            @endphp
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td style="text-align:center;">{{ $row->donem }}</td>
                <td style="text-align:center;">{{ $row->tesisat_no }}</td>
                <td style="text-align:center;">{{ $row->ilk_okuma ? $row->ilk_okuma->format('d.m.Y') : '—' }}</td>
                <td style="text-align:center;">{{ $row->son_okuma ? $row->son_okuma->format('d.m.Y') : '—' }}</td>
                <td style="text-align:right;">{{ number_format($t1Ilave, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($t2Ilave, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($t3Ilave, 2, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($ilaveToplam, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:center;font-weight:700;">GENEL TOPLAM</td>
                <td style="text-align:right;">—</td>
                <td style="text-align:right;">—</td>
                <td style="text-align:right;">—</td>
                <td style="text-align:right;">{{ number_format($totals['total_ilave_toplam'], 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
