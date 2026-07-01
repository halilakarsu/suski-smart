@extends('frontend.layouts.app')

@section('content')
<style>
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #2e1065 100%);
        position: relative; padding: 4rem 2rem 8rem 2rem; margin-top: -30px !important; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
    }
    .page-hero::before {
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.3) 0%, transparent 70%);
        top: -200px; left: -150px; border-radius: 50%; opacity: 0.6; filter: blur(60px);
        animation: pulseSlow 10s infinite alternate; pointer-events: none;
    }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.7; } }
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; }
    .hero-title-group h1 {
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.3rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1rem; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08); margin-bottom: 30px;
    }
    .filter-form { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 20px; }
    .filter-form input { padding: 10px 16px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; font-size: 0.9rem; min-width: 240px; }
    .filter-form button { padding: 10px 24px; border-radius: 12px; border: none; background: linear-gradient(135deg, #10b981, #059669); color: #fff; font-weight: 600; cursor: pointer; }
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 6px; }
    .table-pro th { text-align: left; padding: 12px 16px; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .table-pro td { padding: 14px 16px; background: rgba(255,255,255,0.7); border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; vertical-align: middle; }
    .table-pro tr:first-child td { border-radius: 16px 16px 0 0; }
    .table-pro tr:last-child td { border-radius: 0 0 16px 16px; }
    .pagination-wrap { display: flex; justify-content: center; margin-top: 20px; }
    .vehicle-icon { width: 40px; height: 40px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background: #ecfdf5; color: #059669; font-size: 1.2rem; }
</style>
<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1>Araçlar</h1>
                <p class="hero-subtitle">Tüm araç ve personel bilgileri</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card">
            <form class="filter-form" method="GET">
                <input type="text" name="arama" placeholder="Plaka / Personel / Araç Markası" value="{{ request('arama') }}">
                <button type="submit">Ara</button>
                @if(request('arama'))
                <a href="{{ route('tesis-bilgi-sistemi.araclar') }}" style="padding:10px 20px;border-radius:12px;border:1px solid #e2e8f0;color:#64748b;text-decoration:none;">Temizle</a>
                @endif
            </form>

            <table class="table-pro">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Plaka</th>
                        <th>Marka</th>
                        <th>Model</th>
                        <th>Kullanıcı Personel</th>
                        <th>İrtibat</th>
                        <th>Kullanıldığı İş</th>
                        <th style="width:90px;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($araclar as $a)
                    <tr id="row-{{ $a->id }}">
                        <td>{{ $a->sira_no }}</td>
                        <td><strong><span class="vehicle-icon"><i class="fas fa-truck"></i></span> {{ $a->plaka }}</strong></td>
                        <td>{{ $a->aracin_cinsi }}</td>
                        <td>{{ $a->arac_tipi ?? '-' }}</td>
                        <td>{{ $a->kullanici_personel ?? '-' }}</td>
                        <td>{{ $a->irtibat ?? '-' }}</td>
                        <td>{{ $a->kullanildigi_is ?? '-' }}</td>
                        <td>
                            <div class="dropdown-actions" onclick="toggleDropdown(this)">
                                <button type="button" class="action-btn-text ab-gray" style="gap:6px; padding:0 12px; font-size:.78rem;">
                                    <i class="fas fa-ellipsis-h"></i> İşlem <i class="fas fa-chevron-down" style="font-size:.55rem; opacity:0.5;"></i>
                                </button>
                                <div class="dropdown-actions-menu">
                                    <button class="dropdown-actions-item" onclick="event.stopPropagation(); openEditModal({{ $a->toJson() }})">
                                        <i class="fas fa-pen" style="color:#f59e0b; width:16px;"></i> Güncelle
                                    </button>
                                    <div style="margin:3px 0; border-top:1px solid #f1f5f9;"></div>
                                    <button class="dropdown-actions-item danger" onclick="event.stopPropagation(); confirmDelete('{{ $a->id }}')">
                                        <i class="fas fa-trash" style="width:16px;"></i> Sil
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">Kayıt bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $araclar->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius:28px; border:none; box-shadow:0 25px 50px rgba(0,0,0,0.15);">
            <div class="modal-header" style="padding:25px 30px; border-bottom:1px solid #f1f5f9;">
                <h5 class="modal-title" style="font-weight:800; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-truck" style="color:#2563eb;"></i> Araç Güncelle
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding:30px;">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">
                                Plaka
                            </label>
                            <input type="text" name="plaka" id="m_plaka" class="form-control-pro" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">
                                Araç Cinsi (Marka)
                            </label>
                            <input type="text" name="aracin_cinsi" id="m_aracin_cinsi" class="form-control-pro">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">
                                Araç Tipi (Model)
                            </label>
                            <input type="text" name="arac_tipi" id="m_arac_tipi" class="form-control-pro">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">
                                Kullanıcı Personel
                            </label>
                            <input type="text" name="kullanici_personel" id="m_kullanici_personel" class="form-control-pro">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">
                                İrtibat Telefon
                            </label>
                            <input type="text" name="irtibat" id="m_irtibat" class="form-control-pro">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-slate-500); margin-bottom:8px; text-transform:uppercase;">
                                Kullanıldığı İş
                            </label>
                            <input type="text" name="kullanildigi_is" id="m_kullanildigi_is" class="form-control-pro">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding:20px 30px; background:#f8fafc; border-top:1px solid #f1f5f9;">
                    <button type="button" class="btn-pro btn-outline-pro" data-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn-pro btn-primary-pro">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.dropdown-actions { position: relative; display: inline-block; }
.dropdown-actions-menu { display: none; position: absolute; right: 0; top: 100%; z-index: 1050; min-width: 170px; background: #fff; border-radius: 14px; box-shadow: 0 10px 40px rgba(0,0,0,0.12); border: 1px solid #f1f5f9; padding: 6px; margin-top: 4px; }
.dropdown-actions.open .dropdown-actions-menu { display: block; }
.dropdown-actions-item { display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 14px; border: none; background: none; font-size: 0.85rem; color: #334155; border-radius: 10px; cursor: pointer; transition: all 0.15s; }
.dropdown-actions-item:hover { background: #f1f5f9; }
.dropdown-actions-item.danger { color: #dc2626; }
.dropdown-actions-item.danger:hover { background: #fef2f2; }
.action-btn-text { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #fff; color: #64748b; font-weight: 600; cursor: pointer; transition: all 0.15s; }
.action-btn-text:hover { border-color: #94a3b8; background: #f8fafc; }
</style>

<script>
function toggleDropdown(el) {
    el.classList.toggle('open');
    document.addEventListener('click', function closeDropdown(e) {
        if (!el.contains(e.target)) {
            el.classList.remove('open');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

function openEditModal(arac) {
    $('#editForm').attr('action', '/tesis-bilgi-sistemi/araclar/' + arac.id);
    $('#m_plaka').val(arac.plaka);
    $('#m_aracin_cinsi').val(arac.aracin_cinsi);
    $('#m_arac_tipi').val(arac.arac_tipi);
    $('#m_kullanici_personel').val(arac.kullanici_personel);
    $('#m_irtibat').val(arac.irtibat);
    $('#m_kullanildigi_is').val(arac.kullanildigi_is);
    $('#editModal').modal('show');
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu aracı silmek istediğinizden emin misiniz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Evet, Sil',
        cancelButtonText: 'Vazgeç'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/tesis-bilgi-sistemi/araclar/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    $('#row-' + id).fadeOut();
                    Swal.fire('Silindi!', 'Kayıt başarıyla silindi.', 'success');
                }
            });
        }
    });
}
</script>
@endsection
