<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportLog;
use App\Models\Hamveri;
use App\Models\BeklemeKontrolHavuzu;
use App\Services\ExcelImportService;
use App\Models\Aboneler;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

echo "--- MEVCUT VERİTABANINI TEMİZLİYORUZ (TEST İÇİN) ---\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
BeklemeKontrolHavuzu::truncate();
Hamveri::truncate();
ImportLog::truncate();
Aboneler::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$service = new ExcelImportService();

echo "--- 1. NORMAL DOSYA TESTİ (202602.xlsx) ---\n";
$normalPath = 'public/ornek faturalar/202602.xlsx';

// Geçici bir "orijinal format" oluşturmak için ImportLog (ilk başarılı log)
// Excel formatını algılaması için ilk logu oluşturuyoruz
$headers = $service->readHeaders($normalPath);
$baseLog = ImportLog::create([
    'dosya_adi'        => 'FirstBase.xlsx',
    'yol'              => $normalPath,
    'disk'             => 'local', // Gerçekte disk path istiyor ama doğrudan path kullanacağız
    'sutun_eslestirme' => $headers,
    'yukleyen_kullanici_id' => 1,
    'durum'            => 'tamamlandi'
]);

$log1 = ImportLog::create([
    'dosya_adi'        => '202602.xlsx',
    'yol'              => 'ornek faturalar/202602.xlsx',
    'disk'             => 'public',
    'yukleyen_kullanici_id' => 1,
    'durum'            => 'bekliyor'
]);

// ExcelImportService Storage::disk()->path() kullanıyor, $log1->yol'u ayarlayalım
// Local disk, app/public için. 'disk' => 'public_dir' diyelim ki Storage::disk('public_dir') çalışsın?
// Yok, script içinde doğrudan passlayamıyoruz çünkü ExcelImportService importToRaw() içinde Storage facade kullanıyor.
// Çözüm: Dosyayı storage/app/ içine kopyalayalım ki hata vermesin.
$storagePath = storage_path('app/test_202602.xlsx');
copy($normalPath, $storagePath);
$log1->update(['disk' => 'local', 'yol' => 'test_202602.xlsx']);

try {
    $stats1 = $service->importToRaw($log1);
    echo "Hamveriye Eklendi: " . $stats1['eklenen'] . " satır\n";
    $statsStg1 = $service->promoteToStaging($log1);
    echo "Staging Sonucu:\n";
    print_r($statsStg1);
} catch (\Exception $e) {
    echo "HATA (Normal Test): " . $e->getMessage() . "\n";
}

echo "\n--- 2. BOZULMUŞ (SÜTUN SİLİNMİŞ) DOSYA TESTİ ---\n";
// Orijinal excel'i aç
$spreadsheet = IOFactory::load($normalPath);
$sheet = $spreadsheet->getActiveSheet();
// Sütun sil (örneğin 'GUC_BEDELI' olan D veya E vs.)
$sheet->removeColumn('D');
$sheet->removeColumn('E'); 
// 1. satırı (Fatura id vb) boz
$sheet->setCellValue('A2', 'Bozuk_Sutun_1');

$bozukPath = storage_path('app/bozuk_test.xlsx');
$res = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$res->save($bozukPath);

$log2 = ImportLog::create([
    'dosya_adi' => 'bozuk_test.xlsx',
    'yol' => 'bozuk_test.xlsx',
    'disk' => 'local',
    'yukleyen_kullanici_id' => 1,
    'durum' => 'bekliyor'
]);

try {
    $stats2 = $service->importToRaw($log2);
    echo "Hamveriye Eklendi: " . $stats2['eklenen'] . " satır\n";
} catch (\Exception $e) {
    echo "YAKALANAN HATA (Bozuk Sütun Testi): " . $e->getMessage() . "\n";
}

echo "\n--- 3. VERİLERİ MANİPÜLE EDİLMİŞ DOSYA TESTİ (ANOMALİ SİMÜLASYONU) ---\n";
// Normal dosya üzerinden kopya oluştur
$spread2 = IOFactory::load($normalPath);
$sheet2 = $spread2->getActiveSheet();

// T1 İlk = T1_ILK_ENDEKS (kolon bulmak gerek), veya kolon adını bilmesek bile rastgele hücre değiştirirsek 
// en iyisi indekslere bakalım. "inspect_excel.php"'deki çıkışa göre:
// Tesisat D sütunundaymış, AKTIF KWH AC sütununda, REAKTİF TÜKETİM ...
// Bir şekilde payload da hile yapalım. Sadece Tesisat no'ları bilinenleri güncelleyelim.
// Anomali için:
// - Satır 4: Negatif Tüketim (T1 İlk = 5000, T1 Son = 2000) (Excelde T1_ILK_ENDEKS)
// - Satır 5: Reaktif Ceza (REAKTİF TÜKETİM = 500)
// - Satır 6: Ani Sıfır Tüketim (AKTIF KWH = 0, önceki ay yüksekti)
// - Satır 7: Çarpan Değişimi (CARPAN = 99)

