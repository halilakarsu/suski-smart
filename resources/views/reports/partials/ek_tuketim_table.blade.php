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
        Ek Tüketim Fatura Listesi
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
                    <th style="text-align:right;">Tüketim (kWh)</th>
                    <th style="text-align:right;">T1 İlave (kWh)</th>
                    <th style="text-align:right;">T2 İlave (kWh)</th>
                    <th style="text-align:right;">T3 İlave (kWh)</th>
                    <th style="text-align:right;">İlave Tutar (₺)</th>
                    <th style="text-align:center;">Detay</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $i => $row)
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
                        $tuketim = (float) ($row->fatura_edilecek_toplam_tuketim_kwh ?: ($row->t1_tuketim + $row->t2_tuketim + $row->t3_tuketim + $row->ek_tuketim));
                        $birimFiyat = (float) str_replace(',', '.', $row->birim_fiyat ?? '0');
                        $ilaveTutar = $ilaveToplam * $birimFiyat;
                    @endphp
                    <tr>
                        <td style="color:#94a3b8;font-weight:600;font-size:.85rem;">{{ $results->firstItem() + $i }}</td>
                        <td><span class="badge-donem">{{ $row->donem }}</span></td>
                        <td><span class="badge-tesisat">{{ $row->tesisat_no }}</span></td>
                        <td style="text-align:right;font-weight:700;">{{ number_format($tuketim, 2, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;color:#7c3aed;">{{ number_format($t1Ilave, 2, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;color:#7c3aed;">{{ number_format($t2Ilave, 2, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;color:#7c3aed;">{{ number_format($t3Ilave, 2, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:700;color:#c2410c;">{{ number_format($ilaveTutar, 2, ',', '.') }}</td>
                        <td style="text-align:center;">
                            <button type="button" class="btn-detay ek-tuketim-detay-btn" data-tesisat="{{ $row->tesisat_no }}">
                                <i class="fas fa-search-plus"></i> Detay
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                            <i class="fas fa-search-minus" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                            Seçilen dönemde ek tüketimli fatura bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($results->count() > 0)
            <tfoot>
                <tr style="background:#f1f5f9; font-weight:800;">
                    <td colspan="3" style="font-size:.9rem;letter-spacing:.05em;">GENEL TOPLAM</td>
                    <td style="text-align:right;">{{ number_format($totalKWH, 2, ',', '.') }}</td>
                    <td style="text-align:right;">—</td>
                    <td style="text-align:right;">—</td>
                    <td style="text-align:right;">—</td>
                    <td style="text-align:right;color:#c2410c;">{{ number_format($totalIlaveTutar, 2, ',', '.') }}</td>
                    <td></td>
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
