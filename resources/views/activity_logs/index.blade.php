@extends('frontend.layouts.app')
@section('content')
<style>
:root{--p1:#1a5f8a;--p2:#2179b0;--p3:#3a9fd6;--g1:#2a7a2e;--g2:#3a9e40;--warn:#f59e0b;--wdark:#b45309;--danger:#ef4444;--ddark:#b91c1c;--bg:#eff4f8;--card:#fff;--border:rgba(26,95,138,.1);--text:#1a2e3b;--text2:#4a6a7a;--muted:#90aab8;--sh:0 2px 12px rgba(26,95,138,.08);--r:14px;--r2:9px;--ease:cubic-bezier(.4,0,.2,1);}
*,*::before,*::after{box-sizing:border-box;}
.pg{padding:1.5rem;background:var(--bg);min-height:100vh;}

/* Top bar */
.topbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;background:var(--card);border:1px solid var(--border);border-radius:var(--r);padding:1.1rem 1.4rem;box-shadow:var(--sh);margin-bottom:1.25rem;}
.topbar-left{display:flex;align-items:center;gap:.75rem;}
.tocn{width:44px;height:44px;border-radius:var(--r2);background:linear-gradient(135deg,var(--p1),var(--p3));display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;box-shadow:0 5px 15px rgba(26,95,138,.3);flex-shrink:0;}
.tt2{font-size:1.05rem;font-weight:700;color:var(--text);margin:0;}
.ts2{font-size:.74rem;color:var(--muted);font-weight:500;}

