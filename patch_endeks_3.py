import sys

with open('/Users/akarsu/Desktop/suski/resources/views/reports/endeks.blade.php', 'r') as f:
    content = f.read()

# 1. Update JS to omit matched items and improve premium html output
js_start = "    function showPdfDetayliSonuc(eslesenList, eslesmeyenPdf, sistemdeOlan) {"
js_end = "        $('#pdfAnalizModal').modal('hide');"

idx1 = content.find(js_start)
idx2 = content.find(js_end, idx1)

if idx1 != -1 and idx2 != -1:
    new_js = """    function showPdfDetayliSonuc(eslesenList, eslesmeyenPdf, sistemdeOlan) {
        document.getElementById('detayOzetEslesen').textContent = eslesenList.length;
        document.getElementById('detayOzetPdfYok').textContent = sistemdeOlan.length; // sistemde olan faturanın pdf'i yok
        document.getElementById('detayOzetSistemYok').textContent = eslesmeyenPdf.length; // pdf var sistemde yok

        var tbody = document.getElementById('detayliAnalizTableBody');
        var html = '';

        // Eşleşenler tablodan kaldırıldı (sadece uyumsuzlukları listeliyoruz)
        /* 
        eslesenList.forEach(function(item) { ... }); 
        */

        // Sistemde olup PDF'i olmayanlar
        sistemdeOlan.forEach(function(inv) {
            html += '<tr style="background:#fffafb; border-bottom:1px solid #fce7f3; transition: background 0.3s;" onmouseover="this.style.background=\\'#fef2f2\\'" onmouseout="this.style.background=\\'#fffafb\\'">' +
                '<td style="padding:18px 25px;"><span class="badge" style="background:rgba(239,68,68,0.15); color:#dc2626; padding:8px 14px; border-radius:10px; font-weight:800; font-size:0.8rem; letter-spacing:0.5px;"><i class="fas fa-file-excel mr-2"></i>Sistemde Var, PDF Yok</span></td>' +
                '<td style="padding:18px 25px; color:#ef4444; font-style:italic; font-weight:500;">- Bulunamadı -</td>' +
                '<td style="padding:18px 25px; font-weight:800; color:#334155;">' + inv.id + '</td>' +
                '<td style="padding:18px 25px; color:#475569; font-weight:600;">' + (inv.fatura_no || '-') + '</td>' +
                '<td style="padding:18px 25px; font-size:0.85rem; color:#64748b; font-weight:500;">' + (inv.hesap_adi || '-') + '</td>' +
                '<td style="padding:18px 25px; font-weight:800; color:#0f172a; font-size:1.05rem;">' + (inv.tutar || '-') + '</td>' +
                '</tr>';
        });

        // PDF olup Sistemde olmayanlar
        eslesmeyenPdf.forEach(function(pdfName) {
            html += '<tr style="background:#fdfdf7; border-bottom:1px solid #fef3c7; transition: background 0.3s;" onmouseover="this.style.background=\\'#fffbeb\\'" onmouseout="this.style.background=\\'#fdfdf7\\'">' +
                '<td style="padding:18px 25px;"><span class="badge" style="background:rgba(245,158,11,0.15); color:#d97706; padding:8px 14px; border-radius:10px; font-weight:800; font-size:0.8rem; letter-spacing:0.5px;"><i class="fas fa-file-pdf mr-2"></i>PDF Var, Sistemde Yok</span></td>' +
                '<td style="padding:18px 25px; font-family:\\'Fira Code\\', monospace; color:#ea580c; font-weight:700;">' + pdfName + '</td>' +
                '<td style="padding:18px 25px; color:#cbd5e1; font-weight:500;">-</td>' +
                '<td style="padding:18px 25px; color:#cbd5e1; font-weight:500;">-</td>' +
                '<td style="padding:18px 25px; color:#94a3b8; font-size:0.85rem;">Klasördeki bu dosya sistem verilerinde eşleşmedi.</td>' +
                '<td style="padding:18px 25px; color:#cbd5e1; font-weight:500;">-</td>' +
                '</tr>';
        });

        if (!eslesmeyenPdf.length && !sistemdeOlan.length) {
            html += '<tr><td colspan="6" class="text-center" style="padding: 60px 20px;"><div style="display:inline-block; padding:30px 40px; background:linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.05)); border-radius:24px; border:1px solid rgba(16,185,129,0.2);"><i class="fas fa-check-circle" style="font-size:3.5rem; color:#10b981; margin-bottom:15px; filter:drop-shadow(0 10px 15px rgba(16,185,129,0.3));"></i><h4 style="color:#059669; font-weight:800; margin:0; font-size:1.4rem;">Kusursuz Eşleşme!</h4><p style="color:#047857; margin-top:8px; font-weight:500; opacity:0.8;">Tüm klasördeki PDF dosyaları sistemdeki faturalarla eksiksiz olarak eşleşti. Herhangi bir uyumsuzluk bulunmamaktadır.</p></div></td></tr>';
        } else if (!html) {
            html = '<tr><td colspan="6" class="text-center py-5 text-muted font-weight-bold">Gösterilecek uyumsuzluk bulunamadı.</td></tr>';
        }

        tbody.innerHTML = html;
        
        // Modal aç
"""
    content = content[:idx1] + new_js + content[idx2:]


