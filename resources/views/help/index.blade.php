@extends('frontend.layouts.app')
@section('content')

<style>
:root {
  --p1: #1e293b; --p2: #0f172a; --p3: #334155;
  --a1: #3b82f6; --a2: #2563eb;
  --text: #0f172a; --text2: #475569; --muted: #94a3b8;
  --card: #ffffff; --bg: #f1f5f9;
  --border: #e2e8f0;
  --sh: 0 4px 20px rgba(15, 23, 42, 0.05);
  --r: 16px; --ease: cubic-bezier(0.4, 0, 0.2, 1);
}
.pg { padding: 1.5rem; min-height: 100vh; background: var(--bg); font-family: 'Inter', sans-serif;}

.h-header {
  background: linear-gradient(135deg, var(--p2), var(--p1));
  border-radius: var(--r); padding: 3rem 2rem; color: #fff;
  position: relative; overflow: hidden; margin-bottom: 2rem;
  box-shadow: 0 10px 30px rgba(15,23,42,0.15);
}
.h-header::before {
  content: ''; position: absolute; top: -50%; right: -10%;
  width: 400px; height: 400px; background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%);
  border-radius: 50%;
}
.h-title { font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem; letter-spacing: -0.5px; }
.h-sub { font-size: 0.95rem; color: rgba(255,255,255,0.7); max-width: 600px; line-height: 1.6; }

.h-grid { display: grid; grid-template-columns: 280px 1fr; gap: 2rem; align-items: start; }
@media (max-width: 900px) { .h-grid { grid-template-columns: 1fr; } }

