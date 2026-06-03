#!/bin/bash
# ================================================================
# SUSKI - Kapsamlı Sistem Entegrasyon Testi
# ================================================================

BASE="http://localhost:8080"
COOKIE_FILE="/tmp/suski_test_cookies.txt"
EXCEL_DIR="/Users/akarsu/Desktop/suski/public/ornek faturalar"
LOG_FILE="/tmp/suski_test_results.log"
PASS=0; FAIL=0; WARN=0

# Renkler
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; NC='\033[0m'; BOLD='\033[1m'

echo "" > "$LOG_FILE"

log() { echo -e "$1" | tee -a "$LOG_FILE"; }
pass() { log "${GREEN}  ✅ PASS${NC}: $1"; ((PASS++)); }
fail() { log "${RED}  ❌ FAIL${NC}: $1"; ((FAIL++)); }
warn() { log "${YELLOW}  ⚠️  WARN${NC}: $1"; ((WARN++)); }
section() { log "\n${BOLD}${BLUE}══════════════════════════════════════════════${NC}"; log "${BOLD}${CYAN}  $1${NC}"; log "${BOLD}${BLUE}══════════════════════════════════════════════${NC}"; }

rm -f "$COOKIE_FILE"

# ================================================================
section "ADIM 1 — Uygulama Erişilebilirlik Kontrolü"
# ================================================================
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -L "$BASE/login")
if [ "$HTTP_CODE" = "200" ]; then
  pass "Login sayfası erişilebilir (HTTP $HTTP_CODE)"
else
  fail "Login sayfası erişilemiyor (HTTP $HTTP_CODE)"
fi

# ================================================================
section "ADIM 2 — Login (sea216327@gmail.com / 987654321)"
# ================================================================
# Önce CSRF token al
CSRF=$(curl -s -c "$COOKIE_FILE" "$BASE/login" | grep -o 'name="_token" value="[^"]*"' | grep -o 'value="[^"]*"' | cut -d'"' -f2)
log "  → CSRF Token: ${CSRF:0:20}..."

LOGIN_RESP=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" \
  -X POST "$BASE/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "_token=$CSRF&email=sea216327@gmail.com&password=987654321" \
  -o /dev/null -w "%{http_code}" -L)

DASHBOARD=$(curl -s -b "$COOKIE_FILE" "$BASE/dashboard" -o /dev/null -w "%{http_code}")
if [ "$DASHBOARD" = "200" ]; then
  pass "Login başarılı — Dashboard erişilebilir"
else
  fail "Login başarısız! Dashboard HTTP: $DASHBOARD"
  log "${RED}  → Testi sürdürmek mümkün değil. Çıkılıyor.${NC}"
  exit 1
fi

# ================================================================
section "ADIM 3 — DB Durumu (Başlangıç)"
# ================================================================
cd /Users/akarsu/Desktop/suski

ABONE_INIT=$(php artisan tinker --execute="echo App\Models\Aboneler::count();" 2>/dev/null | tail -1)
HAVUZ_INIT=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::count();" 2>/dev/null | tail -1)
IMPORT_INIT=$(php artisan tinker --execute="echo App\Models\ImportLog::count();" 2>/dev/null | tail -1)
HAMVERI_INIT=$(php artisan tinker --execute="echo App\Models\Hamveri::count();" 2>/dev/null | tail -1)

log "  → Başlangıç Aboneler: $ABONE_INIT"
log "  → Başlangıç Havuz:    $HAVUZ_INIT"
log "  → Başlangıç ImportLog: $IMPORT_INIT"
log "  → Başlangıç Hamveri:  $HAMVERI_INIT"

# ================================================================
section "ADIM 4 — Excel Import Testleri (4 Dosya)"
# ================================================================

