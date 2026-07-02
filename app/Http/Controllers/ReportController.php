<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\NormalizesIlce;
use App\Models\KesinlesenFatura;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ReportController extends Controller
{
    use NormalizesIlce;

    /**
     * Tüketim toplamı için akıllı SQL ifadesi.
     * Önce fatura_edilecek_toplam_tuketim_kwh'e bakar; 0 veya NULL ise t1+t2+t3+ek toplamını kullanır.
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

    private function numericTuketimExpr(string $column): string
    {
        return "CAST(REPLACE(COALESCE({$column}, '0'), ',', '.') AS DECIMAL(18,3))";
    }

    private function rawTotalExpr(): string
    {
        $t1 = $this->numericTuketimExpr('t1_tuketim');
        $t2 = $this->numericTuketimExpr('t2_tuketim');
        $t3 = $this->numericTuketimExpr('t3_tuketim');
        $ek = $this->numericTuketimExpr('ek_tuketim');

        return "({$t1} + {$t2} + {$t3} + {$ek})";
    }

    /**
     * SQL tabanlı anomali öncelik ifadesi.
     * 0 = negatif endeks, 1 = sıfır sayaç, 2 = tutarsız endeks, 9 = normal
     * Bu ifade WHERE/HAVING ile filtreleme için kullanılır.
     */
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
        // Tüketimi akıllı seç: önce fatura_edilecek_toplam_tuketim_kwh, yoksa t1+t2+t3
        $t0Gelen = '(CASE WHEN COALESCE(fatura_edilecek_toplam_tuketim_kwh, 0) > 0'
                 .' THEN fatura_edilecek_toplam_tuketim_kwh'
                 .' ELSE (COALESCE(t1_tuketim,0) + COALESCE(t2_tuketim,0) + COALESCE(t3_tuketim,0)) END)';
        $carpan = 'COALESCE(NULLIF(carpan,0), 1)';
        $t0Gercek = "({$t0Fark} * {$carpan})";

        return "CASE
                    WHEN {$t0Fark} < 0 THEN 0
                    WHEN {$t0Fark} = 0 OR {$t0Gelen} <= 0 THEN 1
                    WHEN ABS({$t0Gelen} - {$t0Gercek}) > 10 AND {$t0Fark} <> 0 THEN 2
                    ELSE 9
                END";
    }

    /**
     * SQL tabanlı anomali kategori ifadesi.
     * Doğrudan anomaly_category string değeri döndürür.
     * Tarife değişimi ve astronomik/düşük tüketim PHP'de hesaplanamaz (history verisi gerektirir),
     * bu yüzden bu kategoriler için SQL'de temel filtreleme yapılır, detay PHP'de eklenir.
     */
    private function endeksAnomalyCategoryExpr(): string
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
        // Tüketimi akıllı seç: önce fatura_edilecek_toplam_tuketim_kwh, yoksa t1+t2+t3
        $t0Gelen = '(CASE WHEN COALESCE(fatura_edilecek_toplam_tuketim_kwh, 0) > 0'
                 .' THEN fatura_edilecek_toplam_tuketim_kwh'
                 .' ELSE (COALESCE(t1_tuketim,0) + COALESCE(t2_tuketim,0) + COALESCE(t3_tuketim,0)) END)';
        $carpan = 'COALESCE(NULLIF(carpan,0), 1)';
        $t0Gercek = "({$t0Fark} * {$carpan})";

        return "CASE
                    WHEN {$t0Fark} < 0 THEN 'negatif_endeks'
                    WHEN {$t0Fark} = 0 OR {$t0Gelen} <= 0 THEN 'sifir_sayac'
                    WHEN ABS({$t0Gelen} - {$t0Gercek}) > 10 AND {$t0Fark} <> 0 THEN 'tutarsiz_endeks'
                    ELSE 'normal'
                END";
    }

    public function yearly(Request $request)
    {
        $results = collect();
        $totals = null;

        $hasFilter = $request->filled('start_year') || $request->filled('end_year') || $request->filled('bolge') || $request->filled('tesisat_no') || $request->filled('yerlesim_tipi') || $request->filled('baglanti_grubu') || $request->filled('tarife');

        if (! $hasFilter) {
            $defaultYear = KesinlesenFatura::where('odeme_durumu', 'odendi')
                ->selectRaw('SUBSTRING(donem, 1, 4) as yil')
                ->distinct()
                ->orderBy('yil', 'desc')
                ->value('yil');
            if ($defaultYear) {
                $request->merge(['start_year' => $defaultYear, 'end_year' => $defaultYear]);
                $hasFilter = true;
            }
        }

        if ($hasFilter) {
            $query = KesinlesenFatura::where('odeme_durumu', 'odendi');

            if ($request->filled('bolge')) {
                $this->applyBolgeFilter($query, (array) $request->bolge);
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
            $normalizedIlce = $this->normalizedIlceExpr();
            $this->applyNormalizesIlceJoin($query);

            $selectRaw = "({$normalizedIlce}) as bolge,
                          SUBSTRING(donem, 1, 4) as yil,
                          COUNT(*) as fatura_sayisi,
                          SUM({$tuketimExpr}) as toplam_tuketim,
                          SUM(CAST(REPLACE(COALESCE(t1_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                            + CAST(REPLACE(COALESCE(t2_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                            + CAST(REPLACE(COALESCE(t3_tuketim, '0'), ',', '.') AS DECIMAL(18,3))) as brut_tuketim,
                          SUM(
                            COALESCE(
                                (CAST(REPLACE(COALESCE(t1_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                                + CAST(REPLACE(COALESCE(t2_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                                + CAST(REPLACE(COALESCE(t3_tuketim, '0'), ',', '.') AS DECIMAL(18,3)))
                                * CAST(REPLACE(COALESCE(birim_fiyat, '0'), ',', '.') AS DECIMAL(18,5)),
                                0
                            )
                          ) as brut_tutar,
                          SUM(COALESCE(tutar_toplam, fatura_tutari, 0)) as toplam_tutar";

            $totals = (clone $query)->selectRaw(
                "COUNT(*) as total_fatura,
                 SUM({$tuketimExpr}) as total_tuketim,
                 SUM(COALESCE(tutar_toplam, fatura_tutari, 0)) as total_tutar,
                 SUM(CAST(REPLACE(COALESCE(t1_tuketim, '0'), ',', '.') AS DECIMAL(18,3))) as total_t1_fark,
                 SUM(CAST(REPLACE(COALESCE(t2_tuketim, '0'), ',', '.') AS DECIMAL(18,3))) as total_t2_fark,
                 SUM(CAST(REPLACE(COALESCE(t3_tuketim, '0'), ',', '.') AS DECIMAL(18,3))) as total_t3_fark,
                 SUM(
                    COALESCE(
                        (CAST(REPLACE(COALESCE(t1_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                        + CAST(REPLACE(COALESCE(t2_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                        + CAST(REPLACE(COALESCE(t3_tuketim, '0'), ',', '.') AS DECIMAL(18,3)))
                        * CAST(REPLACE(COALESCE(birim_fiyat, '0'), ',', '.') AS DECIMAL(18,5)),
                        0
                    )
                 ) as total_brut_tutar"
            )->first();

            $results = $query->selectRaw($selectRaw)
                ->groupByRaw("({$normalizedIlce}), SUBSTRING(donem, 1, 4)")
                ->orderBy('yil', 'desc')
                ->orderBy('bolge', 'asc')
                ->get();

            if ($request->ajax()) {
                return view('reports.partials.yearly_table', compact('results', 'totals'))->render();
            }
        }

        $yillar = KesinlesenFatura::where('odeme_durumu', 'odendi')->selectRaw('SUBSTRING(donem, 1, 4) as yil')->distinct()->orderBy('yil', 'desc')->pluck('yil');
        $bolgeler = $this->getBolgelerList();
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

        return view('reports.yearly', compact('results', 'yillar', 'bolgeler', 'tarifeler', 'totals'));
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

        if (! $hasFilter) {
            $request->merge(['start_period' => KesinlesenFatura::where('odeme_durumu', 'odendi')->max('donem')]);
            $hasFilter = true;
        }

        if ($hasFilter) {
            $query = KesinlesenFatura::where('odeme_durumu', 'odendi');

            if ($request->filled('bolge')) {
                $this->applyBolgeFilter($query, (array) $request->bolge);
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
            $normalizedIlce = $this->normalizedIlceExpr();
            $this->applyNormalizesIlceJoin($query);

            $totals = (clone $query)->selectRaw(
                "COUNT(*) as total_fatura,
                 SUM({$tuketimExpr}) as total_tuketim,
                 SUM(COALESCE(tutar_toplam, fatura_tutari, 0)) as total_tutar,
                 SUM(CAST(REPLACE(COALESCE(t1_tuketim, '0'), ',', '.') AS DECIMAL(18,3))) as total_t1_fark,
                 SUM(CAST(REPLACE(COALESCE(t2_tuketim, '0'), ',', '.') AS DECIMAL(18,3))) as total_t2_fark,
                 SUM(CAST(REPLACE(COALESCE(t3_tuketim, '0'), ',', '.') AS DECIMAL(18,3))) as total_t3_fark,
                 SUM(
                    COALESCE(
                        (CAST(REPLACE(COALESCE(t1_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                        + CAST(REPLACE(COALESCE(t2_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                        + CAST(REPLACE(COALESCE(t3_tuketim, '0'), ',', '.') AS DECIMAL(18,3)))
                        * CAST(REPLACE(COALESCE(birim_fiyat, '0'), ',', '.') AS DECIMAL(18,5)), 
                        0
                    )
                 ) as total_brut_tutar"
            )->first();

            $selectRaw = "donem, ({$normalizedIlce}) as ilce,
                          COUNT(*) as fatura_sayisi,
                          SUM({$tuketimExpr}) as toplam_tuketim,
                          SUM(CAST(REPLACE(COALESCE(t1_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                            + CAST(REPLACE(COALESCE(t2_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                            + CAST(REPLACE(COALESCE(t3_tuketim, '0'), ',', '.') AS DECIMAL(18,3))) as brut_tuketim,
                          SUM(
                            COALESCE(
                                (CAST(REPLACE(COALESCE(t1_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                                + CAST(REPLACE(COALESCE(t2_tuketim, '0'), ',', '.') AS DECIMAL(18,3))
                                + CAST(REPLACE(COALESCE(t3_tuketim, '0'), ',', '.') AS DECIMAL(18,3)))
                                * CAST(REPLACE(COALESCE(birim_fiyat, '0'), ',', '.') AS DECIMAL(18,5)), 
                                0
                            )
                          ) as brut_tutar,
                          SUM(COALESCE(tutar_toplam, fatura_tutari, 0)) as toplam_tutar";

            // Exports: all rows at once
            if ($request->filled('export') && $totals && $totals->total_fatura > 0) {
                set_time_limit(0);
                ini_set('memory_limit', '-1');
                $allResults = $query->selectRaw($selectRaw)
                    ->groupByRaw("donem, ({$normalizedIlce})")
                    ->orderBy('donem', 'desc')
                    ->orderByRaw("({$normalizedIlce}) ASC")
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
                ->groupByRaw("donem, ({$normalizedIlce})")
                ->orderBy('donem', 'desc')
                ->orderByRaw("({$normalizedIlce}) ASC")
                ->paginate(20)
                ->appends($request->all());

            if ($request->ajax()) {
                return view('reports.partials.periodical_table', compact('results', 'totals'))->render();
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = $this->getBolgelerList();
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

        if (! $hasFilter) {
            $defaultPeriod = KesinlesenFatura::where('odeme_durumu', 'odendi')
                ->orderBy('donem', 'desc')
                ->value('donem');
            if ($defaultPeriod) {
                $request->merge(['start_period' => $defaultPeriod]);
                $hasFilter = true;
            }
        }

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
                $this->applyBolgeFilter($query, (array) $request->bolge);
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

            // Tek sorguda hem kwh hem tutar toplama
            $aggregates = (clone $query)->selectRaw(
                'SUM('.$this->tuketimExpr().') as total_kwh, SUM(COALESCE(tutar_toplam, fatura_tutari, 0)) as total_amount'
            )->first();
            $totalKWH = (float) ($aggregates->total_kwh ?? 0);
            $totalAmount = (float) ($aggregates->total_amount ?? 0);

            $query->select('kesinlesen_faturalar.*');

            if ($request->filled('export')) {
                $results = $query->orderBy('donem', 'desc')->orderBy('tutar_toplam', 'desc')->get();
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

            $results = $query->orderBy('donem', 'desc')->orderBy('tutar_toplam', 'desc')->paginate(10)->appends($request->all());

            if ($request->ajax()) {
                return view('reports.partials.detailed_table', compact('results', 'totalKWH', 'totalAmount'))->render();
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = $this->getBolgelerList();
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        return view('reports.detailed', compact('results', 'donemler', 'bolgeler', 'totalKWH', 'totalAmount', 'tarifeler'));
    }

    public function tuketim(Request $request)
    {
        $results = collect();
        $totalKWH = 0;
        $totalAmount = 0;
        $donemler = collect();
        $pivotData = collect();
        $pivotPeriods = collect();
        $veri = $request->get('veri', 'tuketim');

        $hasFilter = $request->anyFilled(['start_period', 'end_period']);

        if (! $hasFilter) {
            $sonDonemler = KesinlesenFatura::where('odeme_durumu', 'odendi')
                ->distinct()->orderBy('donem', 'desc')->limit(12)->pluck('donem');
            if ($sonDonemler->isNotEmpty()) {
                $request->merge([
                    'end_period' => $sonDonemler->first(),
                    'start_period' => $sonDonemler->last(),
                ]);
                $hasFilter = true;
            }
        }

        if ($hasFilter) {
            $baseQuery = KesinlesenFatura::where('odeme_durumu', 'odendi');

            if ($request->filled('start_period')) {
                if ($request->filled('end_period')) {
                    $baseQuery->where('donem', '>=', $request->start_period)
                        ->where('donem', '<=', $request->end_period);
                } else {
                    $baseQuery->where('donem', '=', $request->start_period);
                }
            } elseif ($request->filled('end_period')) {
                $baseQuery->where('donem', '<=', $request->end_period);
            }

            // Toplam KWH
            $aggregates = (clone $baseQuery)->selectRaw(
                'SUM('.$this->tuketimExpr().') as total_kwh, SUM(COALESCE(tutar_toplam,0)) as total_amount'
            )->first();
            $totalKWH = (float) ($aggregates->total_kwh ?? 0);
            $totalAmount = (float) ($aggregates->total_amount ?? 0);

            // Pivot için dönem listesi
            $pivotPeriods = (clone $baseQuery)->distinct()->orderBy('donem')->pluck('donem');

            // Pivot verisi: tesisat_no, donem, deger
            $valueExpr = ($veri === 'tutar') ? 'COALESCE(tutar_toplam, 0)' : $this->tuketimExpr();
            $raw = (clone $baseQuery)
                ->selectRaw("tesisat_no, donem, SUM({$valueExpr}) as deger")
                ->groupBy('tesisat_no', 'donem')
                ->orderBy('tesisat_no')
                ->orderBy('donem')
                ->get();

            // Matrise çevir: [tesisat_no][donem] = deger
            $matrix = [];
            $tesisatTotals = [];
            $periodTotals = [];
            foreach ($raw as $r) {
                $ts = $r->tesisat_no;
                $don = $r->donem;
                $val = (float) $r->deger;
                if (! isset($matrix[$ts])) {
                    $matrix[$ts] = [];
                }
                $matrix[$ts][$don] = $val;
                $tesisatTotals[$ts] = ($tesisatTotals[$ts] ?? 0) + $val;
                $periodTotals[$don] = ($periodTotals[$don] ?? 0) + $val;
            }

            // Son döneme göre büyükten küçüğe sırala
            $sonDonem = $pivotPeriods->last();
            uksort($matrix, function ($a, $b) use ($matrix, $sonDonem) {
                return ($matrix[$b][$sonDonem] ?? 0) <=> ($matrix[$a][$sonDonem] ?? 0);
            });

            $allPivot = collect($matrix);

            // Sütun toplamları (tüm veri için)
            $colTotals = [];
            foreach ($pivotPeriods as $period) {
                $colTotals[$period] = $allPivot->sum(fn ($d) => $d[$period] ?? 0);
            }

            if ($request->filled('export')) {
                if ($request->export === 'pdf') {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                        'reports.tuketim-excel',
                        [
                            'pivotData' => $allPivot,
                            'pivotPeriods' => $pivotPeriods,
                            'totalKWH' => $totalKWH,
                            'totalAmount' => $totalAmount,
                            'veri' => $veri,
                        ]
                    )->setPaper('a4', 'landscape');

                    return $pdf->download('Tuketim_Raporu_'.now()->format('Ymd_His').'.pdf');
                } else {
                    set_time_limit(600);
                    ini_set('memory_limit', '-1');

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\TuketimExport($allPivot, $pivotPeriods, $totalKWH, $veri),
                        'Tuketim_Raporu_'.now()->format('Ymd_His').'.xlsx'
                    );
                }
            }

            $perPage = 50;
            $page = $request->integer('page', 1);
            $offset = ($page - 1) * $perPage;
            $total = $allPivot->count();
            $pivotData = new LengthAwarePaginator(
                $allPivot->slice($offset, $perPage),
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->except('page')]
            );

            if ($request->ajax()) {
                return view('reports.partials.tuketim_table', compact('pivotData', 'pivotPeriods', 'totalKWH', 'totalAmount', 'colTotals', 'veri'))->render();
            }
        }

        if ($request->ajax() && $hasFilter) {
            return view('reports.partials.tuketim_table', compact('pivotData', 'pivotPeriods', 'totalKWH', 'totalAmount', 'veri'))->render();
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');

        return view('reports.tuketim', compact('donemler', 'pivotData', 'pivotPeriods', 'totalKWH', 'totalAmount', 'colTotals', 'veri'));
    }

    private function sendStreamEvent(string $type, array $data): void
    {
        echo 'data: '.json_encode(array_merge(['type' => $type], $data), JSON_UNESCAPED_UNICODE)."\n\n";
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }

    /**
     * PHP tabanlı ek kategorileri hesaplar (tarife_degisen, astronomik, dusuk).
     * SQL'in yapamadığı, geçmiş veri gerektiren kategoriler için kullanılır.
     * SQL zaten negatif_endeks, sifir_sayac, tutarsiz_endeks kategorilerini belirledi.
     */
    private function enrichAnomalyCategory($row, $prev, array $pastTutarlar, array $pastTuketimler): string
    {
        // SQL zaten bu kategorileri belirledi — sadece geçmiş veri gerektirenleri zenginleştir
        $sqlCategory = $row->anomaly_category ?? 'normal';

        // negatif_endeks, sifir_sayac, tutarsiz_endeks SQL'den geliyor, dokunma
        if (in_array($sqlCategory, ['negatif_endeks', 'sifir_sayac', 'tutarsiz_endeks'], true)) {
            return $sqlCategory;
        }

        $count = count($pastTutarlar);
        if ($count > 0) {
            // Çarpan değişimi kontrolü
            if ($prev) {
                $prevCarpan = (float) str_replace(',', '.', $prev->carpan ?? '1');
                $curCarpan = (float) str_replace(',', '.', $row->carpan ?? '1');
                if (abs($prevCarpan - $curCarpan) > 0.001 && $prevCarpan > 0 && $curCarpan > 0) {
                    return 'carpan_degisimi';
                }
            }

            // Birim fiyat değişimi kontrolü
            if ($prev) {
                $prevBirim = (float) str_replace(',', '.', $prev->birim_fiyat ?? '0');
                $curBirim = (float) str_replace(',', '.', $row->birim_fiyat ?? '0');
                if (abs($prevBirim - $curBirim) > 0.001 && $prevBirim > 0 && $curBirim > 0) {
                    return 'birim_fiyat_degisimi';
                }
            }

            // Tarife değişimi kontrolü
            if ($prev) {
                $prevTarif = trim($prev->tarife ?? '');
                $prevTarif2 = trim($prev->tarife_2 ?? '');
                $curTarif = trim($row->tarife ?? '');
                $curTarif2 = trim($row->tarife_2 ?? '');
                if ($prevTarif !== $curTarif || $prevTarif2 !== $curTarif2) {
                    return 'tarife_degisen';
                }
            }

            // Astronomik ve düşük tutar/tüketim kontrolü
            $avgTutar = array_sum($pastTutarlar) / $count;
            $currentTutar = (float) $row->tutar_toplam;
            $currentTuketim = (float) ($row->fatura_edilecek_toplam_tuketim_kwh ?? 0);
            $avgTuketim = array_sum($pastTuketimler) / $count;

            // Eğer tüketim veya tutar sıfırsa (veya sıfırın altındaysa), doğrudan sıfır tüketim kategorisine al.
            // 0.5 altı değerler UI'da 0 göründüğü için yuvarlayarak kontrol et.
            if (round($currentTuketim) <= 0 || round($currentTutar) <= 0) {
                return 'sifir_sayac';
            }

            if ($avgTutar > 0 && $currentTutar > $avgTutar * 2) {
                $recent = array_slice($pastTutarlar, -6);
                $rc = count($recent);
                $stable = $rc >= 2
                    && ($minVal = min($recent)) > 0
                    && max($recent) <= $minVal * 1.9;
                if (! $stable) {
                    return 'astronomik';
                }
            }

            // Düşük tüketim kontrolü
            $avgTuketim = array_sum($pastTuketimler) / $count;
            $currentTuketim = (float) ($row->fatura_edilecek_toplam_tuketim_kwh ?? 0);
            if ($avgTuketim > 0 && $currentTuketim < $avgTuketim * 0.5) {
                return 'dusuk';
            }
        }

        return 'normal';
    }

    public function endeks(Request $request)
    {
        $results = collect();
        $totalKWH = 0;
        $totalAmount = 0;
        $tabCounts = [
            'sifir_sayac' => 0, 'negatif_endeks' => 0, 'tutarsiz_endeks' => 0,
            'dusuk' => 0, 'astronomik' => 0, 'carpan_degisimi' => 0,
            'tarife_degisen' => 0, 'birim_fiyat_degisimi' => 0,
        ];
        $activeTab = $request->get('tab', 'sifir_sayac');

        $hasFilter = $request->anyFilled(['bolge', 'start_period', 'end_period', 'yerlesim_tipi', 'baglanti_grubu', 'tarife', 'tesisat_no']);

        if ($hasFilter) {
            // Eğer normal sayfa yüklemesi ise (AJAX veya EXPORT değilse) analiz yapma, sayfayı boş yükle, JS tetiklesin
            if (! $request->ajax() && ! $request->filled('export')) {
                $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
                $importDonemler = \App\Models\ImportLog::whereNotNull('donem')->distinct()->orderBy('donem', 'desc')->pluck('donem');
                $bolgeler = $this->getBolgelerList();
                $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
                    ->select('tarife', 'abone_grubu')
                    ->distinct()
                    ->get()
                    ->unique('abone_grubu')
                    ->sortBy('abone_grubu')
                    ->values();

                return view('reports.endeks', compact('results', 'donemler', 'importDonemler', 'bolgeler', 'tarifeler', 'totalKWH', 'totalAmount', 'tabCounts', 'activeTab'));
            }

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
                $this->applyBolgeFilter($query, (array) $request->bolge);
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

            // ── Tüm faturalar çekilir, PHP kategorilendirme yapılır ──────────────
            // SQL temel anomali kategorisini belirler (negatif, sıfır, tutarsız),
            // PHP history karşılaştırması ile tarife/çarpan/astronomik/düşük tespit eder.
            $anomalyCategoryExpr = $this->endeksAnomalyCategoryExpr();

            $filteredQuery = (clone $query)
                ->selectRaw("*, ({$anomalyCategoryExpr}) as anomaly_category");

            // ── Geçmiş veri çekme (tarife_degisen / astronomik / dusuk için) ─────
            // Sadece SQL tarafından "normal" bulunan ama geçmiş karşılaştırması
            // gereken kayıtlar için history alınır. SQL zaten negatif/sıfır/tutarsız
            // olanları yakaladığından history sorgusu çok daha küçük bir küme üzerinde çalışır.
            $allResults = $filteredQuery
                ->orderBy('donem', 'desc')
                ->orderBy('ilce')
                ->get();

            $tesisatNumberList = $allResults->pluck('tesisat_no')->filter()->unique()->values();
            $allHistorical = collect();

            if ($tesisatNumberList->count() > 0) {
                $minDonem = $allResults->min('donem');
                $minHistoryDonem = null;
                if ($minDonem) {
                    $parts = explode('-', $minDonem);
                    $y = (int) $parts[0];
                    $m = (int) ($parts[1] ?? 1);
                    $m -= 18;
                    while ($m <= 0) {
                        $y--;
                        $m += 12;
                    }
                    $minHistoryDonem = sprintf('%d-%02d', $y, $m);
                }

                // Tüm tesisat history'sini tek bir sorguyla çek (chunk'lar halinde, ama merge yerine doğrudan groupBy)
                $historyBase = KesinlesenFatura::where('odeme_durumu', 'odendi')
                    ->select(['tesisat_no', 'donem', 'tarife', 'tarife_2', 'tutar_toplam', 'fatura_edilecek_toplam_tuketim_kwh', 'carpan', 'birim_fiyat']);

                if ($minHistoryDonem) {
                    $historyBase->where('donem', '>=', $minHistoryDonem);
                }

                // Tesisat sayısı çoksa chunk yap — ama sonuçları direkt groupBy ile al
                foreach ($tesisatNumberList->chunk(500) as $chunk) {
                    $rows = (clone $historyBase)
                        ->whereIn('tesisat_no', $chunk)
                        ->orderBy('donem', 'desc')
                        ->get()
                        ->groupBy('tesisat_no');

                    foreach ($rows as $tesisatNo => $records) {
                        if (! $allHistorical->has($tesisatNo)) {
                            $allHistorical[$tesisatNo] = $records->take(24);
                        } else {
                            $allHistorical[$tesisatNo] = $allHistorical[$tesisatNo]->merge($records)->take(24);
                        }
                    }
                }
            }

            // Tesisat bazında history'yi ASC sırala (kategorilendirme için)
            $historicalSorted = [];
            foreach ($allHistorical as $tesisatNo => $records) {
                $historicalSorted[$tesisatNo] = $records->sortBy('donem')->values();
            }

            // ── PHP Kategorilendirme: SADECE history gerektiren kategoriler ───────
            // SQL'den gelen negatif_endeks / sifir_sayac / tutarsiz_endeks kategorileri korunur.
            // Tarife_degisen, astronomik, dusuk kategorileri history ile belirlenir.
            $processCategorization = function ($results, &$anomalous, ?\Closure $progressCb = null) use ($historicalSorted) {
                $resultsByTesisat = $results->groupBy('tesisat_no');

                foreach ($resultsByTesisat as $tesisatNo => $records) {
                    $records = $records->sortBy('donem')->values();
                    $history = $historicalSorted[$tesisatNo] ?? collect();
                    $hCount = $history->count();
                    $hCursor = 0;

                    $pastTutarlar = [];
                    $pastTuketimler = [];

                    foreach ($records as $row) {
                        for (; $hCursor < $hCount && $history[$hCursor]->donem < $row->donem; $hCursor++) {
                            $pastTutarlar[] = (float) $history[$hCursor]->tutar_toplam;
                            $pastTuketimler[] = (float) ($history[$hCursor]->fatura_edilecek_toplam_tuketim_kwh ?? 0);
                        }

                        $prev = $hCursor > 0 ? $history[$hCursor - 1] : null;

                        // enrichAnomalyCategory; SQL kategorisini temel alarak sadece history
                        // gerektiren kısımları zenginleştirir
                        $cat = $this->enrichAnomalyCategory($row, $prev, $pastTutarlar, $pastTuketimler);
                        $row->anomaly_category = $cat;

                        if ($cat !== 'normal') {
                            $anomalous->push($row);
                        }

                        if ($progressCb) {
                            $progressCb($row);
                        }
                    }
                }
            };

            // ── Stream modu (AJAX ilk yükleme) ───────────────────────────────────
            if ($request->ajax() && $request->has('stream') && ! $request->has('page')) {
                return response()->stream(function () use ($allResults, $request, $processCategorization) {
                    // set_time_limit(0) KALDIRILDI — sonsuz döngü riskine karşı 300 sn limit
                    set_time_limit(300);

                    try {
                        $total = $allResults->count();
                        $this->sendStreamEvent('start', ['total' => $total]);

                        $anomalous = collect();
                        $processed = 0;

                        $processCategorization($allResults, $anomalous, function () use (&$processed, $total) {
                            $processed++;
                            if ($processed % 100 === 0 || $processed === $total) {
                                $this->sendStreamEvent('progress', ['processed' => $processed, 'total' => $total]);
                            }
                        });

                        // Tüm anomaliler bulunduktan sonra en yüksek tutardan en düşük tutara sırala
                        $anomalous = $anomalous->sortByDesc(function ($item) {
                            return (float) $item->tutar_toplam;
                        })->values();

                        $tabCounts = [
                            'sifir_sayac' => 0, 'negatif_endeks' => 0, 'tutarsiz_endeks' => 0,
                            'dusuk' => 0, 'astronomik' => 0, 'carpan_degisimi' => 0,
                            'tarife_degisen' => 0, 'birim_fiyat_degisimi' => 0,
                        ];
                        foreach ($anomalous as $a) {
                            $cat = $a->anomaly_category;
                            if (! isset($tabCounts[$cat])) {
                                $tabCounts[$cat] = 0;
                            }
                            $tabCounts[$cat]++;
                        }

                        $activeTab = $request->get('tab', 'sifir_sayac');
                        $perPage = 100;

                        // Tüm sekmelerin HTML'ini tek seferde render et — client-side cache için
                        $tabKeys = ['sifir_sayac', 'negatif_endeks', 'tutarsiz_endeks',
                            'dusuk', 'astronomik', 'carpan_degisimi',
                            'tarife_degisen', 'birim_fiyat_degisimi'];

                        $allTabHtml = [];
                        $allTabRowIds = [];

                        foreach ($tabKeys as $tabKey) {
                            $tabAnoms = $anomalous->where('anomaly_category', $tabKey)->values();
                            $tabTotalKWH = $tabAnoms->sum(fn ($r) => (float) ($r->fatura_edilecek_toplam_tuketim_kwh ?? 0));
                            $tabTotalAmount = $tabAnoms->sum('tutar_toplam');
                            $tabResults = new LengthAwarePaginator(
                                $tabAnoms->forPage(1, $perPage),
                                $tabAnoms->count(),
                                $perPage,
                                1,
                                ['path' => Paginator::resolveCurrentPath(), 'query' => array_merge($request->except('stream'), ['tab' => $tabKey])]
                            );
                            $allTabHtml[$tabKey] = view('reports.partials.endeks_table', [
                                'results' => $tabResults,
                                'totalKWH' => $tabTotalKWH,
                                'totalAmount' => $tabTotalAmount,
                                'tabCounts' => $tabCounts,
                                'activeTab' => $tabKey,
                            ])->render();
                            $allTabRowIds[$tabKey] = $tabAnoms->forPage(1, $perPage)->pluck('id')->values();
                        }

                        $activeHtml = $allTabHtml[$activeTab] ?? $allTabHtml['sifir_sayac'] ?? '';
                        $activeRowIds = $allTabRowIds[$activeTab] ?? $allTabRowIds['sifir_sayac'] ?? [];

                        $this->sendStreamEvent('complete', [
                            'html' => $activeHtml,
                            'row_ids' => $activeRowIds,
                            'allTabHtml' => $allTabHtml,
                            'allTabRowIds' => $allTabRowIds,
                            'activeTab' => $activeTab,
                        ]);

                    } catch (\Throwable $e) {
                        // Herhangi bir hata durumunda complete event'i mutlaka gönder.
                        // Aksi halde JS'teki while(true) döngüsü sonsuza açık kalır.
                        $this->sendStreamEvent('complete', [
                            'html' => '<div style="padding:20px;color:#dc2626;font-weight:700;"><i class="fas fa-exclamation-triangle"></i> Analiz hatası: '.htmlspecialchars($e->getMessage()).'</div>',
                            'row_ids' => [],
                            'error' => true,
                        ]);
                    }
                }, 200, [
                    'Content-Type' => 'text/event-stream',
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                ]);
            }

            // ── Normal (sayfalı) mod ──────────────────────────────────────────────
            $anomalous = collect();
            $processCategorization($allResults, $anomalous);

            // Tüm anomaliler bulunduktan sonra en yüksek tutardan en düşük tutara sırala
            $anomalous = $anomalous->sortByDesc(function ($item) {
                return (float) $item->tutar_toplam;
            })->values();

            $tabCounts = [
                'sifir_sayac' => 0, 'negatif_endeks' => 0, 'tutarsiz_endeks' => 0,
                'dusuk' => 0, 'astronomik' => 0, 'carpan_degisimi' => 0,
                'tarife_degisen' => 0, 'birim_fiyat_degisimi' => 0,
            ];
            foreach ($anomalous as $a) {
                $cat = $a->anomaly_category;
                if (! isset($tabCounts[$cat])) {
                    $tabCounts[$cat] = 0;
                }
                $tabCounts[$cat]++;
            }

            $activeTab = $request->get('tab', 'sifir_sayac');
            // 'tumu' seçiliyse tüm anomalileri göster
            if ($activeTab === 'tumu') {
                $filteredAnomalous = $anomalous;
            } else {
                $filteredAnomalous = $anomalous->where('anomaly_category', $activeTab);
            }

            $totalKWH = $filteredAnomalous->sum(fn ($r) => (float) ($r->fatura_edilecek_toplam_tuketim_kwh ?? 0));
            $totalAmount = $filteredAnomalous->sum('tutar_toplam');
            $page = Paginator::resolveCurrentPage();
            $perPage = 100;
            $results = new LengthAwarePaginator(
                $filteredAnomalous->forPage($page, $perPage)->values(),
                $filteredAnomalous->count(),
                $perPage,
                $page,
                ['path' => Paginator::resolveCurrentPath(), 'query' => $request->except('stream')]
            );

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('reports.partials.endeks_table', compact('results', 'totalKWH', 'totalAmount', 'tabCounts', 'activeTab'))->render(),
                    'row_ids' => collect($results->items())->pluck('id')->values(),
                ]);
            }

            if ($request->filled('export') && $filteredAnomalous->count() > 0) {
                $filters = $request->only(['bolge', 'start_period', 'end_period', 'tarife', 'tab']);
                if ($request->export === 'excel') {
                    set_time_limit(600);
                    ini_set('memory_limit', '-1');

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\EndeksReportExport($filteredAnomalous, $totalKWH, $totalAmount, $filters),
                        'Endeks_Raporu_'.now()->format('Ymd_His').'.xlsx'
                    );
                } elseif ($request->export === 'pdf') {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
                        'results' => $filteredAnomalous,
                        'type' => 'endeks',
                        'filters' => $filters,
                    ])->setPaper('a4', 'landscape');

                    return $pdf->download('Endeks_Raporu_'.now()->format('Ymd_His').'.pdf');
                }
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $importDonemler = \App\Models\ImportLog::whereNotNull('donem')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = $this->getBolgelerList();
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        return view('reports.endeks', compact('results', 'donemler', 'importDonemler', 'bolgeler', 'tarifeler', 'totalKWH', 'totalAmount', 'tabCounts', 'activeTab'));
    }

    public function pdfKarsilastirFaturalar($donem)
    {
        $importLogIds = \App\Models\ImportLog::where('donem', $donem)->pluck('id');
        $rows = \App\Models\Hamveri::whereIn('import_log_id', $importLogIds)
            ->whereNotNull('payload')
            ->get(['payload']);

        $cwDegerler = [];
        foreach ($rows as $row) {
            $payload = $row->payload;
            if (! is_array($payload)) {
                continue;
            }

            $efks = null;
            $faturaNo = null;
            $hesapAdi = null;
            $tutar = null;

            foreach ($payload as $key => $val) {
                $upKey = strtoupper(trim($key));
                if ($upKey === 'EFKS_FATURA_ID') {
                    $efks = trim((string) $val);
                }
                if ($upKey === 'FATURA_NO' || str_contains($upKey, 'FATURA')) {
                    if (! $faturaNo) {
                        $faturaNo = $val;
                    }
                }
                if (str_contains($upKey, 'HESAP') || str_contains($upKey, 'MUSTERI') || str_contains($upKey, 'ABONE') || str_contains($upKey, 'UNVAN')) {
                    if (! $hesapAdi) {
                        $hesapAdi = $val;
                    }
                }
                if ($upKey === 'TUTAR_TOPLAM' || $upKey === 'GENEL_TOPLAM' || str_contains($upKey, 'ODENECEK') || $upKey === 'TUTAR') {
                    if (! $tutar) {
                        $tutar = $val;
                    }
                }
            }
            if ($efks && $efks !== '') {
                $cwDegerler[$efks] = [
                    'id' => $efks,
                    'fatura_no' => $faturaNo ?? '-',
                    'hesap_adi' => $hesapAdi ?? '-',
                    'tutar' => $tutar ?? '-',
                ];
            }
        }

        ksort($cwDegerler);

        return response()->json([
            'success' => true,
            'donem' => $donem,
            'faturalar' => $cwDegerler,
            'toplam' => count($cwDegerler),
        ]);
    }

    public function elektrikAboneRaporlari()
    {
        return view('reports.elektrik_abone_raporlari');
    }

    public function pdfKarsilastirFaturaDetay($efksId)
    {
        $rows = \App\Models\Hamveri::whereNotNull('payload')
            ->orderBy('id', 'desc')
            ->limit(5000)
            ->get(['payload', 'import_log_id']);

        $foundPayload = null;
        $importLogId = null;
        foreach ($rows as $r) {
            $payload = $r->payload;
            if (! is_array($payload)) {
                continue;
            }
            foreach ($payload as $key => $val) {
                if (strtoupper(trim($key)) === 'EFKS_FATURA_ID' && trim((string) $val) === $efksId) {
                    $foundPayload = $payload;
                    $importLogId = $r->import_log_id;
                    break 2;
                }
            }
        }

        if (! $foundPayload) {
            return response()->json(['success' => false, 'message' => 'Kayıt bulunamadı.']);
        }

        $findField = function (array $payload, array $keys): ?string {
            foreach ($payload as $k => $v) {
                foreach ($keys as $key) {
                    if (mb_strtolower(trim($k), 'UTF-8') === mb_strtolower($key, 'UTF-8')) {
                        $val = $v !== null ? trim((string) $v) : null;

                        return ($val !== '') ? $val : null;
                    }
                }
            }

            return null;
        };

        $tesisatNo = $findField($foundPayload, ['tesisat', 'tesisat no', 'tesisat_no', 'abone_tesis_no', 'tesisatno']);
        $adres = $findField($foundPayload, ['adres', 'address', 'adresi', 'tesisat adresi']);
        $bolge = $findField($foundPayload, ['dagitim', 'dağıtım', 'bolge', 'bölge', 'dagıtım']);

        $faturaNo = $findField($foundPayload, ['fatura_no', 'fatura no', 'fatuno', 'belge_no']);
        $hesapAdi = $findField($foundPayload, ['hesap_adi', 'hesap adı', 'musteri_adi', 'müşteri adı', 'unvan', 'abone', 'musteri']);

        $donem = null;
        if ($importLogId) {
            $importLog = \App\Models\ImportLog::find($importLogId);
            $donem = $importLog ? $importLog->donem : null;
        }
        if (! $donem) {
            $donem = $findField($foundPayload, ['donem', 'dönem', 'tahakkuk', 'donem_fatura', 'tahakkuk tarihi']);
        }

        $kesinlesen = null;
        if ($tesisatNo && $donem) {
            $kesinlesen = \App\Models\KesinlesenFatura::where('tesisat_no', $tesisatNo)
                ->where('donem', $donem)
                ->first();
        }

        $detail = [
            'efks_id' => $efksId,
            'tesisat_no' => $tesisatNo ?? '—',
            'donem' => $donem ?? '—',
            'fatura_no' => $faturaNo ?? '—',
            'hesap_adi' => $hesapAdi ?? '—',
            'adres' => $adres ?? '—',
            'bolge' => $bolge ?? '—',
            't1_tuketim' => '—',
            't2_tuketim' => '—',
            't3_tuketim' => '—',
            't0_tuketim' => '—',
            'ek_tuketim' => '—',
            'tutar_toplam' => '—',
            'kdv' => '—',
        ];

        if ($kesinlesen) {
            $detail['adres'] = $kesinlesen->adres ?? $detail['adres'];
            $detail['bolge'] = $kesinlesen->dagitim ?? $detail['bolge'];
            $detail['t1_tuketim'] = $kesinlesen->t1_tuketim ?? '—';
            $detail['t2_tuketim'] = $kesinlesen->t2_tuketim ?? '—';
            $detail['t3_tuketim'] = $kesinlesen->t3_tuketim ?? '—';
            $detail['t0_tuketim'] = $kesinlesen->fatura_edilecek_toplam_tuketim_kwh ?? '—';
            $detail['ek_tuketim'] = $kesinlesen->ek_tuketim ?? '—';
            $detail['tutar_toplam'] = $kesinlesen->tutar_toplam ?? '—';
            $detail['kdv'] = $kesinlesen->kdv ?? '—';
        }

        $detail['kesinlesen_var'] = $kesinlesen !== null;

        return response()->json([
            'success' => true,
            'detail' => $detail,
        ]);
    }

    private function jsonFieldExpr(string $field): string
    {
        $path = str_contains($field, ' ') ? '$."'.$field.'"' : '$.'.$field;

        return "CAST(REPLACE(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(payload, '{$path}')), '0'), ',', '.') AS DECIMAL(18,6))";
    }

    private function hasIlaveInPayload(): string
    {
        $conditions = array_map(fn ($f) => $this->jsonFieldExpr($f).' != 0', ['T1_ILAVE_KWH', 'T2_ILAVE_KWH', 'T3_ILAVE_KWH', 'T4_ILAVE_KWH']);

        return '(payload IS NOT NULL AND ('.implode(' OR ', $conditions).'))';
    }

    public function ekTuketim(Request $request)
    {
        $results = collect();
        $totalKWH = 0;
        $totalAmount = 0;
        $totalIlaveToplam = 0;
        $totalIlaveTutar = 0;

        $hasFilter = $request->anyFilled(['start_period', 'end_period']);

        if (! $hasFilter) {
            $defaultPeriod = KesinlesenFatura::where('odeme_durumu', 'odendi')
                ->orderBy('donem', 'desc')
                ->value('donem');
            if ($defaultPeriod) {
                $request->merge(['start_period' => $defaultPeriod]);
                $hasFilter = true;
            }
        }

        if ($hasFilter) {
            $ilaveFields = ['T1_ILAVE_KWH', 'T2_ILAVE_KWH', 'T3_ILAVE_KWH', 'T4_ILAVE_KWH'];
            $ilaveSumExpr = implode(' + ', array_map(fn ($f) => $this->jsonFieldExpr($f), $ilaveFields));

            $query = KesinlesenFatura::where('odeme_durumu', 'odendi')
                ->whereRaw($this->hasIlaveInPayload());

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

            $aggregates = (clone $query)->selectRaw(
                "SUM({$this->tuketimExpr()}) as total_kwh, SUM(COALESCE(tutar_toplam,0)) as total_amount, SUM({$ilaveSumExpr}) as total_ilave_toplam"
            )->first();
            $totalKWH = (float) ($aggregates->total_kwh ?? 0);
            $totalAmount = (float) ($aggregates->total_amount ?? 0);
            $totalIlaveToplam = (float) ($aggregates->total_ilave_toplam ?? 0);

            $query->select('kesinlesen_faturalar.*');
            $query->orderBy('donem', 'desc')->orderBy('tutar_toplam', 'desc');

            $allResults = (clone $query)->get();

            $totalIlaveTutar = $allResults->sum(function ($row) {
                $payload = $row->payload;
                $ilaveToplam = 0;
                if ($payload) {
                    foreach (['T1_ILAVE_KWH', 'T2_ILAVE_KWH', 'T3_ILAVE_KWH', 'T4_ILAVE_KWH'] as $f) {
                        $val = $payload[$f] ?? 0;
                        if ($val !== '' && $val !== ' ' && $val !== null) {
                            $ilaveToplam += (float) str_replace(',', '.', $val);
                        }
                    }
                }
                $birimFiyat = (float) str_replace(',', '.', $row->birim_fiyat ?? '0');

                return $ilaveToplam * $birimFiyat;
            });

            if ($request->filled('export') && $allResults->count() > 0) {
                $filters = $request->only(['start_period', 'end_period']);
                $totals = [
                    'total_kwh' => $totalKWH,
                    'total_amount' => $totalAmount,
                    'total_ilave_toplam' => $totalIlaveToplam,
                    'total_ilave_tutar' => $totalIlaveTutar,
                ];

                if ($request->export === 'excel') {
                    set_time_limit(600);
                    ini_set('memory_limit', '-1');

                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\EkTuketimExport($allResults, $totals, $filters),
                        'EkTuketim_Raporu_'.now()->format('Ymd_His').'.xlsx'
                    );
                } elseif ($request->export === 'pdf') {
                    set_time_limit(0);
                    ini_set('memory_limit', '-1');
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
                        'results' => $allResults,
                        'type' => 'ek_tuketim',
                        'filters' => $filters,
                    ])->setPaper('a4', 'landscape');

                    return $pdf->download('EkTuketim_Raporu_'.now()->format('Ymd_His').'.pdf');
                }
            }

            $results = $query->paginate(20)->appends($request->all());

            if ($request->ajax()) {
                return view('reports.partials.ek_tuketim_table', compact('results', 'totalKWH', 'totalAmount', 'totalIlaveToplam', 'totalIlaveTutar'))->render();
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');

        return view('reports.ek-tuketim', compact('donemler', 'results', 'totalKWH', 'totalAmount', 'totalIlaveToplam', 'totalIlaveTutar'));
    }

    public function ekTuketimSon1Yil($tesisat_no)
    {
        $records = KesinlesenFatura::where('tesisat_no', $tesisat_no)
            ->where('odeme_durumu', 'odendi')
            ->orderBy('donem', 'desc')
            ->limit(12)
            ->get();

        $formatted = $records->map(function ($row) {
            $t1 = (float) ($row->t1_tuketim ?? 0);
            $t2 = (float) ($row->t2_tuketim ?? 0);
            $t3 = (float) ($row->t3_tuketim ?? 0);
            $toplamTuketim = (float) ($row->fatura_edilecek_toplam_tuketim_kwh ?: ($t1 + $t2 + $t3 + (float) ($row->ek_tuketim ?: 0)));
            $birimFiyat = (float) str_replace(',', '.', $row->birim_fiyat ?? '0');

            $payload = $row->payload;
            $t1Ilave = $t2Ilave = $t3Ilave = $t4Ilave = 0;
            if ($payload) {
                foreach (['T1_ILAVE_KWH' => &$t1Ilave, 'T2_ILAVE_KWH' => &$t2Ilave, 'T3_ILAVE_KWH' => &$t3Ilave, 'T4_ILAVE_KWH' => &$t4Ilave] as $key => &$ref) {
                    $val = $payload[$key] ?? 0;
                    $ref = ($val !== '' && $val !== ' ' && $val !== null) ? (float) str_replace(',', '.', $val) : 0;
                }
                unset($ref);
            }
            $ilaveToplam = $t1Ilave + $t2Ilave + $t3Ilave + $t4Ilave;

            return [
                'donem' => $row->donem,
                'tesisat_no' => $row->tesisat_no,
                'ilk_okuma' => $row->ilk_okuma ? $row->ilk_okuma->format('d.m.Y') : null,
                'son_okuma' => $row->son_okuma ? $row->son_okuma->format('d.m.Y') : null,
                't1' => $t1,
                't2' => $t2,
                't3' => $t3,
                'toplam_tuketim' => $toplamTuketim,
                't1_ilave' => $t1Ilave,
                't2_ilave' => $t2Ilave,
                't3_ilave' => $t3Ilave,
                't4_ilave' => $t4Ilave,
                'ilave_toplam' => $ilaveToplam,
                'tutar' => (float) ($row->tutar_toplam ?: 0),
                'ilave_tutar' => $ilaveToplam * $birimFiyat,
            ];
        });

        $abone = \App\Models\Aboneler::where('ABONE_TESIS_NO', $tesisat_no)->first();

        return response()->json([
            'success' => true,
            'tesisat_no' => $tesisat_no,
            'abone' => $abone ? [
                'bolge' => $abone->BOLGE_ADI,
                'adres' => $abone->ADRES,
            ] : null,
            'records' => $formatted,
        ]);
    }

    public function koyMerkez(Request $request)
    {
        $results = collect();
        $totalKoyTuketim = 0;
        $totalKoyTutar = 0;
        $totalMerkezTuketim = 0;
        $totalMerkezTutar = 0;

        // Filtre yoksa son dönemi default olarak uygula
        $hasFilter = $request->anyFilled(['bolge', 'start_period', 'end_period', 'baglanti_grubu', 'tarife'])
            || ! empty($request->input('bolge'))
            || ! empty($request->input('tarife'));

        if (! $hasFilter) {
            $defaultPeriod = KesinlesenFatura::where('odeme_durumu', 'odendi')
                ->orderBy('donem', 'desc')
                ->value('donem');
            if ($defaultPeriod) {
                $request->merge(['start_period' => $defaultPeriod]);
                $hasFilter = true;
            }
        }

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
            if (! empty($request->input('bolge'))) {
                $this->applyBolgeFilter($query, (array) $request->input('bolge'));
            }
            if ($request->filled('baglanti_grubu')) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->where('baglanti_grubu', $request->baglanti_grubu);
                });
            }
            if (! empty($request->input('tarife'))) {
                $query->whereIn('tesisat_no', function ($q) use ($request) {
                    $q->select('ABONE_TESIS_NO')->from('aboneler')->whereIn('tarife', (array) $request->input('tarife'));
                });
            }
            if ($request->filled('tesisat_no')) {
                $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
            }

            $tuketimExpr = $this->tuketimExpr();
            $tutarExpr = 'COALESCE(tutar_toplam, fatura_tutari, 0)';

            // NOT IN + NULL tuzağından kaçınmak için EXISTS / NOT EXISTS kullanılıyor.
            // aboneler.ABONE_TESIS_NO NULL içerirse NOT IN tüm satırları false yapar.
            $isKoyCondition = "EXISTS (
                SELECT 1 FROM aboneler
                WHERE aboneler.ABONE_TESIS_NO = kesinlesen_faturalar.tesisat_no
                  AND aboneler.yerlesim_turu = 'KÖY'
            )";

            $isMerkezCondition = "NOT EXISTS (
                SELECT 1 FROM aboneler
                WHERE aboneler.ABONE_TESIS_NO = kesinlesen_faturalar.tesisat_no
                  AND aboneler.yerlesim_turu = 'KÖY'
            )";

            $normalizedIlce = $this->normalizedIlceExpr();
            $this->applyNormalizesIlceJoin($query);

            $selectRaw = "
                donem,
                ({$normalizedIlce}) as bolge,
                SUM(CASE WHEN {$isKoyCondition} THEN {$tuketimExpr} ELSE 0 END) as koy_tuketim,
                SUM(CASE WHEN {$isKoyCondition} THEN {$tutarExpr} ELSE 0 END) as koy_tutar,
                SUM(CASE WHEN {$isMerkezCondition} THEN {$tuketimExpr} ELSE 0 END) as merkez_tuketim,
                SUM(CASE WHEN {$isMerkezCondition} THEN {$tutarExpr} ELSE 0 END) as merkez_tutar
            ";

            $results = $query->selectRaw($selectRaw)
                ->groupByRaw("donem, ({$normalizedIlce})")
                ->orderBy('donem', 'desc')
                ->orderByRaw("({$normalizedIlce}) ASC")
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
                    ])->setPaper('a4', 'landscape');

                    return $pdf->download('KoyMerkez_Ozet_Raporu_'.now()->format('Ymd_His').'.pdf');
                }
            }
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = $this->getBolgelerList();
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        return view('reports.koy-merkez', compact('results', 'donemler', 'bolgeler', 'tarifeler', 'totalKoyTuketim', 'totalKoyTutar', 'totalMerkezTuketim', 'totalMerkezTutar'));
    }

    public function gecmis1Yil($tesisat_no, Request $request)
    {
        $query = KesinlesenFatura::where('tesisat_no', $tesisat_no);

        if ($request->filled('donem')) {
            $query->where('donem', '<=', $request->donem);
        }

        $records = $query->orderBy('donem', 'desc')
            ->limit(12)
            ->get();

        $formatted = $records->map(function ($row) {
            $carpan = (float) ($row->carpan ?: 1);

            $t1Ilk = (float) str_replace(',', '.', $row->t1_ilk_endeks ?? 0);
            $t1Son = (float) str_replace(',', '.', $row->t1_son_endeks ?? 0);
            $t2Ilk = (float) str_replace(',', '.', $row->t2_ilk_endeks ?? 0);
            $t2Son = (float) str_replace(',', '.', $row->t2_son_endeks ?? 0);
            $t3Ilk = (float) str_replace(',', '.', $row->t3_ilk_endeks ?? 0);
            $t3Son = (float) str_replace(',', '.', $row->t3_son_endeks ?? 0);

            $t1Fark = $t1Son - $t1Ilk;
            $t2Fark = $t2Son - $t2Ilk;
            $t3Fark = $t3Son - $t3Ilk;

            $hasTariff = ($t1Ilk + $t2Ilk + $t3Ilk) > 0;
            $t0Ilk = $hasTariff ? ($t1Ilk + $t2Ilk + $t3Ilk) : (float) str_replace(',', '.', $row->t0_ilk_endeks ?? 0);
            $t0Son = $hasTariff ? ($t1Son + $t2Son + $t3Son) : (float) str_replace(',', '.', $row->t0_son_endeks ?? 0);
            $t0Fark = $t0Son - $t0Ilk;

            $t1Tuketim = (float) ($row->t1_tuketim ?? 0);
            $t2Tuketim = (float) ($row->t2_tuketim ?? 0);
            $t3Tuketim = (float) ($row->t3_tuketim ?? 0);

            $t0Tuketim = $hasTariff ? ($t1Tuketim + $t2Tuketim + $t3Tuketim) : (float) ($row->fatura_edilecek_toplam_tuketim_kwh ?? 0);

            $riIlk = (float) str_replace(',', '.', $row->ri_ilk_endeks ?? 0);
            $riSon = (float) str_replace(',', '.', $row->ri_son_endeks ?? 0);
            $riFark = $row->ri_fark_endeks !== null ? (float) str_replace(',', '.', $row->ri_fark_endeks) : ($riSon - $riIlk);

            $rcIlk = (float) str_replace(',', '.', $row->rc_ilk_endeks ?? 0);
            $rcSon = (float) str_replace(',', '.', $row->rc_son_endeks ?? 0);
            $rcFark = $row->rc_fark_endeks !== null ? (float) str_replace(',', '.', $row->rc_fark_endeks) : ($rcSon - $rcIlk);

            return [
                'donem' => $row->donem,
                'carpan' => $carpan,
                'tutar' => (float) ($row->tutar_toplam ?? 0),
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
