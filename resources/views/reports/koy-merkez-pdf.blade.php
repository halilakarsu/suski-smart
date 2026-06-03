<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body{font-family:courier,monospace;font-size:7.5pt;margin:10px;}
h2{font-size:10pt;text-align:center;margin:0 0 2px;}
p{font-size:7pt;text-align:center;margin:0 0 6px;color:#444;}
table{width:100%;border-collapse:collapse;table-layout:fixed;}
th{font-size:7pt;padding:4px;background:#1E293B;color:#fff;text-align:center;border:1px solid #000;}
td{font-size:7.5pt;padding:3px 4px;border:1px solid #999;}
.r{text-align:right;}
.c{text-align:center;}
.ft{background:#111827;color:#fff;font-weight:bold;}
.mrk{color:#1d4ed8;font-weight:bold;}
.koy{color:#15803d;font-weight:bold;}
.gnl{color:#b45309;font-weight:bold;}
</style>
</head>
<body>
<h2>ŞUSKİ GENEL MÜDÜRLÜĞÜ — KÖY/MERKEZ ÖZET PİVOT RAPORU</h2>
<p>
@if(!empty($filters['start_period']))Dönem:{{$filters['start_period']}}@if(!empty($filters['end_period']))—{{$filters['end_period']}}@endif | @endif
@if(!empty($filters['bolge']))Bölge:{{$filters['bolge']}} | @endif
Toplam:{{$results->count()}} kayıt
</p>
<table>
<thead><tr>
<th style="width:5%">#</th>
<th style="width:10%">DÖNEM</th>
<th style="width:16%">İLÇE / BÖLGE</th>
<th style="width:12%;background:#2563EB;">MRK (kWh)</th>
<th style="width:11%;background:#2563EB;">MRK (₺)</th>
<th style="width:12%;background:#16A34A;">KÖY (kWh)</th>
<th style="width:11%;background:#16A34A;">KÖY (₺)</th>
<th style="width:12%;background:#B45309;">GENEL (kWh)</th>
<th style="width:11%;background:#B45309;">GENEL (₺)</th>
</tr></thead>
<tbody>
@foreach($results as $i=>$row)
@php
    $genelTuketim = $row->merkez_tuketim + $row->koy_tuketim;
    $genelTutar = $row->merkez_tutar + $row->koy_tutar;
@endphp
<tr>
<td class="c">{{$i+1}}</td>
<td class="c">{{$row->donem}}</td>
<td class="c">{{$row->bolge ?? 'Tümü'}}</td>
<td class="r">{{number_format($row->merkez_tuketim,2,',','.')}}</td>
<td class="r mrk">{{number_format($row->merkez_tutar,2,',','.')}}</td>
<td class="r">{{number_format($row->koy_tuketim,2,',','.')}}</td>
<td class="r koy">{{number_format($row->koy_tutar,2,',','.')}}</td>
<td class="r">{{number_format($genelTuketim,2,',','.')}}</td>
<td class="r gnl">{{number_format($genelTutar,2,',','.')}}</td>
</tr>
@endforeach
</tbody>
<tfoot><tr>
<td class="c ft" colspan="3">GENEL TOPLAM</td>
<td class="r ft">{{number_format($totals['merkez_tuketim'],2,',','.')}}</td>
<td class="r ft">{{number_format($totals['merkez_tutar'],2,',','.')}}</td>
<td class="r ft">{{number_format($totals['koy_tuketim'],2,',','.')}}</td>
<td class="r ft">{{number_format($totals['koy_tutar'],2,',','.')}}</td>
<td class="r ft">{{number_format($totals['merkez_tuketim'] + $totals['koy_tuketim'],2,',','.')}}</td>
<td class="r ft">{{number_format($totals['merkez_tutar'] + $totals['koy_tutar'],2,',','.')}}</td>
</tr></tfoot>
</table>
</body>
</html>
