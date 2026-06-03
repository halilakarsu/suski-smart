@extends('frontend.layouts.app')

@php
  $displayColumns = [
    'ilce'                                 => 'İlçe / Kurum',
    'tesisat_no'                           => 'Tesisat No',
    'fatura_no'                            => 'Fatura No',
    'fatura_edilecek_toplam_tuketim_kwh'   => 'Tüketim (kWh)',
    'genel_toplam'                         => 'Ödenecek Tutar',
  ];

  $detayKategoriler = [
    'Kimlik & Konum' => [
      'sira_no' => 'Sıra No', 'tesisat_no' => 'Tesisat No', 'fatura_no' => 'Fatura No', 'pmum_id' => 'PMUM ID',
      'sayac_seri_no' => 'Sayaç Seri No', 'carpan' => 'Çarpan', 'dagitim' => 'Bölge Adı', 'ilce' => 'İlçe',
      'ilce_kodu' => 'İlçe Kodu', 'adres' => 'Adres', 'baglanti_grubu' => 'Bağlantı Grubu', 'serbest_tuketici' => 'Serbest Tüketici',
      'tarife' => 'Tarife', 'tarife_2' => 'Tarife 2',
    ],
    'Okuma & Endeks' => [
      'ilk_okuma' => 'İlk Okuma Tarihi', 'son_okuma' => 'Son Okuma Tarihi', 't1_ilk_endeks' => 'T1 İlk Endeks',
      't1_son_endeks' => 'T1 Son Endeks', 't2_ilk_endeks' => 'T2 İlk Endeks', 't2_son_endeks' => 'T2 Son Endeks',
      't3_ilk_endeks' => 'T3 İlk Endeks', 't3_son_endeks' => 'T3 Son Endeks', 't0_ilk_endeks' => 'T0 İlk Endeks',
      'to_son_endeks' => 'T0 Son Endeks', 'ri_ilk_endeks' => 'Ri İlk Endeks', 'ri_son_endeks' => 'Ri Son Endeks',
      'ri_fark_endeks' => 'Ri Fark Endeks', 'rc_ilk_endeks' => 'Rc İlk Endeks', 'rc_son_endeks' => 'Rc Son Endeks',
      'rc_fark_endeks' => 'Rc Fark Endeks',
    ],
    'Tüketim (kWh)' => [
      't1_tuketim' => 'T1 Tüketim', 't2_tuketim' => 'T2 Tüketim', 't3_tuketim' => 'T3 Tüketim',
      'ek_tuketim' => 'Ek Tüketim', 'trafo_kaybi_kwh' => 'Trafo Kaybı kWh', 'yillik_tuketim' => 'Yıllık Tüketim',
      'fatura_edilecek_toplam_tuketim_kwh' => 'Fat. Edilecek Toplam Tük. kWh', 'gunluk_ortalama_tuketim' => 'Günlük Ort. Tüketim',
    ],
    'Fiyat & Bedel' => [
      'birim_fiyat' => 'Birim Fiyat', 'dagitim_birim_fiyat' => 'Dağıtım Birim Fiyat', 'aktif_tuketim_tl' => 'Aktif Tüketim TL',
      'dagitim_bedeli' => 'Dağıtım Bedeli', 'dagitim_bedeli_ek' => 'Dağıtım Bedeli Ek', 'enerji_fonu' => 'Enerji Fonu',
      'reaktif_tl' => 'Reaktif TL', 'acma_kapama_bedeli' => 'Açma/Kapama Bedeli', 'gecikme_tutari' => 'Gecikme Tutarı',
      'trt_fonu' => 'TRT Fonu', 'btv' => 'BTV', 'btv_orani' => 'BTV Oranı', 'fatura_tutari' => 'Fatura Tutarı',
      'fatura_tutari_ek' => 'Fatura Tutarı Ek', 'kdv' => 'KDV', 'genel_toplam' => 'Genel Toplam', 'tutar_toplam' => 'Tutar Toplam',
    ],
  ];
@endphp