import_file() {
  local FILE="$1"
  local NAME=$(basename "$FILE")
  log "\n  ${BOLD}→ Yükleniyor: $NAME${NC}"
  
  # Yeni CSRF al (her upload öncesi)
  CSRF2=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" "$BASE/import" | grep -o 'name="_token" value="[^"]*"' | grep -o 'value="[^"]*"' | cut -d'"' -f2)
  
  RESP=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
    -X POST "$BASE/import/ajax-upload" \
    -H "X-CSRF-TOKEN: $CSRF2" \
    -H "Accept: application/json" \
    -F "dosya=@$FILE" \
    --max-time 120 \
    -w "\n---HTTP:%{http_code}")
  
  HTTP=$(echo "$RESP" | grep -o 'HTTP:[0-9]*' | cut -d: -f2)
  JSON=$(echo "$RESP" | grep -v 'HTTP:')
  
  SUCCESS=$(echo "$JSON" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('success','?'))" 2>/dev/null)
  MESSAGE=$(echo "$JSON" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('message',''))" 2>/dev/null)
  DONEM=$(echo "$JSON"   | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('donem','?'))" 2>/dev/null)
  YENI=$(echo "$JSON"    | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('yeni',0))" 2>/dev/null)
  MUKERRER=$(echo "$JSON"| python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('mukerrer',0))" 2>/dev/null)
  DEGISTI=$(echo "$JSON" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('degisti',0))" 2>/dev/null)
  YENI_AB=$(echo "$JSON" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('yeni_abone',0))" 2>/dev/null)
  GUNC_AB=$(echo "$JSON" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('guncellenen_abone',0))" 2>/dev/null)
  TOPLAM=$(echo "$JSON"  | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('toplam_kayit',0))" 2>/dev/null)

  if [ "$SUCCESS" = "True" ] || [ "$SUCCESS" = "true" ]; then
    pass "$NAME → HTTP $HTTP | Dönem: $DONEM | Toplam: $TOPLAM | Yeni: $YENI | Mükerrer: $MUKERRER | Değişti: $DEGISTI | Yeni Abone: $YENI_AB | Günc. Abone: $GUNC_AB"
  else
    fail "$NAME → HTTP $HTTP | Mesaj: $MESSAGE"
    log "  → Ham JSON: ${JSON:0:300}"
  fi
}

import_file "$EXCEL_DIR/202511.xlsx"
import_file "$EXCEL_DIR/202512.xlsx"
import_file "$EXCEL_DIR/202601.xlsx"
import_file "$EXCEL_DIR/202602.xlsx"

# ================================================================
section "ADIM 5 — Mükerrer Dosya Testi (202511 tekrar yükle)"
# ================================================================
CSRF3=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" "$BASE/import" | grep -o 'name="_token" value="[^"]*"' | grep -o 'value="[^"]*"' | cut -d'"' -f2)
DUP_RESP=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
  -X POST "$BASE/import/ajax-upload" \
  -H "X-CSRF-TOKEN: $CSRF3" \
  -H "Accept: application/json" \
  -F "dosya=@$EXCEL_DIR/202511.xlsx" \
  --max-time 60 \
  -w "\n---HTTP:%{http_code}")

DUP_CODE=$(echo "$DUP_RESP" | grep -o 'HTTP:[0-9]*' | cut -d: -f2)
DUP_JSON=$(echo "$DUP_RESP" | grep -v 'HTTP:')
DUP_SUCCESS=$(echo "$DUP_JSON" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('success','?'))" 2>/dev/null)
DUP_MSG=$(echo "$DUP_JSON" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('message',''))" 2>/dev/null)

if [ "$DUP_CODE" = "422" ] && [ "$DUP_SUCCESS" = "False" ]; then
  pass "Mükerrer dosya engellendi! HTTP 422 | Mesaj: $DUP_MSG"
else
  fail "Mükerrer dosya engellenmedi! HTTP: $DUP_CODE | Success: $DUP_SUCCESS | Msg: $DUP_MSG"
fi

# ================================================================
section "ADIM 6 — DB Sayımları (Import Sonrası)"
# ================================================================
ABONE_AFTER=$(php artisan tinker --execute="echo App\Models\Aboneler::count();" 2>/dev/null | tail -1)
HAVUZ_AFTER=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::count();" 2>/dev/null | tail -1)
IMPORT_AFTER=$(php artisan tinker --execute="echo App\Models\ImportLog::count();" 2>/dev/null | tail -1)
HAMVERI_AFTER=$(php artisan tinker --execute="echo App\Models\Hamveri::count();" 2>/dev/null | tail -1)
IS_NEW=$(php artisan tinker --execute="echo App\Models\Aboneler::where('is_new',true)->count();" 2>/dev/null | tail -1)
IS_UPDATED=$(php artisan tinker --execute="echo App\Models\Aboneler::where('is_updated',true)->count();" 2>/dev/null | tail -1)
ANOMALI=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::whereJsonLength('payload->_anomaliler','>',0)->count();" 2>/dev/null | tail -1)

