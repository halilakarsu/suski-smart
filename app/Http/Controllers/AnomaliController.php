<?php

namespace App\Http\Controllers;

use App\Models\AnormalFatura;
use App\Models\KesinlesenFatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AnomaliController extends Controller
{
    private function hasAnormalFaturalarTable(): bool
    {
        return Schema::hasTable('anormal_faturalar');
    }

    private function tuketimExpr(): string
    {
        return "CASE 
                    WHEN fatura_edilecek_toplam_tuketim_kwh IS NOT NULL 
                         AND fatura_edilecek_toplam_tuketim_kwh > 0 
                    THEN fatura_edilecek_toplam_tuketim_kwh 
                    ELSE (COALESCE(t1_tuketim,0) + COALESCE(t2_tuketim,0) + COALESCE(t3_tuketim,0) + COALESCE(ek_tuketim,0))
                END";
    }

    private function detailData($faturalar): array
    {
        return collect($faturalar->items())->mapWithKeys(function ($fatura) {
            return [
                $fatura->id => [
                    'id' => $fatura->id,
                    'abone_tesis_no' => $fatura->abone_tesis_no,
                    'tesisat_no' => $fatura->tesisat_no,
                    'fatura_no' => $fatura->fatura_no,
                    'donem' => $fatura->donem,
                    'fatura_edilecek_toplam_tuketim_kwh' => $fatura->fatura_edilecek_toplam_tuketim_kwh,
                    'tutar_toplam' => $fatura->tutar_toplam,
                    'ilk_okuma' => $fatura->ilk_okuma instanceof \DateTimeInterface ? $fatura->ilk_okuma->format('d.m.Y') : $fatura->ilk_okuma,
                    'son_okuma' => $fatura->son_okuma instanceof \DateTimeInterface ? $fatura->son_okuma->format('d.m.Y') : $fatura->son_okuma,
                    'payload' => $fatura->payload,
                ],
            ];
        })->all();
    }

    public function index(Request $request)
    {
        // İlk yüklemede varsayılan olarak hiçbir dönem getirme.
        // Kullanıcı manuel olarak filtrelediğinde veriler listelenecek.

        $faturalar = collect();
        $donemler = KesinlesenFatura::orderByDesc('donem')->pluck('donem')->unique()->values();
        $yillar = $donemler->map(fn($d) => explode('-', $d)[0])->unique()->values();
        $bolgeMap = \App\Models\Bolgeler::pluck('bolge_adi', 'bolge_kodu')->toArray();
        $anomaliCount = null;
        $totals = (object)[
            'total_fatura' => 0,
            'total_tuketim' => 0,
            'total_tutar' => 0
        ];

        // Dönem seçilmişse: önce anomali taraması yap, sonra listele
        if ($request->filled('donem')) {
            // Anomali taramasını çalıştır (her filtre değişiminde güncel veri için)
            $anomaliCount = $this->kontrolEt($request);

            $query = KesinlesenFatura::with(['user', 'abone'])
                ->where('donem', $request->donem);

            if ($this->hasAnormalFaturalarTable()) {
                $query->whereDoesntHave('anormalFatura');
            }

            if ($request->filled('bolge_kodu')) {
                $bolgeKodlari = (array) $request->bolge_kodu;
                $bolgeAdlari = \App\Models\Bolgeler::whereIn('bolge_kodu', $bolgeKodlari)->pluck('bolge_adi')->toArray();
                
                if (!empty($bolgeAdlari)) {
                    $query->where(function($q) use ($bolgeAdlari, $bolgeKodlari) {
                        $q->whereIn('ilce', $bolgeAdlari)
                          ->orWhereHas('abone', function($sq) use ($bolgeKodlari) {
                              $sq->whereIn('BOLGE_KODU', $bolgeKodlari);
                          });
                    });
                }
            }
            if ($request->filled('tesisat_no')) {
                $query->where('tesisat_no', 'like', '%' . $request->tesisat_no . '%');
            }
            if ($request->filled('fatura_no')) {
                $query->where('fatura_no', 'like', '%' . $request->fatura_no . '%');
            }
            if ($request->filled('baglanti_grubu')) {
                $query->whereHas('abone', function($q) use ($request) {
                    $q->where('baglanti_grubu', $request->baglanti_grubu);
                });
            }
            if ($request->filled('tarife')) {
                $query->whereHas('abone', function($q) use ($request) {
                    $q->whereIn('tarife', (array)$request->tarife);
                });
            }

            // Sadece itiraz edilmemiş ve anomalisi olanları getir
            $query->where('itiraz_edildi', false);
            $query->whereJsonLength('payload->_tuketim_anomalileri', '>', 0);

            // Toplamları hesapla (paginasyondan önce)
            $totals = (object)[
                'total_fatura' => $query->count(),
                'total_tuketim' => $query->sum(\DB::raw($this->tuketimExpr())),
                'total_tutar' => $query->sum('tutar_toplam')
            ];

            $faturalar = $query->paginate(20)->withQueryString();
            
            if ($request->filled('export')) {
                $allResults = $query->get();
                if ($request->export === 'excel') {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\ReportExport($allResults, $request->all(), 'anomali'),
                        'Anomali_Raporu.xlsx'
                    );
                } elseif ($request->export === 'pdf') {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
                        'results' => $allResults,
                        'type'    => 'anomali',
                        'filters' => $request->all(),
                    ]);
                    return $pdf->download('Anomali_Raporu.pdf');
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('reports.partials.anomali_table', compact('faturalar', 'bolgeMap', 'totals'))->render(),
                    'detail_data' => $this->detailData($faturalar),
                ]);
            }
        }

        $tarifeler = \App\Models\Aboneler::whereNotNull('tarife')->where('tarife', '!=', '')
            ->select('tarife', 'abone_grubu')
            ->distinct()
            ->get()
            ->unique('abone_grubu')
            ->sortBy('abone_grubu')
            ->values();

        return view('reports.anomali', compact('faturalar', 'donemler', 'yillar', 'bolgeMap', 'anomaliCount', 'tarifeler', 'totals'));
    }

    public function anomaliKontrol(Request $request)
    {
        // GET veya POST ile çalışabilir
        if ($request->isMethod('POST')) {
            $request->validate(['donem' => 'required']);
        }

        if ($request->filled('donem')) {
            $anomaliCount = $this->kontrolEt($request);
            return redirect()->route('reports.anomali', $request->all())
                ->with('success', 'Seçilen dönem için anomali kontrolleri tamamlandı. ' . $anomaliCount . ' adet anomali tespit edildi.');
        }

        return redirect()->route('reports.anomali', $request->all());
    }

    /**
     * Tüketim Anomalilerini Kontrol Et
     */
    public function kontrolEt(Request $request)
    {
        $query = KesinlesenFatura::with('abone');

        if ($this->hasAnormalFaturalarTable()) {
            $query->whereDoesntHave('anormalFatura');
        }

        if ($request->filled('donem')) {
            $query->where('donem', $request->donem);
        }
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%' . $request->fatura_no . '%');
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%' . $request->tesisat_no . '%');
        }
        if ($request->filled('bolge_kodu')) {
            $bolgeKodlari = (array) $request->bolge_kodu;
            $bolgeAdlari = \App\Models\Bolgeler::whereIn('bolge_kodu', $bolgeKodlari)->pluck('bolge_adi')->toArray();
            
            if (!empty($bolgeAdlari)) {
                $query->where(function($q) use ($bolgeAdlari, $bolgeKodlari) {
                    $q->whereIn('ilce', $bolgeAdlari)
                      ->orWhereHas('abone', function($sq) use ($bolgeKodlari) {
                          $sq->whereIn('BOLGE_KODU', $bolgeKodlari);
                      });
                });
            }
        }
        if ($request->filled('baglanti_grubu')) {
            $query->whereHas('abone', function($q) use ($request) {
                $q->where('baglanti_grubu', $request->baglanti_grubu);
            });
        }
        if ($request->filled('tarife')) {
            $query->whereHas('abone', function($q) use ($request) {
                $q->whereIn('tarife', (array)$request->tarife);
            });
        }

        $query->where('itiraz_edildi', false);

        $anomaliCount = 0;

        $query->chunk(500, function ($faturalar) use (&$anomaliCount) {
            foreach ($faturalar as $f) {
                $anomaliler = [];

                // 1. Eksi veya Negatif Endeks (T1 Son < T1 İlk)
                $t1Ilk = (float) $f->t1_ilk_endeks;
                $t1Son = (float) $f->t1_son_endeks;
                if ($t1Son > 0 && $t1Son < $t1Ilk) {
                    $anomaliler[] = [
                        'kod' => 'negatif_tuketim', 
                        'mesaj' => 'Negatif Endeks Farkı Tespit Edildi',
                        'detay' => "T1 İlk Endeks: {$t1Ilk} iken T1 Son Endeks: {$t1Son} olarak düşüş göstermiştir."
                    ];
                }

                // 2. Gelişmiş Tüketim Analizi (Günlük Ortalama Bazlı)
                $guncelTuketim = (float) $f->fatura_edilecek_toplam_tuketim_kwh;
                if ($f->ilk_okuma && $f->son_okuma) {
                    $guncelGun = abs($f->son_okuma->diffInDays($f->ilk_okuma)) ?: 1;
                    $guncelGunlukOrt = $guncelTuketim / $guncelGun;

                    // A. Mükerrer/Çakışan Dönem Kontrolü (Tam çakışma kontrolü: s1 < e2 ve e1 > s2)
                    $cakisan = KesinlesenFatura::where('tesisat_no', $f->tesisat_no)
                        ->where('id', '!=', $f->id)
                        ->where('ilk_okuma', '<', $f->son_okuma)
                        ->where('son_okuma', '>', $f->ilk_okuma)
                        ->first();
                    
                    if ($cakisan) {
                        $anomaliler[] = [
                            'kod' => 'cakisan_donem',
                            'mesaj' => 'Çakışan Fatura Dönemi',
                            'detay' => "Bu abone için {$cakisan->fatura_no} no'lu fatura ile tarihsel çakışma tespit edildi. (" . ($cakisan->ilk_okuma?->format('d.m.Y') ?? '–') . " - " . ($cakisan->son_okuma?->format('d.m.Y') ?? '–') . ")"
                        ];
                    }

                    // B. Önceki Veriyle Karşılaştırma
                    $onceki = KesinlesenFatura::where('tesisat_no', $f->tesisat_no)
                        ->where('id', '!=', $f->id)
                        ->where('ilk_okuma', '<', $f->ilk_okuma)
                        ->orderByDesc('ilk_okuma')
                        ->first();

                    if ($onceki && $onceki->ilk_okuma && $onceki->son_okuma) {
                        $oncekiTuketim = (float) $onceki->fatura_edilecek_toplam_tuketim_kwh;
                        $oncekiGun = abs($onceki->son_okuma->diffInDays($onceki->ilk_okuma)) ?: 1;
                        $oncekiGunlukOrt = $oncekiTuketim / $oncekiGun;

                        // Sadece belirli bir tüketim seviyesinin üzerindeyse "Anormal" uyarısı ver (Örn: Günlük 5 kWh)
                        if ($oncekiGunlukOrt > 5 && $guncelGunlukOrt > 0) {
                            $oran = $guncelGunlukOrt / $oncekiGunlukOrt;
                            // Daha esnek eşik değerleri: 5 kat artış veya 10 kat düşüş
                            if ($oran > 5 || $oran < 0.1) {
                                $durum = $oran > 5 ? "Sıradışı Artış" : "Sıradışı Düşüş";
                                $anomaliler[] = [
                                    'kod' => 'anormal_tuketim', 
                                    'mesaj' => "Anormal Tüketim ($durum)",
                                    'detay' => "Günlük ortalama tüketim önceki dönem " . round($oncekiGunlukOrt, 2) . " kWh iken bu dönem " . round($guncelGunlukOrt, 2) . " kWh olmuştur."
                                ];
                            }
                        }

                        if ($oncekiGunlukOrt > 3 && $guncelGunlukOrt == 0) {
                            $anomaliler[] = [
                                'kod' => 'sifir_tuketim', 
                                'mesaj' => 'Tüketim Durması (Sıfır Tüketim)',
                                'detay' => "Önceki dönem günlük ortalama " . round($oncekiGunlukOrt, 2) . " kWh tüketim varken bu dönem 0 kWh tüketim görünmektedir."
                            ];
                        }
                    }
                }

                // 3. Teknik ve Mali Kontroller
                
                // A. Çarpan Sapması
                if ($onceki && (float)$onceki->carpan > 0 && (float)$f->carpan > 0) {
                    if (abs((float)$onceki->carpan - (float)$f->carpan) > 0.001) {
                        $anomaliler[] = [
                            'kod' => 'carpan_sapmasi',
                            'mesaj' => 'Kritik Çarpan Değişimi',
                            'detay' => "Çarpan değeri {$onceki->carpan} -> {$f->carpan} olarak değişmiştir. Bu durum fatura tutarını doğrudan etkiler."
                        ];
                    }
                }

                // B. Hesaplama Tutarsızlığı (Tüm Tarifeler Dahil)
                $t1Diff = (float)$f->t1_son_endeks - (float)$f->t1_ilk_endeks;
                $t2Diff = (float)$f->t2_son_endeks - (float)$f->t2_ilk_endeks;
                $t3Diff = (float)$f->t3_son_endeks - (float)$f->t3_ilk_endeks;
                $t0Diff = (float)$f->t0_son_endeks - (float)$f->t0_ilk_endeks;
                
                // Eğer T1-T2-T3 toplamı 0 ise T0 farkını kullan
                $toplamFark = ($t1Diff + $t2Diff + $t3Diff) ?: $t0Diff;
                
                $hesaplananTuketim = ($toplamFark * (float)$f->carpan) + (float)$f->trafo_kaybi_kwh + (float)$f->ek_tuketim;
                
                // 2 kWh tolerans ile matematiksel kontrol
                if ($hesaplananTuketim > 0 && abs($hesaplananTuketim - $guncelTuketim) > 2) {
                    $anomaliler[] = [
                        'kod' => 'hesap_hatasi',
                        'mesaj' => 'Matematiksel Hesap Hatası',
                        'detay' => "Endeks verilerine göre tüketim " . round($hesaplananTuketim, 2) . " kWh olmalıydı, faturada {$guncelTuketim} kWh görünmektedir."
                    ];
                }

                // C. Tarife Değişimi
                if ($onceki && $onceki->tarife != $f->tarife) {
                    $anomaliler[] = [
                        'kod' => 'tarife_sapmasi',
                        'mesaj' => 'Tarife Grubu Değişikliği',
                        'detay' => "Abone tarifesi '{$onceki->tarife}' iken bu dönem '{$f->tarife}' olmuştur."
                    ];
                }

                // D. Okuma Periyodu Kontrolü (Sadece ekstrem durumlar)
                if ($f->ilk_okuma && $f->son_okuma) {
                    $guncelGun = abs($f->son_okuma->diffInDays($f->ilk_okuma));
                    if ($guncelGun > 0 && ($guncelGun < 15 || $guncelGun > 45)) {
                        $anomaliler[] = [
                            'kod' => 'donem_sapmasi',
                            'mesaj' => 'Ekstrem Okuma Periyodu',
                            'detay' => "Fatura dönemi {$guncelGun} gündür. Standart dışı okuma süresi tespit edildi."
                        ];
                    }
                }

                // 4. Reaktif Ceza
                if ((float) $f->reaktif_tl > 0) {
                    $anomaliler[] = [
                        'kod' => 'reaktif_ceza', 
                        'mesaj' => 'Reaktif Ceza Uygulanmış',
                        'detay' => "Faturaya toplam ₺ " . number_format($f->reaktif_tl, 2, ',', '.') . " tutarında reaktif ceza yansıtılmıştır."
                    ];
                }

                $payload = is_string($f->payload) ? json_decode($f->payload, true) : $f->payload;
                if (!is_array($payload))
                    $payload = [];

                $payload['_tuketim_anomalileri'] = $anomaliler;

                $f->update([
                    'payload' => $payload,
                    'kontrol_edildi' => true,
                    'kontrol_tarihi' => now()
                ]);

                if (count($anomaliler) > 0)
                    $anomaliCount++;
            }
        });

        return $anomaliCount;
    }

    public function kaydet(Request $request, $id)
    {
        $request->validate([
            'islem_notu' => 'nullable|string|max:1000',
        ]);

        $fatura = KesinlesenFatura::with('abone')->findOrFail($id);

        if (!$this->hasAnormalFaturalarTable()) {
            return response()->json([
                'success' => false,
                'message' => 'Anormal fatura kayıt tablosu bulunamadı. Lütfen migration çalıştırın.',
            ], 503);
        }

        $this->storeAnormalFatura($fatura, 'kaydedildi', $request->islem_notu);

        return response()->json(['success' => true, 'message' => 'Anormal fatura kaydedildi.']);
    }

    public function gormezdenGel(Request $request, $id)
    {
        $request->validate([
            'islem_notu' => 'nullable|string|max:1000',
        ]);

        $fatura = KesinlesenFatura::with('abone')->findOrFail($id);

        if (!$this->hasAnormalFaturalarTable()) {
            return response()->json([
                'success' => false,
                'message' => 'Anormal fatura kayıt tablosu bulunamadı. Lütfen migration çalıştırın.',
            ], 503);
        }

        $this->storeAnormalFatura($fatura, 'gormezden_gelindi', $request->islem_notu);

        return response()->json(['success' => true, 'message' => 'Bu fatura anomali kontrolünde görmezden gelinecek.']);
    }

    private function storeAnormalFatura(KesinlesenFatura $fatura, string $durum, ?string $not = null): AnormalFatura
    {
        $payload = is_array($fatura->payload) ? $fatura->payload : [];

        return AnormalFatura::updateOrCreate(
            ['kesinlesen_fatura_id' => $fatura->id],
            [
                'user_id' => Auth::id(),
                'durum' => $durum,
                'islem_notu' => $not,
                'tesisat_no' => $fatura->tesisat_no,
                'abone_tesis_no' => $fatura->abone_tesis_no,
                'fatura_no' => $fatura->fatura_no,
                'hesap_adi' => $fatura->hesap_adi,
                'donem' => $fatura->donem,
                'ilce' => $fatura->ilce,
                'ilce_kodu' => $fatura->ilce_kodu,
                'baglanti_grubu' => $fatura->abone->baglanti_grubu ?? $fatura->baglanti_grubu,
                'yerlesim_turu' => $fatura->abone->yerlesim_turu ?? null,
                'tarife' => $fatura->abone->tarife ?? $fatura->tarife,
                'fatura_edilecek_toplam_tuketim_kwh' => $fatura->fatura_edilecek_toplam_tuketim_kwh,
                'tutar_toplam' => $fatura->tutar_toplam,
                'ilk_okuma' => $fatura->ilk_okuma,
                'son_okuma' => $fatura->son_okuma,
                'anomali_payload' => $payload['_tuketim_anomalileri'] ?? [],
            ]
        );
    }
}
