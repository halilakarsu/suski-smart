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
    
    @php 
        $totalKwh = 0; $totalAmount = 0; $totalFatura = 0; 
        foreach($results as $row) {
            $totalKwh += $row->toplam_tuketim; 
            $totalAmount += $row->toplam_tutar; 
            $totalFatura += $row->fatura_sayisi;
        }
    @endphp

    @if($results->count() > 0)
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-icon purple"><i class="fas fa-file-invoice"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totalFatura, 0, ',', '.') }}</div>
                    <div class="stat-lbl">Toplam Fatura</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon blue"><i class="fas fa-bolt"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totalKwh, 0, ',', '.') }}</div>
                    <div class="stat-lbl">Toplam Tüketim (kWh)</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon green"><i class="fas fa-lira-sign"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totalAmount, 2, ',', '.') }}</div>
                    <div class="stat-lbl">Toplam Tutar (₺)</div>
                </div>
            </div>
        </div>
    @endif

    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 60px;">Sıra</th>
                    <th style="width: 80px; text-align: center;">Yıl</th>
                    <th>Bölge</th>
                    <th style="text-align: center;">Fatura Sayısı</th>
                    <th style="text-align: right;">Toplam Tüketim (kWh)</th>
                    <th style="text-align: right;">Toplam Maliyet</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: center;"><span style="background: #eff6ff; color: #1e40af; font-weight: 800; padding: 3px 10px; border-radius: 6px; font-size: 0.9rem;">{{ $row->yil }}</span></td>
                        <td><span style="font-weight: 600;">{{ $row->bolge }}</span></td>
                        <td style="text-align: center;"><span class="badge" style="background:#eff6ff;color:#1e40af;font-size:0.9rem;padding:6px 12px;border-radius:8px;">{{ $row->fatura_sayisi }}</span></td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($row->toplam_tuketim, 0, ',', '.') }} <span style="font-size: 0.75rem; color: #94a3b8;">kWh</span></td>
                        <td style="text-align: right; font-weight: 800; color: #059669;">&#8378; {{ number_format($row->toplam_tutar, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px; color: #94a3b8;">
                             <i class="fas fa-search-minus mb-3" style="font-size: 2rem; display: block;"></i>
                            Kriterlere uygun herhangi bir veri bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($results->count() > 0)
                <tfoot>
                    <tr style="background:#f1f5f9; font-weight:800;">
                        <td colspan="3">GENEL TOPLAM</td>
                        <td style="text-align: center;">{{ number_format($totalFatura, 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($totalKwh, 0, ',', '.') }} <span style="font-size: 0.75rem;">kWh</span></td>
                        <td style="text-align: right; color:#059669;">&#8378; {{ number_format($totalAmount, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
