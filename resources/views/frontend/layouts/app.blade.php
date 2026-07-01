@include('frontend.partials.header')
@include('frontend.partials.sidebar')

<div class="main-panel">
    <div class="content">
        @yield('content')
    </div>
    @include('frontend.partials.footer')
</div>

<div id="pageLoaderOverlay">
    <div class="pl-loader-box">
        <div class="pl-spinner"></div>
        <div class="pl-title">Yükleniyor...</div>
        <div class="pl-sub">Lütfen bekleyin, rapor hazırlanıyor.</div>
    </div>
</div>

<style>
#pageLoaderOverlay {
    display: none; position: fixed; inset: 0; z-index: 999999;
    background: rgba(15,23,42,0.75); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    align-items: center; justify-content: center; flex-direction: column;
}
#pageLoaderOverlay.active { display: flex; }
.pl-loader-box {
    background: rgba(255,255,255,0.97); border-radius: 24px; padding: 40px 50px;
    text-align: center; box-shadow: 0 30px 80px rgba(0,0,0,0.25);
    animation: plFadeIn .35s ease; min-width: 280px;
}
@keyframes plFadeIn { from{opacity:0;transform:scale(.92);} to{opacity:1;transform:scale(1);} }
.pl-spinner {
    width: 56px; height: 56px; border: 5px solid #e2e8f0;
    border-top-color: #2563eb; border-radius: 50%;
    animation: plSpin .85s linear infinite; margin: 0 auto 20px;
}
@keyframes plSpin { to{transform:rotate(360deg);} }
.pl-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.pl-sub { font-size: .85rem; color: #64748b; font-weight: 500; }
</style>

<script>
(function() {
    var loader = document.getElementById('pageLoaderOverlay');

    document.addEventListener('click', function(e) {
        var link = e.target.closest('a');
        if (!link) return;
        var href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
        if (link.getAttribute('target') === '_blank') return;
        if (link.hasAttribute('data-toggle') || link.hasAttribute('data-dismiss')) return;
        if (e.ctrlKey || e.metaKey || e.shiftKey) return;

        sessionStorage.setItem('pl_loading', '1');
        sessionStorage.setItem('pl_started', Date.now().toString());
    });

    window.addEventListener('pageshow', function() {
        if (sessionStorage.getItem('pl_loading') !== '1') return;

        var started = parseInt(sessionStorage.getItem('pl_started') || '0');
        var elapsed = Date.now() - started;

        if (elapsed > 3000) {
            loader.classList.add('active');
            setTimeout(function() {
                loader.classList.remove('active');
            }, 800);
        }

        sessionStorage.removeItem('pl_loading');
        sessionStorage.removeItem('pl_started');
    });
})();
</script>

@stack('scripts')