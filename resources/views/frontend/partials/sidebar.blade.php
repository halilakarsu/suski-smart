<!-- Sidebar -->
<div class="sidebar sidebar-style-2">

	<div class="scroll-wrapper sidebar-wrapper scrollbar scrollbar-inner" style="position: relative;">
		<div class="sidebar-wrapper scrollbar scrollbar-inner scroll-content scroll-scrolly_visible"
			style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: none;">
			<div class="sidebar-content">
				<!-- User Profile Card -->
				<div class="user-profile-card">
					<div class="user-profile-avatar">
						{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
					</div>
					<div class="user-profile-info">
						<span class="user-profile-name">{{ auth()->user()->name }}</span>
						<span
							class="user-profile-role">{{ auth()->user()->role == 'admin' ? 'Yönetici' : 'Personel' }}</span>
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

					@canany(['view_kuyu_envanteri', 'view_arizalar', 'view_ariza_raporlari'])
						<li
							class="nav-item {{ request()->routeIs('kuyu-envanteri.*') || request()->is('tesis-bilgi-sistemi/arizalar*') || request()->is('tesis-bilgi-sistemi/ariza-raporlari*') ? 'active' : '' }}">
							<a data-toggle="collapse" href="#kuyu-menu">
								<i class="fas fa-tint"></i>
								<p>Kuyu Envanteri</p>
								<span class="caret"></span>
							</a>
							<div class="collapse {{ request()->routeIs('kuyu-envanteri.*') || request()->is('tesis-bilgi-sistemi/arizalar*') || request()->is('tesis-bilgi-sistemi/ariza-raporlari*') ? 'show' : '' }}"
								id="kuyu-menu">
								<ul class="nav nav-collapse">
									@can('view_kuyu_envanteri')
										<li class="{{ request()->routeIs('kuyu-envanteri.*') ? 'active' : '' }}">
											<a href="{{ route('kuyu-envanteri.index') }}">
												<span class="sub-item">Kuyular</span>
											</a>
										</li>
									@endcan
									@can('view_arizalar')
										<li
											class="{{ request()->routeIs('tesis-bilgi-sistemi.arizalar') && !request()->routeIs('tesis-bilgi-sistemi.arizalar.create') ? 'active' : '' }}">
											<a href="{{ route('tesis-bilgi-sistemi.arizalar') }}">
												<span class="sub-item">Arızalar</span>
											</a>
										</li>
									@endcan
									@can('view_ariza_raporlari')
										<li
											class="{{ request()->routeIs('tesis-bilgi-sistemi.ariza-raporlari.yillik') ? 'active' : '' }}">
											<a href="{{ route('tesis-bilgi-sistemi.ariza-raporlari.yillik') }}">
												<span class="sub-item">Arıza İstatistikleri</span>
											</a>
										</li>
										<li
											class="{{ request()->routeIs('tesis-bilgi-sistemi.ariza-raporlari.yillik-ariza') ? 'active' : '' }}">
											<a href="{{ route('tesis-bilgi-sistemi.ariza-raporlari.yillik-ariza') }}">
												<span class="sub-item">Yıl Bazında Rapor</span>
											</a>
										</li>
									@endcan
								</ul>
							</div>
						</li>
					@endcanany

					@can('view_tesis_bilgi_sistemi')
						<li class="nav-item {{ request()->routeIs('tesis-bilgi-sistemi.*') ? 'active' : '' }}">
							<a data-toggle="collapse" href="#tesis-menu">
								<i class="fas fa-building"></i>
								<p>Tesis Bilgi Sistemi</p>
								<span class="caret"></span>
							</a>
							<div class="collapse {{ request()->routeIs('tesis-bilgi-sistemi.*') ? 'show' : '' }}"
								id="tesis-menu">
								<ul class="nav nav-collapse">
									<li class="{{ request()->routeIs('tesis-bilgi-sistemi.tesisler') ? 'active' : '' }}">
										<a href="{{ route('tesis-bilgi-sistemi.tesisler') }}">
											<span class="sub-item">Tesisler</span>
										</a>
									</li>
									<li
										class="{{ request()->routeIs('tesis-bilgi-sistemi.tesisler.create') ? 'active' : '' }}">
										<a href="{{ route('tesis-bilgi-sistemi.tesisler.create') }}">
											<span class="sub-item">Yeni Tesis</span>
										</a>
									</li>
								</ul>
							</div>
						</li>
					@endcan

					<li
						class="nav-item {{ request()->is('fatura*') || request()->routeIs('staging.*') || request()->routeIs('import.*') || request()->routeIs('reports.elektrik-abone-raporlari') ? 'active' : '' }}">
						<a data-toggle="collapse" href="#diger-fatura">
							<i class="fas fa-file-invoice"></i>
							<p>Elektrik Abone Sistemi </p>
							<span class="caret"></span>
						</a>
						<div class="collapse {{ request()->is('fatura*') || request()->routeIs('staging.*') || request()->routeIs('import.*') || request()->routeIs('reports.elektrik-abone-raporlari') ? 'show' : '' }}"
							id="diger-fatura">
							<ul class="nav nav-collapse">
								<li class="{{ request()->routeIs('aboneler.index') ? 'active' : '' }}">
									<a href="{{ route('aboneler.index') }}">
										<span class="sub-item">Aboneler</span>
									</a>
								</li>
								<li class="{{ request()->routeIs('import.index') ? 'active' : '' }}">
									<a href="{{ route('import.index') }}">
										<span class="sub-item">Excel Yükleme</span>
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
								<li
									class="{{ request()->routeIs('reports.elektrik-abone-raporlari') ? 'active' : '' }}">
									<a href="{{ route('reports.elektrik-abone-raporlari') }}">
										<span class="sub-item"> Raporlar</span>
									</a>
								</li>
							</ul>
						</div>
					</li>

					@can('view_araclar')
						<li class="nav-item {{ request()->is('tesis-bilgi-sistemi/araclar*') ? 'active' : '' }}">
							<a data-toggle="collapse" href="#arac-menu">
								<i class="fas fa-truck"></i>
								<p>Araçlar</p>
								<span class="caret"></span>
							</a>
							<div class="collapse {{ request()->is('tesis-bilgi-sistemi/araclar*') ? 'show' : '' }}"
								id="arac-menu">
								<ul class="nav nav-collapse">
									<li class="{{ request()->routeIs('tesis-bilgi-sistemi.araclar') ? 'active' : '' }}">
										<a href="{{ route('tesis-bilgi-sistemi.araclar') }}">
											<span class="sub-item">Araçlar</span>
										</a>
									</li>
									@can('manage_araclar')
										<li>
											<a href="{{ route('tesis-bilgi-sistemi.araclar.create') }}">
												<span class="sub-item">Araç Ekle</span>
											</a>
										</li>
									@endcan
								</ul>
							</div>
						</li>
					@endcan

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

					<li class="nav-item logout-item"
						style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px;">
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
		<div class="scroll-element scroll-x scroll-scrolly_visible">
			<div class="scroll-element_outer">
				<div class="scroll-element_size"></div>
				<div class="scroll-element_track"></div>
				<div class="scroll-bar ui-draggable ui-draggable-handle" style="width: 88px;"></div>
			</div>
		</div>
		<div class="scroll-element scroll-y scroll-scrolly_visible">
			<div class="scroll-element_outer">
				<div class="scroll-element_size"></div>
				<div class="scroll-element_track"></div>
				<div class="scroll-bar ui-draggable ui-draggable-handle" style="height: 600px; top: 0px;"></div>
			</div>
		</div>
	</div>
</div>
<!-- End Sidebar -->