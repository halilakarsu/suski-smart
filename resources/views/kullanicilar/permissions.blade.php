@extends('frontend.layouts.app')
@section('content')
<style>
:root{
  --p1:#1a5f8a;--p2:#2179b0;--p3:#3a9fd6;
  --bg:#eff4f8;--card:#fff;
  --border:#e2e8f0;
  --text:#0f172a;--text2:#475569;--muted:#94a3b8;
  --sh:0 4px 14px rgba(15,23,42,.03);
  --r:16px;--ease:cubic-bezier(.4,0,.2,1);
}
.pg { padding:1.5rem; min-height:100vh; background:var(--bg); font-family:'Inter', sans-serif;}
.page-title-box { display:flex; align-items:center; margin-bottom:1.5rem; gap:0.5rem; }
.page-title-box h2 { margin:0; font-size:1.35rem; font-weight:800; color:var(--text); letter-spacing:-0.3px; }
.page-title-box i { color:var(--p1); font-size:1.4rem; }

.p-card { background:var(--card); border:1px solid var(--border); border-radius:var(--r); box-shadow:var(--sh); overflow:hidden; }
.p-header { display:flex; justify-content:space-between; align-items:center; padding:1.5rem; border-bottom:1px solid var(--border); background:linear-gradient(to right, #ffffff, #f8fafc); flex-wrap:wrap; gap:1rem; }

.u-profile { display:flex; align-items:center; gap:18px; }
.u-avatar { width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg,var(--p1),var(--p3)); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.5rem; text-transform:uppercase; box-shadow:0 4px 12px rgba(26,95,138,0.25); }
.u-info { display:flex; flex-direction:column; gap:4px; }
.u-info h4 { margin:0; font-size:1.15rem; font-weight:800; color:var(--text); line-height:1; }
.u-info-sub { display:flex; align-items:center; gap:10px; }
.u-mail { font-size:0.85rem; font-weight:500; color:var(--text2); }
.badge-role { display:inline-flex; align-items:center; gap:4px; padding:0.25rem 0.6rem; border-radius:20px; font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; }
.br-admin { background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; }
.br-staff { background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; }

.btn-actions { display:flex; gap:10px; }
.b-cancel { display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:var(--text2); font-size:0.85rem; font-weight:700; padding:0.6rem 1.25rem; border-radius:30px; text-decoration:none; transition:all 0.3s; border:1px solid #cbd5e1; }
.b-cancel:hover { background:#e2e8f0; color:var(--text); transform:translateY(-2px); text-decoration:none;}
.b-save { display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg,var(--p1),var(--p2)); color:#fff; font-size:0.85rem; font-weight:700; padding:0.6rem 1.5rem; border-radius:30px; border:none; cursor:pointer; transition:all 0.3s; box-shadow:0 4px 12px rgba(26,95,138,0.25); }
.b-save:hover { background:linear-gradient(135deg,var(--p2),var(--p3)); transform:translateY(-2px); box-shadow:0 6px 18px rgba(26,95,138,0.35); }

/* Switch Layouts */
.perm-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:1.5rem; padding:1.5rem; }
.perm-module { background:#f8fafc; border:1px solid var(--border); border-radius:14px; overflow:hidden; }
.pm-head { display:flex; justify-content:space-between; align-items:center; padding:1rem 1.25rem; border-bottom:1px solid var(--border); background:#fff; }
.pm-title { margin:0; font-weight:800; font-size:0.95rem; color:var(--text); display:flex; align-items:center; gap:8px; }
.pm-i { width:26px; height:26px; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:0.8rem; }
.pm-sel-all { font-size:0.7rem; font-weight:800; color:var(--p2); background:none; border:none; cursor:pointer; text-transform:uppercase; padding:0; transition:color 0.2s; letter-spacing:0.04em; }
.pm-sel-all:hover { color:var(--p1); }
.pm-body { padding:1.25rem; display:flex; flex-direction:column; gap:0.85rem; }

/* Custom iOS Style Switch */
.switch-item { display:flex; align-items:center; justify-content:space-between; background:#fff; border:1px solid #e2e8f0; padding:0.85rem 1rem; border-radius:10px; cursor:pointer; transition:all 0.2s; margin:0;}
.switch-item:hover { border-color:#cbd5e1; box-shadow:0 2px 8px rgba(15,23,42,0.03); transform:translateY(-1px);}
.si-info { display:flex; flex-direction:column; gap:3px; }
.si-title { font-weight:800; font-size:0.85rem; color:var(--text); }
.si-desc { font-weight:500; font-size:0.7rem; color:var(--muted); }

.toggle-switch { position:relative; width:44px; height:24px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#cbd5e1; transition:.3s var(--ease); border-radius:34px; }
.toggle-slider:before { position:absolute; content:""; height:18px; width:18px; left:3px; bottom:3px; background-color:white; transition:.3s var(--ease); border-radius:50%; box-shadow:0 2px 4px rgba(0,0,0,0.15); }
.toggle-switch input:checked + .toggle-slider { background-color:var(--p2); }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }
</style>

<div class="pg">
    <div class="page-title-box">
        <i class="fas fa-user-shield"></i>
        <h2>Personel Yetki Yönetimi</h2>
    </div>

    <form action="{{ route('users.permissions.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="p-card">
            <div class="p-header">
                <div class="u-profile">
                    <div class="u-avatar">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div class="u-info">
                        <h4>{{ $user->name }}</h4>
                        <div class="u-info-sub">
                            <span class="u-mail">{{ $user->email }}</span>
                            @if($user->role == 'admin')
                                <span class="badge-role br-admin"><i class="fas fa-shield-alt"></i> Sistem Yöneticisi</span>
                            @else
                                <span class="badge-role br-staff"><i class="fas fa-user"></i> Personel</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="btn-actions">
                    <a href="{{ route('users.index') }}" class="b-cancel">Vazgeç</a>
                    <button type="submit" class="b-save"><i class="fas fa-save"></i> Değişiklikleri Uygula</button>
                </div>
            </div>

            @if($user->role === 'admin')
            <div style="padding:1.5rem 1.5rem 0 1.5rem;">
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; color:#1e40af; padding:1.25rem 1.5rem; display:flex; gap:12px; align-items:flex-start;">
                    <i class="fas fa-info-circle mt-1" style="font-size:1.2rem;"></i>
                    <div>
                        <strong style="font-weight:800; font-size:0.95rem;">Yönetici Bilgilendirmesi:</strong><br>
                        <span style="font-size:0.85rem; font-weight:500; opacity:0.9;">Bu kullanıcı "Admin" rolüne sahip olduğu için aslında bazı yetkileri seçmeseniz dahi tam yetkiye sahip olabilir. Yetkiler daha çok "Personel" rollerinin görünürlük ayarlarını denetler.</span>
                    </div>
                </div>
            </div>
            @endif

            <div class="perm-grid">
                {{-- Modül: Abone İşlemleri --}}
                <div class="perm-module">
                    <div class="pm-head">
                        <h6 class="pm-title"><div class="pm-i" style="background:#0284c7;"><i class="fas fa-building"></i></div> Abone & Tesisat</h6>
                        <button type="button" class="pm-sel-all">Tümünü Seç</button>
                    </div>
                    <div class="pm-body">
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Görüntüleme</span>
                                <span class="si-desc">Abone listelerini görme yetkisi</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_aboneler" {{ $user->hasPermission('view_aboneler') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Yönetim Yetkisi</span>
                                <span class="si-desc">Abone ekleme ve düzenleme</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="manage_aboneler" {{ $user->hasPermission('manage_aboneler') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Bölge Yönetimi</span>
                                <span class="si-desc">Bölge tanımlama ve düzenleme</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="manage_bolgeler" {{ $user->hasPermission('manage_bolgeler') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Modül: Yükleme İşlemleri --}}
                <div class="perm-module">
                    <div class="pm-head">
                        <h6 class="pm-title"><div class="pm-i" style="background:#f59e0b;"><i class="fas fa-cloud-upload-alt"></i></div> Yükleme & Havuz</h6>
                        <button type="button" class="pm-sel-all">Tümünü Seç</button>
                    </div>
                    <div class="pm-body">
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Excel Veri Yükleme</span>
                                <span class="si-desc">Yeni fatura veri seti import etme</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="upload_faturalar" {{ $user->hasPermission('upload_faturalar') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Yükleme Kayıtları</span>
                                <span class="si-desc">Geçmiş yükleme loglarını görme</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_import_logs" {{ $user->hasPermission('view_import_logs') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Bekleyen Faturalar</span>
                                <span class="si-desc">Havuzdaki (Staging) faturaları yönetme</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_staging_faturalar" {{ $user->hasPermission('view_staging_faturalar') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Modül: Fatura İşlemleri --}}
                <div class="perm-module">
                    <div class="pm-head">
                        <h6 class="pm-title"><div class="pm-i" style="background:#059669;"><i class="fas fa-file-invoice-dollar"></i></div> Fatura Yönetimi</h6>
                        <button type="button" class="pm-sel-all">Tümünü Seç</button>
                    </div>
                    <div class="pm-body">
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Kesinleşen Faturalar</span>
                                <span class="si-desc">Ana fatura listesine erişim</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_kesinlesen_faturalar" {{ $user->hasPermission('view_kesinlesen_faturalar') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Anomali Takibi</span>
                                <span class="si-desc">Anomalili faturaları inceleme</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_anomali_faturalar" {{ $user->hasPermission('view_anomali_faturalar') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Reaktif Cezalar</span>
                                <span class="si-desc">Reaktif takibi modülüne erişim</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_reaktif_faturalar" {{ $user->hasPermission('view_reaktif_faturalar') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">İtiraz Yönetimi</span>
                                <span class="si-desc">İtiraz edilen faturaları yönetme</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_itirazlar" {{ $user->hasPermission('view_itirazlar') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Modül: Sistem --}}
                <div class="perm-module">
                    <div class="pm-head">
                        <h6 class="pm-title"><div class="pm-i" style="background:#6b7280;"><i class="fas fa-cogs"></i></div> Sistem & Rapor</h6>
                        <button type="button" class="pm-sel-all">Tümünü Seç</button>
                    </div>
                    <div class="pm-body">
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Kullanıcı Yönetimi</span>
                                <span class="si-desc">Hesaplar ve yetki yönetimi</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="manage_users" {{ $user->hasPermission('manage_users') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Analiz Raporları</span>
                                <span class="si-desc">Yıllık/Dönemlik raporlara erişim</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_reports" {{ $user->hasPermission('view_reports') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Destek Yönetimi</span>
                                <span class="si-desc">Gelen talepleri yanıtlama</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="manage_support" {{ $user->hasPermission('manage_support') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                        <label class="switch-item">
                            <div class="si-info">
                                <span class="si-title">Aktivite Logları</span>
                                <span class="si-desc">Eylem geçmişini izleme</span>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" class="perm-check" name="permissions[]" value="view_logs" {{ $user->hasPermission('view_logs') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div style="padding:0 1.5rem 1.5rem 1.5rem; display:flex; justify-content:flex-end;">
               <button type="submit" class="b-save" style="font-size:0.95rem; padding:0.8rem 2rem;"><i class="fas fa-check-double"></i> Tüm Yetkileri Kaydet</button>
            </div>
            
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('.pm-sel-all').forEach(button => {
        button.addEventListener('click', function () {
            const card = this.closest('.perm-module');
            const checkboxes = card.querySelectorAll('.perm-check');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });

            this.textContent = allChecked ? 'Tümünü Seç' : 'Seçimi Kaldır';
        });
    });

    // Checkbox değiştiğinde "Tümünü Seç" metnini güncelle
    document.querySelectorAll('.perm-check').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const card = this.closest('.perm-module');
            const checkboxes = card.querySelectorAll('.perm-check');
            const button = card.querySelector('.pm-sel-all');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            button.textContent = allChecked ? 'Seçimi Kaldır' : 'Tümünü Seç';
        });
    });
</script>
@endsection