<?php

namespace App\Services;

use App\Models\Aboneler;
use App\Models\BeklemeKontrolHavuzu;
use App\Models\Bolgeler;
use App\Models\Hamveri;
use App\Models\ImportLog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportService
{
    /**
     * Aşama 1: Excel dosyasını okuyup hamveri tablosuna yazar.
     * Döndürür: [toplam_satir, eklenen, atlanan, hatalar[]]
     */
    public function importToRaw(ImportLog $importLog): array
    {
        $importLog->update([
            'durum' => 'isleniyor',
            'isleme_basladi' => now(),
        ]);

        $stats = [
            'toplam' => 0,
            'eklenen' => 0,
            'atlanan' => 0,
            'hatalar' => [],
        ];

        try {
            $fullPath = \Illuminate\Support\Facades\Storage::disk($importLog->disk)->path($importLog->yol);

            // Sadece ilk 150 satırı okumak için filtre oluştur (Bunu detectDonem'den kopyaladık ama burada tamamını okuyacağız)
            // Ancak OOM'u engellemek için Chunking veya ReadDataOnly kullanıyoruz.
            $reader = IOFactory::createReaderForFile($fullPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, false, false);

            if (empty($rows)) {
                throw new \Exception('Excel dosyası boş veya okunamadı.');
            }

            // Başlık satırını dinamik bul (ilk 50 satırı kontrol et)
            $headerRowOffset = 0;
            $bulundu = false;

            foreach (array_slice($rows, 0, 50) as $rowIndex => $row) {
                // Her hücredeki metni alfabetik (harf+rakam) temizle
                $cleanRow = array_map(function ($h) {
                    return preg_replace('/[^a-z0-9_]/u', '', mb_strtolower(trim((string) $h), 'UTF-8'));
                }, $row);

                // 'fatura', 'tesisat' veya 'okuma' kelimeleri barındırıyor mu kontrol et
                $eslesenSayisi = 0;
                foreach ($cleanRow as $h) {
                    if (str_contains($h, 'fatura') || str_contains($h, 'tesisat') || str_contains($h, 'okuma')) {
                        $eslesenSayisi++;
                    }
                }

                // En az 2 eşleşme varsa bunu başlık satırı kabul et
                if ($eslesenSayisi >= 2) {
                    $headerRowOffset = $rowIndex;
                    $bulundu = true;
                    break;
                }
            }

            // Orijinal başlık metnini al (fakat yine trim yapılı)
            // Boş hücre başlıklarını temizlemek için filtreleyelim ardından indisleri yeniden sıralamadan olduğu gibi bırakabiliriz
            // ya da filtreleriz. Eğer boş sütun başlıkları Excel'de varsa karışıklık yapabilir.
            $headers = array_map(fn ($h) => trim((string) $h), $rows[$headerRowOffset]);
            $headers = array_filter($headers, fn ($h) => $h !== ''); // Sadece dolu başlıkları al

            // ===== SÜTÜN DOĞRULAMASI (config/excel_import.php) =====
            // Kural tanımları servis sınıfında değil, config dosyasında yaşar.
            // Yeni bir şablon geldiğinde yalnızca config/excel_import.php güncellenir.

            $normalize = fn ($h) => mb_strtolower(trim((string) $h), 'UTF-8');

            $kabulMap = array_combine(
                array_map($normalize, config('excel_import.kabul_edilen')),
                config('excel_import.kabul_edilen')
            );
            $zorunluMap = array_combine(
                array_map($normalize, config('excel_import.zorunlu_sutunlar')),
                config('excel_import.zorunlu_sutunlar')
            );
            $fileMap = [];
            foreach ($headers as $h) {
                if (trim($h) !== '') {
                    $fileMap[$normalize($h)] = $h;
                }
            }

            $bilinmeyenler = array_diff_key($fileMap, $kabulMap);
            $eksikZorunlu = array_diff_key($zorunluMap, $fileMap);

            if (! empty($bilinmeyenler) || ! empty($eksikZorunlu)) {
                $errMsg = "\u0130\u00e7e Aktarma \u0130ptal Edildi! Excel s\u00fctun \u015fablonu ge\u00e7ersiz.\n";
                if (! empty($eksikZorunlu)) {
                    $errMsg .= "[ZORUNLU S\u00dcTÜN EKS\u0130K]: ".implode(', ', array_values($eksikZorunlu)).".\n";
                }
                if (! empty($bilinmeyenler)) {
                    $errMsg .= "[B\u0130L\u0130NMEYEN / \u0130Z\u0130N VER\u0130LMEYEN S\u00dcTÜNLAR]: ".implode(', ', array_values($bilinmeyenler)).".\n";
                    $errMsg .= "L\u00fctfen yaln\u0131zca da\u011f\u0131t\u0131m \u015firketi taraf\u0131ndan \u00fcretilen orijinal Excel \u015fablonunu kullan\u0131n.";
                }
                throw new \Exception($errMsg);
            }
            // =======================================================

            // Yoksayılacak s\u00fctunlar config'den gelir
            $yoksayilanTemiz = array_map($normalize, config('excel_import.yoksayilan'));

            $headersMetadata = [];
            foreach ($headers as $idx => $h) {
                $headersMetadata[$idx] = ! in_array($normalize($h), $yoksayilanTemiz);
            }

            // Sutun eşleştirmesini kaydet
            $importLog->update(['sutun_eslestirme' => $headers]);

            // Başlık satırından sonraki satırları veri olarak al
            $dataRows = array_slice($rows, $headerRowOffset + 1);

            $stats['toplam'] = count($dataRows);
            $importLog->update(['toplam_satir' => $stats['toplam']]);

            $chunk = [];
            $chunkSize = 200;
            $satirNo = $headerRowOffset + 2;
            $siraNoCount = 1; // 1'den başlayan sayaç

            foreach ($dataRows as $row) {
                // Tamamen boş satırları atla
                $filtered = array_filter($row, fn ($v) => $v !== null && $v !== '');
                if (empty($filtered)) {
                    $stats['atlanan']++;
                    $satirNo++;

                    continue;
                }

                // Sütun kısıtlaması yerine payload döngüsünde güvenli eşleme yapıyoruz
                $rowValues = array_values($row);
                $payload = ['SIRA NO' => $siraNoCount]; // Sayaç ekle

                foreach ($headers as $k => $h) {
                    // Sadece $headersMetadata[$k] true olan (kalacak) sütunları ve değerleri al
                    if (! empty($headersMetadata[$k]) && isset($rowValues[$k])) {
                        // Duplicate header varsa key'e _2, _3 suffix ekle (üzerine yazılmasın)
                        $key = $h;
                        $suffix = 1;
                        while (array_key_exists($key, $payload)) {
                            $suffix++;
                            $key = $h.'_'.$suffix;
                        }
                        $payload[$key] = $rowValues[$k];
                    }
                }

                // Fatura No sütununu bul (büyük/küçük harf duyarsız)
                $faturaNo = $this->findField($payload, [
                    'fatura no', 'fatura_no', 'faturano', 'invoice no', 'invoice_no',
                ]);

                $rowHash = md5(serialize($payload));

                $chunk[] = [
                    'import_log_id' => $importLog->id,
                    'fatura_no' => $faturaNo,
                    'row_hash' => $rowHash,
                    'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $satirNo++;
                $siraNoCount++; // Her satır için artır

                if (count($chunk) >= $chunkSize) {
                    $this->insertChunk($chunk, $stats);
                    $chunk = [];

                    // İlerlemeyi güncelle
                    $importLog->increment('islenen_satir', $chunkSize);
                }
            }

            // Kalan chunk
            if (! empty($chunk)) {
                $this->insertChunk($chunk, $stats);
                $importLog->increment('islenen_satir', count($chunk));
            }

            $importLog->update([
                'hata_sayisi' => count($stats['hatalar']),
            ]);

        } catch (\Throwable $e) {
            $importLog->update([
                'durum' => 'hata',
                'notlar' => $e->getMessage(),
            ]);
            Log::error('ExcelImportService hata', [
                'import_log_id' => $importLog->id,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $stats;
    }

    /**
     * Aşama 2: hamveri'den bekleme_kontrol_havuzu'na taşı.
     * Mevcut bekleme_kontrol_havuzu ile karşılaştırarak: yeni / mükerrer / değişti durumunu belirler.
     */
    public function promoteToStaging(ImportLog $importLog): array
    {
        // ── AUTO-FIX: Ensure payload column exists (Tarayıcıdan migration çalıştırılamadığı için otomatik ekler)
        if (! Schema::hasColumn('bekleme_kontrol_havuzu', 'payload')) {
            Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
                $table->json('payload')->nullable()->after('current_row_hash');
            });
        }

        if (! Schema::hasColumn('aboneler', 'baglanti_grubu')) {
            Schema::table('aboneler', function (Blueprint $table) {
                $table->string('baglanti_grubu')->nullable()->after('SAYAC_SERI_NO');
                $table->string('abone_grubu')->nullable()->after('baglanti_grubu');
                $table->string('hesap_adi')->nullable()->after('abone_grubu');
                $table->string('tarife')->nullable()->after('hesap_adi');
                $table->string('prev_abone_grubu')->nullable()->after('prev_sayac_seri_no');
                $table->string('prev_baglanti_grubu')->nullable()->after('prev_abone_grubu');
                $table->string('prev_hesap_adi')->nullable()->after('prev_baglanti_grubu');
                $table->string('prev_tarife')->nullable()->after('prev_hesap_adi');
            });
        }

        if (! Schema::hasColumn('bekleme_kontrol_havuzu', 'hesap_adi')) {
            Schema::table('bekleme_kontrol_havuzu', function (Blueprint $table) {
                $table->string('hesap_adi')->nullable()->after('tesisat_no');
            });
        }

        $stats = [
            'yeni' => 0,
            'mukerrer' => 0,
            'degisti' => 0,
            'reaktif' => 0,
            'bekleyen' => 0,
            'yeni_abone' => 0,
            'yeni_bolge' => 0,
            'guncellenen_abone' => 0,
            'guncellenen_abone_listesi' => [],
        ];

        // Performans için Hamveri kayıtlarını chunk ile oku ve toplu işlemler yap
        Hamveri::where('import_log_id', $importLog->id)
            ->whereNotNull('fatura_no')
            ->chunk(100, function ($rawRows) use (&$stats, $importLog) {

                $faturaNolar = $rawRows->pluck('fatura_no')->toArray();
                $tesisatNolar = $rawRows->map(fn ($r) => $this->findField($r->payload, ['tesisat', 'tesisat no', 'tesisat_no']))->filter()->unique()->toArray();

                // Mevcut kayıtları bir kerede çek
                $existings = BeklemeKontrolHavuzu::whereIn('fatura_no', $faturaNolar)->get()->keyBy('fatura_no');
                $aboneler = Aboneler::whereIn('ABONE_TESIS_NO', $tesisatNolar)->get()->keyBy('ABONE_TESIS_NO');
                $bolgeler = Bolgeler::all()->keyBy('bolge_adi');

                foreach ($rawRows as $raw) {
                    $existing = $existings[$raw->fatura_no] ?? null;

                    if (! $existing) {
                        $kayitDurumu = 'yeni';
                        $stats['yeni']++;
                    } elseif ($existing->current_row_hash === $raw->row_hash) {
                        $kayitDurumu = 'mukerrer';
                        $stats['mukerrer']++;
                    } else {
                        $kayitDurumu = 'degisti';
                        $stats['degisti']++;
                    }

                    $payload = $raw->payload;

                    // Bağlantı Grubu Mantığı: OG_DUR 1 ise OG, değilse AG
                    $ogDur = (string) $this->findField($payload, ['og_dur', 'og dur', 'ogdur']);
                    $baglantiGrubu = ($ogDur === '1') ? 'OG' : 'AG';

                    // Abone Durumu Tespit (yeni|guncellendi|mevcut)
                    $aboneDurumFlag = 'mevcut';

                    // ── Aboneler tablosu kontrolü ──
                    $tesisat = $this->findField($payload, ['tesisat', 'tesisat_no', 'tesisat no']);
                    if ($tesisat) {
                        $mevcutAbone = $aboneler[$tesisat] ?? null;
                        $yeniAboneData = [
                            'BOLGE_ADI' => $this->findField($payload, ['bolge_adi', 'bolge adi', 'dagitim']),
                            'ADRES' => $this->findField($payload, ['adres', 'a d r e s']),
                            'SAYAC_SERI_NO' => $this->findField($payload, ['sayac seri no', 'sayac_seri_no', 'sayac no', 'sayac_no']),
                            'baglanti_grubu' => $this->findField($payload, ['baglanti_dur', 'baglanti dur']) ?? $baglantiGrubu,
                            'abone_grubu' => $this->findField($payload, ['abone_grup_adi', 'abone grup adi', 'abone grubu']),
                            'hesap_adi' => $this->findField($payload, ['hesap_adi', 'hesap adi']),
                            'tarife' => $this->findField($payload, ['trf', 'tarife', 'tarife_kodu', 'fn']),
                            'carpan' => $this->parseDecimal($this->findField($payload, ['carpan', 'çarpan'])) ?? 1,
                            'OG_durumu' => $baglantiGrubu === 'OG',
                            'dagitim_merkezi' => $this->findField($payload, ['alt_isletme_adi', 'alt isletme adi', 'ilce', 'ilçe']),
                            'PMUM' => $this->findField($payload, ['pmum_id', 'pmum id', 'uniped', 'karne_kullanim']),
                        ];

                        // Otomatik Bölge Tespiti
                        $bolgeKoduRaw = $this->findField($payload, ['blg', 'f_bolge_kodu', 'ilce_kodu', 'ilce kodu']);
                        $bolgeAdiRaw = $this->findField($payload, ['bolge_adi', 'bolge adi', 'dagitim']);

                        $detectedBolge = null;
                        if ($bolgeKoduRaw && ! in_array((string) $bolgeKoduRaw, ['31', '91'])) {
                            $detectedBolge = Bolgeler::where('bolge_kodu', $bolgeKoduRaw)->first();
                        }

                        if (! $detectedBolge && $bolgeAdiRaw) {
                            $detectedBolge = $bolgeler[$bolgeAdiRaw] ?? null;
                        }

                        // Eğer tespit edilemediyse ve abone zaten varsa, abonenin mevcut bölgesini koru
                        if (! $detectedBolge && $mevcutAbone) {
                            $yeniAboneData['BOLGE_KODU'] = $mevcutAbone->BOLGE_KODU;
                            $yeniAboneData['BOLGE_ADI'] = $mevcutAbone->BOLGE_ADI;
                            $yeniAboneData['dagitim_merkezi'] = $mevcutAbone->dagitim_merkezi;
                        } elseif ($detectedBolge) {
                            $yeniAboneData['BOLGE_KODU'] = $detectedBolge->bolge_kodu;
                            $yeniAboneData['BOLGE_ADI'] = $detectedBolge->bolge_adi;
                            $yeniAboneData['dagitim_merkezi'] = $detectedBolge->bolge_adi;
                        }

                        if (! $mevcutAbone) {
                            $yeniAboneData['is_new'] = true;
                            $yeniAboneData['created_via'] = 'import';
                            $yeniAboneData['import_log_id'] = $importLog->id;
                            Aboneler::create(array_merge(['ABONE_TESIS_NO' => $tesisat], $yeniAboneData));
                            $stats['yeni_abone']++;
                            $aboneDurumFlag = 'yeni';
                        } else {

                            // Mevcut abone: bilgilerde değişiklik var mı?
                            $eskiSayac = $mevcutAbone->SAYAC_SERI_NO;

                            $tarihStr = $importLog->created_at ? $importLog->created_at->toDateTimeString() : now()->toDateTimeString();
                            $degisiklikler = $mevcutAbone->updateWithHistory($yeniAboneData, $tarihStr);

                            if ($eskiSayac !== $yeniAboneData['SAYAC_SERI_NO'] && ! empty($yeniAboneData['SAYAC_SERI_NO'])) {
                                $mevcutAbone->updateSayacWithHistory($yeniAboneData['SAYAC_SERI_NO'], $tarihStr);
                                $degisiklikler['SAYAC_SERI_NO'] = true;
                            }

                            // Eğer herhangi bir bilgi değiştiyse
                            if (! empty($degisiklikler)) {
                                // UpdateWithHistory zaten ->save() yapıyor ama biz is_updated'ı işaretliyoruz
                                $mevcutAbone->update(['is_updated' => true]);

                                $stats['guncellenen_abone']++;
                                $stats['guncellenen_abone_listesi'][] = [
                                    'tesisat_no' => $tesisat,
                                    'guncelleme_tarihi' => now()->format('d.m.Y H:i'),
                                ];
                                $aboneDurumFlag = 'guncellendi'; // Sayaç değişimi vb.
                            }
                        }
                    }

                    // ── Bolgeler tablosu kontrolü ──
                    // Kullanıcı isteği üzerine ilk yüklemede bölge güncelleme ve ekleme kontrolü iptal edilmiştir.
                    $bolgeAdi = $this->findField($payload, ['bolge_adi', 'bolge adi', 'dagitim', 'dagilim']);
                    if ($bolgeAdi) {
                        $bolgeVar = $bolgeler[$bolgeAdi] ?? null;
                        if (! $bolgeVar) {
                            // $stats['yeni_bolge']++; (Sistem otomatik bölge eklemesin)
                        }
                    }

                    // ── ANOMALİ (MANTIKSAL HATA) TESPİTİ ───────────────────────────
                    $anomaliler = [];

                    // 1. Dinamik Reaktif Ceza Tespiti (Anomali + Tab Ayrıştırması için kritik)
                    $reaktifTl = 0;
                    foreach ($payload as $k => $v) {
                        $kL = mb_strtolower($k, 'UTF-8');
                        $kL = str_replace(['i̇', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'i', 'g', 'u', 's', 'o', 'c'], $kL);
                        if (str_contains($kL, 'enduktif') || str_contains($kL, 'kapasitif') || (str_contains($kL, 'reaktif') && (str_contains($kL, 'bedel') || str_contains($kL, 'tutar') || str_contains($kL, 'tuketim')))) {
                            $dec = $this->parseDecimal($v);
                            if ($dec > 0) {
                                $reaktifTl += $dec;
                            }
                        }
                    }

                    if ($reaktifTl > 0) {
                        $anomaliler[] = [
                            'kod' => 'reaktif_ceza',
                            'mesaj' => 'Abonede '.number_format($reaktifTl, 2, ',', '.').' TL değerinde Reaktif/Kapasitif sınır aşımı cezası tespit edilmiştir. Kompanzasyon panosu incelenmelidir.',
                        ];
                    }

                    if ($kayitDurumu !== 'mukerrer') {
                        if ($reaktifTl > 0) {
                            $stats['reaktif']++;
                        } else {
                            $stats['bekleyen']++;
                        }
                    }

                    // payload içerisine anomali ve abone durumunu göm
                    $payload['_anomaliler'] = $anomaliler;
                    $payload['_abone_durumu'] = $aboneDurumFlag;

                    $rawIlceKodu = $yeniAboneData['BOLGE_KODU'] ?? $this->findField($payload, ['blg', 'f_bolge_kodu', 'ilce_kodu', 'ilce kodu', 'bld']);
                    $rawIlce = $yeniAboneData['BOLGE_ADI'] ?? $this->findField($payload, ['alt_isletme_adi', 'alt isletme adi', 'ilce', 'ilçe']);
                    $rawDagitim = $yeniAboneData['BOLGE_ADI'] ?? $this->findField($payload, ['bolge_adi', 'bolge adi', 'dagitim', 'dağıtım']);
                    $dateReadings = $this->findDateReadingFields($payload);

                    if ($rawIlce && str_starts_with(trim($rawIlce), '=')) {
                        $resolvedBolge = \App\Models\Bolgeler::where('bolge_kodu', $rawIlceKodu)->first();
                        if ($resolvedBolge) {
                            $rawIlce = $resolvedBolge->bolge_adi;
                        }
                    }
                    if ($rawDagitim && str_starts_with(trim($rawDagitim), '=')) {
                        $resolvedBolge = \App\Models\Bolgeler::where('bolge_kodu', $rawIlceKodu)->first();
                        if ($resolvedBolge) {
                            $rawDagitim = $resolvedBolge->bolge_adi;
                        }
                    }

                    // BeklemeKontrolHavuzu'na upsert — GERÇEK Excel sütun adlarıyla eşleştirildi
                    BeklemeKontrolHavuzu::updateOrCreate(
                        ['fatura_no' => $raw->fatura_no],
                        [
                            'hamveri_id' => $raw->id,
                            'import_log_id' => $importLog->id,
                            'kayit_durumu' => $kayitDurumu,
                            'current_row_hash' => $raw->row_hash,
                            'payload' => $payload,

                            // ── Kimlik / Konum ─────────────────────────────────────────────
                            // Excel'de "SR" harf (D, vb.) belirtiyor, Sıra numarası değil.
                            'tesisat_no' => $this->findField($payload, ['tesisat', 'tesisat no', 'tesisat_no']),
                            'hesap_adi' => $this->findField($payload, ['hesap_adi', 'hesap adi']),
                            'sira_no' => $this->findField($payload, ['sira no', 'sira_no', 'sira']), // SR kullanılmaz
                            'pmum_id' => $this->findField($payload, ['pmum_id', 'pmum id', 'uniped', 'karne_kullanim']), // Genelde 1 dönmüş
                            'sayac_seri_no' => $this->findField($payload, ['sayac_no', 'sayac no', 'sayac seri no', 'sayac_seri_no']),
                            'carpan' => $this->parseDecimal($this->findField($payload, ['carpan', 'çarpan'])),
                            'adres' => $this->findField($payload, ['a d r e s', 'adres', 'adress']),
                            // DAĞITIM genelde DEDAS vs.. BOLGE_ADI'ndan veya statik alınır, geçici olarak BOLGE_ADI'nı tarıyoruz
                            'dagitim' => $rawDagitim,
                            'ilce' => $rawIlce,
                            'ilce_kodu' => $rawIlceKodu,

                            'baglanti_grubu' => $this->findField($payload, ['baglanti_dur', 'baglanti dur']) ?? $baglantiGrubu,
                            'tarife' => $this->findField($payload, ['trf', 'tarife', 'tarife_kodu', 'fn']),
                            'tarife_2' => $this->findField($payload, ['abone_grup_adi', 'abone grup adi', 'abone grubu']),

                            // ── Okuma Tarihleri ────────────────────────────────────────────
                            // Akıllı tarih bulma: sütun adlarına bakarak, yoksa tarih değerlerine bakarak ilk/son okuma'yı belirler
                            'ilk_okuma' => $dateReadings['ilk_okuma'],
                            'son_okuma' => $dateReadings['son_okuma'],

                            // ── Endeks Değerleri ───────────────────────────────────────────
                            't1_ilk_endeks' => $this->parseDecimal($this->findField($payload, ['t1_ilk_endeks', 't1 ilk endeks', 't1 ilk', 'ilk t1', 't1_ilk', 'ilk_t1', 't1_ilk_okuma'])),
                            't1_son_endeks' => $this->parseDecimal($this->findField($payload, ['t1_son_endeks', 't1 son endeks', 't1 son', 'son t1', 't1_son', 'son_t1', 't1_son_okuma'])),
                            't2_ilk_endeks' => $this->parseDecimal($this->findField($payload, ['t2_ilk_endeks', 't2 ilk endeks', 't2 ilk', 'ilk t2', 't2_ilk', 'ilk_t2', 't2_ilk_okuma'])),
                            't2_son_endeks' => $this->parseDecimal($this->findField($payload, ['t2_son_endeks', 't2 son endeks', 't2 son', 'son t2', 't2_son', 'son_t2', 't2_son_okuma'])),
                            't3_ilk_endeks' => $this->parseDecimal($this->findField($payload, ['t3_ilk_endeks', 't3 ilk endeks', 't3 ilk', 'ilk t3', 't3_ilk', 'ilk_t3', 't3_ilk_okuma'])),
                            't3_son_endeks' => $this->parseDecimal($this->findField($payload, ['t3_son_endeks', 't3 son endeks', 't3 son', 'son t3', 't3_son', 'son_t3', 't3_son_okuma'])),

                            // T0 İlk/Son: Önce Excel'den ara, yoksa (T1+T2+T3) toplamı olarak hesapla
                            't0_ilk_endeks' => $this->parseDecimal($this->findField($payload, ['t0_ilk_endeks', 't0 ilk endeks', 't0 ilk', 'ilk t0', 't0_ilk', 'ilk_t0'])) ?? round(
                                $this->parseDecimal($this->findField($payload, ['t1_ilk_endeks'])) +
                                $this->parseDecimal($this->findField($payload, ['t2_ilk_endeks'])) +
                                $this->parseDecimal($this->findField($payload, ['t3_ilk_endeks'])), 2),
                            't0_son_endeks' => $this->parseDecimal($this->findField($payload, ['t0_son_endeks', 't0 son endeks', 't0 son', 'son t0', 't0_son', 'son_t0', 'to_son_endeks'])) ?? round(
                                $this->parseDecimal($this->findField($payload, ['t1_son_endeks'])) +
                                $this->parseDecimal($this->findField($payload, ['t2_son_endeks'])) +
                                $this->parseDecimal($this->findField($payload, ['t3_son_endeks'])), 2),

                            // Ri Değerleri Excel'deki T4 alanına denk gelir
                            'ri_ilk_endeks' => $this->parseDecimal($this->findField($payload, ['t4_ilk_endeks', 'ri_ilk_endeks', 'ri ilk endeks'])),
                            'ri_son_endeks' => $this->parseDecimal($this->findField($payload, ['t4_son_endeks', 'ri_son_endeks', 'ri son endeks'])),
                            'ri_fark_endeks' => $this->parseDecimal($this->findField($payload, ['t4_fark', 'ri_fark_endeks', 'ri fark'])),

                            // Rc Değerleri Excel'deki T5 alanına denk gelir
                            'rc_ilk_endeks' => $this->parseDecimal($this->findField($payload, ['t5_ilk_endeks', 'rc_ilk_endeks', 'rc ilk endeks'])),
                            'rc_son_endeks' => $this->parseDecimal($this->findField($payload, ['t5_son_endeks', 'rc_son_endeks', 'rc son endeks'])),
                            'rc_fark_endeks' => $this->parseDecimal($this->findField($payload, ['t5_fark', 'rc_fark_endeks', 'rc fark'])),

                            // ── Tüketim ───────────────────────────────────────────────────
                            // Tüketimler genelde Fark sütunlarıdır.
                            't1_tuketim' => $this->parseDecimal($this->findField($payload, ['t1_fark', 't1_tuketim', 't1 tüketim', 't1_sarfiyat', 't1_sarf', 't1 sarfiyat'])),
                            't2_tuketim' => $this->parseDecimal($this->findField($payload, ['t2_fark', 't2_tuketim', 't2 tüketim', 't2_sarfiyat', 't2_sarf', 't2 sarfiyat'])),
                            't3_tuketim' => $this->parseDecimal($this->findField($payload, ['t3_fark', 't3_tuketim', 't3 tüketim', 't3_sarfiyat', 't3_sarf', 't3 sarfiyat'])),
                            // Trafo kaybı kwh = (T1_TK_KWH + T2_TK_KWH + T3_TK_KWH)
                            'trafo_kaybi_kwh' => round(
                                $this->parseDecimal($this->findField($payload, ['t1_tk_kwh'])) +
                                $this->parseDecimal($this->findField($payload, ['t2_tk_kwh'])) +
                                $this->parseDecimal($this->findField($payload, ['t3_tk_kwh'])), 2),

                            'ek_tuketim' => $this->parseDecimal($this->findField($payload, ['aktif_miktar', 'aktif_kwh', 'ek tuketim'])),
                            'yillik_tuketim' => $this->parseDecimal($this->findField($payload, ['yillik_tuketim', 'yillik tuketim'])),
                            'fatura_edilecek_toplam_tuketim_kwh' => $this->parseDecimal($this->findField($payload, ['aktif kwh', 'aktif_kwh', 'aktif enerji', 'toplam tüketim', 'tuketim kwh', 'fat_ed_top_tuk'])),
                            'gunluk_ortalama_tuketim' => $this->parseDecimal($this->findField($payload, ['gunluk_ortalama_tuketim', 'gunluk_ort'])),

                            // ── Fiyat & Bedel ─────────────────────────────────────────────
                            // Birim Fiyat: aynı adıyla 3 sütun gelebilir (T1/T2/T3 için)
                            // Tüm payload'u tarayarak sıfırdan büyük ilk değeri al
                            'birim_fiyat' => (function ($payload) {
                                $bestVal = null;
                                foreach ($payload as $k => $v) {
                                    // normalize: 'BİRİM FİYAT', 'birim_fiyat', 'BIRIM_FIYAT_2' vb.
                                    $kNorm = str_replace([' ', 'ı', 'İ', 'ß'], ['_', 'i', 'i', 'ss'], mb_strtolower(trim((string)$k), 'UTF-8'));
                                    if (!preg_match('/^birim_fiyat(_\d+)?$/', $kNorm)) continue;
                                    if (is_array($v)) continue;
                                    $dec = $this->parseDecimal($v);
                                    if ($dec !== null && (float)$dec > 0) {
                                        $bestVal = $dec;
                                        break; // ilk sıfırdan büyüğü al
                                    }
                                }
                                // Eğer payload'dan bulunamadıysa, t1_birim_fiyat, t2_birim_fiyat, t3_birim_fiyat veya t1 birim fiyat, t2 birim fiyat vb. alanlara bak
                                if ($bestVal === null) {
                                    $bestVal = $this->parseDecimal(
                                        $this->findField($payload, ['t1_birim_fiyat', 't1 birim fiyat'])
                                        ?? $this->findField($payload, ['t2_birim_fiyat', 't2 birim fiyat'])
                                        ?? $this->findField($payload, ['t3_birim_fiyat', 't3 birim fiyat'])
                                    );
                                }
                                return $bestVal;
                            })($payload),
                            'dagitim_birim_fiyat' => $this->parseDecimal($this->findField($payload, ['dagitim_birim_fiyat', 'dagitim birim fiyat'])),
                            'aktif_tuketim_tl' => $this->parseDecimal($this->findField($payload, ['akti̇f tüketi̇m', 'aktif tüketim', 'aktif tuketim'])),
                            'dagitim_bedeli' => $this->parseDecimal($this->findField($payload, ['dagitim bedeli', 'dagitim_bedeli'])),
                            'dagitim_bedeli_ek' => $this->parseDecimal($this->findField($payload, ['dagitim_bedeli_ek', 'dagitim bedeli ek'])),
                            'enerji_fonu' => $this->parseDecimal($this->findField($payload, ['ee_fonu', 'enerji_fonu', 'enerji fonu'])),
                            // ── Dinamik Reaktif Tespiti (Tüm kalemleri topla) ────────────────
                            'reaktif_tl' => (function ($payload) {
                                $total = 0;
                                $keywords = ['reaktif', 'bedel', 'endüktif', 'kapasitif', 'ceza', 'inductif', 'capacitif'];
                                foreach ($payload as $key => $val) {
                                    $keyLower = mb_strtolower($key, 'UTF-8');
                                    $keyLower = str_replace(['i̇', 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'i', 'g', 'u', 's', 'o', 'c'], $keyLower);
                                    $match = false;

                                    if (str_contains($keyLower, 'enduktif') || str_contains($keyLower, 'kapasitif')) {
                                        $match = true;
                                    } elseif (str_contains($keyLower, 'reaktif') && (str_contains($keyLower, 'bedel') || str_contains($keyLower, 'tutar') || str_contains($keyLower, 'tuketim'))) {
                                        $match = true;
                                    }

                                    if ($match) {
                                        $decimal = $this->parseDecimal($val);
                                        if ($decimal > 0) {
                                            $total += $decimal;
                                        }
                                    }
                                }

                                return $total > 0 ? $total : 0;
                            })($payload),

                            'acma_kapama_bedeli' => $this->parseDecimal($this->findField($payload, ['acma_kapama_bedeli', 'acma kapama bedeli'])),
                            'gecikme_tutari' => $this->parseDecimal($this->findField($payload, ['devir_gecikme', 'gecikme_tutari', 'gecikme tutari'])),
                            'trt_fonu' => $this->parseDecimal($this->findField($payload, ['trt_payi', 'trt fonu', 'trt_fonu'])),
                            'btv' => $this->parseDecimal($this->findField($payload, ['beledi̇ye vergi̇si̇', 'belediye vergisi', 'btv', 'b.t.v.'])),
                            'btv_orani' => $this->parseDecimal($this->findField($payload, ['btv_orani', 'btv orani'])),

                            // Kullanıcı isteği: TOPLAM TUTAR sütununu baz alalım. Fatura tutarı seçilirse olmaz.
                            'fatura_tutari' => $this->parseDecimal($this->findField($payload, ['fatura_tutar', 'fatura tutari'])),
                            'fatura_tutari_ek' => $this->parseDecimal($this->findField($payload, ['fatura_tutari_ek', 'fatura tutari ek'])),
                            'kdv' => $this->parseDecimal($this->findField($payload, ['k.d.v.', 'kdv', 'k d v'])),
                            'genel_toplam' => $this->parseDecimal($this->findField($payload, ['genel_toplam', 'genel toplam', 'toplam tutar', 'toplam_tutar', 'fatura_edilecek_tutar', 'odenecek_tutar', 'net_tutar', 'fatura_tutar', 'fatura tutari'])),
                            'tutar_toplam' => $this->parseDecimal($this->findField($payload, ['genel_toplam', 'genel toplam', 'GENEL TOPLAM', 'toplam tutar', 'toplam_tutar', 'fatura_edilecek_tutar', 'odenecek_tutar', 'net_tutar', 'fatura_tutar', 'fatura tutari'])),
                        ]
                    );
                    // (Aboneler ve Bölgeler kontrolü yukarıya taşındı)
                }
                $importLog->increment('islenen_satir', $rawRows->count());
            });

        return $stats;
    }

    /**
     * Excel başlıklarını okur (preview için).
     */
    public function readHeaders(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $firstRow = $sheet->rangeToArray('A1:'.$sheet->getHighestColumn().'1', null, true, true, false);

        return array_map('trim', $firstRow[0] ?? []);
    }

    /**
     * İlk N satırı önizleme için döndürür.
     */
    public function readPreview(string $filePath, int $limit = 5): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = array_map('trim', array_shift($rows));
        $preview = [];

        foreach (array_slice($rows, 0, $limit) as $row) {
            $preview[] = array_combine($headers, array_values($row));
        }

        return [
            'headers' => $headers,
            'rows' => $preview,
            'total_rows' => count($rows),
        ];
    }

    /**
     * Excel'in OKUMA (sayaç okuma tarihi) sütunundan dönem tespiti yapar.
     * Sadece ilk 150 satırı belleğe alarak OOM hatalarını önler.
     * İlk 100 satırdan en çok tekrar eden YYYY-MM değerini döndürür (mode).
     */
    public function detectDonem(string $filePath): ?string
    {
        try {
            // Sadece ilk 150 satırı okumak için filtre oluştur (OOM u engeller)
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $filter = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
            {
                public function readCell($columnAddress, $row, $worksheetName = ''): bool
                {
                    return $row <= 150;
                }
            };
            $reader->setReadFilter($filter);

            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if (count($rows) < 2) {
                return null;
            }

            // Header logiği: sadece 0. satırda olmayabilir. İlk 50 satıra bakıp 'okuma' sütununu bul
            $kolonIndex = null;
            $headerRowOffset = 0;

            foreach (array_slice($rows, 0, 50) as $rowIndex => $row) {
                $headers = array_map(function ($h) {
                    $lower = mb_strtolower(trim((string) $h), 'UTF-8');

                    return preg_replace('/[^a-z0-9_]/u', '', $lower);
                }, $row);

                foreach ($headers as $idx => $h) {
                    // Sütun ismi içinde "tahakkuk" veya "tahakuk" kelimesi varsa kabul et
                    if (str_contains($h, 'tahakkuk') || str_contains($h, 'tahakuk')) {
                        $kolonIndex = $idx;
                        $headerRowOffset = $rowIndex;
                        break 2;
                    }
                }
            }

            if ($kolonIndex === null) {
                return null;
            }

            // İlk 100 veri satırından YYYY-MM frekansını say (Başlık satırından sonrasını al)
            $frekans = [];
            $ornekSatirlar = array_slice($rows, $headerRowOffset + 1, 100);

            foreach ($ornekSatirlar as $row) {
                $deger = trim((string) ($row[$kolonIndex] ?? ''));
                if (empty($deger)) {
                    continue;
                }

                // Eğer değer doğrudan YYYYMM formatındaysa (örn: 202611)
                if (preg_match('/^(\d{4})(\d{2})$/', $deger, $matches)) {
                    $yyyyMM = $matches[1].'-'.$matches[2];
                    $frekans[$yyyyMM] = ($frekans[$yyyyMM] ?? 0) + 1;
                } else {
                    // Farklı bir tarih formatıysa parseDate'i devreye al
                    $date = $this->parseDate((string) $deger);
                    if ($date) {
                        $yyyyMM = substr($date, 0, 7); // "2026-02"
                        $frekans[$yyyyMM] = ($frekans[$yyyyMM] ?? 0) + 1;
                    }
                }
            }

            if (empty($frekans)) {
                return null;
            }

            // En çok tekrar eden ayı döndür
            arsort($frekans);

            return array_key_first($frekans);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('detectDonem exception: '.$e->getMessage());
        }

        return null;
    }

    // ── Yardımcı metodlar ─────────────────────────────────────────────────────

    private function insertChunk(array $chunk, array &$stats): void
    {
        try {
            // ignore diyerek mükerrer hash'leri atla
            DB::table('hamveri')->insertOrIgnore($chunk);
            $stats['eklenen'] += count($chunk);
        } catch (\Throwable $e) {
            $stats['hatalar'][] = $e->getMessage();
        }
    }

    private function findField(array $payload, array $keys): ?string
    {
        foreach ($payload as $k => $v) {
            foreach ($keys as $key) {
                if (mb_strtolower(trim($k), 'UTF-8') === mb_strtolower($key, 'UTF-8')) {
                    $val = $v !== null ? trim((string) $v) : null;

                    return ($val !== '') ? $val : null;
                }
            }
        }

        return null;
    }

    private function parseDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        // Excel sayısal tarih
        if (is_numeric($value)) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                // Sanity Check: Gelecek tarihli (2026+) okumalar muhtemelen hatalı sutun eşleşmesidir (Endeks vs)
                if ((int) $dt->format('Y') > 2026) {
                    return null;
                }

                return $dt->format('Y-m-d');
            } catch (\Throwable) {
            }
        }

        // Türkçe tarih formatları
        $formats = ['d.m.Y', 'd/m/Y', 'Y-m-d', 'd-m-Y'];
        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, trim($value));
            if ($dt) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * İlk Okuma ve Son Okuma alanlarını tarih değerlerine bakarak bulur
     * Mantık (%90 durum):
     * 1. "ilk_okuma" ve "son_okuma" başlıklarını ara - bulunursa kullan
     * 2. "ilk_okuma" ve "okuma" başlıklarını ara - bulunursa, "okuma"=son_okuma
     * 3. Yukarıdakiler yoksa "okuma" kelimesi içeren sütunları tara ve tarihe göre ayrıştır
     */
    private function findDateReadingFields(array $payload): array
    {
        // Adım 1: Açık başlık adlarını ara - "ilk_okuma" ve "son_okuma"
        $ilkOkumaValue = $this->findField($payload, ['ilk_okuma', 'ilk okuma']);
        $sonOkumaValue = $this->findField($payload, ['son_okuma', 'son okuma']);

        if ($ilkOkumaValue && $sonOkumaValue) {
            return [
                'ilk_okuma' => $this->parseDate($ilkOkumaValue),
                'son_okuma' => $this->parseDate($sonOkumaValue),
            ];
        }

        // Adım 2: "ilk_okuma" ve "okuma" başlıklarını ara (%90 durum)
        if ($ilkOkumaValue) {
            $okumaValue = $this->findField($payload, ['okuma', 'o k u m a']);
            if ($okumaValue) {
                return [
                    'ilk_okuma' => $this->parseDate($ilkOkumaValue),
                    'son_okuma' => $this->parseDate($okumaValue),
                ];
            }

            // ilk_okuma var ama son_okuma bulunamadı
            return [
                'ilk_okuma' => $this->parseDate($ilkOkumaValue),
                'son_okuma' => null,
            ];
        }

        // Adım 3: Alternative - "okuma" kelimesi içeren sütunları tara (son çare)
        $okumaAlanlari = [];
        foreach ($payload as $key => $value) {
            $keyLower = mb_strtolower($key, 'UTF-8');
            $keyLower = str_replace(['ı', 'İ'], ['i', 'i'], $keyLower); // Normalizasyon

            // "okuma" veya "tarih" kelimesi içeren ve tarih değeri olan sütunları bul
            // ANCAK Endeks, Fark, Tutar, Bedel gibi sayısal başlıkları DIŞLA
            if ((str_contains($keyLower, 'okuma') || str_contains($keyLower, 'tarih')) &&
                ! str_contains($keyLower, 'endeks') &&
                ! str_contains($keyLower, 'fark') &&
                ! str_contains($keyLower, 'tutar') &&
                ! str_contains($keyLower, 'bedel')) {

                $parsedDate = $this->parseDate($value);
                if ($parsedDate) {
                    $okumaAlanlari[$key] = $parsedDate;
                }
            }
        }

        // Adım 3a: İki veya daha fazla okuma alanı bulundu
        if (count($okumaAlanlari) >= 2) {
            asort($okumaAlanlari);
            $dateValues = array_values($okumaAlanlari);

            return [
                'ilk_okuma' => $dateValues[0],           // En eski tarih
                'son_okuma' => end($dateValues),         // En yeni tarih
            ];
        } elseif (count($okumaAlanlari) == 1) {
            // Sadece bir tane okuma alanı varsa
            $dateValue = reset($okumaAlanlari);

            // Eğer başlık "son okuma" içeriyorsa son_okuma, yoksa ilk_okuma
            $keyName = key($okumaAlanlari);
            $keyLower = mb_strtolower($keyName, 'UTF-8');

            if (str_contains($keyLower, 'son') || str_contains($keyLower, 'bitiş')) {
                return [
                    'ilk_okuma' => null,
                    'son_okuma' => $dateValue,
                ];
            } else {
                return [
                    'ilk_okuma' => $dateValue,
                    'son_okuma' => null,
                ];
            }
        }

        // Final Check: İlk Okuma her zaman Son Okuma'dan eski olmalıdır.
        // Eğer veritabanından veya Excel'den ters gelmişse yer değiştiriyoruz.
        $res = [
            'ilk_okuma' => $res['ilk_okuma'] ?? null,
            'son_okuma' => $res['son_okuma'] ?? null,
        ];

        if ($res['ilk_okuma'] && $res['son_okuma']) {
            if (strtotime($res['ilk_okuma']) > strtotime($res['son_okuma'])) {
                $temp = $res['ilk_okuma'];
                $res['ilk_okuma'] = $res['son_okuma'];
                $res['son_okuma'] = $temp;
            }
        }

        return $res;
    }

    private function parseDecimal($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        // Sayı içindeki boşlukları temizle
        $value = str_replace(' ', '', $value);

        // 1. Durum: Hem nokta hem virgül varsa (1.234,56 veya 1,234.56)
        if (str_contains($value, '.') && str_contains($value, ',')) {
            if (strrpos($value, '.') > strrpos($value, ',')) {
                // US: 1,234.56
                $value = str_replace(',', '', $value);
            } else {
                // TR: 1.234,56
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            }
        }
        // 2. Durum: Sadece virgül varsa (15489,26)
        elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }
        // 3. Durum: Sadece nokta varsa (1.500 veya 15489.26)
        elseif (str_contains($value, '.')) {
            // Eğer nokta varsa, bunun binlik mi yoksa ondalık mı olduğunu anlamaya çalışalım.
            // TR formatında nokta genellikle binliktir (1.500). US formatında ondalıktır (1.5).
            // Eğer noktadan sonra 3 hane varsa ve öncesi 1-3 hane ise binlik olma ihtimali yüksektir.
            // ANCAK kullanıcı 3 haneli hassasiyet istiyorsa (örn: 1.234), bunu bozmamalıyız.
            // Staging'e geçerken ham veriyi korumak en iyisi.
            $parts = explode('.', $value);
            if (count($parts) === 2 && strlen($parts[1]) === 3) {
                // Sadece değer çok büyükse binlik kabul edelim, yoksa ondalık kalsın.
                // Veya en güvenlisi: Eğer değerde virgül yoksa ve nokta varsa, ondalık kabul et (modern sistemler)
                // Ama eski Excel çıktılarında 1.500 tam sayı olabilir.
                // Şimdilik noktayı ondalık olarak koruyalım, çünkü user hassasiyet istiyor.
            }
        }

        $clean = preg_replace('/[^0-9.\-]/', '', $value);

        return is_numeric($clean) ? (string) $clean : null;
    }
}
