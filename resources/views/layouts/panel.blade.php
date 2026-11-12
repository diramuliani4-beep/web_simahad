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
            --sidebar-width: 268px;
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

        /* ---------------- Sidebar ---------------- */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            z-index: 900;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 16px 13px 18px;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid var(--line);
            transition: transform .26s ease;
        }

        .sidebar-role {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            margin-bottom: 6px;
            border-radius: var(--radius-md);
            background: var(--maroon-tint);
            border: 1px solid #f0dde1;
        }

        .sidebar-role-ikon {
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: 10px;
            background: var(--maroon);
            color: var(--gold);
        }

        .sidebar-role-ikon svg {
            width: 17px;
            height: 17px;
        }

        .sidebar-role-teks {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            min-width: 0;
        }

        .sidebar-role-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #9b7d84;
        }

        .sidebar-role-nama {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--maroon);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .menu-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            min-height: 42px;
            padding: 10px 13px;
            border-radius: var(--radius-md);
            color: #463b3d;
            font-size: 13.6px;
            font-weight: 600;
            line-height: 1.25;
            transition: background .16s ease, color .16s ease;
        }

        .menu-link .ikon-menu {
            width: 19px;
            height: 19px;
            flex-shrink: 0;
            color: #8b7b7f;
            transition: color .16s ease;
        }

        .menu-link:hover {
            background: var(--maroon-tint);
            color: var(--maroon);
        }

        .menu-link:hover .ikon-menu {
            color: var(--maroon);
        }

        .menu-link.active {
            background: var(--maroon-tint);
            color: var(--maroon);
            font-weight: 700;
        }

        .menu-link.active .ikon-menu {
            color: var(--gold);
        }

        .menu-link.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 22px;
            border-radius: 0 4px 4px 0;
            background: var(--gold);
        }

        .menu-link--keluar {
            color: #9a3b45;
        }

        .menu-link--keluar:hover {
            background: #fdeef0;
            color: #8a1224;
        }

        /* Menu induk dengan submenu (expand/collapse klik) */
        .menu-grup {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .menu-grup-toggle {
            border: 0;
            background: transparent;
            font-family: inherit;
            text-align: left;
            cursor: pointer;
        }

        .menu-grup-toggle .menu-chevron {
            width: 15px;
            height: 15px;
            margin-left: auto;
            flex-shrink: 0;
            color: #9b8a8e;
            transition: transform .22s ease, color .16s ease;
        }

        .menu-grup.is-active > .menu-grup-toggle {
            background: var(--maroon-tint);
            color: var(--maroon);
            font-weight: 700;
        }

        .menu-grup.is-active > .menu-grup-toggle .ikon-menu {
            color: var(--gold);
        }

        .menu-grup.is-active > .menu-grup-toggle::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 22px;
            border-radius: 0 4px 4px 0;
            background: var(--gold);
        }

        .menu-grup.is-open > .menu-grup-toggle .menu-chevron {
            transform: rotate(180deg);
            color: var(--maroon);
        }

        .menu-sub {
            display: none;
            flex-direction: column;
            gap: 2px;
            margin: 2px 0 6px 14px;
            padding-left: 10px;
            border-left: 1px solid #f0dde1;
        }

        .menu-grup.is-open .menu-sub {
            display: flex;
        }

        .menu-sub-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 11px;
            border-radius: 10px;
            color: #5c5154;
            font-size: 12.8px;
            font-weight: 600;
            line-height: 1.25;
            transition: background .16s ease, color .16s ease;
        }

        .menu-sub-link .ikon-menu {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            color: #9b8a8e;
        }

        .menu-sub-link:hover {
            background: var(--maroon-tint);
            color: var(--maroon);
        }

        .menu-sub-link:hover .ikon-menu,
        .menu-sub-link.active .ikon-menu {
            color: var(--maroon);
        }

        .menu-sub-link.active {
            background: var(--maroon-tint);
            color: var(--maroon);
            font-weight: 700;
        }

        .sidebar-bottom {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding-top: 10px;
            margin-top: 6px;
            border-top: 1px dashed #eedfe2;
        }

        .sidebar-backdrop {
            display: none;
        }

        /* ---------------- Content ---------------- */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: calc(var(--header-height) + 26px) 30px 40px;
            transition: margin-left .26s ease;
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

            .sidebar {
                width: min(var(--sidebar-width), 84vw);
                transform: translateX(-104%);
                box-shadow: 0 20px 50px rgba(40, 5, 11, .25);
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: calc(var(--header-height) + 18px) 15px 30px;
            }

            .sidebar-backdrop {
                position: fixed;
                inset: var(--header-height) 0 0 0;
                z-index: 850;
                background: rgba(33, 10, 14, .42);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
            }

            body.sidebar-open .sidebar-backdrop {
                display: block;
                opacity: 1;
                pointer-events: auto;
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
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD" class="brand-logo" onerror="this.style.display='none'">

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

    <aside class="sidebar" id="sidebar" aria-label="Menu utama">
        <div class="sidebar-role">
            <span class="sidebar-role-ikon">
                <x-panel.ikon nama="pengguna" />
            </span>

            <span class="sidebar-role-teks">
                <span class="sidebar-role-label">{{ $labelPeranPanel }}</span>
                <span class="sidebar-role-nama">{{ $penggunaPanel?->name ?? 'Pengguna' }}</span>
            </span>
        </div>

        <nav class="sidebar-nav">
            @foreach ($menuUtamaPanel as $menu)
                @if (! empty($menu['anak']))
                    @php
                        $grupAktif = request()->routeIs(...$menu['aktif']);
                    @endphp

                    <div class="menu-grup {{ $grupAktif ? 'is-active is-open' : '' }}" data-menu-grup>
                        <button type="button"
                            class="menu-link menu-grup-toggle"
                            data-menu-toggle
                            aria-expanded="{{ $grupAktif ? 'true' : 'false' }}">
                            <x-panel.ikon :nama="$menu['ikon']" />
                            <span>{{ $menu['label'] }}</span>
                            <svg class="menu-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div class="menu-sub">
                            @foreach ($menu['anak'] as $anak)
                                <a href="{{ route($anak['route']) }}"
                                    class="menu-sub-link {{ request()->routeIs(...$anak['aktif']) ? 'active' : '' }}"
                                    @if (request()->routeIs(...$anak['aktif'])) aria-current="page" @endif>
                                    <x-panel.ikon :nama="$anak['ikon']" />
                                    <span>{{ $anak['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route($menu['route']) }}"
                        class="menu-link {{ request()->routeIs(...$menu['aktif']) ? 'active' : '' }}"
                        @if (request()->routeIs(...$menu['aktif'])) aria-current="page" @endif>
                        <x-panel.ikon :nama="$menu['ikon']" />
                        <span>{{ $menu['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="sidebar-bottom">
            @foreach ($menuBawahPanel as $menu)
                <a href="{{ route($menu['route']) }}"
                    class="menu-link {{ request()->routeIs(...$menu['aktif']) ? 'active' : '' }}"
                    @if (request()->routeIs(...$menu['aktif'])) aria-current="page" @endif>
                    <x-panel.ikon :nama="$menu['ikon']" />
                    <span>{{ $menu['label'] }}</span>
                </a>
            @endforeach

            <a href="{{ route('logout.confirm') }}" class="menu-link menu-link--keluar">
                <x-panel.ikon nama="keluar" />
                <span>Keluar</span>
            </a>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

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

            const menuToggle = document.getElementById('menuToggle');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (!menuToggle) {
                return;
            }

            function tutupSidebar() {
                document.body.classList.remove('sidebar-open');
                menuToggle.setAttribute('aria-expanded', 'false');
            }

            menuToggle.addEventListener('click', function () {
                const terbuka = document.body.classList.toggle('sidebar-open');
                menuToggle.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
            });

            if (backdrop) {
                backdrop.addEventListener('click', tutupSidebar);
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    tutupSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