log "  → Aboneler:   $ABONE_INIT → $ABONE_AFTER (+$(($ABONE_AFTER - $ABONE_INIT)))"
log "  → Havuz:      $HAVUZ_INIT → $HAVUZ_AFTER (+$(($HAVUZ_AFTER - $HAVUZ_INIT)))"
log "  → ImportLog:  $IMPORT_INIT → $IMPORT_AFTER (+$(($IMPORT_AFTER - $IMPORT_INIT)))"
log "  → Hamveri:    $HAMVERI_INIT → $HAMVERI_AFTER (+$(($HAMVERI_AFTER - $HAMVERI_INIT)))"
log "  → is_new=true:     $IS_NEW"
log "  → is_updated=true: $IS_UPDATED"
log "  → Anomalili fatura: $ANOMALI"

[ "$IMPORT_AFTER" -gt "$IMPORT_INIT" ] && pass "ImportLog kayıtları oluştu" || fail "ImportLog kayıt yok!"
[ "$HAVUZ_AFTER" -gt "$HAVUZ_INIT" ]  && pass "BeklemeKontrolHavuzu kayıtları oluştu" || fail "Havuzda kayıt yok!"
[ "$ABONE_AFTER" -gt "$ABONE_INIT" ]  && pass "Yeni aboneler oluştu" || warn "Hiç yeni abone eklenmedi"
[ "$IS_NEW" -gt 0 ]     && pass "is_new=true olan aboneler var ($IS_NEW adet)" || warn "is_new bayrağı taşıyan abone yok"
[ "$ANOMALI" -gt 0 ]    && pass "Anomali tespiti çalışıyor ($ANOMALI kayıt)" || warn "Anomali tespit edilmedi"

# ================================================================
section "ADIM 7 — ImportLog Durumları"
# ================================================================
php artisan tinker --execute="
App\Models\ImportLog::latest()->get()->each(function(\$l) {
  echo \$l->orijinal_adi . ' | ' . \$l->donem . ' | ' . \$l->durum . ' | Satır: ' . \$l->toplam_satir . '\n';
});" 2>/dev/null | grep -v ">>>" | grep -v "^$" | while IFS= read -r line; do
  if echo "$line" | grep -q "tamamlandi"; then
    echo -e "    ${GREEN}✅${NC} $line"
  elif echo "$line" | grep -q "hata"; then
    echo -e "    ${RED}❌${NC} $line"
  else
    echo -e "    ${YELLOW}⚠️${NC} $line"
  fi
done

# ================================================================
section "ADIM 8 — Staging API Endpoint Kontrolleri"
# ================================================================
check_staging_tab() {
  local TAB="$1"
  local RESP=$(curl -s -b "$COOKIE_FILE" "$BASE/import/staging?tab=$TAB" -o /dev/null -w "%{http_code}")
  if [ "$RESP" = "200" ]; then
    pass "Staging tab '$TAB' erişilebilir (HTTP 200)"
  else
    fail "Staging tab '$TAB' hatalı (HTTP $RESP)"
  fi
}
check_staging_tab "bekleyen"
check_staging_tab "onayli"
check_staging_tab "mukerrer"
check_staging_tab "itiraz"
check_staging_tab "yeni_abone"
check_staging_tab "guncellenen_abone"
check_staging_tab "anomalili"

# ================================================================
section "ADIM 9 — Aboneler Sayfası Erişim"
# ================================================================
ABONE_PAGE=$(curl -s -b "$COOKIE_FILE" "$BASE/aboneler" -o /dev/null -w "%{http_code}")
[ "$ABONE_PAGE" = "200" ] && pass "Aboneler sayfası HTTP 200" || fail "Aboneler sayfası HTTP $ABONE_PAGE"

