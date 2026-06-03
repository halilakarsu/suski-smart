<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DetailedReportExport implements FromView, WithTitle, WithColumnWidths, WithEvents
{
    protected Collection $results;
    protected float $totalKWH;
    protected float $totalAmount;
    protected array $filters;

    public function __construct(Collection $results, float $totalKWH, float $totalAmount, array $filters)
    {
        $this->results     = $results;
        $this->totalKWH    = $totalKWH;
        $this->totalAmount = $totalAmount;
        $this->filters     = $filters;
    }

    public function view(): View
    {
        return view('reports.detailed-excel', [
            'results'     => $this->results,
            'totalKWH'    => $this->totalKWH,
            'totalAmount' => $this->totalAmount,
            'filters'     => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Detaylı Rapor';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // DÖNEM
            'B' => 16,  // TESİSAT NO
            'C' => 14,  // İLK OKUMA
            'D' => 14,  // SON OKUMA
            'E' => 14,  // İLK ENDEKS
            'F' => 14,  // SON ENDEKS
            'G' => 10,  // ÇARPAN
            'H' => 16,  // TÜKETİM
            'I' => 18,  // TUTAR
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $rowCount = $this->results->count();
                $lastData = $rowCount + 4; // 1: Title, 2: Filter 1, 3: Filter 2, 4: Headers, 5+: Data
                $totalRow = $lastData + 1;

                // ── 1. BAŞLIK (A1:I1) ─────────────────────────────────────────
                $sheet->mergeCells('A1:I1');
                $sheet->getRowDimension(1)->setRowHeight(48);
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // ── 2. FİLTRE BİLGİSİ 1 (A2:I2) ─────────────────────────────────
                $sheet->mergeCells('A2:I2');
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E293B'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => [
                        'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                        'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                        'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                    ],
                ]);

                // ── 3. FİLTRE BİLGİSİ 2 (A3:I3) ─────────────────────────────────
                $sheet->mergeCells('A3:I3');
                $sheet->getRowDimension(3)->setRowHeight(22);
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '475569'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                        'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                        'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                    ],
                ]);

                // ── 4. SÜTUN BAŞLIKLARI (A4:I4) ──────────────────────────────
                $sheet->getRowDimension(4)->setRowHeight(32);
                $sheet->getStyle('A4:I4')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1D4ED8']]],
                ]);

                // ── 5. VERİ SATIRLARI ─────────────────────────────────────────
                for ($row = 5; $row <= $lastData; $row++) {
                    $bg = ($row % 2 === 1) ? 'FFFFFF' : 'F1F5F9';
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'font'      => ['size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => '1E293B']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    ]);

                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB('2563EB');

                    $sheet->getStyle("C{$row}:D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("E{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("E{$row}:F{$row}")->getNumberFormat()->setFormatCode('#,##0');

                    $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('0.00');

                    $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                    $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("I{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB('059669');
                    $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                }

                // ── 6. TOPLAM SATIRI ──────────────────────────────────────────
                $sheet->mergeCells("A{$totalRow}:G{$totalRow}");
                $sheet->getRowDimension($totalRow)->setRowHeight(28);
                $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '065F46']]],
                ]);
                $sheet->getStyle("H{$totalRow}:I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("H{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("I{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
