<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body>
<table>
    <tr>
        <td colspan="{{ $type === 'yearly' ? 5 : 5 }}" style="text-align: center; font-weight: bold; font-size: 14px;">
            {{ $type === 'periodical' ? 'DÖNEMSEL TÜKETİM RAPORU' : 'YILLIK TÜKETİM RAPORU' }}
        </td>
    </tr>
    <tr>
        <th>SIRA NO</th>
        <th>{{ $type === 'periodical' ? 'DÖNEM' : 'YIL' }}</th>
        <th>BÖLGE</th>
        @if($type === 'yearly')
        <th>FATURA SAYISI</th>
        @endif
        <th>TÜKETİM (kWh)</th>
        <th>TOPLAM TUTAR (₺)</th>
    </tr>
    @foreach($data as $index => $row)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $type === 'periodical' ? ($row->donem ?? '—') : ($row->yil ?? '—') }}</td>
        <td>{{ $row->ilce ?? $row->bolge ?? '—' }}</td>
        @if($type === 'yearly')
        <td>{{ (int)$row->fatura_sayisi }}</td>
        @endif
        <td>{{ (float)$row->toplam_tuketim }}</td>
        <td>₺ {{ number_format((float)$row->toplam_tutar, 2, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="2">GENEL TOPLAM</td>
        <td></td>
        @if($type === 'yearly')
        <td></td>
        @endif
        <td>{{ (float)$data->sum('toplam_tuketim') }}</td>
        <td>₺{{ (float)$data->sum('toplam_tutar') }}</td>
    </tr>
</table>
</body>
</html>
