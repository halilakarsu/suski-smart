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

class KoyMerkezExport implements FromView, WithTitle, WithColumnWidths, WithEvents
{
    protected Collection $results;
    protected array $totals;
    protected array $filters;

    public function __construct(Collection $results, array $totals, array $filters)
    {
        $this->results = $results;
        $this->totals  = $totals;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('reports.koy-merkez-excel', [
            'results' => $this->results,
            'totals'  => $this->totals,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Köy-Merkez Pivot';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // DÖNEM
            'B' => 20,  // İLÇE/BÖLGE
            'C' => 20,  // MERKEZ TÜKETİM
            'D' => 20,  // MERKEZ TUTAR
            'E' => 20,  // KÖY TÜKETİM
            'F' => 20,  // KÖY TUTAR
            'G' => 20,  // GENEL TÜKETİM
            'H' => 20,  // GENEL TUTAR
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $rowCount = $this->results->count();
                $lastData = $rowCount + 3;
                $totalRow = $lastData + 1;

                // ── 1. BAŞLIK (A1:H1)
                $sheet->mergeCells('A1:H1');
                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // ── 2. FİLTRELER (A2:H2)
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('E2:H2');
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getStyle('A2:H2')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '334155'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'CBD5E1']]],
                ]);
                $sheet->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // ── 3. SÜTUN BAŞLIKLARI (A3:H3)
                $sheet->getRowDimension(3)->setRowHeight(30);
                $sheet->getStyle('A3:H3')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '334155']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);

                // Merkez Başlıkları Arka Plan
                $sheet->getStyle('C3:D3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2563EB'); // Mavi
                // Köy Başlıkları Arka Plan
                $sheet->getStyle('E3:F3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('16A34A'); // Yeşil
                // Genel Başlıkları Arka Plan
                $sheet->getStyle('G3:H3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('B45309'); // Turuncu

                // ── 4. VERİ SATIRLARI
                for ($row = 4; $row <= $lastData; $row++) {
                    $bg = ($row % 2 === 1) ? 'FFFFFF' : 'F8FAFC';
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                        'font'      => ['size' => 11, 'name' => 'Calibri', 'color' => ['rgb' => '1E293B']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    ]);

                    $sheet->getStyle("A{$row}:B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("C{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                    // Renklendirme
                    $sheet->getStyle("D{$row}")->getFont()->getColor()->setRGB('2563EB');
                    $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('16A34A');
                    $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('B45309');
                    $sheet->getStyle("G{$row}:H{$row}")->getFont()->setBold(true);
                }

                // ── 4. TOPLAM SATIRI
                $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
                $sheet->getRowDimension($totalRow)->setRowHeight(28);
                $sheet->getStyle("A{$totalRow}:H{$totalRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getStyle("C{$totalRow}:H{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("C{$totalRow}:H{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
