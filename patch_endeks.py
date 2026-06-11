import sys

with open('/Users/akarsu/Desktop/suski/resources/views/reports/endeks.blade.php', 'r') as f:
    content = f.read()

# 1. Hero Section Button
content = content.replace('''            <div class="d-flex gap-2 align-items-center">
                <div class="dropdown" id="exportBtnContainer" style="display: {{ request()->anyFilled(['bolge','start_period','end_period','tesisat_no','tarife','baglanti_grubu','yerlesim_tipi']) ? 'block' : 'none' }};">''', '''            <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn-pro btn-outline-pro ml-2" data-toggle="modal" data-target="#pdfAnalizModal" style="background: rgba(255,255,255,0.15); color: white; border-color: rgba(255,255,255,0.3); box-shadow: none;">
                    <i class="fas fa-file-pdf text-danger"></i> Pdf-Fatura Analiz
                </button>
                <div class="dropdown" id="exportBtnContainer" style="display: {{ request()->anyFilled(['bolge','start_period','end_period','tesisat_no','tarife','baglanti_grubu','yerlesim_tipi']) ? 'block' : 'none' }};">''')

# 2. Delete old PDF KARŞILAŞTIRMA html and style
start_str = "        {{-- PDF KARŞILAŞTIRMA --}}"
end_str = "        </style>"
start_idx = content.find(start_str)
end_idx = content.find(end_str, start_idx) + len(end_str)
if start_idx != -1 and end_idx != -1:
    content = content[:start_idx] + content[end_idx:]

# 3. Replace JS logic
js_start_str = "    /* ─── PDF Karşılaştırma (Client-side, progress bar ile) ─── */"
js_end_str = "    });\\n\\n});"
js_start_idx = content.find(js_start_str)
js_end_idx = content.find("});", content.find("    document.getElementById('pdfOverlayClose').addEventListener('click', function() {")) + 3

