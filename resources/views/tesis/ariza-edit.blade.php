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
    .page-hero { background: linear-gradient(125deg, #0f172a 0%, #2e1065 100%); position: relative; padding: 4rem 2rem 8rem 2rem; margin-top: -20px; color: #fff; overflow: hidden; border-bottom-left-radius: 40px; border-bottom-right-radius: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15); }
    .page-hero::before { content: ''; position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%); top: -150px; right: -100px; border-radius: 50%; opacity: 0.5; filter: blur(60px); animation: pulseSlow 10s infinite alternate; pointer-events: none; }
    @keyframes pulseSlow { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(1.1); opacity: 0.6; } }
    .hero-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .hero-title-group h1 { font-family: var(--font-primary); font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em; background: linear-gradient(to right, #ffffff, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem; }
    .hero-subtitle { color: #94a3b8; font-size: 1.1rem; font-weight: 500; }
    .main-container { width: 100%; max-width: 1400px; margin: -5rem auto 0 auto; padding: 0 2rem; position: relative; z-index: 20; }
    .glass-card { background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 28px; padding: 30px; box-shadow: var(--shadow-elevated); margin-bottom: 30px; }
    .glass-card-sm { background: var(--surface-glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 20px; padding: 20px 24px; box-shadow: var(--shadow-elevated); }
    .form-control-pro { width: 100%; padding: 12px 18px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; font-size: 0.95rem; font-weight: 500; color: var(--text-slate-900); transition: all 0.2s; outline: none; }
    .form-control-pro:focus { border-color: #8b5cf6; box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1); }
    .form-control-pro:disabled { background: #f8fafc; color: #94a3b8; cursor: not-allowed; }
    select.form-control-pro { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 40px; }
    .btn-pro { padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; cursor: pointer; text-decoration: none !important; }
    .btn-primary-pro { background: var(--primary-gradient); color: white !important; box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.3); }
    .btn-primary-pro:hover { transform: translateY(-2px); box-shadow: 0 15px 30px -5px rgba(139, 92, 246, 0.4); }
    .btn-outline-pro { background: #fff; border: 1px solid #e2e8f0; color: var(--text-slate-500); }
    .btn-outline-pro:hover { background: #f8fafc; color: var(--text-slate-900); border-color: #cbd5e1; }
    .form-label { display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-slate-500); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.03em; }
    .section-header { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; font-weight: 700; color: var(--text-slate-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; }
    .section-header i { color: #8b5cf6; font-size: 1rem; }
    .chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; background: #f1f5f9; color: #475569; margin: 3px; cursor: pointer; transition: all 0.2s; border: 1px solid transparent; user-select: none; }
    .chip:hover { background: #e2e8f0; }
    .chip.active { background: #7c3aed; color: #fff; border-color: #6d28d9; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.25); }
    .chip.active:hover { background: #6d28d9; }
    .selected-chips { display: flex; flex-wrap: wrap; gap: 4px; padding: 8px 0; min-height: 20px; }
    .selected-chip { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; background: #7c3aed; color: #fff; }
    .selected-chip .remove { cursor: pointer; font-size: 0.9rem; line-height: 1; opacity: 0.7; }
    .selected-chip .remove:hover { opacity: 1; }
    .badge-durum { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }
    .badge-durum.kayit { background: #dbeafe; color: #1e40af; }
    .badge-durum.devam { background: #fef3c7; color: #92400e; }
    .badge-durum.giderildi { background: #d1fae5; color: #065f46; }
    .badge-durum.bekleme { background: #f3e8ff; color: #6b21a8; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Arıza Kaydı Düzenle</h1>
                <p class="hero-subtitle">#{{ $ariza->id }} — {{ $ariza->ilce }} / {{ $ariza->mahalle }}</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <form action="{{ route('tesis-bilgi-sistemi.arizalar.update', $ariza->id) }}" method="POST" id="arizaForm">
            @csrf @method('PUT')

            <div class="glass-card">
                <div class="section-header">
                    <i class="fas fa-map-marker-alt"></i> Tesis & Lokasyon Bilgileri
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Abone No</label>
                        <input type="text" name="abone_no" class="form-control-pro" value="{{ old('abone_no', $ariza->abone_no) }}" placeholder="Abone no">
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">İlçe</label>
                        <select name="ilce" class="form-control-pro" required>
                            <option value="">Seçiniz</option>
                            @foreach($ilceler as $i)
                            <option value="{{ $i }}" {{ old('ilce', $ariza->ilce) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Mahalle / Köy</label>
                        <input type="text" name="mahalle" class="form-control-pro" value="{{ old('mahalle', $ariza->mahalle) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Sokak / Mevki</label>
                        <input type="text" name="sokak" class="form-control-pro" value="{{ old('sokak', $ariza->sokak) }}">
                    </div>
                    <div class="col-md-2 mb-4">
                        <label class="form-label">Kuyu No</label>
                        <input type="text" name="kuyu_no" class="form-control-pro" value="{{ old('kuyu_no', $ariza->kuyu_no) }}">
                    </div>
                    <div class="col-md-2 mb-4">
                        <label class="form-label">Sayaç No</label>
                        <input type="text" name="sayac_no" class="form-control-pro" value="{{ old('sayac_no', $ariza->sayac_no) }}">
                    </div>
                    <div class="col-md-2 mb-4">
                        <label class="form-label">CBS X</label>
                        <input type="text" name="cbs_x" class="form-control-pro" value="{{ old('cbs_x', $ariza->cbs_x) }}">
                    </div>
                    <div class="col-md-2 mb-4">
                        <label class="form-label">CBS Y</label>
                        <input type="text" name="cbs_y" class="form-control-pro" value="{{ old('cbs_y', $ariza->cbs_y) }}">
                    </div>
                </div>
            </div>

            <div class="glass-card">
                <div class="section-header">
                    <i class="fas fa-tools"></i> Arıza Bilgileri
                </div>

                <div class="row">
                    <div class="col-md-3 mb-4">
                        <label class="form-label">Tarih</label>
                        <input type="date" name="tarih" class="form-control-pro" value="{{ old('tarih', $ariza->tarih ? $ariza->tarih->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3 mb-4">
                        <label class="form-label">Ekip</label>
                        <select name="ekip" class="form-control-pro">
                            <option value="">— Ekip Seçiniz —</option>
                            @foreach($ekipler as $e)
                            <option value="{{ $e->ad }}" {{ old('ekip', $ariza->ekip) == $e->ad ? 'selected' : '' }}>{{ $e->ad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-4">
                        <label class="form-label">Durum</label>
                        <select name="durum" class="form-control-pro" required>
                            <option value="Arıza Kaydı Yapıldı" {{ old('durum', $ariza->durum ?? 'Arıza Kaydı Yapıldı') == 'Arıza Kaydı Yapıldı' ? 'selected' : '' }}>Arıza Kaydı Yapıldı</option>
                            <option value="Devam Ediyor" {{ old('durum', $ariza->durum) == 'Devam Ediyor' ? 'selected' : '' }}>Devam Ediyor</option>
                            <option value="Arıza Giderildi" {{ old('durum', $ariza->durum) == 'Arıza Giderildi' ? 'selected' : '' }}>Arıza Giderildi</option>
                            <option value="Beklemede" {{ old('durum', $ariza->durum) == 'Beklemede' ? 'selected' : '' }}>Beklemede</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-4">
                        <label class="form-label">Tutanak No</label>
                        <input type="text" name="tutanak_no" class="form-control-pro" value="{{ old('tutanak_no', $ariza->tutanak_no) }}" placeholder="Varsa tutanak numarası">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Arıza Türleri (Birden fazla seçebilirsiniz)</label>
                        <input type="hidden" name="ariza_turu" id="arizaTuruInput" value="{{ old('ariza_turu', $ariza->ariza_turu) }}">
                        <div class="selected-chips" id="selectedChips"></div>
                        <div style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid #e2e8f0; border-radius: 14px; background: #fff;">
                            @foreach($arizaTurleri as $at)
                            <span class="chip" data-value="{{ $at->ad }}">{{ $at->ad }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Açıklama / Notlar</label>
                    <textarea name="aciklama" class="form-control-pro" rows="3" placeholder="Arıza ile ilgili ek açıklama...">{{ old('aciklama', $ariza->aciklama) }}</textarea>
                </div>
            </div>

            @if ($errors->any())
            <div class="glass-card-sm" style="margin-bottom: 24px;">
                <div class="alert alert-danger" style="border-radius:14px; margin:0;">
                    <ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            </div>
            @endif

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-bottom: 40px;">
                <a href="{{ route('tesis-bilgi-sistemi.arizalar') }}" class="btn-pro btn-outline-pro"><i class="fas fa-arrow-left"></i> Geri Dön</a>
                <button type="submit" class="btn-pro btn-primary-pro"><i class="fas fa-save"></i> Güncelle</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var arizaTuruInput = document.getElementById('arizaTuruInput');
        var selectedChips = document.getElementById('selectedChips');

        function updateArizaTurleri() {
            var selected = [];
            document.querySelectorAll('.chip.active').forEach(function(chip) {
                selected.push(chip.dataset.value);
            });
            arizaTuruInput.value = selected.join(', ');
            selectedChips.innerHTML = '';
            selected.forEach(function(val) {
                var chip = document.createElement('span');
                chip.className = 'selected-chip';
                chip.innerHTML = val + ' <span class="remove" data-value="' + val + '">&times;</span>';
                chip.querySelector('.remove').addEventListener('click', function() {
                    var v = this.dataset.value;
                    document.querySelectorAll('.chip').forEach(function(c) {
                        if (c.dataset.value === v) c.classList.remove('active');
                    });
                    updateArizaTurleri();
                });
                selectedChips.appendChild(chip);
            });
        }

        document.querySelectorAll('.chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                this.classList.toggle('active');
                updateArizaTurleri();
            });
        });

        var oldValues = '{{ old("ariza_turu", $ariza->ariza_turu) }}'.split(',').map(function(v) { return v.trim(); });
        oldValues.forEach(function(val) {
            if (val) {
                document.querySelectorAll('.chip').forEach(function(c) {
                    if (c.dataset.value === val) c.classList.add('active');
                });
            }
        });
        updateArizaTurleri();
    });
</script>
@endpush
@endsection
