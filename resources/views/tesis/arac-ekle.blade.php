@extends('frontend.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --bg-main: #f4f6f9;
        --surface-glass: rgba(255, 255, 255, 0.85);
        --text-slate-900: #0f172a;
        --text-slate-500: #64748b;
        --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }
    .pg-premium { background-color: var(--bg-main) !important; min-height: 100vh; padding-bottom: 4rem; margin-top: -70px !important; }
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #2e1065 100%);
        position: relative; padding: 5rem 2rem 10rem 2rem; margin-top: -30px !important; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
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
        font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }
    .main-container { width: 100%; max-width: 800px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 35px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px;
    }
    .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-slate-900); margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
    .section-title i { padding: 10px; background: #f5f3ff; border-radius: 12px; color: #7c3aed; }
    .form-group { margin-bottom: 22px; }
    .form-group label { display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.04em; }
    .form-group label .required { color: #ef4444; }
    .form-control {
        width: 100%; padding: 12px 16px; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 12px;
        font-size: 0.95rem; color: var(--text-slate-900); transition: all 0.2s; outline: none;
    }
    .form-control:focus { border-color: #7c3aed; box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1); }
    .form-control::placeholder { color: #94a3b8; }
    .btn-primary {
        padding: 14px 32px; border-radius: 14px; font-weight: 800; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 10px;
        background: linear-gradient(135deg, #7c3aed, #6d28d9); color: #fff !important; border: none; cursor: pointer;
        box-shadow: 0 10px 20px -5px rgba(124, 58, 237, 0.3); transition: all 0.3s; text-decoration: none !important;
    }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(124, 58, 237, 0.4); }
    .btn-secondary {
        padding: 14px 32px; border-radius: 14px; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 10px;
        background: #fff; color: #64748b !important; border: 1px solid #e2e8f0; cursor: pointer;
        transition: all 0.3s; text-decoration: none !important;
    }
    .btn-secondary:hover { background: #f8fafc; color: var(--text-slate-900) !important; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
    .alert-success {
        background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 14px 18px;
        color: #065f46; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
    }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1>Yeni Araç Ekle</h1>
                <p class="hero-subtitle">Envanterinize yeni araç bilgisi ekleyin</p>
            </div>
        </div>
    </div>

    <div class="main-container" style="margin-top: -3.5rem;">
        @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="glass-card" style="background: #fef2f2; border: 1px solid #fecaca; padding: 20px; margin-bottom: 20px;">
            <div style="display:flex;align-items:center;gap:10px;color:#dc2626;font-weight:700;margin-bottom:8px;">
                <i class="fas fa-exclamation-circle"></i> Lütfen hataları düzeltin
            </div>
            <ul style="margin:0;padding-left:24px;color:#991b1b;font-size:0.9rem;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="glass-card">
            <div class="section-title">
                <i class="fas fa-truck"></i>
                Araç Bilgileri
            </div>

            <form method="POST" action="{{ route('tesis-bilgi-sistemi.araclar.store') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label>Plaka <span class="required">*</span></label>
                        <input type="text" name="plaka" class="form-control" placeholder="Örn: 34 ABC 123" value="{{ old('plaka') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Sıra No</label>
                        <input type="number" name="sira_no" class="form-control" placeholder="Otomatik" value="{{ old('sira_no', $maxSira + 1) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Araç Cinsi (Marka) <span class="required">*</span></label>
                        <input type="text" name="aracin_cinsi" class="form-control" placeholder="Örn: Ford, Fiat" value="{{ old('aracin_cinsi') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Araç Tipi (Model)</label>
                        <input type="text" name="arac_tipi" class="form-control" placeholder="Örn: Transit, Doblo" value="{{ old('arac_tipi') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Kullanıcı Personel</label>
                        <input type="text" name="kullanici_personel" class="form-control" placeholder="Personel adı soyadı" value="{{ old('kullanici_personel') }}">
                    </div>
                    <div class="form-group">
                        <label>İrtibat Telefon</label>
                        <input type="text" name="irtibat" class="form-control" placeholder="Örn: 0500 000 00 00" value="{{ old('irtibat') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Kullanıldığı İş</label>
                    <input type="text" name="kullanildigi_is" class="form-control" placeholder="Aracın kullanım amacı" value="{{ old('kullanildigi_is') }}">
                </div>

                <div style="display:flex; gap:12px; justify-content:flex-end; margin-top: 30px; padding-top: 25px; border-top: 1px solid #e2e8f0;">
                    <a href="{{ route('tesis-bilgi-sistemi.araclar') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri Dön
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
