<div class="glass-card">
    <h5 class="section-title">
        <i class="fas fa-chart-line"></i> 
        Dönemsel Tüketim Raporu
        @php $defaultPeriod = $donemler && $donemler->first() ? $donemler->first() : null; @endphp
        @if(request()->filled('start_period'))
            <span style="font-size:.85rem;color:#94a3b8;font-weight:500;margin-left:8px;">({{ request('start_period') }}{{ request('end_period') ? ' - '.request('end_period') : '' }})</span>
        @elseif(request()->filled('end_period'))
            <span style="font-size:.85rem;color:#94a3b8;font-weight:500;margin-left:8px;">(... - {{ request('end_period') }})</span>
        @elseif($defaultPeriod)
            <span style="font-size:.85rem;color:#94a3b8;font-weight:500;margin-left:8px;">(Son Dönem: {{ $defaultPeriod }})</span>
        @endif
    </h5>
    
    @if($totals && $totals->total_fatura > 0)
        {{-- İSTATİSTİK KUTULARI --}}
        <div class="stats-row" style="grid-template-columns: repeat(5, 1fr); align-items: stretch;">
            <div class="stat-box">
                <div class="stat-icon purple"><i class="fas fa-file-invoice"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totals->total_fatura, 0, ',', '.') }}</div>
                    <div class="stat-lbl">Toplam Fatura</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon orange"><i class="fas fa-tachometer-alt"></i></div>
                <div>
                    <div class="stat-val">{{ number_format(($totals->total_t1_fark ?? 0) + ($totals->total_t2_fark ?? 0) + ($totals->total_t3_fark ?? 0), 0, ',', '.') }}</div>
                    <div class="stat-lbl">Brüt Tüketim (kWh)</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#fef2f2; color:#dc2626;"><i class="fas fa-wallet"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totals->total_brut_tutar ?? 0, 2, ',', '.') }}</div>
                    <div class="stat-lbl">Brüt Tutar (₺)</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon blue"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totals->total_tuketim, 0, ',', '.') }}</div>
                    <div class="stat-lbl">Net Tüketim (kWh)</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon green"><i class="fas fa-lira-sign"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totals->total_tutar, 2, ',', '.') }}</div>
                    <div class="stat-lbl">Net Tutar (₺)</div>
                </div>
            </div>
        </div>
    @endif

    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th style="text-align: center;">Dönem</th>
                    <th style="text-align: left;">Bölge / İlçe</th>
                    <th style="text-align: right;">Brüt Tüketim (kWh)</th>
                    <th style="text-align: right;">Brüt Tutar (₺)</th>
                    <th style="text-align: right;">Net Tüketim (kWh)</th>
                    <th style="text-align: right;">Net Tutar (₺)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $row)
                    <tr>
                        <td style="text-align: center; color: #94a3b8;">{{ $results->firstItem() + $loop->index }}</td>
                        <td><span class="badge-donem">{{ $row->donem }}</span></td>
                        <td style="font-weight: 600;">{{ $row->ilce }}</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($row->brut_tuketim, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 700; color: #dc2626;">{{ number_format($row->brut_tutar, 2, ',', '.') }} ₺</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($row->toplam_tuketim, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 800; color: #059669;">{{ number_format($row->toplam_tutar, 2, ',', '.') }} ₺</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 40px; color: #94a3b8;">
                            <i class="fas fa-search-minus mb-3" style="font-size: 2rem; display: block;"></i>
                            Kriterlere uygun herhangi bir veri bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($results->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $results->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
