<?php

namespace App\Http\Controllers;

use App\Models\Reaktifler;
use Illuminate\Http\Request;

class ReaktiflerController extends Controller
{
    public function index(Request $request)
    {
        $query = Reaktifler::with(['importLog', 'aktarimYapan'])->latest();

        // Filtrelemeler
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%'.$request->fatura_no.'%');
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
        }
        if ($request->filled('donem')) {
            $query->where('donem', $request->donem);
        }

        $reaktifler = $query->paginate(50)->withQueryString();

        $donemler = Reaktifler::select('donem')->distinct()->pluck('donem')->filter()->values();

        // Üst panel (Hero) için istatistikler
        $stats = [
            'itiraz' => \App\Models\KesinlesenFatura::where('itiraz_edildi', true)->count(),
            'reaktif' => Reaktifler::count(),
            'bekleyen' => \App\Models\BeklemeKontrolHavuzu::count(),
        ];

        return view('fatura.reaktifler', compact('reaktifler', 'donemler', 'stats'));
    }
}
