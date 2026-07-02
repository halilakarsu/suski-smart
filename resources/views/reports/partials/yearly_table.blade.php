<div class="glass-card">
    <h5 class="section-title">
        <i class="fas fa-chart-line"></i> 
        Yıl Bazında Fatura Bilgileri
        @if(request()->filled('start_year') || request()->filled('end_year'))
            <span style="font-size:.85rem;color:#94a3b8;font-weight:500;margin-left: 8px;">
                (Yıl: 
                @if(request()->filled('start_year') && request()->filled('end_year'))
                    @if(request('start_year') == request('end_year'))
                        {{ request('start_year') }}
                    @else
                        {{ request('start_year') }} - {{ request('end_year') }}
                    @endif
                @elseif(request()->filled('start_year'))
                    {{ request('start_year') }}
                @else
                    ... - {{ request('end_year') }}
                @endif
                )
            </span>
        @endif
    </h5>
    
    @if($totals && $totals->total_fatura > 0)
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
                    <th style="text-align: center;">Yıl</th>
                    <th style="text-align: left;">Bölge</th>
                    <th style="text-align: center;">Fatura Sayısı</th>
                    <th style="text-align: right;">Brüt Tüketim (kWh)</th>
                    <th style="text-align: right;">Brüt Tutar (₺)</th>
                    <th style="text-align: right;">Net Tüketim (kWh)</th>
                    <th style="text-align: right;">Net Tutar (₺)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $index => $row)
                    <tr>
                        <td style="text-align: center; color: #94a3b8;">{{ $index + 1 }}</td>
                        <td style="text-align: center;"><span style="background: #eff6ff; color: #1e40af; font-weight: 800; padding: 3px 10px; border-radius: 6px; font-size: 0.9rem;">{{ $row->yil }}</span></td>
                        <td style="font-weight: 600;">{{ $row->bolge }}</td>
                        <td style="text-align: center;"><span class="badge" style="background:#eff6ff;color:#1e40af;font-size:0.9rem;padding:6px 12px;border-radius:8px;">{{ number_format($row->fatura_sayisi) }}</span></td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($row->brut_tuketim, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 700; color: #dc2626;">{{ number_format($row->brut_tutar, 2, ',', '.') }} ₺</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($row->toplam_tuketim, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 800; color: #059669;">{{ number_format($row->toplam_tutar, 2, ',', '.') }} ₺</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px; color: #94a3b8;">
                             <i class="fas fa-search-minus mb-3" style="font-size: 2rem; display: block;"></i>
                            Kriterlere uygun herhangi bir veri bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($results->count() > 0)
                <tfoot>
                    <tr style="background:#f1f5f9; font-weight:800;">
                        <td colspan="4">GENEL TOPLAM</td>
                        <td style="text-align: right;">{{ number_format(($totals->total_t1_fark ?? 0) + ($totals->total_t2_fark ?? 0) + ($totals->total_t3_fark ?? 0), 2, ',', '.') }} kWh</td>
                        <td style="text-align: right;">{{ number_format($totals->total_brut_tutar ?? 0, 2, ',', '.') }} ₺</td>
                        <td style="text-align: right;">{{ number_format($totals->total_tuketim, 2, ',', '.') }} kWh</td>
                        <td style="text-align: right; color:#059669;">{{ number_format($totals->total_tutar, 2, ',', '.') }} ₺</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