ABONE_NEW_PAGE=$(curl -s -b "$COOKIE_FILE" "$BASE/aboneler?tab=new" -o /dev/null -w "%{http_code}")
[ "$ABONE_NEW_PAGE" = "200" ] && pass "Aboneler tab=new HTTP 200" || fail "Aboneler tab=new HTTP $ABONE_NEW_PAGE"

ABONE_UPD_PAGE=$(curl -s -b "$COOKIE_FILE" "$BASE/aboneler?tab=updated" -o /dev/null -w "%{http_code}")
[ "$ABONE_UPD_PAGE" = "200" ] && pass "Aboneler tab=updated HTTP 200" || fail "Aboneler tab=updated HTTP $ABONE_UPD_PAGE"

# ================================================================
section "ADIM 10 — Yeni Abone Ekleme (POST)"
# ================================================================
CSRF_AB=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" "$BASE/aboneler" | grep -o 'name="_token" value="[^"]*"' | grep -o 'value="[^"]*"' | cut -d'"' -f2)

ADD_RESP=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
  -X POST "$BASE/aboneler" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "Accept: text/html,application/json" \
  -d "_token=$CSRF_AB&ABONE_TESIS_NO=TEST-999999&UNVAN=Test+Abone+Sistemi&BOLGE_ADI=Test+Bolge&SAYAC_SERI_NO=TEST-SAYAC-001&ADRES=Test+Sokak+No1" \
  -o /dev/null -w "%{http_code}" -L)

TEST_ABONE=$(php artisan tinker --execute="echo App\Models\Aboneler::where('ABONE_TESIS_NO','TEST-999999')->count();" 2>/dev/null | tail -1)
if [ "$TEST_ABONE" = "1" ]; then
  pass "Yeni abone eklendi (TEST-999999) — HTTP: $ADD_RESP"
else
  fail "Abone eklenemedi! HTTP: $ADD_RESP | DB count: $TEST_ABONE"
fi

# ================================================================
section "ADIM 11 — Mevcut Aboneyi Düzenleme (PUT)"
# ================================================================
TEST_ID=$(php artisan tinker --execute="echo App\Models\Aboneler::where('ABONE_TESIS_NO','TEST-999999')->value('id');" 2>/dev/null | tail -1)
log "  → Test Abone ID: $TEST_ID"

CSRF_PUT=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" "$BASE/aboneler" | grep -o 'name="_token" value="[^"]*"' | grep -o 'value="[^"]*"' | cut -d'"' -f2)

PUT_RESP=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
  -X POST "$BASE/aboneler/$TEST_ID" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "_token=$CSRF_PUT&_method=PUT&ADRES=Guncellenmis+Test+Adres+No99&BOLGE_ADI=Test+Bolge&SAYAC_SERI_NO=TEST-SAYAC-001" \
  -o /dev/null -w "%{http_code}" -L)

UPDATED_ADRES=$(php artisan tinker --execute="echo App\Models\Aboneler::where('ABONE_TESIS_NO','TEST-999999')->value('ADRES');" 2>/dev/null | tail -1)
IS_UPDATED_FLAG=$(php artisan tinker --execute="echo App\Models\Aboneler::where('ABONE_TESIS_NO','TEST-999999')->value('is_updated');" 2>/dev/null | tail -1)

if echo "$UPDATED_ADRES" | grep -qi "Guncellenmis"; then
  pass "Abone adresi güncellendi | HTTP: $PUT_RESP | Adres: $UPDATED_ADRES"
else
  fail "Adres güncellenemedi! HTTP: $PUT_RESP | Adres: $UPDATED_ADRES"
fi

if [ "$IS_UPDATED_FLAG" = "0" ] || [ "$IS_UPDATED_FLAG" = "" ]; then
  pass "is_updated bayrağı manuel güncelleme sonrası false (düzgün)"
else
  warn "is_updated bayrağı hâlâ true — beklenen: false"
fi

# ================================================================
section "ADIM 12 — mark-old (Onaylama) API Testi"
# ================================================================
CSRF_MO=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" "$BASE/aboneler" | grep -o 'name="_token" value="[^"]*"' | grep -o 'value="[^"]*"' | cut -d'"' -f2)
MO_RESP=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
  -X POST "$BASE/aboneler/$TEST_ID/mark-old" \
  -H "X-CSRF-TOKEN: $CSRF_MO" \
  -H "Accept: application/json" \
  -o - -w "\n---HTTP:%{http_code}")
