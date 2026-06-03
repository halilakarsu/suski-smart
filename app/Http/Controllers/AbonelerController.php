<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schema;

class AbonelerController extends Controller
{
    public function index(Request $request)
    {
        $hasIsNew     = Schema::hasColumn('aboneler', 'is_new');
        $hasIsUpdated = Schema::hasColumn('aboneler', 'is_updated');
        $hasBolgeKodu = Schema::hasColumn('aboneler', 'BOLGE_KODU');
        $hasIsActive  = Schema::hasColumn('aboneler', 'is_active');

        $query = \App\Models\Aboneler::query();
        $tab      = $request->get('tab', 'all'); // 'all', 'passive', 'new', 'sayac_guncelleme', 'bilgi_guncelleme'
        $yerlesim = $request->get('yerlesim', 'all'); // 'all', 'koy', 'merkez', 'aritma'

        // 1. Status Filter (Tab)
        if ($tab === 'passive' && $hasIsActive) {
            $query->where('is_active', false);
        } elseif ($tab === 'new' && $hasIsNew) {
            $query->where('is_new', true);
            if ($hasIsActive) $query->where('is_active', true);
        } elseif ($tab === 'sayac_guncelleme' && $hasIsUpdated) {
            $query->where('is_updated', true)
                  ->whereNotNull('prev_sayac_seri_no')
                  ->whereRaw('prev_sayac_seri_no != SAYAC_SERI_NO');
            if ($hasIsActive) $query->where('is_active', true);
        } elseif ($tab === 'bilgi_guncelleme' && $hasIsUpdated) {
            $query->where('is_updated', true)
                  ->where(function($q) {
                      $q->whereNotNull('prev_adres')
                        ->orWhereNotNull('prev_hesap_adi')
                        ->orWhereNotNull('prev_abone_grubu')
                        ->orWhereNotNull('prev_baglanti_grubu')
                        ->orWhereNotNull('prev_tarife');
                  });
            if ($hasIsActive) $query->where('is_active', true);
        } elseif ($tab === 'all') {
            if ($hasIsActive) $query->where('is_active', true);
        }
        // If tab is 'total_all' (hypothetical), we don't apply is_active filter.

        // 2. Settlement Type Filter (Yerlesim)
        if ($yerlesim === 'koy') {
            $query->where('yerlesim_turu', 'KÖY');
        } elseif ($yerlesim === 'merkez') {
            $query->where('yerlesim_turu', 'MERKEZ');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ABONE_TESIS_NO', 'like', "%{$search}%")
                  ->orWhere('SAYAC_SERI_NO', 'like', "%{$search}%")
                  ->orWhere('UNVAN', 'like', "%{$search}%")
                  ->orWhere('hesap_adi', 'like', "%{$search}%")
                  ->orWhere('abone_grubu', 'like', "%{$search}%")
                  ->orWhere('tarife', 'like', "%{$search}%")
                  ->orWhere('ADRES', 'like', "%{$search}%");
            });
        }

        $aboneler    = $query->with('bolge')->latest()->paginate(20)->withQueryString();

        $baseActive  = $hasIsActive ? \App\Models\Aboneler::where('is_active', true) : \App\Models\Aboneler::query();
        $basePassive = $hasIsActive ? \App\Models\Aboneler::where('is_active', false) : null;

        $totalCount   = $hasIsActive ? (clone $baseActive)->count() : \App\Models\Aboneler::count();
        $passiveCount = $hasIsActive ? (clone $basePassive)->count() : 0;
        $newCount     = $hasIsNew ? (clone $baseActive)->where('is_new', true)->count() : 0;

        // Sayac guncelleme sayisi: prev_sayac_seri_no dolu ve mevcut sayactan farkli
        $sayacGuncellemeSayisi = $hasIsUpdated ? (clone $baseActive)
            ->where('is_updated', true)
            ->whereNotNull('prev_sayac_seri_no')
            ->whereRaw('prev_sayac_seri_no != SAYAC_SERI_NO')
            ->count() : 0;

        // Bilgi guncelleme sayisi: diger prev_ alanlari
        $bilgiGuncellemeSayisi = $hasIsUpdated ? (clone $baseActive)
            ->where('is_updated', true)
            ->where(function($q) {
                $q->whereNotNull('prev_adres')
                  ->orWhereNotNull('prev_hesap_adi')
                  ->orWhereNotNull('prev_abone_grubu')
                  ->orWhereNotNull('prev_baglanti_grubu')
                  ->orWhereNotNull('prev_tarife');
            })
            ->count() : 0;

        $hasIsUpdated_tab = $hasIsUpdated && ($sayacGuncellemeSayisi > 0 || $bilgiGuncellemeSayisi > 0);

        // Köy / Merkez sayımları
        $koyCount    = (clone $baseActive)->where('yerlesim_turu', 'KÖY')->count();
        $merkezCount = (clone $baseActive)->where('yerlesim_turu', 'MERKEZ')->count();
        $bolgeler = \App\Models\Bolgeler::orderBy('bolge_adi')->get();

        return view('aboneler.index', compact(
            'aboneler', 'totalCount', 'newCount',
            'sayacGuncellemeSayisi', 'bilgiGuncellemeSayisi', 'hasIsUpdated_tab',
            'passiveCount', 'hasIsNew', 'hasIsUpdated', 'hasBolgeKodu', 'hasIsActive',
            'koyCount', 'merkezCount', 'bolgeler'
        ));
    }


    public function show($id)
    {
        $abone = \App\Models\Aboneler::findOrFail($id);
        
        $havuzSayaclar = \App\Models\BeklemeKontrolHavuzu::with('importLog:id,donem')
            ->where('tesisat_no', $abone->ABONE_TESIS_NO)
            ->whereNotNull('sayac_seri_no')
            ->where('sayac_seri_no', '!=', '')
            ->select('tesisat_no', 'sayac_seri_no', 'import_log_id', 'id')
            ->orderBy('id', 'desc')
            ->get();
            
        $metersWithDates = [];

        foreach ($havuzSayaclar as $row) {
            $sNo = $row->sayac_seri_no;
            if (!isset($metersWithDates[$sNo])) {
                $donem = $row->importLog->donem ?? 'Bilinmiyor';
                $metersWithDates[$sNo] = $donem;
            }
        }

        // Eğer mevcut (aktif) sayaç havuzdan gelmediyse (manuel eklendiyse) en başa koy
        if ($abone->SAYAC_SERI_NO && !isset($metersWithDates[$abone->SAYAC_SERI_NO])) {
            $metersWithDates = [$abone->SAYAC_SERI_NO => 'Sistem Kaydı'] + $metersWithDates;
        }

        if ($abone->prev_sayac_seri_no && !isset($metersWithDates[$abone->prev_sayac_seri_no])) {
            $metersWithDates[$abone->prev_sayac_seri_no] = 'Eski Kayıt';
        }

        $farkliSayaclar = [];
        foreach ($metersWithDates as $sNo => $tarih) {
            $farkliSayaclar[] = (object)[ 'no' => $sNo, 'tarih' => $tarih ];
        }

        return view('aboneler.show', compact('abone', 'farkliSayaclar'));
    }

    public function create()
    {
        return redirect()->route('aboneler.index', ['create' => 1]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ABONE_TESIS_NO' => 'required|unique:aboneler,ABONE_TESIS_NO',
            'UNVAN'          => 'nullable|string|max:255',
            'BOLGE_ADI'      => 'nullable|string|max:255',
            'ADRES'          => 'nullable|string',
            'SAYAC_SERI_NO'  => 'nullable|string|max:255',
            'hesap_adi'      => 'nullable|string|max:255',
            'abone_grubu'    => 'nullable|string|max:255',
            'baglanti_grubu' => 'nullable|string|max:50',
            'tarife'         => 'nullable|string|max:100',
            'notlar'         => 'nullable|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($validated, &$abone) {
                $abone = \App\Models\Aboneler::create($validated);

                ActivityLog::create([
                    'user_id'     => auth()->id(),
                    'action'      => 'abone_eklendi',
                    'model'       => 'Aboneler',
                    'model_id'    => $abone->id,
                    'description' => "Sisteme yeni abone (Tesisat No: {$abone->ABONE_TESIS_NO}) eklendi.",
                    'new_data'    => $validated,
                    'ip'          => request()->ip(),
                    'user_agent'  => request()->userAgent()
                ]);
            });

            return redirect()->route('aboneler.index')->with('success', 'Abone başarıyla kaydedildi.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        abort(404);
    }

    public function update(Request $request, $id)
    {
        $abone = \App\Models\Aboneler::findOrFail($id);
        $old_data = $abone->toArray();

        $validated = $request->validate([
            'BOLGE_ADI'      => 'nullable|string|max:255',
            'ADRES'          => 'nullable|string',
            'SAYAC_SERI_NO'  => 'nullable|string|max:255',
            'hesap_adi'      => 'nullable|string|max:255',
            'baglanti_grubu' => 'nullable|string|max:50',
            'abone_grubu'    => 'nullable|string|max:255',
            'tarife'         => 'nullable|string|max:100',
            'notlar'         => 'nullable|string',
            'yerlesim_turu'  => 'nullable|in:KÖY,MERKEZ',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($abone, $validated, $old_data) {
                $tarihStr = now()->toDateTimeString();

                // Sayaç değişimini geçmişe kaydet
                $yeniSayac = $validated['SAYAC_SERI_NO'] ?? null;
                if ($yeniSayac && $yeniSayac !== $abone->SAYAC_SERI_NO) {
                    $abone->updateSayacWithHistory($yeniSayac, $tarihStr);
                }
                unset($validated['SAYAC_SERI_NO']); // updateSayacWithHistory zaten kaydetti

                // Diğer alanlar için kronolojik tarihçe kaydı
                $degisiklikler = $abone->updateWithHistory($validated, $tarihStr);

                // Yerleşim türü direkt güncellenir
                if (array_key_exists('yerlesim_turu', $validated)) {
                    $abone->update(['yerlesim_turu' => $validated['yerlesim_turu']]);
                }

                // is_updated = false (manuel düzenleme = kullanıcı inceledi)
                $abone->update(['is_updated' => false]);

                ActivityLog::create([
                    'user_id'     => auth()->id(),
                    'action'      => 'abone_guncellendi',
                    'model'       => 'Aboneler',
                    'model_id'    => $abone->id,
                    'description' => "Abone (Tesisat No: {$abone->ABONE_TESIS_NO}) profil bilgileri güncellendi. " . (empty($degisiklikler) ? 'Değişiklik yok.' : count($degisiklikler) . ' alan değişti.'),
                    'old_data'    => $old_data,
                    'new_data'    => $validated,
                    'ip'          => request()->ip(),
                    'user_agent'  => request()->userAgent()
                ]);
            });

            return redirect()->route('aboneler.index')->with('success', 'Abone bilgileri başarıyla güncellendi.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function destroy($id)
    {
        $abone = \App\Models\Aboneler::findOrFail($id);
        $old_data = $abone->toArray();
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($abone, $old_data, $id) {
            $abone->delete();

            ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'abone_silindi',
                'model'       => 'Aboneler',
                'model_id'    => $id,
                'description' => "Abone (Tesisat No: {$old_data['ABONE_TESIS_NO']}) sistemden silindi.",
                'old_data'    => $old_data,
                'ip'          => request()->ip(),
                'user_agent'  => request()->userAgent()
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Abone başarıyla silindi.']);
    }

    /**
     * Aboneyi onayla: is_new/is_updated bayraklarini ve prev_ snapshot alanlarini temizle.
     * guncelleme_detay JSON gecmisi korunur (detay sayfasinda goruntulenir).
     */
    public function markOld($id)
    {
        if (!Schema::hasColumn('aboneler', 'is_new')) {
            return response()->json(['success' => false, 'message' => 'Migration henuz uygulanmamis.']);
        }

        $abone = \App\Models\Aboneler::findOrFail($id);

        $abone->update([
            'is_new'              => false,
            'is_updated'          => false,
            // Snapshot prev_ alanlarini temizle — kayit normal sekmeye dusuyor.
            // Tarihce guncelleme_detay JSON icinde sakli kalmaya devam eder.
            'prev_adres'          => null,
            'prev_sayac_seri_no'  => null,
            'prev_hesap_adi'      => null,
            'prev_abone_grubu'    => null,
            'prev_baglanti_grubu' => null,
            'prev_tarife'         => null,
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'abone_onaylandi',
            'model'       => 'Aboneler',
            'model_id'    => $abone->id,
            'description' => "Abone (Tesisat No: {$abone->ABONE_TESIS_NO}) incelendi ve onaylandi. Guncelleme bayraklari temizlendi.",
            'ip'          => request()->ip(),
            'user_agent'  => request()->userAgent()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Tum guncelleme bayraklarini tek seferde temizle (Tumunu Onayla).
     * is_new + is_updated bayraklari ve tum prev_ snapshot alanlari null yapilir.
     * guncelleme_detay JSON gecmisi korunur.
     */
    public function markAllOld(Request $request)
    {
        $hasIsNew     = Schema::hasColumn('aboneler', 'is_new');
        $hasIsUpdated = Schema::hasColumn('aboneler', 'is_updated');

        $query = \App\Models\Aboneler::query();
        $tab   = $request->get('tab', 'all');

        if ($tab === 'new' && $hasIsNew) {
            $query->where('is_new', true);
        } elseif ($tab === 'sayac_guncelleme' && $hasIsUpdated) {
            $query->where('is_updated', true)
                  ->whereNotNull('prev_sayac_seri_no')
                  ->whereRaw('prev_sayac_seri_no != SAYAC_SERI_NO');
        } elseif ($tab === 'bilgi_guncelleme' && $hasIsUpdated) {
            $query->where('is_updated', true)
                  ->where(function($q) {
                      $q->whereNotNull('prev_adres')
                        ->orWhereNotNull('prev_hesap_adi')
                        ->orWhereNotNull('prev_abone_grubu')
                        ->orWhereNotNull('prev_baglanti_grubu')
                        ->orWhereNotNull('prev_tarife');
                  });
        } else {
            // "all" veya diger durumlar: her iki bayragi da kapsar
            $query->where(function($q) {
                $q->where('is_new', true)->orWhere('is_updated', true);
            });
        }

        $updatePayload = [
            'is_new'              => false,
            'is_updated'          => false,
            'prev_adres'          => null,
            'prev_sayac_seri_no'  => null,
            'prev_hesap_adi'      => null,
            'prev_abone_grubu'    => null,
            'prev_baglanti_grubu' => null,
            'prev_tarife'         => null,
        ];

        $count = (clone $query)->count();
        $query->update($updatePayload);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'tumunu_onayla',
            'model'       => 'Aboneler',
            'description' => "{$count} abone kaydinin guncelleme bayraklari temizlendi (Tab: {$tab}).",
            'ip'          => request()->ip(),
            'user_agent'  => request()->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'count'   => $count,
            'message' => "{$count} abone onaylandi ve aktif sekmeye tasindi.",
        ]);
    }

    /**
     * Secilen aboneleri onayla (is_new/is_updated temizle).
     */
    public function markSelectedOld(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Lutfen en az bir abone secin.']);
        }

        $updatePayload = [
            'is_new'              => false,
            'is_updated'          => false,
            'prev_adres'          => null,
            'prev_sayac_seri_no'  => null,
            'prev_hesap_adi'      => null,
            'prev_abone_grubu'    => null,
            'prev_baglanti_grubu' => null,
            'prev_tarife'         => null,
        ];

        $count = \App\Models\Aboneler::whereIn('id', $ids)->update($updatePayload);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'secilenleri_onayla',
            'model'       => 'Aboneler',
            'description' => "{$count} secilen abone kaydinin guncelleme bayraklari temizlendi.",
            'ip'          => request()->ip(),
            'user_agent'  => request()->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'count'   => $count,
            'message' => "{$count} abone onaylandi ve aktif sekmeye tasindi.",
        ]);
    }

    /**
     * Abonenin aktif/pasif durumunu değiştir
     */
    public function toggleActive($id)
    {
        if (!Schema::hasColumn('aboneler', 'is_active')) {
            return response()->json(['success' => false, 'message' => 'Migration henüz uygulanmamış.']);
        }

        $abone = \App\Models\Aboneler::findOrFail($id);
        $newStatus = !$abone->is_active;
        
        $updateData = ['is_active' => $newStatus];
        if ($newStatus) {
            $updateData['passive_reason'] = null;
        }

        $abone->update($updateData);

        $durum = $newStatus ? 'Aktif' : 'Pasif';

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => $newStatus ? 'abone_aktif_edildi' : 'abone_pasif_edildi',
            'model'       => 'Aboneler',
            'model_id'    => $abone->id,
            'description' => "Abone (Tesisat No: {$abone->ABONE_TESIS_NO}) durumu {$durum} olarak değiştirildi.",
            'ip'          => request()->ip(),
            'user_agent'  => request()->userAgent()
        ]);

        return response()->json([
            'success'    => true,
            'is_active'  => $newStatus,
            'message'    => "Abone {$durum} olarak işaretlendi."
        ]);
    }

    /**
     * Otomatik Pasif Analizi:
     * Son okuması 120 günü geçmiş veya 3 aydır faturası girmemiş aboneleri pasife çeker.
     */
    public function syncPassiveStatus()
    {
        $hasIsActive = Schema::hasColumn('aboneler', 'is_active');
        if (!$hasIsActive) return redirect()->back()->with('error', 'Pasiflik özelliği henüz aktif değil.');

        $thresholdDays = 120; // 4 Ay
        $today = now();
        $passiveCount = 0;
        $reactivatedCount = 0;

        // 1. Tüm aboneleri tara (Aktif olanları pasife çekmek, hatalı pasifleri gerekirse düzeltmek için)
        $tumAboneler = \App\Models\Aboneler::all();

        foreach ($tumAboneler as $abone) {
            // En son faturasını bul (Gelecek tarihli hatalı okumaları filtrele)
            $sonFatura = \App\Models\KesinlesenFatura::where('tesisat_no', $abone->ABONE_TESIS_NO)
                ->where('son_okuma', '<=', $today->format('Y-m-d'))
                ->orderBy('son_okuma', 'desc')
                ->first();

            $shouldBePassive = false;
            $reason = null;

            if ($sonFatura && $sonFatura->son_okuma) {
                $gunFarki = intval($today->diffInDays($sonFatura->son_okuma));
                if ($gunFarki > $thresholdDays) {
                    $shouldBePassive = true;
                    $reason = "Son okumadan bu yana " . $gunFarki . " gün geçti. (Son Okuma: " . $sonFatura->son_okuma->format('d.m.Y') . ")";
                }
            } else {
                // Hiç faturası yoksa ve eklenme tarihi 4 ayı geçmişse
                $eklenmeFarki = intval($today->diffInDays($abone->created_at));
                if ($eklenmeFarki > $thresholdDays) {
                    $shouldBePassive = true;
                    $reason = "Sisteme eklendiğinden beri " . $eklenmeFarki . " gündür hiç fatura girişi yapılmadı.";
                }
            }

            // Durum Güncelleme
            if ($shouldBePassive && $abone->is_active) {
                $abone->update([
                    'is_active' => false,
                    'passive_reason' => $reason,
                    'last_invoice_date' => $sonFatura ? $sonFatura->son_okuma : null
                ]);
                $passiveCount++;
            } elseif (!$shouldBePassive && !$abone->is_active && $abone->passive_reason) {
                // Eğer daha önce "Hatalı Gelecek Tarih" yüzünden pasife çekilmişse (2030 vakası)
                // Ama şimdi shouldBePassive = false ise (çünkü 2030'u filtreledik), geri aktif edelim
                $abone->update([
                    'is_active' => true,
                    'passive_reason' => null
                ]);
                $reactivatedCount++;
            }
        }

        $message = "Analiz tamamlandı.";
        if ($passiveCount > 0) $message .= " {$passiveCount} yeni pasif abone tespit edildi.";
        if ($reactivatedCount > 0) $message .= " {$reactivatedCount} hatalı pasif kayıt düzeltildi.";
        
        if ($passiveCount > 0 || $reactivatedCount > 0) {
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'pasif_senkronizasyon',
                'model'       => 'Aboneler',
                'description' => "Analiz sonucu: {$passiveCount} pasif, {$reactivatedCount} düzeltme yapıldı.",
                'ip'          => request()->ip(),
                'user_agent'  => request()->userAgent()
            ]);
            return redirect()->route('aboneler.index', ['tab' => 'passive'])->with('success', $message);
        }

        return redirect()->route('aboneler.index', ['tab' => 'passive'])->with('info', "Analiz tamamlandı. Durum değişikliği gerekmedi.");
    }
}