/* Sidebar Nav */
.h-nav { background: var(--card); border-radius: var(--r); padding: 1rem; box-shadow: var(--sh); position: sticky; top: 20px; }
.h-nav-item {
  display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1rem;
  color: var(--text2); font-size: 0.9rem; font-weight: 600; border-radius: 10px; cursor: pointer;
  transition: all 0.2s var(--ease); border: 1px solid transparent;
}
.h-nav-item:hover { background: #f8fafc; color: var(--a1); }
.h-nav-item.active { background: #eff6ff; color: var(--a2); border-color: #bfdbfe; }
.h-nav-item i { width: 20px; text-align: center; font-size: 1.1rem; }

/* Content Areas */
.h-content-area { display: none; animation: h-fade 0.3s forwards; }
.h-content-area.active { display: block; }
@keyframes h-fade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.h-card { background: var(--card); border-radius: var(--r); padding: 2rem; box-shadow: var(--sh); margin-bottom: 1.5rem; }
.h-card-title { font-size: 1.2rem; font-weight: 800; color: var(--text); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.6rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
.h-card-title i { color: var(--a1); }

.h-p { font-size: 0.9rem; color: var(--text2); line-height: 1.7; margin-bottom: 1rem; }

/* Steps */
.step-item { display: flex; gap: 1rem; margin-bottom: 1.5rem; position: relative; }
.step-item:not(:last-child)::before { content: ''; position: absolute; left: 15px; top: 35px; bottom: -15px; width: 2px; background: #e2e8f0; }
.step-num { width: 32px; height: 32px; border-radius: 50%; background: #eff6ff; color: var(--a2); display: flex; center; justify-content: center; align-items: center; font-weight: 800; font-size: 0.9rem; flex-shrink: 0; z-index: 2; border: 2px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; }
.step-box { flex: 1; background: #f8fafc; border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; }
.step-box h5 { margin: 0 0 0.5rem; font-size: 1rem; font-weight: 700; color: var(--text); }
.step-box p { margin: 0; font-size: 0.85rem; color: var(--text2); line-height: 1.6; }

/* Alert Boxes */
.h-alert { display: flex; gap: 1rem; padding: 1.25rem; border-radius: 12px; margin: 1.5rem 0; align-items: flex-start; }
.h-alert-ic { font-size: 1.5rem; flex-shrink: 0; }
.h-alert-body h5 { margin: 0 0 0.25rem; font-size: 0.95rem; font-weight: 800; }
.h-alert-body p { margin: 0; font-size: 0.85rem; line-height: 1.5; }

.alert-danger-c { background: #fef2f2; border: 1px solid #fecaca; }
.alert-danger-c .h-alert-ic { color: #ef4444; }
.alert-danger-c .h-alert-body h5 { color: #991b1b; }
.alert-danger-c .h-alert-body p { color: #b91c1c; }

.alert-warning-c { background: #fffbeb; border: 1px solid #fde68a; }
.alert-warning-c .h-alert-ic { color: #f59e0b; }
.alert-warning-c .h-alert-body h5 { color: #b45309; }
.alert-warning-c .h-alert-body p { color: #d97706; }

.alert-info-c { background: #eff6ff; border: 1px solid #bfdbfe; }
.alert-info-c .h-alert-ic { color: #3b82f6; }
.alert-info-c .h-alert-body h5 { color: #1e40af; }
.alert-info-c .h-alert-body p { color: #1d4ed8; }

/* Mini Grid */
.f-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
.f-item { padding: 1.25rem; border: 1px solid var(--border); border-radius: 12px; background: #fff; text-align: center; transition: all 0.2s; }
.f-item:hover { transform: translateY(-3px); box-shadow: var(--sh); border-color: rgba(59,130,246,0.5); }
.f-item i { font-size: 1.8rem; color: var(--a1); margin-bottom: 0.75rem; display: block; }
.f-item h6 { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text); }
.f-item p { font-size: 0.75rem; color: var(--muted); margin: 0; }

/* Accordion SSS */
.faq-item { border: 1px solid var(--border); border-radius: 8px; margin-bottom: 0.75rem; overflow: hidden; background: #fff; }
.faq-title { padding: 1rem; cursor: pointer; font-weight: 700; color: var(--text); display: flex; justify-content: space-between; align-items: center; background: #f8fafc; transition: background 0.2s; }
.faq-title:hover { background: #eff6ff; color: var(--a1); }
.faq-title i { transition: transform 0.3s; }
.faq-content { padding: 0 1rem; max-height: 0; overflow: hidden; transition: max-height 0.3s ease, padding 0.3s ease; font-size: 0.85rem; color: var(--text2); line-height: 1.6; }
.faq-item.active .faq-content { padding: 1rem; max-height: 800px; border-top: 1px solid var(--border); }
.faq-item.active .faq-title i { transform: rotate(180deg); }

/* Destek Form */
.support-form .form-control { border-radius: 8px; padding: 0.75rem 1rem; border: 1px solid var(--border); font-size: 0.9rem; margin-bottom: 1rem; width: 100%; box-sizing: border-box; font-family: 'Inter', sans-serif;}
.support-form .form-control:focus { outline: none; border-color: var(--a1); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.support-btn { background: var(--a1); color: #fff; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; font-family: 'Inter', sans-serif;}
.support-btn:hover { background: var(--a2); }

/* Status Badges */
.badge-st { padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;}
.badge-err { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
.badge-warn { background: #fef3c7; color: #f59e0b; border: 1px solid #fde68a; }
</style>

<div class="pg">
    <div class="h-header">
        <div class="h-title"><i class="fas fa-book-reader mr-3"></i>SMART ŞUSKİ Bilgi Bankası</div>
        <div class="h-sub">Sistemin nasıl çalıştığı, arka plandaki kontroller, abone yönetim detayları ve fatura içe aktarma senaryolarına dair kullanım rehberi.</div>
    </div>

    <div class="h-grid">
        <!-- Sidebar Navigation -->
        <div class="h-nav">
            <div class="h-nav-item active" onclick="switchH('genel')" id="btn-genel">
                <i class="fas fa-sitemap"></i> Sistemin İşleyişi
            </div>
            <div class="h-nav-item" onclick="switchH('aboneler')" id="btn-aboneler">
                <i class="fas fa-users"></i> Aboneler Yönetimi
            </div>
            <div class="h-nav-item" onclick="switchH('import')" id="btn-import">
                <i class="fas fa-file-excel"></i> İçe Aktarma (Import)
            </div>
            <div class="h-nav-item" onclick="switchH('anomali')" id="btn-anomali">
                <i class="fas fa-shield-alt"></i> Anomali Kontrolleri
            </div>
            <div class="h-nav-item" onclick="switchH('kullanici')" id="btn-kullanici">
                <i class="fas fa-user-lock"></i> Yetki ve Kullanıcılar
            </div>
            <div class="h-nav-item" onclick="switchH('hata')" id="btn-hata">
                <i class="fas fa-bug"></i> Hata Kodları ve Çözümleri
            </div>
            <div class="h-nav-item" onclick="switchH('kesinlesen')" id="btn-kesinlesen">
                <i class="fas fa-file-invoice-dollar"></i> Ödeme Emirleri (Kesinleşenler)
            </div>
            <div class="h-nav-item" onclick="switchH('bakim')" id="btn-bakim">
                <i class="fas fa-tools"></i> Veri Bakımı ve Onarım
            </div>
            <div class="h-nav-item" onclick="switchH('sss')" id="btn-sss">
                <i class="fas fa-question-circle"></i> Sıkça Sorulan Sorular
            </div>
            <div class="h-nav-item" onclick="switchH('destek')" id="btn-destek">
                <i class="fas fa-headset"></i> Destek & İletişim
            </div>
        </div>

                <!-- Content Box: Sistem İşleyişi -->
        <div class="h-content-area active" id="cnt-genel">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-sitemap"></i> Sistemin Temel İşleyişi</div>
                <p class="h-p">SMART ŞUSKİ; kurumun faturasını ödediği abonelerin elektrik tüketimlerinin Excel listesinden <strong>güvenli ve hatasız</strong> biçimde sisteme aktarılmasını, şüpheli faturaların otomatik tespit edilmesini ve onay süreçlerinin kolayca yönetilmesini sağlar.</p>

                <div class="h-alert alert-info-c" style="margin-bottom:1.5rem;">
                    <div class="h-alert-ic"><i class="fas fa-info-circle"></i></div>
                    <div class="h-alert-body">
                        <h5>Kontrollü Geçiş (Bekleme Havuzu)</h5>
                        <p>Yüklediğiniz veriler hemen sisteme işlenmez. Önce <strong>Bekleme Havuzu'na</strong> alınır, hatalara karşı kontrol edilir ve sizin onayınızdan geçtikten sonra asıl kayıtlara eklenir.</p>
                    </div>
                </div>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-stream mr-2 text-primary"></i>Adım Adım İşlem Akışı</h5>

                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-box">
                        <h5><i class="fas fa-shield-alt mr-1" style="color:#ef4444"></i> Format Kontrolü</h5>
                        <p>Yüklenen Excel dosyası, onaylı şablonumuzla karşılaştırılır. Belirlenen formata uymayan bir dosya varsa, yanlışlık olmaması adına işlem anında durdurulur. Dosyada Fatura No, Tesisat No, Tutar gibi alanların mutlaka olması istenir.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-box">
                        <h5><i class="fas fa-fingerprint mr-1" style="color:#f59e0b"></i> Mükerrer (Çift) Kayıt Önleme</h5>
                        <p>Dosyanın içeriği sistem tarafından gizli bir kodla işaretlenir. Daha önce yüklenmiş aynı fatura listesi (dosya adı değişmiş bile olsa) tekrar yüklenmeye çalışılırsa sistem çift fatura ödemesini engellemek için dosyayı hemen reddeder.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-box">
                        <h5><i class="fas fa-calendar-check mr-1" style="color:#8b5cf6"></i> Otomatik Ay Bulma</h5>
                        <p>Dosyadaki bilgilere bakılarak hangi aya ait fatura yüklendiği sistem tarafından otomatik tespit edilir. Sizin manuel olarak tarih girmenize gerek yoktur.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">4</div>
                    <div class="step-box">
                        <h5><i class="fas fa-layer-group mr-1" style="color:#10b981"></i> Ön İnceleme (Havuz) Kategorileri</h5>
                        <p>Yüklenen listelerdeki "onaylanması gereken" veriler otomatik olarak ilgili ekranlarda <strong>tamamen steril</strong> şekilde ayrıştırılır:</p>
                        <ul style="margin:0.5rem 0 0; padding-left:1.25rem; font-size:0.85rem; color:var(--text2); line-height:1.9;">
                            <li><strong style="color:var(--text);">1. Bekleyenler:</strong> Herhangi bir risk barındırmayan, hata içermeyen <em>sorunsuz</em> standart faturalardır.</li>
                            <li><strong style="color:#be123c;">2. Anomaliler:</strong> Astronomik sarfiyat artışları, trafo çarpan sapması veya sıfır tüketim gibi <em>kritik riskler</em> içeren izolasyon alanıdır.</li>
                            <li><strong style="color:#9f1239;">3. Reaktifler:</strong> Panosunda kompanzasyon ihlali olan ve <em>reaktif/kapasitif bedel cezası</em> yansıyan faturalardır. Bu sekmeden <strong>"Reaktif Faturaları Gönder"</strong> butonuyla tüm reaktif cezalı faturaları özel "Reaktifler" tablosuna taşıyabilirsiniz.</li>
                            <li><strong style="color:#ef4444;">4. Mükerrer:</strong> Zaten yüklenmiş olup mükerrer sayılan faturalardır.</li>
                        </ul>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">5</div>
                    <div class="step-box" style="border: none; background: #f0fdf4; border: 1px dashed #86efac;">
                        <h5><i class="fas fa-check-double mr-1" style="color:#16a34a"></i> Kesinleştirme ve Ödeme Emri</h5>
                        <p>Havuzda onayladığınız faturalar son bir kontrolden sonra <strong>"Kesinleşen Faturalar"</strong> listesine aktarılır. Bu işlemden sonra veriler resmi kayıt haline gelir ve muhasebe birimine gönderilmek üzere ödeme emri (çıktı) oluşturulur.</p>
                        <p style="margin-top:0.5rem; font-size:0.85rem;"><strong>İpucu:</strong> Tüm bekleyen faturaları tek tıkla onaylamak için <strong>"Tümünü Onayla"</strong> butonunu kullanabilirsiniz.</p>
                    </div>
                </div>
            </div>

            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-cogs"></i> Esnek Formata Uyum</div>
                <p class="h-p">Elektrik dağıtım firmaları Excel fatura listelerinde yeni bir sütun eklediğinde veya değiştirdiğinde, sistemde büyük bir yapılandırma karışıklığı yaşanmaz. Her ayarda oynanabilirlik sağlanarak esneklik korunmuştur.</p>
            </div>
        </div>

                <!-- Content Box: Aboneler Yönetimi -->
        <div class="h-content-area" id="cnt-aboneler">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-users"></i> Aboneler Yönetimi ve İşleyiş Mantığı</div>
                <p class="h-p">Sistem, kurumunuza ait tesisat listesini her Excel yüklendiğinde güncel tutar. Dosyada ilk kez görülen bir abone veya mevcut abonenin bilgilerinde değişim (sayaç/adres) varsa, bunları arka planda otomatik güncelleyerek Aboneler listesine (YENİ / GÜNCELLENDİ rozetleriyle) anında yansıtır. Ayrı bir havuz süreciyle vaktinizi kaybettirmez.</p>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-star text-warning"></i> Yeni Abone ve Hızlı Güncelleme Kavramı</h5>
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-box">
                        <h5>Otomatik Keşif ve Kayıt</h5>
                        <p>Excel aktarılırken tesisat numaraları tek tek kontrol edilir. Daha önce kurumunuzda hiç bulunmayan yepyeni bir abone veya adresi/sayacı değişmiş eski bir abone tespit edildiğinde sistem bu aboneleri veritabanında hemen oluşturur/günceller. Fatura onaylarken bu tip bilgi güncellemeleri için ekstra yorulmazsınız.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-box">
                        <h5>Aboneler Listesinden Takip Edebilirsiniz</h5>
                        <p>Hangi abonenin yeni eklendiğini, hangisinin ise sayacının veya adresinin değiştiğini görmek için ana menüdeki <strong>"Aboneler"</strong> sekmesini kullanabilirsiniz. Orada sadece bu tarz işlemlere maruz kalanlara ait "Yeni Eklendi" ve "Bilgi Güncellendi" şeklindeki rozetleri görebilirsiniz.</p>
                    </div>
                </div>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-user-slash" style="color:#ef4444"></i> Aboneleri Pasife Alma ve Aktif Etme</h5>
                <p class="h-p">Kurumla ilişiği kesilen, sözleşmesi iptal olan veya geçici olarak faturalandırılmayacak olan tesisatları silmek yerine <strong>Pasif</strong> duruma getirebilirsiniz.</p>
                <div class="step-item">
                    <div class="step-num"><i class="fas fa-toggle-on"></i></div>
                    <div class="step-box">
                        <h5>Durum Değiştirme (Aktif / Pasif) İşlemi</h5>
                        <p><strong>Aboneler Listesi</strong> ekranında her satırın sağ tarafında yer alan işlem düğmelerinden (<i class="fas fa-user-slash"></i>/<i class="fas fa-user-check"></i>) tıklayarak abonenin durumunu değiştirebilirsiniz. Pasif duruma getirilen aboneler, karışıklığı önlemek adına <strong>"Pasif Aboneler"</strong> isimli kendi özel sekmelerine aktarılır ve soluk (opasitesi düşük) şekilde listelenir. Dilediğiniz zaman bu sekmeden aboneleri tekrar Aktif yapabilirsiniz.</p>
                    </div>
                </div>

                <div class="h-alert alert-info-c">
                    <div class="h-alert-ic"><i class="fas fa-microchip"></i></div>
                    <div class="h-alert-body">
                        <h5>Sayaç Geçmişi Takibi</h5>
                        <p>Kuruma ait bir tesiste sayaç değiştiğinde, eski sayaç numarası silinip gitmez. Abone geçmişine bakarak eski tarihlerde hangi sayaçların kullanıldığını rahatlıkla görebilirsiniz.</p>
                    </div>
                </div>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-map-marked-alt"></i> Bölge Eşleştirmesi</h5>
                <p class="h-p">Sisteme giren her abonenin bağlı olduğu ilçe ya da bölge, mevcut bölge tanımlarıyla otomatik birleşir. Eksik bir bölge varsa sonradan sistem ayarlarından kolaylıkla tanımlayabilirsiniz.</p>
            </div>
        </div>

                <!-- Content Box: İçe Aktarma -->
        <div class="h-content-area" id="cnt-import">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-file-excel"></i> İçe Aktarma Süreci ve Senaryolar</div>
                <p class="h-p">Excel'den faturaları aktarırken sunucu yükünü önlemek ve sistemin hatasız çalışmasını sağlamak için her şey otomatik yürütülür. Aşağıda adım adım bir örneği görebilirsiniz.</p>
                
                <div class="h-alert alert-info-c" style="margin-bottom: 1.5rem;">
                    <div class="h-alert-ic"><i class="fas fa-lightbulb"></i></div>
                    <div class="h-alert-body">
                        <h5>Örnek Senaryo: Yeni Ay Faturalarının Sisteme Eklenmesi</h5>
                        <p><strong>Dağıtım Şirketinden Excel Geldiğinde Ne Yapmalıyım?</strong><br><br>
                        1. Sol menüden <strong>İçe Aktar</strong> sayfasına girin.<br>
                        2. Gelen Excel dosyasını sürükleyip ilgili alana bırakın.<br>
                        3. Sistem Excel dosyasını okur ve faturanın hangi döneme ait olduğunu otomatik bulur. Sizin bir ay ve tarih seçmenize gerek yoktur.<br>
                        4. <strong>"İçe Aktarmayı Başlat"</strong> butonuna basın, yükleme bitene kadar sayfayı kapatmayın.<br>
                        5. Aktarım başarıyla bitince sistem sizi <strong>Bekleme Havuzuna</strong> yönlendirir. Atılan faturalar ana listeye işlemeden önce burada denetlenmek üzere bekleyecektir.<br>
                        6. Orada hatalı, cezalı veya yüksek tutarlı faturalar kırmızıyla size bildirilir. Düzgün olanlara "Onayla ve Kaydet" diyerek ana sisteme geçirebilirsiniz.</p>
                    </div>
                </div>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-folder-plus" style="color:#10b981"></i> Ek Fatura ve Geçmiş Dönem Yüklemeleri</h5>
                <p class="h-p">Örneğin Mart dönemi faturalarını sisteme aktardınız ancak daha sonra dağıtım şirketinden "Eksik kalan/Geciken faturalar" şeklinde ek bir liste daha geldi. Sistemin altyapısı bu durumu <strong>tam otomatik</strong> olarak çözer.</p>
                <div class="step-item">
                    <div class="step-num" style="color:#10b981; border-color:#86efac; background:#f0fdf4;"><i class="fas fa-magic"></i></div>
                    <div class="step-box">
                        <h5>Dosya Adını Değiştirip Yüklemeniz Yeterlidir</h5>
                        <p>Sistem, yanlışlıkla aynı dosyanın iki kere yüklenmesini engellediği için; ek gelen dosyanın adını değiştirin <em>(Örn: Mart_Fatura_EK.xlsx)</em> ve normal şekilde sisteme aktarın. Geri kalan tüm kontroller sistem tarafından yapılır:<br>
                           <br>• <strong>Dönemi Kendi Bulur:</strong> Dosyadaki tarihlere bakarak otomatik olarak "Mart" döneminin havuzuna atar. (Aynı durum Geçmiş Dönem yüklemeleri için de geçerlidir.)
                           <br>• <strong>Akıllı Eşleştirme Yapar:</strong> Zaten eklediğiniz bir abone tekrar gelirse bunu "Mükerrer", sadece o ek dosyada olan aboneleri ise "Yeni" Fatura olarak işaretler.
                        </p>
                    </div>
                </div>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-layer-group text-primary"></i> Karışık/Çoklu Dönem İçeren Faturaların Yüklenmesi (Satır Bazlı Dönem Algılama)</h5>
                <p class="h-p">Elektrik faturası Excel listelerinin içinde bazen geçmiş döneme (Örn: Bir yıl önceki düzeltme faturası veya sonradan işlenen faturalar) ait kayıtlar karışık olarak gelebilir. Sistemin finansal kayıt bütünlüğünü (Denetim İzini) korumak için tasarlanan <strong>"Satır Bazlı Dönem Mimari"</strong> algoritması devreye girer.</p>
                <div class="step-item">
                    <div class="step-num" style="color:var(--primary); border-color:var(--primary); background:rgba(79, 70, 229, 0.05);"><i class="fas fa-calendar-alt"></i></div>
                    <div class="step-box">
                        <h5>Aynı Tabloda Farklı Dönemler Kendi Ayına Gider</h5>
                        <p>Diyelim ki siz "Mart" dönemini yüklüyorsunuz ve dosyanın adı Mart olarak kaydedildi. Ancak sistem içindeki satırları okurken "Şubat" ayına ait 3 adet fatura yakaladı.<br>
                           Sistem, bunu eski "Şubat" dosyasına eklemek yerine kendi orijinal <strong>Mart dosyasına</strong> (import işlemi) bağlı tutar. Fakat bu faturaları "Bekleyenlerden -> Kesinleşen Faturalara" atarken sadece o satırın içindeki "Tahakkuk / Dönem" bilgisini okuyarak, o satırı finansal olarak "Şubat" ayına yazar.<br>
                           <strong>Sonuç:</strong> Faturanın hangi Excel ile yüklendiği kaybolmaz, ancak raporlarda ve mali tablolarda tam olarak ait olduğu ayda gözükmeye devam eder. 
                        </p>
                    </div>
                </div>

                <h5 style="margin-top:2.5rem; font-weight:800; color:var(--text); margin-bottom:1rem; padding-bottom: 0.5rem; border-bottom: 1px dashed #e2e8f0;">
                    <i class="fas fa-times-circle text-danger mr-2"></i> Hangi Durumlarda Yükleme İptal Edilir?
                </h5>
                
                <div class="step-item">
                    <div class="step-num"><i class="fas fa-ban"></i></div>
                    <div class="step-box" style="border-left: 3px solid #ef4444;">
                        <h5>1. Mükerrer (Aynı) Dosyanın Yüklenmesi</h5>
                        <p>Daha önce başarıyla yüklenmiş olan bir Excel dosyasını (ismi değiştirilmiş olsa bile) sistemi kandırmak veya yanlışlıkla tekrar yüklemek isterseniz, sistem faturanın içeriğini tanır ve çift fatura işlemini engellemek için işlemi tamamen iptal eder.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num"><i class="fas fa-table"></i></div>
                    <div class="step-box" style="border-left: 3px solid #ef4444;">
                        <h5>2. Eksik Veya Yanlış Sütunlu Dosya</h5>
                        <p>Sistem, gelen Excel dosyasında "Fatura No, Tesisat No, Toplam Fatura Tutarı" gibi çok temel başlıkların olmasını zorunlu tutar.</p>
                        <ul style="margin:0.5rem 0 0; padding-left:1.25rem; font-size:0.85rem; color:var(--text2); line-height:1.9;">
                            <li><strong>Temel sütun yoksa:</strong> Hesaplama yapılamayacağı için işlem iptal edilir.</li>
                            <li><strong>Tanımsız bir format varsa:</strong> Dağıtım firmasının orijinal tablosu dışında kendi başınıza çok farklı sütunlar eklerseniz dosyayı güvenli bulmaz ve reddeder.</li>
                        </ul>
                        <p style="margin-top:0.75rem;">Dosya yapısını kurcalamadan orijinal gönderildiği şekliyle yüklemeniz her zaman en doğrusudur.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num"><i class="fas fa-calendar-times"></i></div>
                    <div class="step-box" style="border-left: 3px solid #ef4444;">
                        <h5>3. Dönemin Bulunamaması</h5>
                        <p>Sistem, dosyada ay ve yıl bilgilerine (Tahakkuk dönemine) ulaşamazsa, hangi dönemin faturası olduğunu bilemeyeceği için geçmiş faturalarla karışmaması adına dosyayı yüklemekten vazgeçer.</p>
                    </div>
                </div>

                <h5 style="margin-top:2.5rem; font-weight:800; color:var(--text); margin-bottom:1rem; padding-bottom: 0.5rem; border-bottom: 1px dashed #e2e8f0;">
                    <i class="fas fa-trash-alt text-danger mr-2"></i> Hatalı Yüklemeleri Silme ve Geri Alma
                </h5>
                <p class="h-p">Eğer bir fatura dosyasını yanlışlıkla yüklediyseniz (Örn: Yanlış aya ait dosya seçilmesi veya eksik liste yüklenmesi), sisteme işlenen tüm verileri tek tuşla temizleyebilirsiniz.</p>

                <div class="h-alert alert-warning-c">
                    <div class="h-alert-ic"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="h-alert-body">
                        <h5>Dosya Silme İşlemi Adımları</h5>
                        <p>1. Sol menüden <strong>Veri Aktar > Yükleme Geçmişi (Loglar)</strong> sayfasına gidin.<br>
                        2. Silmek istediğiniz dosyanın yanındaki kırmızı <strong>"Sil"</strong> butonuna basın.<br>
                        3. Uyarı penceresini onayladığınızda; o dosyaya ait ham veriler ve havuzdaki tüm faturalar silinecektir.</p>
                    </div>
                </div>

                <div class="h-alert alert-danger-c" style="margin-top:1rem;">
                    <div class="h-alert-ic"><i class="fas fa-lock"></i></div>
                    <div class="h-alert-body">
                        <h5>Hangi Durumlarda Silme Yapılamaz?</h5>
                        <p>Eğer o yükleme dosyasından bazı faturaları çoktan onaylayıp <strong>"Kesinleşen Faturalar / Ödeme Emirleri"</strong> listesine aktardıysanız, sistem veri tutarlılığı için bu aktarımı silmenize izin vermez. Önce kesinleşen kayıtları temizlemeli veya teknik destek almalısınız.</p>
                    </div>
                </div>

                <h5 style="margin-top:2.5rem; font-weight:800; color:var(--text); margin-bottom:1rem; padding-bottom: 0.5rem; border-bottom: 1px dashed #e2e8f0;">
                    <i class="fas fa-sync-alt text-success mr-2"></i> Havuzu Yeniden Ayrıştırma (Restore Staging)
                </h5>
                <p class="h-p">Eğer bekleyen faturalar havuzunda işlemleri birbirine karıştırdıysanız veya verilerin ayrıştırma/kategorize (Anomali, Reaktif vs.) işlemlerini ilk günkü haline sıfırlamak istiyorsanız bu özelliği kullanabilirsiniz.</p>
                <div class="step-item">
                    <div class="step-num" style="color:#10b981; border-color:#10b981; background:rgba(16, 185, 129, 0.05);"><i class="fas fa-redo"></i></div>
                    <div class="step-box" style="border-left: 3px solid #10b981;">
                        <h5>Havuzu Baştan Yaratmak (Sıfırlamak)</h5>
                        <p>1. Sol menüden <strong>Veri Aktar > Yükleme Geçmişi (Loglar)</strong> sayfasına gidin.<br>
                        2. İstediğiniz dosyanın yanındaki yeşil renkli <strong>"Yeniden Ayrıştır"</strong> butonuna tıklayın.<br>
                        3. Sistem mevcut onaylanmamış havuzdaki her şeyi çöpe atar ve elindeki orijinal (ham) verileri tarayarak sistemi hatasız şekilde tekrar inşa eder.<br>
                        <em>Not: Eğer o faturadan 1 adet bile kesinleşen varsa sistem bu restore işlemine güvenlik gereği izin vermez.</em></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Content Box: Anomali -->
        <div class="h-content-area" id="cnt-anomali">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-shield-alt"></i> Anomali ve Güvenlik Kontrolleri</div>
                <p class="h-p">Sistemin en büyük ve temel yapı taşı: <strong>Anomali Filtreleridir.</strong> Kuruma gelen binlerce satırlık Excel listelerinde fark edilmesi imkansız olan teknik ve mali kayıpları bularak sistemin sizi uyarmasını sağlar.</p>
                
                <div class="f-grid" style="grid-template-columns: 1fr; gap: 1.5rem;">
                    <!-- Eksi Endeks -->
                    <div class="f-item" style="text-align: left; display: flex; gap: 1.5rem; align-items: flex-start;">
                        <i class="fas fa-arrow-down text-danger" style="font-size: 2.2rem; flex-shrink:0; width: 40px; text-align: center;"></i>
                        <div>
                            <h6>Eksi (-) veya Hatalı Endeks (Tüketim Yok / Tutarsız Boyut)</h6>
                            <p style="margin-bottom:0.5rem; color:var(--text2); font-size:0.85rem;"><strong>Ne zaman oluşur:</strong> Faturadaki <i>Son Endeks</i> ile <i>İlk Endeks</i> arasındaki fark hesaplandığında sonucun negatif (Eksi) veya teknik olarak imkansız olan SIFIR tüketim çıktığı durumlarda tespit edilir.</p>
                            <p style="color:#64748b; font-size:0.8rem; background:#f8fafc; padding:10px; border-radius:8px; border-left: 3px solid var(--border);"><strong>Meydana Geliş Senaryosu:</strong> Tesisattaki pano arızalanmış olabilir, sayaç geriye sarmış veya tamamen sıfırlanmış olabilir. En yaygın olay; veri dağıtım şirketinden gelirken endeks kolonlarındaki okuma rakamlarının klavyeyle yanlış veya ters yazılmasıdır.</p>
                        </div>
                    </div>

                    <!-- Çarpan Değişimi -->
                    <div class="f-item" style="text-align: left; display: flex; gap: 1.5rem; align-items: flex-start;">
                        <i class="fas fa-random text-warning" style="font-size: 2.2rem; flex-shrink:0; width: 40px; text-align: center;"></i>
                        <div>
                            <h6>Çarpan Değişimi</h6>
                            <p style="margin-bottom:0.5rem; color:var(--text2); font-size:0.85rem;"><strong>Ne zaman oluşur:</strong> Abonenin elektrik tüketimlerini çarptığımız katsayının (Akım / Gerilim Trafosu oranları), abonenin geçmişte sisteme kaydedilmiş oranlarıyla hiçbir şekilde Birebir (1:1) eşleşmediği noktada sistem uyarır.</p>
                            <p style="color:#64748b; font-size:0.8rem; background:#fffbeb; padding:10px; border-radius:8px; border-left: 3px solid #fcd34d;"><strong>Meydana Geliş Senaryosu:</strong> İlgili tesiste (Boru hattı pompası, arıtma vb.) büyük bir trafo değişimi yapılmıştır ancak ŞUSKİ'nin ve sistemin haberi yoktur, veya dağıtım makamı kasti/yanlışlıkla fatura tutarını şişirmek için yüksek oranlı bir trafo çarpanı işlemiştir. Anında müdahale edilip teyitlenmelidir.</p>
                        </div>
                    </div>

                    <!-- Reaktif Ceza -->
                    <div class="f-item" style="text-align: left; display: flex; gap: 1.5rem; align-items: flex-start;">
                        <i class="fas fa-bolt text-danger" style="font-size: 2.2rem; flex-shrink:0; width: 40px; text-align: center;"></i>
                        <div>
                            <h6>Reaktif / Kapasitif Ceza Sınırı İhlali</h6>
                            <p style="margin-bottom:0.5rem; color:var(--text2); font-size:0.85rem;"><strong>Ne zaman oluşur:</strong> Elektrik Piyasası mevzuatı gereği (Aktif tüketimin %20'si İndüktif, %15'i Kapasitif limiti şayet aşılırsa) kuruma kesilen ve mali tahribatlara yol açan bedel cezalarıdır, görüldüğü an sistem engeller ve karantinaya hapseder.</p>
                            <p style="color:#64748b; font-size:0.8rem; background:#fef2f2; padding:10px; border-radius:8px; border-left: 3px solid #fecaca;"><strong>Meydana Geliş Senaryosu:</strong> O abonenin veya pompanın olduğu tesisteki <kbd>Kompanzasyon Panosunda</kbd> kritik donanımsal bir arıza (röle hatası, kondansatör patlaması vb.) meydana gelmiştir. Kurumun o faturayı geçirmeden (Onaylamadan) önce ilgili şantiyeye elektrik/bakım mühendislerini yönlendirmesi şarttır.</p>
                            
                            <div style="margin-top:1.25rem; padding:0.75rem; background:#fff7ed; border-radius:8px; border-left: 3px solid #fb923c;">
                                <h6 style="margin-top:0; font-size:0.85rem; color:#b45309; font-weight:700;">
                                    <i class="fas fa-arrow-right" style="margin-right:0.4rem;"></i> Reaktif Faturaları Özel Tabloya Taşıma
                                </h6>
                                <p style="margin:0.5rem 0 0; font-size:0.8rem; color:#92400e;">
                                    Reaktif cezası olan tüm faturaları Staging'den çıkarmak ve merkezi bir tabloda yönetmek için:
                                </p>
                                <ol style="margin:0.5rem 0 0; padding-left:1.5rem; font-size:0.8rem; color:#92400e;">
                                    <li><strong>Staging / Bekleme Havuzu</strong> sayfasında <strong>"Reaktifler"</strong> sekmesine tıklayın</li>
                                    <li><strong>"Reaktif Faturaları Gönder"</strong> butonuna basın</li>
                                    <li>Sistem:
                                        <ul style="margin:0.25rem 0; padding-left:1rem; font-size:0.8rem;">
                                            <li>✅ Tüm 60+ alanı özel "Reaktifler" tablosuna kopyalar</li>
                                            <li>✅ Orijinal kayıtları havuzdan siler</li>
                                            <li>✅ İşlemi sistem loglarına kaydeder</li>
                                        </ul>
                                    </li>
                                    <li>Başarı mesajı alırsanız, reaktif faturalar artık <strong>Reaktifler Tablosunda</strong> merkezi yönetim için hazır</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Astronomik Tüketim -->
                    <div class="f-item" style="text-align: left; display: flex; gap: 1.5rem; align-items: flex-start;">
                        <i class="fas fa-chart-line text-primary" style="font-size: 2.2rem; flex-shrink:0; width: 40px; text-align: center;"></i>
                        <div>
                            <h6>Gelişmiş Tüketim Analizi (Günlük Yoğunluk Bazlı) <span class="badge-st badge-warn">YENİ</span></h6>
                            <p style="margin-bottom:0.5rem; color:var(--text2); font-size:0.85rem;"><strong>Ne zaman oluşur:</strong> Bir abonenin mevcut faturasındaki <strong>Günlük Ortalama Tüketimi (kWh/Gün)</strong>, bir önceki dönem faturasıyla kıyaslandığında %300'den fazla artış veya %80'den fazla düşüş gösterdiğinde tespit edilir.</p>
                            <p style="color:#64748b; font-size:0.8rem; background:#eff6ff; padding:10px; border-radius:8px; border-left: 3px solid #bfdbfe;"><strong>Neden Önemli?</strong> Basit toplam tüketim yerine "Günlük Ortalama" baz alındığı için, 28 günlük bir fatura ile 35 günlük bir fatura karşılaştırılırken "gün sayısı farkından kaynaklı" yanlış uyarılar engellenmiş olur. Analiz notunda kıyaslanan önceki faturanın numarası da belirtilerek tam şeffaflık sağlanır.</p>
                        </div>
                    </div>

                    <!-- Dönem Çakışması -->
                    <div class="f-item" style="text-align: left; display: flex; gap: 1.5rem; align-items: flex-start;">
                        <i class="fas fa-copy" style="color: #0ea5e9; font-size: 2.2rem; flex-shrink:0; width: 40px; text-align: center;"></i>
                        <div>
                            <h6>Dönem Çakışması (Mükerrer Gün Kontrolü) <span class="badge-st badge-warn">YENİ</span></h6>
                            <p style="margin-bottom:0.5rem; color:var(--text2); font-size:0.85rem;"><strong>Ne zaman oluşur:</strong> Aynı tesisat numarasına ait iki farklı faturanın tarih aralıkları (İlk Okuma - Son Okuma) birbiriyle çakıştığında (üst üste bindiğinde) sistem alarm verir.</p>
                            <p style="color:#64748b; font-size:0.8rem; background:#f0f9ff; padding:10px; border-radius:8px; border-left: 3px solid #0ea5e9;"><strong>Meydana Geliş Senaryosu:</strong> Sayaç değişimi sırasında tarihlerin yanlış girilmesi veya bir faturanın iptal edilmeden yeniden kesilmesi sonucu oluşur. Bu anomali, abonenin aynı günler için iki defa para ödemesini (Mükerrer Tahsilat) önlemek için geliştirilmiştir.</p>
                        </div>
                    </div>

                    <!-- Ultra Derin Hesaplama -->
                    <div class="f-item" style="text-align: left; display: flex; gap: 1.5rem; align-items: flex-start;">
                        <i class="fas fa-calculator text-danger" style="font-size: 2.2rem; flex-shrink:0; width: 40px; text-align: center;"></i>
                        <div>
                            <h6>Matematiksel Tutarsızlık ve Çarpan Sapması <span class="badge-st badge-warn">YENİ</span></h6>
                            <p style="margin-bottom:0.5rem; color:var(--text2); font-size:0.85rem;"><strong>Ne zaman oluşur:</strong> 1- Faturadaki endeks farkı ile çarpanın çarpımı, faturada beyan edilen "Toplam Tüketim" rakamıyla uyuşmadığında. 2- Abonenin çarpanı önceki faturasına göre değiştiğinde.</p>
                            <p style="color:#64748b; font-size:0.8rem; background:#fef2f2; padding:10px; border-radius:8px; border-left: 3px solid #ef4444;"><strong>Neden Önemli?</strong> Bu kontrol, Excel dosyasındaki verilerin elle değiştirilip değiştirilmediğini veya fatura yazılımında teknik bir hesaplama hatası olup olmadığını yakalar. Gözle görülmesi imkansız olan "küçük kaydırmaları" dahi yakalayabilen en derin güvenlik katmanıdır.</p>
                        </div>
                    </div>

                    <!-- Tarife ve Periyot -->
                    <div class="f-item" style="text-align: left; display: flex; gap: 1.5rem; align-items: flex-start;">
                        <i class="fas fa-id-card text-info" style="color: #8b5cf6; font-size: 2.2rem; flex-shrink:0; width: 40px; text-align: center;"></i>
                        <div>
                            <h6>Tarife Değişimi ve Periyot Sapması <span class="badge-st badge-warn">YENİ</span></h6>
                            <p style="margin-bottom:0.5rem; color:var(--text2); font-size:0.85rem;"><strong>Ne zaman oluşur:</strong> Abone grubunun (Tarife) bir önceki aya göre değişmesi veya faturanın standart dışı (25 günden az, 35 günden fazla) bir dönemi kapsaması durumunda oluşur.</p>
                            <p style="color:#64748b; font-size:0.8rem; background:#f5f3ff; padding:10px; border-radius:8px; border-left: 3px solid #8b5cf6;"><strong>Neden Önemli?</strong> Hatalı tarife tanımları kurumun eksik tahsilat yapmasına yol açabilir. Periyot sapmaları ise aylık mali raporlarda "hayali" artış veya azalışlar görünmesine sebep olur.</p>
                        </div>
                    </div>
                </div>

                <div class="h-alert alert-info-c" style="margin-top: 2rem;">
                    <div class="h-alert-ic"><i class="fas fa-robot"></i></div>
                    <div class="h-alert-body">
                        <h5>Sistemsel Anomali Tespit Raporu</h5>
                        <p>Anomali sayfasında faturanın detayına tıkladığınızda karşınıza çıkan <strong>"Analiz Notu"</strong> bölümü, sistemin neden bu uyarıyı verdiğini size teknik verilerle açıklar. Bu notlarda kıyaslanan fatura numaraları, hesaplanan günlük ortalamalar ve yüzdesel sapmalar net bir şekilde listelenir.</p>
                    </div>
                </div>
            </div>
        </div>

                <!-- Content Box: Kesinleşen Faturalar -->
        <div class="h-content-area" id="cnt-kesinlesen">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-file-invoice-dollar"></i> Ödeme Emirleri ve Kesinleşen Faturalar</div>
                <p class="h-p">Bekleme havuzunda onayladığınız faturaların resmiyet kazandığı ve ödeme sürecine girdiği son duraktır. Bu bölüm, mali disiplin ve raporlama için en kritik alandır.</p>
                
                <div class="h-alert alert-info-c" style="margin-bottom:1.5rem;">
                    <div class="h-alert-ic"><i class="fas fa-money-check-alt"></i></div>
                    <div class="h-alert-body">
                        <h5>Ödeme Emri Oluşturma</h5>
                        <p>Bu sayfadaki kayıtlar artık "Onaylı" statüsündedir. Buradaki listeyi filtreleyerek ilgili dönem veya birim için <strong>Ödeme Listesi (Çıktı)</strong> alabilir ve ödeme birimine iletebilirsiniz.</p>
                    </div>
                </div>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-calculator mr-2 text-primary"></i>Otomatik Toplam ve Raporlama</h5>
                <p class="h-p">Listenin en altında ve üstündeki özet kartlarında, o an ekranda seçili olan tüm faturaların kalem kalem toplamları otomatik hesaplanır:</p>
                <ul style="margin:0.5rem 0 1.5rem; padding-left:1.5rem; font-size:0.9rem; color:var(--text2); line-height:1.7;">
                    <li><strong>Aktif Bedel Toplamı:</strong> Faturadaki asıl kullanım tutarlarının toplamı.</li>
                    <li><strong>Ceza Toplamı:</strong> Varsa reaktif, kapasitif veya diğer cezai işlemlerin toplam maliyeti.</li>
                    <li><strong>KDV Toplamı:</strong> Ödenecek toplam vergi yükü.</li>
                    <li><strong>Genel Toplam:</strong> Kurumun kasasından çıkacak net ödeme miktarı.</li>
                </ul>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-print mr-2 text-info"></i> Resmî Çıktı ve PDF / Excel</h5>
                <p class="h-p">"Listeyi Yazdır (PDF)" ve "Excel'e Aktar" butonları ile hazırladığınız tabloyu resmî evrak standardında dışa aktarabilirsiniz. Çıktılar <strong>Türkçe Karakter</strong> desteğine tam uyumlu (`DejaVu Sans` fontu ve `BOM` kodlamasıyla) üretilmektedir.</p>
            </div>
        </div>

        <!-- Content Box: Veri Bakımı -->
        <div class="h-content-area" id="cnt-bakim">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-tools"></i> Veri Bakımı ve Onarım İşlemleri</div>
                <p class="h-p">Sistemin veri bütünlüğü ve finansal güvenliği için geliştirilmiş özel onarım araçlarını içerir. Özellikle veri aktarım hatalarında (kur farkı, ondalık kayması vb.) sisteme müdahale etmenizi sağlar.</p>

                <div class="h-alert alert-warning-c" style="margin-bottom:1.5rem;">
                    <div class="h-alert-ic"><i class="fas fa-magic"></i></div>
                    <div class="h-alert-body">
                        <h5>Genel Veri Onarımı (100x Hatası Fix)</h5>
                        <p>Dosyadan okunan verilerin (fatura bedeli, çarpan, tüketim miktarları vb.) ondalık hatalar nedeniyle yanlış (örn: 100 kat fazla) kaydedilmesi durumunda kullanılır.</p>
                        <p style="margin-top:0.5rem; font-size:0.8rem;"><strong>Nasıl Kullanılır:</strong> Yükleme Geçmişi (Loglar) sayfasındaki "Genel Veri Onarımı" butonuna bastığınızda, sistem veritabanındaki tüm satırların "Orijinal Ham Veri (Payload)" yedeğine bakarak bedelleri hatasız mantıkla baştan hesaplar ve veritabanını günceller.</p>
                    </div>
                </div>

                <h5 style="font-weight:800; color:var(--p2); margin:1.5rem 0 1rem;"><i class="fas fa-database mr-2 text-primary"></i>Dinamik Sütun Yönetimi</h5>
                <p class="h-p">Dağıtım firmasının Excel dosyalarına yeni sütunlar eklemesi durumunda (örn: 'hesap_adi' kolonunun eklenmesi), sistem aktarım sırasında tabloyu kontrol eder ve eksik olan sütunu SQL seviyesinde <strong>otomatik olarak</strong> oluşturur. Bu sayede manuel veritabanı müdahalesine gerek kalmadan sistem kendini genişletebilir.</p>
            </div>
        </div>

        <!-- Content Box: Kullanıcılar -->
        <div class="h-content-area" id="cnt-kullanici">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-user-lock"></i> Sistem Yönetimi ve Kullanıcı İzinleri</div>
                <p class="h-p">Veri güvenliği açısından sistemde Yönetici ve Personel olmak üzere farklı erişim yetkileri bulunur.</p>
                
                <div class="step-item">
                    <div class="step-num"><i class="fas fa-user-tie"></i></div>
                    <div class="step-box" style="border-left: 3px solid var(--a1);">
                        <h5>Yönetici (Admin) Rolü</h5>
                        <p>Sistemdeki tüm bölümlere tam erişimi vardır. Alt kullanıcıları yönetebilir ve onlara görevleri doğrultusunda sayfaları görme veya işlem yapma yetkileri atayabilir.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num"><i class="fas fa-user"></i></div>
                    <div class="step-box" style="border-left: 3px solid #cbd5e1;">
                        <h5>Personel Rolü</h5>
                        <p>Sadece yöneticinin izin verdiği sayfalarda gezinebilir. Bir personelin Abone silme, fatura dosyasını sisteme yükleme gibi işlemleri yetkilendirilmeden yapma hakkı yoktur.</p>
                    </div>
                </div>

                <div class="h-alert alert-danger-c">
                    <div class="h-alert-ic"><i class="fas fa-ban"></i></div>
                    <div class="h-alert-body">
                        <h5>Hareket Kaydı (Kayıt Defteri)</h5>
                        <p>Sisteme bir dosya yüklenmesi, hatalı faturaya onay verilmesi veya yetki değiştirilmesi gibi işlemler, işlemi yapan kullanıcının ismi ve saati ile birlikte bir deftere kayıt olarak düşer. Sistemde silinmiş ve geri alınamayan işlemler dahi bu eylemler güvenlik kayıtlarından takip edilebilir.</p>
                    </div>
                </div>
            </div>
        </div>

                <!-- Content Box: Hata Kodları -->
        <div class="h-content-area" id="cnt-hata">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-bug"></i> Sık Karşılaşılan Uyarılar ve Çözümleri</div>
                <p class="h-p">Dosya yükleme esnasında çıkabilecek bazı uyarı mesajlarının sebepleri ve kullanıcı olarak neler yapmanız gerektiği aşağıda sıralanmıştır.</p>
                
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse: collapse; margin-top: 1rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid var(--border); text-align: left;">
                                <th style="padding: 1rem; font-size:0.85rem; color: var(--text);">Uyarı Kodu</th>
                                <th style="padding: 1rem; font-size:0.85rem; color: var(--text);">Açıklama</th>
                                <th style="padding: 1rem; font-size:0.85rem; color: var(--text);">Çözüm Önerisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem;"><span class="badge-st badge-err"><i class="fas fa-times-circle"></i> ERR-101</span></td>
                                <td style="padding: 1rem; font-size:0.85rem; font-weight:600; color:var(--text);">Dosya düzeni (Excel formatı) geçersiz.</td>
                                <td style="padding: 1rem; font-size:0.85rem; color:var(--text2); line-height:1.5;"><strong>Neden?</strong> <br>Dosyanın içinde olması gereken "Fatura No", "Tesisat" gibi temel başlıklarla oynanmış, silinmiş ya da sistemin tanıyamayacağı apayrı bir sütun eklenmiş.<br><br><strong>Çözüm:</strong> Dağıtım firmasından size ilk gönderilen orijinal Excel dosyasını hiç değiştirmeden yükleyin.</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem;"><span class="badge-st badge-err"><i class="fas fa-copy"></i> ERR-105</span></td>
                                <td style="padding: 1rem; font-size:0.85rem; font-weight:600; color:var(--text);">Çift (Mükerrer) Dosya Uyarısı.</td>
                                <td style="padding: 1rem; font-size:0.85rem; color:var(--text2); line-height:1.5;">Bu fatura dosyası daha önce sisteme kaydedilmiş. Şayet ismi değiştiyse bile sistem bunu içeriğinden hemen anlar ve engeller. Bekleme havuzundaki faturaları gözden geçirebilirsiniz.</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem;"><span class="badge-st badge-warn"><i class="fas fa-exclamation-triangle"></i> WRN-201</span></td>
                                <td style="padding: 1rem; font-size:0.85rem; font-weight:600; color:var(--text);">Dosyada çok fazla hatalı veya cezalı fatura var.</td>
                                <td style="padding: 1rem; font-size:0.85rem; color:var(--text2); line-height:1.5;">Faturalar yüklendi ama listenin çok büyük bir kısmında "Ceza" veya "Aşırı Tüketim" problemi görüldü. Yöneticinin "Bekleme Havuzu" ekranından bu duruma göz atıp gerekli tedbirleri alması önerilir.</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem;"><span class="badge-st badge-err"><i class="fas fa-server"></i> ERR-500</span></td>
                                <td style="padding: 1rem; font-size:0.85rem; font-weight:600; color:var(--text);">Sistem geçici olarak yanıt vermedi.</td>
                                <td style="padding: 1rem; font-size:0.85rem; color:var(--text2); line-height:1.5;">Bağlantı koptuğu veya geçici yoğunluk olduğu için işlem yarıda kalmış olabilir. İnternetinizi kontrol edip sayfayı yenileyin. Sorun tekrar ederse menüden "Destek Talebi" bildirebilirsiniz.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

                <!-- Content Box: SSS -->
        <div class="h-content-area" id="cnt-sss">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-question-circle"></i> Sıkça Sorulan Sorular (S.S.S.)</div>
                <p class="h-p">Sistem kullanımı ve yaşanabilecek örneklere ilişkin en çok merak edilen konular.</p>

                <div class="faq-container">
                    <div class="faq-item">
                        <div class="faq-title" onclick="toggleFaq(this)"><span>Sisteme neden sütun düzenini benim değiştirdiğim bir faturayı yükleyemiyorum?</span> <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-content">Sistemimiz, faturaların en ufak hesabında dahi hata çıkmaması için özel ayarlanmış kurallarla çalışır. Beklediği sütun başlıklarını göremezse veya formülü karıştıran farklı hücrelerle karşılaşırsa, hatalı tespitlere mahal vermemek adına okuma işlemine başlamaz. <strong>Çözüm:</strong> Size resmi yollardan gelen ham fatura dosyasının şablonuyla hiç oynamadan direkt yüklemek her zaman en doğru yoldur.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-title" onclick="toggleFaq(this)"><span>Ana sisteme yanlışlıkla onayladığım bir faturayı geri alabilir miyim?</span> <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-content">Bekleme Havuzundan (Ön İnceleme) bir faturaya "Onayla" dediğiniz an o fatura artık kalıcı ana listeye işler. Normal personellerin silme yetkileri sistemde kapalıdır. İşlemin kime ait olduğu kayıt defterinden görülebilir ve düzeltme yapılması için yetkililerin durumu inceleyerek "Teknik İletişim Formu"ndan destek istemesi gerekir.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-title" onclick="toggleFaq(this)"><span>Reaktif Ceza veya Aşırı Tüketim bulunan faturada süreç tam olarak nasıl işler?</span> <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-content">Sistem, faturada "Ceza" veya çok uçuk meblağlı bir tüketim sezdiğinde faturayı Kırmızı Alarm uyarısı ile ekrana getirir. Onay vermeden duruma el koymalısınız. Kurumun saha ve elektrik ekiplerine durum bildirilerek, teknik ekipman (kompanzasyon, pano vs.) kaynaklı olan bu faturayı önlem almak veya resmi itiraz süreçleriyle değerlendirmek adına erteleyebilirsiniz.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-title" onclick="toggleFaq(this)"><span>Yeni abone kuruma eklendiyse fakat bizim listemizde ekli değilse ne yapacağım?</span> <i class="fas fa-chevron-down"></i></div>
                        <div class="faq-content">Bunun için kaygılanmanıza gerek yoktur, manuel işlem yapmanız istenmez. Excel listesinde sizin takip listelerinizde olmayan hiç görülmemiş bir Tesisat Numarası varsa; Akıllı sistem o satırı bir abone gibi görür, onu sizin için yeni abone klasörüne ayırır. Gelecekte de o aboneye ait abonelik bilgilerini otomatik yolla sistemde kayda geçirir.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Box: Destek -->
        <div class="h-content-area" id="cnt-destek">
            <div class="h-card">
                <div class="h-card-title"><i class="fas fa-headset"></i> Teknik Destek & İletişim Talebi</div>
                <p class="h-p">Uygulamada yaşadığınız beklenmeyen problemler, tasarımsal hatalar veya acil veritabanı müdahaleleri için teknik merkeze doğrudan destek bileti gönderebilirsiniz.</p>
                
                <div class="support-form" style="background:#f8fafc; padding:1.5rem; border-radius:12px; border:1px solid var(--border);">
                    <form id="supportTicketForm">
                        @csrf
                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem;">
                            <div>
                                <label style="font-size:0.85rem; font-weight:700; color:var(--text); margin-bottom:0.5rem; display:block;">Sorun Kategorisi</label>
                                <select name="kategori" class="form-control" required>
                                    <option value="">Seçiniz...</option>
                                    <option value="Excel Yükleme Sorunu">Excel Yükleme ve Timeout Sorunları</option>
                                    <option value="Anomali Mantık Hatası">Anomali Hesaplanması Mantık Hatası</option>
                                    <option value="Erişim ve Yetki">Erişim, Şifre ve Yetki İşlemleri</option>
                                    <option value="Tasarım Hatası">Sayfa Tasarımı ve Tablo Kaymaları</option>
                                    <option value="Diğer">Diğer Talep ve Öneriler</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size:0.85rem; font-weight:700; color:var(--text); margin-bottom:0.5rem; display:block;">Öncelik Derecesi</label>
                                <select name="oncelik" class="form-control" required>
                                    <option value="Düşük">Düşük Seviye (Rutin İşleyişi Etkilemiyor)</option>
                                    <option value="Orta" selected>Orta Seviye (Genel Süreci Yavaşlatıyor)</option>
                                    <option value="Yüksek">Yüksek Seviye (Kritik: İşlemler Durdu/Sistem Hata Veriyor)</option>
                                </select>
                            </div>
                        </div>
                        <div style="margin-top:0.5rem;">
                            <label style="font-size:0.85rem; font-weight:700; color:var(--text); margin-bottom:0.5rem; display:block;">Karşılaştığınız Durumu Detaylandırın</label>
                            <textarea name="mesaj" class="form-control" rows="4" placeholder="Oluşan hatayı, saat dilimini ve aldığınız uyarı mesajını detaylıca yazarsanız çözüm süreci hızlanacaktır." required></textarea>
                        </div>
                        <div style="text-align: right; margin-top: 0.5rem;">
                            <button type="submit" class="support-btn" id="submitSupportBtn"><i class="fas fa-paper-plane"></i> Teknik Destek Talebini Gönder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function switchH(id) {
    // Nav butonlarındaki active classını çıkar
    document.querySelectorAll('.h-nav-item').forEach(e => e.classList.remove('active'));
    // İlgili butona active ekle
    document.getElementById('btn-' + id).classList.add('active');
    
    // Tüm içerik alanlarını gizle
    document.querySelectorAll('.h-content-area').forEach(e => {
        e.classList.remove('active');
    });
    // İstediğimiz alanı göster
    document.getElementById('cnt-' + id).classList.add('active');
}

document.getElementById('supportTicketForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitSupportBtn');
    const oldHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';

    const formData = new FormData(this);

    fetch('{{ route("support.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Talebiniz Alındı',
                text: 'Destek talebiniz başarıyla kaydedildi. En kısa sürede admin panelinden incelenecektir.',
                confirmButtonText: 'Tamam'
            });
            this.reset();
        } else {
            Swal.fire('Hata!', 'Talep gönderilirken bir sorun oluştu.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Hata!', 'Sunucuyla iletişim kurulamadı.', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = oldHtml;
    });
});

function toggleFaq(el) {
    const parent = el.parentElement;
    const isActive = parent.classList.contains('active');
    // Diğer tüm SSS öğelerini kapat
    document.querySelectorAll('.faq-item').forEach(item => item.classList.remove('active'));
    // Eğer tıklanılan öğe kapalıysa, aç
    if(!isActive) {
        parent.classList.add('active');
    }
}
</script>
@endsection
