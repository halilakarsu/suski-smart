<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body>
<table>
    <tr>
        <td colspan="7" style="text-align: center; font-weight: bold; font-size: 14px;">
            {{ $type === 'periodical' ? 'DÖNEMSEL TÜKETİM RAPORU' : 'YILLIK TÜKETİM RAPORU' }}
        </td>
    </tr>
    <tr>
        <th>SIRA NO</th>
        <th>{{ $type === 'periodical' ? 'DÖNEM' : 'YIL' }}</th>
        <th>BÖLGE</th>
        @if($type === 'yearly')
        <th>FATURA SAYISI</th>
        <th>TÜKETİM (kWh)</th>
        @else
        <th>BRÜT TÜKETİM (kWh)</th>
        <th>BRÜT TUTAR (₺)</th>
        <th>NET TÜKETİM (kWh)</th>
        @endif
        <th>NET TUTAR (₺)</th>
    </tr>
    @foreach($data as $index => $row)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $type === 'periodical' ? ($row->donem ?? '—') : ($row->yil ?? '—') }}</td>
        <td>{{ $row->ilce ?? $row->bolge ?? '—' }}</td>
        @if($type === 'yearly')
        <td>{{ (int)$row->fatura_sayisi }}</td>
        <td>{{ (float)$row->toplam_tuketim }}</td>
        @else
        <td>{{ (float)($row->brut_tuketim ?? 0) }}</td>
        <td>₺ {{ number_format((float)($row->brut_tutar ?? 0), 2, ',', '.') }}</td>
        <td>{{ (float)$row->toplam_tuketim }}</td>
        @endif
        <td>₺ {{ number_format((float)$row->toplam_tutar, 2, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="2">GENEL TOPLAM</td>
        <td></td>
        @if($type === 'yearly')
        <td></td>
        <td>{{ (float)$data->sum('toplam_tuketim') }}</td>
        @else
        <td>{{ (float)$data->sum('brut_tuketim') }}</td>
        <td>₺ {{ number_format((float)$data->sum('brut_tutar'), 2, ',', '.') }}</td>
        <td>{{ (float)$data->sum('toplam_tuketim') }}</td>
        @endif
        <td>₺ {{ number_format((float)$data->sum('toplam_tutar'), 2, ',', '.') }}</td>
    </tr>
</table>
</body>
</html>
