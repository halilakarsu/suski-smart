<?php

namespace App\Http\Controllers;

use App\Exports\OdenenFaturalarExport;
use App\Models\KesinlesenFatura;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KesinlesenFaturaController extends Controller
{
    /**
     * Ödenen (odeme_durumu = odendi) faturaların listesi
     */
    public function odenenler(Request $request)
    {
        $selectedYil = $request->yil;

        // Tablo sadece dönem veya spesifik filtre ile gösterilir; yalnızca yıl seçmek yetmez
        $tableFilter = $request->filled('donem') ||
            $request->filled('fatura_no') ||
            $request->filled('tesisat_no') ||
            $request->filled('bolge_kodu');

        $baseQuery = KesinlesenFatura::with(['user', 'abone'])->where('odeme_durumu', 'odendi');

        if ($selectedYil) {
            if ($request->filled('donem') && ! str_starts_with($request->donem, $selectedYil)) {
                // skip — donem yılla uyuşmuyor
            } else {
                $baseQuery->where('donem', 'like', $selectedYil.'%');
            }
        }
        if ($request->filled('donem')) {
            if (! $selectedYil || str_starts_with($request->donem, $selectedYil)) {
                $baseQuery->where('donem', $request->donem);
            }
        }
        if ($request->filled('fatura_no')) {
            $baseQuery->where('fatura_no', 'like', '%'.$request->fatura_no.'%');
        }
        if ($request->filled('tesisat_no')) {
            $baseQuery->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
        }
        if ($request->filled('bolge_kodu')) {
            $bolgeAdi = \App\Models\Bolgeler::where('bolge_kodu', $request->bolge_kodu)->value('bolge_adi');
            if ($bolgeAdi) {
                $baseQuery->where(function ($q) use ($bolgeAdi, $request) {
                    $q->where('ilce', 'like', '%'.$bolgeAdi.'%')
                        ->orWhereHas('abone', function ($sq) use ($request) {
                            $sq->where('BOLGE_KODU', $request->bolge_kodu);
                        });
                });
            }
        }

        if ($tableFilter) {
            $query = clone $baseQuery;

            $statsRaw = (clone $query)
                ->selectRaw('count(*) as count')
                ->selectRaw('sum(fatura_edilecek_toplam_tuketim_kwh) as total_tuketim')
                ->selectRaw('sum(tutar_toplam) as total_tutar')
                ->first();

            $filteredStats = [
                'count' => $statsRaw->count ?? 0,
                'total_tutar' => $statsRaw->total_tutar ?? 0,
                'total_tuketim' => $statsRaw->total_tuketim ?? 0,
            ];

            $query->latest();
            $perPage = $request->filled('donem') ? 5 : 20;
            $faturalar = $query->paginate($perPage)->withQueryString();
        } else {
            $faturalar = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $filteredStats = ['count' => 0, 'total_tutar' => 0, 'total_tuketim' => 0];
        }

        $donemler = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->orderByDesc('donem')->pluck('donem')->unique()->values();

        $yillar = $donemler->map(fn ($d) => explode('-', $d)[0])->unique()->values();

        $bolgeMap = \App\Models\Bolgeler::pluck('bolge_adi', 'bolge_kodu')->toArray();

        // Üst yıl kartları — her zaman yüklenir
        $yilStats = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->selectRaw('SUBSTRING(donem, 1, 4) as yil,
                         count(DISTINCT donem)   as donem_count,
                         count(*)                as fatura_count,
                         sum(tutar_toplam)        as total_tutar,
                         sum(fatura_edilecek_toplam_tuketim_kwh) as total_tuketim')
            ->groupBy('yil')
            ->orderByDesc('yil')
            ->get();

        if (! $selectedYil) {
            $selectedYil = $yilStats->first()?->yil;
        }

        // Content dönem kartları — yalnızca yıl seçilince yüklenir
        $donemStats = collect();
        if ($selectedYil) {
            $donemStats = KesinlesenFatura::where('odeme_durumu', 'odendi')
                ->where('donem', 'like', $selectedYil.'%')
                ->selectRaw('donem, count(*) as count, sum(tutar_toplam) as total_tutar, sum(fatura_edilecek_toplam_tuketim_kwh) as total_tuketim')
                ->groupBy('donem')
                ->orderByDesc('donem')
                ->get();
        }

        return view('fatura.odenenler', compact(
            'faturalar',
            'donemler',
            'yillar',
            'bolgeMap',
            'filteredStats',
            'yilStats',
            'donemStats',
            'selectedYil'
        ));
    }


    /** AJAX: Detayları dön */
    public function show($id)
    {
        $fatura = KesinlesenFatura::findOrFail($id);

        return response()->json($fatura);
    }

    public function exportOdenenlerPDF(Request $request)
    {
        if (! $request->anyFilled(['donem', 'yil', 'fatura_no', 'tesisat_no', 'bolge_kodu'])) {
            return back()->with('error', 'Lütfen önce bir dönem, yıl veya filtre seçerek tabloyu yükleyin.');
        }

        set_time_limit(600);
        ini_set('memory_limit', '-1'); // Sınırsız bellek (DomPDF çok bellek tüketir)

        $query = KesinlesenFatura::with(['user', 'abone'])->where('odeme_durumu', 'odendi')->orderBy('tutar_toplam', 'desc');

        if ($request->filled('yil')) {
            $yil = $request->yil;
            if ($request->filled('donem') && ! str_starts_with($request->donem, $yil)) {
                // skip
            } else {
                $query->where('donem', 'like', $yil.'%');
            }
        }

        if ($request->filled('donem')) {
            $yil = $request->yil;
            if (! $request->filled('yil') || str_starts_with($request->donem, $yil)) {
                $query->where('donem', $request->donem);
            }
        }
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%'.$request->fatura_no.'%');
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
        }
        if ($request->filled('bolge_kodu')) {
            $bolgeAdi = \App\Models\Bolgeler::where('bolge_kodu', $request->bolge_kodu)->value('bolge_adi');
            if ($bolgeAdi) {
                $query->where(function ($q) use ($bolgeAdi, $request) {
                    $q->where('ilce', 'like', '%'.$bolgeAdi.'%')
                        ->orWhereHas('abone', function ($sq) use ($request) {
                            $sq->where('BOLGE_KODU', $request->bolge_kodu);
                        });
                });
            }
        }

        // Çok büyük PDF'ler için uyarı ve limit eklenebilir, şimdilik veriyi alıyoruz
        $faturalar = $query->get();

        if ($faturalar->isEmpty()) {
            return back()->with('error', 'Seçilen kriterlere uygun fatura bulunamadı.');
        }

        if ($faturalar->count() > 5000) {
            return back()->with('error', 'Dışa aktarmak istediğiniz veri çok büyük ('.$faturalar->count().' kayıt). Lütfen daha dar bir filtre uygulayın veya Excel kullanın.');
        }

        $totalKWH = $faturalar->sum('fatura_edilecek_toplam_tuketim_kwh');
        $totalAmount = $faturalar->sum('tutar_toplam');

        $periodTitle = 'Tüm Dönemlere Ait Ödenen Faturalar';
        if ($request->filled('donem')) {
            try {
                $date = \Carbon\Carbon::createFromFormat('Y-m', $request->donem);
                $aylar = [
                    1 => 'Ocak',
                    2 => 'Şubat',
                    3 => 'Mart',
                    4 => 'Nisan',
                    5 => 'Mayıs',
                    6 => 'Haziran',
                    7 => 'Temmuz',
                    8 => 'Ağustos',
                    9 => 'Eylül',
                    10 => 'Ekim',
                    11 => 'Kasım',
                    12 => 'Aralık',
                ];
                $ayAdi = $aylar[$date->month];
                $periodTitle = $date->year.' yılı '.strtolower($ayAdi).' ayına ait ödenen faturalar';
            } catch (\Exception $e) {
                $periodTitle = $request->donem.' Dönemine Ait Ödenen Faturalar';
            }
        } elseif ($request->filled('yil')) {
            $periodTitle = $request->yil.' Yılına Ait Ödenen Faturalar';
        }

        $pdf = Pdf::loadView('fatura.kesinlesen-pdf', compact(
            'faturalar',
            'totalKWH',
            'totalAmount',
            'periodTitle'
        ))->setPaper('a4', 'landscape');

        $cleanDonem = str_replace(['-', ' '], '_', $request->donem ?? $request->yil ?? 'Tum');
        $filename = "Odenen_Faturalar_{$cleanDonem}.pdf";

        if ($request->filled('preview') && $request->preview == '1') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    public function exportOdenenlerExcel(Request $request)
    {
        if (! $request->anyFilled(['donem', 'yil', 'fatura_no', 'tesisat_no', 'bolge_kodu'])) {
            return back()->with('error', 'Lütfen önce bir dönem, yıl veya filtre seçerek tabloyu yükleyin.');
        }

        set_time_limit(600);
        ini_set('memory_limit', '-1');

        $query = KesinlesenFatura::with(['user', 'abone'])->where('odeme_durumu', 'odendi')->orderBy('tutar_toplam', 'desc');

        if ($request->filled('yil')) {
            $yil = $request->yil;
            if ($request->filled('donem') && ! str_starts_with($request->donem, $yil)) {
                // skip
            } else {
                $query->where('donem', 'like', $yil.'%');
            }
        }
        if ($request->filled('donem')) {
            $yil = $request->yil;
            if (! $request->filled('yil') || str_starts_with($request->donem, $yil)) {
                $query->where('donem', $request->donem);
            }
        }
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%'.$request->fatura_no.'%');
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
        }
        if ($request->filled('bolge_kodu')) {
            $bolgeAdi = \App\Models\Bolgeler::where('bolge_kodu', $request->bolge_kodu)->value('bolge_adi');
            if ($bolgeAdi) {
                $query->where(function ($q) use ($bolgeAdi, $request) {
                    $q->where('ilce', 'like', '%'.$bolgeAdi.'%')
                        ->orWhereHas('abone', function ($sq) use ($request) {
                            $sq->where('BOLGE_KODU', $request->bolge_kodu);
                        });
                });
            }
        }

        $faturalar = $query->get();
        if ($faturalar->isEmpty()) {
            return back()->with('error', 'Seçilen kriterlere uygun fatura bulunamadı.');
        }

        $totalKWH = $faturalar->sum('fatura_edilecek_toplam_tuketim_kwh');
        $totalAmount = $faturalar->sum('tutar_toplam');
        $donemLabel = $request->donem ?? ($request->yil ? $request->yil.'_Yili' : 'Tum');
        $cleanDonem = str_replace(['-', ' '], '_', $donemLabel);
        $filename = "Odenen_Faturalar_{$cleanDonem}.xlsx";

        return Excel::download(
            new OdenenFaturalarExport($faturalar, $totalKWH, $totalAmount, $donemLabel),
            $filename
        );
    }

    public function itirazEt(Request $request, $id)
    {
        $request->validate([
            'itiraz_aciklamasi' => 'required|string|max:1000',
        ]);

        $fatura = KesinlesenFatura::findOrFail($id);

        // 1. Durumu güncelle
        $fatura->update([
            'itiraz_edildi' => true,
            'itiraz_aciklamasi' => $request->itiraz_aciklamasi,
        ]);

        // 2. İtiraz Edilenler tablosuna KOPYALA (Silme yapmıyoruz)
        $data = $fatura->toArray();
        unset($data['id']);
        $data['itiraz_edildi'] = true;
        $data['itiraz_aciklamasi'] = $request->itiraz_aciklamasi;
        $data['user_id'] = \Illuminate\Support\Facades\Auth::id();
        $data['durum'] = 'bekliyor';

        \App\Models\ItirazEdilenler::create($data);

        return response()->json(['success' => true]);
    }

    /**
     * İtirazı geri al
     */
    public function itirazIptal($id)
    {
        $fatura = KesinlesenFatura::findOrFail($id);
        $fatura->update([
            'itiraz_edildi' => false,
            'itiraz_aciklamasi' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function itirazlar(Request $request)
    {
        $query = \App\Models\ItirazEdilenler::with(['user']);

        if ($request->filled('donem')) {
            $query->where('donem', $request->donem);
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
        }
        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }

        $itirazlar = $query->latest()->paginate(20)->withQueryString();

        return view('fatura.itirazlar', compact('itirazlar'));
    }

    /**
     * İtiraz Edilen Faturayı Kaldır / Eski Tabloya Geri Döndür
     */
    public function itirazKaldir(Request $request, $id)
    {
        $request->validate([
            'sonuc_notu' => 'required|string',
        ]);

        $itiraz = \App\Models\ItirazEdilenler::findOrFail($id);

        // Kesinleşen Faturayı bul
        $fatura = KesinlesenFatura::where('fatura_no', $itiraz->fatura_no)
            ->where('tesisat_no', $itiraz->tesisat_no)
            ->where('donem', $itiraz->donem)
            ->first();

        if ($fatura) {
            // Mevcut kaydın itirazını kaldır
            $fatura->itiraz_edildi = false;
            $fatura->itiraz_aciklamasi = null;

            $payload = $fatura->payload ?? [];
            if (! isset($payload['_itiraz_gecmisi'])) {
                $payload['_itiraz_gecmisi'] = [];
            }

            $payload['_itiraz_gecmisi'][] = [
                'aciklama' => $itiraz->itiraz_aciklamasi,
                'sonuc_notu' => $request->sonuc_notu,
                'tarih' => now()->toDateTimeString(),
                'kaldiran_user_id' => auth()->id(),
            ];

            $fatura->payload = $payload;
            $fatura->save();
        } else {
            // Kayıt silinmiş veya staging'den gelmiş → yeniden oluştur
            $data = $itiraz->toArray();
            unset($data['id'], $data['user_id'], $data['durum'], $data['sonuc_notu'], $data['sonuclayan_user_id'], $data['sonuclanma_tarihi'], $data['created_at'], $data['updated_at']);
            $data['itiraz_edildi'] = false;
            $data['itiraz_aciklamasi'] = null;
            $data['kontrol_edildi'] = true;
            $data['kontrol_tarihi'] = now();
            $data['odeme_durumu'] = 'odendi';
            $data['payload'] = array_merge($itiraz->payload ?? [], [
                '_itiraz_gecmisi' => [
                    [
                        'aciklama' => $itiraz->itiraz_aciklamasi,
                        'sonuc_notu' => $request->sonuc_notu,
                        'tarih' => now()->toDateTimeString(),
                        'kaldiran_user_id' => auth()->id(),
                        'geri_yuklendi' => true,
                    ],
                ],
            ]);
            KesinlesenFatura::create($data);
        }

        // İtiraz kaydını tablodan sil
        $itiraz->delete();

        return response()->json([
            'success' => true,
            'message' => 'İtiraz kaydı başarıyla kaldırıldı ve fatura eski tabloya geri döndürüldü.',
        ]);
    }

    /* ─── AJAX: Seçilen yıla ait dönem istatistikleri ─────────────────────── */
    public function ajaxDonemler($yil)
    {
        $donemStats = KesinlesenFatura::where('odeme_durumu', 'odendi')
            ->where('donem', 'like', $yil.'%')
            ->selectRaw('
            donem,
            count(*) as count,
            sum(tutar_toplam) as total_tutar,
            sum(fatura_edilecek_toplam_tuketim_kwh) as total_tuketim,
            0 as total_brut_tuketim
        ')
            ->groupBy('donem')
            ->orderByDesc('donem')
            ->get();

        return response()->json(['yil' => $yil, 'donemler' => $donemStats]);
    }

    /* ─── AJAX: Paginated fatura tablosu ──────────────────────────────────── */
    public function ajaxTablo(Request $request)
    {
        if (! $request->anyFilled(['donem', 'fatura_no', 'tesisat_no', 'bolge_kodu'])) {
            return response()->json([
                'data' => [],
                'stats' => ['count' => 0, 'total_tutar' => 0, 'total_tuketim' => 0],
                'total' => 0,
                'current_page' => 1,
                'last_page' => 1,
            ]);
        }

        $query = KesinlesenFatura::with(['abone'])->where('odeme_durumu', 'odendi');

        if ($request->filled('donem')) {
            $query->where('donem', $request->donem);
        } elseif ($request->filled('yil')) {
            $query->where('donem', 'like', $request->yil.'%');
        }
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%'.$request->fatura_no.'%');
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
        }
        if ($request->filled('bolge_kodu')) {
            $bolgeAdi = \App\Models\Bolgeler::where('bolge_kodu', $request->bolge_kodu)->value('bolge_adi');
            if ($bolgeAdi) {
                $query->where(function ($q) use ($bolgeAdi, $request) {
                    $q->where('ilce', 'like', '%'.$bolgeAdi.'%')
                        ->orWhereHas('abone', fn ($sq) => $sq->where('BOLGE_KODU', $request->bolge_kodu));
                });
            }
        }

        $statsRaw = (clone $query)
            ->selectRaw('count(*) as count, sum(tutar_toplam) as total_tutar, sum(fatura_edilecek_toplam_tuketim_kwh) as total_tuketim')
            ->first();

        $bolgeMap = \App\Models\Bolgeler::pluck('bolge_adi', 'bolge_kodu')->toArray();
        $perPage = $request->filled('donem') ? 5 : 20;
        $paginator = $query->latest()->paginate($perPage);

        $items = $paginator->getCollection()->map(function ($f) use ($bolgeMap) {
            $arr = $f->toArray();
            $bd = $bolgeMap[(string) ($f->ilce_kodu ?? '')] ?? ($f->abone->BOLGE_ADI ?? $f->ilce ?? '—');
            if (str_starts_with((string) $bd, '=')) {
                $bd = '—';
            }
            $arr['bolge_display'] = $bd;

            return $arr;
        });

        return response()->json([
            'data' => $items,
            'stats' => [
                'count' => $statsRaw->count ?? 0,
                'total_tutar' => $statsRaw->total_tutar ?? 0,
                'total_tuketim' => $statsRaw->total_tuketim ?? 0,
            ],
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
        ]);
    }

    /* ─── AJAX: Tüm kayıtları export için döndür (pagination yok) ────────── */
    public function ajaxExportAll(Request $request)
    {
        if (! $request->anyFilled(['donem', 'yil', 'fatura_no', 'tesisat_no', 'bolge_kodu'])) {
            return response()->json(['data' => [], 'stats' => [], 'error' => 'Filtre gerekli']);
        }

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $query = KesinlesenFatura::with(['abone'])->where('odeme_durumu', 'odendi');

        if ($request->filled('donem')) {
            $query->where('donem', $request->donem);
        } elseif ($request->filled('yil')) {
            $query->where('donem', 'like', $request->yil.'%');
        }
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%'.$request->fatura_no.'%');
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%'.$request->tesisat_no.'%');
        }
        if ($request->filled('bolge_kodu')) {
            $bolgeAdi = \App\Models\Bolgeler::where('bolge_kodu', $request->bolge_kodu)->value('bolge_adi');
            if ($bolgeAdi) {
                $query->where(function ($q) use ($bolgeAdi, $request) {
                    $q->where('ilce', 'like', '%'.$bolgeAdi.'%')
                        ->orWhereHas('abone', fn ($sq) => $sq->where('BOLGE_KODU', $request->bolge_kodu));
                });
            }
        }

        $bolgeMap = \App\Models\Bolgeler::pluck('bolge_adi', 'bolge_kodu')->toArray();

        $faturalar = $query->orderBy('tutar_toplam', 'desc')->get();

        $items = $faturalar->map(function ($f) use ($bolgeMap) {
            $bd = $bolgeMap[(string) ($f->ilce_kodu ?? '')] ?? ($f->abone->BOLGE_ADI ?? $f->ilce ?? '—');
            if (str_starts_with((string) $bd, '=')) {
                $bd = '—';
            }

            return [
                'bolge' => $bd,
                'abone_no' => $f->abone_tesis_no ?? $f->tesisat_no ?? '',
                'fatura_no' => $f->fatura_no ?? '',
                'donem' => $f->donem ?? '',
                'tuketim' => round((float) ($f->fatura_edilecek_toplam_tuketim_kwh ?? 0), 2),
                'tutar' => round((float) ($f->tutar_toplam ?? 0), 2),
            ];
        });

        return response()->json([
            'data' => $items,
            'total_tutar' => round($faturalar->sum('tutar_toplam'), 2),
            'total_tuketim' => round($faturalar->sum('fatura_edilecek_toplam_tuketim_kwh'), 2),
            'count' => $faturalar->count(),
            'donem_label' => $request->donem ?? ($request->yil ? $request->yil.' yılı tümü' : 'Tüm dönemler'),
        ]);
    }
}