/* Table */
.tcard{background:var(--card);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--sh);overflow:hidden;}
.tchead{display:flex;align-items:center;justify-content:space-between;padding:.95rem 1.25rem;border-bottom:1px solid var(--border);background:linear-gradient(135deg,#f4f8fc,#f4f9f4);}
.tct{font-size:.88rem;font-weight:700;color:var(--text);display:flex;align-items:center;gap:.5rem;margin:0;}
.tct i{color:var(--p2);}
.ptable{width:100%;border-collapse:collapse;}
.ptable thead th{font-size:.63rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--p1);padding:.82rem 1.1rem;border-bottom:2px solid rgba(26,95,138,.12);white-space:nowrap;vertical-align:middle;background:linear-gradient(135deg,#eef4fa,#eef8ee);}
.ptable tbody tr{border-bottom:1px solid rgba(26,95,138,.07);transition:background .15s;}
.ptable tbody tr:last-child{border-bottom:none;}
.ptable tbody tr:hover{background:rgba(33,121,176,.04);}
.ptable tbody td{padding:.82rem 1.1rem;vertical-align:middle;font-size:.8rem;color:var(--text2);}

/* Action badge tags */
.at{display:inline-flex;align-items:center;gap:4px;font-size:.64rem;font-weight:700;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.05em;}
.atcr{background:rgba(42,122,46,.1);color:var(--g1);border:1px solid rgba(42,122,46,.2);}
.atu{background:rgba(245,158,11,.1);color:var(--wdark);border:1px solid rgba(245,158,11,.2);}
.atd{background:rgba(239,68,68,.1);color:var(--ddark);border:1px solid rgba(239,68,68,.2);}
.ato{background:rgba(144,170,184,.12);color:var(--muted);border:1px solid rgba(144,170,184,.2);}
.at-login{background:rgba(14,165,233,.1);color:#0369a1;border:1px solid rgba(14,165,233,.2);}
.at-logout{background:rgba(100,116,139,.1);color:#475569;border:1px solid rgba(100,116,139,.2);}
.at-import{background:rgba(139,92,246,.1);color:#6d28d9;border:1px solid rgba(139,92,246,.2);}

/* User chip */
.uch{display:flex;align-items:center;gap:.5rem;}
.uchav{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--p1),var(--g1));display:flex;align-items:center;justify-content:center;font-size:.62rem;font-weight:700;color:#fff;flex-shrink:0;}

/* Model pill */
.mpill{font-family:'JetBrains Mono','Courier New',monospace;font-size:.68rem;color:var(--p1);background:rgba(26,95,138,.08);padding:3px 8px;border-radius:20px;border:1px solid rgba(26,95,138,.15);}

/* See btn */
.sbtn{display:inline-flex;align-items:center;gap:5px;font-family:'Poppins',sans-serif;font-size:.75rem;font-weight:600;border:1.5px solid rgba(26,95,138,.22);background:rgba(26,95,138,.06);color:var(--p1);border-radius:var(--r2);padding:.38rem .8rem;cursor:pointer;transition:all .18s;white-space:nowrap;text-decoration:none;}
.sbtn:hover{background:var(--p2);color:#fff;border-color:var(--p2);}

.sem{text-align:center;padding:3rem 2rem;color:var(--muted);}
.sem i{font-size:2.5rem;opacity:.3;margin-bottom:.75rem;display:block;color:var(--p2);}
.sem p{font-size:.82rem;font-weight:500;margin:0;}
.pag-w{padding:.9rem 1.25rem;border-top:1px solid rgba(26,95,138,.08);}

/* Modal */
.modal-content{border-radius:16px !important;overflow:hidden;border:none !important;box-shadow:0 20px 60px rgba(0,0,0,.2) !important;}
.modal-header{background:linear-gradient(135deg,var(--p1),var(--p2)) !important;border-bottom:none !important;padding:1.1rem 1.4rem !important;}
.modal-title{font-size:.9rem !important;font-weight:700 !important;color:#fff !important;}
.modal-header .close{color:rgba(255,255,255,.7) !important;opacity:1 !important;}
.info-box{padding:.6rem .9rem;border-radius:var(--r2);border:1px solid var(--border);background:#f8fbfe;font-size:.82rem;font-weight:600;color:var(--text);}
pre.dpre{background:#f8fbfe;border:1px solid rgba(26,95,138,.12);border-radius:var(--r2);padding:.85rem 1rem;font-size:.74rem;max-height:200px;overflow-y:auto;font-family:'JetBrains Mono','Courier New',monospace;line-height:1.6;}
pre.old-pre{border-left:4px solid var(--danger);background:#fff5f5;}
pre.new-pre{border-left:4px solid var(--g2);background:#f0fdf4;}
.bbl2{display:inline-flex;align-items:center;gap:5px;font-family:'Poppins',sans-serif;font-size:.78rem;font-weight:600;border:none;border-radius:var(--r2);padding:.45rem .95rem;cursor:pointer;transition:all .2s;white-space:nowrap;}
.bg2h{background:rgba(26,95,138,.07);color:var(--p1);border:1.5px solid rgba(26,95,138,.18);}.bg2h:hover{background:rgba(26,95,138,.15);color:var(--p1);}
.mlbl{font-size:.63rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.09em;margin-bottom:.35rem;display:block;}
</style>

<div class="pg">

  {{-- Top Bar --}}
  <div class="topbar">
    <div class="topbar-left">
      <div class="tocn"><i class="fas fa-history"></i></div>
      <div>
        <h5 class="tt2">Sistem Aktivite Logları</h5>
        <div class="ts2">Sistemdeki tüm kullanıcı işlemleri ve hareketler</div>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="tcard">
    <div class="tchead">
      <h4 class="tct"><i class="fas fa-list"></i> Tüm Kayıt ve Hareketler</h4>
    </div>
    <div style="overflow-x:auto;">
      <table class="ptable">
        <thead>
          <tr>
            <th style="width:120px;">Tarih</th>
            <th style="width:140px;">Kullanıcı</th>
            <th style="width:130px;">İşlem Tipi</th>
            <th>Açıklama</th>
            <th style="width:100px;">Model</th>
            <th style="width:90px;text-align:right;">Detay</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
          <tr>
            <td>
              <div style="font-size:.79rem;font-weight:600;color:var(--text);">{{ $log->created_at->format('d.m.Y') }}</div>
              <div style="font-size:.7rem;color:var(--muted);">{{ $log->created_at->format('H:i:s') }}</div>
            </td>
            <td>
              <div class="uch">
                <div class="uchav">{{ strtoupper(substr($log->user ? $log->user->name : 'S', 0, 1)) }}</div>
                <span style="font-size:.8rem;font-weight:600;color:var(--text);">{{ $log->user ? $log->user->name : 'Sistem' }}</span>
              </div>
            </td>
            <td>
              @if($log->action == 'Oluşturuldu' || $log->action == 'abone_eklendi')
                <span class="at atcr"><i class="fas fa-plus-circle"></i> Ekleme</span>
              @elseif($log->action == 'Güncellendi' || $log->action == 'abone_guncellendi')
                <span class="at atu"><i class="fas fa-pen"></i> Güncelleme</span>
              @elseif($log->action == 'Silindi' || $log->action == 'abone_silindi')
                <span class="at atd"><i class="fas fa-trash"></i> Silme</span>
              @elseif($log->action == 'login')
                <span class="at at-login"><i class="fas fa-sign-in-alt"></i> Oturum Açıldı</span>
              @elseif($log->action == 'logout')
                <span class="at at-logout"><i class="fas fa-sign-out-alt"></i> Oturum Kapandı</span>
              @elseif($log->action == 'excel_import')
                <span class="at at-import"><i class="fas fa-file-excel"></i> Excel Import</span>
              @else
                <span class="at ato">{{ $log->action }}</span>
              @endif
            </td>
            <td style="font-size:.79rem;color:var(--text2);max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              {{ $log->description ?? '–' }}
            </td>
            <td><span class="mpill">{{ $log->model ? class_basename($log->model) : 'Sistem Çekirdeği' }}</span></td>
            <td style="text-align:right;">
              <button class="sbtn" data-toggle="modal" data-target="#lm{{ $log->id }}">
                <i class="fas fa-eye"></i> İncele
              </button>

              {{-- Modal --}}
              <div class="modal fade" id="lm{{ $log->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg text-left">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title"><i class="fas fa-info-circle" style="margin-right:.4rem;"></i>Log Detayı — {{ $log->created_at->format('d.m.Y H:i:s') }}</h5>
                      <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body" style="background:#f8fbfe;padding:1.25rem;">
                      <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                          <span class="mlbl">İşlem Yapan</span>
                          <div class="info-box">
                            {{ $log->user ? $log->user->name : 'Sistem' }}
                            <small style="color:var(--muted);font-weight:400;margin-left:.5rem;">({{ $log->ip }})</small>
                          </div>
                        </div>
                        <div class="col-md-6 mb-2">
                          <span class="mlbl">Aksiyon</span>
                          <div class="info-box">{{ $log->action }} — <span class="mpill">{{ $log->model ? class_basename($log->model) : 'Sistem Çekirdeği' }}</span></div>
                        </div>
                      </div>
                      @if(!empty($log->old_data))
                        <span class="mlbl" style="color:var(--ddark);">Eski Data</span>
                        <pre class="dpre old-pre">{{ json_encode($log->old_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                      @endif
                      @if(!empty($log->new_data))
                        <span class="mlbl" style="color:var(--g1);margin-top:.75rem;">Yeni Data</span>
                        <pre class="dpre new-pre">{{ json_encode($log->new_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                      @endif
                    </div>
                    <div class="modal-footer" style="background:#f0f5f9;border-top:1px solid rgba(26,95,138,.08);">
                      <button type="button" class="bbl2 bg2h" data-dismiss="modal"><i class="fas fa-times"></i> Kapat</button>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6"><div class="sem"><i class="fas fa-search"></i><p>Henüz aktivite kaydı bulunmuyor</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($logs->hasPages())
    <div class="pag-w">{{ $logs->links('pagination::bootstrap-4') }}</div>
    @endif
  </div>

</div>
@endsection
