@extends('frontend.layouts.app')
@section('content')
<style>
:root { --p1: #1e293b; --p2: #0f172a; --a1: #3b82f6; --bg: #f1f5f9; --card: #ffffff; --sh: 0 4px 20px rgba(15, 23, 42, 0.05); --r: 16px; }
.pg { padding: 1.5rem; min-height: 100vh; background: var(--bg); font-family: 'Inter', sans-serif;}
.h-header { background: linear-gradient(135deg, var(--p2), var(--p1)); border-radius: var(--r); padding: 2rem; color: #fff; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(15,23,42,0.15); }
.h-title { font-size: 1.5rem; font-weight: 800; }
.m-card { background: var(--card); border-radius: var(--r); padding: 1.5rem; box-shadow: var(--sh); margin-bottom: 1rem; border: 1px solid #e2e8f0; }
.m-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9; }
.m-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
.b-cat { background: #f1f5f9; color: #475569; }
.b-pri-yüksek { background: #fee2e2; color: #ef4444; }
.b-pri-orta { background: #fffbe6; color: #d97706; }
.b-pri-düşük { background: #f0fdf4; color: #22c55e; }
.b-status-yeni { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; }
.b-status-okundu { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.b-status-cozuldu { background: #22c55e; color: #fff; }
.m-body { font-size: 0.9rem; color: #334155; line-height: 1.6; margin-bottom: 1.5rem; white-space: pre-wrap; padding: 1rem; background: #fcfdfe; border-radius: 8px; border-left: 3px solid #e2e8f0; }
.admin-reply { margin-top: 1rem; padding: 1rem; background: #f0f9ff; border-radius: 12px; border-left: 4px solid #0ea5e9; position: relative; }
.admin-reply::before { content: '\f3e5'; font-family: 'Font Awesome 5 Free'; font-weight: 900; position: absolute; left: -12px; top: 10px; background: #0ea5e9; color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; }
.admin-reply h6 { font-size: 0.85rem; font-weight: 800; color: #0369a1; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
.admin-reply p { font-size: 0.88rem; color: #0c4a6e; margin: 0; line-height: 1.5; }
.reply-group { margin-top: 1rem; padding-left: 1.5rem; border-left: 2px solid #eef2f7; }
.r-item { margin-bottom: 0.75rem; padding: 0.75rem; border-radius: 12px; font-size: 0.88rem; }
.r-user { background: #f8fafc; border: 1px solid #e2e8f0; }
.r-admin { background: #eff6ff; border: 1px solid #bfdbfe; }
</style>

<div class="pg">
    <div class="h-header">
        <div class="h-title"><i class="fas fa-paper-plane mr-3"></i>Destek Taleplerim</div>
    </div>

    <div class="m-list">
        @forelse($messages as $msg)
            <div class="m-card">
                <div class="m-head">
                    <div class="m-info">
                        <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.3rem;">
                            <i class="far fa-clock mr-1"></i> {{ $msg->created_at->format('d.m.Y H:i') }}
                        </div>
                        <div class="m-badges">
                            <span class="badge b-cat">{{ $msg->kategori }}</span>
                            <span class="badge b-pri-{{ strtolower($msg->oncelik) }}">{{ $msg->oncelik }}</span>
                            <span class="badge b-status-{{ $msg->durum }}">{{ strtoupper($msg->durum) }}</span>
                        </div>
                    </div>
                </div>
                <div class="m-body" style="font-weight: 500; border-left-color: #3b82f6;">{{ $msg->mesaj }}</div>
                
                <div class="reply-group">
                    @foreach($msg->replies as $reply)
                        <div class="r-item {{ $reply->user->role == 'admin' ? 'r-admin' : 'r-user' }}">
                            <div style="font-size: 0.7rem; font-weight: 800; color: #64748b; margin-bottom: 0.25rem; display: flex; justify-content: space-between;">
                                <span><i class="fas {{ $reply->user->role == 'admin' ? 'fa-user-shield' : 'fa-user' }}"></i> {{ $reply->user->role == 'admin' ? 'Yönetici' : 'Siz' }}</span>
                                <span>{{ $reply->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div style="color: #334155;">{{ $reply->mesaj }}</div>
                        </div>
                    @endforeach
                </div>

                @if($msg->admin_notu)
                    <div class="admin-reply" style="background: #fffbeb; border-left-color: #f59e0b;">
                        <h6><i class="fas fa-history"></i> Arşivlenmiş Cevap</h6>
                        <p>{{ $msg->admin_notu }}</p>
                    </div>
                @endif

                @if($msg->durum != 'cozuldu')
                    <div style="margin-top: 1.5rem; border-top: 1px dashed #e2e8f0; padding-top: 1rem;">
                        <form onsubmit="submitReply(event, {{ $msg->id }})" class="support-reply-form">
                            <div style="display: flex; gap: 0.5rem;">
                                <input type="text" name="mesaj" class="form-control form-control-sm" placeholder="Buradan cevap yazabilirsiniz..." required style="border-radius: 20px; padding: 0.5rem 1rem;">
                                <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"><i class="fas fa-paper-plane" style="font-size: 0.7rem;"></i></button>
                            </div>
                        </form>
                    </div>
                @else
                    <div style="text-align: center; margin-top: 1rem; color: #16a34a; font-size: 0.75rem; font-weight: 700;">
                        <i class="fas fa-check-circle"></i> Bu talep çözüldüğü için kapatılmıştır.
                    </div>
                @endif
            </div>
        @empty
            <div style="text-align:center; padding: 5rem; color: #94a3b8;">
                <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Henüz bir destek talebi göndermemişsiniz.</p>
                <a href="{{ route('help.index') }}#destek" class="btn btn-primary btn-sm mt-3" style="border-radius: 20px;">Yeni Talep Oluştur</a>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 2rem;">
        {{ $messages->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function submitReply(e, id) {
    e.preventDefault();
    const form = e.target;
    const input = form.querySelector('input');
    const btn = form.querySelector('button');
    const msg = input.value;

    btn.disabled = true;

    const formData = new FormData();
    formData.append('mesaj', msg);
    formData.append('_token', '{{ csrf_token() }}');

    fetch(`/support/reply/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    })
    .catch(() => {
        btn.disabled = false;
        alert('Cevap gönderilirken bir hata oluştu.');
    });
}
</script>
@endpush
