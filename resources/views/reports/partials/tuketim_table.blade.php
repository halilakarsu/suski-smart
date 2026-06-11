<style>
    .tbl-tuketim { width: 100%; min-width: auto; border-collapse: separate; border-spacing: 0; }
    .tbl-tuketim th { background: #f8fafc; padding: 6px 8px; font-size: 0.65rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.03em; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
    .tbl-tuketim td { padding: 5px 8px; font-size: 0.7rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; background: #fff; }
    .tbl-tuketim tr:hover td { background: #f8fafc; }
    .tbl-tuketim .tt-tesisat { font-size: 0.7rem; font-weight: 700; color: #0f172a; white-space: nowrap; }
    .tbl-tuketim .tt-val { text-align: right; font-weight: 600; font-size: 0.7rem; font-variant-numeric: tabular-nums; }
    .tbl-tuketim .tt-footer { background: #f1f5f9; font-weight: 800; }
    .tbl-tuketim .tt-footer td { font-size: 0.7rem; }
</style>

<div class="glass-card" style="padding:0; overflow:hidden;">
    <div style="padding:12px 16px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-table" style="color:#3b82f6; font-size:0.85rem;"></i>
        <span style="font-size:0.8rem; font-weight:800; color:#0f172a;">{{ ($veri ?? 'tuketim') === 'tutar' ? 'Tutar Bazlı Dönem Raporu' : 'Tüketim Dönem Raporu' }}</span>
        <span style="font-size:0.7rem; color:#94a3b8; font-weight:500;">
            {{ $pivotData->total() }} tesisat
            @if(request()->filled('start_period') && request()->filled('end_period'))
                | {{ request('start_period') }} - {{ request('end_period') }}
            @elseif(request()->filled('start_period'))
                | {{ request('start_period') }}
            @elseif(request()->filled('end_period'))
                | ... - {{ request('end_period') }}
            @endif
        </span>
    </div>
    <div class="tbl-wrap" style="overflow-x:auto; border-radius:0; box-shadow:none;">
        <table class="tbl-tuketim">
            <thead>
                <tr>
                    <th style="width:28px;">#</th>
                    <th>Tesisat No</th>
                    @foreach($pivotPeriods as $period)
                        <th style="text-align:right;">{{ $period }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($pivotData as $tesisatNo => $donemler)
                    <tr>
                        <td style="color:#94a3b8;font-weight:600;text-align:center;">{{ $pivotData->firstItem() + $loop->index }}</td>
                        <td><span class="tt-tesisat">{{ $tesisatNo }}</span></td>
                        @foreach($pivotPeriods as $period)
                            <td class="tt-val">
                                {{ isset($donemler[$period]) ? number_format($donemler[$period], 2, ',', '.') : '—' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 2 + count($pivotPeriods) }}" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="fas fa-search-minus" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                            Kriterlere uygun fatura bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($pivotData->total() > 0)
            <tfoot>
                <tr class="tt-footer">
                    <td style="text-align:center;">#</td>
                    <td>GENEL TOPLAM</td>
                    @foreach($pivotPeriods as $period)
                        <td style="text-align:right;">{{ number_format($colTotals[$period] ?? 0, 2, ',', '.') }}</td>
                    @endforeach
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($pivotData->hasPages())
        <div class="d-flex justify-content-center" style="padding:12px;">
            {{ $pivotData->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
