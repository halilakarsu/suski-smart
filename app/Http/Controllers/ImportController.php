<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Models\BeklemeKontrolHavuzu;
use App\Models\Hamveri;
use App\Services\ExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Cache;

use App\Models\Bolgeler;

class ImportController extends Controller
{
    public function index()
    {
        $stagingStats = [
            'pending' => BeklemeKontrolHavuzu::where('kontrol_edildi', false)->where('kayit_durumu', '!=', 'mukerrer')->count(),
        ];

        $gecmisImportlar = ImportLog::with('user')
            ->withCount([
                'kesinlesenFaturalar as odenen_fatura_sayisi' => function ($query) {
                    $query->where('odeme_durumu', 'odendi');
                },
                'stagingInvoices as staging_bekleyen_sayisi' => function ($query) {
                    $query->where('kontrol_edildi', false);
                },
            ])
            ->latest()
            ->paginate(15);

        return view('import.index', compact('stagingStats', 'gecmisImportlar'));
    }


    /** Geçmiş yükleme logları sayfası */
    public function logs()
    {
        $gecmisImportlar = ImportLog::with('user')
            ->withCount([
                'kesinlesenFaturalar as odenen_fatura_sayisi' => function ($query) {
                    $query->where('odeme_durumu', 'odendi');
                }
            ])
            ->latest()
            ->paginate(20);
        return view('import.logs', compact('gecmisImportlar'));
    }

    /** Ham veri listesi ve detayları */


    /** AJAX: Excel dosyasını yükle ve işle */
    public function ajaxImport(Request $request)
    {
        // Zaman aşımını engelle ve bellek limitini artır
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        session_write_close(); // Prevent session blocking for concurrent progress polling

        $request->validate([
            'dosya' => 'required|file|mimes:xlsx,xls,csv|max:51200',
        ], [
            'dosya.required' => 'Lütfen bir Excel dosyası seçin.',
            'dosya.mimes' => 'Yalnızca .xlsx, .xls veya .csv dosyaları kabul edilir.',
            'dosya.max' => 'Dosya boyutu en fazla 50 MB olabilir.',
        ]);

        $dosya = $request->file('dosya');
        $orijinalAd = $dosya->getClientOriginalName();
        $dosyaHash = hash_file('sha256', $dosya->getRealPath());

        // Daha önce aynı dosya (hash bazlı) yüklenmiş mi?
        $ayniDosya = ImportLog::where('dosya_hash', $dosyaHash)->first();
        if ($ayniDosya) {
            return response()->json([
                'success' => false,
                'message' => "Bu dosya daha önce '{$ayniDosya->donem}' dönemi için {$ayniDosya->created_at->format('d.m.Y H:i')} tarihinde yüklenmiş! Lütfen mükerrer yükleme yapmayınız.",
            ], 422);
        }

        // Önce geçici yola kaydet — dönem tespiti için
        $yol = $dosya->storeAs(
            'imports/temp',
            time() . '_' . $orijinalAd,
            'local'
        );

        $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($yol);
        $service = app(ExcelImportService::class);

        // TAHAKKUK sütunundan dönemi otomatik tespit et
        $donem = $service->detectDonem($fullPath);

        if (!$donem) {
            return response()->json([
                'success' => false,
                'message' => 'Excel dosyasından dönem (TAHAKKUK sütunu) tespit edilemedi. Dosya formatını kontrol edin.',
            ], 422);
        }

        // Kalıcı konuma taşı
        $kaliciYol = 'imports/' . $donem . '/' . basename($yol);
        \Illuminate\Support\Facades\Storage::move($yol, $kaliciYol);

        // Ayrıca aynı orijinal isim ve döneme sahip var mı diye de opsiyonel kontrol
        $mevcutDosya = ImportLog::where('orijinal_adi', $orijinalAd)
            ->where('donem', $donem)
            ->first();

        if ($mevcutDosya) {
            \Illuminate\Support\Facades\Storage::delete($kaliciYol);
            return response()->json([
                'success' => false,
                'message' => "'$orijinalAd' ismiyle daha önce '$donem' dönemi için yükleme yapılmış. İçeriği farklı olsa da karışıklığı önlemek için dosya ismini değiştirerek tekrar deneyin.",
            ], 422);
        }

        try {
            $importLog = ImportLog::create([
                'user_id' => Auth::id(),
                'dosya_adi' => basename($kaliciYol),
                'orijinal_adi' => $orijinalAd,
                'donem' => $donem,
                'yol' => $kaliciYol,
                'dosya_hash' => $dosyaHash,
                'durum' => 'isleniyor',
                'notlar' => null,
            ]);

            // Aktif işlemi Cache'e kaydet (10 dakika geçerli)
            Cache::put('active_import_' . Auth::id(), $importLog->id, 600);

            $hamVeriStats = $service->importToRaw($importLog);
            $stagingStats = $service->promoteToStaging($importLog);


            $toplam = ($stagingStats['yeni'] ?? 0)
                + ($stagingStats['mukerrer'] ?? 0)
                + ($stagingStats['degisti'] ?? 0);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'excel_import',
                'model' => 'ImportLog',
                'model_id' => $importLog->id,
                'description' => "Sisteme yeni bir fatura listesi ({$orijinalAd}) başarıyla içe aktarıldı. Toplam işlenen kayıt: {$toplam}, Yeni: {$stagingStats['yeni']}",
                'new_data' => [
                    'donem' => $donem,
                    'toplam_kayit' => $toplam,
                    'istatistikler' => $stagingStats
                ],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            $importLog->update([
                'durum' => 'tamamlandi',
                'isleme_bitti' => now(),
            ]);

            return response()->json([
                'success' => true,
                'donem' => $donem,
                'toplam_kayit' => $toplam,
                'yeni' => $stagingStats['yeni'] ?? 0,
                'mukerrer' => $stagingStats['mukerrer'] ?? 0,
                'degisti' => $stagingStats['degisti'] ?? 0,
                'reaktif' => $stagingStats['reaktif'] ?? 0,
                'bekleyen' => $stagingStats['bekleyen'] ?? 0,
                'yeni_abone' => $stagingStats['yeni_abone'] ?? 0,
                'yeni_bolge' => $stagingStats['yeni_bolge'] ?? 0,
                'guncellenen_abone' => $stagingStats['guncellenen_abone'] ?? 0,
                'guncellenen_abone_listesi' => $stagingStats['guncellenen_abone_listesi'] ?? [],
                'detay_url' => route('staging.index', ['import_log_id' => $importLog->id]),
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem hatası: ' . $e->getMessage(),
            ], 500);
        } finally {
            // İşlem bittiğinde Cache'den sil
            Cache::forget('active_import_' . Auth::id());
        }
    }

