# Excel Aboneler Senkronizasyon Sistemi - Kurulum ve Kullanım Kılavuzu

## 📋 Genel Bakış

Masaüstünde yer alan `aboneler.xls` dosyasından veritabanını senkronize etmek için bir sistem kurulmuştur. Bu sistem:

✅ Eksik aboneleri otomatik ekler
✅ Sayaç numarası değişimlerini geçmişle saklar
✅ İlçe ve adres bilgilerini günceller
✅ Güncel sayaçları en üstte gösterir

---

## 🚀 Hızlı Başlangıç

### Adım 1: Docker'ı Başlat
```bash
cd /Users/akarsu/Desktop/suski
./vendor/bin/sail up -d
```

### Adım 2: Database Migration'ını Çalıştır
```bash
./vendor/bin/sail artisan migrate
```

### Adım 3: Senkronizasyon Komutunu Çalıştır

**Önce Test Modunda (değişiklik yapmaz):**
```bash
./vendor/bin/sail artisan app:sync-aboneler-from-excel --dry-run
```

**Sonra Gerçek Senkronizasyon:**
```bash
./vendor/bin/sail artisan app:sync-aboneler-from-excel
```

---

## 📁 Yapılan Dosya Değişiklikleri

### 1. **Database Migration**
📄 Dosya: `database/migrations/2026_04_16_083358_add_sayac_gecmisi_to_aboneler_table.php`

Eklenen alanlar:
- `sayac_gecmisi` (JSON) - Eski sayaçların geçmişi
- `sayac_guncelleme_tarihi` (TIMESTAMP) - Son güncelleme tarihi

```sql
-- Otomatik oluşturulacak
ALTER TABLE aboneler ADD COLUMN sayac_gecmisi JSON;
ALTER TABLE aboneler ADD COLUMN sayac_guncelleme_tarihi TIMESTAMP NULL;
```

### 2. **Aboneler Model Güncellemesi**
📄 Dosya: `app/Models/Aboneler.php`

Eklenen method'lar:

#### `updateSayacWithHistory($newSayacNo, $tarih = null)`
Sayaç numarasını günceller ve eski sayacı geçmişe ekler.

```php
$abone->updateSayacWithHistory('78510969', now());
// Eski sayaç otomatik geçmişe kaydedilir
// JSON yapısı: [ { sayac, guncelleme_tarihi, updated_from }, ... ]
```

#### `getSayacGecmisiFormatted()`
Sayaç geçmişini formatted array olarak döndürür (güncel en üstte).

```php
$gecmis = $abone->getSayacGecmisiFormatted();
// Döner: [
//   { sayac: '78510969', tip: 'Güncel', durum: 'aktif', ... },
//   { sayac: '61010358', tip: 'Geçmiş', durum: 'pasif', ... },
// ]
```

### 3. **Artisan Command Oluşturuldu**
📄 Dosya: `app/Console/Commands/SyncAbonelerFromExcel.php`

Komut: `php artisan app:sync-aboneler-from-excel`

Seçenekler:
- `--file=/path/to/file` - Kustom Excel dosyası yolu (varsayılan: `/Users/akarsu/Desktop/aboneler.xls`)
- `--dry-run` - Değişiklikleri göster ama kaydetme

### 4. **Abone Detay Sayfası Güncellemesi**
📄 Dosya: `resources/views/aboneler/show.blade.php`

Güncelleme:
- Eski `$farkliSayaclar` yapısı kaldırıldı
- Yeni `getSayacGecmisiFormatted()` method'u kullanılıyor
- Sayaç geçmişi güncel en üstte gösterilir
- "AKTİF" ve "GEÇMİŞ" badge'leri dinamik olarak ekleniyor

---

## 📊 Excel Dosya Yapısı

**Dosya:** `/Users/akarsu/Desktop/aboneler.xls`

| Satır | İçerik |
|-------|--------|
| 1 | Boş |
| 2 | Başlık: "ELEKTRİK ABONELERİ LİSTESİ" |
| 3 | Header satırı |
| 4+ | Veri satırları (3065 abone) |

**Kullanılan Kolonlar:**

| Kolon | Index | İçerik |
|-------|-------|---------|
| A | 1 | (Boş) |
| B | 2 | SIRA |
| C | 3 | İLÇESİ |
| D | 4 | ABONE TESIS NO |
| E | 5 | PMUM |
| F | 6 | SAYAC NO |
| G | 7 | SÖZLEŞME GÜCÜ |
| H | 8 | ADRES |

