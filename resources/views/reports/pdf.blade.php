<!DOCTYPE html>
<html lang="tr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rapor Çıktısı</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm 4mm 5mm 4mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif !important;
            color: #212529;
            line-height: 0.9;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 5pt;
        }

        th {
            background-color: #1a73e8;
            color: #FFFFFF;
            font-weight: bold;
            padding: 2px 2px;
            border: 1px solid #1a73e8;
            border-bottom: 1px solid #155bb5;
            text-transform: uppercase;
        }

        td {
            padding: 2px 2px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        .total-row td {
            background-color: #eef4ff !important;
            color: #1a73e8;
            font-weight: bold;
            font-size: 6pt;
            border-top: 1px solid #1a73e8;
            border-bottom: 1px solid #1a73e8;
        }

        .total-label {
            text-align: right;
            color: #1a73e8 !important;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .footer {
            margin-top: 5px;
            font-size: 5pt;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 3px;
        }

        .badge {
            padding: 1px 3px;
            border-radius: 2px;
            font-size: 5pt;
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>

@php
    $isDetailList = in_array($type, ['detailed', 'anomali', 'endeks']);
    $reportTitle  = match($type) {
        'anomali'    => 'ANOMALİ TESPİT RAPORU',
        'periodical' => 'DÖNEMSEL TÜKETİM RAPORU',
        'yearly'     => 'YILLIK TÜKETİM RAPORU',
        'detailed'   => 'DETAYLI FATURA RAPORU',
        'koy_merkez' => 'KÖY VE MERKEZLERE YÖNELİK ÖZET BİLGİLER',
        'ek_tuketim' => 'EK TÜKETİM RAPORU',
        'endeks'     => 'TÜKETİM & ENDEKS ANALİZ RAPORU',
        default      => 'SİSTEM RAPORU'
    };
    $filters = $filters ?? [];

    $bolge = !empty($filters['bolge']) ? (is_array($filters['bolge']) ? implode(', ', $filters['bolge']) : $filters['bolge']) : 'Tümü';
    $startD = $filters['start_period'] ?? ($filters['start_year'] ?? null);
    $endD   = $filters['end_period']   ?? ($filters['end_year']   ?? null);
    $donem  = $startD && $endD ? "{$startD} - {$endD}" : ($startD ?? ($endD ?? 'Tümü'));
    
    $yerlesimRaw = $filters['yerlesim_tipi'] ?? null;
    $yerlesim    = match($yerlesimRaw) { 'koy' => 'Köy', 'merkez' => 'Merkez', default => 'Tümü' };
    $baglanti = !empty($filters['baglanti_grubu']) ? $filters['baglanti_grubu'] : 'Tümü';
    $tarife = !empty($filters['tarife']) ? (is_array($filters['tarife']) ? implode(', ', $filters['tarife']) : $filters['tarife']) : 'Tümü';

    $hasBolge = !empty($filters['bolge']);
    $toplamTutar = $results->sum('toplam_tutar') ?: $results->sum('tutar_toplam');
    
    // Toplamları hesapla
    $tF = 0; $tK = 0; $tT = 0;
    $tMK = 0; $tMT = 0; $tKK = 0; $tKT = 0;
    $tT1I = 0; $tT2I = 0; $tT3I = 0;
    $tBrut = 0; $tBrutTutar = 0;
    foreach($results as $row) {
        if ($type === 'koy_merkez') {
            $tMK += $row->merkez_tuketim;
            $tMT += $row->merkez_tutar;
            $tKK += $row->koy_tuketim;
            $tKT += $row->koy_tutar;
            $tK  += ($row->merkez_tuketim + $row->koy_tuketim);
            $tT  += ($row->merkez_tutar   + $row->koy_tutar);
        } elseif ($type === 'ek_tuketim') {
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
            $valK = (float) ($row->fatura_edilecek_toplam_tuketim_kwh ?: ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim + $row->ek_tuketim));
            $tK += $valK;
            $tT += (float)($row->tutar_toplam ?? 0);
            $tF += (int)($row->fatura_sayisi ?? 1);
            $tT1I += $t1Ilave;
            $tT2I += $t2Ilave;
            $tT3I += $t3Ilave;
        } else {
            $valK = $isDetailList ? ($row->fatura_edilecek_toplam_tuketim_kwh ?: ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim + $row->ek_tuketim)) : ($row->toplam_tuketim ?? 0);
            $tK += (float)$valK;
            $tT += (float)($row->tutar_toplam ?? ($row->toplam_tutar ?? 0));
            $tF += (int)($row->fatura_sayisi ?? 1);
            if (in_array($type, ['periodical', 'yearly'])) {
                $tBrut += (float)($row->brut_tuketim ?? 0);
                $tBrutTutar += (float)($row->brut_tutar ?? 0);
            }
        }
    }
