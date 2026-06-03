@extends('frontend.layouts.app')
@section('content')
<style>
:root { --p1: #1e293b; --p2: #0f172a; --a1: #3b82f6; --bg: #f1f5f9; --card: #ffffff; --sh: 0 4px 20px rgba(15, 23, 42, 0.05); --r: 16px; }
.pg { padding: 1.5rem; min-height: 100vh; background: var(--bg); font-family: 'Inter', sans-serif;}
.h-header { background: linear-gradient(135deg, var(--p2), var(--p1)); border-radius: var(--r); padding: 2rem; color: #fff; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(15,23,42,0.15); }
.h-title { font-size: 1.5rem; font-weight: 800; }
.m-card { background: var(--card); border-radius: var(--r); padding: 1.5rem; box-shadow: var(--sh); margin-bottom: 1rem; border: 1px solid #e2e8f0; transition: transform 0.2s; }
.m-card:hover { transform: translateY(-2px); }
.m-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9; }
.m-user { display: flex; align-items: center; gap: 0.75rem; }
.m-avatar { width: 40px; height: 40px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; }
.m-info h6 { margin: 0; font-weight: 700; color: #1e293b; }
.m-info span { font-size: 0.75rem; color: #64748b; }
.m-badges { display: flex; gap: 0.5rem; }
.badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
.b-cat { background: #f1f5f9; color: #475569; }
.b-pri-yüksek { background: #fee2e2; color: #ef4444; }
.b-pri-orta { background: #fffbe6; color: #d97706; }
.b-pri-düşük { background: #f0fdf4; color: #22c55e; }
.b-status-yeni { border: 2px solid #3b82f6; color: #3b82f6; }
.b-status-okundu { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.b-status-cozuldu { background: #22c55e; color: #fff; }
.m-body { font-size: 0.9rem; color: #334155; line-height: 1.6; margin-bottom: 1.5rem; white-space: pre-wrap; buffer: 1px solid #f8fafc; padding: 1rem; background: #fcfdfe; border-radius: 8px; }
.m-footer { display: flex; gap: 1rem; align-items: center; justify-content: flex-end; }
.btn-m { padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; }
.btn-solve { background: #22c55e; color: #fff; }
.btn-read { background: #f1f5f9; color: #475569; }
</style>

<div class="pg">
    <div class="h-header">
        <div class="h-title"><i class="fas fa-inbox mr-3"></i>Gelen Destek Bildirimleri</div>
    </div>

    <div class="m-list">
        @forelse($messages as $msg)
            <div class="m-card" id="msg-{{ $msg->id }}">
                <div class="m-head">
                    <div class="m-user">
                        <div class="m-avatar">{{ substr($msg->user->name, 0, 1) }}</div>
                        <div class="m-info">
                            <h6>{{ $msg->user->name }}</h6>
                            <span>{{ $msg->created_at->diffForHumans() }} · {{ $msg->user->email }}</span>
                        </div>
                    </div>
                    <div class="m-badges">
                        <span class="badge b-cat">{{ $msg->kategori }}</span>
                        <span class="badge b-pri-{{ strtolower($msg->oncelik) }}">{{ $msg->oncelik }}</span>
                        <span class="badge b-status-{{ $msg->durum }}">{{ strtoupper($msg->durum) }}</span>
                    </div>
                </div>
                <div class="m-body" style="border-left: 3px solid #3b82f6; font-weight: 500;">
                    <strong>Soru:</strong><br>
                    {{ $msg->mesaj }}
                </div>

                <div style="margin-top: 1rem; padding-left: 1.5rem; border-left: 2px solid #eef2f7;">
                    @foreach($msg->replies as $reply)
                        <div style="margin-bottom: 0.75rem; padding: 0.75rem; border-radius: 12px; font-size: 0.88rem; background: {{ $reply->user->role == 'admin' ? '#eff6ff' : '#f8fafc' }}; border: 1px solid {{ $reply->user->role == 'admin' ? '#bfdbfe' : '#e2e8f0' }};">
                            <div style="font-size: 0.7rem; font-weight: 800; color: #64748b; margin-bottom: 0.25rem; display: flex; justify-content: space-between;">
                                <span><i class="fas {{ $reply->user->role == 'admin' ? 'fa-user-shield' : 'fa-user' }}"></i> {{ $reply->user->role == 'admin' ? 'Siz' : $reply->user->name }}</span>
                                <span>{{ $reply->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div style="color: #334155;">{{ $reply->mesaj }}</div>
                        </div>
                    @endforeach
                </div>
                
                @if($msg->admin_notu)
                    <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 1rem; padding: 0.75rem; background: #fffbeb; border-radius: 6px;">
                        <strong>Arşivlenmiş Not:</strong> {{ $msg->admin_notu }}
                    </div>
                @endif

                <div class="m-footer" style="padding-top: 1rem; border-top: 1px dashed #e2e8f0; margin-top: 1rem;">
                    @if($msg->durum != 'cozuldu')
                        <div style="flex: 1;">
                            <form onsubmit="adminSubmitReply(event, {{ $msg->id }})" style="display:flex; gap: 0.5rem;">
                                <input type="text" name="mesaj" class="form-control form-control-sm" placeholder="Buradan cevap yazın..." required style="border-radius: 20px;">
                                <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 20px;"><i class="fas fa-reply"></i> Gönder</button>
                            </form>
                        </div>
                        <button class="btn-m btn-read" onclick="updateStatus({{ $msg->id }}, 'okundu')">Okundu</button>
                        <button class="btn-m btn-solve" onclick="updateStatus({{ $msg->id }}, 'cozuldu')">Çözüldü</button>
                    @else
                         <span style="color: #16a34a; font-weight: 700; font-size: 0.8rem;"><i class="fas fa-check-circle"></i> Çözümlendi</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align:center; padding: 5rem; color: #94a3b8;">
                <i class="fas fa-comment-slash" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Henüz bir destek mesajı bulunmuyor.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 2rem;">
        {{ $messages->links() }}
    </div>
</div>

<script>
function adminSubmitReply(e, id) {
    e.preventDefault();
    const form = e.target;
    const msg = form.querySelector('[name=mesaj]').value;
    
    const formData = new FormData();
    formData.append('mesaj', msg);
    formData.append('_token', '{{ csrf_token() }}');

    fetch(`/support/reply/${id}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

function updateStatus(id, status) {
    Swal.fire({
        title: 'Not eklemek ister misiniz?',
        input: 'textarea',
        inputPlaceholder: 'İşlemle ilgili notunuzu buraya yazın...',
        showCancelButton: true,
        confirmButtonText: 'Durumu Güncelle',
        cancelButtonText: 'Vazgeç'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/destek/${id}`, {
                method: 'PATCH',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    durum: status,
                    admin_notu: result.value
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Güncellendi', 'Mesaj durumu güncellendi.', 'success').then(() => location.reload());
                }
            });
        }
    });
}
</script>
@endsection
