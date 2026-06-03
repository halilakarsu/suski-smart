<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body>
<table>
    <tr>
        <td colspan="{{ $type === 'yearly' ? 6 : 5 }}" style="text-align: center; font-weight: bold; font-size: 14px;">ŞUSKİ GENEL MÜDÜRLÜĞÜ - RAPOR ÇIKTISI</td>
    </tr>
    <tr>
        <th>SIRA NO</th>
        <th>TESİSAT NO</th>
        <th>BÖLGE</th>
        @if($type === 'yearly')
        <th>YIL</th>
        @endif
        <th>TÜKETİM (kWh)</th>
        <th>TOPLAM TUTAR (₺)</th>
    </tr>
    @foreach($data as $index => $row)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $row->tesisat_no }}</td>
        <td>{{ $row->bolge ?? '—' }}</td>
        @if($type === 'yearly')
        <td>{{ $row->yil }}</td>
        @endif
        <td>{{ (float)$row->toplam_tuketim }}</td>
        <td>₺ {{ number_format((float)$row->toplam_tutar, 2, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="3">GENEL TOPLAM</td>
        @if($type === 'yearly')
        <td></td>
        @endif
        <td>{{ (float)$data->sum('toplam_tuketim') }}</td>
        <td>₺{{ (float)$data->sum('toplam_tutar') }}</td>
    </tr>
</table>
</body>
</html>
