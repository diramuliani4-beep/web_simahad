@php
    $penggunaPanel = auth()->user();
    $menuUtamaPanel = \App\Support\MenuPeran::utama($penggunaPanel);
    $menuBawahPanel = \App\Support\MenuPeran::bawah($penggunaPanel);
    $labelPeranPanel = \App\Support\MenuPeran::labelPeran($penggunaPanel);
    $berandaPanel = isset($menuUtamaPanel[0]) ? route($menuUtamaPanel[0]['route']) : url('/');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMAHAD') - {{ $namaSistem ?? 'SIMAHAD' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        :root {
            --maroon: #6B0D1B;
            --maroon-dark: #52070f;
            --primary-maroon: #6B0D1B;
            --gold: #C9A96A;
            --gold-soft: #f3e3bf;
            --maroon-tint: #F8EDEF;
            --tint: #F8EDEF;
            --ink: #221b1d;
            --text: #292929;
            --text-dark: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --muted: #6b7280;
            --line: #ecd9dd;
            --border: #eadfda;
            --background: #f7f5f4;
            --header-height: 68px;
            --sidebar-width: 280px;
            --radius-lg: 18px;
            --radius-md: 12px;
            --shadow-soft: 0 12px 30px rgba(59, 7, 15, .07);
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            font-size: 14px;
            color: var(--text);
            background: var(--background);
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
        }

        .panel-skip {
            position: absolute;
            left: -9999px;
            top: 8px;
            z-index: 2000;
            padding: 10px 16px;
            border-radius: 10px;
            background: var(--maroon);
            color: #fff;
            font-weight: 700;
        }

        .panel-skip:focus {
            left: 12px;
        }

        /* ---------------- Header ---------------- */
        .topbar {
            position: fixed;
            inset: 0 0 auto 0;
            height: var(--header-height);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 0 20px;
            background: #fff;
            border-bottom: 1px solid var(--line);
            box-shadow: 0 2px 14px rgba(59, 7, 15, .05);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .brand-teks {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            min-width: 0;
        }

        .brand-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: .03em;
            color: var(--maroon);
        }

        .brand-sub {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--gold);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .menu-toggle {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            padding: 0;
            border: 1px solid var(--line);
            border-radius: 11px;
            background: #fff;
            color: var(--maroon);
            cursor: pointer;
            transition: background .18s ease, border-color .18s ease;
        }

        .menu-toggle:hover {
            background: var(--maroon-tint);
            border-color: var(--gold);
        }

        .menu-toggle svg {
            width: 20px;
            height: 20px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ---------------- Content ---------------- */
        /* Sidebar bersama dirender oleh komponen panel.sidebar. */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: calc(var(--header-height) + 26px) 30px 40px;
            transition: margin-left .22s ease;
        }

        body.sb-sempit .main-content {
            margin-left: var(--sb-w-sempit, 84px);
        }

        .flash {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 16px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.45;
        }

        .flash--sukses {
            background: #eef7f0;
            border: 1px solid #cfe6d5;
            color: #22653a;
        }

        .flash--gagal {
            background: #fdeef0;
            border: 1px solid #f2ccd2;
            color: #8a1224;
        }

        /* Alias kelas lama agar halaman yang sudah ada tetap tampil benar. */
        .flash-success {
            margin-bottom: 16px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            background: #eef7f0;
            border: 1px solid #cfe6d5;
            color: #22653a;
            font-size: 13.5px;
            font-weight: 600;
        }

        .flash-error {
            margin-bottom: 16px;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            background: #fdeef0;
            border: 1px solid #f2ccd2;
            color: #8a1224;
            font-size: 13.5px;
            font-weight: 600;
        }

        /* ---------------- Mobile ---------------- */
        @media (max-width: 991px) {
            :root {
                --header-height: 62px;
            }

            .topbar {
                padding: 0 13px;
            }

            .brand-sub {
                display: none;
            }

            .brand-title {
                font-size: 17px;
            }

            .brand-logo {
                width: 34px;
                height: 34px;
            }

            .main-content,
            body.sb-sempit .main-content {
                margin-left: 0;
                padding: calc(var(--header-height) + 18px) 15px 30px;
            }
        }

        @media (max-width: 480px) {
            .brand-title {
                font-size: 15.5px;
            }

            .header-actions {
                gap: 6px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <a class="panel-skip" href="#konten-utama">Lewati ke konten</a>

    <header class="topbar">
        <div class="brand">
            <button type="button"
                class="menu-toggle"
                id="menuToggle"
                aria-label="Buka atau tutup menu"
                aria-controls="sidebar"
                aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round">
                    <path d="M3 6h18" />
                    <path d="M3 12h18" />
                    <path d="M3 18h18" />
                </svg>
            </button>

            <a href="{{ $berandaPanel }}" class="brand-link">
                <img src="{{ $logoAplikasi ?? asset('images/logo_uin.png') }}" alt="Logo {{ $namaSistem ?? 'SIMAHAD' }}" class="brand-logo" onerror="this.src='{{ asset('images/logo_uin.png') }}'">

                <span class="brand-teks">
                    <span class="brand-title">{{ $namaSistem ?? 'SIMAHAD' }}</span>
                    <span class="brand-sub">Ma’had Al-Jami’ah UIN Imam Bonjol</span>
                </span>
            </a>
        </div>

        <div class="header-actions">
            @auth
                <x-notifikasi-dropdown />
                <x-profile-dropdown />
            @endauth
        </div>
    </header>

    <x-panel.sidebar ragam="panel" />

    <main class="main-content" id="konten-utama">
        @if (session('success'))
            <div class="flash flash--sukses" role="status">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="flash flash--gagal" role="alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="flash flash--gagal" role="alert">{{ $errors->first() }}</div>
        @endif

        @yield('konten')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Submenu sidebar: buka/tutup lewat klik (desktop maupun sentuh).
            document.querySelectorAll('[data-menu-grup]').forEach(function (grup) {
                const tombol = grup.querySelector('[data-menu-toggle]');

                if (!tombol) {
                    return;
                }

                tombol.addEventListener('click', function () {
                    const terbuka = grup.classList.toggle('is-open');
                    tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
                });
            });

            // Buka/tutup laci sidebar ditangani komponen panel.sidebar agar sama di semua halaman.
        });
    </script>

    @stack('scripts')
</body>
</html>
