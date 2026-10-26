<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Pengumuman Mahasiswa | SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary-maroon: #6e001b;
            --primary-maroon-hover: #580015;
            --maroon-tint: #faedf0;
            --maroon-border: #7c1a2e;
            --text-dark: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --border: #e5ded9;
            --sidebar-width: 280px;
            --header-height: 66px;
        }

        html {
            min-height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #fff;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        button, input {
            font: inherit;
        }

        /* ================= HEADER ================= */

        .top-navbar {
            height: var(--header-height);
            min-height: var(--header-height);
            background: #fff;
            border-bottom: 1.5px solid var(--maroon-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar-brand-wrap {
            display: flex;
            align-items: center;
            gap: .85rem;
            text-decoration: none;
        }

        .brand-logo-img {
            height: 44px;
            width: auto;
            object-fit: contain;
        }

        .brand-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--primary-maroon);
            letter-spacing: -.01em;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1.15rem;
        }

        .btn-circle-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            cursor: pointer;
            text-decoration: none;
            transition: transform .15s ease;
        }

        .btn-circle-action:hover {
            transform: scale(1.05);
        }

        .btn-bell-badge {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--primary-maroon);
            color: #fff;
        }

        .btn-bell-badge svg {
            width: 21px;
            height: 21px;
            stroke: #fff;
        }

        .btn-profile-badge {
            width: 44px;
            height: 44px;
            border: 2px solid #262626;
            border-radius: 50%;
            color: #262626;
            background: #fff;
        }

        .btn-profile-badge svg {
            width: 28px;
            height: 28px;
        }

        .mobile-toggle-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--primary-maroon);
            cursor: pointer;
            padding: .4rem;
        }

        /* ================= LAYOUT ================= */

        .dashboard-body {
            display: flex;
            flex: 1;
            min-height: calc(100vh - var(--header-height));
            width: 100%;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            position: relative;
            display: flex;
            flex-direction: column;
            border-right: 1.5px solid var(--maroon-border);
            overflow: hidden;
            background: #f1f5f9;
        }

        .sidebar-bg {
            position: absolute;
            inset: 0;
            z-index: 1;
            background-image: url('{{ asset("images/mahad_al_jamiah.jpeg") }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
        }

        .sidebar-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: linear-gradient(
                180deg,
                rgba(219, 234, 254, .43) 0%,
                rgba(241, 245, 249, .55) 35%,
                rgba(255, 255, 255, .80) 70%,
                rgba(255, 255, 255, .93) 100%
            );
        }

        .sidebar-watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            pointer-events: none;
            text-align: center;
            opacity: .28;
            width: 100%;
            padding: 0 10px;
        }

        .watermark-emblem {
            display: block;
            margin: 0 auto 4px;
        }

        .watermark-text-2 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #334155;
            line-height: 1.2;
        }

        .sidebar-content {
            position: relative;
            z-index: 4;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: calc(100vh - var(--header-height));
            justify-content: space-between;
            padding: 1.25rem 1.15rem 1.5rem;
        }

        .sidebar-nav,
        .sidebar-nav-bottom {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .sidebar-nav {
            flex-grow: 1;
        }

        .sidebar-nav-bottom {
            margin-top: 1.5rem;
        }

        .nav-pill,
        .btn-keluar-pill {
            display: flex;
            align-items: center;
            gap: .85rem;
            min-height: 46px;
            padding: 0 1rem;
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 12px;
            text-decoration: none;
            color: #1a1a1a;
            font-size: .92rem;
            font-weight: 700;
            transition: all .2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .03);
            cursor: pointer;
            width: 100%;
        }

        .nav-pill:hover,
        .btn-keluar-pill:hover {
            transform: translateX(3px);
            background: #fff8f9;
        }

        .nav-pill.active {
            background: var(--maroon-tint);
            border-color: var(--primary-maroon);
            color: var(--primary-maroon);
        }

        .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }

        .nav-icon svg {
            display: block;
            width: 21px;
            height: 21px;
            stroke: #667085;
        }

        .nav-pill.active .nav-icon svg {
            stroke: var(--primary-maroon);
        }

        .nav-label {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-chevron {
            margin-left: auto;
            color: var(--primary-maroon);
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1;
        }

        .btn-keluar-pill {
            text-align: left;
            font-family: inherit;
        }

        .btn-keluar-pill:hover {
            color: var(--primary-maroon);
        }

        /* ================= KONTEN PENGUMUMAN ================= */

        .main-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .page-heading {
            padding: 1.9rem 2.75rem 1rem;
            border-bottom: 1.5px solid var(--maroon-border);
            background: #fff;
        }

        .breadcrumb {
            color: var(--primary-maroon);
            font-size: 1.15rem;
            font-weight: 500;
            margin-bottom: .75rem;
        }

        .page-heading h1 {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: .35rem;
            color: #111;
        }

        .subtitle {
            font-size: 1.05rem;
            color: #333;
            line-height: 1.5;
        }

        .content {
            padding: 1.25rem 2.75rem 2.5rem;
        }

        /* ================= FILTER KATEGORI ================= */

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .filter-btn {
            border: 1px solid var(--border);
            background: #fff;
            color: #555;
            border-radius: 30px;
            padding: .7rem 1rem;
            font-size: .95rem;
            cursor: pointer;
            transition: all .2s ease;
        }

        .filter-btn:hover {
            border-color: var(--primary-maroon);
            color: var(--primary-maroon);
        }

        .filter-btn.active {
            background: var(--primary-maroon);
            color: #fff;
            border-color: var(--primary-maroon);
        }

        /* ================= ITEM PENGUMUMAN ================= */

        .announcement {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border);
        }

        .announcement[hidden] {
            display: none !important;
        }

        .announcement-icon {
            width: 61px;
            height: 62px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: #f8ecef;
            color: var(--primary-maroon);
            border-radius: 12px;
        }

        .announcement-icon svg {
            width: 25px;
            height: 25px;
        }

        .announcement-body {
            flex: 1;
            min-width: 0;
        }

        .tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        .tag-purple {
            color: #8250c8;
            background: #eee1fa;
        }

        .tag-orange {
            color: #e66b1d;
            background: #ffeadb;
        }

        .tag-blue {
            color: #3185f6;
            background: #dcecff;
        }

        .tag-green {
            color: #21865b;
            background: #dff5e8;
        }

        .tag-pink {
            color: #be185d;
            background: #fce7f3;
        }

        .announcement h2 {
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.45;
            margin: 0 0 .45rem;
            color: #171717;
        }

        .description {
            font-size: .95rem;
            line-height: 1.6;
            color: #77716d;
            margin: 0 0 .4rem;
        }

        .date {
            font-size: .85rem;
            color: #88817d;
            margin-bottom: .55rem;
        }

        .read-more {
            color: var(--primary-maroon);
            font-size: .95rem;
            font-weight: 700;
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        .empty {
            display: none;
            padding: 2rem 0;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ================= MODAL ================= */

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(2px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            max-width: 520px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            padding: 1.85rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, .15);
            position: relative;
            border: 1.5px solid var(--maroon-border);
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--primary-maroon);
            margin: 0 2rem .75rem 0;
        }

        .modal-body {
            font-size: .95rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            overflow-wrap: anywhere;
        }

        .btn-modal-x {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: transparent;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: #94a3b8;
        }

        .btn-modal-action {
            width: 100%;
            background: var(--primary-maroon);
            color: #fff;
            padding: .8rem;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-modal-action:hover {
            background: var(--primary-maroon-hover);
        }

        /* ================= RESPONSIVE ================= */

        .sidebar-backdrop {
            display: none;
        }

        @media (max-width: 992px) {
            .mobile-toggle-btn {
                display: block;
            }

            .sidebar {
                position: fixed;
                top: var(--header-height);
                bottom: 0;
                left: calc(-1 * var(--sidebar-width) - 10px);
                z-index: 46;
                transition: left .3s ease;
                overflow-y: auto;
                box-shadow: 4px 0 15px rgba(0, 0, 0, .1);
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-content {
                min-height: calc(100vh - var(--header-height));
            }

            .sidebar-backdrop.active {
                display: block;
                position: fixed;
                inset: var(--header-height) 0 0;
                background: rgba(0, 0, 0, .4);
                z-index: 45;
            }

            .main-content {
                width: 100%;
            }

            .page-heading {
                padding: 1.5rem 1.5rem 1rem;
            }

            .content {
                padding: 1.25rem 1.5rem 2rem;
            }
        }

        @media (max-width: 600px) {
            .top-navbar {
                padding: 0 1rem;
            }

            .brand-title {
                font-size: 1.2rem;
            }

            .brand-logo-img {
                height: 36px;
            }

            .navbar-actions {
                gap: .65rem;
            }

            .btn-profile-badge {
                width: 38px;
                height: 38px;
            }

            .page-heading {
                padding: 1.25rem 1rem 1rem;
            }

            .breadcrumb {
                font-size: .95rem;
            }

            .page-heading h1 {
                font-size: 1.65rem;
            }

            .subtitle {
                font-size: .9rem;
            }

            .content {
                padding: 1rem;
            }

            .filters {
                gap: .5rem;
            }

            .filter-btn {
                padding: .6rem .8rem;
                font-size: .85rem;
            }

            .announcement {
                gap: .75rem;
                padding: 1.15rem 0;
            }

            .announcement-icon {
                width: 45px;
                height: 45px;
            }

            .announcement-icon svg {
                width: 21px;
                height: 21px;
            }

            .announcement h2 {
                font-size: .95rem;
            }

            .description {
                font-size: .85rem;
            }

            .date,
            .read-more {
                font-size: .8rem;
            }

            .modal-box {
                padding: 1.4rem;
            }
        }
    </style>
</head>

<body>

    <!-- ================= HEADER ================= -->

    <header class="top-navbar">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <button
                type="button"
                class="mobile-toggle-btn"
                id="mobileMenuBtn"
                aria-label="Buka menu"
            >
                <svg width="26" height="26" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="navbar-brand-wrap">
                <img
                    src="{{ asset('images/logo_uin.png') }}"
                    alt="Logo SIMAHAD"
                    class="brand-logo-img"
                    onerror="this.style.display='none'"
                >
                <span class="brand-title">SIMAHAD</span>
            </a>
        </div>

        <div class="navbar-actions">
            <button
                type="button"
                class="btn-circle-action btn-bell-badge"
                title="Pemberitahuan"
                aria-label="Pemberitahuan"
                onclick="openInfoModal('Pemberitahuan', 'Belum ada pengumuman mendesak. Pantau selalu informasi kegiatan asrama di sini.')"
            >
                <svg viewBox="0 0 24 24" fill="none"
                     stroke-width="2.2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </button>

            <button
                type="button"
                class="btn-circle-action btn-profile-badge"
                title="Profil Pengguna"
                aria-label="Profil Pengguna"
                onclick="openInfoModal('Profil Pengguna', 'Nama: {{ Auth::user()->name ?? 'Mahasiswa' }}<br>Email: {{ Auth::user()->email ?? '-' }}')"
            >
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </button>
        </div>
    </header>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ================= LAYOUT ================= -->

    <div class="dashboard-body">

        <!-- ================= SIDEBAR ================= -->

        <aside class="sidebar" id="sidebar">

            <div class="sidebar-bg"></div>
            <div class="sidebar-overlay"></div>

            <div class="sidebar-watermark">
                <svg class="watermark-emblem"
                     width="60" height="60"
                     viewBox="0 0 100 100" fill="none">
                    <polygon points="50,4 92,25 92,75 50,96 8,75 8,25"
                             stroke="#a16207" stroke-width="3"/>
                    <polygon points="50,14 82,30 82,70 50,86 18,70 18,30"
                             stroke="#64748b" stroke-width="2.5"/>
                    <path d="M50 20 L50 80 M25 50 L75 50"
                          stroke="#64748b" stroke-width="2"/>
                </svg>
                <div class="watermark-text-2">UIN Imam Bonjol</div>
            </div>

            <div class="sidebar-content">

                <nav class="sidebar-nav">

                    <a href="{{ route('dashboard') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                            </svg>
                        </span>
                        <span class="nav-label">Dashboard</span>
                    </a>

                    <a href="{{ route('pendaftaran.pribadi') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L9 17l-4 1 1-4Z"/>
                                <path d="M4 21h16"/>
                            </svg>
                        </span>
                        <span class="nav-label">Pendaftaran</span>
                    </a>

                    <a href="{{ route('pembelajaran.mhs') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </span>
                        <span class="nav-label">Pembelajaran</span>
                    </a>

                    <a href="{{ route('kamar') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M2 4v16"/>
                                <path d="M2 8h18a2 2 0 0 1 2 2v10"/>
                                <path d="M2 17h20"/>
                                <path d="M6 8v9"/>
                            </svg>
                        </span>
                        <span class="nav-label">Informasi Kamar</span>
                    </a>

                    <a href="{{ route('pembayaran') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="15" rx="2"/>
                                <path d="M2 10h20"/>
                                <path d="M16 15h2"/>
                                <path d="M6 5V3h12v2"/>
                            </svg>
                        </span>
                        <span class="nav-label">Pembayaran</span>
                    </a>

                    <a href="{{ route('absensi.mhs') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18"/>
                                <path d="m8 15 2 2 5-5"/>
                            </svg>
                        </span>
                        <span class="nav-label">Absensi</span>
                    </a>

                    
<!-- Surat dan Perizinan -->
<a
    href="{{ route('surat.perizinan.mhs') }}"
    class="nav-pill {{ request()->routeIs('surat.perizinan.mhs*') ? 'active' : '' }}"
    @if(request()->routeIs('surat.perizinan.mhs*')) aria-current="page" @endif
>
    <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none"
             stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
            <path d="M8 13h8M8 17h8"/>
        </svg>
    </span>
    <span class="nav-label">Surat &amp; Perizinan</span>
</a>


                    <a href="{{ route('pengumuman.mhs') }}"
                       class="nav-pill {{ request()->routeIs('pengumuman.mhs') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="m3 11 18-5v12L3 14v-3z"/>
                                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                            </svg>
                        </span>
                        <span class="nav-label">Pengumuman</span>
                        <span class="nav-chevron">&gt;</span>
                    </a>

                </nav>

                <div class="sidebar-nav-bottom">

                    <a href="javascript:void(0)" class="nav-pill"
                       onclick="openInfoModal('Pengaturan Akun', 'Fitur Pengaturan Akun belum terhubung ke halaman tersendiri.')">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33h-.09A1.65 1.65 0 0 0 13.5 21v.1a2 2 0 1 1-4 0V21a1.65 1.65 0 0 0-1.1-1.56 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.1 15v-.1A1.65 1.65 0 0 0 2.5 13.5H2.4a2 2 0 1 1 0-4h.1A1.65 1.65 0 0 0 4.1 8v-.1a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8.4 3.6h.1A1.65 1.65 0 0 0 10 2h.1a2 2 0 1 1 4 0v.1A1.65 1.65 0 0 0 15.6 3.6h.1a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 20 7.9v.1a1.65 1.65 0 0 0 1.6 1.5h.1a2 2 0 1 1 0 4h-.1a1.65 1.65 0 0 0-1.6 1.5z"/>
                            </svg>
                        </span>
                        <span class="nav-label">Pengaturan Akun</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-keluar-pill">
                            <span class="nav-icon">
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                            </span>
                            <span class="nav-label">Keluar</span>
                        </button>
                    </form>

                </div>
            </div>
        </aside>

        <!-- ================= KONTEN UTAMA ================= -->

        <main class="main-content">

            <section class="page-heading">
                <div class="breadcrumb">Dashboard / Pengumuman</div>
                <h1>Pengumuman</h1>
                <p class="subtitle">
                    Informasi terbaru seputar kegiatan dan aktivitas Ma’had.
                </p>
            </section>

            <section class="content">

                <div class="filters">
                    <button type="button" class="filter-btn active" data-filter="Semua">
                        Semua
                    </button>

                    <button type="button" class="filter-btn" data-filter="Pengumuman">
                        Pengumuman
                    </button>

                    <button type="button" class="filter-btn" data-filter="Kegiatan">
                        Kegiatan
                    </button>

                    <button type="button" class="filter-btn" data-filter="Pembelajaran">
                        Pembelajaran
                    </button>
                </div>

                <div id="announcement-list">

                    @foreach ($pengumuman as $item)
                        <article class="announcement"
                                 data-category="{{ $item['kategori'] }}">

                            <div class="announcement-icon">
                                @if ($item['ikon'] === 'calendar-days')
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <path d="M16 2v4M8 2v4M3 10h18"/>
                                        <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/>
                                    </svg>
                                @elseif ($item['ikon'] === 'megaphone')
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m3 11 18-5v12L3 14v-3z"/>
                                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                    </svg>
                                @elseif ($item['ikon'] === 'heart-handshake')
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m11 17 2 2a2 2 0 0 0 3-3"/>
                                        <path d="m14 14 4 4a2 2 0 0 0 3-3l-7-7-4 1"/>
                                        <path d="m3 12 5-5 4 1 2-2 5 1 2 2"/>
                                        <path d="m3 12 5 5a2 2 0 0 0 3-3l-2-2"/>
                                        <path d="M2 11V7a2 2 0 0 1 2-2h3"/>
                                        <path d="M22 11V7a2 2 0 0 0-2-2h-2"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="announcement-body">

                                @php
                                    $tagClass = match ($item['warna']) {
                                        'purple' => 'tag-purple',
                                        'orange' => 'tag-orange',
                                        'blue' => 'tag-blue',
                                        'green' => 'tag-green',
                                        'pink' => 'tag-pink',
                                        default => 'tag-purple',
                                    };
                                @endphp

                                <span class="tag {{ $tagClass }}">
                                    {{ $item['status'] }}
                                </span>

                                <h2>{{ $item['judul'] }}</h2>

                                <p class="description">
                                    {{ $item['deskripsi'] }}
                                </p>

                                <div class="date">
                                    Tanggal publikasi: {{ $item['tanggal'] }}
                                </div>

                                <button
                                    type="button"
                                    class="read-more"
                                    data-title="{{ $item['judul'] }}"
                                    data-description="{{ $item['deskripsi'] }}"
                                    data-date="{{ $item['tanggal'] }}"
                                    onclick="showAnnouncementDetail(this)"
                                >
                                    Baca selengkapnya →
                                </button>

                            </div>
                        </article>
                    @endforeach

                </div>

                <div class="empty" id="empty-message">
                    Tidak ada pengumuman pada kategori ini.
                </div>

            </section>
        </main>
    </div>

    <!-- ================= MODAL INFORMASI ================= -->

    <div class="modal-overlay" id="infoModal" role="dialog"
         aria-modal="true" aria-labelledby="modalTitle"
         onclick="closeModalOnBackdrop(event)">

        <div class="modal-box">
            <button type="button" class="btn-modal-x"
                    aria-label="Tutup" onclick="closeInfoModal()">
                &times;
            </button>

            <h2 class="modal-title" id="modalTitle">Informasi</h2>
            <div class="modal-body" id="modalBody"></div>

            <button type="button" class="btn-modal-action"
                    onclick="closeInfoModal()">
                Tutup
            </button>
        </div>
    </div>

    <!-- ================= JAVASCRIPT ================= -->

    <script>
        // Filter kategori pengumuman
        const filterButtons = document.querySelectorAll('.filter-btn');
        const announcements = document.querySelectorAll('.announcement');
        const emptyMessage = document.getElementById('empty-message');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(item => item.classList.remove('active'));
                button.classList.add('active');

                const category = button.dataset.filter;
                let visibleCount = 0;

                announcements.forEach(item => {
                    const show = category === 'Semua'
                        || item.dataset.category === category;

                    item.hidden = !show;

                    if (show) visibleCount++;
                });

                emptyMessage.style.display = visibleCount === 0
                    ? 'block'
                    : 'none';
            });
        });

        // Buka dan tutup sidebar pada layar kecil
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarBackdrop.classList.toggle('active');
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
        }

        mobileMenuBtn.addEventListener('click', toggleSidebar);
        sidebarBackdrop.addEventListener('click', closeSidebar);

        sidebar.querySelectorAll('a.nav-pill').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 992) closeSidebar();
            });
        });

        // Modal informasi
        function openInfoModal(title, message) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').innerHTML = message;
            document.getElementById('infoModal').classList.add('active');
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.remove('active');
        }

        function closeModalOnBackdrop(event) {
            if (event.target.id === 'infoModal') {
                closeInfoModal();
            }
        }

        // Detail pengumuman menggunakan modal karena route detail
        // belum tersedia pada daftar route Laravel saat ini.
        function showAnnouncementDetail(button) {
            const title = button.dataset.title;
            const description = button.dataset.description;
            const date = button.dataset.date;

            document.getElementById('modalTitle').textContent = title;

            const body = document.getElementById('modalBody');
            body.textContent = '';

            const paragraph = document.createElement('p');
            paragraph.textContent = description;

            const dateText = document.createElement('p');
            dateText.style.marginTop = '12px';
            dateText.style.color = '#77716d';
            dateText.textContent = 'Tanggal publikasi: ' + date;

            body.appendChild(paragraph);
            body.appendChild(dateText);

            document.getElementById('infoModal').classList.add('active');
        }

        // Tutup modal dengan tombol Escape
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closeInfoModal();
                closeSidebar();
            }
        });
    </script>

</body>
</html>