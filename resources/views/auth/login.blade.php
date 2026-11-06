<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SIMAHAD - Masuk & Akses Layanan Ma'had</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@1,500;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-maroon: #781023;
            --brand-maroon-hover: #600c1b;
            --brand-maroon-dark: #460914;
            --brand-gold: #F5A623;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg-canvas: #f3f4f6;
            --border-maroon: #781023;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
        }

        /* Outer Frame Container */
        .login-card {
            width: 100%;
            max-width: 1080px;
            background: #ffffff;
            border: 1.5px solid var(--border-maroon);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 860px) {
            .login-card {
                flex-direction: row;
                min-height: 640px;
            }
        }

        /* ================= LEFT COLUMN ================= */
        
.hero-panel {
    position: relative;
    flex: 1.05;
    background-color: #d1d5db;
    background-image: url('{{ asset('images/mahad_al_jamiah.jpeg') }}');
    background-size: cover;
    background-position: center bottom;
    background-repeat: no-repeat;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2.25rem 2.25rem 2rem;
    min-height: 480px;
    overflow: hidden;
    border-bottom: 1.5px solid var(--border-maroon);
}


        @media (min-width: 860px) {
            .hero-panel {
                border-bottom: none;
                border-right: 1.5px solid var(--border-maroon);
                min-height: 640px;
            }
        }

        /* Subtle gradient to preserve sky & foreground clarity */
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg, 
                rgba(255, 255, 255, 0.35) 0%, 
                rgba(255, 255, 255, 0.15) 30%, 
                rgba(255, 255, 255, 0.5) 65%, 
                rgba(255, 255, 255, 0.2) 100%
            );
            pointer-events: none;
        }

        .hero-content-top {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hero-brand-text {
            display: flex;
            flex-direction: column;
        }

        .hero-brand-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #111827;
            line-height: 1.1;
        }

        .hero-brand-sub {
            font-size: 0.76rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.25;
            margin-top: 2px;
        }

        .hero-brand-inst {
            font-size: 0.74rem;
            font-weight: 500;
            color: #374151;
            line-height: 1.25;
        }

        .hero-content-middle {
            position: relative;
            z-index: 2;
            margin-top: 2.5rem;
            margin-bottom: auto;
            max-width: 440px;
        }

        .hero-welcome {
            font-family: 'Lora', Georgia, serif;
            font-style: italic;
            font-weight: 600;
            font-size: 1.7rem;
            color: var(--brand-maroon);
            margin-bottom: 2px;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        .hero-simahad {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--brand-maroon-dark);
            line-height: 1.05;
            margin-bottom: 0.65rem;
            letter-spacing: -0.01em;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        .hero-tagline {
            font-size: 0.96rem;
            font-weight: 500;
            color: #111827;
            line-height: 1.45;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.7);
        }

        /* Building Signage matching the facade in the screenshot */
        .building-sign-overlay {
            position: relative;
            z-index: 2;
            align-self: center;
            text-align: center;
            margin-top: 1rem;
            padding: 0.65rem 1.25rem;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(2px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .building-sign-logo {
            display: inline-flex;
            margin-bottom: 4px;
        }

        .building-sign-text {
            font-size: 0.92rem;
            font-weight: 800;
            color: #27303f;
            letter-spacing: 0.08em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .building-sign-sub {
            font-size: 0.82rem;
            font-weight: 800;
            color: #334155;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* ================= RIGHT COLUMN ================= */
        .form-panel {
            flex: 1.05;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.75rem 2rem 2rem;
        }

        @media (min-width: 600px) {
            .form-panel {
                padding: 2rem 2.75rem 2rem;
            }
        }

        .motto-bar {
            text-align: right;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            letter-spacing: 0.02em;
            margin-bottom: 1rem;
        }

        /* Framed Card on right with rounded top */
        .inner-frame {
            border: 1.5px solid var(--border-maroon);
            border-radius: 36px 36px 28px 28px;
            padding: 2.25rem 2rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        @media (min-width: 600px) {
            .inner-frame {
                padding: 2.5rem 2.5rem 2.25rem;
            }
        }

        .center-emblem {
            margin-bottom: 0.75rem;
        }

        .form-headline {
            color: var(--brand-maroon);
            font-size: 1.18rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.35rem;
        }

        .form-subheadline {
            color: #4b5563;
            font-size: 0.82rem;
            line-height: 1.35;
            text-align: center;
            max-width: 320px;
            margin-bottom: 1.5rem;
        }

        /* Alerts */
        .alert-box {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            line-height: 1.3;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        /* Form elements */
        .auth-form {
            width: 100%;
        }

        .form-row {
            margin-bottom: 0.9rem;
            width: 100%;
        }

        .field-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.35rem;
        }

        .field-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .field-input {
            width: 100%;
            height: 42px;
            border: 1.5px solid var(--border-maroon);
            border-radius: 8px;
            padding: 0 14px;
            font-family: inherit;
            font-size: 0.85rem;
            color: #111827;
            background-color: #ffffff;
            outline: none;
            transition: all 0.2s ease;
        }

        .field-input::placeholder {
            color: #9ca3af;
            font-size: 0.83rem;
        }

        .field-input:focus {
            box-shadow: 0 0 0 3px rgba(120, 16, 35, 0.15);
            border-color: var(--brand-maroon);
        }

        .field-input.has-icon {
            padding-right: 42px;
        }

        .toggle-pwd-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: color 0.15s;
        }

        .toggle-pwd-btn:hover {
            color: var(--brand-maroon);
        }

        .forgot-link-wrap {
            text-align: right;
            margin-top: -0.2rem;
            margin-bottom: 1.15rem;
        }

        .forgot-link {
            color: var(--brand-maroon);
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            transition: text-decoration 0.2s;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Buttons */
        .btn-submit {
            width: 100%;
            height: 42px;
            background-color: var(--brand-maroon);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(120, 16, 35, 0.2);
        }

        .btn-submit:hover {
            background-color: var(--brand-maroon-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(120, 16, 35, 0.25);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .divider-atau {
            text-align: center;
            font-size: 0.72rem;
            font-weight: 700;
            color: #374151;
            letter-spacing: 0.06em;
            margin: 0.75rem 0;
            text-transform: uppercase;
        }

        .btn-google {
            width: 100%;
            height: 42px;
            background-color: #ffffff;
            border: 1.2px solid #374151;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 600;
            font-size: 0.84rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-google:hover {
            background-color: #f9fafb;
            border-color: var(--brand-maroon);
            color: var(--brand-maroon);
        }

        .register-footer {
            text-align: center;
            font-size: 0.78rem;
            color: #1f2937;
            margin-top: 1.15rem;
        }

        .register-link {
            color: var(--brand-maroon);
            font-weight: 700;
            text-decoration: none;
            margin-left: 2px;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        /* Demo credentials pill */
        .demo-pill-wrap {
            margin-top: 0.85rem;
            text-align: center;
        }

        .demo-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s;
        }

        .demo-pill:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        /* Google notice modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #ffffff;
            border-radius: 12px;
            border: 1.5px solid var(--border-maroon);
            max-width: 400px;
            width: 100%;
            padding: 1.75rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);
            text-align: center;
            position: relative;
        }

        .modal-title {
            color: var(--brand-maroon);
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .modal-desc {
            font-size: 0.84rem;
            color: #4b5563;
            line-height: 1.45;
            margin-bottom: 1.25rem;
        }

        .btn-modal-close {
            background: var(--brand-maroon);
            color: #ffffff;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- ================= LEFT HERO PANEL ================= -->
        <div class="hero-panel">
            <div class="hero-overlay"></div>

            <!-- Top Left Logo & Title -->
            <div class="hero-content-top">
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol" style="width: 46px; height: 46px; object-fit: contain;">
                <div class="hero-brand-text">
                    <span class="hero-brand-title">SIMAHAD</span>
                    <span class="hero-brand-sub">Sistem Informasi Manajemen Ma'had</span>
                    <span class="hero-brand-inst">Al-Jami'ah Tuanku Imam Bonjol</span>
                </div>
            </div>

            <!-- Middle Text -->
            <div class="hero-content-middle">
                <div class="hero-welcome">Selamat Datang di</div>
                <div class="hero-simahad">SIMAHAD</div>
                <p class="hero-tagline">
                    Akses informasi, kelola data,<br>
                    wujudkan layanan Ma'had yang lebih baik.
                </p>
            </div>

            <!-- Building Facade Signage matching screenshot -->
            <div class="building-sign-overlay">
                <div class="building-sign-logo">
                    <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol" style="width: 26px; height: 26px; object-fit: contain;">
                </div>
                <div class="building-sign-text">MA'HAD AL-JAMI'AH</div>
                <div class="building-sign-sub">TUANKU IMAM BONJOL</div>
            </div>
        </div>

        <!-- ================= RIGHT FORM PANEL ================= -->
        <div class="form-panel">
            <!-- Top Motto Tagline -->
            <div class="motto-bar">
                Berakhlak | Berilmu | Berprestasi
            </div>

            <!-- Framed Login Form Card -->
            <div class="inner-frame">
                <!-- Center Emblem Logo -->
                <div class="center-emblem">
                    <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol" style="width: 54px; height: 54px; object-fit: contain;">
                </div>

                <!-- Titles -->
                <h1 class="form-headline">Masuk & Akses Layanan Ma'had</h1>
                <p class="form-subheadline">
                    Temukan kemudahan dalam mengakses layanan Ma'had melalui satu sistem.
                </p>

                <!-- Status Flash Message -->
                @if (session('status'))
                    <div class="alert-box alert-success">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                
{{-- Notifikasi registrasi berhasil, tetapi menunggu verifikasi --}}
@if (session('status'))
    <div class="alert-box alert-warning">
        <svg width="18" height="18" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            </path>
        </svg>
        <span>{{ session('status') }}</span>
    </div>
@endif
            {{-- Notifikasi berhasil --}}
            @if (session('success'))
                <div class="alert-box alert-success">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Notifikasi kesalahan --}}
            @if (session('error'))
                <div class="alert-box alert-error">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Kesalahan validasi --}}
            @if ($errors->any())
                <div class="alert-box alert-error">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
                <!-- Login Form -->
                <form action="{{ route('login.post') }}" method="POST" id="loginForm" class="auth-form" autocomplete="on">
                    @csrf

                    <!-- Field: Email atau Username -->
                    <div class="form-row">
                        <label for="login" class="field-label">Email atau Username</label>
                        <div class="field-input-wrapper">
                            <input 
                                type="text" 
                                id="login" 
                                name="login" 
                                class="field-input" 
                                placeholder="Masukkan email atau username"
                                value="{{ old('login') }}" 
                                required 
                                autofocus 
                                spellcheck="false"
                            >
                        </div>
                    </div>

                    <!-- Field: Kata Sandi -->
                    <div class="form-row">
                        <label for="password" class="field-label">Kata Sandi</label>
                        <div class="field-input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="field-input has-icon" 
                                placeholder="Masukkan kata sandi"
                                required
                            >
                            <button type="button" class="toggle-pwd-btn" id="togglePasswordBtn" aria-label="Lihat Kata Sandi">
                                <!-- Eye Icon -->
                                <svg id="eyeIcon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye Off Icon -->
                                <svg id="eyeOffIcon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Lupa Kata Sandi Link -->
                    <div class="forgot-link-wrap">
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa Kata Sandi?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="submitBtn">
                        Masuk
                    </button>

                    <!-- Divider -->
                    <div class="divider-atau">ATAU</div>

                    <!-- Google Sign-In Button -->
                    <button type="button" class="btn-google" onclick="openGoogleNotice()">
                        <!-- Google Multi-Color G Icon -->
                        <svg width="18" height="18" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Lanjutkan dengan Google</span>
                    </button>

                    <!-- Footer: Daftar di sini -->
                    <div class="register-footer">
                        Mahasiswa baru? <a href="{{ route('register') }}" class="register-link">Daftar di sini</a>
                    </div>
                </form>

                <!-- Quick Demo Pill for fast testing -->
                <div class="demo-pill-wrap">
                    <button type="button" class="demo-pill" onclick="fillDemoCredentials()">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Isi Akun Demo (NIM: <strong>240101</strong> / <strong>mahasiswa@asrama.ac.id</strong>)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Login Notice Modal -->
    <div id="googleModal" class="modal-overlay" onclick="closeGoogleModalOnBackdrop(event)">
        <div class="modal-box">
            <h3 class="modal-title">Integrasi Google Akun</h3>
            <p class="modal-desc">
                Fitur autentikasi Google Single Sign-On (SSO) sedang dipersiapkan khusus untuk domain <strong>@uinib.ac.id</strong>. Silakan gunakan <strong>Email atau Username</strong> Anda untuk masuk ke sistem.
            </p>
            <button type="button" class="btn-modal-close" onclick="closeGoogleModal()">Mengerti</button>
        </div>
    </div>

    <script>
        // Password toggle visibility
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        togglePasswordBtn.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        });

        // Quick demo filler
        function fillDemoCredentials() {
            document.getElementById('login').value = 'mahasiswa@asrama.ac.id';
            document.getElementById('password').value = 'password123';
            document.getElementById('password').focus();
        }

        // Google Modal Helpers
        function openGoogleNotice() {
            document.getElementById('googleModal').classList.add('active');
        }

        function closeGoogleModal() {
            document.getElementById('googleModal').classList.remove('active');
        }

        function closeGoogleModalOnBackdrop(event) {
            if (event.target === document.getElementById('googleModal')) {
                closeGoogleModal();
            }
        }

        // Form Submit Loading Feedback
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = 'Memproses...';
            btn.style.opacity = '0.85';
            btn.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>