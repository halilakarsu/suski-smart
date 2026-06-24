<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EkTuketimExport implements FromView, WithColumnWidths, WithEvents, WithTitle
{
    protected Collection $results;

    protected array $totals;

    protected array $filters;

    protected const LAST_COL = 'I';

    protected const DATA_ROW_START = 5;

    public function __construct(Collection $results, array $totals, array $filters)
    {
        $this->results = $results;
        $this->totals = $totals;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('reports.ek-tuketim-excel', [
            'results' => $this->results,
            'totals' => $this->totals,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Ek Tuketim Raporu';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 28,
            'D' => 18,
            'E' => 18,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = $this->results->count();
                $dataEnd = self::DATA_ROW_START + $rowCount - 1;
                $totalRow = $dataEnd + 1;
                $last = self::LAST_COL;

                $this->styleTitle($sheet);
                $this->styleFilters($sheet);
                $this->styleHeaderGroupRow($sheet);
                $this->styleHeaderSubRow($sheet);
                $this->styleDataRows($sheet, $dataEnd);
                $this->styleTotalRow($sheet, $totalRow);

                $sheet->freezePane('D'.self::DATA_ROW_START);
                $sheet->setAutoFilter("A4:{$last}{$dataEnd}");
            },
        ];
    }

    protected function styleTitle($sheet): void
    {
        $last = self::LAST_COL;
        $sheet->mergeCells("A1:{$last}1");
        $sheet->getRowDimension(1)->setRowHeight(44);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->setCellValue('A1', 'Ek Tüketim Raporu');
    }

    protected function styleFilters($sheet): void
    {
        $last = self::LAST_COL;
        $sheet->mergeCells("A2:{$last}2");
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 9,
                'color' => ['rgb' => '475569'],
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'CBD5E1']],
            ],
        ]);

        $donem = empty($this->filters['start_period']) ? 'Tümü' : $this->filters['start_period'];
        if (! empty($this->filters['end_period'])) {
            $donem .= ' - '.$this->filters['end_period'];
        }

        $sheet->setCellValue('A2', "Dönem: {$donem}");
    }

    protected function styleHeaderGroupRow($sheet): void
    {
        $last = self::LAST_COL;
        $sheet->getRowDimension(3)->setRowHeight(30);

        $sheet->getStyle("A3:{$last}3")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E293B']],
            ],
        ]);

        $sheet->getStyle('A3:C3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1E293B');

        $sheet->getStyle('D3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2563EB');

        $sheet->getStyle('E3:G3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('7C3AED');

        $sheet->getStyle('H3:I3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D97706');
    }

    protected function styleHeaderSubRow($sheet): void
    {
        $last = self::LAST_COL;
        $sheet->getRowDimension(4)->setRowHeight(26);

        $sheet->getStyle("A4:{$last}4")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E293B']],
            ],
        ]);

        $sheet->getStyle('D3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2563EB');

        $sheet->getStyle('E3:G3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('7C3AED');

        $sheet->getStyle('H3:I3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D97706');
    }

    protected function styleDataRows($sheet, int $dataEnd): void
    {
        $last = self::LAST_COL;

        for ($row = self::DATA_ROW_START; $row <= $dataEnd; $row++) {
            $bg = ($row % 2 === 0) ? 'FFFFFF' : 'F8FAFC';
            $sheet->getRowDimension($row)->setRowHeight(18);
            $sheet->getStyle("A{$row}:{$last}{$row}")->applyFromArray([
                'font' => [
                    'size' => 10,
                    'name' => 'Calibri',
                    'color' => ['rgb' => '1E293B'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bg],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                ],
            ]);

            $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("D{$row}:{$last}{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("D{$row}:{$last}{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

            $sheet->getStyle("E{$row}")->getFont()->getColor()->setRGB('7C3AED');
            $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB('7C3AED');
            $sheet->getStyle("G{$row}")->getFont()->getColor()->setRGB('7C3AED');
            $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('059669');
            $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB('D97706');
            $sheet->getStyle("H{$row}:{$last}{$row}")->getFont()->setBold(true);
        }
    }

    protected function styleTotalRow($sheet, int $totalRow): void
    {
        $last = self::LAST_COL;
        $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
        $sheet->getRowDimension($totalRow)->setRowHeight(28);
        $sheet->getStyle("A{$totalRow}:{$last}{$totalRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2563EB']],
            ],
        ]);

        $sheet->getStyle("D{$totalRow}:{$last}{$totalRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle("D{$totalRow}:{$last}{$totalRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }
}
