<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class OdenenFaturalarExport implements
    FromView,
    WithTitle,
    ShouldAutoSize,
    WithEvents
{
    protected Collection $faturalar;
    protected float $totalKWH;
    protected float $totalAmount;
    protected string $donem;

    public function __construct(Collection $faturalar, float $totalKWH, float $totalAmount, string $donem)
    {
        $this->faturalar   = $faturalar;
        $this->totalKWH    = $totalKWH;
        $this->totalAmount = $totalAmount;
        $this->donem       = $donem;
    }

    public function view(): View
    {
        return view('fatura.odenenler-excel', [
            'faturalar'   => $this->faturalar,
            'totalKWH'    => $this->totalKWH,
            'totalAmount' => $this->totalAmount,
            'donem'       => $this->donem,
        ]);
    }

    public function title(): string
    {
        return 'Ödenen Faturalar';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $rowCount = $this->faturalar->count();
                $lastData = $rowCount + 4; // row1-2=başlık, row3=stats, row4=headers, 5..N+4=data
                $totalRow = $lastData + 1;

                // ── YAZDIRMA AYARLARI ────────────────────────────────────────
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);

                // ── 1. ÜST BAŞLIK (ORTALI METİN) ─────────────────────────────
                $sheet->getRowDimension(1)->setRowHeight(15);
                $sheet->getRowDimension(2)->setRowHeight(15);
                $sheet->getStyle('A1:E2')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 9,
                        'color' => ['rgb' => '1a73e8'],
                        'name'  => 'DejaVu Sans',
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ── 2. İSTATİSTİK SATIRI (3. Satır) ───────────────────────────
                $sheet->getRowDimension(3)->setRowHeight(12);
                $sheet->getStyle('A3:B3')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 7,
                        'color' => ['rgb' => '495057'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getStyle('C3:E3')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 7,
                        'color' => ['rgb' => '495057'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ── 3. SÜTUN BAŞLIKLARI (A4:E4) ──────────────────────────────
                $sheet->getRowDimension(4)->setRowHeight(14);
                $sheet->getStyle('A4:E4')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 7,
                        'color' => ['rgb' => 'FFFFFF'],
                        'name'  => 'DejaVu Sans',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1a73e8'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '1a73e8'],
                        ],
                    ],
                ]);

                // ── 4. VERİ SATIRLARI ─────────────────────────────────────────
                for ($row = 5; $row <= $lastData; $row++) {
                    $bg = ($row % 2 === 1) ? 'f8f9fa' : 'FFFFFF';
                    $sheet->getRowDimension($row)->setRowHeight(10);

                    $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'font' => [
                            'size'  => 6,
                            'name'  => 'DejaVu Sans',
                            'color' => ['rgb' => '212529'],
                        ],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $bg],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => 'CBD5E1'],
                            ],
                        ],
                    ]);

                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                    $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00 "₺"');
                }

                // ── 5. GENEL TOPLAM SATIRI ────────────────────────────────────
                $sheet->getRowDimension($totalRow)->setRowHeight(13);
                $sheet->getStyle("A{$totalRow}:E{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 7,
                        'color' => ['rgb' => '1a73e8'],
                        'name'  => 'DejaVu Sans',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'eef4ff'],
                    ],
                    'alignment' => [
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '1a73e8'],
                        ],
                    ],
                ]);
                
                $sheet->getStyle("D{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("E{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00 "₺"');
                $sheet->getStyle("D{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("E{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