if js_start_idx != -1 and js_end_idx != -1:
    new_js = """    /* ─── PDF Karşılaştırma (Modal & Professional Progress) ─── */
    var pdfAnalizFiles = [];
    var pdfAnalizFaturaSet = {};
    var pdfFolderSelected = false;

    // Progress Modal Elements
    const pdfProgModal   = document.getElementById('pdfProgModal');
    const pdfPmFill      = document.getElementById('pdfPmFill');
    const pdfPmStripFill = document.getElementById('pdfPmStripFill');
    const pdfPmStripDot  = document.getElementById('pdfPmStripDot');
    const pdfPmPerc      = document.getElementById('pdfPmPerc');
    const pdfPmHint      = document.getElementById('pdfPmHint');
    const pdfPmIcon      = document.getElementById('pdfPmIcon');
    const pdfPmTitle     = document.getElementById('pdfPmTitle');
    const pdfPmSub       = document.getElementById('pdfPmSub');
    const pdfPmStep1     = document.getElementById('pdfPmStep1');
    const pdfPmStep2     = document.getElementById('pdfPmStep2');
    const pdfPmStep3     = document.getElementById('pdfPmStep3');
    const pdfPmLine1     = document.getElementById('pdfPmLine1');
    const pdfPmLine2     = document.getElementById('pdfPmLine2');

    let pdfDisplayedPct = 0;
    let pdfAnimFrame    = null;

    function setPdfProgress(targetPct, textHint) {
        if (pdfAnimFrame) cancelAnimationFrame(pdfAnimFrame);
        function step() {
            const diff  = targetPct - pdfDisplayedPct;
            const speed = Math.max(0.4, Math.abs(diff) * 0.1);
            if (Math.abs(diff) < 0.3) { pdfDisplayedPct = targetPct; }
            else                       { pdfDisplayedPct += speed; }
            const p      = Math.min(100, pdfDisplayedPct);
            const pRound = Math.round(p);

            pdfPmFill.style.width      = p + '%';
            pdfPmStripFill.style.width = p + '%';
            pdfPmStripDot.style.left   = 'calc(' + p + '% - 6px)';
            pdfPmPerc.textContent      = pRound + '%';
            if (textHint) pdfPmHint.textContent = textHint;

            if (pRound >= 100)     pdfPmPerc.style.color = '#15803d';
            else if (pRound >= 60) pdfPmPerc.style.color = '#059669';
            else                   pdfPmPerc.style.color = '#1a5f8a';

            if (pdfDisplayedPct < targetPct) pdfAnimFrame = requestAnimationFrame(step);
        }
        pdfAnimFrame = requestAnimationFrame(step);
    }

    function setPdfPhase(phase) {
        if (phase === 'fetch') {
            pdfPmStep1.className = 'pm-step pm-step-active';
            pdfPmStep2.className = 'pm-step';
            pdfPmStep3.className = 'pm-step';
            pdfPmLine1.classList.remove('pm-line-done');
            pdfPmLine2.classList.remove('pm-line-done');
            pdfPmIcon.className    = 'pm-icon';
            pdfPmIcon.innerHTML    = '<i class="fas fa-cloud-download-alt"></i>';
            pdfPmTitle.textContent = 'Veriler Alınıyor';
            pdfPmSub.textContent   = 'Sistemdeki hamveri kayıtları getiriliyor...';
        } else if (phase === 'match') {
            pdfPmStep1.className = 'pm-step pm-step-done';
            pdfPmStep2.className = 'pm-step pm-step-active';
            pdfPmStep3.className = 'pm-step';
            pdfPmLine1.classList.add('pm-line-done');
            pdfPmLine2.classList.remove('pm-line-done');
            pdfPmIcon.className    = 'pm-icon pm-icon-proc';
            pdfPmIcon.innerHTML    = '<i class="fas fa-cogs fa-spin"></i>';
            pdfPmTitle.textContent = 'PDF\\'ler Eşleştiriliyor';
        } else if (phase === 'done') {
            pdfPmStep1.className = 'pm-step pm-step-done';
            pdfPmStep2.className = 'pm-step pm-step-done';
            pdfPmStep3.className = 'pm-step pm-step-active';
            pdfPmLine1.classList.add('pm-line-done');
            pdfPmLine2.classList.add('pm-line-done');
            pdfPmIcon.className    = 'pm-icon pm-icon-done';
            pdfPmIcon.innerHTML    = '<i class="fas fa-check-double"></i>';
            pdfPmTitle.textContent = 'Analiz Tamamlandı!';
            pdfPmSub.textContent   = 'Sonuçlar hazırlanıyor...';
        }
    }

    function pdfMatchFilename(filename, set) {
        var name = filename.replace(/\\.pdf$/i, '').trim();
        if (set[name]) return name;
        
        var alphanums = name.match(/[a-zA-Z0-9]+/g);
        if (alphanums) {
            for (var i = 0; i < alphanums.length; i++) {
                if (set[alphanums[i]]) return alphanums[i];
            }
        }
        return null;
    }

    function showPdfAnalizSonuc(eslesenList, eslesmeyenPdf, sistemdeOlan) {
        document.getElementById('pdfAnalizSonucArea').style.display = 'block';
        
        var ozetDiv = document.getElementById('pdfAnalizOzet');
        ozetDiv.innerHTML = 
            '<span class="badge-pdf success"><i class="fas fa-check"></i> ' + eslesenList.length + ' Eşleşti</span>' +
            '<span class="badge-pdf error"><i class="fas fa-times"></i> ' + eslesmeyenPdf.length + ' Eşleşmeyen PDF</span>' +
            '<span class="badge-pdf warning"><i class="fas fa-exclamation-triangle"></i> ' + sistemdeOlan.length + ' Sistemde PDF\\'si Yok</span>';

        var tbody = document.getElementById('pdfAnalizTableBody');
        var html = '';

        eslesmeyenPdf.forEach(function(f) {
            html += '<tr>' +
                '<td><span class="badge-pdf error">PDF Bulunamadı</span></td>' +
                '<td style="font-family:monospace;">' + f + '</td>' +
                '<td style="color:#64748b;">Klasörde var ancak sistemde (Hamveri) eşleşen bir fatura bulunamadı.</td>' +
                '</tr>';
        });

        sistemdeOlan.forEach(function(f) {
            html += '<tr>' +
                '<td><span class="badge-pdf warning">Sistemde Eksik</span></td>' +
                '<td style="font-family:monospace;">' + f + '</td>' +
                '<td style="color:#64748b;">Sistemde (Hamveri) kayıtlı, ancak klasörde karşılık gelen bir PDF dosyası bulunamadı.</td>' +
                '</tr>';
        });

        if (!eslesmeyenPdf.length && !sistemdeOlan.length) {
            html = '<tr><td colspan="3" class="text-center py-4" style="color:#16a34a;font-weight:700;"><i class="fas fa-check-circle mr-2" style="font-size:1.5rem;vertical-align:middle;"></i> Harika! Tüm PDF\\'ler sistemdeki faturalarla eksiksiz eşleşti.</td></tr>';
        }

        tbody.innerHTML = html;
    }

    document.getElementById('pdfFolderInput').addEventListener('change', function(e) {
        var files = e.target.files;
        if (!files.length) return;

        var pdfs = [];
        for (var i = 0; i < files.length; i++) {
            if (files[i].name.toLowerCase().endsWith('.pdf')) {
                pdfs.push(files[i].name);
            }
        }

        if (!pdfs.length) {
            Swal.fire({ icon: 'warning', title: 'Uyarı', text: 'Seçilen klasörde PDF dosyası bulunamadı.', confirmButtonText: 'Tamam' });
            return;
        }

        pdfAnalizFiles = pdfs;
        pdfFolderSelected = true;
        document.getElementById('pdfAnalizBaslaBtn').disabled = false;

        document.getElementById('pdfAnalizFolderInfo').style.display = 'block';
        document.getElementById('pdfAnalizFileCount').textContent = pdfs.length;
        document.getElementById('pdfDropZone').style.borderColor = '#3b82f6';
        document.getElementById('pdfDropZone').style.background = '#eff6ff';
    });

    document.getElementById('pdfAnalizBaslaBtn').addEventListener('click', function() {
        if (!pdfFolderSelected || !pdfAnalizFiles.length) {
            Swal.fire({ icon: 'warning', title: 'Uyarı', text: 'Lütfen önce Gözat ile bir klasör seçin.', confirmButtonText: 'Tamam' });
            return;
        }

        var donem = document.getElementById('pdfAnalizDonem').value;
        if (!donem) {
            Swal.fire({ icon: 'warning', title: 'Uyarı', text: 'Lütfen önce karşılaştırma dönemi seçin.', confirmButtonText: 'Tamam' });
            return;
        }

        pdfDisplayedPct = 0;
        setPdfProgress(0, '0 / 0');
        setPdfPhase('fetch');
        pdfProgModal.classList.add('pm-show');

        document.getElementById('pdfAnalizSonucArea').style.display = 'none';
        document.getElementById('pdfAnalizTableBody').innerHTML = '';

        fetch('/raporlar/endeks/pdf-karsilastir/faturalar/' + encodeURIComponent(donem))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) throw new Error('Veri alınamadı');

                pdfAnalizFaturaSet = {};
                data.faturalar.forEach(function(f) { pdfAnalizFaturaSet[f] = true; });

                setPdfPhase('match');
                pdfPmSub.textContent = pdfAnalizFiles.length + ' adet PDF dosyası analiz ediliyor...';
                
                var eslesenList = [];
                var eslesmeyenPdf = [];
                var toplam = pdfAnalizFiles.length;
                var sira = 0;

                function sonraki() {
                    if (sira >= toplam) {
                        var eslenenSet = {};
                        eslesenList.forEach(function(e) { eslenenSet[e] = true; });
                        var sistemdeOlan = data.faturalar.filter(function(f) { return !eslenenSet[f]; });

                        setPdfPhase('done');
                        setPdfProgress(100, toplam + ' / ' + toplam + ' tamamlandı');
                        
                        setTimeout(function() {
                            pdfProgModal.classList.remove('pm-show');
                            showPdfAnalizSonuc(eslesenList, eslesmeyenPdf, sistemdeOlan);
                        }, 1200);
                        return;
                    }

                    var chunkEnd = Math.min(sira + 20, toplam);
                    for (; sira < chunkEnd; sira++) {
                        var fname = pdfAnalizFiles[sira];
                        var matched = pdfMatchFilename(fname, pdfAnalizFaturaSet);
                        if (matched) {
                            eslesenList.push(matched);
                        } else {
                            eslesmeyenPdf.push(fname);
                        }
                    }

                    var pct = Math.round((sira / toplam) * 100);
                    setPdfProgress(pct, sira + ' / ' + toplam + ' analiz edildi');
                    setTimeout(sonraki, 20);
                }

                sonraki();
            })
            .catch(function(err) {
                console.error(err);
                pdfProgModal.classList.remove('pm-show');
                Swal.fire({ icon: 'error', title: 'Hata', text: 'Hamveri kayıtları alınırken bir hata oluştu.', confirmButtonText: 'Tamam' });
            });
    });"""
    content = content[:js_start_idx] + new_js + content[js_end_idx:]

