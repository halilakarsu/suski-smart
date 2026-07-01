#!/usr/bin/env python3
"""
kuyular.xlsx → MySQL kuyular tablosu import script
"""

import openpyxl
import pymysql
from datetime import datetime

# ── Bağlantı ayarları ─────────────────────────────────────────────────────────
DB_HOST     = '127.0.0.1'
DB_PORT     = 3306
DB_USER     = 'sail'
DB_PASSWORD = 'password'
DB_NAME     = 'laravel'
EXCEL_FILE  = '/Users/akarsu/Desktop/kuyular.xlsx'

# ── Yardımcı fonksiyonlar ─────────────────────────────────────────────────────
def parse_dt(val):
    """'2026-04-27 07:44' → datetime"""
    if not val:
        return None
    if isinstance(val, datetime):
        return val
    s = str(val).strip()
    for fmt in ('%Y-%m-%d %H:%M', '%Y-%m-%d %H:%M:%S', '%d.%m.%Y %H:%M'):
        try:
            return datetime.strptime(s, fmt)
        except ValueError:
            pass
    return None

def clean_str(val, max_len=None):
    if val is None:
        return None
    s = str(val).strip()
    if s in ('-', '0', ''):
        return None
    if max_len:
        s = s[:max_len]
    return s

def clean_decimal(val):
    if val is None:
        return None
    try:
        return float(val)
    except (TypeError, ValueError):
        return None

def normalize_durum(val):
    if not val:
        return 'aktif'
    s = str(val).strip().lower()
    if 'pasif' in s:
        return 'pasif'
    return 'aktif'

# ── Excel oku ─────────────────────────────────────────────────────────────────
print(f"Excel dosyası okunuyor: {EXCEL_FILE}")
wb = openpyxl.load_workbook(EXCEL_FILE)
ws = wb.active

rows = list(ws.iter_rows(min_row=2, values_only=True))
print(f"Toplam satır: {len(rows)}")

# ── DB bağlantısı ─────────────────────────────────────────────────────────────
conn = pymysql.connect(
    host=DB_HOST, port=DB_PORT,
    user=DB_USER, password=DB_PASSWORD,
    database=DB_NAME, charset='utf8mb4',
)
cursor = conn.cursor()

# Mevcut kayıtları temizle
cursor.execute("TRUNCATE TABLE kuyular")
conn.commit()
print("Mevcut kayıtlar temizlendi.")

# ── İnsert ───────────────────────────────────────────────────────────────────
sql = """
INSERT INTO kuyular (
    kuyu_no, ilce, adres,
    demontaj_derinligi, montaj_derinligi,
    depo_bilgisi, boru_tipi, kablo, motor, pompa, debi,
    aciklama, durum,
    olusturulma_tarihi, guncellenme_tarihi,
    created_at, updated_at
) VALUES (
    %s, %s, %s,
    %s, %s,
    %s, %s, %s, %s, %s, %s,
    %s, %s,
    %s, %s,
    NOW(), NOW()
)
"""

batch = []
BATCH_SIZE = 500
inserted = 0
skipped  = 0

now = datetime.now()

for i, row in enumerate(rows):
    # Satır boşsa atla
    if all(v is None for v in row):
        skipped += 1
        continue

    # Sütunlar:
    # 0:Kuyu No  1:İlçe  2:Adres  3:Demontaj  4:Montaj
    # 5:Depo     6:Boru  7:Kablo  8:Motor     9:Pompa
    # 10:Debi   11:Açıklama  12:Durum  13:Oluşturulma  14:Güncelleme

    kuyu_no             = clean_str(row[0], 50)
    ilce                = clean_str(row[1], 100)
    adres               = clean_str(row[2], 500)
    demontaj_derinligi  = clean_decimal(row[3])
    montaj_derinligi    = clean_decimal(row[4])
    depo_bilgisi        = clean_str(row[5], 300)
    boru_tipi           = clean_str(row[6], 200)
    kablo               = clean_str(row[7], 200)
    motor               = clean_str(row[8], 300)
    pompa               = clean_str(row[9], 300)
    debi                = clean_str(row[10], 100)
    aciklama            = clean_str(row[11])
    durum               = normalize_durum(row[12])
    olusturulma_tarihi  = parse_dt(row[13])
    guncellenme_tarihi  = parse_dt(row[14])

    batch.append((
        kuyu_no, ilce, adres,
        demontaj_derinligi, montaj_derinligi,
        depo_bilgisi, boru_tipi, kablo, motor, pompa, debi,
        aciklama, durum,
        olusturulma_tarihi, guncellenme_tarihi,
    ))

    if len(batch) >= BATCH_SIZE:
        cursor.executemany(sql, batch)
        conn.commit()
        inserted += len(batch)
        print(f"  {inserted} kayıt eklendi...")
        batch = []

if batch:
    cursor.executemany(sql, batch)
    conn.commit()
    inserted += len(batch)

cursor.close()
conn.close()

print()
print(f"✅ Import tamamlandı!")
print(f"   Eklenen kayıt : {inserted}")
print(f"   Atlanan satır : {skipped}")
