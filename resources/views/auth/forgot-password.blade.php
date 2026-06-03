<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart ŞUSKİ – Şifremi Unuttum</title>
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
}

.login-wrap {
    display: flex;
    width: 820px;
    max-width: 96vw;
    min-height: 520px;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 28px 70px rgba(26,95,138,.18), 0 4px 18px rgba(0,0,0,.09);
    animation: fadeUp .65s ease both;
}
@keyframes fadeUp {
    from { opacity:0; transform:translateY(22px); }
    to   { opacity:1; transform:translateY(0); }
}

/* Sol panel */
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

/* Sağ panel */
.right-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 50px 44px;
    background: linear-gradient(160deg, #d6e5ef 0%, #deeaf3 100%);
}

.form-heading {
    text-align: center;
    margin-bottom: 10px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(26,95,138,.15);
}
.form-heading h1 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a5f8a;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.form-desc {
    font-size: .82rem;
    color: #4a6a7a;
    text-align: center;
    margin-bottom: 24px;
    line-height: 1.6;
    font-weight: 400;
}

/* Status */
.alert-success {
    background: #ecfdf5;
    border: 1px solid #6ee7b7;
    border-radius: 9px;
    padding: 12px 16px;
    font-size: .82rem;
    color: #065f46;
    margin-bottom: 18px;
    line-height: 1.55;
}

/* Errors */
.alert-error {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    border-radius: 9px;
    padding: 12px 16px;
    font-size: .82rem;
    color: #991b1b;
    margin-bottom: 18px;
}
.alert-error ul { list-style: none; }
.alert-error ul li { margin-bottom: 2px; }

.form-group { margin-bottom: 20px; }
.form-group label {
    display: block;
    font-size: .74rem;
    font-weight: 600;
    color: #2d4a5a;
    margin-bottom: 6px;
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
    width: 44px; height: 46px;
    display: flex; align-items: center; justify-content: center;
    color: #7a9ab0; flex-shrink: 0;
    border-right: 1px solid #d5e3ed;
}
.input-wrap input {
    flex: 1; border: none; background: transparent;
    outline: none; padding: 11px 14px;
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

.btn-send {
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #2a7a2e 0%, #3a9e40 50%, #2179b0 100%);
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
    box-shadow: 0 6px 20px rgba(42,122,46,.32);
    margin-bottom: 16px;
}
.btn-send::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 60%);
    pointer-events: none;
}
.btn-send:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(42,122,46,.38); }
.btn-send:active { transform: translateY(0); }

.back-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: .8rem;
    color: #2179b0;
    text-decoration: none;
    font-weight: 500;
    transition: color .15s;
}
.back-link:hover { color: #1a5f8a; }

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
        <img width="80%" src="{{ asset('frontend/assets/img/logo.png') }}" alt="Smart ŞUSKİ"> 
    </div>

    {{-- Sağ Panel --}}
    <div class="right-panel">

        <div class="form-heading">
            <h1>Şifremi Unuttum</h1>
        </div>

        <p class="form-desc">
            E-posta adresinizi girin, şifre sıfırlama bağlantısı gönderelim.
        </p>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

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

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">E-posta Adresi</label>
                <div class="input-wrap">
                    <div class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <input type="email" id="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="ornek@suski.gov.tr"
                        class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        required autofocus autocomplete="username">
                </div>
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-send">Bağlantı Gönder</button>

            <a href="{{ route('login') }}" class="back-link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Giriş sayfasına dön
            </a>

        </form>

    </div>
</div>

</body>
</html>