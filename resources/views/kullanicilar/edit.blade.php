@extends('frontend.layouts.app')

@section('content')
<style>
    /* Ultra-Premium Glassmorphic Design for User Editing */
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
        padding-bottom: 4rem;
    }

    /* Hero Section */
    .page-hero {
        background: linear-gradient(125deg, #0f172a 0%, #1e1b4b 100%);
        position: relative;
        padding: 4rem 2rem 10rem 2rem;
        margin-top: -20px;
        color: #fff;
        overflow: hidden;
        border-bottom-left-radius: 40px;
        border-bottom-right-radius: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .page-hero::before {
        content: ''; position: absolute; width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
        top: -150px; left: -100px; border-radius: 50%; opacity: 0.6; filter: blur(40px);
        animation: pulseSlow 8s infinite alternate; pointer-events: none;
    }

    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.7; } }

    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 800px; margin: 0 auto; text-align: center; }
    .hero-title { 
        font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }

    /* Form Card Container */
    .form-container { width: 100%; max-width: 800px; margin: -6rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }

    .glass-card {
        background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 32px; padding: 40px;
        box-shadow: var(--shadow-elevated);
    }

    .section-title { font-size: 1rem; font-weight: 800; color: var(--text-slate-900); margin-bottom: 25px; display: flex; align-items: center; gap: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
    .section-title i { color: #3b82f6; }

    /* Form Elements */
    .input-grp { margin-bottom: 24px; }
    .input-grp label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-slate-900); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.03em; }
    .input-grp label span { color: #ef4444; }
    
    .form-control-pro {
        width: 100%; padding: 14px 18px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        font-size: 1rem; color: var(--text-slate-900); font-weight: 500; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }
    .form-control-pro:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); background: #fff; }

    .select-pro {
        width: 100%; padding: 14px 18px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        font-size: 1rem; color: var(--text-slate-900); font-weight: 600; cursor: pointer; outline: none;
    }

    .divider { height: 1px; background: rgba(226, 232, 240, 0.8); margin: 30px 0; border: none; }

    /* Action Buttons */
    .form-footer { display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px; }
    .btn-pro {
        padding: 14px 28px; border-radius: 16px; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 10px;
        transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important;
    }
    .btn-cancel { background: #f1f5f9; color: #64748b; }
    .btn-cancel:hover { background: #e2e8f0; color: #475569; transform: translateY(-2px); }
    
    .btn-save {
        background: var(--primary-gradient); color: white; box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3);
    }
    .btn-save:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4); }

    .invalid-fb { color: #ef4444; font-size: 0.75rem; font-weight: 700; margin-top: 6px; display: block; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <h1 class="hero-title">Profil Düzenle</h1>
            <p class="hero-subtitle"><b>{{ $user->name }}</b> adlı kullanıcının hesap detaylarını güncelleyin.</p>
        </div>
    </div>

    <div class="form-container">
        <div class="glass-card">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="section-title"><i class="fas fa-id-badge"></i> Hesap Detayları</h5>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-grp">
                            <label for="name">Ad Soyad <span>*</span></label>
                            <input type="text" id="name" name="name" class="form-control-pro" value="{{ old('name', $user->name) }}" required placeholder="Örn: Ahmet Yılmaz">
                            @error('name') <span class="invalid-fb">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-grp">
                            <label for="email">E-Posta Adresi <span>*</span></label>
                            <input type="email" id="email" name="email" class="form-control-pro" value="{{ old('email', $user->email) }}" required placeholder="ahmet@kurum.com">
                            @error('email') <span class="invalid-fb">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-grp">
                            <label for="role">Erişim Yetki Seviyesi <span>*</span></label>
                            <select name="role" id="role" class="select-pro" required>
                                <option value="personel" {{ old('role', $user->role) == 'personel' ? 'selected' : '' }}>Personel (Kısmi Yetki)</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Yönetici (Tam Yetki)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="divider">

                <h5 class="section-title"><i class="fas fa-lock"></i> Şifre Güncelleme (İsteğe Bağlı)</h5>
                <p style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 20px;">Eğer personelin şifresini değiştirmek istemiyorsanız bu alanları boş bırakın.</p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-grp">
                            <label for="password">Yeni Şifre</label>
                            <input type="password" id="password" name="password" class="form-control-pro" placeholder="Değiştirmek için yazın">
                            @error('password') <span class="invalid-fb">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-grp">
                            <label for="password_confirmation">Yeni Şifre Onayı</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control-pro" placeholder="Tekrar yazın">
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('users.index') }}" class="btn-pro btn-cancel">
                        <i class="fas fa-arrow-left"></i> Listeye Dön
                    </a>
                    <button type="submit" class="btn-pro btn-save">
                        <i class="fas fa-check-circle"></i> Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection