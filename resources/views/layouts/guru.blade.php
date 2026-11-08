
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Guru') - SIMAHAD</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --maroon: #800d22;
            --maroon-dark: #590018;
            --header-height: 90px;
            --sidebar-width: 280px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f7f7f7;
            color: #111;
        }

        .topbar {
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background: #fff;
            border: 1px solid var(--maroon);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--maroon);
            font-size: 30px;
            font-weight: bold;
        }

        .menu-toggle {
            flex-shrink: 0;
            border: none;
            background: transparent;
            color: var(--maroon);
            font-size: 32px;
            line-height: 1;
            cursor: pointer;
            padding: 5px;
        }

        .brand-logo {
            display: block;
            width: 55px;
            height: 55px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notification-button {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 0;
            border: 2px solid var(--maroon);
            border-radius: 50%;
            background: #fff;
            color: var(--maroon);
            cursor: pointer;
        }

        .notification-button svg {
            width: 23px;
            height: 23px;
        }

        .notification-button:hover {
            background: var(--maroon);
            color: #fff;
        }

        .profile-button {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: transparent;
            cursor: pointer;
        }

        .profile-button svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            padding: 25px 20px;
            overflow-y: auto;
            z-index: 900;
            background: #e9f2fa;
            border-right: 1px solid var(--maroon);
            transition: transform 0.3s ease;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .sidebar a,
        .logout-button {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            min-height: 50px;
            padding: 12px;
            border: 1px solid var(--maroon);
            border-radius: 15px;
            background: #fff;
            color: #111;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active,
        .logout-button:hover {
            background: var(--maroon);
            color: #fff;
        }

        .menu-icon {
            width: 25px;
            flex-shrink: 0;
            text-align: center;
            font-size: 20px;
        }

        .logout-form {
            width: 100%;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 125px 35px 35px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed .main-content {
            margin-left: 0;
        }

        .sidebar-overlay {
            display: none;
        }

        @media (max-width: 768px) {
            :root {
                --header-height: 75px;
            }

            .topbar {
                padding: 0 12px;
            }

            .brand {
                gap: 8px;
                font-size: 22px;
            }

            .brand-logo {
                width: 40px;
                height: 40px;
            }

            .header-actions {
                gap: 8px;
            }

            .notification-button {
                width: 34px;
                height: 34px;
            }

            .profile-button {
                width: 36px;
                height: 36px;
            }

            .sidebar {
                top: var(--header-height);
                width: min(var(--sidebar-width), 85vw);
                transform: translateX(-100%);
            }

            body:not(.sidebar-collapsed) .sidebar {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 105px 15px 25px;
            }

            body.sidebar-collapsed .sidebar {
                transform: translateX(-100%);
            }

            .sidebar-overlay {
                position: fixed;
                top: var(--header-height);
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 800;
                background: rgba(0, 0, 0, 0.35);
            }

            body:not(.sidebar-collapsed) .sidebar-overlay {
                display: block;
            }
        }

        @media (max-width: 420px) {
            .brand {
                gap: 6px;
                font-size: 18px;
            }

            .brand-logo {
                width: 32px;
                height: 32px;
            }

            .topbar {
                padding: 0 8px;
            }

            .header-actions {
                gap: 6px;
            }
        }
    </style>
</head>

<body class="sidebar-collapsed">

    <header class="topbar">
        <div class="brand">
            <button
                type="button"
                class="menu-toggle"
                id="menuToggle"
                aria-label="Buka atau tutup sidebar"
                aria-expanded="false"
                aria-controls="sidebar"
            >☰</button>

            <img
                src="{{ asset('images/logo_uin.png') }}"
                alt="Logo SIMAHAD"
                class="brand-logo"
                onerror="this.style.display='none'"
            >

            <span>SIMAHAD</span>
        </div>

        <div class="header-actions">
            <x-notifikasi-dropdown />

            <button
                type="button"
                class="profile-button"
                aria-label="Profil pengguna"
                title="Profil pengguna"
            >
                <svg viewBox="0 0 48 48" aria-hidden="true">
                    <circle cx="24" cy="24" r="22"
                        fill="#fff" stroke="#414141" stroke-width="2"/>
                    <circle cx="24" cy="17" r="8" fill="#414141"/>
                    <path d="M9 39c1-9 7-14 15-14s14 5 15 14"
                        fill="#414141"/>
                </svg>
            </button>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <nav>
            <a
                href="{{ route('guru.dashboard') }}"
                class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"
            >
                <span class="menu-icon">▦</span>
                <span>Dashboard</span>
            </a>

            
<a href="{{ route('guru.jadwal-saya') }}"
    class="{{ request()->is('guru/jadwal-saya*') ? 'active' : '' }}">
    <span class="menu-icon">▤</span>
    <span>Jadwal Saya</span>
</a>


            
<a href="{{ route('guru.absensi') }}"
    class="{{ request()->is('guru/absensi*') ? 'active' : '' }}">
    <span class="menu-icon">▣</span>
    <span>Absensi</span>
</a>

        
<a href="{{ route('guru.kegiatan-dokumentasi') }}"
    class="{{ request()->is('guru/kegiatan-dokumentasi*') ? 'active' : '' }}">
    <span class="menu-icon">▯</span>
    <span>Kegiatan &amp; Dokumentasi</span>
</a>

            <a href="{{ route('guru.tugas.index') }}"
                class="{{ request()->routeIs('guru.tugas.*', 'guru.pengumpulan.*') ? 'active' : '' }}">
                <span class="menu-icon">✎</span>
                <span>Tugas</span>
            </a>

            <a href="{{ url('/guru/pengaturan-akun') }}"
                class="{{ request()->is('guru/pengaturan-akun*') ? 'active' : '' }}">
                <span class="menu-icon">⚙</span>
                <span>Pengaturan Akun</span>
            </a>

            <a href="{{ route('logout.confirm') }}" class="logout-button">
                <span class="menu-icon">⇥</span>
                <span>Keluar</span>
            </a>
        </nav>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menuToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (!menuToggle || !sidebarOverlay) return;

            function toggleSidebar() {
                document.body.classList.toggle('sidebar-collapsed');

                const isOpen =
                    !document.body.classList.contains('sidebar-collapsed');

                menuToggle.setAttribute('aria-expanded', String(isOpen));
            }

            menuToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);
        });
    </script>

</body>
</html>