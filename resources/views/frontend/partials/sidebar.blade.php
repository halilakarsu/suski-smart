<!-- Sidebar -->
<div class="sidebar sidebar-style-2">

	<div class="scroll-wrapper sidebar-wrapper scrollbar scrollbar-inner" style="position: relative;">
		<div class="sidebar-wrapper scrollbar scrollbar-inner scroll-content scroll-scrolly_visible" style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 686px;">
			<div class="sidebar-content">
				<!-- User Profile Card -->
				<div class="user-profile-card">
					<div class="user-profile-avatar">
						{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
					</div>
					<div class="user-profile-info">
						<span class="user-profile-name">{{ auth()->user()->name }}</span>
						<span class="user-profile-role">{{ auth()->user()->role == 'admin' ? 'Yönetici' : 'Personel' }}</span>
					</div>
				</div>

				<ul class="nav nav-primary">
					<li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
						<a href="{{ url('/') }}">
							<i class="fas fa-home"></i>
							<p>Anasayfa</p>
						</a>
					</li>

					<li class="nav-section">
						<span class="sidebar-mini-icon">
							<i class="fa fa-ellipsis-h"></i>
						</span>
						<h4 class="text-section">İŞLEM MENÜSÜ</h4>
					</li>

					<li class="nav-item {{ request()->routeIs('import.index') ? 'active' : '' }}">
						<a href="{{ route('import.index') }}">
							<i class="fas fa-cloud-upload-alt"></i>
							<p>Excel Yükleme</p>
						</a>
					</li>

					<li class="nav-item {{ request()->is('fatura*') || request()->routeIs('staging.*') ? 'active' : '' }}">
						<a data-toggle="collapse" href="#diger-fatura">
							<i class="fas fa-file-invoice"></i>
							<p>Fatura İşlemleri</p>
							<span class="caret"></span>
						</a>
						<div class="collapse {{ request()->is('fatura*') || request()->routeIs('staging.*') ? 'show' : '' }}" id="diger-fatura">
							<ul class="nav nav-collapse">
								<li class="{{ request()->routeIs('staging.index') ? 'active' : '' }}">
									<a href="{{ route('staging.index') }}">
										<span class="sub-item">Bekleyen Faturalar</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('fatura.odenenler') ? 'active' : '' }}">
									<a href="{{ route('fatura.odenenler') }}">
										<span class="sub-item">Ödenen Faturalar</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('reaktifler.*') ? 'active' : '' }}">
									<a href="{{ route('reaktifler.index') }}">
										<span class="sub-item">Reaktif Faturalar</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('fatura.itirazlar') ? 'active' : '' }}">
									<a href="{{ route('fatura.itirazlar') }}">
										<span class="sub-item">İtiraz Edilen Faturalar</span>
									</a>
								</li>
							</ul>
						</div>
					</li>

					<li class="nav-item {{ request()->is('raporlar*') && !request()->is('raporlar/endeks') && !request()->is('raporlar/anomali*') ? 'active' : '' }}">
						<a data-toggle="collapse" href="#reports">
							<i class="fas fa-chart-bar"></i>
							<p>Rapor İşlemleri</p>
							<span class="caret"></span>
						</a>
						<div class="collapse {{ request()->is('raporlar*') && !request()->is('raporlar/endeks') && !request()->is('raporlar/anomali*') ? 'show' : '' }}" id="reports">
							<ul class="nav nav-collapse">
								<li class="{{ request()->routeIs('reports.periodical') ? 'active' : '' }}">
									<a href="{{ route('reports.periodical') }}">
										<span class="sub-item">Dönem Bazında Rapor</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('reports.yearly') ? 'active' : '' }}">
									<a href="{{ route('reports.yearly') }}">
										<span class="sub-item">Yıl Bazında Rapor</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('reports.detailed') ? 'active' : '' }}">
									<a href="{{ route('reports.detailed') }}">
										<span class="sub-item">Detaylı Fatura Raporu</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('reports.koy-merkez') ? 'active' : '' }}">
									<a href="{{ route('reports.koy-merkez') }}">
										<span class="sub-item">Köy / Merkez Raporu</span>
									</a>
								</li>
							</ul>
						</div>
					</li>

					<li class="nav-item {{ request()->is('raporlar/endeks') || request()->is('raporlar/anomali*') ? 'active' : '' }}">
						<a data-toggle="collapse" href="#analysis">
							<i class="fas fa-search-plus"></i>
							<p>Analiz ve Denetim</p>
							<span class="caret"></span>
						</a>
						<div class="collapse {{ request()->is('raporlar/endeks') || request()->is('raporlar/anomali*') ? 'show' : '' }}" id="analysis">
							<ul class="nav nav-collapse">
								<li class="{{ request()->routeIs('reports.endeks') ? 'active' : '' }}">
									<a href="{{ route('reports.endeks') }}">
										<span class="sub-item">Endeks Analizi</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('reports.anomali') ? 'active' : '' }}">
									<a href="{{ route('reports.anomali') }}">
										<span class="sub-item">Anomali Analizi</span>
									</a>
								</li>
							</ul>
						</div>
					</li>

					<li class="nav-item {{ request()->is('aboneler*') || request()->routeIs('bolgeler.*') ? 'active' : '' }}">
						<a data-toggle="collapse" href="#abone">
							<i class="fas fa-users"></i>
							<p>Abone İşlemleri</p>
							<span class="caret"></span>
						</a>
						<div class="collapse {{ request()->is('aboneler*') || request()->routeIs('bolgeler.*') ? 'show' : '' }}" id="abone">
							<ul class="nav nav-collapse">
								<li class="{{ request()->routeIs('aboneler.index') ? 'active' : '' }}">
									<a href="{{ route('aboneler.index') }}">
										<span class="sub-item">Aboneler</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('bolgeler.*') ? 'active' : '' }}">
									<a href="{{ route('bolgeler.index') }}">
										<span class="sub-item">Bölgeler</span>
									</a>
								</li>
							</ul>
						</div>
					</li>

					<li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
						<a data-toggle="collapse" href="#sidebarLayouts">
							<i class="fas fa-user-shield"></i>
							<p>Sistem Yönetimi</p>
							<span class="caret"></span>
						</a>
						<div class="collapse {{ request()->routeIs('users.*') ? 'show' : '' }}" id="sidebarLayouts">
							<ul class="nav nav-collapse">
								<li class="{{ request()->routeIs('users.index') ? 'active' : '' }}">
									<a href="{{ route('users.index') }}">
										<span class="sub-item">Kullanıcı Listesi</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('users.create') ? 'active' : '' }}">
									<a href="{{ route('users.create') }}">
										<span class="sub-item">Yeni Kullanıcı Tanımla</span>
									</a>
								</li>
							</ul>
						</div>
					</li>

					<li class="nav-item {{ request()->routeIs('activity-logs.index') ? 'active' : '' }}">
						<a href="{{ route('activity-logs.index') }}">
							<i class="fas fa-history"></i>
							<p>Aktivite Logları</p>
						</a>
					</li>

					<li class="nav-item logout-item" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px;">
						<form method="POST" action="{{ route('logout') }}" id="logout-form">
							@csrf
							<a href="#"
								onclick="event.preventDefault(); let form = this.closest('form'); alertify.confirm('Güvenli Çıkış', 'Sistemden çıkıp oturumu kapatmak istediğinize emin misiniz?', function(){ form.submit(); }, function(){}).set('labels', {ok:'Evet, Çıkış Yap', cancel:'Vazgeç'});">
								<i class="fas fa-power-off"></i>
								<p>Güvenli Çıkış</p>
							</a>
						</form>
					</li>
				</ul>
			</div>
		</div>
		<div class="scroll-element scroll-x scroll-scrolly_visible"><div class="scroll-element_outer"><div class="scroll-element_size"></div><div class="scroll-element_track"></div><div class="scroll-bar ui-draggable ui-draggable-handle" style="width: 88px;"></div></div></div><div class="scroll-element scroll-y scroll-scrolly_visible"><div class="scroll-element_outer"><div class="scroll-element_size"></div><div class="scroll-element_track"></div><div class="scroll-bar ui-draggable ui-draggable-handle" style="height: 600px; top: 0px;"></div></div></div>
	</div>
</div>
<!-- End Sidebar -->
