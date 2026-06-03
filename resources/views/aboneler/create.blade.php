@extends('frontend.layouts.app')

@section('content')
<style>
    /* Ultra-Premium Glassmorphic Design for Create Subscriber */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --font-primary: 'Plus Jakarta Sans', sans-serif;
        --primary-gradient: linear-gradient(135deg, #2563eb, #4f46e5);
        --bg-main: #f4f6f9;
        --surface-glass: rgba(255, 255, 255, 0.85);
        --text-slate-900: #0f172a;
        --text-slate-500: #64748b;
        --shadow-elevated: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
    }

    .pg-premium { background-color: var(--bg-main) !important; min-height: 100vh; padding-bottom: 4rem; }

    /* Hero Section */
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative; padding: 4rem 2rem 8rem 2rem; margin-top: -20px;
        color: #fff; overflow: hidden; border-bottom-left-radius: 40px; border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 { 
        font-family: var(--font-primary); font-size: 2.2rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1rem; font-weight: 500; }

    /* Main Container */
    .main-container { width: 100%; max-width: 1000px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }

    /* Glass Card */
    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 40px;
        box-shadow: var(--shadow-elevated); margin-bottom: 30px;
    }

    .form-label-pro { display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-slate-500); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-control-pro {
        width: 100%; padding: 12px 18px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        font-size: 0.95rem; font-weight: 500; color: var(--text-slate-900); transition: all 0.2s; outline: none;
    }
    .form-control-pro:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }

    .btn-pro {
        padding: 14px 28px; border-radius: 14px; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 10px;
        transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important;
    }
    .btn-primary-pro { background: var(--primary-gradient); color: white !important; box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3); }
    .btn-primary-pro:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4); }
    .btn-outline-pro { background: #fff; border: 1px solid #e2e8f0; color: var(--text-slate-500); }
    .btn-outline-pro:hover { background: #f8fafc; color: var(--text-slate-900); }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Yeni Abone Kaydı</h1>
                <p class="hero-subtitle">Sisteme yeni bir abone tesisatı tanımlayın.</p>
            </div>
            <a href="{{ route('aboneler.index') }}" class="btn-pro btn-outline-pro" style="background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.2);">
                <i class="fas fa-arrow-left"></i> Listeye Dön
            </a>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card">
            <form action="{{ route('aboneler.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label-pro">Abone Ünvanı / İsim</label>
                        <input type="text" name="UNVAN" class="form-control-pro" placeholder="Örn: Ahmet Yılmaz veya Şirket Adı" required>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label-pro">Abone Tesisat No</label>
                        <input type="text" name="ABONE_TESIS_NO" class="form-control-pro" placeholder="Eşsiz tesisat numarası" required>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label-pro">Bölge Koordinasyonu</label>
                        <input type="text" name="BOLGE_ADI" class="form-control-pro" placeholder="Örn: Haliliye">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label-pro">Sayaç Seri No</label>
                        <input type="text" name="SAYAC_SERI_NO" class="form-control-pro" placeholder="Sayaç üzerindeki numara">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label-pro">Bağlantı Grubu</label>
                        <select name="baglanti_grubu" class="form-control-pro">
                            <option value="">Seçiniz...</option>
                            <option value="AG">AG (Alçak Gerilim)</option>
                            <option value="OG">OG (Orta Gerilim)</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label-pro">Abone Grubu</label>
                        <input type="text" name="abone_grubu" class="form-control-pro" placeholder="Örn: Mesken, Sanayi">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label-pro">Tarife</label>
                        <input type="text" name="tarife" class="form-control-pro" placeholder="Tarife kodu">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label-pro">Adres Bilgisi</label>
                        <textarea name="ADRES" class="form-control-pro" rows="3" placeholder="Detaylı adres bilgisi..."></textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label-pro">Özel Notlar</label>
                        <textarea name="notlar" class="form-control-pro" rows="2" placeholder="Abone hakkında önemli notlar..."></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('aboneler.index') }}" class="btn-pro btn-outline-pro">İptal</a>
                    <button type="submit" class="btn-pro btn-primary-pro">
                        <i class="fas fa-save"></i> Aboneyi Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
