<?php

namespace App\Http\Controllers;

use App\Models\KesinlesenFatura;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Tüketim toplamı için akıllı SQL ifadesi.
     * Önce fatura_edilecek_toplam_tuketim_kwh'e bakar; 0 veya NULL ise t1+t2+t3+ek toplamını kullanır.
     * 2017/2019 gibi eski aktarımlarda kwh alanı boş geldiği için bu fallback gereklidir.
     */
    private function tuketimExpr(): string
    {
        return 'CASE 
                    WHEN fatura_edilecek_toplam_tuketim_kwh IS NOT NULL 
                         AND fatura_edilecek_toplam_tuketim_kwh > 0 
                    THEN fatura_edilecek_toplam_tuketim_kwh 
                    ELSE (COALESCE(t1_tuketim,0) + COALESCE(t2_tuketim,0) + COALESCE(t3_tuketim,0) + COALESCE(ek_tuketim,0))
                END';
    }

    private function numericEndeksExpr(string $column): string
    {
        return "CAST(REPLACE(COALESCE({$column}, 0), ',', '.') AS DECIMAL(18,6))";
    }

    private function endeksAnomalyPriorityExpr(): string
    {
        $t1Ilk = $this->numericEndeksExpr('t1_ilk_endeks');
        $t1Son = $this->numericEndeksExpr('t1_son_endeks');
        $t2Ilk = $this->numericEndeksExpr('t2_ilk_endeks');
        $t2Son = $this->numericEndeksExpr('t2_son_endeks');
        $t3Ilk = $this->numericEndeksExpr('t3_ilk_endeks');
        $t3Son = $this->numericEndeksExpr('t3_son_endeks');
        $t0IlkRaw = $this->numericEndeksExpr('t0_ilk_endeks');
        $t0SonRaw = $this->numericEndeksExpr('t0_son_endeks');

        $tariffIlkTotal = "({$t1Ilk} + {$t2Ilk} + {$t3Ilk})";
        $tariffSonTotal = "({$t1Son} + {$t2Son} + {$t3Son})";
        $t0Ilk = "(CASE WHEN {$tariffIlkTotal} > 0 THEN {$tariffIlkTotal} ELSE {$t0IlkRaw} END)";
        $t0Son = "(CASE WHEN {$tariffIlkTotal} > 0 THEN {$tariffSonTotal} ELSE {$t0SonRaw} END)";
        $t0Fark = "({$t0Son} - {$t0Ilk})";
        $t0Gelen = '(COALESCE(t1_tuketim,0) + COALESCE(t2_tuketim,0) + COALESCE(t3_tuketim,0))';
        $carpan = 'COALESCE(NULLIF(carpan,0), 1)';
        $t0Gercek = "({$t0Fark} * {$carpan})";

        return "CASE
                    WHEN {$t0Fark} < 0 THEN 0
                    WHEN {$t0Fark} = 0 OR {$t0Gelen} <= 0 THEN 1
                    WHEN ABS({$t0Gelen} - {$t0Gercek}) > 10 AND {$t0Fark} <> 0 THEN 2
                    ELSE 9
                END";
    }

    public function yearly(Request $request)
    {
        $results = collect();

        if ($request->filled('start_year') || $request->filled('end_year') || $request->filled('bolge') || $request->filled('tesisat_no') || $request->filled('yerlesim_tipi') || $request->filled('baglanti_grubu') || $request->filled('tarife')) {
            $query = KesinlesenFatura::where('odeme_durumu', 'odendi');

            if ($request->filled('bolge')) {
                $query->whereIn('ilce', (array) $request->bolge);
            }
            if ($request->filled('tesisat_no')) {
                $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
            }
            if ($request->filled('start_year')) {
                $query->whereRaw('SUBSTRING(donem, 1, 4) >= ?', [$request->start_year]);
            }
            if ($request->filled('end_year')) {
                $query->whereRaw('SUBSTRING(donem, 1, 4) <= ?', [$request->end_year]);
            }
            if ($request->filled('yerlesim_tipi')) {
                $typeMap = ['koy' => 'KÖY', 'merkez' => 'MERKEZ'];
                $type = $typeMap[$request->yerlesim_tipi] ?? null;
                if ($type) {
                    $query->whereIn('tesisat_no', function ($q) use ($type) {
                        $q->select('ABONE_TESIS_NO')->from('aboneler')->where('yerlesim_turu', $type);
                    });
                }
            }
            if ($request->filled('baglanti_grubu')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->where('baglanti_grubu', $request->baglanti_grubu);
                });
            }
            if ($request->filled('tarife')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->whereIn('tarife', (array) $request->tarife);
                });
            }

            $tuketimExpr = $this->tuketimExpr();

            $selectRaw = "ilce as bolge,
                          SUBSTRING(donem, 1, 4) as yil,
                          COUNT(*) as fatura_sayisi,
                          SUM({$tuketimExpr}) as toplam_tuketim, 
                          SUM(COALESCE(tutar_toplam, fatura_tutari, 0)) as toplam_tutar";

            $groupBy = ['ilce', 'yil'];

            $results = $query->selectRaw($selectRaw)
                ->groupBy($groupBy)
                ->orderBy('yil', 'desc')
                ->orderBy('bolge', 'asc')
                ->get();

            if ($request->ajax()) {
                return view('reports.partials.yearly_table', compact('results'))->render();
            }
        }

        $yillar = KesinlesenFatura::where('odeme_durumu', 'odendi')->selectRaw('SUBSTRING(donem, 1, 4) as yil')->distinct()->orderBy('yil', 'desc')->pluck('yil');
        $bolgeler = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->whereNotNull('ilce')
            ->where('ilce', '!=', '')
            ->where('ilce', 'not like', '=%')
            ->where('ilce', 'not like', '#%')
            ->whereNotIn('ilce', ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])
            ->distinct()
            ->orderBy('ilce', 'asc')
            ->pluck('ilce');
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        if ($request->filled('export') && $results->count() > 0) {
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            if ($request->export === 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReportExport($results, $request->all(), 'yearly'), 'Yillik_Rapor.xlsx');
            } elseif ($request->export === 'pdf') {
                $filters = $request->only(['bolge', 'start_year', 'end_year', 'tarife']);
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
                    'results' => $results,
                    'type' => 'yearly',
                    'filters' => $filters,
                ])->setPaper('a4', 'landscape');

                return $pdf->download('Yillik_Rapor_'.now()->format('Ymd_His').'.pdf');
            }
        }

        return view('reports.yearly', compact('results', 'yillar', 'bolgeler', 'tarifeler'));
    }

    public function periodical(Request $request)
    {
        $results = collect();
        $totals = null;

        $hasFilter = $request->filled('donem')
                  || $request->filled('bolge')
                  || $request->filled('tesisat_no')
                  || $request->filled('start_period')
                  || $request->filled('end_period')
                  || $request->filled('yerlesim_tipi')
                  || $request->filled('baglanti_grubu')
                  || $request->filled('tarife');

        if ($hasFilter) {
            $query = KesinlesenFatura::where('odeme_durumu', 'odendi');

            if ($request->filled('bolge')) {
                $query->whereIn('ilce', (array) $request->bolge);
            }
            if ($request->filled('tesisat_no')) {
                $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
            }
            if ($request->filled('start_period')) {
                if ($request->filled('end_period')) {
                    $query->where('donem', '>=', $request->start_period)
                        ->where('donem', '<=', $request->end_period);
                } else {
                    $query->where('donem', '=', $request->start_period);
                }
            } elseif ($request->filled('end_period')) {
                $query->where('donem', '<=', $request->end_period);
            }
            if ($request->filled('yerlesim_tipi')) {
                $typeMap = ['koy' => 'KÖY', 'merkez' => 'MERKEZ'];
                $type = $typeMap[$request->yerlesim_tipi] ?? null;
                if ($type) {
                    $query->whereIn('tesisat_no', function ($q) use ($type) {
                        $q->select('ABONE_TESIS_NO')->from('aboneler')->where('yerlesim_turu', $type);
                    });
                }
            }
            if ($request->filled('baglanti_grubu')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->where('baglanti_grubu', $request->baglanti_grubu);
                });
            }
            if ($request->filled('tarife')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->whereIn('tarife', (array) $request->tarife);
                });
            }

            $tuketimExpr = $this->tuketimExpr();

            // Grand totals across all pages (before groupBy)
            $totals = (clone $query)->selectRaw(
                "COUNT(*) as total_fatura,
                 SUM({$tuketimExpr}) as total_tuketim,
                 SUM(COALESCE(tutar_toplam, fatura_tutari, 0)) as total_tutar"
            )->first();

            $selectRaw = "donem, ilce,
                          COUNT(*) as fatura_sayisi,
                          SUM({$tuketimExpr}) as toplam_tuketim, 
                          SUM(COALESCE(tutar_toplam, fatura_tutari, 0)) as toplam_tutar";

            $groupBy = ['donem', 'ilce'];

            // Exports: all rows at once
            if ($request->filled('export') && $totals && $totals->total_fatura > 0) {
                set_time_limit(0);
                ini_set('memory_limit', '-1');
                $allResults = $query->selectRaw($selectRaw)
                    ->groupBy($groupBy)
                    ->orderBy('donem', 'desc')
                    ->orderBy('ilce', 'asc')
                    ->get();

                if ($request->export === 'excel') {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\ReportExport($allResults, $request->all(), 'periodical'),
                        'Donem_Rapor.xlsx'
                    );
                } elseif ($request->export === 'pdf') {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
                        'results' => $allResults,
                        'type' => 'periodical',
                        'filters' => $request->all(),
                    ]);

                    return $pdf->download('Donem_Rapor.pdf');
                }
            }

            $results = $query->selectRaw($selectRaw)
                ->groupBy($groupBy)
                ->orderBy('donem', 'desc')
                ->orderBy('ilce', 'asc')
                ->paginate(20)
                ->appends($request->all());

            if ($request->ajax()) {
                return view('reports.partials.periodical_table', compact('results', 'totals'))->render();
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->whereNotNull('ilce')
            ->where('ilce', '!=', '')
            ->where('ilce', 'not like', '=%')
            ->where('ilce', 'not like', '#%')
            ->whereNotIn('ilce', ['\u015eANLIURFA', '\u015eANLIURFA \u00d6ZEL'])
            ->distinct()
            ->orderBy('ilce', 'asc')
            ->pluck('ilce');
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        return view('reports.periodical', compact('results', 'donemler', 'bolgeler', 'tarifeler', 'totals'));
    }

    public function detailed(Request $request)
    {
        $results = collect();
        $totalKWH = 0;
        $totalAmount = 0;

        $hasFilter = $request->anyFilled(['bolge', 'tesisat_no', 'start_period', 'end_period', 'yerlesim_tipi', 'baglanti_grubu', 'tarife']);

        if ($hasFilter) {
            $query = KesinlesenFatura::where('odeme_durumu', 'odendi')->select('kesinlesen_faturalar.*');

            if ($request->filled('start_period')) {
                if ($request->filled('end_period')) {
                    $query->where('donem', '>=', $request->start_period)
                        ->where('donem', '<=', $request->end_period);
                } else {
                    $query->where('donem', '=', $request->start_period);
                }
            } elseif ($request->filled('end_period')) {
                $query->where('donem', '<=', $request->end_period);
            }
            if ($request->filled('bolge')) {
                $query->whereIn('ilce', (array) $request->bolge);
            }
            if ($request->filled('tesisat_no')) {
                $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
            }
            if ($request->filled('yerlesim_tipi')) {
                $typeMap = ['koy' => 'KÖY', 'merkez' => 'MERKEZ'];
                $type = $typeMap[$request->yerlesim_tipi] ?? null;

                if ($type) {
                    $query->whereIn('kesinlesen_faturalar.tesisat_no', function ($q) use ($type) {
                        $q->select('ABONE_TESIS_NO')
                            ->from('aboneler')
                            ->where('yerlesim_turu', $type);
                    });
                }
            }
            if ($request->filled('baglanti_grubu')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->where('baglanti_grubu', $request->baglanti_grubu);
                });
            }
            if ($request->filled('tarife')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->whereIn('tarife', (array) $request->tarife);
                });
            }

            $totalKWH = $query->sum(\DB::raw($this->tuketimExpr()));
            $totalAmount = $query->sum('tutar_toplam');

            if ($request->filled('export')) {
                $results = $query->orderBy('tutar_toplam', 'desc')->get();
                $filters = $request->only(['bolge', 'tesisat_no', 'start_period', 'end_period', 'tarife']);

                if ($request->export === 'excel') {
                    set_time_limit(600);
                    ini_set('memory_limit', '-1');

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\DetailedReportExport($results, $totalKWH, $totalAmount, $filters),
                        'Detayli_Rapor_'.now()->format('Ymd_His').'.xlsx'
                    );
                } elseif ($request->export === 'pdf') {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                        'reports.detailed-pdf',
                        ['results' => $results, 'type' => 'detailed', 'filters' => $filters]
                    )
                        ->setPaper('a4', 'landscape');

                    return $pdf->download('Detayli_Rapor_'.now()->format('Ymd_His').'.pdf');
                }
            }

            $results = $query->orderBy('tutar_toplam', 'desc')->paginate(10)->appends($request->all());

            if ($request->ajax()) {
                return view('reports.partials.detailed_table', compact('results', 'totalKWH', 'totalAmount'))->render();
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->whereNotNull('ilce')->where('ilce', '!=', '')->where('ilce', 'not like', '=%')->where('ilce', 'not like', '#%')
            ->whereNotIn('ilce', ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])
            ->distinct()->orderBy('ilce', 'asc')->pluck('ilce');
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        return view('reports.detailed', compact('results', 'donemler', 'bolgeler', 'totalKWH', 'totalAmount', 'tarifeler'));
    }

    public function endeks(Request $request)
    {
        $results = collect();
        $totalKWH = 0;
        $totalAmount = 0;

        $hasFilter = $request->anyFilled(['bolge', 'start_period', 'end_period', 'yerlesim_tipi', 'baglanti_grubu', 'tarife', 'tesisat_no']);

        if ($hasFilter) {
            $query = KesinlesenFatura::where('odeme_durumu', 'odendi');

            if ($request->filled('start_period')) {
                if ($request->filled('end_period')) {
                    $query->where('donem', '>=', $request->start_period)
                        ->where('donem', '<=', $request->end_period);
                } else {
                    $query->where('donem', '=', $request->start_period);
                }
            } elseif ($request->filled('end_period')) {
                $query->where('donem', '<=', $request->end_period);
            }
            if ($request->filled('bolge')) {
                $query->whereIn('ilce', (array) $request->bolge);
            }

            if ($request->filled('yerlesim_tipi')) {
                $typeMap = ['koy' => 'KÖY', 'merkez' => 'MERKEZ'];
                $type = $typeMap[$request->yerlesim_tipi] ?? null;
                if ($type) {
                    $query->whereIn('tesisat_no', function ($q) use ($type) {
                        $q->select('ABONE_TESIS_NO')->from('aboneler')->where('yerlesim_turu', $type);
                    });
                }
            }

            if ($request->filled('baglanti_grubu')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->where('baglanti_grubu', $request->baglanti_grubu);
                });
            }
            if ($request->filled('tarife')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->whereIn('tarife', (array) $request->tarife);
                });
            }
            if ($request->filled('tesisat_no')) {
                $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
            }

            $totalKWH = (clone $query)->sum(\DB::raw($this->tuketimExpr()));
            $totalAmount = (clone $query)->sum('tutar_toplam');
            $results = $query
                ->orderByRaw($this->endeksAnomalyPriorityExpr().' ASC')
                ->orderBy('donem', 'desc')
                ->orderBy('ilce')
                ->paginate(100);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('reports.partials.endeks_table', compact('results', 'totalKWH', 'totalAmount'))->render(),
                    'row_ids' => collect($results->items())->pluck('id')->values(),
                ]);
            }

            if ($request->filled('export') && $results->count() > 0) {
                $filters = $request->only(['bolge', 'start_period', 'end_period', 'tarife']);
                if ($request->export === 'excel') {
                    set_time_limit(600);
                    ini_set('memory_limit', '-1');

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\EndeksReportExport($results, $totalKWH, $totalAmount, $filters),
                        'Endeks_Raporu_'.now()->format('Ymd_His').'.xlsx'
                    );
                } elseif ($request->export === 'pdf') {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
                        'results' => $results,
                        'type' => 'endeks',
                        'filters' => $filters,
                    ])
                        ->setPaper('a4', 'landscape');

                    return $pdf->download('Endeks_Raporu_'.now()->format('Ymd_His').'.pdf');
                }
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->whereNotNull('ilce')->where('ilce', '!=', '')->where('ilce', 'not like', '=%')->where('ilce', 'not like', '#%')
            ->whereNotIn('ilce', ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])
            ->distinct()->orderBy('ilce', 'asc')->pluck('ilce');
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        return view('reports.endeks', compact('results', 'donemler', 'bolgeler', 'tarifeler', 'totalKWH', 'totalAmount'));
    }

    public function koyMerkez(Request $request)
    {
        $results = collect();
        $totalKoyTuketim = 0;
        $totalKoyTutar = 0;
        $totalMerkezTuketim = 0;
        $totalMerkezTutar = 0;

        $hasFilter = $request->anyFilled(['bolge', 'start_period', 'end_period', 'baglanti_grubu', 'tarife']);

        if ($hasFilter) {
            $query = KesinlesenFatura::where('odeme_durumu', 'odendi');

            if ($request->filled('start_period')) {
                if ($request->filled('end_period')) {
                    $query->where('donem', '>=', $request->start_period)
                        ->where('donem', '<=', $request->end_period);
                } else {
                    $query->where('donem', '=', $request->start_period);
                }
            } elseif ($request->filled('end_period')) {
                $query->where('donem', '<=', $request->end_period);
            }
            if ($request->filled('bolge')) {
                $query->whereIn('ilce', (array) $request->bolge);
            }

            if ($request->filled('baglanti_grubu')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->where('baglanti_grubu', $request->baglanti_grubu);
                });
            }
            if ($request->filled('tarife')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->whereIn('tarife', (array) $request->tarife);
                });
            }
            if ($request->filled('tesisat_no')) {
                $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
            }

            $tuketimExpr = $this->tuketimExpr();
            $tutarExpr = 'COALESCE(tutar_toplam, fatura_tutari, 0)';

            $isKoyCondition = "kesinlesen_faturalar.tesisat_no IN (
                SELECT ABONE_TESIS_NO FROM aboneler WHERE yerlesim_turu = 'KÖY'
            )";

            $selectRaw = "
                donem,
                ilce as bolge,
                SUM(CASE WHEN {$isKoyCondition} THEN {$tuketimExpr} ELSE 0 END) as koy_tuketim,
                SUM(CASE WHEN {$isKoyCondition} THEN {$tutarExpr} ELSE 0 END) as koy_tutar,
                SUM(CASE WHEN NOT {$isKoyCondition} THEN {$tuketimExpr} ELSE 0 END) as merkez_tuketim,
                SUM(CASE WHEN NOT {$isKoyCondition} THEN {$tutarExpr} ELSE 0 END) as merkez_tutar
            ";

            $results = $query->selectRaw($selectRaw)
                ->groupBy('donem', 'ilce')
                ->orderBy('donem', 'desc')
                ->orderBy('ilce', 'asc')
                ->get();

            $totalKoyTuketim = $results->sum('koy_tuketim');
            $totalKoyTutar = $results->sum('koy_tutar');
            $totalMerkezTuketim = $results->sum('merkez_tuketim');
            $totalMerkezTutar = $results->sum('merkez_tutar');

            if ($request->ajax()) {
                return view('reports.partials.koy_merkez_table', compact('results'))->render();
            }

            if ($request->filled('export') && $results->count() > 0) {
                $filters = $request->only(['bolge', 'start_period', 'end_period', 'tarife']);
                $totals = [
                    'koy_tuketim' => $totalKoyTuketim,
                    'koy_tutar' => $totalKoyTutar,
                    'merkez_tuketim' => $totalMerkezTuketim,
                    'merkez_tutar' => $totalMerkezTutar,
                ];

                if ($request->export === 'excel') {
                    set_time_limit(600);
                    ini_set('memory_limit', '-1');

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\KoyMerkezExport($results, $totals, $filters),
                        'KoyMerkez_Ozet_Raporu_'.now()->format('Ymd_His').'.xlsx'
                    );
                } elseif ($request->export === 'pdf') {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
                        'results' => $results,
                        'type' => 'koy_merkez',
                        'filters' => $filters,
                    ])
                        ->setPaper('a4', 'landscape');

                    return $pdf->download('KoyMerkez_Ozet_Raporu_'.now()->format('Ymd_His').'.pdf');
                }
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->whereNotNull('ilce')->where('ilce', '!=', '')->where('ilce', 'not like', '=%')->where('ilce', 'not like', '#%')
            ->whereNotIn('ilce', ['ŞANLIURFA', 'ŞANLIURFA ÖZEL'])
            ->distinct()->orderBy('ilce', 'asc')->pluck('ilce');
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        return view('reports.koy-merkez', compact('results', 'donemler', 'bolgeler', 'tarifeler', 'totalKoyTuketim', 'totalKoyTutar', 'totalMerkezTuketim', 'totalMerkezTutar'));
    }

    public function gecmis6Ay($tesisat_no, Request $request)
    {
        $query = KesinlesenFatura::where('tesisat_no', $tesisat_no)
            ->where('odeme_durumu', 'odendi');

        if ($request->filled('donem')) {
            $query->where('donem', '<=', $request->donem);
        }

        $records = $query->orderBy('donem', 'desc')
            ->limit(6)
            ->get();

        $formatted = $records->map(function ($row) {
            $carpan = (float)($row->carpan ?: 1);

            $t1Ilk = (float)str_replace(',', '.', $row->t1_ilk_endeks ?? 0);
            $t1Son = (float)str_replace(',', '.', $row->t1_son_endeks ?? 0);
            $t2Ilk = (float)str_replace(',', '.', $row->t2_ilk_endeks ?? 0);
            $t2Son = (float)str_replace(',', '.', $row->t2_son_endeks ?? 0);
            $t3Ilk = (float)str_replace(',', '.', $row->t3_ilk_endeks ?? 0);
            $t3Son = (float)str_replace(',', '.', $row->t3_son_endeks ?? 0);

            $t1Fark = $t1Son - $t1Ilk;
            $t2Fark = $t2Son - $t2Ilk;
            $t3Fark = $t3Son - $t3Ilk;

            $hasTariff = ($t1Ilk + $t2Ilk + $t3Ilk) > 0;
            $t0Ilk = $hasTariff ? ($t1Ilk + $t2Ilk + $t3Ilk) : (float)str_replace(',', '.', $row->t0_ilk_endeks ?? 0);
            $t0Son = $hasTariff ? ($t1Son + $t2Son + $t3Son) : (float)str_replace(',', '.', $row->t0_son_endeks ?? 0);
            $t0Fark = $t0Son - $t0Ilk;

            $t1Tuketim = (float)($row->t1_tuketim ?? 0);
            $t2Tuketim = (float)($row->t2_tuketim ?? 0);
            $t3Tuketim = (float)($row->t3_tuketim ?? 0);
            
            $t0Tuketim = $hasTariff ? ($t1Tuketim + $t2Tuketim + $t3Tuketim) : (float)($row->fatura_edilecek_toplam_tuketim_kwh ?? 0);

            $riIlk = (float)str_replace(',', '.', $row->ri_ilk_endeks ?? 0);
            $riSon = (float)str_replace(',', '.', $row->ri_son_endeks ?? 0);
            $riFark = $row->ri_fark_endeks !== null ? (float)str_replace(',', '.', $row->ri_fark_endeks) : ($riSon - $riIlk);

            $rcIlk = (float)str_replace(',', '.', $row->rc_ilk_endeks ?? 0);
            $rcSon = (float)str_replace(',', '.', $row->rc_son_endeks ?? 0);
            $rcFark = $row->rc_fark_endeks !== null ? (float)str_replace(',', '.', $row->rc_fark_endeks) : ($rcSon - $rcIlk);

            return [
                'donem' => $row->donem,
                'carpan' => $carpan,
                'tutar' => (float)($row->tutar_toplam ?? 0),
                't0' => ['ilk' => $t0Ilk, 'son' => $t0Son, 'fark' => $t0Fark, 'tuketim' => $t0Tuketim],
                't1' => ['ilk' => $t1Ilk, 'son' => $t1Son, 'fark' => $t1Fark, 'tuketim' => $t1Tuketim],
                't2' => ['ilk' => $t2Ilk, 'son' => $t2Son, 'fark' => $t2Fark, 'tuketim' => $t2Tuketim],
                't3' => ['ilk' => $t3Ilk, 'son' => $t3Son, 'fark' => $t3Fark, 'tuketim' => $t3Tuketim],
                'ri' => ['ilk' => $riIlk, 'son' => $riSon, 'fark' => $riFark, 'tuketim' => null],
                'rc' => ['ilk' => $rcIlk, 'son' => $rcSon, 'fark' => $rcFark, 'tuketim' => null],
            ];
        });

        return response()->json([
            'success' => true,
            'tesisat_no' => $tesisat_no,
            'records' => $formatted,
        ]);
    }
}
