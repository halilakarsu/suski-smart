<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportExport implements FromView, WithColumnWidths, WithEvents, WithTitle
{
    protected $data;

    protected $filters;

    protected $type;

    public function __construct($data, $filters, $type)
    {
        $this->data = $data;
        $this->filters = $filters;
        $this->type = $type;
    }

    public function view(): View
    {
        return view('reports.periodical-excel', [
            'data' => $this->data,
            'filters' => $this->filters,
            'type' => $this->type,
        ]);
    }

    public function title(): string
    {
        return 'Rapor';
    }

    public function columnWidths(): array
    {
        if ($this->type === 'yearly') {
            return [
                'A' => 10,  // SIRA NO
                'B' => 12,  // YIL
                'C' => 20,  // BÖLGE
                'D' => 10,  // FATURA
                'E' => 18,  // TÜKETİM
                'F' => 18,  // TUTAR
            ];
        }

        return [
            'A' => 10,  // SIRA NO
            'B' => 18,  // TESİSAT NO
            'C' => 20,  // BÖLGE
            'D' => 18,  // TÜKETİM
            'E' => 18,  // TUTAR
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = $this->data->count();
                $lastData = $rowCount + 2;
                $totalRow = $lastData + 1;
                $isYearly = $this->type === 'yearly';
                $lastCol = $isYearly ? 'F' : 'E';

                // ── 1. BAŞLIK (A1:lastCol1) ─────────────────────────────────────────
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getRowDimension(1)->setRowHeight(48);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // ── 2. SÜTUN BAŞLIKLARI (A2:lastCol2) ──────────────────────────────
                $sheet->getRowDimension(2)->setRowHeight(32);
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1D4ED8']]],
                ]);

                // ── 3. VERİ SATIRLARI ─────────────────────────────────────────
                for ($row = 3; $row <= $lastData; $row++) {
                    $bg = ($row % 2 === 1) ? 'FFFFFF' : 'F1F5F9';
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'font' => ['size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => '1E293B']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    ]);

                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // BÖLGE

                    if ($isYearly) {
                        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // YIL
                        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // FATURA
                        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00'); // TÜKETİM
                        $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("F{$row}")->getFont()->setBold(true);
                        $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('059669');
                        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0.00'); // TUTAR
                    } else {
                        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                        $sheet->getStyle("B{$row}")->getFont()->getColor()->setRGB('2563EB');

                        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.00'); // TÜKETİM
                        $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("E{$row}")->getFont()->setBold(true);
                        $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB('059669');
                        $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0.00'); // TUTAR
                    }
                }

                // ── 4. TOPLAM SATIRI ──────────────────────────────────────────
                $mergeCol = 'C';
                $sheet->mergeCells("A{$totalRow}:{$mergeCol}{$totalRow}");
                $sheet->getRowDimension($totalRow)->setRowHeight(28);
                $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '065F46']]],
                ]);

                if ($isYearly) {
                    $sheet->getStyle("E{$totalRow}:F{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("E{$totalRow}:F{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } else {
                    $sheet->getStyle("D{$totalRow}:E{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle("D{$totalRow}:E{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            },
        ];
    }
}