@section('content')
<style>
    /* Ultra-Premium Glassmorphic Staging CSS - MATCHING DASHBOARD */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --primary-gradient: linear-gradient(135deg, #2563eb, #4f46e5);
        --bg-main: #f4f6f9;
        --card-bg: rgba(255, 255, 255, 0.95);
        --surface-glass: rgba(255, 255, 255, 0.85);
        --text-slate-900: #0f172a;
        --text-slate-500: #64748b;
        --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
        --shadow-glow: 0 0 25px rgba(37, 99, 235, 0.3);
    }

    .content {
        background-color: var(--bg-main) !important;
        min-height: 100vh;
    }

    /* Hero Area */
    .dashboard-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative; padding: 5rem 2rem 10rem 2rem; margin-top: -20px;
        color: #fff; overflow: hidden; border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .dashboard-hero::before {
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.4) 0%, transparent 70%);
        top: -200px; left: -100px; border-radius: 50%; opacity: 0.6; filter: blur(40px);
        animation: pulseSlow 8s infinite alternate; pointer-events: none; z-index: 1;
    }

    .dashboard-hero::after {
        content: ''; position: absolute; width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
        bottom: -100px; right: 5%; border-radius: 50%; opacity: 0.6; filter: blur(50px);
        animation: pulseDelay 10s infinite alternate; pointer-events: none; z-index: 1;
    }

    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.5; } 100% { transform: scale(1.2); opacity: 0.9; } }
    @keyframes pulseDelay { 0% { transform: scale(1) translate(0, 0); } 100% { transform: scale(1.3) translate(-20px, -20px); } }

    .hero-content {
        position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;
    }

    .hero-title {
        font-family: var(--font-primary); font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; letter-spacing: -0.04em;
        margin-bottom: 0.5rem; background: linear-gradient(to right, #ffffff, #93c5fd);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }

    /* Layout Wrapper */
    .dashboard-container {
        width: 100%; max-width: 1500px; margin: -6rem auto 0 auto;
        padding: 0 2rem; position: relative; z-index: 20;
    }

    /* State Cards Grid */
    .stats-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 40px;
    }

    @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--surface-glass); backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.6); border-radius: 20px;
        padding: 20px; box-shadow: var(--shadow-elevated);
        position: relative; overflow: hidden; transition: all 0.4s;
        display: flex !important; flex-direction: column; text-decoration: none !important;
        min-height: 130px; cursor: pointer;
    }

    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.12); border-color: white; }
    .stat-card.active { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2); }
    .stat-card::before {
        content: ''; position: absolute; right: -15%; top: -15%; width: 100px; height: 100px;
        border-radius: 50%; opacity: 0.12; filter: blur(20px); transition: all 0.5s;
    }

    .stat-c1::before { background: #3b82f6; }
    .stat-c5::before { background: #ef4444; }
    .stat-c2::before { background: #64748b; }

    .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; z-index: 2; }
    .stat-title { font-size: 0.75rem; font-weight: 700; color: var(--text-slate-500); text-transform: uppercase; letter-spacing: 0.5px; }

    .stat-icon {
        width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: white; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .ic1 { background: linear-gradient(135deg, #60a5fa, #2563eb); }
    .ic5 { background: linear-gradient(135deg, #f87171, #ef4444); }
    .ic2 { background: linear-gradient(135deg, #94a3b8, #64748b); }

    .stat-value { font-size: 1.9rem; font-weight: 800; color: var(--text-slate-900); z-index: 2; margin-top: auto; line-height: 1; }

    /* Widgets */
    .premium-widget { background: var(--card-bg); border-radius: 28px; padding: 30px; box-shadow: var(--shadow-elevated); border: 1px solid rgba(226, 232, 240, 0.6); margin-bottom: 30px; }
    
    /* Table */
    .pro-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .pro-table th { color: #94a3b8; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; padding: 0 20px 10px 20px; text-align: left; }
    .pro-table td { padding: 18px 20px; background: #f8fafc; transition: all 0.2s; font-size: 0.85rem; font-weight: 600; }
    .pro-table tr td:first-child { border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
    .pro-table tr td:last-child { border-top-right-radius: 16px; border-bottom-right-radius: 16px; text-align: right; }
    .pro-table tr:hover td { background: #eff6ff; }

    /* Buttons */
    .btn-premium {
        background: var(--primary-gradient); color: white !important; border: none; padding: 10px 24px; border-radius: 12px;
        font-weight: 800; font-size: 0.9rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4); }
    
    .action-btn { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; background: white; border: 1px solid #e2e8f0; color: #64748b; transition: all 0.2s; }
    .action-btn:hover { background: #3b82f6; color: white; border-color: #3b82f6; }
    
    .p-input { background: #f1f5f9; border: 1.5px solid transparent; border-radius: 10px; padding: 8px 16px; font-size: 0.85rem; font-weight: 700; outline: none; transition: all 0.2s; }
    .p-input:focus { background: white; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
</style>

<div class="content p-0">
    <!-- HERO -->
    <div class="dashboard-hero">
        <div class="hero-content">
            <div>
                <div class="mb-3">
                    <span style="background: rgba(255,255,255,0.1); padding: 6px 16px; border-radius: 100px; font-size: 0.8rem; font-weight: 600; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); letter-spacing: 1px; color:#a5b4fc;">
                        <i class="fas fa-layer-group text-warning mr-1"></i> FATURA HAVUZU
                    </span>
                </div>
                <h1 class="hero-title">Bekleme Havuzu</h1>
                <p class="hero-subtitle">Yüklenen verileri kontrol edin ve sisteme kesinleştirin.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('import.index') }}" class="btn btn-outline-light px-4 py-2" style="border-radius: 12px; font-weight: 700; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-upload mr-2"></i> Yeni Yükleme
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="dashboard-container">
        
        <!-- TABS / STATS -->
        <div class="stats-grid">
            <a href="{{ route('staging.index', array_merge(request()->query(), ['tab' => 'bekleyen'])) }}" class="stat-card stat-c1 {{ $tab === 'bekleyen' ? 'active' : '' }}">
                <div class="stat-header">
                    <div class="stat-title">Bekleyen Faturalar</div>
                    <div class="stat-icon ic1"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-value">{{ number_format($sayaclar['bekleyen'] ?? 0) }}</div>
                <div class="stat-desc">Kontrol aşamasında</div>
            </a>

            <a href="{{ route('staging.index', array_merge(request()->query(), ['tab' => 'reaktif'])) }}" class="stat-card stat-c5 {{ $tab === 'reaktif' ? 'active' : '' }}">
                <div class="stat-header">
                    <div class="stat-title">Reaktif Cezalılar</div>
                    <div class="stat-icon ic5"><i class="fas fa-bolt"></i></div>
                </div>
                <div class="stat-value">{{ number_format($sayaclar['reaktif'] ?? 0) }}</div>
                <div class="stat-desc">Dikkat gerektirenler</div>
            </a>

            <a href="{{ route('staging.index', array_merge(request()->query(), ['tab' => 'mukerrer'])) }}" class="stat-card stat-c2 {{ $tab === 'mukerrer' ? 'active' : '' }}">
                <div class="stat-header">
                    <div class="stat-title">Mükerrer Kayıtlar</div>
                    <div class="stat-icon ic2"><i class="fas fa-copy"></i></div>
                </div>
                <div class="stat-value">{{ number_format($sayaclar['mukerrer'] ?? 0) }}</div>
                <div class="stat-desc">Sistemde eşleşenler</div>
            </a>
        </div>

        <!-- FILTERS -->
        <div class="premium-widget py-3 mb-4">
            <form method="GET" action="{{ route('staging.index') }}" class="row align-items-center g-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="col-auto">
                    <select name="donem" class="p-input">
                        <option value="">🗓️ Tüm Dönemler</option>
                        @foreach($donemler as $d)
                            <option value="{{ $d }}" {{ request('donem') == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto"><input type="text" name="ilce" value="{{ request('ilce') }}" placeholder="🏢 İlçe / Kurum" class="p-input"></div>
                <div class="col-auto"><input type="text" name="tesisat_no" value="{{ request('tesisat_no') }}" placeholder="🔍 Tesisat No" class="p-input"></div>
                <div class="col-auto"><input type="text" name="fatura_no" value="{{ request('fatura_no') }}" placeholder="🧾 Fatura No" class="p-input"></div>
                <div class="col-auto">
                    <button type="submit" class="btn-premium px-3"><i class="fas fa-filter"></i> Filtrele</button>
                    @if(request()->hasAny(['donem', 'ilce', 'tesisat_no', 'fatura_no']))
                        <a href="{{ route('staging.index', ['tab' => $tab]) }}" class="btn btn-light ml-1" style="border-radius: 10px; padding: 9px 15px;"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <!-- LIST -->
        <div class="premium-widget">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-table text-primary"></i> 
                    @if($tab === 'reaktif') Reaktif Listesi @elseif($tab === 'mukerrer') Mükerrer Listesi @else Fatura Listesi @endif
                </h3>
                <div class="d-flex gap-2">
                    @if($tab === 'reaktif')
                        <button class="btn btn-danger font-weight-bold" style="border-radius:12px;" onclick="sendToReaktifler()"><i class="fas fa-bolt mr-2"></i> Reaktifleri Arşive Gönder</button>
                    @elseif($tab === 'mukerrer')
                        <button class="btn btn-outline-danger font-weight-bold" style="border-radius:12px;" onclick="deleteSelected()"><i class="fas fa-trash mr-2"></i> Seçilenleri Sil</button>
                    @else
                        <button class="btn btn-success font-weight-bold px-4" style="border-radius:12px; background:#10b981; border:none;" id="sendPaymentBtn"><i class="fas fa-check-double mr-2"></i> Ödemeye Gönder</button>
                    @endif
                </div>
            </div>

            <div id="selectAllBanner" style="display:none; background: #eff6ff; border: 1px dashed #3b82f6; border-radius: 12px; padding: 12px 20px; margin-bottom: 20px; text-align: center; font-weight: 700; color: #1e40af;">
                Bu sayfadaki tüm kayıtlar seçildi. 
                <a href="javascript:void(0)" onclick="selectAllInTab()" style="color: #2563eb; text-decoration: underline; margin-left: 10px;">Sekmedeki TÜM (<span class="tab-total-count">{{ $sayaclar[$tab] ?? 0 }}</span>) faturaları seç</a>
            </div>

            <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
                <table class="pro-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle font-weight-bold" type="button" data-toggle="dropdown" style="border-radius: 8px; font-size: 0.7rem; background: white; border: 1px solid #e2e8f0;">
                                        SEÇ
                                    </button>
                                    <div class="dropdown-menu shadow-lg border-0" style="border-radius: 16px;">
                                        <a class="dropdown-item py-2 font-weight-bold" href="javascript:void(0)" onclick="selectAllInTab()"><i class="fas fa-check-double mr-2 text-primary"></i> Tümünü Seç</a>
                                        <a class="dropdown-item py-2 font-weight-bold" href="javascript:void(0)" onclick="selectCurrentPage()"><i class="fas fa-check mr-2 text-success"></i> Sayfayı Seç</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item py-2 font-weight-bold text-danger" href="javascript:void(0)" onclick="clearSelection()"><i class="fas fa-times mr-2"></i> Temizle</a>
                                    </div>
                                </div>
                            </th>
                            @foreach($displayColumns as $k => $lbl)<th>{{ $lbl }}</th>@endforeach
                            <th style="text-align: right;">İŞLEM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stagingler as $item)
                            <tr data-id="{{ $item->id }}">
                                <td><input type="checkbox" class="rc" value="{{ $item->id }}" style="width:18px; height:18px; cursor:pointer;"></td>
                                @foreach($displayColumns as $k => $lbl)
                                    <td>
                                        @php
                                            $displayVal = $item->$k ?? null;
                                        @endphp
                                        <div style="font-weight:{{ in_array($k, ['tesisat_no', 'fatura_no', 'genel_toplam']) ? '800' : '600' }}; color:{{ $k === 'genel_toplam' ? '#059669' : 'inherit' }}">
                                            @if($k === 'genel_toplam')
                                                ₺{{ number_format((float)$displayVal, 2, ',', '.') }}
                                            @elseif($k === 'fatura_edilecek_toplam_tuketim_kwh')
                                                @php
                                                    $num = (float)$displayVal;
                                                    $formatted = $num == floor($num) ? number_format($num, 0, ',', '.') : rtrim(rtrim(number_format($num, 3, ',', '.'), '0'), ',');
                                                @endphp
                                                {{ $formatted }}
                                            @else
                                                {{ $displayVal ?? '–' }}
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button onclick="showDetail({{ $item->id }})" class="action-btn" title="Gözat"><i class="fas fa-eye"></i></button>
                                        <button onclick="deleteRow({{ $item->id }})" class="action-btn text-danger" title="Sil"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/slate/empty-folder.svg" style="width: 150px; opacity: 0.5; margin-bottom: 20px;">
                                    <p style="font-weight: 700; color: #94a3b8;">Havuzda kayıt bulunamadı.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($stagingler->hasPages())
                <div class="d-flex justify-content-end mt-4">
                    {{ $stagingler->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Premium Detail Modal -->
<div class="modal fade" id="detayModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 32px; overflow: hidden;">
            <div class="modal-header px-4 py-4 border-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: white;">
                <h5 class="modal-title font-weight-bold" style="display: flex; align-items: center; gap: 12px; font-size: 1.3rem;">
                    <i class="fas fa-search-dollar text-warning"></i> Fatura Detay Analizi
                </h5>
                <button type="button" class="close text-white opacity-1" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0" style="max-height: 70vh; overflow-y: auto;">
                <div id="modalContent">
                    @foreach($detayKategoriler as $kat => $fields)
                        <div style="background: #f8fafc; padding: 12px 30px; font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0;">{{ $kat }}</div>
                        @foreach($fields as $fKey => $fLbl)
                            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom" style="font-size: 0.9rem;">
                                <span style="color: #64748b; font-weight: 700;">{{ $fLbl }}</span>
                                <span style="font-weight: 800; color: #1e293b;" data-field="{{ $fKey }}">...</span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-light">
                <button type="button" class="btn btn-secondary font-weight-bold px-4 py-2" data-dismiss="modal" style="border-radius: 12px;">Pencereyi Kapat</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedIds = new Set();
    let allSelectedInDb = false;

    function selectCurrentPage() {
        allSelectedInDb = false;
        $('.rc').prop('checked', true);
        $('.rc').each(function() { selectedIds.add($(this).val()); });
        if ({{ $stagingler->total() }} > {{ $stagingler->count() }}) $('#selectAllBanner').show();
    }

    $(document).on('change', '.rc', function() {
        if ($(this).is(':checked')) selectedIds.add($(this).val());
        else { selectedIds.delete($(this).val()); allSelectedInDb = false; $('#selectAllBanner').hide(); }
    });

    function selectAllInTab() {
        allSelectedInDb = true;
        $('.rc').prop('checked', true);
        $('.rc').each(function() { selectedIds.add($(this).val()); });
        $('#selectAllBanner').hide();
    }

    function clearSelection() {
        selectedIds.clear(); allSelectedInDb = false;
        $('.rc').prop('checked', false); $('#selectAllBanner').hide();
    }

    function showDetail(id) {
        $('#detayModal').modal('show');
        fetch(`/staging/${id}`).then(r => r.json()).then(data => {
            Object.keys(data).forEach(k => {
                let val = data[k];
                if (val !== null && val !== undefined && val !== '') {
                    if (k.includes('tarih') || k.includes('okuma')) {
                        const d = new Date(val); val = isNaN(d) ? val : d.toLocaleDateString('tr-TR');
                    } else if (k.includes('tutar') || k.includes('tl') || k === 'genel_toplam' || k === 'kdv') {
                        val = '₺' + parseFloat(val).toLocaleString('tr-TR', {minimumFractionDigits: 2});
                    } else if (!isNaN(val) && !['fatura_no', 'tesisat_no'].includes(k)) {
                        val = parseFloat(val).toString().replace('.', ',');
                    }
                }
                $(`[data-field="${k}"]`).text(val ?? '–');
            });
        });
    }

    $('#sendPaymentBtn').on('click', function() {
        const ids = Array.from(selectedIds);
        if (ids.length === 0 && !allSelectedInDb) return Swal.fire('Hata', 'Lütfen kayıt seçin.', 'warning');
        
        Swal.fire({
            title: 'Kesinleştirme Onayı',
            text: allSelectedInDb ? 'Tüm havuz kayıtları kesinleştirilecek.' : ids.length + ' kayıt kesinleştirilecek.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Evet, Kesinleştir',
            confirmButtonColor: '#10b981'
        }).then(result => {
            if (result.isConfirmed) {
                const url = allSelectedInDb ? `/staging/send-all` : `/staging/send-multiple`;
                const params = allSelectedInDb ? {!! json_encode(request()->only(['donem', 'ilce', 'tesisat_no', 'fatura_no'])) !!} : { ids: ids };
                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify(params)
                }).then(r => r.json()).then(data => {
                    if (data.success) Swal.fire('Başarılı', 'Kayıtlar aktarıldı.', 'success').then(() => location.reload());
                });
            }
        });
    });

    function deleteRow(id) {
        if (!confirm('Bu kaydı havuzdan silmek istediğinize emin misiniz?')) return;
        fetch(`/staging/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json()).then(data => { if (data.success) $(`tr[data-id="${id}"]`).fadeOut(); });
    }

    function sendToReaktifler() {
        Swal.fire({ title: 'Emin misiniz?', text: 'Tüm reaktif cezalı faturalar arşive taşınacak.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Evet, Taşı' })
        .then(result => { if (result.isConfirmed) fetch(`/staging/reaktif`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => location.reload()); });
    }
</script>
@endpush
@endsection
