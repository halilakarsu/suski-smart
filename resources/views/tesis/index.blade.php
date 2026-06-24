@extends('frontend.layouts.app')

@section('content')
<style>
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
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 {
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;
        background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card {
        background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04); margin-bottom: 30px;
    }
</style>
<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Tesis Bilgi Sistemi</h1>
                <p class="hero-subtitle">Tesis bilgilerini görüntüleme ve yönetme paneli</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="glass-card" style="text-align:center;padding:80px 40px;">
            <div style="width:100px;height:100px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);border-radius:28px;display:flex;align-items:center;justify-content:center;font-size:3rem;margin:0 auto 24px;color:#fff;box-shadow:0 20px 40px -10px rgba(139,92,246,0.4);">
                <i class="fas fa-hard-hat"></i>
            </div>
            <h3 style="font-weight:800;color:var(--text-slate-900);margin-bottom:12px;">Tesis Bilgi Sistemi</h3>
            <p style="color:var(--text-slate-500);max-width:500px;margin:0 auto;font-size:1.05rem;line-height:1.6;">
                Bu modül henüz yapılandırılıyor. Yakında hizmete girecektir.
            </p>
        </div>
    </div>
</div>
@endsection
