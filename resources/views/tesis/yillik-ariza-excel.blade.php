<table>
    <thead>
        <tr>
            <th colspan="4" style="font-size: 14pt; font-weight: bold; text-align: center; background-color: #1E293B; color: #FFFFFF; padding: 12px;">YILLIK ARIZA RAPORU</th>
        </tr>
        <tr>
            <th>SIRA</th>
            <th>YIL</th>
            <th>İLÇE</th>
            <th>ARIZA SAYISI</th>
        </tr>
    </thead>
    <tbody>
        @php $toplam = 0; @endphp
        @forelse($data as $index => $row)
        @php $toplam += $row->toplam; @endphp
        <tr>
            <td align="center">{{ $index + 1 }}</td>
            <td align="center">{{ $row->yil }}</td>
            <td>{{ $row->ilce ?? '—' }}</td>
            <td align="center">{{ number_format($row->toplam, 0, '', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" align="center">Kayıt bulunamadı.</td>
        </tr>
        @endforelse
    </tbody>
    @if($data->count() > 0)
    <tfoot>
        <tr>
            <td colspan="3" align="right"><strong>GENEL TOPLAM :</strong></td>
            <td align="center"><strong>{{ number_format($toplam, 0, '', '.') }}</strong></td>
        </tr>
    </tfoot>
    @endif
</table>
