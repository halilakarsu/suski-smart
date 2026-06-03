<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body>
<table>
    <tr>
        <td colspan="8" style="text-align: center; font-weight: bold; font-size: 14px;">ŞUSKİ GENEL MÜDÜRLÜĞÜ - KÖY/MERKEZ ÖZET PİVOT RAPORU</td>
    </tr>
    <tr>
        <td colspan="4">Bölge: {{ empty($filters['bolge']) ? 'Tümü' : (is_array($filters['bolge']) ? implode(', ', $filters['bolge']) : $filters['bolge']) }} | Dönem: {{ empty($filters['start_period']) ? 'Tümü' : $filters['start_period'] }} - {{ empty($filters['end_period']) ? 'Tümü' : $filters['end_period'] }}</td>
        <td colspan="4" style="text-align: right;">Tarife: {{ empty($filters['tarife']) ? 'Tümü' : (is_array($filters['tarife']) ? implode(', ', $filters['tarife']) : $filters['tarife']) }}</td>
    </tr>
    <tr>
        <th>DÖNEM</th>
        <th>İLÇE / BÖLGE</th>
        <th>MERKEZ (kWh)</th>
        <th>MERKEZ TUTAR (₺)</th>
        <th>KÖY (kWh)</th>
        <th>KÖY TUTAR (₺)</th>
        <th>GENEL (kWh)</th>
        <th>GENEL TUTAR (₺)</th>
    </tr>
    @foreach($results as $row)
    @php
        $genelTuketim = $row->merkez_tuketim + $row->koy_tuketim;
        $genelTutar = $row->merkez_tutar + $row->koy_tutar;
    @endphp
    <tr>
        <td>{{ $row->donem }}</td>
        <td>{{ $row->bolge ?? 'Tümü' }}</td>
        <td>{{ (float)$row->merkez_tuketim }}</td>
        <td>{{ (float)$row->merkez_tutar }}</td>
        <td>{{ (float)$row->koy_tuketim }}</td>
        <td>{{ (float)$row->koy_tutar }}</td>
        <td>{{ (float)$genelTuketim }}</td>
        <td>{{ (float)$genelTutar }}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="2">GENEL TOPLAM</td>
        <td>{{ (float)$totals['merkez_tuketim'] }}</td>
        <td>{{ (float)$totals['merkez_tutar'] }}</td>
        <td>{{ (float)$totals['koy_tuketim'] }}</td>
        <td>{{ (float)$totals['koy_tutar'] }}</td>
        <td>{{ (float)($totals['merkez_tuketim'] + $totals['koy_tuketim']) }}</td>
        <td>{{ (float)($totals['merkez_tutar'] + $totals['koy_tutar']) }}</td>
    </tr>
</table>
</body>
</html>