MO_CODE=$(echo "$MO_RESP" | grep -o 'HTTP:[0-9]*' | cut -d: -f2)
MO_SUCCESS=$(echo "$MO_RESP" | grep -v 'HTTP:' | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('success','?'))" 2>/dev/null)
[ "$MO_SUCCESS" = "True" ] && pass "mark-old API çalışıyor (HTTP $MO_CODE)" || fail "mark-old API hatası! HTTP: $MO_CODE"

# ================================================================
section "ADIM 13 — Abone Silme (DELETE)"
# ================================================================
DEL_RESP=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
  -X DELETE "$BASE/aboneler/$TEST_ID" \
  -H "X-CSRF-TOKEN: $CSRF_MO" \
  -H "Accept: application/json" \
  -w "\n---HTTP:%{http_code}")
DEL_CODE=$(echo "$DEL_RESP" | grep -o 'HTTP:[0-9]*' | cut -d: -f2)
DEL_SUCCESS=$(echo "$DEL_RESP" | grep -v 'HTTP:' | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('success','?'))" 2>/dev/null)
STILL_EXISTS=$(php artisan tinker --execute="echo App\Models\Aboneler::where('ABONE_TESIS_NO','TEST-999999')->count();" 2>/dev/null | tail -1)

if [ "$DEL_SUCCESS" = "True" ] && [ "$STILL_EXISTS" = "0" ]; then
  pass "Abone silindi (HTTP $DEL_CODE) — DB'de yok"
else
  fail "Silme hatası! HTTP: $DEL_CODE | Success: $DEL_SUCCESS | DB sayı: $STILL_EXISTS"
fi

# ================================================================
section "ADIM 14 — İtiraz API Testi"
# ================================================================
FIRST_HAVUZ_ID=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::where('kontrol_edildi',false)->where('kayit_durumu','!=','mukerrer')->first()->id ?? 0;" 2>/dev/null | tail -1)
log "  → Test Havuz ID: $FIRST_HAVUZ_ID"

if [ "$FIRST_HAVUZ_ID" != "0" ] && [ -n "$FIRST_HAVUZ_ID" ]; then
  CSRF_IT=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" "$BASE/import/staging" | grep -o '"_token":"[^"]*"' | cut -d'"' -f4 | head -1)
  [ -z "$CSRF_IT" ] && CSRF_IT=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" "$BASE/import/staging" | grep -o 'csrf-token" content="[^"]*"' | cut -d'"' -f3)
  
  IT_RESP=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
    -X POST "$BASE/import/staging/$FIRST_HAVUZ_ID/itiraz" \
    -H "Content-Type: application/json" \
    -H "X-CSRF-TOKEN: $CSRF_IT" \
    -H "Accept: application/json" \
    -d "{\"itiraz_nedeni\": \"Test itiraz - entegrasyon testi\"}" \
    -w "\n---HTTP:%{http_code}")
  IT_CODE=$(echo "$IT_RESP" | grep -o 'HTTP:[0-9]*' | cut -d: -f2)
  IT_SUCCESS=$(echo "$IT_RESP" | grep -v 'HTTP:' | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('success','?'))" 2>/dev/null)
  [ "$IT_SUCCESS" = "True" ] && pass "İtiraz API çalışıyor (HTTP $IT_CODE)" || fail "İtiraz API hatası! HTTP: $IT_CODE"
  
  # İtrazı geri al
  IT_CANCEL=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
    -X POST "$BASE/import/staging/$FIRST_HAVUZ_ID/itiraz-iptal" \
    -H "X-CSRF-TOKEN: $CSRF_IT" \
    -H "Accept: application/json" \
    -w "\n---HTTP:%{http_code}")
  IT_C_CODE=$(echo "$IT_CANCEL" | grep -o 'HTTP:[0-9]*' | cut -d: -f2)
  IT_C_SUCCESS=$(echo "$IT_CANCEL" | grep -v 'HTTP:' | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('success','?'))" 2>/dev/null)
  [ "$IT_C_SUCCESS" = "True" ] && pass "İtiraz iptal API çalışıyor (HTTP $IT_C_CODE)" || fail "İtiraz iptal API hatası!"
