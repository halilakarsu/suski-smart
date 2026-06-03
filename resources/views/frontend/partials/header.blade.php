<!DOCTYPE html>
<html lang="tr">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>SMART ŞUSKİ PROJESİ</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="icon" href="/frontend/assets/img/icon.ico" type="image/x-icon" />

	<!-- Fonts and icons -->
	<script src="/frontend/assets/js/plugin/webfont/webfont.min.js"></script>

	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="/frontend/assets/css/fonts.min.css">

	<!-- CSS Files -->
	<link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/frontend/assets/css/atlantis.min.css">

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="/frontend/assets/css/demo.css">
	<link rel="stylesheet" href="/frontend/assets/css/custom-styles.css">

	<!-- AlertifyJS -->
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />
	<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

	<!-- SweetAlert2 -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
	<div class="wrapper">
		<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" style="background: #0f172a !important; border-bottom: 1px solid rgba(255,255,255,0.05);">

				<a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
					<div class="d-flex align-items-center justify-content-center"
						style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); width: 32px; height: 32px; border-radius: 8px; margin-right: 12px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);">
						<i class="fas fa-tint text-white" style="font-size: 16px;"></i>
					</div>
					<span class="text-white fw-bold"
						style="font-size: 17px; letter-spacing: 0.8px; font-family: 'Inter', sans-serif;">
						SMART <span style="color: #60a5fa;">ŞUSKİ</span>
					</span>
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
					data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="icon-menu text-white"></i>
					</span>
				</button>
				<button class="topbar-toggler more"><i class="icon-options-vertical text-white"></i></button>
				<div class="nav-toggle">
					<button class="btn btn-toggle toggle-sidebar">
						<i class="icon-menu text-white"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->

			<!-- Navbar Header -->
			<nav class="navbar navbar-header navbar-expand-lg" style="background: #0f172a !important; border-bottom: 1px solid rgba(255,255,255,0.05);">

				<div class="container-fluid">
					<div class="collapse" id="search-nav">
						<form class="navbar-left navbar-form nav-search mr-md-3" onsubmit="return false;">
							<div class="input-group" style="background: rgba(255,255,255,0.05); border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); padding: 2px 10px;">
								<div class="input-group-prepend">
									<button type="submit" class="btn btn-search pr-1">
										<i class="fa fa-search search-icon" style="color: rgba(255,255,255,0.4);"></i>
									</button>
								</div>
								<input type="text" id="global-search-input"
									placeholder="Hızlı arama..." class="form-control"
									style="background: transparent; border: none; font-size: 13px; color: #fff;"
									autocomplete="off">
								<div id="search-results-dropdown" class="dropdown-menu animated fadeIn"
									style="width: 100%; top: 120%; border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.3); background: #1e293b;">
									<!-- Sonuçlar JS ile buraya gelecek -->
								</div>
							</div>
						</form>
					</div>
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item dropdown hidden-caret">
                            @php
                                $unreadCount = 0;
                                if(auth()->user()->role == 'admin') {
                                    // Admin: Yeni biletler + Kullanıcılardan gelen unread reply'lar
                                    $newTickets = \App\Models\SupportMessage::where('durum', 'yeni')->count();
                                    $unreadReplies = \App\Models\SupportReply::where('is_read', false)
                                        ->whereHas('user', function($q) { $q->where('role', '!=', 'admin'); })
                                        ->count();
                                    $unreadCount = $newTickets + $unreadReplies;
                                } else {
                                    // User: Kendi biletlerine admin'den gelen unread reply'lar
                                    $unreadCount = \App\Models\SupportReply::where('is_read', false)
                                        ->whereHas('user', function($q) { $q->where('role', 'admin'); })
                                        ->whereHas('supportMessage', function($q) { $q->where('user_id', auth()->id()); })
                                        ->count();
                                }
                            @endphp
							<a class="nav-link dropdown-toggle" href="{{ auth()->user()->role == 'admin' ? route('admin.support.index') : route('support.user.index') }}" role="button">
								<i class="fa fa-bell text-white"></i>
                                @if($unreadCount > 0)
								    <span class="notification" style="background: #ef4444; color: #fff; border: 2px solid #0f172a; font-size: 9px; min-width: 16px; height: 16px; border-radius: 10px; display: flex; align-items: center; justify-content: center; position: absolute; top: 10px; right: 5px; font-weight: 800;">{{ $unreadCount }}</span>
                                @endif
							</a>
						</li>

                        {{-- Bildirim veya Profil alanı --}}
						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"
								aria-expanded="false" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
								<div class="user-info d-none d-md-block text-right">
                                    <div class="text-white fw-bold" style="font-size: 13px; line-height: 1;">{{ Auth::user()->name ?? 'Kullanıcı' }}</div>
                                    <div style="font-size: 11px; color: rgba(255,255,255,0.5);">{{ auth()->user()->role == 'admin' ? 'Yönetici' : 'Personel' }}</div>
                                </div>
                                <div class="avatar-sm">
									<div
										class="avatar-img rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold" style="border: 2px solid rgba(255,255,255,0.1); width: 35px; height: 35px;">
										{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
									</div>
								</div>
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
								<div class="dropdown-user-scroll scrollbar-outer">
									<li>
										<div class="user-box p-3">
											<div class="avatar-lg bg-primary d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
												style="font-size: 24px; width: 60px; height: 60px; margin: 0 auto 10px;">
												{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
											</div>
											<div class="u-text text-center">
												<h4 style="font-weight: 700;">{{ Auth::user()->name ?? 'Kullanıcı' }}</h4>
												<p class="text-muted" style="font-size: 12px;">{{ Auth::user()->email ?? '' }}</p>
											</div>
										</div>
									</li>
									<li>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-cog mr-2 text-primary"></i> Profil Ayarları
                                        </a>
										<div class="dropdown-divider"></div>

										<form method="POST" action="{{ route('logout') }}">
											@csrf
											<a class="dropdown-item text-danger py-2" href="#"
												onclick="event.preventDefault(); let form = this.closest('form'); alertify.confirm('Güvenli Çıkış', 'Hesabınızdan güvenli şekilde çıkış yapmak istediğinize emin misiniz?', function(){ form.submit(); }, function(){}).set('labels', {ok:'Evet, Çıkış Yap', cancel:'Vazgeç'});">
												<i class="fas fa-sign-out-alt mr-2"></i> {{ __('Çıkış Yap') }}
											</a>
										</form>
									</li>
								</div>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
			<!-- End Navbar -->
		</div>
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('global-search-input');
    const searchDropdown = document.getElementById('search-results-dropdown');
    
    if(!searchInput || !searchDropdown) return;

    // Sistemdeki taranabilir modüllerin özel zeki eşleşme (keyword) haritası
    const routes = [
        { title: "Kumanda Merkezi", link: "{{ route('dashboard') }}", icon: "fas fa-home", keywords: "anasayfa ana sayfa dashboard panel ekran giriş" },
        { title: "Yeni Fatura Excel İçe Aktar", link: "{{ route('import.index') }}", icon: "fas fa-file-excel", keywords: "fatura ekle yükle içe aktar excel import aktarım" },
        { title: "Onay Bekleyen Faturalar", link: "{{ route('staging.index') }}", icon: "fas fa-layer-group", keywords: "fatura bekleme havuz onay bekleyenler red itiraz kontrol liste" },
        { title: "Geçmiş Yükleme Logları", link: "{{ route('import.logs') }}", icon: "fas fa-history", keywords: "yükleme geçmiş import log liste" },
        { title: "Aboneler Tablosu", link: "{{ route('aboneler.index') }}", icon: "fas fa-users", keywords: "abone aboneler müşteri liste tesisat acomp" },
        { title: "Yeni Abone Kayıt", link: "{{ route('aboneler.create') }}", icon: "fas fa-user-plus", keywords: "yeni abone kayıt ekle oluştur müşteri" },
        { title: "Bölgeler Listesi", link: "{{ route('bolgeler.index') }}", icon: "fas fa-map-marker-alt", keywords: "bölge mahalle sokak ilçe listesi" },
        { title: "Personeller (Kullanıcılar)", link: "{{ route('users.index') }}", icon: "fas fa-user-shield", keywords: "kullanıcılar personel ekibi admin roller" },
        { title: "Yeni Personel Ekle", link: "{{ route('users.create') }}", icon: "fas fa-user-plus", keywords: "yeni kullanıcı personel ekle yetki" },
        { title: "Sistem İzleme Logları", link: "{{ route('activity-logs.index') }}", icon: "fas fa-list-alt", keywords: "aktivite faaliyet loglar geçmiş izleme sistem" },
        { title: "Kişisel Profil Ayarları", link: "{{ route('profile.edit') }}", icon: "fas fa-user-cog", keywords: "profil şifre ayar hesap güvenliği" }
    ];

    searchInput.addEventListener('input', function() {
        const val = this.value.toLowerCase().trim();
        searchDropdown.innerHTML = '';
        
        if (val.length < 2) {
            searchDropdown.style.display = 'none';
            return;
        }

        const exactMatches = [];
        const partialMatches = [];

        routes.forEach(route => {
            const titleMatch = route.title.toLowerCase().includes(val);
            const keywordMatch = route.keywords.includes(val);

            if (titleMatch || keywordMatch) {
                if (titleMatch) {
                    exactMatches.push(route);
                } else {
                    partialMatches.push(route);
                }
            }
        });

        const results = [...exactMatches, ...partialMatches];

        if (results.length > 0) {
            results.forEach((route, index) => {
                const a = document.createElement('a');
                a.className = "dropdown-item d-flex align-items-center py-2";
                a.href = route.link;
                // Sona border eklememek için kontrol
                let borderStyle = (index !== results.length - 1) ? 'border-bottom: 1px solid rgba(255,255,255,0.05);' : '';
                
                a.style = `color: #e2e8f0; font-size: 13px; font-weight: 500; transition: all 0.2s; ${borderStyle} gap: 10px; cursor: pointer;`;
                a.innerHTML = `<div style="width:28px; height:28px; border-radius:6px; background:rgba(59,130,246,0.1); display:flex; align-items:center; justify-content:center; color:#60a5fa;"><i class="${route.icon}"></i></div> ${route.title}`;
                
                // Hover Effects
                a.addEventListener('mouseenter', () => {
                    a.style.background = 'rgba(255,255,255,0.08)';
                    a.style.color = '#fff';
                    a.style.borderRadius = '8px';
                    a.querySelector('div').style.background = 'rgba(59,130,246,0.2)';
                });
                a.addEventListener('mouseleave', () => {
                    a.style.background = 'transparent';
                    a.style.color = '#e2e8f0';
                    a.querySelector('div').style.background = 'rgba(59,130,246,0.1)';
                });

                searchDropdown.appendChild(a);
            });
            searchDropdown.style.display = 'block';
        } else {
            const div = document.createElement('div');
            div.className = "dropdown-item text-center py-3";
            div.style = "color: rgba(255,255,255,0.4); font-size: 12px; pointer-events: none;";
            div.innerHTML = `<i class="fas fa-search mb-2" style="font-size:16px;"></i><br>Kayıt Bulunamadı`;
            searchDropdown.appendChild(div);
            searchDropdown.style.display = 'block';
        }
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            searchDropdown.style.display = 'block';
        }
    });
});
</script>
@endpush