---

## 🔄 Sayaç Geçmiş Yapısı

### Database'de Depolanma

JSON formatında saklanır:

```json
{
  "sayac_gecmisi": [
    {
      "sayac": "63453632",
      "guncelleme_tarihi": "2026-04-16 10:30:45",
      "updated_from": "manual"
    },
    {
      "sayac": "61010358",
      "guncelleme_tarihi": "2026-04-10 14:22:00",
      "updated_from": "manual"
    }
  ],
  "sayac_guncelleme_tarihi": "2026-04-16 10:30:45"
}
```

### Abone Detay Sayfasında Gösterim

```
┌─────────────────────────────────────────────┐
│ ✓ 78510969                                  │ AKTİF
│ 📅 2026-04-16 10:30                         │
├─────────────────────────────────────────────┤
│ ⌛ 63453632                                  │ GEÇMİŞ
│ 📅 2026-04-10 14:22                         │
└─────────────────────────────────────────────┘
```

---

## 📝 Senkronizasyon Mantığı

### Algorit

```
Excel dosyasındaki her abone için:
  
  1. Veritabanında varsa:
     ✓ Sayaç numarası kontrol et
       - Farklıysa: updateSayacWithHistory() çağır
       - Eski sayacı sayac_gecmisi'ne ekle
       - Güncel tarihi kaydet
     
     ✓ İlçe bilgisi kontrol et
       - Farklıysa: BOLGE_ADI güncelle
     
     ✓ Adres bilgisi kontrol et
       - Farklıysa: ADRES güncelle
  
  2. Veritabanında yoksa:
     ✓ Yeni abone oluştur
     ✓ Tüm bilgileri ekle
     ✓ is_active = true olarak ayarla
```

---

## 🎯 Örnek Çıktı

```
Excel dosyası okunuyor...
Excel yapısı tespit edildi:
  - Abone Tesis No: Kolon 4
  - Sayaç No: Kolon 6
  - İlçe: Kolon 3
  - Adres: Kolon 8
Toplam 3065 abone Excel dosyasında bulundu

=== SENKRONİZASYON BAŞLANIYOR ===

✓ EKLENDI: 5492623 (Sayaç: 78510969)
✓ EKLENDI: 5999375 (Sayaç: 63453632)
⚠ GÜNCELLENDİ: 5999428
    - Sayaç: 61010358 → 61018460
    - Adres güncellendi
✓ EKLENDI: 5999492 (Sayaç: 61018460)

=== ÖZET ===
Yeni Aboneler Eklendi: 1200
Güncellenen Aboneler: 45
  - Sayaç Değişimi: 23
  - Adres Güncelleme: 22

Veritabanı Toplam: 3065
Excel Toplam: 3065

✓ Senkronizasyon tamamlandı!
```

---

## 🛠️ Troubleshooting

### ❌ "Dosya bulunamadı" hatası

**Çözüm:** Excel dosyası yolunu kontrol edin
```bash
ls -la /Users/akarsu/Desktop/aboneler.xls
```

### ❌ "SQLSTATE [HY000]" - Database bağlantı hatası

**Çözüm:** Docker'ı başlatın
```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

### ❌ "Deprecated: Increment on non-numeric string"

Bu warning'ler PhpSpreadsheet kütüphanesinden kaynaklanır ve önemli değildir. Command'ı bu şekilde çalıştırarak gizleyebilirsiniz:
```bash
php artisan app:sync-aboneler-from-excel 2>&1 | grep -v "Deprecated"
```

---

## 📅 Scheduled Execution (İsteğe bağlı)

Senkronizasyon'u haftalık otomatik çalıştırmak için `app/Console/Kernel.php`'e ekleyebilirsiniz:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('app:sync-aboneler-from-excel')
             ->weekly()
             ->mondays()
             ->at('02:00')
             ->emailOutputTo('admin@example.com');
}
```

---

## ✅ Kontrol Listesi

- [x] Migration oluşturuldu
- [x] Model method'ları eklendi
- [x] Artisan command yazıldı
- [x] Abone detay sayfası güncellendi
- [ ] Docker'da test et
- [ ] Production deployment

---

**Son Güncelleme:** 16 Nisan 2026
**Sistem Sürümü:** v1.0
**Durum:** Hazır (Test Bekleniyor)
