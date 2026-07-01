<div class="glass-card">
    <h5 class="section-title"><i class="fas fa-list"></i> İlçe Bazında Arıza Sayıları</h5>
    <div class="tbl-wrap">
        <table class="tbl">
            <thead>
                <tr>
                    <th class="text-center" width="8%">#</th>
                    <th class="text-left">İLÇE</th>
                    <th class="text-center" width="15%">YIL</th>
                    <th class="text-center" width="15%">ARIZA SAYISI</th>
                </tr>
            </thead>
            <tbody>
                @php $toplam = 0; @endphp
                @forelse($results as $index => $row)
                @php $toplam += $row->toplam; @endphp
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td style="font-weight: 700;">{{ $row->ilce ?? '—' }}</td>
                    <td class="text-center" style="font-weight: 800; color: #dc2626;">{{ $row->yil }}</td>
                    <td class="text-center fw-bold">{{ number_format($row->toplam, 0, '', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding:40px; color:#94a3b8;">
                        <div style="font-size:1.2rem; margin-bottom:8px;">🔍</div>
                        Seçilen kriterlere uygun kayıt bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($results->count() > 0)
            <tfoot>
                <tr style="background: #fef2f2;">
                    <td colspan="3" style="text-align: right; font-weight: 800; color: #dc2626; padding-right: 20px;">GENEL TOPLAM :</td>
                    <td class="text-center" style="font-weight: 800; color: #dc2626; font-size: 1rem;">{{ number_format($toplam, 0, '', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
