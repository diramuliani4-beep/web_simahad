
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin') - {{ $namaSistem ?? 'SIMAHAD' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --maroon: #780d20;
            --border: #eadfda;
            --text: #292929;
            --muted: #77716e;
            --background: #f8f8f8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--background);
        }

        /* HEADER */
        .admin-header {
            height: 89px;
            background: #fff;
            border: 1px solid var(--maroon);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px 0 14px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* TOMBOL GARIS TIGA */
        .menu-toggle {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            color: var(--maroon);
            cursor: pointer;
        }

        .menu-toggle:hover {
            background: #f9eded;
        }

        .menu-toggle svg {
            width: 25px;
            height: 25px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 13px;
            text-decoration: none;
            color: var(--maroon);
        }

        .brand img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }

        .brand span {
            font-size: 31px;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .header-icon {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 50%;
            background: var(--maroon);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .profile-icon {
            width: 61px;
            height: 61px;
            border: 2px solid #555;
            border-radius: 50%;
            color: #555;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            padding: 0;
            overflow: hidden;
            cursor: pointer;
            font: inherit;
            transition: border-color .2s, color .2s;
        }

        .profile-icon:hover,
        .profile-icon[aria-expanded="true"] {
            border-color: var(--maroon);
            color: var(--maroon);
        }

        .profile-icon img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-icon svg {
            width: 44px;
            height: 44px;
        }

        .profile-menu {
            position: relative;
            display: flex;
            align-items: center;
        }

        .profile-panel {
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            width: 292px;
            max-width: calc(100vw - 32px);
            background: #fff;
            border: 1px solid #e7ded8;
            border-radius: 14px;
            box-shadow: 0 18px 42px rgba(56, 10, 18, .18);
            padding: 12px;
            z-index: 80;
        }

        .profile-panel[hidden] {
            display: none;
        }

        .profile-panel__head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 8px 14px;
            border-bottom: 1px solid #f0e9e4;
            margin-bottom: 8px;
        }

        .profile-panel__avatar {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #f6ece8;
            color: #790d20;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            overflow: hidden;
        }

        .profile-panel__avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-panel__identitas {
            min-width: 0;
        }

        .profile-panel__nama {
            font-size: 15px;
            font-weight: 600;
            color: #2b2b2b;
            line-height: 1.3;
        }

        .profile-panel__email {
            font-size: 12.5px;
            color: #8a8a8a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-panel__peran {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 9px;
            border-radius: 999px;
            background: #f6ece8;
            color: #790d20;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .profile-panel__item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 9px;
            color: #3d3d3d;
            font-size: 14.5px;
            font-weight: 500;
            text-decoration: none;
        }

        .profile-panel__item:hover {
            background: #faf5f2;
            color: #790d20;
        }

        .profile-panel__item svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .profile-panel__item--keluar {
            color: #b3261e;
        }

        .profile-panel__item--keluar:hover {
            background: #fdf0ef;
            color: #8f1d17;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 89px;
            bottom: 0;
            left: 0;
            width: 418px;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid var(--maroon);
            background-image:
                linear-gradient(
                    to bottom,
                    rgba(255,255,255,.28),
                    rgba(255,255,255,.55)
                ),
                url('/images/mahad_al_jamiah.jpeg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            z-index: 900;
            transition: width .25s ease, transform .25s ease;
        }

        .sidebar-menu {
            padding: 36px 18px 20px;
        }

        .menu-link {
            min-height: 50px;
            margin-bottom: 45px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #111;
            background: #fff;
            border: 1px solid var(--maroon);
            border-radius: 15px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            font-family: inherit;
            transition: background-color .2s ease, box-shadow .2s ease;
        }

        /* Warna ketika mouse diarahkan ke menu */
        .menu-link:hover {
            background-color: #f9eded;
            color: var(--maroon);
        }

        /* Warna menu yang sedang dibuka */
        .menu-link.active {
            background-color: #f3dfe3;
            color: var(--maroon);
            border: 1px solid var(--maroon);
            box-shadow: 0 2px 5px rgba(0,0,0,.12);
        }

        .menu-link svg {
            width: 27px;
            height: 27px;
            flex-shrink: 0;
            color: var(--maroon);
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 10px 18px 15px;
        }

        .sidebar-bottom .menu-link {
            margin-bottom: 13px;
        }

        .sidebar-bottom .menu-link:last-child {
            margin-bottom: 0;
        }

        .sidebar form {
            margin: 0;
        }

        /* KONTEN UTAMA */
        .main-content {
            margin-left: 418px;
            padding: 130px 25px 35px;
            min-height: 100vh;
            transition: margin-left .25s ease;
        }

        /* SIDEBAR TERTUTUP KHUSUS LAYAR PC */
        @media (min-width: 1001px) {
            body.sidebar-collapsed .sidebar {
                width: 0;
                transform: translateX(-100%);
                border-right: none;
            }

            body.sidebar-collapsed .main-content {
                margin-left: 0;
            }
        }

        /* TABLET */
        @media (max-width: 1000px) {
            .sidebar {
                width: 280px;
            }

            .main-content {
                margin-left: 280px;
                padding-left: 24px;
                padding-right: 24px;
            }

            body.sidebar-collapsed .main-content {
                margin-left: 0;
            }

            .brand span {
                font-size: 25px;
            }
        }

        /* HP */
        @media (max-width: 700px) {
            .admin-header {
                height: 72px;
                padding: 0 12px;
            }

            .header-left {
                gap: 8px;
            }

            .menu-toggle {
                width: 36px;
                height: 36px;
            }

            .menu-toggle svg {
                width: 22px;
                height: 22px;
            }

            .brand {
                gap: 7px;
            }

            .brand img {
                width: 43px;
                height: 43px;
            }

            .brand span {
                font-size: 20px;
            }

            .header-actions {
                gap: 10px;
            }

            .header-icon {
                width: 33px;
                height: 33px;
            }

            .profile-icon {
                width: 42px;
                height: 42px;
            }

            .profile-icon svg {
                width: 31px;
                height: 31px;
            }

            .sidebar {
                top: 72px;
                width: 280px;
                transform: translateX(-100%);
                box-shadow: 4px 0 12px rgba(0,0,0,.12);
            }

            body.sidebar-collapsed .sidebar {
                width: 280px;
                transform: translateX(-100%);
            }

            body.sidebar-mobile-open .sidebar {
                width: 280px;
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 100px 16px 25px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <header class="admin-header">
        <div class="header-left">

            <button type="button"
                class="menu-toggle"
                id="menuToggle"
                aria-label="Buka atau tutup sidebar"
                aria-controls="sidebar"
                aria-expanded="false">

                <svg viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round">
                    <path d="M3 6h18"/>
                    <path d="M3 12h18"/>
                    <path d="M3 18h18"/>
                </svg>
            </button>

            <a href="{{ url('/admin/dashboard') }}" class="brand">
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD">
                <span>{{ $namaSistem ?? 'SIMAHAD' }}</span>
            </a>
        </div>

        <div class="header-actions">
            <button type="button"
                class="header-icon"
                title="Notifikasi"
                aria-label="Notifikasi">

                <svg viewBox="0 0 24 24"
                    width="27"
                    height="27"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>
            </button>

            <div class="profile-menu">
                <button type="button"
                    class="profile-icon"
                    id="profileMenuToggle"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-controls="profileMenuPanel"
                    title="Menu akun">

                    @if (auth()->user()?->foto_profil)
                        <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}"
                            alt="Foto profil {{ auth()->user()->name }}">
                    @else
                        <svg viewBox="0 0 48 48" fill="currentColor" aria-hidden="true">
                            <circle cx="24" cy="16" r="11"/>
                            <path d="M5 44c0-11 8-17 19-17s19 6 19 17z"/>
                        </svg>
                    @endif
                </button>

                <div class="profile-panel" id="profileMenuPanel" hidden>
                    <div class="profile-panel__head">
                        <div class="profile-panel__avatar">
                            @if (auth()->user()?->foto_profil)
                                <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}"
                                    alt="Foto profil {{ auth()->user()->name }}">
                            @else
                                {{ strtoupper(mb_substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                            @endif
                        </div>

                        <div class="profile-panel__identitas">
                            <div class="profile-panel__nama">
                                {{ auth()->user()?->name ?? 'Administrator' }}
                            </div>
                            <div class="profile-panel__email">
                                {{ auth()->user()?->email ?? '-' }}
                            </div>
                            <span class="profile-panel__peran">Admin</span>
                        </div>
                    </div>

                    <a href="{{ route('admin.pengaturan-akun') }}" class="profile-panel__item">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                        </svg>
                        <span>Pengaturan Akun</span>
                    </a>

                    <a href="{{ route('logout.confirm') }}"
                        class="profile-panel__item profile-panel__item--keluar">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">
                            <path d="M10 17l5-5-5-5"/>
                            <path d="M15 12H3"/>
                            <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                        </svg>
                        <span>Keluar</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- SIDEBAR ADMIN --}}
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-menu">

            <a href="{{ url('/admin/dashboard_admin') }}"
               class="menu-link {{ request()->is('admin/dashboard_admin') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="2"/>
                    <rect x="14" y="3" width="7" height="7" rx="2"/>
                    <rect x="3" y="14" width="7" height="7" rx="2"/>
                    <rect x="14" y="14" width="7" height="7" rx="2"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.manajemen-pengguna') }}"
                class="menu-link {{ request()->is('admin/manajemen-pengguna*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="8" r="3"/>
                        <path d="M3 20v-2a6 6 0 0 1 12 0v2"/>
                        <circle cx="18" cy="9" r="2"/>
                        <path d="M17 15a5 5 0 0 1 4 5"/>
                    </svg>
                    <span>Manajemen Pengguna</span>
                </a>
            <a href="{{ route('admin.data-pegawai.index') }}"
               class="menu-link {{ request()->is('admin/data-pegawai*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <circle cx="9" cy="10" r="2"/>
                    <path d="M6 16c0-2 6-2 6 0M15 9h3M15 13h3"/>
                </svg>
                <span>Data Pegawai</span>
            </a>

            <a href="{{ route('admin.data-mahasiswa.index') }}"
               class="menu-link {{ request()->is('admin/data-mahasiswa*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="m2 10 10-5 10 5-10 5z"/>
                    <path d="M6 12v5c4 3 8 3 12 0v-5"/>
                    <path d="M22 10v7"/>
                </svg>
                <span>Data Mahasiswa</span>
            </a>

                <a href="{{ route('admin.log-aktivitas') }}"
                    class="menu-link {{ request()->is('admin/log-aktivitas*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                        <path d="M3 4v5h5"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                    <span>Log Aktivitas</span>
                </a>
        </nav>

        <div class="sidebar-bottom">
            <a href="{{ route('admin.pengaturan-sistem') }}"
        class="menu-link {{ request()->is('admin/pengaturan-sistem*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 3.1-.2-.1a1.7 1.7 0 0 0-1.9.2l-.2.1h-3.5l-.1-.2a1.7 1.7 0 0 0-1.6-1l-.3.1-3.1-1.8.1-.2a1.7 1.7 0 0 0-.2-1.9l-.1-.2v-3.5l.2-.1a1.7 1.7 0 0 0 1-1.6l-.1-.3 1.8-3.1.2.1a1.7 1.7 0 0 0 1.9-.2l.2-.1h3.5l.1.2a1.7 1.7 0 0 0 1.6 1l.3-.1 3.1 1.8-.1.2a1.7 1.7 0 0 0 .2 1.9l.1.2V15z"/>
            </svg>
            <span>Pengaturan Sistem</span>
        </a>

        <a href="{{ route('logout.confirm') }}" class="menu-link">
    <svg viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <path d="M10 17l5-5-5-5"/>
        <path d="M15 12H3"/>
        <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
    </svg>
    <span>Keluar</span>
</a>


        </div>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
                
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menuToggle');

            if (!menuToggle) return;

            const STORAGE_KEY = 'simahad_sidebar_collapsed';

            function updateExpanded() {
                let isOpen;

                if (window.innerWidth <= 700) {
                    isOpen = document.body.classList.contains('sidebar-mobile-open');
                } else if (window.innerWidth > 1000) {
                    isOpen = !document.body.classList.contains('sidebar-collapsed');
                } else {
                    isOpen = true;
                }

                menuToggle.setAttribute('aria-expanded', String(isOpen));
            }

            // Mengembalikan kondisi sidebar PC yang terakhir dipilih
            function restoreSidebar() {
                if (window.innerWidth > 1000) {
                    const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';

                    document.body.classList.toggle('sidebar-collapsed', isCollapsed);
                    document.body.classList.remove('sidebar-mobile-open');
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                }

                updateExpanded();
            }

            // Tombol buka/tutup sidebar
            menuToggle.addEventListener('click', function () {
                if (window.innerWidth <= 700) {
                    document.body.classList.toggle('sidebar-mobile-open');
                } else if (window.innerWidth > 1000) {
                    document.body.classList.toggle('sidebar-collapsed');

                    localStorage.setItem(
                        STORAGE_KEY,
                        document.body.classList.contains('sidebar-collapsed')
                    );
                }

                updateExpanded();
            });

            // Menutup sidebar saat klik area luar khusus HP
            document.addEventListener('click', function (event) {
                if (
                    window.innerWidth <= 700 &&
                    document.body.classList.contains('sidebar-mobile-open') &&
                    !event.target.closest('#sidebar') &&
                    !event.target.closest('#menuToggle')
                ) {
                    document.body.classList.remove('sidebar-mobile-open');
                    updateExpanded();
                }
            });

            window.addEventListener('resize', restoreSidebar);

            restoreSidebar();
        });

        // Panel menu akun di sudut kanan atas header
        document.addEventListener('DOMContentLoaded', function () {
            const tombolMenu = document.getElementById('profileMenuToggle');
            const panelMenu = document.getElementById('profileMenuPanel');

            if (!tombolMenu || !panelMenu) return;

            function tutupPanel() {
                panelMenu.hidden = true;
                tombolMenu.setAttribute('aria-expanded', 'false');
            }

            tombolMenu.addEventListener('click', function (event) {
                event.stopPropagation();

                const akanDibuka = panelMenu.hidden;

                panelMenu.hidden = !akanDibuka;
                tombolMenu.setAttribute('aria-expanded', String(akanDibuka));
            });

            document.addEventListener('click', function (event) {
                if (!panelMenu.hidden && !event.target.closest('.profile-menu')) {
                    tutupPanel();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    tutupPanel();
                }
            });
        });

    </script>

    @stack('scripts')
</body>
</html>