@endphp

    <!-- Üst Header Alanı -->
    <table style="width: 100%; border: none; margin-bottom: 6px;">
        <tr>
            <!-- Logo Alanı -->
            <td style="width: 25%; border: none; padding: 0; vertical-align: middle; text-align: left;">
                <img src="{{ public_path('images/logo.png') }}" style="width:130px; height: auto; max-width: 100%; object-fit: contain;" alt="Logo">
            </td>

            <!-- Başlık Alanı (Ortalı) -->
            <td style="width: 50%; border: none; padding: 0; vertical-align: middle; text-align: center;">
                <div style="font-size: 10pt; font-weight: bold; color: #1a73e8; margin-bottom: 4px; line-height: 1.2;">
                    ŞUSKİ GENEL MÜDÜRLÜĞÜ<br>ELEKTRİK BİLGİ YÖNETİM SİSTEMİ
                </div>
                <div style="font-size: 12pt; font-weight: bold; color: #212529; text-transform: uppercase;">
                    {{ $reportTitle }}
                </div>
            </td>

            <!-- Sağ Taraf (Tarih) -->
            <td style="width: 25%; border: none; padding: 0; vertical-align: bottom; text-align: right;">
                <div style="font-size: 6pt; color: #6c757d;">Tarih: {{ now()->format('d.m.Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- Kalın Mavi Çizgi -->
    <div style="border-bottom: 1px solid #1a73e8; margin-bottom: 3px;"></div>

    <!-- Rapor Bilgileri Kutusu -->
    <table style="width: 100%; border: none; margin: 0; font-size: 6pt; margin-bottom: 3px;">
        <tr>
            <td style="border: none; padding: 1px 0; width: 33%;">
                <span style="font-weight: bold; color: #495057;">Bölge:</span> <span style="color: #212529;">{{ $bolge }}</span>
            </td>
            <td style="border: none; padding: 1px 0; width: 33%;">
                <span style="font-weight: bold; color: #495057;">Dönem:</span> <span style="color: #212529;">{{ $donem }}</span>
            </td>
            <td style="border: none; padding: 1px 0; width: 34%;">
                <span style="font-weight: bold; color: #495057;">Yerleşim Yeri:</span> <span style="color: #212529;">{{ $yerlesim }}</span>
            </td>
        </tr>
        <tr>
            <td style="border: none; padding: 1px 0;">
                <span style="font-weight: bold; color: #495057;">Abone Bağlantı:</span> <span style="color: #212529;">{{ $baglanti }}</span>
            </td>
            <td style="border: none; padding: 1px 0;">
                <span style="font-weight: bold; color: #495057;">Tarife:</span> <span style="color: #212529;">{{ $tarife }}</span>
            </td>
            <td style="border: none; padding: 1px 0;">
                <span style="font-weight: bold; color: #495057;">Kayıt Sayısı:</span> <span style="color: #212529;">{{ number_format($results->count(), 0, '', '.') }}</span>
            </td>
        </tr>
    </table>

    <!-- Veri Tablosu -->
    <table style="margin-top: 3px;">
        <thead>
            @if($type === 'endeks')
                <tr>
                    <th width="4%" class="text-center">SIRA</th>
                    <th width="{{ $hasBolge ? '10%' : '12%' }}" class="text-center">DÖNEM</th>
                    <th width="{{ $hasBolge ? '14%' : '16%' }}" class="text-center">TESİSAT NO</th>
                    @if($hasBolge)<th width="12%" class="text-left">BÖLGE</th>@endif
                    <th width="{{ $hasBolge ? '10%' : '12%' }}" class="text-right">İLK ENDEKS</th>
                    <th width="{{ $hasBolge ? '10%' : '12%' }}" class="text-right">SON ENDEKS</th>
                    <th width="{{ $hasBolge ? '10%' : '12%' }}" class="text-right">FARK</th>
                    <th width="{{ $hasBolge ? '10%' : '12%' }}" class="text-right">TÜKETİM (KWH)</th>
                    <th width="12%" class="text-right">TUTAR (₺)</th>
                    <th width="8%" class="text-center">DURUM</th>
                </tr>
            @elseif($isDetailList)
                <tr>
                    <th width="4%" class="text-center">SIRA</th>
                    <th width="14%" class="text-center">TESİSAT NO</th>
                    <th width="24%" class="text-left">HESAP ADI</th>
                    <th width="12%" class="text-center">FATURA NO</th>
                    @if($type === 'anomali')
                        <th width="20%" class="text-left">ANOMALİ DETAYI</th>
                    @else
                        <th width="10%" class="text-center">DÖNEM</th>
                    @endif
                    <th width="18%" class="text-right">TÜKETİM (KWH)</th>
                    <th width="18%" class="text-right">TUTAR (₺)</th>
                </tr>
            @elseif($type === 'koy_merkez')
                <tr>
                    <th width="4%" class="text-center">SIRA</th>
                    <th width="10%" class="text-center">DÖNEM</th>
                    <th width="16%" class="text-left">BÖLGE</th>
                    <th width="12%" class="text-right">MERKEZ (KWH)</th>
                    <th width="12%" class="text-right">MERKEZ TUTAR</th>
                    <th width="12%" class="text-right">KÖY (KWH)</th>
                    <th width="12%" class="text-right">KÖY TUTAR</th>
                    <th width="11%" class="text-right">TOP. (KWH)</th>
                    <th width="11%" class="text-right">TOP. TUTAR</th>
                </tr>
            @elseif($type === 'ek_tuketim')
                <tr>
                    <th width="4%" class="text-center">SIRA</th>
                    <th width="14%" class="text-center">DÖNEM</th>
                    <th width="16%" class="text-center">TESİSAT NO</th>
                    <th width="12%" class="text-center">İLK OKUMA</th>
                    <th width="12%" class="text-center">SON OKUMA</th>
                    <th width="10%" class="text-right">T1 İLAVE</th>
                    <th width="10%" class="text-right">T2 İLAVE</th>
                    <th width="10%" class="text-right">T3 İLAVE</th>
                    <th width="12%" class="text-right">TOP. İLAVE</th>
                </tr>
            @elseif($type === 'yearly')
                <tr>
                    <th width="3%" class="text-center">SIRA</th>
                    <th width="7%" class="text-center">YIL</th>
                    @if($hasBolge)<th width="13%" class="text-left">BÖLGE</th>@endif
                    <th width="7%" class="text-center">FATURA SAYISI</th>
                    <th width="{{ $hasBolge ? '17%' : '20%' }}" class="text-right">BRÜT TÜKETİM (KWH)</th>
                    <th width="{{ $hasBolge ? '17%' : '20%' }}" class="text-right">BRÜT TUTAR (₺)</th>
                    <th width="{{ $hasBolge ? '17%' : '20%' }}" class="text-right">NET TÜKETİM (KWH)</th>
                    <th width="{{ $hasBolge ? '19%' : '22%' }}" class="text-right">NET TUTAR (₺)</th>
                </tr>
            @else {{-- periodical --}}
                <tr>
                    <th width="4%" class="text-center">SIRA</th>
                    <th width="16%" class="text-center">DÖNEM</th>
                    <th width="12%" class="text-left">BÖLGE</th>
                    <th width="18%" class="text-right">BRÜT TÜKETİM (KWH)</th>
                    <th width="18%" class="text-right">BRÜT TUTAR (₺)</th>
                    <th width="16%" class="text-right">NET TÜKETİM (KWH)</th>
                    <th width="16%" class="text-right">NET TUTAR (₺)</th>
                </tr>
            @endif
        </thead>
        
        <tbody>
            @forelse($results as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>

                @if($type === 'endeks')
                    @php
                        $t1I = (float)str_replace(',', '.', $row->t1_ilk_endeks);
                        $t1S = (float)str_replace(',', '.', $row->t1_son_endeks);
                        $t2I = (float)str_replace(',', '.', $row->t2_ilk_endeks);
                        $t2S = (float)str_replace(',', '.', $row->t2_son_endeks);
                        $t3I = (float)str_replace(',', '.', $row->t3_ilk_endeks);
                        $t3S = (float)str_replace(',', '.', $row->t3_son_endeks);
                        $hasT = ($t1I + $t2I + $t3I) > 0;
                        $t0I  = $hasT ? ($t1I+$t2I+$t3I) : (float)str_replace(',', '.', $row->t0_ilk_endeks);
                        $t0S  = $hasT ? ($t1S+$t2S+$t3S) : (float)str_replace(',', '.', $row->t0_son_endeks);
                        $fark = $t0S - $t0I;
                        $t0G  = (float)($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim);
                    @endphp
                    <td class="text-center">{{ $row->donem }}</td>
                    <td class="text-center">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</td>
                    @if($hasBolge)<td class="text-left">{{ $row->bolge ?? '—' }}</td>@endif
                    <td class="text-right">{{ number_format($t0I, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($t0S, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($fark, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($t0G, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row->tutar_toplam, 2, ',', '.') }} ₺</td>
                    <td class="text-center">{{ $t0S < $t0I ? 'HATALI' : 'TAMAM' }}</td>

                @elseif($type === 'koy_merkez')
                    <td class="text-center">{{ $row->donem }}</td>
                    <td class="text-left">{{ $row->bolge ?? '—' }}</td>
                    <td class="text-right">{{ number_format($row->merkez_tuketim, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row->merkez_tutar, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row->koy_tuketim, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row->koy_tutar, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row->merkez_tuketim + $row->koy_tuketim, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row->merkez_tutar + $row->koy_tutar, 2, ',', '.') }} ₺</td>

                @elseif($type === 'ek_tuketim')
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
                    <td class="text-center">{{ $row->donem }}</td>
                    <td class="text-center">{{ $row->tesisat_no }}</td>
                    <td class="text-center">{{ $row->ilk_okuma ? $row->ilk_okuma->format('d.m.Y') : '—' }}</td>
                    <td class="text-center">{{ $row->son_okuma ? $row->son_okuma->format('d.m.Y') : '—' }}</td>
                    <td class="text-right">{{ number_format($t1Ilave, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($t2Ilave, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($t3Ilave, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($ilaveToplam, 2, ',', '.') }}</td>

                @elseif($isDetailList)
                    <td class="text-center">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</td>
                    <td class="text-left">{{ substr($row->hesap_adi ?? '', 0, 38) }}</td>
                    <td class="text-center">{{ $row->fatura_no }}</td>
                    @if($type === 'anomali')
                        <td class="text-left">
                            @php $anomaliler = $row->payload['_tuketim_anomalileri'] ?? []; @endphp
                            @foreach($anomaliler as $ano)
                                <span class="badge">{{ is_array($ano) ? ($ano['mesaj'] ?? '') : $ano }}</span>
                            @endforeach
                        </td>
                    @else
                        <td class="text-center">{{ $row->donem }}</td>
                    @endif
                    <td class="text-right" style="font-weight: bold;">{{ number_format(($row->fatura_edilecek_toplam_tuketim_kwh ?: ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim + $row->ek_tuketim)), 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row->tutar_toplam, 2, ',', '.') }} ₺</td>

                @elseif($type === 'yearly')
                    <td class="text-center" style="font-weight: bold; color: #1a73e8;">{{ $row->yil }}</td>
                    @if($hasBolge)<td class="text-left">{{ $row->bolge ?? '—' }}</td>@endif
                    <td class="text-center">{{ $row->fatura_sayisi }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format((float)($row->brut_tuketim ?? 0), 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; color: #dc2626;">{{ number_format((float)($row->brut_tutar ?? 0), 2, ',', '.') }} ₺</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row->toplam_tuketim, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row->toplam_tutar, 2, ',', '.') }} ₺</td>

                @else {{-- periodical --}}
                    <td class="text-center">{{ $row->donem }}</td>
                    <td class="text-left">{{ $row->ilce ?? $row->bolge ?? '—' }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format((float)($row->brut_tuketim ?? 0), 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; color: #dc2626;">{{ number_format((float)($row->brut_tutar ?? 0), 2, ',', '.') }} ₺</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row->toplam_tuketim, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row->toplam_tutar, 2, ',', '.') }} ₺</td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding:10px;">Kriterlere uygun kayıt bulunamadı.</td>
            </tr>
            @endforelse
        </tbody>

        @if($results->count() > 0)
        <tfoot>
            <tr class="total-row">
                @if($type === 'endeks')
                    <td colspan="{{ $hasBolge ? 7 : 6 }}" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                    <td class="text-right">{{ number_format($tK, 2, ',', '.') }} kWh</td>
                    <td class="text-right">{{ number_format($tT, 2, ',', '.') }} ₺</td>
                    <td></td>

                @elseif($type === 'koy_merkez')
                    <td colspan="3" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                    <td class="text-right">{{ number_format($tMK, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tMT, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tKK, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tKT, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tK, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tT, 2, ',', '.') }} ₺</td>

                @elseif($type === 'ek_tuketim')
                    <td colspan="5" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                    <td class="text-right">{{ number_format($tT1I, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tT2I, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tT3I, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tT1I + $tT2I + $tT3I, 2, ',', '.') }}</td>

                @elseif($isDetailList)
                    <td colspan="5" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                    <td class="text-right">{{ number_format($tK, 2, ',', '.') }} kWh</td>
                    <td class="text-right">{{ number_format($tT, 2, ',', '.') }} ₺</td>

                @elseif($type === 'yearly')
                    <td colspan="{{ $hasBolge ? 4 : 3 }}" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                    <td class="text-right">{{ number_format($tBrut ?? 0, 2, ',', '.') }} kWh</td>
                    <td class="text-right">{{ number_format($tBrutTutar ?? 0, 2, ',', '.') }} ₺</td>
                    <td class="text-right">{{ number_format($tK, 2, ',', '.') }} kWh</td>
                    <td class="text-right">{{ number_format($tT, 2, ',', '.') }} ₺</td>

                @else {{-- periodical --}}
                    <td colspan="3" class="total-label" style="padding-right: 15px;">GENEL TOPLAM :</td>
                    <td class="text-right">{{ number_format($tBrut ?? 0, 2, ',', '.') }} kWh</td>
                    <td class="text-right">{{ number_format($tBrutTutar ?? 0, 2, ',', '.') }} ₺</td>
                    <td class="text-right">{{ number_format($tK, 2, ',', '.') }} kWh</td>
                    <td class="text-right">{{ number_format($tT, 2, ',', '.') }} ₺</td>
                @endif
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width:100%; border:none; margin:0;">
            <tr>
                <td style="border:none; text-align:left; padding:0; background:transparent;">Smart ŞUSKİ - Akıllı Raporlama Sistemi</td>
                <td style="border:none; text-align:right; padding:0; background:transparent;">Bu belge elektronik ortamda üretilmiştir.</td>
            </tr>
        </table>
    </div>

</body>
</html>