// Satırları manipüle edecek kadar kolon yapılarını bilmiyoruz dersen DB'den okuyup Hamveri'ye sahte eklemek daha güvenli.
echo "Veritabanına manuel anomali enjekte edilip promoteToStaging çağrılıyor...\n";

// Sadece normal excel'den gelen bir kaç staging kaydının "hamveri" kayıtlarına manipülasyon yapalım ve tekrar triggerlayalım
// 4 adet fatura seç
$ornekler = Hamveri::where('import_log_id', $log1->id)->take(4)->get();
$yeniImportLog = ImportLog::create(['dosya_adi' => 'hata_simulasyonu', 'yol'=>'simulasyon', 'disk'=>'local', 'yukleyen_kullanici_id'=>1]);

if($ornekler->count() == 4) {
    $satir1 = json_decode($ornekler[0]->payload, true);
    $satir1['T1_ILK_ENDEKS'] = 5000;
    $satir1['T1_SON_ENDEKS'] = 2000; // Negatif Tüketim

    $satir2 = json_decode($ornekler[1]->payload, true);
    $satir2['REAKTİF TÜKETİM'] = 999.50; // Reaktif ceza (Bilinçli yüksek değer)
    $satir2['REAKTIF_MIKTAR'] = 999.50;  // Excelde iki türlü isim varsa diye 

    $satir3 = json_decode($ornekler[2]->payload, true);
    // Sıfır Tüketim: Mevcut faturayı sıfırlıyoruz.
    // Bekleme kontrol havuzunda bu tesisatın önceki faturası 50'den büyük tüketim olmalı 
    BeklemeKontrolHavuzu::where('fatura_no', $ornekler[2]->fatura_no)->update(['fatura_edilecek_toplam_tuketim_kwh' => 200]); // Önceki faturası var varsayımı için DB'de mevcut olanı yüksek tuttuk.
    $satir3['AKTIF KWH'] = 0; 
    $satir3['AKTİF TÜKETİM'] = 0;

    $satir4 = json_decode($ornekler[3]->payload, true);
    // Çarpan Değişimi
    BeklemeKontrolHavuzu::where('fatura_no', $ornekler[3]->fatura_no)->update(['carpan' => 10]); // Önceki faturada çarpan 10 olsun
    $satir4['CARPAN'] = 99; // Yeni faturada çarpan 99 gelsin
    $satir4['ÇARPAN'] = 99;

    // Yeni hamveriler olarak ekle ki existing = false olsun! Fatura no'ları "YENI-" ekleyerek değiştirelim.
    $fno1 = "YENI-" . $ornekler[0]->fatura_no;
    $fno2 = "YENI-" . $ornekler[1]->fatura_no;
    // Sıfır tüketim ve Çarpan kontrolünde Tesisat No aynı olmalı ama fatura no farklı olmalı!
    $fno3 = "YENI-" . $ornekler[2]->fatura_no;
    $fno4 = "YENI-" . $ornekler[3]->fatura_no;

    $satir1['FATURA NO'] = $fno1;
    $satir2['FATURA NO'] = $fno2;
    $satir3['FATURA NO'] = $fno3;
    $satir4['FATURA NO'] = $fno4;

    Hamveri::create(['import_log_id' => $yeniImportLog->id, 'fatura_no' => $fno1, 'row_hash' => md5(json_encode($satir1)), 'payload' => json_encode($satir1)]);
    Hamveri::create(['import_log_id' => $yeniImportLog->id, 'fatura_no' => $fno2, 'row_hash' => md5(json_encode($satir2)), 'payload' => json_encode($satir2)]);
    Hamveri::create(['import_log_id' => $yeniImportLog->id, 'fatura_no' => $fno3, 'row_hash' => md5(json_encode($satir3)), 'payload' => json_encode($satir3)]);
    Hamveri::create(['import_log_id' => $yeniImportLog->id, 'fatura_no' => $fno4, 'row_hash' => md5(json_encode($satir4)), 'payload' => json_encode($satir4)]);
    
    // Şimdi Promote to Staging yapalım
    $stats3 = $service->promoteToStaging($yeniImportLog);
    echo "Simüle Edilen Anomali Sayacı (Yeni Faturalar): " . $stats3['yeni'] . "\n";
    
    echo "\n--- ANOMALİ TESPİT SONUÇLARI ---\n";
    $sonuclar = BeklemeKontrolHavuzu::whereIn('fatura_no', [$fno1, $fno2, $fno3, $fno4])->get();
    foreach($sonuclar as $sonuc) {
        echo "Fatura: " . $sonuc->fatura_no . " | Tesisat: " . $sonuc->tesisat_no . "\n";
        if(empty($sonuc->payload['_anomaliler'])) {
            echo "  -> Bulunan Hata Yok\n";
        } else {
            foreach($sonuc->payload['_anomaliler'] as $hata) {
                echo "  -> HATA YAKALANDI! Kod: " . $hata['kod'] . "\n";
                echo "     Mesaj: " . $hata['mesaj'] . "\n";
            }
        }
        echo "---------------------------------\n";
    }

} else {
    echo "Yeterince örnek bulunamadı.\n";
}

echo "TEST YAZILIMI TAMAMLANDI.\n";
