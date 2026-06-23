{{-- İSTATİSTİK KUTULARI --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-icon purple"><i class="fas fa-file-invoice"></i></div>
        <div>
            <div class="stat-val">{{ number_format($results->total()) }}</div>
            <div class="stat-lbl">Toplam Fatura</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-icon blue"><i class="fas fa-bolt"></i></div>
        <div>
            <div class="stat-val">{{ number_format($totalKWH, 2, ',', '.') }}</div>
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

<div class="glass-card">
    <h5 class="section-title">
        <i class="fas fa-table"></i> 
        Fatura Listesi 
        <span style="font-size:.85rem;color:#94a3b8;font-weight:500;">
            ({{ $results->total() }} kayıt
            @if(request()->filled('start_period') || request()->filled('end_period'))
                | Dönem: 
                @if(request()->filled('start_period') && request()->filled('end_period'))
                    {{ request('start_period') }} - {{ request('end_period') }}
                @elseif(request()->filled('start_period'))
                    {{ request('start_period') }}
                @else
                    ... - {{ request('end_period') }}
                @endif
            @endif
            )
        </span>
    </h5>
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Dönem</th>
                    <th>Tesisat No</th>
                    <th>İlk Okuma</th>
                    <th>Son Okuma</th>
                    <th style="text-align:right;">İlk Endeks</th>
                    <th style="text-align:right;">Son Endeks</th>
                    <th style="text-align:right;">Çarpan</th>
                    <th style="text-align:right;">Tüketim (kWh)</th>
                    <th style="text-align:right;">Tutar (₺)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $i => $row)
                    <tr>
                        <td style="color:#94a3b8;font-weight:600;font-size:.85rem;">{{ $results->firstItem() + $i }}</td>
                        <td><span class="badge-donem">{{ $row->donem }}</span></td>
                        <td><span class="badge-tesisat">{{ $row->abone_tesis_no ?? $row->tesisat_no }}</span></td>
                        <td style="font-size:.85rem;color:#475569;">{{ $row->ilk_okuma ? $row->ilk_okuma->format('d.m.Y') : '—' }}</td>
                        <td style="font-size:.85rem;color:#475569;">{{ $row->son_okuma ? $row->son_okuma->format('d.m.Y') : '—' }}</td>
                        <td style="text-align:right;font-weight:600;">{{ number_format($row->t0_ilk_endeks, 0, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;">{{ number_format($row->t0_son_endeks, 0, ',', '.') }}</td>
                        <td style="text-align:right;color:#64748b;">{{ number_format($row->carpan, 2, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:700;">
                            {{ number_format($row->fatura_edilecek_toplam_tuketim_kwh ?: ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim + $row->ek_tuketim), 2, ',', '.') }}
                            <span style="font-size:.72rem;color:#94a3b8;">kWh</span>
                        </td>
                        <td style="text-align:right;font-weight:800;color:#059669;">
                            ₺ {{ number_format($row->tutar_toplam ?? $row->fatura_tutari ?? 0, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="fas fa-search-minus" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                            Kriterlere uygun fatura bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($results->count() > 0)
            <tfoot>
                <tr style="background:#f1f5f9; font-weight:800;">
                    <td colspan="8" style="font-size:.9rem;letter-spacing:.05em;">GENEL TOPLAM</td>
                    <td style="text-align:right;">{{ number_format($totalKWH, 2, ',', '.') }} <span style="font-size:.72rem;">kWh</span></td>
                    <td style="text-align:right;color:#059669;">₺ {{ number_format($totalAmount, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($results->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $results->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