# 4. Replace old overlay HTML with Modals
html_start_str = "{{-- ═══ PDF Karşılaştırma Progress Overlay ═══ --}}"
html_end_str = "@endsection"
html_start_idx = content.find(html_start_str)
html_end_idx = content.find(html_end_str, html_start_idx)
if html_start_idx != -1 and html_end_idx != -1:
    new_html = """<style>
/* ══════════════════════════════════════════════════════
   Progress Modal — Version-1 Premium Design
   ══════════════════════════════════════════════════════ */
.pm-backdrop {
  position:fixed;inset:0;z-index:999999;
  background:rgba(5,14,26,.86);
  backdrop-filter:blur(10px) saturate(1.4);
  display:flex;align-items:center;justify-content:center;padding:1rem;
  opacity:0;pointer-events:none;
  transition:opacity .35s cubic-bezier(.4,0,.2,1);
}
.pm-backdrop.pm-show{opacity:1;pointer-events:auto;}

.pm-box{
  background:#fff;border-radius:24px;
  box-shadow:0 40px 100px rgba(0,0,0,.38),0 0 0 1px rgba(255,255,255,.05);
  width:100%;max-width:440px;overflow:hidden;
  transform:translateY(40px) scale(.92);
  transition:transform .42s cubic-bezier(.34,1.38,.64,1);
}
.pm-backdrop.pm-show .pm-box{transform:translateY(0) scale(1);}

.pm-strip{width:100%;height:5px;background:rgba(26,95,138,.1);position:relative;}
.pm-strip-fill{
  position:absolute;top:0;left:0;height:100%;width:0%;
  background:linear-gradient(90deg,#1a5f8a 0%,#3a9fd6 45%,#22c55e 100%);
  background-size:200% 100%;
  animation:strip-flow 2.2s linear infinite;
  transition:width .45s cubic-bezier(.4,0,.2,1);
  border-radius:0 3px 3px 0;
}
@keyframes strip-flow{0%{background-position:0% 0}100%{background-position:200% 0}}

.pm-strip-dot{
  position:absolute;top:50%;left:0;
  width:12px;height:12px;border-radius:50%;
  background:#3a9fd6;transform:translateY(-50%);
  box-shadow:0 0 0 4px rgba(58,159,214,.25);
  transition:left .45s cubic-bezier(.4,0,.2,1);
  animation:dot-pulse 1.8s ease-in-out infinite;
}
@keyframes dot-pulse{
  0%,100%{box-shadow:0 0 0 4px rgba(58,159,214,.25);}
  50%{box-shadow:0 0 0 9px rgba(58,159,214,.08);}
}

.pm-body{padding:2.25rem 2rem 1.75rem;text-align:center;}

.pm-icon-wrap{
  width:90px;height:90px;position:relative;
  margin:0 auto 1.5rem;
  display:flex;align-items:center;justify-content:center;
}
.pm-icon-ring{
  position:absolute;inset:0;border-radius:50%;
  border:2px solid rgba(26,95,138,.18);
  animation:ring-out 2.2s ease-out infinite;
}
.pm-ring2{animation-delay:1.1s;}
@keyframes ring-out{
  0%{transform:scale(.82);opacity:0;}
  35%{opacity:1;}
  100%{transform:scale(1.6);opacity:0;}
}
.pm-icon{
  width:72px;height:72px;border-radius:50%;
  background:linear-gradient(135deg,#1a5f8a 0%,#3a9fd6 100%);
  display:flex;align-items:center;justify-content:center;
  font-size:1.75rem;color:#fff;position:relative;z-index:2;
  box-shadow:0 12px 36px rgba(26,95,138,.38);
  transition:background .55s ease,box-shadow .55s ease;
}
.pm-icon.pm-icon-proc{
  background:linear-gradient(135deg,#059669 0%,#10b981 100%);
  box-shadow:0 12px 36px rgba(5,150,105,.38);
}
.pm-icon.pm-icon-done{
  background:linear-gradient(135deg,#15803d 0%,#22c55e 100%);
  box-shadow:0 12px 36px rgba(21,128,61,.45);
  animation:done-pop .4s cubic-bezier(.34,1.56,.64,1) forwards;
}
@keyframes done-pop{0%{transform:scale(.8);}100%{transform:scale(1);}}

.pm-title{font-size:1.25rem;font-weight:800;color:#1a2e3b;margin-bottom:.35rem;}
.pm-sub{font-size:.84rem;color:#4a6a7a;font-weight:500;margin-bottom:1.75rem;min-height:2.2em;}

.pm-track{
  background:rgba(26,95,138,.08);border-radius:40px;
  height:13px;width:100%;overflow:hidden;
  position:relative;margin-bottom:.7rem;
}
.pm-fill{
  height:100%;width:0%;
  background:linear-gradient(90deg,#1a5f8a 0%,#3a9fd6 50%,#22c55e 100%);
  background-size:200% 100%;border-radius:40px;
  transition:width .45s cubic-bezier(.4,0,.2,1);
  position:relative;overflow:hidden;
  animation:fill-flow 2.5s linear infinite;
}
@keyframes fill-flow{0%{background-position:0% 0}100%{background-position:200% 0}}
.pm-shine{
  position:absolute;inset:0;
  background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,.38) 50%,transparent 100%);
  transform:translateX(-100%);
  animation:shine-sweep 2s ease-in-out infinite;
}
@keyframes shine-sweep{0%{transform:translateX(-100%);}100%{transform:translateX(300%);}}

.pm-perc-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;}
.pm-perc{font-size:1.6rem;font-weight:900;color:#1a5f8a;letter-spacing:-1px;transition:color .4s;}
.pm-hint{font-size:.68rem;color:#90aab8;font-weight:500;}

.pm-steps{display:flex;align-items:center;justify-content:center;}
.pm-step{
  display:flex;flex-direction:column;align-items:center;
  gap:5px;font-size:.65rem;font-weight:600;color:#90aab8;
  transition:color .4s;min-width:76px;
}
.pm-step-dot{
  width:10px;height:10px;border-radius:50%;
  background:#dde8f0;border:2px solid #dde8f0;
  transition:all .4s cubic-bezier(.34,1.38,.64,1);
}
.pm-step.pm-step-active{color:#1a5f8a;}
.pm-step.pm-step-active .pm-step-dot{
  background:#3a9fd6;border-color:#3a9fd6;
  box-shadow:0 0 0 5px rgba(58,159,214,.2);
  transform:scale(1.35);
}
.pm-step.pm-step-done{color:#15803d;}
.pm-step.pm-step-done .pm-step-dot{
  background:#22c55e;border-color:#22c55e;
  box-shadow:0 0 0 5px rgba(34,197,94,.2);
}
.pm-step-line{
  flex:1;height:2px;background:#e2ecf3;
  margin-bottom:18px;max-width:48px;
  position:relative;overflow:hidden;
}
.pm-step-line::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,#3a9fd6,#22c55e);
  transform:translateX(-100%);transition:transform .6s ease;
}
.pm-step-line.pm-line-done::after{transform:translateX(0);}

.pro-table-pdf {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
}
.pro-table-pdf th {
    color: #64748b;
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    padding: 0 20px 10px 20px;
    text-align: left;
    border-bottom: 2px solid #e2e8f0;
}
.pro-table-pdf td {
    padding: 16px 20px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-style: solid none;
    transition: all 0.2s;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
}
.pro-table-pdf tr td:first-child {
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
    border-left: 1px solid #f1f5f9;
}
.pro-table-pdf tr td:last-child {
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
    border-right: 1px solid #f1f5f9;
}
.pro-table-pdf tr:hover td {
    background: #eff6ff;
}
.badge-pdf {
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.badge-pdf.success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
.badge-pdf.error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.badge-pdf.warning { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }

.drop-zone-premium {
    border: 2px dashed #e2e8f0;
    border-radius: 20px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    transition: all 0.3s ease;
    cursor: pointer;
    text-align: center;
}
.drop-zone-premium:hover {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: scale(1.02);
}
.drop-zone-premium .upload-icon-box {
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #3b82f6;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 16px;
}
</style>

{{-- ═══ Pdf-Fatura Analiz Modal HTML ═══ --}}
<div class="modal fade" id="pdfAnalizModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.4);">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" style="border-radius:28px; border:1px solid rgba(255,255,255,0.2); overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.25); background: rgba(255, 255, 255, 0.98);">
            <div class="modal-header" style="background:linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 27, 75, 0.95)); border:none; padding:25px 35px; border-bottom: 1px solid rgba(255,255,255,0.1); position: relative;">
                <div>
                    <h5 class="modal-title" style="color:#fff; font-weight:800; font-size:1.35rem; margin:0; letter-spacing:-0.02em;">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:rgba(239,68,68,0.2);border-radius:12px;margin-right:12px;color:#f87171;"><i class="fas fa-file-pdf"></i></div>
                        Pdf-Fatura Analiz (Eşleştirme)
                    </h5>
                    <p style="color:#94a3b8; font-size:0.85rem; margin:8px 0 0 50px; font-weight:500;">Klasördeki PDF dosyalarını sistemdeki yüklenmiş hamverilerle tek tek eşleştirin.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; font-size:1.6rem; background:rgba(255,255,255,0.1); border:none; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.2s; margin-top:-10px;">
                    <span aria-hidden="true" style="margin-top:-2px;">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding:35px; max-height:75vh; overflow-y:auto; background:#f4f6f9;">
                
                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="form-group-pro">
                            <label><i class="far fa-calendar-alt"></i> Karşılaştırma Dönemi</label>
                            <select id="pdfAnalizDonem" class="form-control-pro" style="height:50px;font-size:1rem;border-radius:14px;">
                                <option value="">Dönem Seçiniz</option>
                                @foreach($donemler as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-top: 20px;">
                            <button type="button" id="pdfAnalizBaslaBtn" class="btn-pro w-100 justify-content-center" style="background: linear-gradient(135deg, #2563eb, #4f46e5); color: #fff; height: 55px; border-radius: 16px; font-size: 1.05rem; box-shadow: 0 10px 25px -5px rgba(37,99,235,0.4);" disabled>
                                <i class="fas fa-play-circle mr-2"></i> Analize Başla
                            </button>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="drop-zone-premium" id="pdfDropZone" onclick="document.getElementById('pdfFolderInput').click()">
                            <div class="upload-icon-box"><i class="fas fa-folder-open"></i></div>
                            <h5 style="font-weight: 800; color: #1e293b; margin-bottom: 4px;">PDF Klasörünü Seçin</h5>
                            <p style="color: #64748b; font-weight: 500; font-size: 0.85rem;">Tıklayarak içerisinde PDF'lerin bulunduğu klasörü seçin</p>
                            <input type="file" id="pdfFolderInput" webkitdirectory multiple style="display:none;">
                            
                            <div id="pdfAnalizFolderInfo" style="display:none; margin-top: 15px; padding: 10px 16px; background: white; border-radius: 12px; border: 1px solid #e2e8f0; font-weight: 700; color: #2563eb;">
                                <i class="fas fa-file-pdf"></i> <span id="pdfAnalizFileCount">0</span> PDF seçildi
                            </div>
                        </div>
                    </div>
                </div>

                <div id="pdfAnalizSonucArea" style="display:none; margin-top: 20px; background:#fff; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border:1px solid #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 style="font-weight: 800; color: #0f172a; margin: 0;"><i class="fas fa-clipboard-list text-primary mr-2"></i> Karşılaştırma Sonuçları</h5>
                        <div id="pdfAnalizOzet" class="d-flex gap-2"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="pro-table-pdf">
                            <thead>
                                <tr>
                                    <th style="width: 150px;">Durum</th>
                                    <th>Tanımlayıcı (ID / Dosya Adı)</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody id="pdfAnalizTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="pdfProgModal" class="pm-backdrop">
  <div class="pm-box">
    <div class="pm-strip">
      <div class="pm-strip-fill" id="pdfPmStripFill"></div>
      <div class="pm-strip-dot"  id="pdfPmStripDot"></div>
    </div>
    <div class="pm-body">
      <div class="pm-icon-wrap">
        <div class="pm-icon-ring"></div>
        <div class="pm-icon-ring pm-ring2"></div>
        <div class="pm-icon" id="pdfPmIcon"><i class="fas fa-sync-alt fa-spin"></i></div>
      </div>
      <div class="pm-title" id="pdfPmTitle">Analiz Yapılıyor</div>
      <div class="pm-sub"   id="pdfPmSub">Sistem kayıtları alınıyor...</div>
      <div class="pm-track">
        <div class="pm-fill" id="pdfPmFill"><div class="pm-shine"></div></div>
      </div>
      <div class="pm-perc-row">
        <span class="pm-perc" id="pdfPmPerc">0%</span>
        <span class="pm-hint" id="pdfPmHint">0 / 0 dosya</span>
      </div>
      <div class="pm-steps">
        <div class="pm-step pm-step-active" id="pdfPmStep1">
          <div class="pm-step-dot"></div><span>Sistem Verisi</span>
        </div>
        <div class="pm-step-line" id="pdfPmLine1"></div>
        <div class="pm-step" id="pdfPmStep2">
          <div class="pm-step-dot"></div><span>Eşleştirme</span>
        </div>
        <div class="pm-step-line" id="pdfPmLine2"></div>
        <div class="pm-step" id="pdfPmStep3">
          <div class="pm-step-dot"></div><span>Tamamlandı</span>
        </div>
      </div>
    </div>
  </div>
</div>
\n@endsection"""
    content = content[:html_start_idx] + new_html

with open('/Users/akarsu/Desktop/suski/resources/views/reports/endeks.blade.php', 'w') as f:
    f.write(content)

