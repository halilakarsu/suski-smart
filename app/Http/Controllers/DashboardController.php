<?php

namespace App\Http\Controllers;

use App\Models\Aboneler;
use App\Models\KesinlesenFatura;

class DashboardController extends Controller
{
    public function index()
    {
        $abone = [
            'toplam' => Aboneler::count(),
            'bolge' => Aboneler::whereNotNull('BOLGE_ADI')->where('BOLGE_ADI', '!=', '')->distinct('BOLGE_ADI')->count('BOLGE_ADI'),
        ];

        $odenen = KesinlesenFatura::where('odeme_durumu', 'odendi')->count();

        $merkezDagilim = Aboneler::selectRaw('BOLGE_ADI, COUNT(*) as adet')
            ->whereNotNull('BOLGE_ADI')
            ->where('BOLGE_ADI', '!=', '')
            ->where('yerlesim_turu', 'MERKEZ')
            ->groupBy('BOLGE_ADI')
            ->orderByDesc('adet')
            ->get();

        $koyDagilim = Aboneler::selectRaw('BOLGE_ADI, COUNT(*) as adet')
            ->whereNotNull('BOLGE_ADI')
            ->where('BOLGE_ADI', '!=', '')
            ->where('yerlesim_turu', 'KÖY')
            ->groupBy('BOLGE_ADI')
            ->orderByDesc('adet')
            ->get();

        return view('frontend.home.index', compact(
            'abone',
            'odenen',
            'merkezDagilim',
            'koyDagilim'
        ));
    }
}
