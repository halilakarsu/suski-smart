<style>
    .tt-premium { box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .tt-premium .tt-header th {
        background: linear-gradient(135deg, #1e293b, #334155);
        padding: 14px 12px; font-size: 0.7rem; font-weight: 700; color: #e2e8f0;
        text-transform: uppercase; letter-spacing: 0.05em; border: none;
        white-space: nowrap; position: sticky; top: 0; z-index: 5;
    }
    .tt-premium .tt-header th:first-child { border-top-left-radius: 12px; }
    .tt-premium .tt-header th:last-child { border-top-right-radius: 12px; }
    .tt-premium .tt-header th .period-badge {
        display: inline-block; background: rgba(255,255,255,0.08); padding: 2px 8px;
        border-radius: 6px; font-weight: 700;
    }
    .tt-premium td {
        padding: 10px 12px; font-size: 0.78rem; color: #1e293b;
        border-bottom: 1px solid #f1f5f9; transition: background 0.15s;
    }
    .tt-premium tbody tr:nth-child(even) td { background: #fafbfc; }
    .tt-premium tbody tr:hover td { background: #eef2ff; }
    .tt-premium .tt-tesisat { font-weight: 700; color: #0f172a; font-size: 0.78rem; }
    .tt-premium .tt-val {
        text-align: right; font-weight: 600; font-size: 0.78rem;
        font-variant-numeric: tabular-nums; letter-spacing: 0.02em;
    }
    .tt-premium .tt-val.high { color: #059669; }
    .tt-premium .tt-val.medium { color: #2563eb; }
    .tt-premium .tt-val.low { color: #64748b; }
    .tt-premium .tt-footer td {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        font-weight: 800; font-size: 0.78rem; padding: 12px;
        border-top: 2px solid #2563eb; color: #0f172a;
    }
    .tt-premium .tt-footer .tt-val { font-weight: 800; color: #1d4ed8; }
    .tt-premium .tt-index { color: #94a3b8; font-weight: 600; text-align: center; font-size: 0.72rem; }
</style>

<div class="glass-card tt-premium" style="padding:0; overflow:hidden;">
    <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; background:#fff;">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#2563eb;"><i class="fas fa-table" style="font-size:0.9rem;"></i></div>
            <div>
                <span style="font-size:0.85rem; font-weight:800; color:#0f172a;">{{ ($veri ?? 'tuketim') === 'tutar' ? 'Tutar Bazlı Dönem Raporu' : 'Tüketim Dönem Raporu' }}</span>
                <span style="font-size:0.72rem; color:#94a3b8; font-weight:500; margin-left:8px;">
                    {{ $pivotData->total() }} tesisat
                    @if(request()->filled('start_period') && request()->filled('end_period'))
                        | {{ request('start_period') }} — {{ request('end_period') }}
                    @elseif(request()->filled('start_period'))
                        | {{ request('start_period') }}
                    @elseif(request()->filled('end_period'))
                        | … {{ request('end_period') }}
                    @endif
                </span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
            @if(($veri ?? 'tuketim') === 'tutar')
            <div style="text-align:right;">
                <span style="font-size:0.6rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;display:block;">Toplam Tutar</span>
                <span style="font-size:1rem;font-weight:800;color:#059669;">₺ {{ number_format($totalAmount, 2, ',', '.') }}</span>
            </div>
            @else
            <div style="text-align:right;">
                <span style="font-size:0.6rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;display:block;">Toplam Tüketim</span>
                <span style="font-size:1rem;font-weight:800;color:#2563eb;">{{ number_format($totalKWH, 2, ',', '.') }} kWh</span>
            </div>
            @endif
        </div>
    </div>
    <div class="tbl-wrap" style="overflow-x:auto; border-radius:0; box-shadow:none;">
        <table class="tbl-tuketim tt-premium" style="width:100%; border-collapse:separate; border-spacing:0;">
            <thead>
                <tr class="tt-header">
                    <th style="width:40px;text-align:center;">#</th>
                    <th style="min-width:130px;">Tesisat No</th>
                    @foreach($pivotPeriods as $pIdx => $period)
                        @php
                            $colors = ['#2563eb','#7c3aed','#059669','#dc2626','#d97706','#0891b2','#4f46e5','#16a34a','#ca8a04','#db2777','#0d9488','#6366f1'];
                            $c = $colors[$pIdx % count($colors)];
                        @endphp
                        <th style="text-align:right; min-width:100px;">
                            <span class="period-badge" style="background:{{ $c }}20; color:{{ $c }};">{{ $period }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($pivotData as $tesisatNo => $donemler)
                    @php
                        $vals = array_map(fn($p) => $donemler[$p] ?? 0, iterator_to_array($pivotPeriods));
                        $maxVal = max($vals) ?: 1;
                    @endphp
                    <tr>
                        <td class="tt-index">{{ $pivotData->firstItem() + $loop->index }}</td>
                        <td><span class="tt-tesisat">{{ $tesisatNo }}</span></td>
                        @foreach($pivotPeriods as $period)
                            @php
                                $val = $donemler[$period] ?? null;
                                $ratio = $val ? ($val / $maxVal) : 0;
                                $colorClass = $ratio > 0.7 ? 'high' : ($ratio > 0.35 ? 'medium' : 'low');
                            @endphp
                            <td class="tt-val {{ $colorClass }}">
                                {{ $val !== null ? number_format($val, 2, ',', '.') : '—' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 2 + count($pivotPeriods) }}" style="text-align:center;padding:50px;color:#94a3b8;">
                            <i class="fas fa-search-minus" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                            Kriterlere uygun fatura bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($pivotData->total() > 0)
            <tfoot>
                <tr class="tt-footer">
                    <td class="tt-index" style="background:transparent;">#</td>
                    <td>GENEL TOPLAM</td>
                    @foreach($pivotPeriods as $period)
                        <td class="tt-val">{{ number_format($colTotals[$period] ?? 0, 2, ',', '.') }}</td>
                    @endforeach
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($pivotData->hasPages())
        <div class="d-flex justify-content-center" style="padding:14px;">
            {{ $pivotData->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>