else
  warn "İtiraz testi için bekleyen havuz kaydı bulunamadı"
fi

# ================================================================
section "ADIM 15 — Toggle Onay API Testi"
# ================================================================
FIRST_BEKLEYEN=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::where('kontrol_edildi',false)->where('kayit_durumu','!=','mukerrer')->first()->id ?? 0;" 2>/dev/null | tail -1)
if [ "$FIRST_BEKLEYEN" != "0" ] && [ -n "$FIRST_BEKLEYEN" ]; then
  CSRF_TOG=$(curl -s -c "$COOKIE_FILE" -b "$COOKIE_FILE" "$BASE/import/staging" | grep -o 'csrf-token" content="[^"]*"' | cut -d'"' -f3)
  TOG_RESP=$(curl -s -b "$COOKIE_FILE" -c "$COOKIE_FILE" \
    -X PATCH "$BASE/import/staging/$FIRST_BEKLEYEN/toggle-kontrol" \
    -H "X-CSRF-TOKEN: $CSRF_TOG" \
    -H "Accept: application/json" \
    -w "\n---HTTP:%{http_code}")
  TOG_CODE=$(echo "$TOG_RESP" | grep -o 'HTTP:[0-9]*' | cut -d: -f2)
  TOG_SUCCESS=$(echo "$TOG_RESP" | grep -v 'HTTP:' | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('success','?'))" 2>/dev/null)
  [ "$TOG_SUCCESS" = "True" ] && pass "Toggle onay API çalışıyor (HTTP $TOG_CODE)" || fail "Toggle onay hatası! HTTP: $TOG_CODE"
else
  warn "Toggle testi için bekleyen kayıt yok"
fi

# ================================================================
section "ADIM 16 — Activity Log Kontrolü"
# ================================================================
ACTIVITY_PAGE=$(curl -s -b "$COOKIE_FILE" "$BASE/activity-logs" -o /dev/null -w "%{http_code}")
[ "$ACTIVITY_PAGE" = "200" ] && pass "Activity logs sayfası HTTP 200" || fail "Activity logs HTTP $ACTIVITY_PAGE"

LOG_COUNT=$(php artisan tinker --execute="echo App\Models\ActivityLog::count();" 2>/dev/null | tail -1)
[ "$LOG_COUNT" -gt 0 ] && pass "ActivityLog kayıtları var ($LOG_COUNT adet)" || fail "ActivityLog boş!"

# ================================================================
section "ADIM 17 — Ham Veri Sayfası"
# ================================================================
HAM_PAGE=$(curl -s -b "$COOKIE_FILE" "$BASE/import/hamveri" -o /dev/null -w "%{http_code}")
[ "$HAM_PAGE" = "200" ] && pass "Ham veri sayfası HTTP 200" || fail "Ham veri HTTP $HAM_PAGE"

# ================================================================
section "ADIM 18 — Import Logs Sayfası"
# ================================================================
LOGS_PAGE=$(curl -s -b "$COOKIE_FILE" "$BASE/import/logs" -o /dev/null -w "%{http_code}")
[ "$LOGS_PAGE" = "200" ] && pass "Import logs sayfası HTTP 200" || fail "Import logs HTTP $LOGS_PAGE"

# ================================================================
section "ADIM 19 — Veri Kalitesi Kontrolleri"
# ================================================================
# Import log durumu tümü tamamlandı mı?
HATA_LOG=$(php artisan tinker --execute="echo App\Models\ImportLog::where('durum','hata')->count();" 2>/dev/null | tail -1)
TAMAM_LOG=$(php artisan tinker --execute="echo App\Models\ImportLog::where('durum','tamamlandi')->count();" 2>/dev/null | tail -1)
[ "$HATA_LOG" = "0" ] && pass "Hiç hatalı ImportLog yok" || fail "$HATA_LOG adet hatalı import log var!"
[ "$TAMAM_LOG" -gt 0 ] && pass "$TAMAM_LOG import log 'tamamlandi' durumunda" || warn "Tamamlanan import yok"

