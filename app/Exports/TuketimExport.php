<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class TuketimExport implements FromView, WithTitle, WithColumnWidths, WithEvents
{
    protected $pivotData;
    protected $pivotPeriods;
    protected $totalKWH;
    protected $veri;

    public function __construct($pivotData, $pivotPeriods, float $totalKWH, $veri = 'tuketim')
    {
        $this->pivotData = $pivotData;
        $this->pivotPeriods = $pivotPeriods;
        $this->totalKWH = $totalKWH;
        $this->veri = $veri;
    }

    public function view(): View
    {
        return view('reports.tuketim-excel', [
            'pivotData' => $this->pivotData,
            'pivotPeriods' => $this->pivotPeriods,
            'totalKWH' => $this->totalKWH,
            'veri' => $this->veri,
        ]);
    }

    public function title(): string
    {
        return 'Tüketim Raporu';
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 10,  // Sıra No (#)
            'B' => 20,  // Tesisat No
        ];

        $colIndex = 3;
        foreach ($this->pivotPeriods as $period) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $widths[$colLetter] = 16;
            $colIndex++;
        }

        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = count($this->pivotData);
                $lastData = $rowCount + 3; // 1: Title, 2: Filters, 3: Headers, 4+: Data
                $totalRow = $lastData + 1;

                $totalCols = 2 + count($this->pivotPeriods);
                $lastColLetter = Coordinate::stringFromColumnIndex($totalCols);

                // Set default row height for data rows
                $sheet->getDefaultRowDimension()->setRowHeight(20);

                // ── 1. BAŞLIK (A1:lastCol1) ─────────────────────────────────────────
                $sheet->mergeCells("A1:{$lastColLetter}1");
                $sheet->getRowDimension(1)->setRowHeight(48);
                $sheet->getStyle("A1")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // ── 2. FİLTRE BİLGİSİ (A2:lastCol2) ─────────────────────────────────
                $sheet->mergeCells("A2:{$lastColLetter}2");
                $sheet->getRowDimension(2)->setRowHeight(24);
                $sheet->getStyle("A2")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E293B'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => [
                        'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                        'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                        'right'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
                    ],
                ]);

                // ── 3. SÜTUN BAŞLIKLARI (A3:lastCol3) ──────────────────────────────
                $sheet->getRowDimension(3)->setRowHeight(32);
                $sheet->getStyle("A3:{$lastColLetter}3")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1D4ED8']]],
                ]);

                if ($rowCount > 0) {
                    // ── 4. VERİ SATIRLARI TOPLU STİL (A4:lastColLetter.lastData) ────
                    $sheet->getStyle("A4:{$lastColLetter}{$lastData}")->applyFromArray([
                        'font'      => ['size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => '1E293B']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Sıra No ortala
                    $sheet->getStyle("A4:A{$lastData}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Tesisat No ortala, kalın ve mavi yap
                    $sheet->getStyle("B4:B{$lastData}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '2563EB']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);

                    // Dönem verilerini sağa yasla ve biçimlendir
                    $sheet->getStyle("C4:{$lastColLetter}{$lastData}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);

                    $sheet->getStyle("C4:{$lastColLetter}{$lastData}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                }

                // ── 5. TOPLAM SATIRI ──────────────────────────────────────────
                $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
                $sheet->getRowDimension($totalRow)->setRowHeight(28);
                $sheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '065F46']]],
                ]);

                if ($totalCols >= 3) {
                    $firstValCol = 'C';
                    $sheet->getStyle("{$firstValCol}{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $sheet->getStyle("{$firstValCol}{$totalRow}:{$lastColLetter}{$totalRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                }
            },
        ];
    }
}
