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

class EndeksReportExport implements FromView, WithTitle, WithColumnWidths, WithEvents
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
        return view('reports.endeks-excel', [
            'results'     => $this->results,
            'totalKWH'    => $this->totalKWH,
            'totalAmount' => $this->totalAmount,
            'filters'     => $this->filters,
        ]);
    }

    public function title(): string { return 'Rapor'; }

    public function columnWidths(): array
    {
        $hasBolge = !empty($this->filters['bolge']);
        
        if ($hasBolge) {
            return [
                'A' => 10,  // SIRA NO
                'B' => 15,  // DÖNEM
                'C' => 18,  // TESİSAT NO
                'D' => 20,  // BÖLGE
                'E' => 16,  // İLK ENDEKS
                'F' => 16,  // SON ENDEKS
                'G' => 16,  // FARK
                'H' => 18,  // TÜKETİM
                'I' => 18,  // TUTAR
                'J' => 12,  // DURUM
            ];
        } else {
            return [
                'A' => 10,  // SIRA NO
                'B' => 15,  // DÖNEM
                'C' => 18,  // TESİSAT NO
                'D' => 16,  // İLK ENDEKS
                'E' => 16,  // SON ENDEKS
                'F' => 16,  // FARK
                'G' => 18,  // TÜKETİM
                'H' => 18,  // TUTAR
                'I' => 12,  // DURUM
            ];
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $rowCount = $this->results->count();
                $lastData = $rowCount + 2;
                $totalRow = $lastData + 1;
                $hasBolge = !empty($this->filters['bolge']);
                $lastCol  = $hasBolge ? 'J' : 'I';

                // ── 1. BAŞLIK ─────────────────────────────────────────
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getRowDimension(1)->setRowHeight(48);
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // ── 2. SÜTUN BAŞLIKLARI ──────────────────────────────
                $sheet->getRowDimension(2)->setRowHeight(32);
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1D4ED8']]],
                ]);

                // ── 3. VERİ SATIRLARI ─────────────────────────────────────────
                for ($row = 3; $row <= $lastData; $row++) {
                    $bg = ($row % 2 === 1) ? 'FFFFFF' : 'F1F5F9';
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'font'      => ['size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => '1E293B']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    ]);

                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // SIRA NO
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // DÖNEM
                    
                    $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // TESİSAT NO
                    $sheet->getStyle("C{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("C{$row}")->getFont()->getColor()->setRGB('2563EB');

                    $idx = 'D';
                    if ($hasBolge) {
                        $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // BÖLGE
                        $idx = 'E';
                    }

                    // İLK ENDEKS, SON ENDEKS, FARK
                    for ($i = 0; $i < 3; $i++) {
                        $col = chr(ord($idx) + $i);
                        $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
                    }

                    // TÜKETİM
                    $tuketimCol = chr(ord($idx) + 3);
                    $sheet->getStyle("{$tuketimCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("{$tuketimCol}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                    // TUTAR
                    $tutarCol = chr(ord($idx) + 4);
                    $sheet->getStyle("{$tutarCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("{$tutarCol}{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("{$tutarCol}{$row}")->getFont()->getColor()->setRGB('059669');
                    $sheet->getStyle("{$tutarCol}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                    // DURUM
                    $durumCol = chr(ord($idx) + 5);
                    $sheet->getStyle("{$durumCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── 4. TOPLAM SATIRI ──────────────────────────────────────────
                $mergeCol = $hasBolge ? 'G' : 'F';
                $sheet->mergeCells("A{$totalRow}:{$mergeCol}{$totalRow}");
                $sheet->getRowDimension($totalRow)->setRowHeight(28);
                $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Calibri'],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '065F46']]],
                ]);
                
                $tuketimCol = $hasBolge ? 'H' : 'G';
                $tutarCol   = $hasBolge ? 'I' : 'H';

                $sheet->getStyle("{$tuketimCol}{$totalRow}:{$tutarCol}{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("{$tuketimCol}{$totalRow}:{$tutarCol}{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