# 2. Update Modal HTML to be ultra premium
modal_start = "{{-- ═══ Detaylı Sonuçlar Modalı ═══ --}}"
modal_end = "    </div>\n</div>\n\n@endsection"

idx3 = content.find(modal_start)
idx4 = content.find(modal_end, idx3)

if idx3 != -1 and idx4 != -1:
    new_modal_html = """{{-- ═══ Detaylı Sonuçlar Modalı ═══ --}}
<div class="modal fade" id="pdfDetayliSonucModal" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); background: rgba(15, 23, 42, 0.7);">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 96%;">
        <div class="modal-content" style="border-radius:32px; border:1px solid rgba(255,255,255,0.15); overflow:hidden; box-shadow:0 50px 100px -20px rgba(0,0,0,0.5), inset 0 1px 1px rgba(255,255,255,0.3); background: #f8fafc;">
            
            <div class="modal-header" style="background:linear-gradient(135deg, #0f172a, #1e1b4b); border:none; padding:30px 45px; position: relative; overflow:hidden;">
                <!-- Premium Gloss Effect -->
                <div style="position:absolute; top:-50%; left:-50%; width:200%; height:200%; background:radial-gradient(circle at top left, rgba(255,255,255,0.08) 0%, transparent 60%); pointer-events:none;"></div>
                
                <div style="position:relative; z-index:1;">
                    <h5 class="modal-title d-flex align-items-center" style="color:#fff; font-weight:800; font-size:1.6rem; margin:0; letter-spacing:-0.03em; text-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                        <div style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; background:linear-gradient(135deg, rgba(59,130,246,0.3), rgba(37,99,235,0.1)); border: 1px solid rgba(96,165,250,0.3); border-radius:14px; margin-right:16px; color:#60a5fa; box-shadow: 0 10px 20px -5px rgba(59,130,246,0.4);">
                            <i class="fas fa-layer-group" style="font-size:1.2rem;"></i>
                        </div>
                        Detaylı Uyumsuzluk Raporu
                    </h5>
                    <p style="color:#94a3b8; font-size:0.95rem; margin:10px 0 0 64px; font-weight:500;">Klasördeki dosyalar ile sistem faturalarının eşleşme özetleri ve uyumsuzluk listesi.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.9; font-size:1.8rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); cursor:pointer; width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.3s; margin-top:-15px; position:relative; z-index:1;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='rotate(90deg)';" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.transform='rotate(0deg)';">
                    <span aria-hidden="true" style="margin-top:-2px;">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding:40px 45px; max-height:78vh; overflow-y:auto; background:linear-gradient(to bottom, #f8fafc, #f1f5f9);">
                
                <div class="row mb-5">
                    <div class="col-md-4">
                        <div class="stat-card" style="position:relative; overflow:hidden; min-height:110px; padding:25px; border-radius:24px; background:#fff; box-shadow:0 20px 40px -15px rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.2);">
                            <div style="position:absolute; top:-20px; right:-20px; font-size:6rem; color:rgba(16,185,129,0.05);"><i class="fas fa-check-circle"></i></div>
                            <div style="position:relative; z-index:1;">
                                <div style="font-size:0.8rem; color:#64748b; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Başarıyla Eşleşen</div>
                                <div style="display:flex; align-items:baseline;">
                                    <div style="font-size:2.4rem; font-weight:900; color:#10b981; line-height:1;" id="detayOzetEslesen">0</div>
                                    <div style="margin-left:8px; font-size:0.9rem; color:#10b981; font-weight:600; opacity:0.8;">Fatura</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="position:relative; overflow:hidden; min-height:110px; padding:25px; border-radius:24px; background:#fff; box-shadow:0 20px 40px -15px rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.2);">
                            <div style="position:absolute; top:-20px; right:-20px; font-size:6rem; color:rgba(239,68,68,0.05);"><i class="fas fa-times-circle"></i></div>
                            <div style="position:relative; z-index:1;">
                                <div style="font-size:0.8rem; color:#64748b; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Sistemde Var, PDF Yok</div>
                                <div style="display:flex; align-items:baseline;">
                                    <div style="font-size:2.4rem; font-weight:900; color:#ef4444; line-height:1;" id="detayOzetPdfYok">0</div>
                                    <div style="margin-left:8px; font-size:0.9rem; color:#ef4444; font-weight:600; opacity:0.8;">Kayıt</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="position:relative; overflow:hidden; min-height:110px; padding:25px; border-radius:24px; background:#fff; box-shadow:0 20px 40px -15px rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.2);">
                            <div style="position:absolute; top:-20px; right:-20px; font-size:6rem; color:rgba(245,158,11,0.05);"><i class="fas fa-exclamation-triangle"></i></div>
                            <div style="position:relative; z-index:1;">
                                <div style="font-size:0.8rem; color:#64748b; font-weight:800; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">PDF Var, Sistemde Yok</div>
                                <div style="display:flex; align-items:baseline;">
                                    <div style="font-size:2.4rem; font-weight:900; color:#f59e0b; line-height:1;" id="detayOzetSistemYok">0</div>
                                    <div style="margin-left:8px; font-size:0.9rem; color:#f59e0b; font-weight:600; opacity:0.8;">Dosya</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="background:#fff; border-radius: 28px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08); border:1px solid #e2e8f0; overflow:hidden; position:relative;">
                    <div style="padding: 20px 30px; border-bottom:1px solid #e2e8f0; background:#f8fafc; display:flex; align-items:center;">
                        <div style="width:12px; height:12px; border-radius:50%; background:#ef4444; margin-right:8px; box-shadow:0 0 10px rgba(239,68,68,0.5);"></div>
                        <div style="width:12px; height:12px; border-radius:50%; background:#f59e0b; margin-right:12px; box-shadow:0 0 10px rgba(245,158,11,0.5);"></div>
                        <h6 style="margin:0; font-weight:800; color:#334155; letter-spacing:-0.01em; font-size:1.1rem;">Uyumsuzluk Listesi</h6>
                        <span style="margin-left:auto; font-size:0.8rem; color:#94a3b8; font-weight:600;"><i class="fas fa-info-circle mr-1"></i>Sadece hatalı veya eksik kayıtlar listelenir</span>
                    </div>
                    <div class="table-responsive">
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background:#fff; border-bottom:2px solid #e2e8f0;">
                                    <th style="padding:20px 25px; color:#64748b; font-weight:800; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Durum</th>
                                    <th style="padding:20px 25px; color:#64748b; font-weight:800; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">PDF Dosya Adı</th>
                                    <th style="padding:20px 25px; color:#64748b; font-weight:800; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">EFKS ID</th>
                                    <th style="padding:20px 25px; color:#64748b; font-weight:800; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Fatura No</th>
                                    <th style="padding:20px 25px; color:#64748b; font-weight:800; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Hesap Adı / Ünvan</th>
                                    <th style="padding:20px 25px; color:#64748b; font-weight:800; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Tutar</th>
                                </tr>
                            </thead>
                            <tbody id="detayliAnalizTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            
            <div class="modal-footer" style="border-top:1px solid rgba(0,0,0,0.05); padding:25px 45px; background:linear-gradient(to right, #f8fafc, #fff);">
                <button type="button" class="btn" data-dismiss="modal" style="border-radius:16px; font-weight:800; padding:12px 32px; background:#fff; color:#475569; border:2px solid #e2e8f0; font-size:1.05rem; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); transition:all 0.2s;" onmouseover="this.style.borderColor='#cbd5e1'; this.style.color='#0f172a'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569'; this.style.transform='translateY(0)';">Pencereyi Kapat</button>
            </div>
        </div>
"""
    content = content[:idx3] + new_modal_html + "\n" + content[idx4:]

with open('/Users/akarsu/Desktop/suski/resources/views/reports/endeks.blade.php', 'w') as f:
    f.write(content)