    /** AJAX: İçe aktarma ilerleme durumunu dön */
    public function progress(Request $request, ImportLog $importLog = null)
    {
        if (!$importLog) {
            $activeId = Cache::get('active_import_' . Auth::id());
            if ($activeId) {
                $importLog = ImportLog::find($activeId);
            }
        }

        if (!$importLog) {
            return response()->json(['success' => false, 'message' => 'Aktif işlem bulunamadı.']);
        }

        $toplam = $importLog->toplam_satir ?? 0;
        $islenen = $importLog->islenen_satir ?? 0;

        // Raw + Staging olduğu için %100'e 2*toplam ile ulaşacağız
        $payda = $toplam > 0 ? $toplam * 2 : 1;
        $yuzde = min(99, round(($islenen / $payda) * 100));

        if ($importLog->durum === 'tamamlandi')
            $yuzde = 100;

        if ($islenen <= $toplam) {
            $mesaj = "İşlem hazırlanıyor, veriler düzenleniyor...";
        } else {
            $mesaj = "Veriler detaylı olarak inceleniyor...";
        }

        return response()->json([
            'success' => true,
            'durum' => $importLog->durum,
            'toplam' => $toplam,
            'islenen' => $islenen,
            'yuzde' => $yuzde,
            'mesaj' => $mesaj,
            'hata' => $importLog->notlar
        ]);
    }

    /** AJAX: Tek satırın kontrol_edildi durumunu toggle et */
    public function toggleKontrol(Request $request, BeklemeKontrolHavuzu $beklemeKontrolHavuzu)
    {
        $yeniDurum = !$beklemeKontrolHavuzu->kontrol_edildi;

        $payload = is_array($beklemeKontrolHavuzu->payload) ? $beklemeKontrolHavuzu->payload : [];
        if ($yeniDurum) {
            unset($payload['itiraz_durumu']);
            unset($payload['itiraz_nedeni']);
        }

        $beklemeKontrolHavuzu->update([
            'kontrol_edildi' => $yeniDurum,
            'kontrol_tarihi' => $yeniDurum ? now() : null,
            'payload' => $payload
        ]);

        return response()->json([
            'success' => true,
            'kontrol_edildi' => $yeniDurum,
            'counts' => [
                'bekleyen' => BeklemeKontrolHavuzu::where('kontrol_edildi', false)
                    ->where('kayit_durumu', '!=', 'mukerrer')
                    ->where(function ($q) {
                        $q->whereNull('reaktif_tl')
                            ->orWhere('reaktif_tl', '<=', 0);
                    })->count(),
                'onayli' => BeklemeKontrolHavuzu::where('kontrol_edildi', true)->count(),
                'mukerrer' => BeklemeKontrolHavuzu::where('kayit_durumu', 'mukerrer')->count(),
            ]
        ]);
    }

    /** AJAX: Bekleme havuzundaki kaydın detaylarını dön */
    public function show($id)
    {
        $kayit = BeklemeKontrolHavuzu::findOrFail($id);
        return response()->json($kayit);
    }

    /** Staging kontrol sayfası — sekmeli yapı */
    public function staging(Request $request)
    {
        $tab = $request->get('tab', 'bekleyen'); // Varsayılan: bekleyen
        $query = BeklemeKontrolHavuzu::with(['rawData', 'importLog'])->latest();

        // ── ORTAK FİLTRELER (Tab'lar için base filtre) ──
        if ($request->filled('import_log_id')) {
            $query->where('import_log_id', $request->import_log_id);
        }
        if ($request->filled('donem')) {
            $query->whereHas('importLog', fn($q) => $q->where('donem', $request->donem));
        }

        // Base Query (Sekme sayaçları için kopyalanacak anahtar query)
        $baseQuery = clone $query;

        // ── ARAMA FİLTRELERİ (Sadece o anki tabloyu süzmek için) ──
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%' . $request->fatura_no . '%');
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%' . $request->tesisat_no . '%');
        }
        if ($request->filled('ilce')) {
            $query->where('ilce', 'like', '%' . $request->ilce . '%');
        }

        // ── SEKME FİLTRELERİ ──
        if ($tab === 'onayli') {
            $query->where('kontrol_edildi', true);
        } elseif ($tab === 'mukerrer') {
            $query->where('kontrol_edildi', false)->where('kayit_durumu', 'mukerrer');
        } elseif ($tab === 'reaktif') {
            $query->where('kontrol_edildi', false)
                ->where('kayit_durumu', '!=', 'mukerrer')
                ->where('reaktif_tl', '>', 0);
        } else {
            // 'bekleyen' veya 'all'
            $query->where('kayit_durumu', '!=', 'mukerrer')
                ->where(function ($q) {
                    $q->whereNull('reaktif_tl')
                        ->orWhere('reaktif_tl', '<=', 0);
                });
        }

        // Tüm sekmeler için genel kısıt: Kesinleşmemişler
        $query->where('kontrol_edildi', false);

        $stagingler = $query->paginate(5)->withQueryString();

        // ── TOPLAMLAR ──
        $sayfa_toplam_tutar = $stagingler->sum('genel_toplam');
        $sayfa_toplam_tuketim = $stagingler->sum('fatura_edilecek_toplam_tuketim_kwh');

        $genel_toplam_tutar = (clone $query)->sum('genel_toplam');
        $genel_toplam_tuketim = (clone $query)->sum('fatura_edilecek_toplam_tuketim_kwh');

        // Sekme sayaçları (Header özeti için - Arama hariç)
        $sayaclar = [
            'bekleyen' => (clone $baseQuery)->where('kontrol_edildi', false)->where('kayit_durumu', '!=', 'mukerrer')
                ->where(fn($q) => $q->whereNull('reaktif_tl')->orWhere('reaktif_tl', '<=', 0))->count(),
            'onayli' => (clone $baseQuery)->where('kontrol_edildi', true)->count(),
            'mukerrer' => (clone $baseQuery)->where('kontrol_edildi', false)->where('kayit_durumu', 'mukerrer')->count(),
            'reaktif' => (clone $baseQuery)->where('kontrol_edildi', false)->where('kayit_durumu', '!=', 'mukerrer')->where('reaktif_tl', '>', 0)->count(),
        ];

        // Özet istatistikler
        $ozet = [
            'kontrol_edilen' => (clone $baseQuery)->where('kontrol_edildi', true)->count(),
            'kontrol_edilmeyen' => (clone $baseQuery)->where('kontrol_edildi', false)->count(),
            'sayfa_tutar' => $sayfa_toplam_tutar,
            'sayfa_tuketim' => $sayfa_toplam_tuketim,
            'genel_tutar' => $genel_toplam_tutar,
            'genel_tuketim' => $genel_toplam_tuketim,
        ];

