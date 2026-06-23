<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ($veri ?? 'tuketim') === 'tutar' ? 'Tutar Bazlı Dönem Raporu' : 'Tüketim Dönem Raporu' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8pt; margin: 10px; }
        table { width: 100%; border-collapse: collapse; }
        .title-row td { background: #1e293b; color: #ffffff; text-align: center; font-weight: bold; font-size: 12pt; height: 38px; border: 1px solid #1e293b; }
        .filter-row td { background: #e2e8f0; color: #1e293b; text-align: center; font-weight: bold; font-size: 9pt; height: 22px; border: 1px solid #cbd5e1; }
        th { background: #2563eb; color: #ffffff; padding: 6px 8px; font-size: 8pt; font-weight: 700; border: 1px solid #cbd5e1; text-align: center; }
        td { padding: 5px 8px; font-size: 8pt; border: 1px solid #cbd5e1; }
        .r { text-align: right; }
        .c { text-align: center; }
        .b { font-weight: 700; }
        .data-row-even td { background: #ffffff; }
        .data-row-odd td { background: #f8fafc; }
        .total-row td { background: #059669; color: #ffffff; font-weight: 700; border: 1px solid #065f46; }
    </style>
</head>
<body>
    <table>
        <tr class="title-row">
            <td colspan="{{ 2 + count($pivotPeriods) }}">
                {{ ($veri ?? 'tuketim') === 'tutar' ? 'Tutar Bazlı Dönem Raporu' : 'Tüketim Dönem Raporu' }}
            </td>
        </tr>
        <tr class="filter-row">
            <td colspan="{{ 2 + count($pivotPeriods) }}">
                DÖNEM: {{ request('start_period') }} - {{ request('end_period') }} | {{ count($pivotData) }} tesisat
            </td>
        </tr>
        <thead>
            <tr>
                <th class="c" style="width:30px;">#</th>
                <th>Tesisat No</th>
                @foreach($pivotPeriods as $period)
                    <th class="r">{{ $period }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($pivotData as $tesisatNo => $donemler)
                <tr class="{{ $i % 2 === 0 ? 'data-row-even' : 'data-row-odd' }}">
                    <td class="c">{{ $i++ }}</td>
                    <td class="b" style="color: #2563eb; text-align: center;">{{ $tesisatNo }}</td>
                    @foreach($pivotPeriods as $period)
                        <td class="r">{{ isset($donemler[$period]) ? number_format($donemler[$period], 2, ',', '.') : '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="c">#</td>
                <td style="color: #ffffff;">GENEL TOPLAM</td>
                @foreach($pivotPeriods as $period)
                    @php
                        $items = ($pivotData instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $pivotData->getCollection() : collect($pivotData);
                    @endphp
                    <td class="r">{{ number_format($items->sum(fn($d) => $d[$period] ?? 0), 2, ',', '.') }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</body>
</html>
