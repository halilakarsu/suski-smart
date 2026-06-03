@extends('frontend.layouts.app')

@section('content')
<style>
    /* Ultra-Premium Glassmorphic Design for Users Index */
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
    }

    .pg-premium {
        background-color: var(--bg-main) !important;
        min-height: 100vh;
        padding-bottom: 3rem;
    }

    /* Hero Section for Page Title */
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative;
        padding: 4rem 2rem 8rem 2rem;
        margin-top: -20px;
        color: #fff;
        overflow: hidden;
        border-bottom-left-radius: 40px;
        border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .page-hero::before {
        content: ''; position: absolute; width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.3) 0%, transparent 70%);
        top: -150px; left: -100px; border-radius: 50%; opacity: 0.6; filter: blur(40px);
        animation: pulseSlow 8s infinite alternate; pointer-events: none;
    }

    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.15); opacity: 0.7; } }

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 { 
        font-family: var(--font-primary); font-size: clamp(1.8rem, 4vw, 2.5rem); font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-title-group p { color: #94a3b8; font-size: 1rem; font-weight: 500; }

    /* Main Container */
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }

    /* Glass Card */
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated);
    }

    .card-header-pro { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .card-title-pro { font-size: 1.25rem; font-weight: 800; color: var(--text-slate-900); display: flex; align-items: center; gap: 12px; }
    .card-title-pro i { padding: 10px; background: #f1f5f9; border-radius: 12px; color: #3b82f6; }

    .btn-add-pro {
        background: var(--primary-gradient); color: white !important; padding: 12px 24px; border-radius: 14px;
        font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
        border: none; text-decoration: none !important;
    }
    .btn-add-pro:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.5); }

    /* Table Styling */
    .table-pro { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .table-pro th { color: #94a3b8; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 20px; text-align: left; border: none; }
    .table-pro td { background: #fff; padding: 16px 20px; vertical-align: middle; border: none; transition: all 0.2s ease; }
    .table-pro tr td:first-child { border-top-left-radius: 18px; border-bottom-left-radius: 18px; }
    .table-pro tr td:last-child { border-top-right-radius: 18px; border-bottom-right-radius: 18px; }
    .table-pro tr:hover td { background: #f8fafc; transform: scale(1.005); box-shadow: 0 5px 15px rgba(0,0,0,0.02); }

    /* User Info Col */
    .user-info { display: flex; align-items: center; gap: 15px; }
    .avatar-pro {
        width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.1rem; color: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .u-details h6 { margin: 0; font-weight: 700; color: var(--text-slate-900); font-size: 0.95rem; }
    .u-details span { color: var(--text-slate-500); font-size: 0.8rem; font-weight: 500; }

    /* Badges */
    .badge-pro { padding: 6px 14px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; }
    .badge-admin { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
    .badge-staff { background: rgba(37, 99, 235, 0.1); color: #1d4ed8; }

    /* Action Buttons */
    .actions-pro { display: flex; gap: 8px; }
    .action-btn-pro {
        width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem; transition: all 0.2s; border: 1px solid transparent; text-decoration: none !important;
    }
    .a-key { background: #fff7ed; color: #ea580c; } .a-key:hover { background: #ffedd5; border-color: #fdba74; }
    .a-edit { background: #eff6ff; color: #2563eb; } .a-edit:hover { background: #dbeafe; border-color: #93c5fd; }
    .a-del { background: #fef2f2; color: #dc2626; border: none; padding: 0; } .a-del:hover { background: #fee2e2; border-color: #fecaca; }
    .a-del button { background: transparent; border: none; color: inherit; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; cursor: pointer; }

    /* Datatable overrides */
    .dataTables_wrapper .dataTables_filter input {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 8px 15px; font-weight: 600; font-size: 0.9rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-gradient) !important; border: none !important; color: white !important; border-radius: 10px !important;
    }
</style>

<div class="pg-premium p-0">
    <!-- HERO TITLE AREA -->
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Kullanıcı Yönetimi</h1>
                <p>Sistem personellerini ve yetki seviyelerini buradan yönetebilirsiniz.</p>
            </div>
            <div>
                <span style="background: rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 100px; font-size: 0.85rem; font-weight: 700; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color:#cbd5e1;">
                    <i class="fas fa-users-cog mr-2"></i> Personel Listesi
                </span>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-container">
        
        @if(session('success'))
            <div class="alert alert-custom mb-4" style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; border-radius: 18px; padding: 15px 25px; font-weight: 700; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="glass-card">
            <div class="card-header-pro">
                <h4 class="card-title-pro"><i class="fas fa-id-card"></i> Sistem Kullanıcıları</h4>
                <a href="{{ route('users.create') }}" class="btn-add-pro">
                    <i class="fas fa-plus-circle"></i> Yeni Personel Ekle
                </a>
            </div>

            <div class="table-responsive">
                <table id="user-table-pro" class="table-pro">
                    <thead>
                        <tr>
                            <th>Personel Bilgileri</th>
                            <th>Erişim Seviyesi</th>
                            <th style="width: 150px; text-align: right;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @php
                            $colors = [
                                ['bg' => 'linear-gradient(135deg, #6366f1, #4338ca)', 'c' => '#ffffff'],
                                ['bg' => 'linear-gradient(135deg, #10b981, #059669)', 'c' => '#ffffff'],
                                ['bg' => 'linear-gradient(135deg, #f59e0b, #d97706)', 'c' => '#ffffff'],
                                ['bg' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)', 'c' => '#ffffff'],
                                ['bg' => 'linear-gradient(135deg, #8b5cf6, #7c3aed)', 'c' => '#ffffff'],
                            ];
                            $c = $colors[crc32($user->name) % count($colors)];
                            $initials = mb_substr($user->name, 0, 2);
                        @endphp
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="avatar-pro" style="background: {{ $c['bg'] }};">
                                        {{ mb_strtoupper($initials) }}
                                    </div>
                                    <div class="u-details">
                                        <h6>{{ $user->name }}</h6>
                                        <span>{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge-pro badge-admin"><i class="fas fa-shield-alt"></i> Yönetici</span>
                                @else
                                    <span class="badge-pro badge-staff"><i class="fas fa-user-tie"></i> Personel</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-pro justify-content-end">
                                    <a href="{{ route('users.permissions', $user->id) }}" class="action-btn-pro a-key" title="Yetki Düzenle">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="action-btn-pro a-edit" title="Profili Güncelle">
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">
                                        @csrf @method('DELETE')
                                        <div class="action-btn-pro a-del">
                                            <button type="submit" title="Kullanıcıyı Sil">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        if ( ! $.fn.DataTable.isDataTable( '#user-table-pro' ) ) {
            $('#user-table-pro').DataTable({
                ordering: true,
                pageLength: 10,
                autoWidth: false,
                dom: '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
            });
        }
    });
</script>
@endpush
@endsection