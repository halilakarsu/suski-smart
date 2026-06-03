<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body>
@php
    $hasBolge = !empty($filters['bolge']);
@endphp
<table>
    <tr>
        <td colspan="{{ $hasBolge ? 10 : 9 }}" style="text-align: center; font-weight: bold; font-size: 14px;">ŞUSKİ GENEL MÜDÜRLÜĞÜ - RAPOR ÇIKTISI</td>
    </tr>
    <tr>
        <th>SIRA NO</th>
        <th>DÖNEM</th>
        <th>TESİSAT NO</th>
        @if($hasBolge)<th>BÖLGE</th>@endif
        <th>İLK ENDEKS</th>
        <th>SON ENDEKS</th>
        <th>FARK</th>
        <th>TÜKETİM (kWh)</th>
        <th>TOPLAM TUTAR (₺)</th>
        <th>DURUM</th>
    </tr>
    @foreach($results as $index => $row)
    @php
        $t1I = (float)str_replace(',', '.', $row->t1_ilk_endeks); $t1S = (float)str_replace(',', '.', $row->t1_son_endeks);
        $t2I = (float)str_replace(',', '.', $row->t2_ilk_endeks); $t2S = (float)str_replace(',', '.', $row->t2_son_endeks);
        $t3I = (float)str_replace(',', '.', $row->t3_ilk_endeks); $t3S = (float)str_replace(',', '.', $row->t3_son_endeks);
        $hasT = ($t1I + $t2I + $t3I) > 0;
        $t0I = $hasT ? ($t1I + $t2I + $t3I) : (float)str_replace(',', '.', $row->t0_ilk_endeks);
        $t0S = $hasT ? ($t1S + $t2S + $t3S) : (float)str_replace(',', '.', $row->t0_son_endeks);
        $fark = $t0S - $t0I;
        $t0G = (float)($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim);
    @endphp
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $row->donem }}</td>
        <td>{{ $row->abone_tesis_no ?? $row->tesisat_no }}</td>
        @if($hasBolge)<td>{{ $row->bolge ?? '—' }}</td>@endif
        <td>{{ (float)$t0I }}</td>
        <td>{{ (float)$t0S }}</td>
        <td>{{ (float)$fark }}</td>
        <td>{{ (float)$t0G }}</td>
        <td>₺ {{ number_format((float)$row->tutar_toplam, 2, ',', '.') }}</td>
        <td>{{ $t0S < $t0I ? 'HATALI' : 'TAMAM' }}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="{{ $hasBolge ? 7 : 6 }}">GENEL TOPLAM</td>
        <td>{{ (float)$totalKWH }}</td>
        <td>₺{{ number_format((float)$totalAmount, 2, ',', '.') }}</td>
        <td></td>
    </tr>
</table>
</body>
</html>
