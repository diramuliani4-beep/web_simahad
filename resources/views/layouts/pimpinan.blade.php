
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Pimpinan') - SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --maroon: #780d20;
            --maroon-dark: #650919;
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
        .pimpinan-header {
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
            min-width: 0;
        }

        /* TOMBOL GARIS TIGA */
        .menu-toggle {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eadfda;
            border-radius: 8px;
            background: #fff;
            color: var(--maroon);
            cursor: pointer;
            transition: background .2s, color .2s;
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
            color: white;
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
            flex-shrink: 0;
        }

        .profile-icon svg {
            width: 44px;
            height: 44px;
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
            transition: background .2s, color .2s;
        }

        .menu-link:hover,
        .menu-link.active {
            background: #f9eded;
            color: #111;
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
            padding: 110px 4.1% 35px;
            min-height: 100vh;
            transition: margin-left .25s ease;
        }

        /* SIDEBAR DITUTUP PADA DESKTOP */
        body.sidebar-collapsed .sidebar {
            width: 0;
            transform: translateX(-100%);
            border-right: none;
        }

        body.sidebar-collapsed .main-content {
            margin-left: 0;
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
            .pimpinan-header {
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

            body.sidebar-collapsed.sidebar-mobile-open .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 92px 16px 25px;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="sidebar-collapsed">

    <header class="pimpinan-header">

        <div class="header-left">

            {{-- TOMBOL GARIS TIGA --}}
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

            {{-- LOGO SIMAHAD --}}
            <a href="{{ url('/pimpinan/dashboard') }}" class="brand">
                <img src="{{ asset('images/logo_uin.png') }}"
                    alt="Logo SIMAHAD">
                <span>SIMAHAD</span>
            </a>

        </div>

        {{-- NOTIFIKASI DAN PROFIL --}}
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

            <div class="profile-icon" title="Profil Pimpinan">
                <svg viewBox="0 0 48 48" fill="currentColor">
                    <circle cx="24" cy="16" r="11"/>
                    <path d="M5 44c0-11 8-17 19-17s19 6 19 17z"/>
                </svg>
            </div>

        </div>

    </header>

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">

        <nav class="sidebar-menu">

            <a href="{{ url('/pimpinan/dashboard') }}"
                class="menu-link {{ request()->is('pimpinan/dashboard') ? 'active' : '' }}">

                <svg viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" rx="2"/>
                    <rect x="14" y="3" width="7" height="7" rx="2"/>
                    <rect x="3" y="14" width="7" height="7" rx="2"/>
                    <rect x="14" y="14" width="7" height="7" rx="2"/>
                </svg>

                <span>Dashboard</span>
            </a>

            
<a href="{{ route('pimpinan.data-penghuni') }}"
    class="menu-link {{ request()->is('pimpinan/data-penghuni*') ? 'active' : '' }}">
    <svg viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round">
        <circle cx="9" cy="8" r="3"/>
        <path d="M3 20v-2a6 6 0 0 1 12 0v2"/>
        <circle cx="18" cy="9" r="2"/>
        <path d="M17 15a5 5 0 0 1 4 5"/>
    </svg>
    <span>Data Penghuni</span>
</a>


            <a href="{{ url('/pimpinan/laporan') }}"
                class="menu-link {{ request()->is('pimpinan/laporan*') ? 'active' : '' }}">

                <svg viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M4 3h16v15H8l-4 3z"/>
                    <path d="M8 8h8M8 12h8"/>
                </svg>

                <span>Laporan Ma’had</span>
            </a>

            <a href="{{ url('/pimpinan/pembelajaran') }}"
                class="menu-link {{ request()->is('pimpinan/pembelajaran*') ? 'active' : '' }}">

                <svg viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M12 7v14"/>
                    <path d="M3 5a2 2 0 0 1 2-2h7v18H5a2 2 0 0 0-2 2z"/>
                    <path d="M21 5a2 2 0 0 0-2-2h-7v18h7a2 2 0 0 1 2 2z"/>
                </svg>

                <span>Pembelajaran</span>
            </a>

        </nav>

        {{-- MENU BAWAH --}}
        <div class="sidebar-bottom">

            <a href="{{ url('/pimpinan/pengaturan-akun') }}"
                class="menu-link {{ request()->is('pimpinan/pengaturan-akun*') ? 'active' : '' }}">

                <svg viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 3.1-.2-.1a1.7 1.7 0 0 0-1.9.2l-.2.1h-3.5l-.1-.2a1.7 1.7 0 0 0-1.6-1l-.3.1-3.1-1.8.1-.2a1.7 1.7 0 0 0-.2-1.9l-.1-.2v-3.5l.2-.1a1.7 1.7 0 0 0 1-1.6l-.1-.3 1.8-3.1.2.1a1.7 1.7 0 0 0 1.9-.2l.2-.1h3.5l.1.2a1.7 1.7 0 0 0 1.6 1l.3-.1 3.1 1.8-.1.2a1.7 1.7 0 0 0 .2 1.9l.1.2V15z"/>
                </svg>

                <span>Pengaturan Akun</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="menu-link"
                    style="width:100%; cursor:pointer; font-family:inherit; text-align:left;">

                    <svg viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M10 17l5-5-5-5"/>
                        <path d="M15 12H3"/>
                        <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                    </svg>

                    <span>Keluar</span>
                </button>
            </form>

        </div>

    </aside>

    {{-- KONTEN HALAMAN --}}
    <main class="main-content">
        @yield('content')
    </main>

    {{-- FUNGSI BUKA/TUTUP SIDEBAR --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menuToggle');

            if (!menuToggle) {
                return;
            }

            function updateExpanded() {
                const isOpen = window.innerWidth <= 700
                    ? document.body.classList.contains('sidebar-mobile-open')
                    : !document.body.classList.contains('sidebar-collapsed');

                menuToggle.setAttribute('aria-expanded', String(isOpen));
            }

            menuToggle.addEventListener('click', function () {
                if (window.innerWidth <= 700) {
                    document.body.classList.toggle('sidebar-mobile-open');
                } else {
                    document.body.classList.toggle('sidebar-collapsed');
                }

                updateExpanded();
            });

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

            window.addEventListener('resize', function () {
                document.body.classList.remove('sidebar-mobile-open');
                updateExpanded();
            });

            updateExpanded();
        });
    </script>

    @stack('scripts')

</body>
</html>