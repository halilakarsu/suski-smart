<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart ŞUSKİ – Kayıt Ol</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #dde8f0 0%, #e8f5ea 60%, #dff0ff 100%);
    padding: 20px;
}

/* ── Main card ── */
.login-wrap {
    display: flex;
    width: 820px;
    max-width: 100%;
    min-height: 580px;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 28px 70px rgba(26,95,138,.18), 0 4px 18px rgba(0,0,0,.09);
    animation: fadeUp .65s ease both;
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(22px); }
    to   { opacity:1; transform:translateY(0); }
}

/* ── Left: logo placeholder ── */
.left-panel {
    flex: 1;
    background: #f0f5f8;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    border-right: 1px solid #dce8f0;
}

.left-panel .placeholder-hint {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    pointer-events: none;
    user-select: none;
}
.left-panel .placeholder-hint svg { width: 48px; height: 48px; color: #7a9ab0; }
.left-panel .placeholder-hint span { font-size: .78rem; color: #5a7a8a; font-weight: 500; }

/* ── Right: form ── */
.right-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 40px 44px;
    background: linear-gradient(160deg, #d6e5ef 0%, #deeaf3 100%);
}

.form-heading {
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(26,95,138,.15);
}
.form-heading h1 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a5f8a;
    letter-spacing: .14em;
    text-transform: uppercase;
}

/* Session errors */
.alert-error {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    border-radius: 9px;
    padding: 12px 16px;
    font-size: .82rem;
    color: #991b1b;
    margin-bottom: 18px;
}
.alert-error ul { list-style: none; margin-left: 15px; }
.alert-error ul li { margin-bottom: 2px; }

/* Form groups */
.form-group { margin-bottom: 15px; }
.form-group label {
    display: block;
    font-size: .74rem;
    font-weight: 600;
    color: #2d4a5a;
    margin-bottom: 5px;
    letter-spacing: .04em;
    text-transform: uppercase;
}

.input-wrap {
    display: flex;
    align-items: center;
    background: #f4f8fb;
    border: 1.5px solid #c5d6e2;
    border-radius: 10px;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s, background .2s;
}
.input-wrap:focus-within {
    border-color: #2179b0;
    box-shadow: 0 0 0 3px rgba(33,121,176,.13);
    background: #fff;
}
.input-icon {
    width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
    color: #7a9ab0; flex-shrink: 0;
    border-right: 1px solid #d5e3ed;
}
.input-wrap input {
    flex: 1; border: none; background: transparent;
    outline: none; padding: 10px 14px;
    font-family: 'Poppins', sans-serif;
    font-size: .86rem; color: #1a2e3b;
}
.input-wrap input::placeholder { color: #a8bfcc; }
.input-wrap input.is-invalid { color: #991b1b; }

.field-error {
    font-size: .74rem;
    color: #dc2626;
    margin-top: 5px;
    font-weight: 500;
}

/* Register Link row */
.login-row {
    text-align: center;
    margin-top: 15px;
    margin-bottom: 25px;
}
.login-link {
    font-size: .8rem;
    color: #2179b0;
    text-decoration: none;
    font-weight: 600;
    transition: color .15s;
}
.login-link:hover { color: #1a5f8a; text-decoration: underline; }

/* Submit btn */
.btn-login {
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #1a5f8a 0%, #2179b0 50%, #2a9ba8 100%);
    color: #fff;
    font-family: 'Poppins', sans-serif;
    font-size: .95rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 6px 20px rgba(26,95,138,.32);
}
.btn-login::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 60%);
    pointer-events: none;
}
.btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(26,95,138,.38); }
.btn-login:active { transform: translateY(0); }
.btn-login:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* Responsive */
@media (max-width: 640px) {
    .left-panel { display: none; }
    .right-panel { padding: 36px 24px; }
}
</style>
</head>
<body>

<div class="login-wrap">

    {{-- Sol Panel: Logo --}}
    <div class="left-panel">
        <div class="placeholder-hint">
          <img width="80%" src="/frontend/assets/img/logo.png" alt="Smart ŞUSKİ Logo">
        </div>
    </div>

    {{-- Sağ Panel: Register Form --}}
    <div class="right-panel">

        <div class="form-heading">
            <h1>HESAP OLUŞTUR</h1>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Ad Soyad --}}
            <div class="form-group">
                <label for="name">Ad Soyad</label>
                <div class="input-wrap">
                    <div class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Adınız Soyadınız" class="{{ $errors->has('name') ? 'is-invalid' : '' }}">
                </div>
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">E-posta</label>
                <div class="input-wrap">
                    <div class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="ornek@suski.gov.tr" class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                </div>
            </div>

            {{-- Şifre --}}
            <div class="form-group">
                <label for="password">Şifre</label>
                <div class="input-wrap">
                    <div class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                </div>
            </div>

            {{-- Şifre Tekrar --}}
            <div class="form-group">
                <label for="password_confirmation">Şifre Tekrarı</label>
                <div class="input-wrap">
                    <div class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}">
                </div>
            </div>

            <div class="login-row">
                <span style="font-size:.8rem;color:#4a6a7a;">Zaten hesabınız var mı?</span> 
                <a href="{{ route('login') }}" class="login-link">Giriş Yap</a>
            </div>

            <button type="submit" class="btn-login">Kayıt Ol</button>
            <div style="text-align: center; margin-top:20px;">
                <small style="color: #6a8c9e;">Bilgi İşlem Daire Başkanlığı</small>
            </div>
        </form>

    </div>
</div>

</body>
</html>
