
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelajaran | SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --maroon: #790c20;
            --maroon-light: #f8edef;
            --border: #8a1428;
            --text: #171717;
            --muted: #777;
            --page: #f8f8f8;
            --white: #fff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--page);
            font-size: 14px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font: inherit;
        }

        /* =========================
           HEADER
        ========================= */

        .topbar {
            height: 89px;
            background: #fff;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 31px 0 20px;
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 65px;
            height: 62px;
            color: var(--maroon);
        }

        .brand-name {
            font-size: 30px;
            font-weight: 700;
            color: var(--maroon);
            letter-spacing: -.8px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .notification {
            width: 36px;
            height: 36px;
            background: var(--maroon);
            border: none;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: white;
            cursor: pointer;
        }

        .notification svg {
            width: 27px;
            height: 27px;
            stroke: white;
            fill: none;
            stroke-width: 1.8;
        }

        .profile {
            width: 61px;
            height: 61px;
            border: 1.5px solid #222;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .profile svg {
            width: 57px;
            height: 57px;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            position: fixed;
            top: 89px;
            bottom: 0;
            left: 0;
            width: 300px;
            overflow-y: auto;
            border: 1px solid var(--border);
            background:
                linear-gradient(
                    to bottom,
                    rgba(255,255,255,.35),
                    rgba(255,255,255,.68)
                ),
                url("{{ asset('images/mahad_al_jamiah.jpeg') }}");
            background-size: cover;
            background-position: center center;
            display: flex;
            flex-direction: column;
            z-index: 50;
        }

        .sidebar-menu {
            padding: 22px 12px 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .nav-pill {
            min-height: 46px;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: rgba(255,255,255,.97);
            color: #111;
            font-size: 14px;
            font-weight: 400;
            transition: background .2s, color .2s;
            cursor: pointer;
        }

        .nav-pill:hover {
            background: var(--maroon-light);
            color: var(--maroon);
        }

        .nav-pill.active {
            background: var(--maroon-light);
            color: var(--maroon);
            box-shadow: 0 3px 5px rgba(0,0,0,.12);
            font-weight: 600;
        }

        .nav-icon {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            color: #626d7e;
        }

        .nav-pill.active .nav-icon {
            color: var(--maroon);
        }

        .nav-icon svg {
            width: 21px;
            height: 21px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .nav-label {
            flex: 1;
        }

        .nav-arrow {
            color: var(--maroon);
            font-size: 25px;
            line-height: 1;
            font-weight: 700;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 18px 12px 20px;
            border-top: 1px solid var(--border);
        }

        .sidebar-bottom .nav-pill {
            margin-bottom: 12px;
        }

        .sidebar-bottom .nav-pill:last-child {
            margin-bottom: 0;
        }

        .logout-form {
            width: 100%;
            margin: 0;
        }

        .logout-button {
            width: 100%;
            text-align: left;
            font-family: inherit;
        }

        /* =========================
           MAIN CONTENT
        ========================= */

        .main {
            margin-left: 300px;
            padding: 105px 35px 35px;
            min-height: 100vh;
            min-width: 0;
        }

        .breadcrumb {
            font-size: 20px;
            color: var(--maroon);
            margin-bottom: 1px;
        }

        .page-title {
            font-size: 31px;
            font-weight: 500;
            line-height: 1.5;
            letter-spacing: -.7px;
        }

        .page-description {
            font-size: 19px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        /* =========================
           STATISTIK
        ========================= */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 9px;
            margin: 0 7px 19px 3px;
        }

        .stat-card {
            min-height: 120px;
            background: #fff;
            border-radius: 11px;
            padding: 16px 17px 12px;
        }

        .stat-icon {
            width: 24px;
            height: 24px;
            color: var(--maroon);
            margin-bottom: 3px;
        }

        .stat-icon svg {
            width: 23px;
            height: 23px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .stat-number {
            font-size: 24px;
            line-height: 1.35;
            font-weight: 500;
        }

        .stat-label {
            color: #777;
            font-size: 14px;
            margin-top: 5px;
        }

        /* =========================
           JADWAL PEMBELAJARAN
        ========================= */

        .schedule-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 25px 29px;
            margin-bottom: 27px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #3c3c3c;
            margin-bottom: 18px;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .schedule-time {
            width: 91px;
            min-width: 91px;
            height: 89px;
            border-radius: 10px;
            background: #f8edef;
            color: var(--maroon);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .schedule-day {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 3px;
        }

        .schedule-hour {
            font-size: 24px;
            font-weight: 600;
        }

        .schedule-info h3 {
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .schedule-info p {
            color: #777;
            font-size: 15px;
            line-height: 1.9;
        }

        /* =========================
           KALENDER
        ========================= */

        .calendar-card {
            width: min(596px, 100%);
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 11px;
            padding: 10px 12px 14px;
        }

        .calendar-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 9px;
        }

        .calendar-heading h2 {
            font-size: 13px;
            font-weight: 500;
        }

        .sample-label {
            font-size: 9px;
            padding: 2px 7px;
            background: #f2f2f2;
            border-radius: 10px;
            color: #444;
        }

        .calendar-toolbar {
            display: grid;
            grid-template-columns: 25px 1fr 25px;
            align-items: center;
            text-align: center;
            margin-bottom: 10px;
        }

        .calendar-toolbar strong {
            font-size: 11px;
            font-weight: 500;
        }

        .calendar-toolbar button {
            background: transparent;
            border: 0;
            color: var(--maroon);
            font-size: 21px;
            cursor: pointer;
            padding: 0 3px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 3px 7px;
        }

        .day-name {
            text-align: center;
            color: #777;
            font-size: 9px;
            padding: 4px 0 7px;
        }

        .calendar-day {
            min-height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-radius: 5px;
            color: #454545;
            font-size: 10px;
        }

        .calendar-day.muted {
            color: #c7c7c7;
        }

        .calendar-day.has-event {
            background: #f8edef;
            border: 1px solid #edd1d7;
            color: var(--maroon);
            font-weight: 600;
        }

        .calendar-day.has-event::after {
            content: "";
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #c89d45;
            position: absolute;
            bottom: 3px;
        }

        .calendar-legend {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 9px;
            color: #777;
            margin-top: 10px;
        }

        .legend-dot {
            width: 6px;
            height: 6px;
            background: #c89d45;
            border-radius: 50%;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        .mobile-toggle,
        .mobile-overlay {
            display: none;
        }

        @media (max-width: 1100px) {
            .sidebar {
                width: 260px;
            }

            .main {
                margin-left: 260px;
                padding-left: 24px;
                padding-right: 24px;
            }

            .sidebar-menu {
                gap: 10px;
            }

            .nav-pill {
                font-size: 13px;
            }

            .page-title {
                font-size: 28px;
            }

            .page-description {
                font-size: 15px;
            }

            .stats-grid {
                gap: 8px;
            }

            .stat-card {
                padding: 13px;
            }

            .stat-number {
                font-size: 21px;
            }

            .stat-label {
                font-size: 12px;
            }
        }

        @media (max-width: 700px) {
            .topbar {
                height: 72px;
                padding: 0 14px;
            }

            .brand-logo {
                width: 43px;
                height: 45px;
            }

            .brand-name {
                font-size: 22px;
            }

            .top-actions {
                gap: 12px;
            }

            .profile {
                width: 43px;
                height: 43px;
            }

            .profile svg {
                width: 41px;
                height: 41px;
            }

            .notification {
                width: 31px;
                height: 31px;
            }

            .sidebar {
                top: 72px;
                bottom: 0;
                width: min(310px, 85vw);
                transform: translateX(-100%);
                transition: transform .25s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .mobile-overlay.open {
                display: block;
                position: fixed;
                inset: 72px 0 0;
                background: rgba(0,0,0,.3);
                z-index: 40;
            }

            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border: 1px solid #e5d4d8;
                border-radius: 7px;
                padding: 6px 10px;
                background: white;
                color: var(--maroon);
                margin-bottom: 12px;
                cursor: pointer;
            }

            .main {
                margin-left: 0;
                padding: 88px 15px 30px;
            }

            .breadcrumb {
                font-size: 15px;
            }

            .page-title {
                font-size: 27px;
            }

            .page-description {
                font-size: 14px;
                margin-bottom: 17px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                margin: 0 0 18px;
                gap: 10px;
            }

            .stat-card {
                min-height: auto;
                display: grid;
                grid-template-columns: 30px 1fr;
                align-items: center;
                padding: 13px;
            }

            .stat-icon {
                grid-row: span 3;
                margin: 0;
            }

            .stat-number {
                font-size: 21px;
            }

            .stat-label {
                margin-top: 1px;
                font-size: 12px;
            }

            .schedule-card {
                padding: 15px;
            }

            .schedule-item {
                align-items: flex-start;
                gap: 12px;
            }

            .schedule-time {
                width: 75px;
                min-width: 75px;
                height: 75px;
            }

            .schedule-hour {
                font-size: 20px;
            }

            .schedule-info h3 {
                font-size: 14px;
            }

            .schedule-info p {
                font-size: 12px;
            }

            .calendar-card {
                width: 100%;
                padding: 10px;
            }

            .calendar-grid {
                gap: 3px;
            }

            .calendar-day {
                min-height: 32px;
                font-size: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <header class="topbar">
        <a href="{{ route('dashboard') }}" class="brand" aria-label="SIMAHAD - Dashboard">
            <svg class="brand-logo" viewBox="0 0 70 65" fill="none" aria-label="logo_uin.png">
                <path d="M35 3L64 25L58 53L35 63L12 53L6 25L35 3Z"
                      stroke="#B99124" stroke-width="5"/>
                <path d="M35 13L54 28L50 47L35 56L20 47L16 28L35 13Z"
                      stroke="#790C20" stroke-width="4"/>
                <path d="M24 30H46V45H24Z" fill="#790C20"/>
                <path d="M28 26L35 20L42 26V42L35 47L28 42V26Z"
                      stroke="#fff" stroke-width="2"/>
                <path d="M31 31H39M31 35H39M31 39H39"
                      stroke="#fff" stroke-width="1.5"/>
            </svg>
            <span class="brand-name">SIMAHAD</span>
        </a>

        <div class="top-actions">
            <button class="notification" type="button" aria-label="Notifikasi">
                <svg viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>
            </button>

            <div class="profile" aria-label="Profil mahasiswa">
                <svg viewBox="0 0 60 60">
                    <circle cx="30" cy="30" r="29" fill="#fff" stroke="#333"/>
                    <circle cx="30" cy="22" r="11" fill="#45484b" stroke="#222"/>
                    <path d="M9 53C10 40 19 34 30 34S50 40 51 53"
                          fill="#45484b" stroke="#222" stroke-width="1.5"/>
                </svg>
            </div>
        </div>
    </header>

    <!-- OVERLAY MOBILE -->
    <div
        class="mobile-overlay"
        id="mobileOverlay"
        onclick="toggleSidebar()"
    ></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-menu" aria-label="Navigasi utama">

            <!-- Dashboard -->
            <a
                href="{{ route('dashboard') }}"
                class="nav-pill {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                @if(request()->routeIs('dashboard')) aria-current="page" @endif
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="2"/>
                        <rect x="14" y="3" width="7" height="7" rx="2"/>
                        <rect x="3" y="14" width="7" height="7" rx="2"/>
                        <rect x="14" y="14" width="7" height="7" rx="2"/>
                    </svg>
                </span>
                <span class="nav-label">Dashboard</span>
            </a>

            <!-- Pendaftaran -->
            <a
                href="{{ route('pendaftaran.pribadi') }}"
                class="nav-pill {{ request()->routeIs('pendaftaran.*') ? 'active' : '' }}"
                @if(request()->routeIs('pendaftaran.*')) aria-current="page" @endif
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="10" cy="8" r="4"/>
                        <path d="M3 21v-2a7 7 0 0 1 14 0v2"/>
                        <path d="M19 8v6M16 11h6"/>
                    </svg>
                </span>
                <span class="nav-label">Pendaftaran</span>
            </a>

            <!-- Pembelajaran -->
            <a
                href="{{ route('pembelajaran.mhs') }}"
                class="nav-pill {{ request()->routeIs('pembelajaran.mhs') ? 'active' : '' }}"
                @if(request()->routeIs('pembelajaran.mhs')) aria-current="page" @endif
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 7L4 4v15l8 3 8-3V4l-8 3Z"/>
                        <path d="M12 7v15"/>
                    </svg>
                </span>
                <span class="nav-label">Pembelajaran</span>
                @if(request()->routeIs('pembelajaran.mhs'))
                    <span class="nav-arrow">&rsaquo;</span>
                @endif
            </a>

            <!-- Informasi Kamar -->
            <a
                href="{{ route('kamar') }}"
                class="nav-pill {{ request()->routeIs('kamar') ? 'active' : '' }}"
                @if(request()->routeIs('kamar')) aria-current="page" @endif
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="9" rx="1"/>
                        <path d="M5 11V6h5a3 3 0 0 1 3 3v2M13 11V8a2 2 0 0 1 2-2h3a3 3 0 0 1 3 3v2M3 20v2M21 20v2"/>
                    </svg>
                </span>
                <span class="nav-label">Informasi Kamar</span>
            </a>

            <!-- Pembayaran -->
            <a
                href="{{ route('pembayaran') }}"
                class="nav-pill {{ request()->routeIs('pembayaran') ? 'active' : '' }}"
                @if(request()->routeIs('pembayaran')) aria-current="page" @endif
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="15" rx="2"/>
                        <path d="M3 9h18M16 14h5"/>
                        <circle cx="16" cy="14" r="1"/>
                    </svg>
                </span>
                <span class="nav-label">Pembayaran</span>
            </a>

            <!-- Absensi -->
            <a
                href="{{ route('absensi.mhs') }}"
                class="nav-pill {{ request()->routeIs('absensi.mhs') ? 'active' : '' }}"
                @if(request()->routeIs('absensi.mhs')) aria-current="page" @endif
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="4" y="5" width="16" height="16" rx="2"/>
                        <path d="M9 5V3h6v2M8 13l3 3 5-6"/>
                    </svg>
                </span>
                <span class="nav-label">Absensi</span>
            </a>

            <!-- Pengumuman -->
            <a
                href="{{ route('pengumuman.mhs') }}"
                class="nav-pill {{ request()->routeIs('pengumuman.mhs') ? 'active' : '' }}"
                @if(request()->routeIs('pengumuman.mhs')) aria-current="page" @endif
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 11v-1l18-7v18L3 14v-3Z"/>
                        <path d="M7 16l2 5h4l-3-6"/>
                    </svg>
                </span>
                <span class="nav-label">Pengumuman</span>
            </a>

            <!-- Surat dan Perizinan -->
            <a
                href="#"
                class="nav-pill"
                onclick="event.preventDefault(); alert('Halaman Surat dan Perizinan belum tersedia.');"
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M6 3h9l5 5v13H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                        <path d="M14 3v6h6M8 13h8M8 17h6"/>
                    </svg>
                </span>
                <span class="nav-label">Surat dan Perizinan</span>
            </a>
        </nav>

        <!-- PENGATURAN DAN KELUAR -->
        <div class="sidebar-bottom">

            <a
                href="#"
                class="nav-pill"
                onclick="event.preventDefault(); alert('Halaman Pengaturan Akun belum tersedia.');"
            >
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="m19.4 15 .1.1 1.4 1.1-1.5 2.6-1.7-.7a8 8 0 0 1-1.7 1l-.3 1.9h-3l-.3-1.9a8 8 0 0 1-1.7-1l-1.7.7-1.5-2.6 1.4-1.1a7 7 0 0 1 0-2L5.5 12l1.5-2.6 1.7.7a8 8 0 0 1 1.7-1l.3-1.9h3l.3 1.9a8 8 0 0 1 1.7 1l1.7-.7 1.5 2.6-1.4 1.1a7 7 0 0 1-.1 1.9Z"/>
                    </svg>
                </span>
                <span class="nav-label">Pengaturan Akun</span>
            </a>

            <form
                action="{{ route('logout') }}"
                method="POST"
                class="logout-form"
                onsubmit="return confirm('Apakah kamu yakin ingin keluar?')"
            >
                @csrf
                <button type="submit" class="nav-pill logout-button">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 17l5-5-5-5M15 12H3"/>
                            <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
                        </svg>
                    </span>
                    <span class="nav-label">Keluar</span>
                </button>
            </form>

        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main">
        <button
            class="mobile-toggle"
            type="button"
            onclick="toggleSidebar()"
            aria-controls="sidebar"
            aria-expanded="false"
            id="sidebarToggle"
        >
            ☰ Menu
        </button>

        <div class="breadcrumb">Dashboard / Pembelajaran</div>
        <h1 class="page-title">Pembelajaran</h1>
        <p class="page-description">
            Pantau jadwal dan informasi pembelajaran Ma’had kamu.
        </p>

        <!-- STATISTIK PEMBELAJARAN -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 7 4 4v15l8 3 8-3V4l-8 3Z"/>
                        <path d="M12 7v15"/>
                    </svg>
                </div>
                <div class="stat-number">7</div>
                <div class="stat-label">Mata Pelajaran</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M2 21v-2a7 7 0 0 1 14 0v2"/>
                        <circle cx="18" cy="8" r="3"/>
                        <path d="M18 15a6 6 0 0 1 4 5"/>
                    </svg>
                </div>
                <div class="stat-number">1</div>
                <div class="stat-label">Kelompok Saya</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 10h18M8 14h2M14 14h2M8 17h2"/>
                    </svg>
                </div>
                <div class="stat-number">16</div>
                <div class="stat-label">Target Pertemuan</div>
            </div>
        </section>

        <!-- JADWAL TERDEKAT -->
        <section class="schedule-card">
            <h2 class="section-title">Jadwal Terdekat</h2>

            <div class="schedule-item">
                <div class="schedule-time">
                    <span class="schedule-day">SENIN</span>
                    <span class="schedule-hour">08.00</span>
                </div>

                <div class="schedule-info">
                    <h3>Mata Pelajaran A</h3>
                    <p>Pengajar: Nama Pengajar</p>
                    <p>Ruang: Ruang Pembelajaran 1</p>
                </div>
            </div>
        </section>

        <!-- KALENDER PEMBELAJARAN -->
        <section class="calendar-card">
            <div class="calendar-heading">
                <h2>Kalender Pembelajaran</h2>
                <span class="sample-label">Contoh</span>
            </div>

            <div class="calendar-toolbar">
                <button
                    type="button"
                    id="prevMonth"
                    aria-label="Bulan sebelumnya"
                >‹</button>

                <strong id="calendarTitle">Oktober 2026</strong>

                <button
                    type="button"
                    id="nextMonth"
                    aria-label="Bulan berikutnya"
                >›</button>
            </div>

            <div class="calendar-grid" id="calendarGrid"></div>

            <div class="calendar-legend">
                <span class="legend-dot"></span>
                Tanggal dengan jadwal pembelajaran
            </div>
        </section>
    </main>

    <!-- JAVASCRIPT -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            const toggleButton = document.getElementById('sidebarToggle');

            const isOpen = sidebar.classList.toggle('open');

            overlay.classList.toggle('open', isOpen);

            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', String(isOpen));
            }
        }

        // Menutup sidebar setelah menu dipilih pada layar mobile.
        document.querySelectorAll('#sidebar .nav-pill[href]').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 700 && link.getAttribute('href') !== '#') {
                    document.getElementById('sidebar').classList.remove('open');
                    document.getElementById('mobileOverlay').classList.remove('open');

                    const toggleButton = document.getElementById('sidebarToggle');
                    if (toggleButton) {
                        toggleButton.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });

        // Kalender pembelajaran.
        let shownYear = 2026;
        let shownMonth = 9; // Oktober, indeks bulan dimulai dari 0.

        const eventDates = {
            '2026-10': [2, 5, 9, 12, 16, 19, 23, 26, 30]
        };

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April',
            'Mei', 'Juni', 'Juli', 'Agustus',
            'September', 'Oktober', 'November', 'Desember'
        ];

        function renderCalendar() {
            const grid = document.getElementById('calendarGrid');
            const title = document.getElementById('calendarTitle');

            const days = [
                'Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'
            ];

            title.textContent = monthNames[shownMonth] + ' ' + shownYear;
            grid.innerHTML = '';

            // Nama hari.
            days.forEach(function (day) {
                const element = document.createElement('div');
                element.className = 'day-name';
                element.textContent = day;
                grid.appendChild(element);
            });

            const firstDay = new Date(
                shownYear,
                shownMonth,
                1
            ).getDay();

            const daysInMonth = new Date(
                shownYear,
                shownMonth + 1,
                0
            ).getDate();

            const daysInPrevMonth = new Date(
                shownYear,
                shownMonth,
                0
            ).getDate();

            const key = shownYear + '-' +
                String(shownMonth + 1).padStart(2, '0');

            const events = eventDates[key] || [];

            const cells = Math.ceil(
                (firstDay + daysInMonth) / 7
            ) * 7;

            for (let i = 0; i < cells; i++) {
                const element = document.createElement('div');
                element.className = 'calendar-day';

                let day;

                if (i < firstDay) {
                    day = daysInPrevMonth - firstDay + i + 1;
                    element.classList.add('muted');
                } else if (i >= firstDay + daysInMonth) {
                    day = i - firstDay - daysInMonth + 1;
                    element.classList.add('muted');
                } else {
                    day = i - firstDay + 1;

                    if (events.includes(day)) {
                        element.classList.add('has-event');
                        element.title = 'Ada jadwal pembelajaran';
                    }
                }

                element.textContent = day;
                grid.appendChild(element);
            }
        }

        document.getElementById('prevMonth').addEventListener('click', function () {
            shownMonth--;

            if (shownMonth < 0) {
                shownMonth = 11;
                shownYear--;
            }

            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', function () {
            shownMonth++;

            if (shownMonth > 11) {
                shownMonth = 0;
                shownYear++;
            }

            renderCalendar();
        });

        renderCalendar();
    </script>
</body>
</html>
