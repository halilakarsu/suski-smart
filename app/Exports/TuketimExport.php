<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class TuketimExport implements FromView, WithTitle
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
}
