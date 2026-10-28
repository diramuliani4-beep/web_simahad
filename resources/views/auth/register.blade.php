<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SIMAHAD - Pendaftaran Akun Mahasiswa Baru</title>

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

        .register-card {
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
            .register-card {
                flex-direction: row;
                min-height: 680px;
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
                min-height: 680px;
            }
        }

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
            margin-bottom: 0.75rem;
        }

        .inner-frame {
            border: 1.5px solid var(--border-maroon);
            border-radius: 36px 36px 28px 28px;
            padding: 2rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        @media (min-width: 600px) {
            .inner-frame {
                padding: 2.25rem 2.5rem 2rem;
            }
        }

        .center-emblem {
            margin-bottom: 0.65rem;
        }

        .form-headline {
            color: var(--brand-maroon);
            font-size: 1.18rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.25rem;
        }

        .form-subheadline {
            color: #4b5563;
            font-size: 0.82rem;
            line-height: 1.35;
            text-align: center;
            max-width: 320px;
            margin-bottom: 1.25rem;
        }

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

        .auth-form {
            width: 100%;
        }

        .form-row {
            margin-bottom: 0.75rem;
            width: 100%;
        }

        .field-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .field-input {
            width: 100%;
            height: 38px;
            border: 1.5px solid var(--border-maroon);
            border-radius: 8px;
            padding: 0 12px;
            font-family: inherit;
            font-size: 0.85rem;
            color: #111827;
            background-color: #ffffff;
            outline: none;
            transition: all 0.2s ease;
        }

        .field-input::placeholder {
            color: #9ca3af;
            font-size: 0.82rem;
        }

        .field-input:focus {
            box-shadow: 0 0 0 3px rgba(120, 16, 35, 0.15);
            border-color: var(--brand-maroon);
        }

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
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background-color: var(--brand-maroon-hover);
            transform: translateY(-1px);
        }

        .register-footer {
            text-align: center;
            font-size: 0.78rem;
            color: #1f2937;
            margin-top: 1rem;
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
    </style>
</head>
<body>
    <div class="register-card">
        <!-- ================= LEFT HERO PANEL ================= -->
        <div class="hero-panel">
            <div class="hero-overlay"></div>

            <div class="hero-content-top">
                <x-simahad-emblem size="46" />
                <div class="hero-brand-text">
                    <span class="hero-brand-title">SIMAHAD</span>
                    <span class="hero-brand-sub">Sistem Informasi Manajemen Ma'had</span>
                    <span class="hero-brand-inst">Al-Jami'ah Tuanku Imam Bonjol</span>
                </div>
            </div>

            <div class="hero-content-middle">
                <div class="hero-welcome">Selamat Datang di</div>
                <div class="hero-simahad">SIMAHAD</div>
                <p class="hero-tagline">
                    Akses informasi, kelola data,<br>
                    wujudkan layanan Ma'had yang lebih baik.
                </p>
            </div>

            <div class="building-sign-overlay">
                <div class="building-sign-logo">
                    <x-simahad-emblem size="26" />
                </div>
                <div class="building-sign-text">MA'HAD AL-JAMI'AH</div>
                <div class="building-sign-sub">TUANKU IMAM BONJOL</div>
            </div>
        </div>

        <!-- ================= RIGHT FORM PANEL ================= -->
        <div class="form-panel">
            <div class="motto-bar">
                Berakhlak | Berilmu | Berprestasi
            </div>

            <div class="inner-frame">
                <div class="center-emblem">
                    <x-simahad-emblem size="50" />
                </div>

                <h1 class="form-headline">Pendaftaran Akun Mahasiswa</h1>
                <p class="form-subheadline">
                    Lengkapi data di bawah ini untuk membuat akun baru di sistem SIMAHAD.
                </p>

                @if ($errors->any())
                    <div class="alert-box alert-error">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST" class="auth-form" autocomplete="on">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="form-row">
                        <label for="name" class="field-label">Nama Lengkap</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="field-input" 
                            placeholder="Contoh: Muhammad Fikri" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus
                        >
                    </div>

                    <!-- NIM -->
                    <div class="form-row">
                        <label for="nim" class="field-label">NIM (Nomor Induk Mahasiswa)</label>
                        <input 
                            type="text" 
                            id="nim" 
                            name="nim" 
                            class="field-input" 
                            placeholder="Contoh: 240102" 
                            value="{{ old('nim') }}" 
                            required
                        >
                    </div>

                    <!-- Email -->
                    <div class="form-row">
                        <label for="email" class="field-label">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="field-input" 
                            placeholder="Contoh: fikri@uinib.ac.id" 
                            value="{{ old('email') }}" 
                            required
                        >
                    </div>

                    <!-- Kata Sandi -->
                    <div class="form-row">
                        <label for="password" class="field-label">Kata Sandi</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="field-input" 
                            placeholder="Minimal 6 karakter" 
                            required
                        >
                    </div>

                    <!-- Konfirmasi Kata Sandi -->
                    <div class="form-row">
                        <label for="password_confirmation" class="field-label">Konfirmasi Kata Sandi</label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            class="field-input" 
                            placeholder="Ulangi kata sandi" 
                            required
                        >
                    </div>

                    <button type="submit" class="btn-submit">
                        Daftar Akun Baru
                    </button>

                    <div class="register-footer">
                        Sudah punya akun? <a href="{{ route('login') }}" class="register-link">Masuk di sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
