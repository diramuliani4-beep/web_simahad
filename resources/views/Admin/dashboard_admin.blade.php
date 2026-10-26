```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin | SIMAHAD</title>

    <style>
        :root {
            --maroon: #780d22;
            --maroon-dark: #5e091a;
            --maroon-light: #f8edf0;
            --background: #f7f7f8;
            --white: #ffffff;
            --text: #292929;
            --muted: #777777;
            --border: #eadfe1;
            --green: #238653;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: var(--background);
            color: var(--text);
            font-size: 13px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        /* HEADER */

        .topbar {
            height: 54px;
            background: var(--white);
            border: 1px solid #d9a5ae;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 17px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--maroon);
            font-size: 17px;
            font-weight: 800;
        }

        .brand img {
            width: 30px;
            height: 32px;
            object-fit: contain;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification {
            position: relative;
            border: none;
            background: transparent;
            color: var(--maroon);
            font-size: 21px;
            cursor: pointer;
        }

        .notification-count {
            position: absolute;
            top: -3px;
            right: -5px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d94a55;
        }

        .profile {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f4f4f4;
            border: 1px solid #bcbcbc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* LAYOUT */

        .layout {
            display: flex;
            min-height: 100vh;
            padding-top: 54px;
        }

        /* SIDEBAR */

        .sidebar {
            width: 205px;
            flex-shrink: 0;
            min-height: calc(100vh - 54px);
            padding: 17px 10px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #d8b4bb;

            background-color: #e5eff9;
            background-image:
                linear-gradient(
                    rgba(235, 243, 252, .50),
                    rgba(255, 255, 255, .65)
                ),
                url("{{ asset('images/kampus.jpg') }}");
            background-size: cover;
            background-position: center;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 36px;
            padding: 9px 10px;
            background: rgba(255, 255, 255, .94);
            border: 1px solid #d6a1aa;
            border-radius: 9px;
            color: #303030;
            font-size: 11px;
            transition: .2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--maroon);
            background: #fff0f2;
            border-color: var(--maroon);
        }

        .nav-icon {
            width: 16px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 25px;
        }

        .logout-form {
            margin-top: 10px;
        }

        .logout-button {
            width: 100%;
            cursor: pointer;
            text-align: left;
        }

        /* KONTEN UTAMA */

        .main {
            flex: 1;
            min-width: 0;
            padding: 17px 14px 24px;
        }

        .breadcrumb {
            color: var(--maroon);
            font-size: 12px;
            margin-bottom: 17px;
        }

        .page-heading {
            margin-bottom: 17px;
        }

        .page-heading h1 {
            font-size: 23px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-heading p {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.6;
        }

        /* KARTU */

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 17px;
            margin-bottom: 15px;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .card-title span {
            color: var(--maroon);
            font-size: 16px;
        }

        .welcome-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 18px;
            margin-bottom: 15px;
        }

        .welcome-card h2 {
            font-size: 16px;
            color: var(--maroon);
            margin-bottom: 8px;
        }

        .welcome-card p {
            color: var(--muted);
            line-height: 1.7;
        }

        /* STATISTIK */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 13px;
            margin-bottom: 15px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 16px;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            color: var(--muted);
        }

        .stat-icon {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: var(--maroon-light);
            color: var(--maroon);
            font-size: 18px;
        }

        .stat-number {
            font-size: 26px;
            font-weight: bold;
            color: var(--maroon);
            margin-top: 13px;
        }

        .stat-description {
            color: var(--muted);
            font-size: 11px;
            margin-top: 5px;
        }

        /* INFORMASI DAN AKSES CEPAT */

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(220px, 1fr);
            gap: 15px;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .activity-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--maroon);
            margin-top: 4px;
            flex-shrink: 0;
        }

        .activity-item p {
            line-height: 1.6;
        }

        .activity-item small {
            display: block;
            color: var(--muted);
            line-height: 1.6;
            margin-top: 3px;
        }

        .quick-links {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .quick-link {
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            transition: .2s;
        }

        .quick-link:hover {
            background: var(--maroon-light);
            border-color: var(--maroon);
        }

        .quick-link strong {
            display: block;
            margin-bottom: 5px;
        }

        .quick-link small {
            color: var(--muted);
            line-height: 1.5;
        }

        .quick-arrow {
            color: var(--maroon);
            font-size: 20px;
        }

        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            color: var(--green);
            background: #e4f5e9;
            font-size: 10px;
        }

        .footer {
            text-align: center;
            color: var(--muted);
            font-size: 11px;
            padding: 15px 5px 0;
        }

        /* RESPONSIVE */

        @media (max-width: 850px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .sidebar {
                width: 62px;
                padding: 14px 6px;
            }

            .nav-link {
                justify-content: center;
                padding: 10px 4px;
            }

            .nav-label {
                display: none;
            }

            .main {
                padding: 15px 9px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-heading h1 {
                font-size: 20px;
            }

            .brand {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <header class="topbar">
        <a href="{{ route('dashboard') }}" class="brand">
            <img
                src="{{ asset('images/logo-simahad.png') }}"
                alt="Logo SIMAHAD"
                onerror="this.style.display='none'"
            >
            <span>SIMAHAD</span>
        </a>

        <div class="top-actions">
            <button
                type="button"
                class="notification"
                title="Notifikasi"
                onclick="alert('Belum ada notifikasi baru.')"
            >
                ♧
                <span class="notification-count"></span>
            </button>

            <div class="profile" title="Profil pengguna">👤</div>
        </div>
    </header>

    <div class="layout">

        {{-- SIDEBAR --}}
        <aside class="sidebar">

            <nav class="nav-menu">
                <a href="{{ route('dashboard') }}" class="nav-link active">
                    <span class="nav-icon">▦</span>
                    <span class="nav-label">Dashboard</span>
                </a>

                <a href="{{ url('/admin/pengguna') }}" class="nav-link">
                    <span class="nav-icon">♟</span>
                    <span class="nav-label">Manajemen Pengguna</span>
                </a>

                <a href="{{ url('/admin/hak-akses') }}" class="nav-link">
                    <span class="nav-icon">▣</span>
                    <span class="nav-label">Hak Akses</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <nav class="nav-menu">
                    <a href="{{ url('/admin/pengaturan') }}" class="nav-link">
                        <span class="nav-icon">⚙</span>
                        <span class="nav-label">Pengaturan Sistem</span>
                    </a>

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        class="logout-form"
                    >
                        @csrf

                        <button type="submit" class="nav-link logout-button">
                            <span class="nav-icon">⇥</span>
                            <span class="nav-label">Keluar</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        {{-- KONTEN DASHBOARD --}}
        <main class="main">

            <div class="breadcrumb">
                Dashboard / Admin
            </div>

            <section class="page-heading">
                <h1>Dashboard Admin</h1>
                <p>
                    Selamat datang di Sistem Informasi Ma'had Al-Jami'ah
                    (SIMAHAD).
                </p>
            </section>

            <section class="welcome-card">
                <h2>Selamat Datang di SIMAHAD</h2>
                <p>
                    Kelola informasi pengguna dan aktivitas sistem Ma'had
                    Al-Jami'ah melalui halaman dashboard admin ini.
                </p>
            </section>

            {{-- KARTU STATISTIK --}}
            <section class="stats-grid">

                <article class="stat-card">
                    <div class="stat-header">
                        <span>Manajemen Pengguna</span>
                        <span class="stat-icon">♟</span>
                    </div>

                    <div class="stat-number">—</div>
                    <p class="stat-description">Data pengguna sistem</p>
                </article>

                <article class="stat-card">
                    <div class="stat-header">
                        <span>Data Kamar</span>
                        <span class="stat-icon">⌂</span>
                    </div>

                    <div class="stat-number">—</div>
                    <p class="stat-description">Informasi kamar ma'had</p>
                </article>

                <article class="stat-card">
                    <div class="stat-header">
                        <span>Pendaftaran</span>
                        <span class="stat-icon">▤</span>
                    </div>

                    <div class="stat-number">—</div>
                    <p class="stat-description">Data pendaftaran mahasiswa</p>
                </article>

            </section>

            {{-- INFORMASI SISTEM --}}
            <section class="content-grid">

                <div class="card">
                    <h2 class="card-title">
                        <span>▣</span>
                        Informasi Sistem
                    </h2>

                    <div class="activity-item">
                        <span class="activity-dot"></span>
                        <div>
                            <p><strong>Informasi aplikasi</strong></p>
                            <small>
                                Sistem Informasi Ma'had Al-Jami'ah UIN Imam Bonjol.
                            </small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-dot"></span>
                        <div>
                            <p><strong>Manajemen pengguna</strong></p>
                            <small>
                                Pengelolaan akun dan informasi pengguna sistem.
                            </small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <span class="activity-dot"></span>
                        <div>
                            <p><strong>Keamanan sistem</strong></p>
                            <small>
                                Akses halaman dibatasi melalui proses login.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- AKSES CEPAT --}}
                <div class="card">
                    <h2 class="card-title">
                        <span>↗</span>
                        Akses Cepat
                    </h2>

                    <div class="quick-links">

                        <a href="{{ url('/admin/pengguna') }}" class="quick-link">
                            <div>
                                <strong>Manajemen Pengguna</strong>
                                <small>Kelola akun pengguna</small>
                            </div>
                            <span class="quick-arrow">›</span>
                        </a>

                        <a href="{{ url('/admin/hak-akses') }}" class="quick-link">
                            <div>
                                <strong>Hak Akses</strong>
                                <small>Pengaturan akses pengguna</small>
                            </div>
                            <span class="quick-arrow">›</span>
                        </a>

                        <a href="{{ url('/admin/pengaturan') }}" class="quick-link">
                            <div>
                                <strong>Pengaturan Sistem</strong>
                                <small>Konfigurasi aplikasi</small>
                            </div>
                            <span class="quick-arrow">›</span>
                        </a>

                    </div>
                </div>

            </section>

            <footer class="footer">
                SIMAHAD &copy; {{ date('Y') }} · Sistem Informasi Ma'had Al-Jami'ah
            </footer>

        </main>
    </div>

</body>
</html>
```