        $donemler = ImportLog::orderByDesc('donem')
            ->pluck('donem')
            ->unique()
            ->values();

        return view('import.staging', compact('stagingler', 'ozet', 'donemler', 'tab', 'sayaclar'));
    }

    /** AJAX: İtiraz Et eylemi */
    public function itirazEt(Request $request, BeklemeKontrolHavuzu $beklemeKontrolHavuzu)
    {
        $request->validate([
            'itiraz_nedeni' => 'required|string|max:1000'
        ]);

        if (!\Illuminate\Support\Facades\Schema::hasColumn('itiraz_edilenler', 'hesap_adi')) {
            \Illuminate\Support\Facades\Schema::table('itiraz_edilenler', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('hesap_adi')->nullable()->after('tesisat_no');
            });
        }

        $donem = $beklemeKontrolHavuzu->donem;
        if (!$donem) {
            $log = \App\Models\ImportLog::find($beklemeKontrolHavuzu->import_log_id);
            $donem = $log ? $log->donem : date('Y-m');
        }

        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('itiraz_edilenler');
        $columnKeys = array_flip($columns);

        $data = $beklemeKontrolHavuzu->getAttributes();
        $data['user_id'] = \Illuminate\Support\Facades\Auth::id();
        $data['itiraz_aciklamasi'] = $request->itiraz_nedeni;
        $data['itiraz_edildi'] = true;
        $data['durum'] = 'bekliyor';
        $data['donem'] = $donem;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Sadece hedef tablo sütunlarını tut ve null/array temizliği yap
        $filtered = array_intersect_key($data, $columnKeys);
        foreach ($filtered as $key => $val) {
            if ($val === null) {
                $numericSuffixes = ['_bedeli', '_tutari', '_tl', '_tuketim', '_endeks', '_fiyat', '_tutar', '_toplam', 'kdv', 'btv', 'trt_fonu', 'enerji_fonu', 'carpan', 'gunluk_ortalama_tuketim', 'yillik_tuketim', 'trafo_kaybi_kwh', 'ek_tuketim', 'serbest_tuketici', 'kontrol_edildi', 'itiraz_edildi'];
                foreach ($numericSuffixes as $suffix) {
                    if (str_ends_with($key, $suffix) || str_contains($key, $suffix) || $key === 'carpan') {
                        $filtered[$key] = 0;
                        break;
                    }
                }
            } elseif ($val instanceof \DateTimeInterface) {
                $filtered[$key] = $val->format('Y-m-d H:i:s');
            } elseif (is_array($val) || is_object($val)) {
                $filtered[$key] = json_encode($val, JSON_UNESCAPED_UNICODE);
            }
        }
        unset($filtered['id']);

        \App\Models\ItirazEdilenler::create($filtered);

        // Havuzdan sil
        $beklemeKontrolHavuzu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kayıt itiraz edilenler listesine taşındı.'
        ]);
    }

    /** AJAX: İtirazı Geri Al / İptal Et */
    public function itirazIptal(BeklemeKontrolHavuzu $beklemeKontrolHavuzu)
    {
        $payload = is_array($beklemeKontrolHavuzu->payload) ? $beklemeKontrolHavuzu->payload : [];
        unset($payload['itiraz_durumu']);
        unset($payload['itiraz_nedeni']);
        unset($payload['itiraz_tarihi']);

        $beklemeKontrolHavuzu->update([
            'payload' => $payload,
            'kontrol_edildi' => false
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    /** AJAX: Havuz tablosundan sil (Hamveriden silinmez) */
    public function destroyHavuz(BeklemeKontrolHavuzu $beklemeKontrolHavuzu)
    {
        $beklemeKontrolHavuzu->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /** AJAX: Toplu Onayla */
    public function approveMultiple(Request $request)
    {
        $ids = $request->ids ?? [];
        if (!empty($ids)) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($ids) {
                // Binlerce kayıt seçilme ihtimaline karşı chunk kullan
                BeklemeKontrolHavuzu::whereIn('id', $ids)->chunkById(500, function ($havuzlar) {
                    foreach ($havuzlar as $havuz) {
                        $payload = is_array($havuz->payload) ? $havuz->payload : [];
                        unset($payload['itiraz_durumu'], $payload['itiraz_nedeni']);
                        $havuz->update([
                            'kontrol_edildi' => true,
                            'kontrol_tarihi' => now(),
                            'payload' => $payload
                        ]);
                    }
                });
            });
        }
        return response()->json(['success' => true]);
    }

    /** AJAX: Toplu Beklemeye Al */
    public function pendMultiple(Request $request)
    {
        $ids = $request->ids ?? [];
        if (!empty($ids)) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($ids) {
                BeklemeKontrolHavuzu::whereIn('id', $ids)->update([
                    'kontrol_edildi' => false,
                    'kontrol_tarihi' => null,
                ]);
            });
        }
        return response()->json(['success' => true]);
    }

    /**
     * Bekleme havuzundaki TÜM bekleyen kayıtları onayla (Filtreleri dikkate alarak)
     */
    public function approveAll(Request $request)
    {
        $query = BeklemeKontrolHavuzu::where('kontrol_edildi', false);

        // Filtreleri uygula
        if ($request->filled('donem')) {
            $query->whereHas('importLog', fn($q) => $q->where('donem', $request->donem));
        }
        if ($request->filled('ilce')) {
            $query->where('ilce', 'like', '%' . $request->ilce . '%');
        }
        if ($request->filled('fatura_no')) {
            $query->where('fatura_no', 'like', '%' . $request->fatura_no . '%');
        }
        if ($request->filled('tesisat_no')) {
            $query->where('tesisat_no', 'like', '%' . $request->tesisat_no . '%');
        }

        $count = $query->update([
            'kontrol_edildi' => true,
            'kontrol_tarihi' => now()
        ]);

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Bekleme havuzundaki TÜM Onaylanmış (kontrol_edildi=1) kayıtları kesinleştirip ödemeye gönder (Filtreleri dikkate alarak)
     */
    public function sendAllToPayment(Request $request)
    {
        try {
            set_time_limit(0);
            ini_set('memory_limit', '1024M');
            // Sütun kontrolü
            if (!\Illuminate\Support\Facades\Schema::hasColumn('kesinlesen_faturalar', 'hesap_adi')) {
                \Illuminate\Support\Facades\Schema::table('kesinlesen_faturalar', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('hesap_adi')->nullable()->after('tesisat_no');
                });
            }

            $count = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $columns = \Illuminate\Support\Facades\Schema::getColumnListing('kesinlesen_faturalar');
                $columnKeys = array_flip($columns);
                $totalMoved = 0;

                // Hem onaylı hem onay bekleyen (fakat kesinleşmemiş) tüm kayıtları gönder
                $query = BeklemeKontrolHavuzu::query();

                // Filtreleri uygula
                if ($request->filled('donem')) {
                    $query->whereHas('importLog', fn($q) => $q->where('donem', $request->donem));
                }
                if ($request->filled('ilce')) {
                    $query->where('ilce', 'like', '%' . $request->ilce . '%');
                }
                if ($request->filled('fatura_no')) {
                    $query->where('fatura_no', 'like', '%' . $request->fatura_no . '%');
                }
                if ($request->filled('tesisat_no')) {
                    $query->where('tesisat_no', 'like', '%' . $request->tesisat_no . '%');
                }

                // ── KRİTİK FİLTRE: Sadece temiz/bekleyen faturaları gönder ──
                // Mükerrerleri gönderme
                $query->where('kayit_durumu', '!=', 'mukerrer');

                // İtirazlıları veya anomalilileri engellemiyoruz, hepsi kesinleşene taşınıyor.
                // ANCAK: Reaktif faturalar Ödemeye Gönder (Kesinleşenlere) AK-TA-RIL-MAZ!
                // Onlar staging'de Reaktifler sekmesinde reaktifler tablosuna gönderilmeyi bekler.
                $query->where(function ($q) {
                    $q->whereNull('reaktif_tl')
                        ->orWhere('reaktif_tl', '<=', 0);
                });



                $query->chunkById(100, function ($havuzlar) use ($columnKeys, &$totalMoved) {
                    $insertData = [];
                    foreach ($havuzlar as $havuz) {
                        try {
                            $baseDonem = $havuz->importLog ? $havuz->importLog->donem : null;
                            $donem = $baseDonem ?? date('Y-m');

                            $tahakkukVal = $havuz->son_odeme_tarihi ?? ($havuz->ilk_okuma ?? null);
                            if (!$tahakkukVal && is_array($havuz->payload)) {
                                foreach (['tahakkuk', 'donem', 'dönem', 'donem_fatura', 'Tahakkuk Tarihi'] as $k) {
                                    if (!empty($havuz->payload[$k])) {
                                        $tahakkukVal = $havuz->payload[$k];
                                        break;
                                    }
                                }
                            }

                            if ($tahakkukVal) {
                                $tStr = trim((string) $tahakkukVal);
                                if (preg_match('/^(\d{4})(\d{2})$/', $tStr, $m)) {
                                    $donem = $m[1] . '-' . $m[2];
                                } elseif (is_numeric($tStr) && $tStr > 20000) {
                                    try {
                                        $donem = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $tStr)->format('Y-m');
                                    } catch (\Throwable $e) {
                                    }
                                } else {
                                    foreach (['Y-m-d', 'd.m.Y', 'd/m/Y', 'Y/m/d'] as $fmt) {
                                        try {
                                            $dObj = \Carbon\Carbon::createFromFormat($fmt, $tStr);
                                            if ($dObj) {
                                                $donem = $dObj->format('Y-m');
                                                break;
                                            }
                                        } catch (\Exception $e) {
                                        }
                                    }
                                }
                            }

                            $data = $havuz->getAttributes();

                            // ── MAPPING HELPERS ──
                            $find = function ($keys) use ($havuz) {
                                if (!is_array($havuz->payload))
                                    return null;
                                foreach ($keys as $k) {
                                    if (isset($havuz->payload[$k]))
                                        return $havuz->payload[$k];
                                    $upper = mb_strtoupper($k);
                                    if (isset($havuz->payload[$upper]))
                                        return $havuz->payload[$upper];
                                }
                                return null;
                            };
                            $parse = function ($val) {
                                if ($val === null || $val === '' || $val === false)
                                    return 0;
                                if (is_numeric($val))
                                    return (float) $val;

                                $v = trim((string) $val);
                                // Akıllı Temizlik
                                if (strpos($v, '.') !== false && strpos($v, ',') !== false) {
                                    $v = str_replace('.', '', $v);
                                    $v = str_replace(',', '.', $v);
                                } elseif (strpos($v, ',') !== false) {
                                    $v = str_replace(',', '.', $v);
                                }
                                return is_numeric($v) ? (float) $v : 0;
                            };

                            // ── MAPPING OVERRIDE: tutar_toplam her zaman genel_toplam'dan al ──
                            // Önce payload'dan genel_toplam kaynaklarını bul
                            $rawGenel = $find(['genel_toplam', 'GENEL TOPLAM', 'toplam tutar', 'fatura_tutar', 'fatura tutari', 'fatura_edilecek_tutar']);
                            $genelToplam = $rawGenel !== null ? $parse($rawGenel) : ($havuz->genel_toplam ?? 0);

                            // Her iki tutarı da genel_toplam'a sabitle
                            $data['genel_toplam'] = $genelToplam;
                            $data['tutar_toplam'] = $genelToplam;

                            $data['donem'] = $donem;
                            $data['aktarim_yapan_id'] = \Illuminate\Support\Facades\Auth::id();
                            $data['odeme_durumu'] = 'odendi';
                            $data['odeme_tarihi'] = now();
                            $data['odeme_yapan_id'] = \Illuminate\Support\Facades\Auth::id();
                            $data['created_at'] = now();
                            $data['updated_at'] = now();

                            // Temizlenmiş veri
                            $filteredItem = array_intersect_key($data, $columnKeys);

                            // Null/Sayısal temizliği (sendToPaymentMultiple'daki gibi)
                            foreach ($filteredItem as $key => $val) {
                                if ($val === null) {
                                    $numericSuffixes = ['_bedeli', '_tutari', '_tl', '_tuketim', '_endeks', '_fiyat', '_tutar', '_toplam', 'kdv', 'btv', 'trt_fonu', 'enerji_fonu', 'carpan', 'gunluk_ortalama_tuketim', 'yillik_tuketim', 'trafo_kaybi_kwh', 'ek_tuketim', 'serbest_tuketici', 'kontrol_edildi', 'itiraz_edildi'];
                                    $isNumeric = false;
                                    foreach ($numericSuffixes as $suffix) {
                                        if (str_ends_with($key, $suffix) || str_contains($key, $suffix) || $key === 'carpan') {
                                            $isNumeric = true;
                                            break;
                                        }
                                    }
                                    if ($isNumeric)
                                        $filteredItem[$key] = 0;
                                }

                                if ($val instanceof \DateTimeInterface)
                                    $filteredItem[$key] = $val->format('Y-m-d H:i:s');
                                elseif (is_array($val) || is_object($val))
                                    $filteredItem[$key] = json_encode($val, JSON_UNESCAPED_UNICODE);
                            }

                            unset($filteredItem['id']);
                            $insertData[] = $filteredItem;
                            $totalMoved++;
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Fatura kesinleştirme hatası (ID: {$havuz->id}): " . $e->getMessage());
                        }
                    }

                    if (!empty($insertData)) {
                        \App\Models\KesinlesenFatura::insert($insertData);
                        BeklemeKontrolHavuzu::whereIn('id', $havuzlar->pluck('id'))->delete();
                    }
                });
                return $totalMoved;
            });

            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: Seçilen Onaylıları Kesinleştirip Ödemeye Gönder */
    public function sendToPaymentMultiple(Request $request)
    {
        $ids = $request->ids ?? [];
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Lütfen en az bir kayıt seçin.'], 422);
        }

        try {
            set_time_limit(0);
            ini_set('memory_limit', '1024M');
            // AUTO-FIX: Ensure hesap_adi exists in kesinlesen_faturalar
            if (!\Illuminate\Support\Facades\Schema::hasColumn('kesinlesen_faturalar', 'hesap_adi')) {
                \Illuminate\Support\Facades\Schema::table('kesinlesen_faturalar', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('hesap_adi')->nullable()->after('tesisat_no');
                });
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($ids) {
                // Not: Artık kontrol_edildi=true kısıtı yok, kullanıcı doğrudan gönderim yapıyor.

                // Hedef tablo sütunlarını bir kez al
                $columns = \Illuminate\Support\Facades\Schema::getColumnListing('kesinlesen_faturalar');
                $columnKeys = array_flip($columns);

                BeklemeKontrolHavuzu::whereIn('id', $ids)->chunkById(100, function ($havuzlar) use ($columnKeys) {
                    $insertData = [];
                    foreach ($havuzlar as $havuz) {
                        $baseDonem = $havuz->importLog ? $havuz->importLog->donem : null;
                        $donem = $baseDonem ?? date('Y-m');

                        $tahakkukVal = $havuz->son_odeme_tarihi ?? ($havuz->ilk_okuma ?? null);
                        if (!$tahakkukVal && is_array($havuz->payload)) {
                            foreach (['tahakkuk', 'donem', 'dönem', 'donem_fatura', 'Tahakkuk Tarihi'] as $k) {
                                if (!empty($havuz->payload[$k])) {
                                    $tahakkukVal = $havuz->payload[$k];
                                    break;
                                }
                            }
                        }

                        if ($tahakkukVal) {
                            $tStr = trim((string) $tahakkukVal);
                            if (preg_match('/^(\d{4})(\d{2})$/', $tStr, $m)) {
                                $donem = $m[1] . '-' . $m[2];
                            } elseif (is_numeric($tStr) && $tStr > 20000) {
                                try {
                                    $donem = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $tStr)->format('Y-m');
                                } catch (\Throwable $e) {
                                }
                            } else {
                                foreach (['Y-m-d', 'd.m.Y', 'd/m/Y', 'Y/m/d'] as $fmt) {
                                    $dt = \DateTime::createFromFormat($fmt, $tStr);
                                    if ($dt !== false) {
                                        $donem = $dt->format('Y-m');
                                        break;
                                    }
                                }
                            }
                        }

                        // toArray() yerine getAttributes() kullanarak ilişkilerin (importLog vb) array'e dahil olup 
                        // "Array to string conversion" hatası vermesini engelliyoruz.
                        $data = $havuz->getAttributes();
                        $data['donem'] = $donem;
                        $data['aktarim_yapan_id'] = \Illuminate\Support\Facades\Auth::id();
                        $data['odeme_durumu'] = 'odendi';
                        $data['odeme_tarihi'] = now();
                        $data['odeme_yapan_id'] = \Illuminate\Support\Facades\Auth::id();
                        $data['created_at'] = now();
                        $data['updated_at'] = now();

                        // ── tutar_toplam her zaman genel_toplam'dan gelsin ──
                        $genelToplam = isset($data['genel_toplam']) ? (float) $data['genel_toplam'] : 0;
                        if ($genelToplam == 0) {
                            $genelToplam = (float) ($havuz->genel_toplam ?? $havuz->fatura_tutari ?? 0);
                        }
                        $data['tutar_toplam'] = $genelToplam;
                        $data['genel_toplam'] = $genelToplam;

                        // Sadece hedef tabloda olan sütunları filtrele
                        $filteredItem = array_intersect_key($data, $columnKeys);

                        // Null ve Array kontrolü
                        foreach ($filteredItem as $key => $val) {
                            // Veritabanında NOT NULL olan sayısal alanlar için null gelirse 0'a çek
                            if ($val === null) {
                                $numericSuffixes = ['_bedeli', '_tutari', '_tl', '_tuketim', '_endeks', '_fiyat', '_tutar', '_toplam', 'kdv', 'btv', 'trt_fonu', 'enerji_fonu', 'carpan', 'gunluk_ortalama_tuketim', 'yillik_tuketim', 'trafo_kaybi_kwh', 'ek_tuketim', 'serbest_tuketici', 'kontrol_edildi', 'itiraz_edildi'];
                                $isNumeric = false;
                                foreach ($numericSuffixes as $suffix) {
                                    if (str_ends_with($key, $suffix) || str_contains($key, $suffix) || $key === 'carpan') {
                                        $isNumeric = true;
                                        break;
                                    }
                                }
                                if ($isNumeric) {
                                    $filteredItem[$key] = 0;
                                }
                            }

                            if ($val instanceof \DateTimeInterface) {
                                $filteredItem[$key] = $val->format('Y-m-d H:i:s');
                            } elseif (is_array($val) || is_object($val)) {
                                $filteredItem[$key] = json_encode($val, JSON_UNESCAPED_UNICODE);
                            }
                        }

                        // ID her zaman unset edilmeli (insert işlemi için)
                        unset($filteredItem['id']);

                        $insertData[] = $filteredItem;
                    }

                    if (!empty($insertData)) {
                        \App\Models\KesinlesenFatura::insert($insertData);
                        $havuzIds = $havuzlar->pluck('id')->toArray();
                        BeklemeKontrolHavuzu::whereIn('id', $havuzIds)->delete();
                    }
                });
            });

            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'odeme_emri',
                'description' => count($ids) . " adet onaylı fatura kesinleştirilerek Ödeme Emirleri sayfasına aktarıldı."
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aktarım hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteLog($id)
    {
        $log = \App\Models\ImportLog::findOrFail($id);

        $kesinlesenCount = \App\Models\KesinlesenFatura::where('import_log_id', $log->id)->count();

        \Illuminate\Support\Facades\DB::transaction(function () use ($log, $kesinlesenCount) {
            // 1. Kesinleşen faturaları sil
            if ($kesinlesenCount > 0) {
                \App\Models\KesinlesenFatura::where('import_log_id', $log->id)->delete();
            }

            // 2. Havuz (Staging) verilerini sil
            \App\Models\BeklemeKontrolHavuzu::where('import_log_id', $log->id)->delete();

            // 3. Ham verileri sil
            \App\Models\Hamveri::where('import_log_id', $log->id)->delete();

            // 4. Fiziksel dosyayı sil
            if ($log->yol && \Illuminate\Support\Facades\Storage::disk($log->disk ?? 'local')->exists($log->yol)) {
                \Illuminate\Support\Facades\Storage::disk($log->disk ?? 'local')->delete($log->yol);
            }

            // 5. Logu sil
            $log->delete();

            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'import_silindi',
                'description' => "{$log->orijinal_adi} isimli veri yükleme kaydı ve bağlı tüm havuz/ham/kesinleşen verileri sistemden temizlendi. (Silinen kesinleşen fatura: {$kesinlesenCount})"
            ]);
        });

        return response()->json(['success' => true]);
    }



    /** AJAX: Reaktif faturaları reaktifler tablosuna gönder */
    public function sendToReaktifler(Request $request)
    {
        try {
            set_time_limit(0);
            ini_set('memory_limit', '1024M');

            if (!\Illuminate\Support\Facades\Schema::hasColumn('reaktifler', 'hesap_adi')) {
                \Illuminate\Support\Facades\Schema::table('reaktifler', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('hesap_adi')->nullable()->after('tesisat_no');
                });
            }

            // Reaktif_tl > 0 olan tüm kayıtları bul (Mükerrerler hariç)
            $reaktifler = BeklemeKontrolHavuzu::where('reaktif_tl', '>', 0)
                ->where('kontrol_edildi', false)
                ->where('kayit_durumu', '!=', 'mukerrer')
                ->get();

            if ($reaktifler->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Henüz reaktif cezası olan fatura bulunmamaktadır.'
                ], 422);
            }

            $count = 0;
            $skippedCount = 0;
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('reaktifler');
            $columnKeys = array_flip($columns);

            foreach ($reaktifler as $havuz) {
                $donem = $havuz->donem;
                if (!$donem) {
                    $log = \App\Models\ImportLog::find($havuz->import_log_id);
                    $donem = $log ? $log->donem : date('Y-m');
                }

                $data = $havuz->getAttributes();
                $data['aktarim_yapan_id'] = \Illuminate\Support\Facades\Auth::id();
                $data['durum'] = 'islendi';
                $data['donem'] = $donem;
                $data['created_at'] = now();
                $data['updated_at'] = now();

                // Sadece hedef tablo sütunlarını tut ve null/array temizliği yap
                $filtered = array_intersect_key($data, $columnKeys);
                foreach ($filtered as $key => $val) {
                    if ($val === null) {
                        $numericSuffixes = ['_bedeli', '_tutari', '_tl', '_tuketim', '_endeks', '_fiyat', '_tutar', '_toplam', 'kdv', 'btv', 'trt_fonu', 'enerji_fonu', 'carpan', 'gunluk_ortalama_tuketim', 'yillik_tuketim', 'trafo_kaybi_kwh', 'ek_tuketim', 'serbest_tuketici', 'kontrol_edildi', 'itiraz_edildi'];
                        foreach ($numericSuffixes as $suffix) {
                            if (str_ends_with($key, $suffix) || str_contains($key, $suffix) || $key === 'carpan') {
                                $filtered[$key] = 0;
                                break;
                            }
                        }
                    } elseif ($val instanceof \DateTimeInterface) {
                        $filtered[$key] = $val->format('Y-m-d H:i:s');
                    } elseif (is_array($val) || is_object($val)) {
                        $filtered[$key] = json_encode($val, JSON_UNESCAPED_UNICODE);
                    } else {
                        // Eğer sayısal alansa MySQL "Out of range" yememek için limitleri uygula
                        $numericSuffixes = ['_bedeli', '_tutari', '_tl', '_tuketim', '_endeks', '_fiyat', '_tutar', '_toplam', 'kdv', 'btv', 'trt_fonu', 'enerji_fonu', 'carpan', 'gunluk_ortalama_tuketim', 'yillik_tuketim', 'trafo_kaybi_kwh', 'ek_tuketim'];
                        foreach ($numericSuffixes as $suffix) {
                            if (str_ends_with($key, $suffix) || str_contains($key, $suffix) || $key === 'carpan') {
                                if (is_string($val) && str_contains($val, ',') && !str_contains($val, '.')) {
                                    $val = str_replace(',', '.', $val);
                                }
                                $fVal = floatval($val);
                                // Özellikle çarpan alanı db'de DECIMAL(8,4) olduğu için 9999'u geçemez.
                                if ($key === 'carpan') {
                                    if ($fVal > 9999.99)
                                        $fVal = 9999.99;
                                    if ($fVal < -9999.99)
                                        $fVal = -9999.99;
                                } else {
                                    if ($fVal > 999999.99)
                                        $fVal = 999999.99;
                                    if ($fVal < -999999.99)
                                        $fVal = -999999.99;
                                }
                                $filtered[$key] = $fVal;
                                break;
                            }
                        }
                    }
                }
                unset($filtered['id']);

                // Mükerrer Kontrolü: Aynı fatura ve dönem daha önce arşive girmiş mi?
                $exists = \App\Models\Reaktifler::where('fatura_no', $filtered['fatura_no'])
                    ->where('donem', $filtered['donem'])
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                \App\Models\Reaktifler::create($filtered);
                $count++;
            }

            // Reaktif kayıtları bekleme havuzundan sil (Hem yeni aktarılanları hem de zaten arşivde olan kopyaları temizle)
            $havuzIds = $reaktifler->pluck('id')->toArray();
            BeklemeKontrolHavuzu::whereIn('id', $havuzIds)->delete();

            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'reaktifler_gonderildi',
                'description' => "{$count} adet yeni reaktif faturası arşive eklendi, {$skippedCount} adet kayıt zaten arşivde olduğu için atlandı."
            ]);

            return response()->json([
                'success' => true,
                'message' => "İşlem tamamlandı: {$count} adet yeni fatura arşive gönderildi. " . ($skippedCount > 0 ? "{$skippedCount} adet fatura zaten arşivde mevcut olduğu için atlandı." : ""),
                'count' => $count,
                'skipped' => $skippedCount
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('REAKTIFLER GONDERIM HATASI: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Reaktif faturaları gönderirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /** 
     * Reaktifler tablosundaki mükerrer (aynı fatura_no ve donem) kayıtları temizler.
     */
    public function deduplicateReaktifler()
    {
        try {
            $duplicates = \App\Models\Reaktifler::select('fatura_no', 'donem', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
                ->groupBy('fatura_no', 'donem')
                ->having('count', '>', 1)
                ->get();

            $deletedCount = 0;
            foreach ($duplicates as $dup) {
                // En yeni ID hariç diğerlerini sil
                $idsToDelete = \App\Models\Reaktifler::where('fatura_no', $dup->fatura_no)
                    ->where('donem', $dup->donem)
                    ->orderBy('id', 'desc')
                    ->skip(1)
                    ->pluck('id');

                $deletedCount += \App\Models\Reaktifler::whereIn('id', $idsToDelete)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} adet mükerrer reaktif kaydı başarıyla temizlendi."
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /** 
     * Veritabanındaki hatalı ondalık değerleri (100 kat hatası) 
     * mevcut payload (JSON) verilerini kullanarak düzeltir.
     */
    public function fixCorruptedDecimals()
    {
        try {
            set_time_limit(0);
            ini_set('memory_limit', '1024M');

            $service = app(\App\Services\ExcelImportService::class);
            $summary = [];

            $tables = [
                \App\Models\BeklemeKontrolHavuzu::class => 'Bekleme Havuzu',
                \App\Models\KesinlesenFatura::class => 'Kesinleşen Faturalar',
                \App\Models\Reaktifler::class => 'Reaktifler',
                \App\Models\ItirazEdilenler::class => 'İtiraz Edilenler',
            ];

            foreach ($tables as $modelClass => $label) {
                $count = 0;
                $modelClass::chunkById(200, function ($records) use ($service, &$count) {
                    foreach ($records as $record) {
                        $payload = $record->payload;
                        if (!$payload || !is_array($payload))
                            continue;

                        $newValues = $this->mapDecimalsFromPayload($payload, $service);
                        $needsUpdate = false;
                        foreach ($newValues as $k => $v) {
                            if ($v !== null && isset($record->$k) && $record->$k != $v) {
                                $record->$k = $v;
                                $needsUpdate = true;
                            }
                        }

                        // ── REAKTİF ANOMALİ SENKRONİZASYONU ──
                        // Eğer reaktif_tl > 0 ise payload'a reaktif_ceza anomalisini ekle (tab'da görünmesi için)
                        if ($record->reaktif_tl > 0) {
                            $anomaliler = $payload['_anomaliler'] ?? [];
                            $hasReaktif = false;
                            foreach ($anomaliler as $ano) {
                                if (($ano['kod'] ?? '') === 'reaktif_ceza') {
                                    $hasReaktif = true;
                                    break;
                                }
                            }
                            if (!$hasReaktif) {
                                $anomaliler[] = [
                                    'kod' => 'reaktif_ceza',
                                    'mesaj' => "Abonede " . number_format($record->reaktif_tl, 2) . " TL değerinde Reaktif/Kapasitif sınır aşımı cezası tespit edilmiştir. Kompanzasyon panosu incelenmelidir."
                                ];
                                $payload['_anomaliler'] = $anomaliler;
                                $record->payload = $payload;
                                $needsUpdate = true;
                            }
                        }

                        if ($needsUpdate) {
                            $record->save();
                            $count++;
                        }
                    }
                });
                $summary[] = "$label: $count kayıt düzeltildi.";
            }

            // Aboneler tablosunu düzelt
            $aboneCount = 0;
            \App\Models\Aboneler::chunkById(200, function ($aboneler) use (&$aboneCount) {
                foreach ($aboneler as $abone) {
                    $fatura = \App\Models\BeklemeKontrolHavuzu::where('tesisat_no', $abone->ABONE_TESIS_NO)->orderBy('id', 'desc')->first()
                        ?? \App\Models\KesinlesenFatura::where('tesisat_no', $abone->ABONE_TESIS_NO)->orderBy('id', 'desc')->first();

                    if ($fatura && $fatura->carpan) {
                        if ($abone->carpan != $fatura->carpan) {
                            $abone->carpan = $fatura->carpan;
                            $abone->save();
                            $aboneCount++;
                        }
                    }
                }
            });
            $summary[] = "Aboneler: $aboneCount abone güncellendi.";

            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'veri_duzeltme',
                'description' => "Veritabanındaki ondalık veri hatası (" . implode(', ', $summary) . ") otomatik olarak düzeltildi."
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Düzeltme tamamlandı: ' . implode(' | ', $summary)
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Düzeltme sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /** Payload'dan sayısal değerleri re-parse eder (ExcelImportService mappingine sadık kalarak) */
    private function mapDecimalsFromPayload(array $payload, $service)
    {
        $find = function ($keys) use ($payload) {
            foreach ($payload as $k => $v) {
                foreach ($keys as $key) {
                    if (mb_strtolower(trim((string) $k), 'UTF-8') === mb_strtolower($key, 'UTF-8')) {
                        $val = $v !== null ? trim((string) $v) : null;
                        return ($val !== '') ? $val : null;
                    }
                }
            }
            return null;
        };

        $parse = fn($val) => $service->parseDecimal($val);

        return [
            'carpan' => $parse($find(['carpan', 'çarpan'])),
            't1_ilk_endeks' => $parse($find(['t1_ilk_endeks', 't1 ilk endeks'])),
            't1_son_endeks' => $parse($find(['t1_son_endeks', 't1 son endeks'])),
            't2_ilk_endeks' => $parse($find(['t2_ilk_endeks', 't2 ilk endeks'])),
            't2_son_endeks' => $parse($find(['t2_son_endeks', 't2 son endeks'])),
            't3_ilk_endeks' => $parse($find(['t3_ilk_endeks', 't3 ilk endeks'])),
            't3_son_endeks' => $parse($find(['t3_son_endeks', 't3 son endeks'])),
            'ri_ilk_endeks' => $parse($find(['t4_ilk_endeks', 'ri_ilk_endeks', 'ri ilk endeks'])),
            'ri_son_endeks' => $parse($find(['t4_son_endeks', 'ri_son_endeks', 'ri son endeks'])),
            'ri_fark_endeks' => $parse($find(['t4_fark', 'ri_fark_endeks', 'ri fark'])),
            'rc_ilk_endeks' => $parse($find(['t5_ilk_endeks', 'rc_ilk_endeks', 'rc ilk endeks'])),
            'rc_son_endeks' => $parse($find(['t5_son_endeks', 'rc_son_endeks', 'rc son endeks'])),
            'rc_fark_endeks' => $parse($find(['t5_fark', 'rc_fark_endeks', 'rc fark'])),
            't1_tuketim' => $parse($find(['t1_fark', 't1_tuketim'])),
            't2_tuketim' => $parse($find(['t2_fark', 't2_tuketim'])),
            't3_tuketim' => $parse($find(['t3_fark', 't3_tuketim'])),
            'ek_tuketim' => $parse($find(['aktif_miktar', 'aktif_kwh', 'ek tuketim'])),
            'fatura_edilecek_toplam_tuketim_kwh' => $parse($find(['aktif kwh', 'aktif_kwh'])),
            'birim_fiyat' => (function ($payload) use ($parse, $find) {
                $bestVal = null;
                foreach ($payload as $k => $v) {
                    $kNorm = str_replace([' ', 'ı', 'İ', 'ß'], ['_', 'i', 'i', 'ss'], mb_strtolower(trim((string)$k), 'UTF-8'));
                    if (!preg_match('/^birim_fiyat(_\d+)?$/', $kNorm)) continue;
                    if (is_array($v)) continue;
                    $dec = $parse($v);
                    if ($dec !== null && (float)$dec > 0) {
                        $bestVal = $dec;
                        break;
                    }
                }
                if ($bestVal === null) {
                    $bestVal = $parse(
                        $find(['t1_birim_fiyat', 't1 birim fiyat'])
                        ?? $find(['t2_birim_fiyat', 't2 birim fiyat'])
                        ?? $find(['t3_birim_fiyat', 't3 birim fiyat'])
                    );
                }
                return $bestVal;
            })($payload),
            'dagitim_birim_fiyat' => $parse($find(['dagitim_birim_fiyat', 'dagitim birim fiyat'])),
            'aktif_tuketim_tl' => $parse($find(['akti̇f tüketi̇m', 'aktif tüketim', 'aktif tuketim'])),
            'dagitim_bedeli' => $parse($find(['dagitim bedeli', 'dagitim_bedeli'])),
            'dagitim_bedeli_ek' => $parse($find(['dagitim_bedeli_ek', 'dagitim bedeli ek'])),
            'enerji_fonu' => $parse($find(['ee_fonu', 'enerji_fonu', 'enerji fonu'])),
            'reaktif_tl' => $parse($find([
                'reakti̇f tüketi̇m',
                'reaktif tüketim',
                'reaktif_tl',
                'reaktif_miktar',
                'reaktif bedel',
                'reaktif bedeli',
                'reaktif tutar',
                'reaktif tutari',
                'reakti̇f bedel',
                'reakti̇f bedeli',
                'reakti̇f tutar',
                'reakti̇f tutari',
                'reakti̇f tüketi̇m bedeli̇',
                'reaktif tüketim bedeli'
            ])),
            'acma_kapama_bedeli' => $parse($find(['acma_kapama_bedeli', 'acma kapama bedeli'])),
            'gecikme_tutari' => $parse($find(['devir_gecikme', 'gecikme_tutari', 'gecikme tutari'])),
            'trt_fonu' => $parse($find(['trt_payi', 'trt fonu', 'trt_fonu'])),
            'btv' => $parse($find(['beledi̇ye vergi̇si̇', 'belediye vergisi', 'btv', 'b.t.v.'])),
            'btv_orani' => $parse($find(['btv_orani', 'btv orani'])),
            'fatura_tutari' => $parse($find(['toplam tutar', 'fatura_tutar'])),
            'fatura_tutari_ek' => $parse($find(['fatura_tutari_ek', 'fatura tutari ek'])),
            'kdv' => $parse($find(['k.d.v.', 'kdv', 'k d v'])),
            'genel_toplam' => $parse($find(['toplam tutar', 'fatura_tutar', 'fatura tutari'])),

            'tutar_toplam' => $parse($find(['toplam tutar', 'fatura_tutar'])),
        ];
    }


    public function auditTotals(Request $request)
    {
        try {
            $donem = $request->donem ?? '2026-03';
            $file = public_path('Şuski 202603 data.xlsx');
            if (!file_exists($file))
                return "Excel dosyası bulunamadı: $file";

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $headers = array_shift($rows);
            $colFatura = null;
            $colTutar = null;

            foreach ($headers as $k => $v) {
                $vU = mb_strtoupper((string) $v, 'UTF-8');
                if (str_contains($vU, 'FATURA NO'))
                    $colFatura = $k;
                if ($vU === 'FATURA_TUTAR' || $vU === 'FATURA TUTAR')
                    $colTutar = $k;
            }

            if (!$colFatura || !$colTutar)
                return "Excel sütunları eşleşmedi. (Fatura: $colFatura, Tutar: $colTutar)";

            $excelData = [];
            $excelTotal = 0;
            foreach ($rows as $row) {
                $fNo = trim((string) $row[$colFatura]);
                $tutar = app(\App\Services\ExcelImportService::class)->parseDecimal($row[$colTutar]);
                if ($fNo) {
                    $excelData[$fNo] = $tutar;
                    $excelTotal += $tutar;
                }
            }

            $dbTotals = \App\Models\KesinlesenFatura::where('donem', $donem)
                ->select('fatura_no', 'genel_toplam')
                ->get()
                ->keyBy('fatura_no');

            $dbTotalAmount = $dbTotals->sum('genel_toplam');

            $errors = [];
            $missingInDb = [];
            $mismatch = [];

            foreach ($excelData as $fNo => $eTutar) {
                if (!isset($dbTotals[$fNo])) {
                    $missingInDb[] = $fNo;
                } else {
                    $dbTutar = (float) $dbTotals[$fNo]->genel_toplam;
                    if (abs($dbTutar - $eTutar) > 0.01) {
                        $mismatch[] = [
                            'no' => $fNo,
                            'excel' => $eTutar,
                            'db' => $dbTutar,
                            'fark' => $dbTutar - $eTutar
                        ];
                    }
                }
            }

            return response()->json([
                'ozet' => [
                    'excel_toplam_kayit' => count($excelData),
                    'db_toplam_kayit' => $dbTotals->count(),
                    'excel_toplam_tutar' => $excelTotal,
                    'db_toplam_tutar' => $dbTotalAmount,
                    'fark' => $dbTotalAmount - $excelTotal,
                ],
                'eksik_kayitlar' => array_slice($missingInDb, 0, 100), // İlk 100 tanesi
                'tutar_uyusmazliklari' => array_slice($mismatch, 0, 100),
            ]);

        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}

