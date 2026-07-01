@extends('frontend.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --primary-gradient: linear-gradient(135deg, #8b5cf6, #6d28d9);
        --bg-main: #f4f6f9;
        --surface-glass: rgba(255, 255, 255, 0.85);
        --text-slate-900: #0f172a;
        --text-slate-500: #64748b;
        --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }
    .pg-premium { background-color: var(--bg-main) !important; min-height: 100vh; padding-bottom: 4rem; }
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #2e1065 100%);
        position: relative; padding: 4rem 2rem 8rem 2rem; margin-top: -20px; color: #fff; overflow: hidden;
        border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }
    .page-hero::before {
        content: ''; position: absolute; width: 600px; height: 600px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%);
        top: -150px; right: -100px; border-radius: 50%; opacity: 0.5; filter: blur(60px);
        animation: pulseSlow 10s infinite alternate; pointer-events: none;
    }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.6; } }
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 {
        font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px;
    }
    .form-control-pro {
        width: 100%; padding: 12px 18px; background: #fff; border: 1px solid #e2e8f0;
        border-radius: 14px; font-size: 0.95rem; font-weight: 500; color: var(--text-slate-900);
        transition: all 0.2s; outline: none;
    }
    .form-control-pro:focus { border-color: #8b5cf6; box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1); }
    .btn-pro { padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important; }
    .btn-primary-pro { background: var(--primary-gradient); color: white !important; box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.3); }
    .btn-primary-pro:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(139, 92, 246, 0.4); }
    .btn-outline-pro { background: #fff; border: 1px solid #e2e8f0; color: var(--text-slate-500); }
    .btn-outline-pro:hover { background: #f8fafc; color: var(--text-slate-900); border-color: #cbd5e1; }
    .form-label { display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-slate-500); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.03em; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Yeni Tesis</h1>
                <p class="hero-subtitle">Sisteme yeni bir tesis kaydı ekleyin.</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card">
            <form action="{{ route('tesis-bilgi-sistemi.tesisler.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Durum</label>
                        <select name="durum" class="form-control-pro" required>
                            <option value="aktif" {{ old('durum') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="pasif" {{ old('durum') == 'pasif' ? 'selected' : '' }}>Pasif</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">İlçe</label>
                        <select name="ilce" class="form-control-pro" required>
                            <option value="">Seçiniz</option>
                            @foreach($ilceler as $i)
                            <option value="{{ $i }}" {{ old('ilce') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Abone (Bağlantı)</label>
                        <select name="abone_id" class="form-control-pro">
                            <option value="">Bağlantı Yok</option>
                            @foreach($aboneler as $a)
                            <option value="{{ $a->id }}" {{ old('abone_id') == $a->id ? 'selected' : '' }}>{{ $a->ABONE_TESIS_NO }} - {{ $a->UNVAN }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Mahalle</label>
                        <input type="text" name="mahalle" class="form-control-pro" value="{{ old('mahalle') }}" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Sokak</label>
                        <input type="text" name="sokak" class="form-control-pro" value="{{ old('sokak') }}">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Kuyu No</label>
                        <input type="text" name="kuyu_no" class="form-control-pro" value="{{ old('kuyu_no') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">CBS X</label>
                        <input type="text" name="cbs_x" class="form-control-pro" value="{{ old('cbs_x') }}">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">CBS Y</label>
                        <input type="text" name="cbs_y" class="form-control-pro" value="{{ old('cbs_y') }}">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Abone No</label>
                        <input type="text" name="abone_no" class="form-control-pro" value="{{ old('abone_no') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Sayaç No</label>
                        <input type="text" name="sayac_no" class="form-control-pro" value="{{ old('sayac_no') }}">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Tesis Kurulma Tarihi</label>
                        <input type="date" name="tesis_kurulma_tarihi" class="form-control-pro" value="{{ old('tesis_kurulma_tarihi') }}">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Abone Tipi</label>
                        <input type="text" name="abone_tipi" class="form-control-pro" value="{{ old('abone_tipi') }}" placeholder="AG / OG">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Trafo Gücü</label>
                        <input type="text" name="trafo_gucu" class="form-control-pro" value="{{ old('trafo_gucu') }}">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Trafo Seri No</label>
                        <input type="text" name="trafo_seri_no" class="form-control-pro" value="{{ old('trafo_seri_no') }}">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">ENH Durumu</label>
                        <input type="text" name="enh_durumu" class="form-control-pro" value="{{ old('enh_durumu') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Demontaj Tarihi</label>
                        <input type="date" name="demontaj_tarihi" class="form-control-pro" value="{{ old('demontaj_tarihi') }}">
                    </div>
                    <div class="col-md-8 mb-4">
                        <label class="form-label">Demontaj Yapılan Malzemeler</label>
                        <textarea name="demontaj_yapilan_malzemeler" class="form-control-pro" rows="2">{{ old('demontaj_yapilan_malzemeler') }}</textarea>
                    </div>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger" style="border-radius:14px; margin-top:10px;">
                    <ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
                @endif

                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('tesis-bilgi-sistemi.tesisler') }}" class="btn-pro btn-outline-pro">İptal</a>
                    <button type="submit" class="btn-pro btn-primary-pro"><i class="fas fa-save"></i> Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
