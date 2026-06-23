<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body>
<table>
    <tr>
        <td colspan="9" style="text-align: center; font-weight: bold; font-size: 14px;">SMART ŞUSKİ ELEKTRİK FATURALARI BİLGİ YÖNETİM SİSTEMİ - DETAYLI FATURA RAPORU</td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; font-weight: bold; font-size: 11px;">
            @php
                $fBolge = !empty($filters['bolge']) ? (is_array($filters['bolge']) ? implode(', ', $filters['bolge']) : $filters['bolge']) : 'Tümü';
                $fStart = $filters['start_period'] ?? null;
                $fEnd = $filters['end_period'] ?? null;
                $fDonem = $fStart && $fEnd ? "{$fStart} - {$fEnd}" : ($fStart ?? ($fEnd ?? 'Tümü'));
                $fYerlesim = match($filters['yerlesim_tipi'] ?? null) { 'koy' => 'Köy', 'merkez' => 'Merkez', default => 'Tümü' };
                $fBaglanti = !empty($filters['baglanti_grubu']) ? $filters['baglanti_grubu'] : 'Tümü';
                $fTarife = !empty($filters['tarife']) ? (is_array($filters['tarife']) ? implode(', ', $filters['tarife']) : $filters['tarife']) : 'Tümü';
            @endphp
            BÖLGE: {{ $fBolge }}   
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; font-weight: bold; font-size: 11px;">
            YERLEŞİM TÜRÜ: {{ $fYerlesim }}   |   BAĞLANTI GRUBU: {{ $fBaglanti }} DÖNEM: {{ $fDonem }}   |   TARİFE: {{ $fTarife }}
        </td>
    </tr>
    <tr>
        <th>DÖNEM</th>
        <th>TESİSAT NO</th>
        <th>İLK OKUMA</th>
        <th>SON OKUMA</th>
        <th>İLK ENDEKS</th>
        <th>SON ENDEKS</th>
        <th>ÇARPAN</th>
        <th>TÜKETİM (kWh)</th>
        <th>TUTAR (₺)</th>
    </tr>
    @foreach($results as $row)
    <tr>
        <td>{{ $row->donem }}</td>
        <td>{{ $row->abone_tesis_no ?? $row->tesisat_no }}</td>
        <td>{{ $row->ilk_okuma ? \Carbon\Carbon::parse($row->ilk_okuma)->format('d.m.Y') : '—' }}</td>
        <td>{{ $row->son_okuma ? \Carbon\Carbon::parse($row->son_okuma)->format('d.m.Y') : '—' }}</td>
        <td>{{ (float) $row->t0_ilk_endeks }}</td>
        <td>{{ (float) $row->t0_son_endeks }}</td>
        <td>{{ (float) $row->carpan }}</td>
        <td>{{ (float) ($row->fatura_edilecek_toplam_tuketim_kwh ?: ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim + $row->ek_tuketim)) }}</td>
        <td>{{ number_format($row->tutar_toplam ?? $row->fatura_tutari ?? 0, 2, ',', '.') }}</td>
    </tr>
    @endforeach
    <tr>
        <td colspan="7">GENEL TOPLAM</td>
        <td>{{ number_format($totalKWH, 2, ',', '.') }}</td>
        <td>{{ number_format($totalAmount, 2, ',', '.') }}</td>
    </tr>
</table>
</body>
</html>
