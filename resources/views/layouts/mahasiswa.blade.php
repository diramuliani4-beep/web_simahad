<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMAHAD') - {{ $namaSistem ?? 'SIMAHAD' }} UIN Imam Bonjol</title>

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
            --primary-maroon: #6B0D1B;
            --maroon-dark: #52070f;
            --gold: #C9A96A;
            --maroon-tint: #faedf0;
            --text-dark: #111827;
            --text-secondary: #4b5563;
            --text-muted: #6b7280;
            --sidebar-width: 272px;
            --header-height: 66px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #fdfbfa;
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* Top navbar */
        .top-navbar {
            position: fixed;
            inset: 0 0 auto 0;
            height: var(--header-height);
            z-index: 1100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 1.5rem;
            background: #fff;
            border-bottom: 1.5px solid var(--primary-maroon);
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .navbar-brand-wrap {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .brand-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .brand-title {
            font-size: 1.28rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: var(--primary-maroon);
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .mobile-toggle-btn {
            display: none;
            border: none;
            background: transparent;
            color: var(--primary-maroon);
            cursor: pointer;
            padding: 4px;
        }

        /* Sidebar */
        .dashboard-body {
            display: flex;
            min-height: 100vh;
            padding-top: var(--header-height);
        }

        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            overflow-y: auto;
            overflow-x: hidden;
            background: #6B0D1B;
            border-right: 1px solid rgba(201, 169, 106, 0.4);
            transition: transform 0.28s ease;
            z-index: 1000;
        }

        .sidebar-bg {
            position: absolute;
            inset: 0;
            background-image: url("{{ asset('images/sidebar_bg.jpg') }}");
            background-size: cover;
            background-position: center;
            opacity: 0.22;
        }

        .sidebar-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(107, 13, 27, 0.94), rgba(60, 6, 15, 0.96));
        }

        .sidebar-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
            padding: 1.15rem 0.9rem 1.4rem;
            gap: 1rem;
        }

        .sidebar-nav,
        .sidebar-nav-bottom {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-pill {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.62rem 0.75rem;
            border-radius: 11px;
            border: 1px solid rgba(201, 169, 106, 0.35);
            background: rgba(255, 255, 255, 0.08);
            color: #fdf7f3;
            font-size: 0.87rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.18s ease, color 0.18s ease;
        }

        .nav-pill:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        .nav-pill.active {
            background: var(--gold);
            border-color: var(--gold);
            color: #3b0810;
        }

        .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            flex-shrink: 0;
            color: inherit;
        }

        .nav-icon svg {
            stroke: currentColor;
        }

        .nav-label {
            flex: 1;
            min-width: 0;
        }

        .nav-chevron {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .btn-keluar-pill {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.62rem 0.75rem;
            border-radius: 11px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: transparent;
            color: #fff;
            font-size: 0.87rem;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-keluar-pill:hover {
            background: rgba(255, 255, 255, 0.16);
        }

        .sidebar-backdrop {
            display: none;
        }

        /* Main content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 1.8rem 2.2rem 3rem;
            min-width: 0;
            background: #fff;
            min-height: calc(100vh - var(--header-height));
        }

        .flash-success {
            margin-bottom: 1rem;
            padding: 0.8rem 1rem;
            border: 1px solid #bbf7d0;
            border-radius: 11px;
            background: #f0fdf4;
            color: #166534;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .flash-error {
            margin-bottom: 1rem;
            padding: 0.8rem 1rem;
            border: 1px solid #fecaca;
            border-radius: 11px;
            background: #fef2f2;
            color: #991b1b;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .page-heading {
            margin-bottom: 1.1rem;
        }

        .page-heading h1 {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #111827;
        }

        .page-heading p {
            margin-top: 0.35rem;
            font-size: 0.88rem;
            color: var(--text-secondary);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1.35rem 1rem 2.4rem;
            }

            .mobile-toggle-btn {
                display: inline-flex;
            }

            .sidebar-backdrop {
                position: fixed;
                inset: var(--header-height) 0 0 0;
                z-index: 900;
                background: rgba(0, 0, 0, 0.4);
            }

            .sidebar-backdrop.active {
                display: block;
            }
        }

        @media (max-width: 480px) {
            .brand-title {
                font-size: 1.05rem;
            }

            .brand-logo-img {
                width: 34px;
                height: 34px;
            }

            .top-navbar {
                padding: 0 0.8rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <header class="top-navbar">
        <div class="navbar-left">
            <button type="button" class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Buka Menu">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="navbar-brand-wrap">
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD" class="brand-logo-img">
                <span class="brand-title">{{ $namaSistem ?? 'SIMAHAD' }}</span>
            </a>
        </div>

        <div class="navbar-actions">
            <x-notifikasi-dropdown />
            <x-mahasiswa.profile-dropdown />
        </div>
    </header>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="dashboard-body">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-bg"></div>
            <div class="sidebar-overlay"></div>

            <div class="sidebar-content">
                <nav class="sidebar-nav">
                    <a href="{{ route('dashboard') }}" class="nav-pill {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                            </svg>
                        </span>
                        <span class="nav-label">Dashboard</span>
                    </a>

                    <a href="{{ route('pendaftaran.pribadi') }}" class="nav-pill {{ request()->routeIs('pendaftaran.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pendaftaran</span>
                    </a>

                    <a href="{{ route('pembelajaran.mhs') }}" class="nav-pill {{ request()->routeIs('pembelajaran.mhs*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pembelajaran</span>
                    </a>

                    <a href="{{ route('tugas.mhs.index') }}" class="nav-pill {{ request()->routeIs('tugas.mhs*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <path d="M8 8h8M8 12h8M8 16h5"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Tugas</span>
                    </a>

                    <a href="{{ route('kamar') }}" class="nav-pill {{ request()->routeIs('kamar*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 4v16"></path>
                                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                                <path d="M2 17h20"></path>
                                <path d="M6 8v9"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Informasi Kamar</span>
                    </a>

                    <a href="{{ route('pembayaran') }}" class="nav-pill {{ request()->routeIs('pembayaran*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                                <path d="M16 3H4a2 2 0 0 0-2 2v2"></path>
                                <circle cx="16" cy="14" r="1.5" fill="currentColor"></circle>
                            </svg>
                        </span>
                        <span class="nav-label">Pembayaran</span>
                    </a>

                    <a href="{{ route('absensi.mhs') }}" class="nav-pill {{ request()->routeIs('absensi.mhs*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="8" y="2" width="8" height="4" rx="1"></rect>
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <path d="m9 14 2 2 4-4"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Absensi</span>
                    </a>

                    <a href="{{ route('surat.perizinan.mhs') }}" class="nav-pill {{ request()->routeIs('surat.perizinan.mhs*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </span>
                        <span class="nav-label">Surat dan Perizinan</span>
                    </a>

                    <a href="{{ route('pengumuman.mhs') }}" class="nav-pill {{ request()->routeIs('pengumuman.mhs*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 11 18-5v12L3 14v-3z"></path>
                                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pengumuman</span>
                    </a>
                </nav>

                <div class="sidebar-nav-bottom">
                    <a href="{{ route('profil.mhs') }}" class="nav-pill {{ request()->routeIs('profil.mhs*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pengaturan Akun</span>
                    </a>

                    <a href="{{ route('logout.confirm') }}" class="btn-keluar-pill">
                        <span class="nav-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 17l5-5-5-5"></path>
                                <path d="M15 12H3"></path>
                                <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Keluar</span>
                    </a>
                </div>
            </div>
        </aside>

        <main class="main-content">
            @if (session('success'))
                <div class="flash-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash-error">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (mobileMenuBtn && sidebar && sidebarBackdrop) {
            mobileMenuBtn.addEventListener('click', function () {
                sidebar.classList.toggle('active');
                sidebarBackdrop.classList.toggle('active');
            });

            sidebarBackdrop.addEventListener('click', function () {
                sidebar.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
