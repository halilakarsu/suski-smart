@extends('frontend.layouts.app')

@section('content')
<style>
    .page-hero{background:linear-gradient(125deg,#0f172a 0%,#064e3b 100%);position:relative;padding:4rem 2rem 8rem;margin-top:-30px!important;color:#fff;overflow:hidden;border-bottom-left-radius:40px;border-bottom-right-radius:40px}
    .page-hero::before{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(circle,rgba(16,185,129,.25) 0%,transparent 70%);top:-150px;left:-100px;border-radius:50%;filter:blur(60px);pointer-events:none}
    .hero-container{position:relative;z-index:10;width:100%;max-width:1200px;margin:0 auto}
    .hero-title-group h1{font-size:2rem;font-weight:800;letter-spacing:-.04em;background:linear-gradient(to right,#fff,#6ee7b7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:.3rem}
    .hero-subtitle{color:#94a3b8;font-size:.95rem}
    .main-container{width:100%;max-width:1200px;margin:-4rem auto 0;padding:0 2rem;position:relative;z-index:20}
    .glass-card{background:rgba(255,255,255,.9);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.7);border-radius:24px;padding:32px;box-shadow:0 20px 40px -10px rgba(0,0,0,.08);margin-bottom:24px}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
    .form-grid.col3{grid-template-columns:1fr 1fr 1fr}
    .form-group{display:flex;flex-direction:column;gap:6px}
    .form-group label{font-size:.82rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px}
    .form-group input,.form-group select,.form-group textarea{padding:10px 14px;border-radius:12px;border:1.5px solid #e2e8f0;background:#fff;font-size:.9rem;color:#1e293b;outline:none;transition:border-color .2s,box-shadow .2s;width:100%;box-sizing:border-box}
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.12)}
    .form-group textarea{resize:vertical;min-height:80px}
    .section-header{font-size:.9rem;font-weight:700;color:#0f172a;padding:10px 0;margin:24px 0 16px;border-bottom:2px solid #f1f5f9;display:flex;align-items:center;gap:8px}
    .section-header i{color:#10b981}
    .btn{padding:10px 22px;border-radius:12px;border:none;font-weight:600;cursor:pointer;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:transform .15s,box-shadow .15s}
    .btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.12)}
    .btn-primary{background:linear-gradient(135deg,#10b981,#059669);color:#fff}
    .btn-secondary{background:#f1f5f9;color:#475569;border:1px solid #e2e8f0}
    .form-footer{display:flex;gap:12px;justify-content:flex-end;margin-top:28px;padding-top:20px;border-top:1px solid #f1f5f9}
    @media(max-width:640px){.form-grid,.form-grid.col3{grid-template-columns:1fr}}
</style>

<div class="page-hero">
    <div class="hero-container">
        <div class="hero-title-group">
            <h1>✏️ Kuyu Düzenle — #{{ $kuyu->kuyu_no ?? $kuyu->id }}</h1>
            <p class="hero-subtitle">Kuyu Envanteri / Düzenleme</p>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="glass-card">
        <form action="{{ route('kuyu-envanteri.update', $kuyu) }}" method="POST">
            @csrf @method('PUT')

            {{-- Temel Bilgiler --}}
            <div class="section-header"><i class="fas fa-info-circle"></i> Temel Bilgiler</div>
            <div class="form-grid col3">
                <div class="form-group">
                    <label>Kuyu Numarası</label>
                    <input type="text" name="kuyu_no" value="{{ old('kuyu_no', $kuyu->kuyu_no) }}" placeholder="ör: 100">
                </div>
                <div class="form-group">
                    <label>Abone No / Tesisat No</label>
                    <input type="text" name="abone_no" value="{{ old('abone_no', $kuyu->abone_no) }}" placeholder="Tesist no">
                </div>
                <div class="form-group">
                    <label>İlçe</label>
                    <input type="text" name="ilce" list="ilce-list" value="{{ old('ilce', $kuyu->ilce) }}" placeholder="İlçe adı">
                    <datalist id="ilce-list">
                        @foreach($ilceler as $il)
                            <option value="{{ $il }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group">
                    <label>Durum</label>
                    <select name="durum">
                        <option value="aktif" {{ old('durum', $kuyu->durum) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="pasif" {{ old('durum', $kuyu->durum) == 'pasif' ? 'selected' : '' }}>Pasif</option>
                    </select>
                </div>
            </div>

            <div class="form-grid" style="margin-top:16px">
                <div class="form-group" style="grid-column:1/-1">
                    <label>Adres</label>
                    <input type="text" name="adres" value="{{ old('adres', $kuyu->adres) }}" placeholder="Mahalle / köy / bölge">
                </div>
            </div>

            {{-- CBS Koordinatları --}}
            <div class="section-header"><i class="fas fa-map-marker-alt"></i> CBS Koordinatları</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>CBS X Koordinatı</label>
                    <input type="number" step="0.000001" name="cbs_x" value="{{ old('cbs_x', $kuyu->cbs_x) }}" placeholder="ör: 28.123456">
                </div>
                <div class="form-group">
                    <label>CBS Y Koordinatı</label>
                    <input type="number" step="0.000001" name="cbs_y" value="{{ old('cbs_y', $kuyu->cbs_y) }}" placeholder="ör: 41.123456">
                </div>
            </div>

            {{-- Teknik Bilgiler --}}
            <div class="section-header"><i class="fas fa-cogs"></i> Teknik Bilgiler</div>
            <div class="form-grid col3">
                <div class="form-group">
                    <label>Demontaj Derinliği (m)</label>
                    <input type="number" step="0.01" name="demontaj_derinligi" value="{{ old('demontaj_derinligi', $kuyu->demontaj_derinligi) }}" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Montaj Derinliği (m)</label>
                    <input type="number" step="0.01" name="montaj_derinligi" value="{{ old('montaj_derinligi', $kuyu->montaj_derinligi) }}" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Debi</label>
                    <input type="text" name="debi" value="{{ old('debi', $kuyu->debi) }}" placeholder="ör: 25 L/dk">
                </div>
            </div>

            <div class="form-grid col3" style="margin-top:16px">
                <div class="form-group">
                    <label>Boru Tipi</label>
                    <input type="text" name="boru_tipi" value="{{ old('boru_tipi', $kuyu->boru_tipi) }}">
                </div>
                <div class="form-group">
                    <label>Kablo</label>
                    <input type="text" name="kablo" value="{{ old('kablo', $kuyu->kablo) }}">
                </div>
                <div class="form-group">
                    <label>Depo Bilgisi</label>
                    <input type="text" name="depo_bilgisi" value="{{ old('depo_bilgisi', $kuyu->depo_bilgisi) }}">
                </div>
            </div>

            <div class="form-grid" style="margin-top:16px">
                <div class="form-group">
                    <label>Motor</label>
                    <input type="text" name="motor" value="{{ old('motor', $kuyu->motor) }}">
                </div>
                <div class="form-group">
                    <label>Pompa</label>
                    <input type="text" name="pompa" value="{{ old('pompa', $kuyu->pompa) }}">
                </div>
            </div>

            {{-- Tarihler --}}
            <div class="section-header"><i class="fas fa-calendar"></i> Tarihler</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Oluşturulma Tarihi</label>
                    <input type="datetime-local" name="olusturulma_tarihi"
                           value="{{ old('olusturulma_tarihi', $kuyu->olusturulma_tarihi ? $kuyu->olusturulma_tarihi->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="form-group">
                    <label>Güncellenme Tarihi</label>
                    <input type="datetime-local" name="guncellenme_tarihi"
                           value="{{ old('guncellenme_tarihi', $kuyu->guncellenme_tarihi ? $kuyu->guncellenme_tarihi->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>

            {{-- Açıklama --}}
            <div class="section-header"><i class="fas fa-comment"></i> Açıklama / Notlar</div>
            <div class="form-group">
                <textarea name="aciklama" placeholder="Ek açıklama, notlar…">{{ old('aciklama', $kuyu->aciklama) }}</textarea>
            </div>

            <div class="form-footer">
                <a href="{{ route('kuyu-envanteri.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Geri</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Güncelle</button>
            </div>
        </form>
    </div>
</div>
@endsection
