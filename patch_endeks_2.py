import sys

with open('/Users/akarsu/Desktop/suski/resources/views/reports/endeks.blade.php', 'r') as f:
    content = f.read()

# 1. Remove inline result area from pdfAnalizModal
result_area_str = '<div id="pdfAnalizSonucArea"'
result_area_end_str = '</div>\n\n            </div>\n        </div>\n    </div>\n</div>\n\n<div id="pdfProgModal"'
idx1 = content.find(result_area_str)
idx2 = content.find('</div>\n\n            </div>\n        </div>\n    </div>\n</div>\n\n<div id="pdfProgModal"', idx1)
if idx1 != -1 and idx2 != -1:
    content = content[:idx1] + content[idx2:]


# 2. Add Detailed Results Modal at the end of HTML
new_modal_html = """

{{-- ═══ Detaylı Sonuçlar Modalı ═══ --}}
<div class="modal fade" id="pdfDetayliSonucModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.6);">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 95%;">
        <div class="modal-content" style="border-radius:28px; border:1px solid rgba(255,255,255,0.2); overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.3); background: #f8fafc;">
            <div class="modal-header" style="background:linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 27, 75, 0.95)); border:none; padding:25px 35px; border-bottom: 1px solid rgba(255,255,255,0.1); position: relative;">
                <div>
                    <h5 class="modal-title" style="color:#fff; font-weight:800; font-size:1.4rem; margin:0; letter-spacing:-0.02em;">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;background:rgba(59,130,246,0.2);border-radius:12px;margin-right:12px;color:#60a5fa;"><i class="fas fa-list-alt"></i></div>
                        Detaylı Analiz Sonuçları
                    </h5>
                    <p style="color:#94a3b8; font-size:0.85rem; margin:8px 0 0 50px; font-weight:500;">Klasördeki dosyalar ile sistem faturalarının eşleşme dökümü.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; font-size:1.6rem; background:rgba(255,255,255,0.1); border:none; cursor:pointer; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.2s; margin-top:-10px;">
                    <span aria-hidden="true" style="margin-top:-2px;">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding:35px; max-height:75vh; overflow-y:auto; background:#f4f6f9;">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stat-card" style="min-height:100px; padding:15px; border-left:4px solid #10b981;">
                            <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Eşleşen</div>
                            <div style="font-size:1.5rem; font-weight:800; color:#10b981;" id="detayOzetEslesen">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="min-height:100px; padding:15px; border-left:4px solid #ef4444;">
                            <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">PDF Bulunamadı</div>
                            <div style="font-size:1.5rem; font-weight:800; color:#ef4444;" id="detayOzetPdfYok">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="min-height:100px; padding:15px; border-left:4px solid #f59e0b;">
                            <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Sistemde Eksik</div>
                            <div style="font-size:1.5rem; font-weight:800; color:#f59e0b;" id="detayOzetSistemYok">0</div>
                        </div>
                    </div>
                </div>

                <div style="background:#fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border:1px solid #e2e8f0; overflow:hidden;">
                    <div class="table-responsive">
                        <table class="pro-table-pdf" style="margin:0; border-spacing: 0;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="padding:15px 20px;">Durum</th>
                                    <th style="padding:15px 20px;">PDF Dosya Adı</th>
                                    <th style="padding:15px 20px;">EFKS ID</th>
                                    <th style="padding:15px 20px;">Fatura No</th>
                                    <th style="padding:15px 20px;">Hesap Adı / Ünvan</th>
                                    <th style="padding:15px 20px;">Tutar</th>
                                </tr>
                            </thead>
                            <tbody id="detayliAnalizTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            
            <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:20px 35px; background:#fff;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:12px; font-weight:700; padding:10px 24px;">Kapat</button>
            </div>
        </div>
    </div>
</div>
"""
# insert just before @endsection
endsection_idx = content.rfind('@endsection')
if endsection_idx != -1:
    content = content[:endsection_idx] + new_modal_html + "\n" + content[endsection_idx:]


# 3. Update JS Logic
js_start_str = "    function pdfMatchFilename(filename, set) {"
js_end_str = "        if (!pdfFolderSelected || !pdfAnalizFiles.length) {"
idx3 = content.find(js_start_str)
idx4 = content.find(js_end_str, idx3)

