<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tüketim Dönem Raporu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8pt; margin: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e5e7eb; padding: 4px 6px; font-size: 7pt; font-weight: 700; border: 1px solid #d1d5db; text-align: center; }
        td { padding: 3px 6px; font-size: 7pt; border: 1px solid #d1d5db; }
        .r { text-align: right; }
        .c { text-align: center; }
        .b { font-weight: 700; }
        .total-row { background: #f3f4f6; font-weight: 700; }
        h2 { font-size: 11pt; margin-bottom: 8px; }
    </style>
</head>
<body>
    <h2>Tüketim Dönem Raporu</h2>
    <p style="font-size:7pt;color:#6b7280;margin-bottom:10px;">
        Dönem: {{ request('start_period') }} - {{ request('end_period') }} | {{ count($pivotData) }} tesisat
    </p>
    <table>
        <thead>
            <tr>
                <th class="c" style="width:20px;">#</th>
                <th>Tesisat No</th>
                @foreach($pivotPeriods as $period)
                    <th class="r">{{ $period }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($pivotData as $tesisatNo => $donemler)
                <tr>
                    <td class="c">{{ $i++ }}</td>
                    <td class="b">{{ $tesisatNo }}</td>
                    @foreach($pivotPeriods as $period)
                        <td class="r">{{ isset($donemler[$period]) ? number_format($donemler[$period], 2, ',', '.') : '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="c">#</td>
                <td>GENEL TOPLAM</td>
                @foreach($pivotPeriods as $period)
                    <td class="r">{{ number_format(collect($pivotData)->sum(fn($d) => $d[$period] ?? 0), 2, ',', '.') }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</body>
</html>
