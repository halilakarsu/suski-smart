@php
    $totalKoyTuketim = $results->sum('koy_tuketim');
    $totalKoyTutar = $results->sum('koy_tutar');
    $totalMerkezTuketim = $results->sum('merkez_tuketim');
    $totalMerkezTutar = $results->sum('merkez_tutar');
    $genelToplamTuketim = $totalKoyTuketim + $totalMerkezTuketim;
    $genelToplamTutar = $totalKoyTutar + $totalMerkezTutar;
@endphp

{{-- İSTATİSTİK KUTULARI --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon blue"><i class="fas fa-city"></i></div>
        <div>
            <div class="stat-val">{{ number_format($totalMerkezTuketim, 2, ',', '.') }}</div>
            <div class="stat-lbl">Merkez Toplam Tüketim (kWh)</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon green"><i class="fas fa-tree"></i></div>
        <div>
            <div class="stat-val">{{ number_format($totalKoyTuketim, 2, ',', '.') }}</div>
            <div class="stat-lbl">Köy Toplam Tüketim (kWh)</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon purple"><i class="fas fa-chart-line"></i></div>
        <div>
            <div class="stat-val">₺ {{ number_format($genelToplamTutar, 2, ',', '.') }}</div>
            <div class="stat-lbl">Genel Toplam Tutar</div>
        </div>
    </div>
</div>

<div class="glass-card">
    <h5 class="section-title">
        <i class="fas fa-table"></i> 
        Merkez & Köy Karşılaştırma Raporu (Özet Pivot)
        @if(request()->filled('start_period') || request()->filled('end_period'))
            <span style="font-size:.85rem;color:#94a3b8;font-weight:500;margin-left: 8px;">
                (Dönem: 
                @if(request()->filled('start_period') && request()->filled('end_period'))
                    {{ request('start_period') }} - {{ request('end_period') }}
                @elseif(request()->filled('start_period'))
                    {{ request('start_period') }}
                @else
                    ... - {{ request('end_period') }}
                @endif
                )
            </span>
        @endif
    </h5>
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Dönem</th>
                    <th>İlçe / Bölge</th>
                    <th style="text-align:right;background:#f8fafc;border-left:1px solid #e2e8f0;border-top-left-radius:10px;">Merkez (kWh)</th>
                    <th style="text-align:right;background:#f8fafc;border-right:1px solid #e2e8f0;">Merkez Tutar (₺)</th>
                    <th style="text-align:right;background:#f0fdf4;border-left:1px solid #dcfce7;">Köy (kWh)</th>
                    <th style="text-align:right;background:#f0fdf4;border-right:1px solid #dcfce7;">Köy Tutar (₺)</th>
                    <th style="text-align:right;background:#fefce8;border-left:1px solid #fef08a;">Genel Top. (kWh)</th>
                    <th style="text-align:right;background:#fefce8;border-right:1px solid #fef08a;border-top-right-radius:10px;">Genel Tutar (₺)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $i => $row)
                @php
                    $rowGenelTuketim = $row->merkez_tuketim + $row->koy_tuketim;
                    $rowGenelTutar = $row->merkez_tutar + $row->koy_tutar;
                @endphp
                <tr>
                    <td style="color:#94a3b8;font-size:.82rem;font-weight:600;">{{ $i + 1 }}</td>
                    <td><span class="badge-donem">{{ $row->donem }}</span></td>
                    <td style="font-weight:700;color:#1e293b;">{{ $row->bolge ?? 'Tümü' }}</td>
                    
                    <td style="text-align:right;font-weight:600;border-left:1px dashed #e2e8f0;">{{ number_format($row->merkez_tuketim, 2, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:700;color:#1d4ed8;border-right:1px dashed #e2e8f0;">{{ number_format($row->merkez_tutar, 2, ',', '.') }}</td>
                    
                    <td style="text-align:right;font-weight:600;border-left:1px dashed #dcfce7;">{{ number_format($row->koy_tuketim, 2, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:700;color:#15803d;border-right:1px dashed #dcfce7;">{{ number_format($row->koy_tutar, 2, ',', '.') }}</td>
                    
                    <td style="text-align:right;font-weight:700;background:#fefce8;border-left:1px solid #fef08a;">{{ number_format($rowGenelTuketim, 2, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:800;color:#b45309;background:#fefce8;border-right:1px solid #fef08a;">{{ number_format($rowGenelTutar, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fas fa-search-minus" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                        Kriterlere uygun veri bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($results->count() > 0)
            <tfoot>
                <tr style="background:#f1f5f9; font-weight:800;">
                    <td colspan="3">GENEL TOPLAM</td>
                    <td style="text-align:right;">{{ number_format($totalMerkezTuketim, 2, ',', '.') }}</td>
                    <td style="text-align:right;color:#1d4ed8;">{{ number_format($totalMerkezTutar, 2, ',', '.') }}</td>
                    <td style="text-align:right;">{{ number_format($totalKoyTuketim, 2, ',', '.') }}</td>
                    <td style="text-align:right;color:#15803d;">{{ number_format($totalKoyTutar, 2, ',', '.') }}</td>
                    <td style="text-align:right;color:#b45309;">{{ number_format($genelToplamTuketim, 2, ',', '.') }}</td>
                    <td style="text-align:right;color:#b45309;">{{ number_format($genelToplamTutar, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