if idx3 != -1 and idx4 != -1:
    new_js = """    function pdfMatchFilename(filename, set) {
        var name = filename.replace(/\\.pdf$/i, '').trim();
        if (set[name]) return { pdf: filename, invoice: set[name] };
        
        var alphanums = name.match(/[a-zA-Z0-9]+/g);
        if (alphanums) {
            for (var i = 0; i < alphanums.length; i++) {
                if (set[alphanums[i]]) return { pdf: filename, invoice: set[alphanums[i]] };
            }
        }
        return null;
    }

    function showPdfDetayliSonuc(eslesenList, eslesmeyenPdf, sistemdeOlan) {
        document.getElementById('detayOzetEslesen').textContent = eslesenList.length;
        document.getElementById('detayOzetPdfYok').textContent = sistemdeOlan.length; // sistemde olan faturanın pdf'i yok
        document.getElementById('detayOzetSistemYok').textContent = eslesmeyenPdf.length; // pdf var sistemde yok

        var tbody = document.getElementById('detayliAnalizTableBody');
        var html = '';

        // Eşleşenler
        eslesenList.forEach(function(item) {
            html += '<tr>' +
                '<td style="padding:15px 20px;"><span class="badge-pdf success"><i class="fas fa-check"></i> Eşleşti</span></td>' +
                '<td style="padding:15px 20px; font-family:monospace; color:#3b82f6;">' + item.pdf + '</td>' +
                '<td style="padding:15px 20px; font-weight:700;">' + item.invoice.id + '</td>' +
                '<td style="padding:15px 20px;">' + (item.invoice.fatura_no || '-') + '</td>' +
                '<td style="padding:15px 20px; font-size:0.8rem;">' + (item.invoice.hesap_adi || '-') + '</td>' +
                '<td style="padding:15px 20px; font-weight:700;">' + (item.invoice.tutar || '-') + '</td>' +
                '</tr>';
        });

        // Sistemde olup PDF'i olmayanlar
        sistemdeOlan.forEach(function(inv) {
            html += '<tr style="background:#fef2f2;">' +
                '<td style="padding:15px 20px;"><span class="badge-pdf error"><i class="fas fa-times"></i> PDF Eksik</span></td>' +
                '<td style="padding:15px 20px; color:#ef4444; font-style:italic;">- Bulunamadı -</td>' +
                '<td style="padding:15px 20px; font-weight:700;">' + inv.id + '</td>' +
                '<td style="padding:15px 20px;">' + (inv.fatura_no || '-') + '</td>' +
                '<td style="padding:15px 20px; font-size:0.8rem;">' + (inv.hesap_adi || '-') + '</td>' +
                '<td style="padding:15px 20px; font-weight:700;">' + (inv.tutar || '-') + '</td>' +
                '</tr>';
        });

        // PDF olup Sistemde olmayanlar
        eslesmeyenPdf.forEach(function(pdfName) {
            html += '<tr style="background:#fffbeb;">' +
                '<td style="padding:15px 20px;"><span class="badge-pdf warning"><i class="fas fa-exclamation-triangle"></i> Sistemde Yok</span></td>' +
                '<td style="padding:15px 20px; font-family:monospace; color:#ea580c;">' + pdfName + '</td>' +
                '<td style="padding:15px 20px; color:#94a3b8;">-</td>' +
                '<td style="padding:15px 20px; color:#94a3b8;">-</td>' +
                '<td style="padding:15px 20px; color:#94a3b8;">Klasördeki bu PDF dosyası sistemdeki hamverilerde bulunamadı.</td>' +
                '<td style="padding:15px 20px; color:#94a3b8;">-</td>' +
                '</tr>';
        });

        if (!eslesmeyenPdf.length && !sistemdeOlan.length && eslesenList.length) {
            html += '<tr><td colspan="6" class="text-center py-5" style="color:#16a34a;font-weight:700;font-size:1.1rem;"><i class="fas fa-check-circle mr-2" style="font-size:2rem;vertical-align:middle;"></i> Harika! Tüm PDF\\'ler sistemdeki faturalarla eksiksiz eşleşti.</td></tr>';
        } else if (!html) {
            html = '<tr><td colspan="6" class="text-center py-4 text-muted">Gösterilecek sonuç bulunamadı.</td></tr>';
        }

        tbody.innerHTML = html;
        
        // Modal aç
        $('#pdfAnalizModal').modal('hide');
        setTimeout(function(){
            $('#pdfDetayliSonucModal').modal('show');
        }, 400);
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
"""
    content = content[:idx3] + new_js + content[idx4:]

# 4. Update the logic inside fetch to handle Objects instead of flat array
fetch_start = "fetch('/raporlar/endeks/pdf-karsilastir/faturalar/' + encodeURIComponent(donem))"
fetch_end = "            .catch(function(err) {"
idx5 = content.find(fetch_start)
idx6 = content.find(fetch_end, idx5)
if idx5 != -1 and idx6 != -1:
    new_fetch_logic = """fetch('/raporlar/endeks/pdf-karsilastir/faturalar/' + encodeURIComponent(donem))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) throw new Error('Veri alınamadı');

                pdfAnalizFaturaSet = data.faturalar; // already object (efks => details)

                setPdfPhase('match');
                pdfPmSub.textContent = pdfAnalizFiles.length + ' adet PDF dosyası analiz ediliyor...';
                
                var eslesenList = [];
                var eslesmeyenPdf = [];
                var toplam = pdfAnalizFiles.length;
                var sira = 0;

                function sonraki() {
                    if (sira >= toplam) {
                        var eslenenSet = {};
                        eslesenList.forEach(function(e) { eslenenSet[e.invoice.id] = true; });
                        
                        var sistemdeOlan = [];
                        Object.keys(data.faturalar).forEach(function(k) {
                            if (!eslenenSet[k]) {
                                sistemdeOlan.push(data.faturalar[k]);
                            }
                        });

                        setPdfPhase('done');
                        setPdfProgress(100, toplam + ' / ' + toplam + ' tamamlandı');
                        
                        setTimeout(function() {
                            pdfProgModal.classList.remove('pm-show');
                            showPdfDetayliSonuc(eslesenList, eslesmeyenPdf, sistemdeOlan);
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
"""
    content = content[:idx5] + new_fetch_logic + content[idx6:]

with open('/Users/akarsu/Desktop/suski/resources/views/reports/endeks.blade.php', 'w') as f:
    f.write(content)

