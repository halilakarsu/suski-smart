<?php

namespace App\Http\Controllers;

use App\Models\Kuyu;
use Illuminate\Http\Request;

class KuyuEnvanterController extends Controller
{
    public function index(Request $request)
    {
        $query = Kuyu::query();
        $aktifFiltre = $request->get('filter');

        if ($aktifFiltre === 'pasif') {
            $query->where('durum', 'pasif');
        } elseif ($aktifFiltre === 'abonesiz') {
            $query->whereNull('abone_no');
        } elseif ($aktifFiltre === 'cbssiz') {
            $query->whereNull('cbs_x');
        } elseif ($aktifFiltre === 'kuyusuz') {
            $query->whereNotNull('abone_no')->whereNull('kuyu_no');
        } else {
            $aktifFiltre = 'all';
        }

        if ($request->filled('durum') && $aktifFiltre === 'all') {
            $query->where('durum', $request->durum);
        }

        if ($request->filled('ilce')) {
            $query->where('ilce', $request->ilce);
        }

        if ($request->filled('arama')) {
            $arama = $request->arama;
            $query->where(function ($q) use ($arama) {
                $q->where('kuyu_no', 'like', "%$arama%")
                    ->orWhere('abone_no', 'like', "%$arama%")
                    ->orWhere('adres', 'like', "%$arama%")
                    ->orWhere('motor', 'like', "%$arama%")
                    ->orWhere('pompa', 'like', "%$arama%")
                    ->orWhere('ilce', 'like', "%$arama%");
            });
        }

        $kuyular = $query->with('arizaKaydi')->orderBy('ilce')->orderBy('kuyu_no')->paginate(25)->withQueryString();
        $ilceler = Kuyu::select('ilce')->whereNotNull('ilce')->distinct()->orderBy('ilce')->pluck('ilce');

        $toplamKuyu = Kuyu::count();
        $pasifKuyu = Kuyu::where('durum', 'pasif')->count();
        $abonesizKuyu = Kuyu::whereNull('abone_no')->count();
        $cbssizKuyu = Kuyu::whereNull('cbs_x')->count();
        $kuyusuzKuyu = Kuyu::whereNotNull('abone_no')->whereNull('kuyu_no')->count();

        return view('kuyu-envanter.index', compact(
            'kuyular', 'ilceler',
            'toplamKuyu', 'pasifKuyu', 'abonesizKuyu', 'cbssizKuyu', 'kuyusuzKuyu', 'aktifFiltre'
        ));
    }

    public function create()
    {
        $ilceler = Kuyu::select('ilce')->whereNotNull('ilce')->distinct()->orderBy('ilce')->pluck('ilce');

        return view('kuyu-envanter.create', compact('ilceler'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kuyu_no' => 'nullable|string|max:50',
            'abone_no' => 'nullable|string|max:100',
            'ilce' => 'nullable|string|max:100',
            'adres' => 'nullable|string|max:500',
            'cbs_x' => 'nullable|numeric',
            'cbs_y' => 'nullable|numeric',
            'demontaj_derinligi' => 'nullable|numeric|min:0',
            'montaj_derinligi' => 'nullable|numeric|min:0',
            'depo_bilgisi' => 'nullable|string|max:300',
            'boru_tipi' => 'nullable|string|max:200',
            'kablo' => 'nullable|string|max:200',
            'motor' => 'nullable|string|max:300',
            'pompa' => 'nullable|string|max:300',
            'debi' => 'nullable|string|max:100',
            'aciklama' => 'nullable|string',
            'durum' => 'required|in:aktif,pasif',
            'olusturulma_tarihi' => 'nullable|date',
            'guncellenme_tarihi' => 'nullable|date',
        ]);

        Kuyu::create($validated);

        return redirect()->route('kuyu-envanteri.index')
            ->with('success', 'Kuyu başarıyla eklendi.');
    }

    public function show(Kuyu $kuyu)
    {
        return view('kuyu-envanter.show', compact('kuyu'));
    }

    public function edit(Kuyu $kuyu)
    {
        $ilceler = Kuyu::select('ilce')->whereNotNull('ilce')->distinct()->orderBy('ilce')->pluck('ilce');

        return view('kuyu-envanter.edit', compact('kuyu', 'ilceler'));
    }

    public function update(Request $request, Kuyu $kuyu)
    {
        $validated = $request->validate([
            'kuyu_no' => 'nullable|string|max:50',
            'abone_no' => 'nullable|string|max:100',
            'ilce' => 'nullable|string|max:100',
            'adres' => 'nullable|string|max:500',
            'cbs_x' => 'nullable|numeric',
            'cbs_y' => 'nullable|numeric',
            'demontaj_derinligi' => 'nullable|numeric|min:0',
            'montaj_derinligi' => 'nullable|numeric|min:0',
            'depo_bilgisi' => 'nullable|string|max:300',
            'boru_tipi' => 'nullable|string|max:200',
            'kablo' => 'nullable|string|max:200',
            'motor' => 'nullable|string|max:300',
            'pompa' => 'nullable|string|max:300',
            'debi' => 'nullable|string|max:100',
            'aciklama' => 'nullable|string',
            'durum' => 'required|in:aktif,pasif',
            'olusturulma_tarihi' => 'nullable|date',
            'guncellenme_tarihi' => 'nullable|date',
        ]);

        $kuyu->update($validated);

        return redirect()->route('kuyu-envanteri.index')
            ->with('success', 'Kuyu başarıyla güncellendi.');
    }

    public function destroy(Kuyu $kuyu)
    {
        $kuyu->delete();

        return redirect()->route('kuyu-envanteri.index')
            ->with('success', 'Kuyu başarıyla silindi.');
    }
}
