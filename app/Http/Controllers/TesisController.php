<?php

namespace App\Http\Controllers;

use App\Models\Aboneler;
use App\Models\Bolgeler;
use App\Models\Tesis;
use App\Models\TesisArac;
use App\Models\TesisArizaKaydi;
use App\Models\TesisArizaTuru;
use App\Models\TesisEkip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TesisController extends Controller
{
    public function index()
    {
        $tesisSayisi = Tesis::count();
        $aktifTesis = Tesis::where('durum', 'aktif')->count();
        $pasifTesis = Tesis::where('durum', 'pasif')->count();
        $arizaSayisi = TesisArizaKaydi::count();
        $aracSayisi = TesisArac::count();

        $arizaTurleri = TesisArizaKaydi::selectRaw('ariza_turu, count(*) as toplam')
            ->groupBy('ariza_turu')
            ->orderByDesc('toplam')
            ->limit(10)
            ->get();

        $ilceAriza = TesisArizaKaydi::selectRaw('ilce, count(*) as toplam')
            ->groupBy('ilce')
            ->orderByDesc('toplam')
            ->get();

        return view('tesis.index', compact(
            'tesisSayisi', 'aktifTesis', 'pasifTesis',
            'arizaSayisi', 'aracSayisi',
            'arizaTurleri', 'ilceAriza'
        ));
    }

    public function tesisler(Request $request)
    {
        $query = Tesis::with('abone');

        if ($request->filled('ilce')) {
            $query->where('ilce', $request->ilce);
        }
        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }
        if ($request->filled('arama')) {
            $arama = $request->arama;
            $query->where(function ($q) use ($arama) {
                $q->where('mahalle', 'like', "%$arama%")
                    ->orWhere('kuyu_no', 'like', "%$arama%")
                    ->orWhere('abone_no', 'like', "%$arama%");
            });
        }

        $tesisler = $query->latest()->paginate(20)->withQueryString();
        $ilceler = Tesis::select('ilce')->distinct()->orderBy('ilce')->pluck('ilce');

        return view('tesisler.index', compact('tesisler', 'ilceler'));
    }

    public function create()
    {
        $ilceler = Tesis::select('ilce')->distinct()->orderBy('ilce')->pluck('ilce');
        $aboneler = Aboneler::select('id', 'ABONE_TESIS_NO', 'UNVAN')->orderBy('ABONE_TESIS_NO')->get();

        return view('tesisler.create', compact('ilceler', 'aboneler'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'abone_id' => 'nullable|exists:aboneler,id',
            'durum' => 'required|in:aktif,pasif',
            'ilce' => 'required|string|max:100',
            'mahalle' => 'required|string|max:200',
            'sokak' => 'nullable|string|max:200',
            'kuyu_no' => 'nullable|string|max:50',
            'cbs_x' => 'nullable|numeric',
            'cbs_y' => 'nullable|numeric',
            'tesis_kurulma_tarihi' => 'nullable|date',
            'hibe_tarihi' => 'nullable|date',
            'abone_tipi' => 'nullable|string|max:20',
            'abone_tarihi' => 'nullable|date',
            'sayac_no' => 'nullable|string|max:100',
            'abone_no' => 'nullable|string|max:100',
            'trafo_gucu' => 'nullable|string|max:50',
            'trafo_seri_no' => 'nullable|string|max:100',
            'enh_durumu' => 'nullable|string|max:100',
            'demontaj_tarihi' => 'nullable|date',
            'demontaj_yapilan_malzemeler' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            Tesis::create($validated);
        });

        return redirect()->route('tesis-bilgi-sistemi.tesisler')->with('success', 'Tesis başarıyla eklendi.');
    }

    public function edit($id)
    {
        $tesis = Tesis::findOrFail($id);
        $ilceler = Tesis::select('ilce')->distinct()->orderBy('ilce')->pluck('ilce');
        $aboneler = Aboneler::select('id', 'ABONE_TESIS_NO', 'UNVAN')->orderBy('ABONE_TESIS_NO')->get();

        return view('tesisler.edit', compact('tesis', 'ilceler', 'aboneler'));
    }

    public function update(Request $request, $id)
    {
        $tesis = Tesis::findOrFail($id);

        $validated = $request->validate([
            'abone_id' => 'nullable|exists:aboneler,id',
            'durum' => 'required|in:aktif,pasif',
            'ilce' => 'required|string|max:100',
            'mahalle' => 'required|string|max:200',
            'sokak' => 'nullable|string|max:200',
            'kuyu_no' => 'nullable|string|max:50',
            'cbs_x' => 'nullable|numeric',
            'cbs_y' => 'nullable|numeric',
            'tesis_kurulma_tarihi' => 'nullable|date',
            'hibe_tarihi' => 'nullable|date',
            'abone_tipi' => 'nullable|string|max:20',
            'abone_tarihi' => 'nullable|date',
            'sayac_no' => 'nullable|string|max:100',
            'abone_no' => 'nullable|string|max:100',
            'trafo_gucu' => 'nullable|string|max:50',
            'trafo_seri_no' => 'nullable|string|max:100',
            'enh_durumu' => 'nullable|string|max:100',
            'demontaj_tarihi' => 'nullable|date',
            'demontaj_yapilan_malzemeler' => 'nullable|string',
        ]);

        DB::transaction(function () use ($tesis, $validated) {
            $tesis->update($validated);
        });

        return redirect()->route('tesis-bilgi-sistemi.tesisler')->with('success', 'Tesis başarıyla güncellendi.');
    }

    public function show($id)
    {
        $tesis = Tesis::with('abone')->findOrFail($id);

        return view('tesisler.show', compact('tesis'));
    }

    public function destroy($id)
    {
        $tesis = Tesis::findOrFail($id);

        DB::transaction(function () use ($tesis) {
            $tesis->delete();
        });

        return redirect()->route('tesis-bilgi-sistemi.tesisler')->with('success', 'Tesis başarıyla silindi.');
    }

    public function arizalar(Request $request)
    {
        $query = TesisArizaKaydi::with('abone');

        $aktifFiltre = $request->get('filter');

        if ($aktifFiltre === 'giderildi') {
            $query->where('durum', 'Arıza Giderildi');
        } elseif ($aktifFiltre === 'devam') {
            $query->where('durum', 'Devam Ediyor');
        } elseif ($aktifFiltre === 'bekleme') {
            $query->where('durum', 'Beklemede');
        }

        if ($request->filled('ilce')) {
            $query->where('ilce', $request->ilce);
        }
        if ($request->filled('ariza_turu')) {
            $query->where('ariza_turu', $request->ariza_turu);
        }
        if ($request->filled('durum') && ! $aktifFiltre) {
            $query->where('durum', $request->durum);
        }
        if ($request->filled('tarih_baslangic')) {
            $query->whereDate('tarih', '>=', $request->tarih_baslangic);
        }
        if ($request->filled('tarih_bitis')) {
            $query->whereDate('tarih', '<=', $request->tarih_bitis);
        }
        if ($request->filled('arama')) {
            $arama = $request->arama;
            $query->where(function ($q) use ($arama) {
                $q->where('mahalle', 'like', "%$arama%")
                    ->orWhere('ekip', 'like', "%$arama%")
                    ->orWhere('kuyu_no', 'like', "%$arama%");
            });
        }

        $arizalar = $query->latest('tarih')->paginate(20)->withQueryString();
        $ilceler = TesisArizaKaydi::select('ilce')->distinct()->orderBy('ilce')->pluck('ilce');
        $arizaTurleri = TesisArizaKaydi::select('ariza_turu')->distinct()->orderBy('ariza_turu')->pluck('ariza_turu');

        $toplamAriza = TesisArizaKaydi::count();
        $giderilenAriza = TesisArizaKaydi::where('durum', 'Arıza Giderildi')->count();
        $devamAriza = TesisArizaKaydi::where('durum', 'Devam Ediyor')->count();
        $beklemeAriza = TesisArizaKaydi::where('durum', 'Beklemede')->count();

        return view('tesis.arizalar', compact(
            'arizalar', 'ilceler', 'arizaTurleri',
            'toplamAriza', 'giderilenAriza', 'devamAriza', 'beklemeAriza', 'aktifFiltre'
        ));
    }

    public function arizaCreate()
    {
        $arizaTurleri = TesisArizaTuru::orderBy('ad')->get();
        $ekipler = TesisEkip::orderBy('ad')->get();
        $ilceler = Tesis::select('ilce')->distinct()->orderBy('ilce')->pluck('ilce');

        return view('tesis.ariza-create', compact('arizaTurleri', 'ekipler', 'ilceler'));
    }

    public function arizaCheckKuyu(Request $request)
    {
        $kuyuNo = $request->query('kuyu_no');
        if (! $kuyuNo) {
            return response()->json(['exists' => false]);
        }

        $exists = \App\Models\Kuyu::where('kuyu_no', $kuyuNo)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function arizaGetKuyuData(Request $request)
    {
        $kuyuNo = $request->query('kuyu_no');
        $aboneNo = $request->query('abone_no');

        if (! $kuyuNo && ! $aboneNo) {
            return response()->json(null);
        }

        $query = \App\Models\Kuyu::with('arizaKaydi');

        if ($kuyuNo) {
            $query->where('kuyu_no', $kuyuNo);
        } elseif ($aboneNo) {
            $query->where('abone_no', $aboneNo);
        }

        $kuyu = $query->first();

        if (! $kuyu) {
            return response()->json(null);
        }

        return response()->json([
            'abone_no' => $kuyu->abone_no ?? $kuyu->arizaKaydi->abone_no ?? null,
            'ilce' => $kuyu->ilce,
            'adres' => $kuyu->adres,
            'kuyu_no' => $kuyu->kuyu_no,
            'motor' => $kuyu->motor,
            'pompa' => $kuyu->pompa,
            'kablo' => $kuyu->kablo,
            'boru_tipi' => $kuyu->boru_tipi,
            'debi' => $kuyu->debi,
            'durum' => $kuyu->durum,
        ]);
    }

    public function arizaStore(Request $request)
    {
        $validated = $request->validate([
            'abone_id' => 'nullable|exists:aboneler,id',
            'tarih' => 'required|date',
            'ariza_turu' => 'required|string|max:200',
            'ilce' => 'required|string|max:100',
            'mahalle' => 'nullable|string|max:200',
            'sokak' => 'nullable|string|max:200',
            'kuyu_no' => 'nullable|string|max:50',
            'sayac_no' => 'nullable|string|max:100',
            'abone_no' => 'nullable|string|max:100',
            'tutanak_no' => 'nullable|string|max:100',
            'ekip' => 'nullable|string|max:100',
            'cbs_x' => 'nullable|numeric',
            'cbs_y' => 'nullable|numeric',
            'durum' => 'nullable|string|max:50',
            'aciklama' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $maxSira = TesisArizaKaydi::max('sira_no') ?? 0;
            $validated['sira_no'] = $maxSira + 1;
            $validated['durum'] = $validated['durum'] ?? 'Arıza Kaydı Yapıldı';
            TesisArizaKaydi::create($validated);
        });

        return redirect()->route('tesis-bilgi-sistemi.arizalar')
            ->with('success', 'Arıza kaydı başarıyla eklendi.');
    }

    public function arizaEdit($id)
    {
        $ariza = TesisArizaKaydi::findOrFail($id);
        $arizaTurleri = TesisArizaTuru::orderBy('ad')->get();
        $ekipler = TesisEkip::orderBy('ad')->get();
        $ilceler = Tesis::select('ilce')->distinct()->orderBy('ilce')->pluck('ilce');

        return view('tesis.ariza-edit', compact('ariza', 'arizaTurleri', 'ekipler', 'ilceler'));
    }

    public function arizaUpdate(Request $request, $id)
    {
        $ariza = TesisArizaKaydi::findOrFail($id);

        $validated = $request->validate([
            'tarih' => 'required|date',
            'ariza_turu' => 'required|string|max:200',
            'ilce' => 'required|string|max:100',
            'mahalle' => 'nullable|string|max:200',
            'sokak' => 'nullable|string|max:200',
            'kuyu_no' => 'nullable|string|max:50',
            'sayac_no' => 'nullable|string|max:100',
            'abone_no' => 'nullable|string|max:100',
            'tutanak_no' => 'nullable|string|max:100',
            'ekip' => 'nullable|string|max:100',
            'cbs_x' => 'nullable|numeric',
            'cbs_y' => 'nullable|numeric',
            'durum' => 'required|string|max:50',
            'aciklama' => 'nullable|string',
        ]);

        $ariza->update($validated);

        return redirect()->route('tesis-bilgi-sistemi.arizalar')
            ->with('success', 'Arıza kaydı başarıyla güncellendi.');
    }

    public function arizaDestroy($id)
    {
        $ariza = TesisArizaKaydi::findOrFail($id);
        $ariza->delete();

        return redirect()->route('tesis-bilgi-sistemi.arizalar')
            ->with('success', 'Arıza kaydı başarıyla silindi.');
    }

    public function arizaUpdateStatus(Request $request, $id)
    {
        $ariza = TesisArizaKaydi::findOrFail($id);

        $validated = $request->validate([
            'durum' => 'required|in:Arıza Kaydı Yapıldı,Devam Ediyor,Arıza Giderildi,Beklemede',
        ]);

        $ariza->update(['durum' => $validated['durum']]);

        return redirect()->route('tesis-bilgi-sistemi.arizalar')
            ->with('success', "Arıza kaydı durumu \"{$validated['durum']}\" olarak güncellendi.");
    }

    public function arizaTesisByAbone(Request $request)
    {
        $aboneNo = $request->query('abone_no');
        if (! $aboneNo) {
            return response()->json([]);
        }

        $tesis = Tesis::where('abone_no', $aboneNo)->first();

        if (! $tesis) {
            return response()->json(null);
        }

        return response()->json([
            'id' => $tesis->id,
            'ilce' => $tesis->ilce,
            'mahalle' => $tesis->mahalle,
            'sokak' => $tesis->sokak,
            'kuyu_no' => $tesis->kuyu_no,
            'sayac_no' => $tesis->sayac_no,
            'abone_no' => $tesis->abone_no,
            'abone_id' => $tesis->abone_id,
            'cbs_x' => $tesis->cbs_x,
            'cbs_y' => $tesis->cbs_y,
        ]);
    }

    public function araclar(Request $request)
    {
        $query = TesisArac::query();

        if ($request->filled('arama')) {
            $arama = $request->arama;
            $query->where(function ($q) use ($arama) {
                $q->where('plaka', 'like', "%$arama%")
                    ->orWhere('kullanici_personel', 'like', "%$arama%")
                    ->orWhere('aracin_cinsi', 'like', "%$arama%");
            });
        }

        $araclar = $query->orderBy('sira_no')->paginate(20)->withQueryString();

        return view('tesis.araclar', compact('araclar'));
    }

    public function aracUpdate(Request $request, $id)
    {
        $request->validate([
            'plaka' => 'required|string|max:255',
            'aracin_cinsi' => 'nullable|string|max:255',
            'arac_tipi' => 'nullable|string|max:255',
            'kullanici_personel' => 'nullable|string|max:255',
            'irtibat' => 'nullable|string|max:255',
            'kullanildigi_is' => 'nullable|string|max:255',
        ]);

        $arac = TesisArac::findOrFail($id);
        $arac->update($request->only([
            'plaka', 'aracin_cinsi', 'arac_tipi',
            'kullanici_personel', 'irtibat', 'kullanildigi_is',
        ]));

        return redirect()->route('tesis-bilgi-sistemi.araclar')->with('success', 'Araç başarıyla güncellendi.');
    }

    public function aracDestroy($id)
    {
        $arac = TesisArac::findOrFail($id);
        $arac->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tesis-bilgi-sistemi.araclar')->with('success', 'Araç başarıyla silindi.');
    }

    public function aracEkle()
    {
        $maxSira = TesisArac::max('sira_no');

        return view('tesis.arac-ekle', compact('maxSira'));
    }

    public function aracStore(Request $request)
    {
        $request->validate([
            'plaka' => 'required|string|max:50',
            'aracin_cinsi' => 'required|string|max:100',
            'arac_tipi' => 'nullable|string|max:100',
            'kullanici_personel' => 'nullable|string|max:200',
            'irtibat' => 'nullable|string|max:100',
            'kullanildigi_is' => 'nullable|string|max:200',
        ]);

        $siraNo = $request->filled('sira_no') ? $request->sira_no : (TesisArac::max('sira_no') + 1);

        TesisArac::create([
            'sira_no' => $siraNo,
            'plaka' => $request->plaka,
            'aracin_cinsi' => $request->aracin_cinsi,
            'arac_tipi' => $request->arac_tipi,
            'kullanici_personel' => $request->kullanici_personel,
            'irtibat' => $request->irtibat,
            'kullanildigi_is' => $request->kullanildigi_is,
        ]);

        return redirect()->route('tesis-bilgi-sistemi.araclar')->with('success', 'Araç başarıyla eklendi.');
    }

    private function getArizaExcelData()
    {
        $cacheKey = 'ariza_excel_data_v3';

        return cache()->rememberForever($cacheKey, function () {
            $yillar = [2023, 2024, 2025, 2026];
            $ayIsimleri = ['', 'OCAK', 'ŞUBAT', 'MART', 'NİSAN', 'MAYIS', 'HAZİRAN', 'TEMMUZ', 'AĞUSTOS', 'EYLÜL', 'EKİM', 'KASIM', 'ARALIK'];

            $raw = DB::table('tesis_ariza_kayitlari')
                ->selectRaw('ilce, YEAR(tarih) as yil, MONTH(tarih) as ay, COUNT(*) as adet')
                ->whereNotNull('tarih')
                ->whereYear('tarih', '>=', 2023)
                ->whereYear('tarih', '<=', 2026)
                ->groupBy('ilce', 'yil', 'ay')
                ->orderBy('ilce')
                ->get();

            $grouped = collect($raw)->groupBy('ilce');
            $ilceBazli = [];
            $yilToplam = array_fill_keys($yillar, 0);

            foreach ($grouped as $ilce => $items) {
                if (empty(trim($ilce))) {
                    continue;
                }

                $ilceVeri = ['ilce' => $ilce, 'aylar' => []];
                foreach ($yillar as $yi) {
                    $ilceVeri['yil_toplam'][$yi] = 0;
                }

                for ($ay = 1; $ay <= 12; $ay++) {
                    $ayVeri = ['ay' => $ay, 'ay_adi' => $ayIsimleri[$ay]];
                    foreach ($yillar as $yi) {
                        $val = $items->where('yil', $yi)->where('ay', $ay)->sum('adet');
                        $ayVeri[$yi] = $val;
                        $ilceVeri['yil_toplam'][$yi] += $val;
                    }
                    $ilceVeri['aylar'][] = $ayVeri;
                }

                $ilceBazli[] = $ilceVeri;

                foreach ($yillar as $yi) {
                    $yilToplam[$yi] += $ilceVeri['yil_toplam'][$yi];
                }
            }

            return compact('yillar', 'ilceBazli', 'yilToplam');
        });
    }

    public function arizaRaporYillik(Request $request)
    {
        $data = $this->getArizaExcelData();
        $yillar = $data['yillar'];
        $ilceBazli = $data['ilceBazli'];
        $yilToplam = $data['yilToplam'];

        $selectedYil = $request->yil ? (int) $request->yil : 2026;

        $chartLabels = [];
        $chartValues = [];
        $ayAdlari = ['', 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
        foreach (range(1, 12) as $ay) {
            $chartLabels[] = $ayAdlari[$ay];
            $toplam = 0;
            foreach ($ilceBazli as $ib) {
                foreach ($ib['aylar'] as $a) {
                    if ($a['ay'] == $ay) {
                        $toplam += $a[$selectedYil] ?? 0;
                    }
                }
            }
            $chartValues[] = $toplam;
        }

        $ilceChartLabels = [];
        $ilceChartValues = [];
        foreach ($ilceBazli as $ib) {
            $ilceChartLabels[] = $ib['ilce'];
            $ilceChartValues[] = $ib['yil_toplam'][$selectedYil] ?? 0;
        }

        $ayIsimleri = ['', 'OCAK', 'ŞUBAT', 'MART', 'NİSAN', 'MAYIS', 'HAZİRAN', 'TEMMUZ', 'AĞUSTOS', 'EYLÜL', 'EKİM', 'KASIM', 'ARALIK'];

        return view('tesis.ariza-raporlari-yillik', compact('yillar', 'ilceBazli', 'yilToplam', 'selectedYil', 'ayIsimleri', 'chartLabels', 'chartValues', 'ilceChartLabels', 'ilceChartValues'));
    }

    public function yillikAriza(Request $request)
    {
        $results = collect();
        $query = TesisArizaKaydi::query()
            ->whereYear('tarih', '>=', 2023)
            ->whereYear('tarih', '<=', 2026);

        if ($request->filled('bolge')) {
            $selectedBolge = (array) $request->bolge;
            $query->where(function ($q) use ($selectedBolge) {
                foreach ($selectedBolge as $b) {
                    $q->orWhereRaw('ilce COLLATE utf8mb4_turkish_ci = ?', [$b]);
                }
            });
        }
        if ($request->filled('yil')) {
            $query->whereYear('tarih', (int) $request->yil);
        }
        if ($request->filled('ariza_turu')) {
            $query->whereIn('ariza_turu', (array) $request->ariza_turu);
        }
        if ($request->filled('ekip')) {
            $query->whereIn('ekip', (array) $request->ekip);
        }
        $hasFilter = $request->anyFilled(['bolge', 'yil', 'ariza_turu', 'ekip']);

        if ($hasFilter) {
            $results = $query->selectRaw('YEAR(tarih) as yil, ilce, COUNT(*) as toplam')
                ->groupByRaw('YEAR(tarih), ilce')
                ->orderBy('yil', 'desc')
                ->orderBy('toplam', 'desc')
                ->get();

            if ($request->ajax()) {
                return view('tesis.yillik-ariza-table', compact('results'))->render();
            }
        }

        $yillar = TesisArizaKaydi::selectRaw('YEAR(tarih) as yil')
            ->whereYear('tarih', '>=', 2023)->whereYear('tarih', '<=', 2026)
            ->distinct()->whereNotNull('tarih')->orderBy('yil', 'desc')->pluck('yil');
        $bolgeler = Bolgeler::orderBy('bolge_adi')->pluck('bolge_adi');
        $arizaTurleri = TesisArizaTuru::orderBy('ad')->pluck('ad');
        $ekipler = TesisEkip::orderBy('ad')->pluck('ad');

        if ($request->filled('export') && $results->count() > 0) {
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            if ($request->export === 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\ArizaExport($results, $request->all()),
                    'Yillik_Ariza_Raporu.xlsx'
                );
            } elseif ($request->export === 'pdf') {
                $filters = $request->only(['bolge', 'yil', 'ariza_turu', 'ekip']);
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tesis.yillik-ariza-pdf', compact('results', 'filters'));

                return $pdf->download('Yillik_Ariza_Raporu.pdf');
            }
        }

        return view('tesis.yillik-ariza', compact('results', 'yillar', 'bolgeler', 'arizaTurleri', 'ekipler'));
    }

    public function arizaRaporYillikDetayAjax($yil, $ay)
    {
        $data = $this->getArizaExcelData();
        $yillar = $data['yillar'];
        $ilceBazli = $data['ilceBazli'];

        $yiIdx = array_search((int) $yil, $yillar);
        if ($yiIdx === false) {
            return response()->json([]);
        }

        $result = [];
        foreach ($ilceBazli as $ib) {
            foreach ($ib['aylar'] as $a) {
                if ($a['ay'] == (int) $ay) {
                    $val = $a[$yil] ?? 0;
                    if ($val > 0) {
                        $result[] = ['ilce' => $ib['ilce'], 'toplam' => $val];
                    }
                }
            }
        }

        usort($result, fn ($a, $b) => $b['toplam'] - $a['toplam']);

        return response()->json($result);
    }
}