# Havuzda fatura_no NULL kontrolü
NULL_FATURA=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::whereNull('fatura_no')->count();" 2>/dev/null | tail -1)
[ "$NULL_FATURA" = "0" ] && pass "Havuzda NULL fatura_no yok" || warn "$NULL_FATURA kayıtta fatura_no NULL"

# Payload NULL kontrolü
NULL_PAYLOAD=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::whereNull('payload')->count();" 2>/dev/null | tail -1)
[ "$NULL_PAYLOAD" = "0" ] && pass "Havuzda NULL payload yok" || warn "$NULL_PAYLOAD kayıtta payload NULL"

# tesisat_no NULL kontrolü
NULL_TESISAT=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::whereNull('tesisat_no')->count();" 2>/dev/null | tail -1)
[ "$NULL_TESISAT" = "0" ] && pass "Havuzda NULL tesisat_no yok" || warn "$NULL_TESISAT kayıtta tesisat_no NULL"

# Anomali detay
php artisan tinker --execute="
\$anomaliCount = App\Models\BeklemeKontrolHavuzu::whereJsonLength('payload->_anomaliler','>',0)->count();
\$typeCounts = [];
App\Models\BeklemeKontrolHavuzu::whereJsonLength('payload->_anomaliler','>',0)->each(function(\$r) use (&\$typeCounts) {
  foreach((\$r->payload['_anomaliler'] ?? []) as \$a) {
    \$kod = is_array(\$a) ? (\$a['kod'] ?? '?') : \$a;
    \$typeCounts[\$kod] = (\$typeCounts[\$kod] ?? 0) + 1;
  }
});
foreach(\$typeCounts as \$k => \$v) echo '  → ' . \$k . ': ' . \$v . ' adet' . PHP_EOL;" 2>/dev/null | grep "→" | while IFS= read -r line; do echo -e "    ${CYAN}$line${NC}"; done

# ================================================================
section "ÖZET RAPORU"
# ================================================================
TOTAL=$((PASS + FAIL + WARN))
log "\n  Toplam test: $TOTAL"
log "  ${GREEN}✅ PASS: $PASS${NC}"
log "  ${RED}❌ FAIL: $FAIL${NC}"
log "  ${YELLOW}⚠️  WARN: $WARN${NC}"

FINAL_ABONE=$(php artisan tinker --execute="echo App\Models\Aboneler::count();" 2>/dev/null | tail -1)
FINAL_HAVUZ=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::count();" 2>/dev/null | tail -1)
FINAL_IS_NEW=$(php artisan tinker --execute="echo App\Models\Aboneler::where('is_new',true)->count();" 2>/dev/null | tail -1)
FINAL_IS_UPD=$(php artisan tinker --execute="echo App\Models\Aboneler::where('is_updated',true)->count();" 2>/dev/null | tail -1)
FINAL_ANOMALI=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::whereJsonLength('payload->_anomaliler','>',0)->count();" 2>/dev/null | tail -1)
FINAL_ONAY=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::where('kontrol_edildi',true)->count();" 2>/dev/null | tail -1)
FINAL_MUK=$(php artisan tinker --execute="echo App\Models\BeklemeKontrolHavuzu::where('kayit_durumu','mukerrer')->count();" 2>/dev/null | tail -1)

log "\n${BOLD}  📊 Son Veritabanı Durumu:${NC}"
log "  ├─ Aboneler:           $FINAL_ABONE"
log "  ├─ Yeni (is_new):      $FINAL_IS_NEW"
log "  ├─ Güncellenen:        $FINAL_IS_UPD"
log "  ├─ Kontrol Havuzu:     $FINAL_HAVUZ"
log "  ├─ Onaylanan:          $FINAL_ONAY"
log "  ├─ Mükerrer fatura:    $FINAL_MUK"
log "  └─ Anomalili fatura:   $FINAL_ANOMALI"

log "\n  📁 Sonuçlar kaydedildi: $LOG_FILE"
if [ "$FAIL" = "0" ]; then
  log "\n  ${GREEN}${BOLD}🎉 SİSTEM HAZIR — Hiç kritik hata bulunmadı!${NC}"
else
  log "\n  ${RED}${BOLD}⛔ $FAIL KRİTİK HATA MEVCUT — Düzeltme gerekiyor!${NC}"
fi
