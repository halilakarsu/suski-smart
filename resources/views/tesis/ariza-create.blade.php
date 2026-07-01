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
    .kuyu-card { background: #fff; border-radius: 20px; padding: 24px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; display: none; }
    .kuyu-card .kc-header { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; }
    .kuyu-card .kc-icon { width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #dbeafe, #93c5fd); color: #1e40af; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .kuyu-card .kc-title { font-weight: 800; font-size: 1rem; color: var(--text-slate-900); }
    .kuyu-card .kc-subtitle { font-size: 0.78rem; color: var(--text-slate-500); }
    .kuyu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .kuyu-grid .kg-item { padding: 8px 12px; background: #f8fafc; border-radius: 10px; }
    .kuyu-grid .kg-label { font-size: 0.65rem; font-weight: 700; color: var(--text-slate-500); text-transform: uppercase; letter-spacing: 0.03em; }
    .kuyu-grid .kg-value { font-size: 0.88rem; font-weight: 600; color: var(--text-slate-900); margin-top: 2px; word-break: break-all; }
</style>

<div class="pg-premium p-0">
    <div class="page-hero">
        <div class="hero-container">
            <div class="hero-title-group">
                <h1 class="hero-title">Yeni Arıza Kaydı</h1>
                <p class="hero-subtitle">Sisteme yeni bir arıza kaydı ekleyin.</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <form action="{{ route('tesis-bilgi-sistemi.arizalar.store') }}" method="POST" id="arizaForm">
            @csrf
            <input type="hidden" name="abone_id" id="abone_id" value="{{ old('abone_id') }}">
            <input type="hidden" name="abone_no" id="abone_no_hidden" value="{{ old('abone_no') }}">
            <input type="hidden" name="ilce" id="ilce_hidden" value="{{ old('ilce') }}">
            <input type="hidden" name="mahalle" id="mahalle_hidden" value="{{ old('mahalle') }}">
            <input type="hidden" name="sokak" id="sokak_hidden" value="{{ old('sokak') }}">
            <input type="hidden" name="sayac_no" id="sayac_no_hidden" value="{{ old('sayac_no') }}">
            <input type="hidden" name="cbs_x" id="cbs_x_hidden" value="{{ old('cbs_x') }}">
            <input type="hidden" name="cbs_y" id="cbs_y_hidden" value="{{ old('cbs_y') }}">

            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="glass-card" style="padding:24px;">
                        <div class="section-header" style="margin-bottom:16px;">
                            <i class="fas fa-search"></i> Kuyu Ara
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kuyu Numarası</label>
                            <input type="text" name="kuyu_no" id="kuyu_no" class="form-control-pro" value="{{ old('kuyu_no') }}" placeholder="Kuyu no ile ara" autocomplete="off" style="font-size:1rem; font-weight:600;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Abone No</label>
                            <input type="text" name="abone_no" id="abone_no" class="form-control-pro" value="{{ old('abone_no') }}" placeholder="Abone no ile ara" autocomplete="off" style="font-size:1rem; font-weight:600;">
                        </div>
                        <div id="kuyuCheckResult" style="font-size:0.85rem; font-weight:600;"></div>
                    </div>
                </div>

                <div class="col-lg-7 mb-4">
                    <div class="kuyu-card" id="kuyuCard">
                            <div class="kc-header">
                                <div class="kc-icon"><i class="fas fa-water"></i></div>
                                <div>
                                    <div class="kc-title">Kuyu #<span id="cardKuyuNo">-</span></div>
                                    <div class="kc-subtitle"><span id="cardIlce">-</span> / <span id="cardAdres">-</span></div>
                                </div>
                                <div style="margin-left:auto;">
                                    <span id="cardDurumBadge" class="badge" style="padding:4px 12px; border-radius:20px; font-size:0.7rem; font-weight:700;"></span>
                                </div>
                            </div>
                            <div class="kuyu-grid">
                                <div class="kg-item"><div class="kg-label">Abone No</div><div class="kg-value" id="cardAboneNo">-</div></div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="glass-card" id="arizaFormSection" style="display:none;">
                <div class="section-header">
                    <i class="fas fa-tools"></i> Arıza Bilgileri
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Tarih</label>
                        <input type="date" name="tarih" class="form-control-pro" value="{{ old('tarih', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Ekip</label>
                        <select name="ekip" class="form-control-pro">
                            <option value="">— Ekip Seçiniz —</option>
                            @foreach($ekipler as $e)
                            <option value="{{ $e->ad }}" {{ old('ekip') == $e->ad ? 'selected' : '' }}>{{ $e->ad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label class="form-label">Tutanak No</label>
                        <input type="text" name="tutanak_no" class="form-control-pro" value="{{ old('tutanak_no') }}" placeholder="Varsa tutanak numarası">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Arıza Türleri (Birden fazla seçebilirsiniz)</label>
                        <input type="hidden" name="ariza_turu" id="arizaTuruInput" value="{{ old('ariza_turu') }}">
                        <div class="selected-chips" id="selectedChips"></div>
                        <div style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid #e2e8f0; border-radius: 14px; background: #fff;">
                            @foreach($arizaTurleri as $at)
                            <span class="chip" data-value="{{ $at->ad }}">{{ $at->ad }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Açıklama / Notlar</label>
                        <textarea name="aciklama" class="form-control-pro" rows="3" placeholder="Arıza ile ilgili ek açıklama...">{{ old('aciklama') }}</textarea>
                    </div>
                </div>

                @if ($errors->any())
                <div class="glass-card-sm" style="margin-bottom: 16px;">
                    <div class="alert alert-danger" style="border-radius:14px; margin:0;">
                        <ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    </div>
                </div>
                @endif

                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 8px;">
                    <a href="{{ route('tesis-bilgi-sistemi.arizalar') }}" class="btn-pro btn-outline-pro">İptal</a>
                    <button type="submit" class="btn-pro btn-primary-pro" id="submitBtn" disabled><i class="fas fa-save"></i> Arıza Kaydını Oluştur</button>
                </div>
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

        @if(old('ariza_turu'))
        var oldValues = '{{ old("ariza_turu") }}'.split(',').map(function(v) { return v.trim(); });
        oldValues.forEach(function(val) {
            if (val) {
                document.querySelectorAll('.chip').forEach(function(c) {
                    if (c.dataset.value === val) c.classList.add('active');
                });
            }
        });
        updateArizaTurleri();
        @endif

        var kuyuInput = document.getElementById('kuyu_no');
        var aboneInput = document.getElementById('abone_no');
        var checkResult = document.getElementById('kuyuCheckResult');
        var kuyuCard = document.getElementById('kuyuCard');
        var arizaFormSection = document.getElementById('arizaFormSection');
        var submitBtn = document.getElementById('submitBtn');
        var checkTimer;
        var kuyuValid = false;

        function resetKuyu() {
            kuyuCard.style.display = 'none';
            arizaFormSection.style.display = 'none';
            kuyuValid = false;
            submitBtn.disabled = true;
        }

        function fillCard(data) {
            document.getElementById('cardKuyuNo').textContent = data.kuyu_no;
            document.getElementById('cardIlce').textContent = data.ilce || '-';
            document.getElementById('cardAdres').textContent = data.adres || '-';
            document.getElementById('cardAboneNo').textContent = data.abone_no || '-';
            var badge = document.getElementById('cardDurumBadge');
            if (data.durum === 'aktif') {
                badge.textContent = 'Aktif';
                badge.style.background = '#d1fae5';
                badge.style.color = '#065f46';
            } else {
                badge.textContent = 'Pasif';
                badge.style.background = '#fee2e2';
                badge.style.color = '#991b1b';
            }
            kuyuCard.style.display = 'block';
            arizaFormSection.style.display = 'block';
        }

        function lookupKuyu(params) {
            clearTimeout(checkTimer);
            var qs = Object.keys(params).map(function(k) { return k + '=' + encodeURIComponent(params[k]); }).join('&');
            if (!qs) { checkResult.innerHTML = ''; resetKuyu(); return; }
            checkResult.innerHTML = '<span style="color:#8b5cf6;"><i class="fas fa-spinner fa-pulse"></i> Aranıyor...</span>';
            checkTimer = setTimeout(function() {
                fetch('{{ route("tesis-bilgi-sistemi.arizalar.kuyu-data") }}?' + qs)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data && data.kuyu_no) {
                            checkResult.innerHTML = '<span style="color:#059669;"><i class="fas fa-check-circle"></i> Kuyu bulundu</span>';
                            fillCard(data);
                            kuyuInput.value = data.kuyu_no;
                            aboneInput.value = data.abone_no || '';
                            document.getElementById('abone_no_hidden').value = data.abone_no || '';
                            document.getElementById('ilce_hidden').value = data.ilce || '';
                            document.getElementById('mahalle_hidden').value = data.adres || '';
                            kuyuValid = true;
                            submitBtn.disabled = false;
                        } else {
                            checkResult.innerHTML = '<span style="color:#dc2626;"><i class="fas fa-exclamation-circle"></i> Bu kuyu sisteme kayıtlı değil</span>';
                            resetKuyu();
                        }
                    })
                    .catch(function() {
                        checkResult.innerHTML = '<span style="color:#f59e0b;"><i class="fas fa-exclamation-triangle"></i> Kontrol edilemedi</span>';
                    });
            }, 500);
        }

        kuyuInput.addEventListener('input', function() {
            var val = this.value.trim();
            if (!val) { checkResult.innerHTML = ''; resetKuyu(); return; }
            aboneInput.value = '';
            lookupKuyu({ kuyu_no: val });
        });

        aboneInput.addEventListener('input', function() {
            var val = this.value.trim();
            if (!val) { checkResult.innerHTML = ''; resetKuyu(); return; }
            kuyuInput.value = '';
            lookupKuyu({ abone_no: val });
        });

        @if(old('kuyu_no'))
        if (kuyuInput.value.trim()) { kuyuInput.dispatchEvent(new Event('input')); }
        @elseif(old('abone_no'))
        if (aboneInput.value.trim()) { aboneInput.dispatchEvent(new Event('input')); }
        @endif
    });
</script>
@endpush
@endsection
