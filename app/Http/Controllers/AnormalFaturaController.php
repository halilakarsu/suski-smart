<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\NormalizesIlce;
use App\Models\AnormalFatura;
use App\Models\KesinlesenFatura;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class AnormalFaturaController extends Controller
{
    use NormalizesIlce;

    public function index(Request $request)
    {
        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')->distinct()->orderBy('donem', 'desc')->pluck('donem');
        $bolgeler = $this->getBolgelerList();
        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();
        $totals = (object) [
            'total_fatura' => 0,
            'total_tuketim' => 0,
            'total_tutar' => 0,
        ];

        if (!Schema::hasTable('anormal_faturalar')) {
            $results = new LengthAwarePaginator([], 0, 50, LengthAwarePaginator::resolveCurrentPage(), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return view('reports.anormal-faturalar', compact('results', 'donemler', 'bolgeler', 'tarifeler', 'totals'));
        }

        $query = AnormalFatura::with(['fatura', 'user'])->where('durum', 'kaydedildi');

        if ($request->filled('bolge')) {
            $this->applyBolgeFilter($query, (array) $request->bolge);
        }
        if ($request->filled('start_period')) {
            if ($request->filled('end_period')) {
                $query->where('donem', '>=', $request->start_period)
                    ->where('donem', '<=', $request->end_period);
            } else {
                $query->where('donem', $request->start_period);
            }
        } elseif ($request->filled('end_period')) {
            $query->where('donem', '<=', $request->end_period);
        }
        if ($request->filled('tesisat_no')) {
            $query->where(function ($q) use ($request) {
                $q->where('tesisat_no', 'like', '%' . $request->tesisat_no . '%')
                    ->orWhere('abone_tesis_no', 'like', '%' . $request->tesisat_no . '%');
            });
        }
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%' . $request->fatura_no . '%');
        }
        if ($request->filled('baglanti_grubu')) {
            $query->where('baglanti_grubu', $request->baglanti_grubu);
        }
        if ($request->filled('yerlesim_tipi')) {
            $typeMap = ['koy' => 'KÖY', 'merkez' => 'MERKEZ'];
            if (isset($typeMap[$request->yerlesim_tipi])) {
                $query->where('yerlesim_turu', $typeMap[$request->yerlesim_tipi]);
            }
        }
        if ($request->filled('tarife')) {
            $query->whereIn('tarife', (array) $request->tarife);
        }

        $totals = (object) [
            'total_fatura' => (clone $query)->count(),
            'total_tuketim' => (clone $query)->sum('fatura_edilecek_toplam_tuketim_kwh'),
            'total_tutar' => (clone $query)->sum('tutar_toplam'),
        ];

        $results = $query->latest()->paginate(50)->withQueryString();

        return view('reports.anormal-faturalar', compact('results', 'donemler', 'bolgeler', 'tarifeler', 'totals'));
    }
}
