
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Kamar - SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f8f8f8;
            color: #171717;
        }

        :root {
            --maroon: #790c20;
            --border: #8a1428;
            --soft-pink: #f8edef;
            --muted: #777;
        }

        /* HEADER */
        .topbar {
            height: 89px;
            background: #fff;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px 0 20px;
            position: relative;
            z-index: 2;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--maroon);
            font-size: 32px;
            font-weight: 700;
        }

        .brand img {
            width: 65px;
            height: 60px;
            object-fit: contain;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .notification {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--maroon);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .notification svg {
            width: 26px;
            height: 26px;
            fill: none;
            stroke: white;
            stroke-width: 2;
        }

        .avatar {
            width: 61px;
            height: 61px;
            border: 2px solid #333;
            border-radius: 50%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .avatar svg {
            width: 46px;
            height: 46px;
            fill: #42464a;
            stroke: #222;
            stroke-width: 1;
        }

        /* LAYOUT */
        .layout {
            display: flex;
            min-height: calc(100vh - 89px);
        }

        /* SIDEBAR */
        .sidebar {
            width: 407px;
            flex: 0 0 407px;
            min-height: calc(100vh - 89px);
            padding: 41px 14px 30px;
            border: 1px solid var(--border);
            border-top: 0;
            background:
                linear-gradient(rgba(255,255,255,.36), rgba(255,255,255,.57)),
                url("{{ asset('images/mahad_al_jamiah.jpeg') }}")
                center center / cover no-repeat;
            display: flex;
            flex-direction: column;
            gap: 36px;
        }

        .nav-item {
            min-height: 50px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 15px;
            text-decoration: none;
            color: #111;
            font-size: 19px;
            transition: background .2s ease;
        }

        .nav-item:hover {
            background: #f8edef;
        }

        .nav-item.active {
            background: #f8edef;
            color: var(--maroon);
            box-shadow: 0 2px 4px #00000020;
        }

        .nav-icon {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            color: #606b7e;
        }

        .nav-icon svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .nav-label {
            flex: 1;
        }

        .nav-arrow {
            font-size: 25px;
            font-weight: 700;
            color: var(--maroon);
        }

        .logout {
            margin-top: auto;
            font-weight: 600;
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            min-width: 0;
            padding: 15px 45px 30px;
        }

        .breadcrumb {
            color: var(--maroon);
            font-size: 20px;
            margin: 0;
        }

        .page-title {
            font-size: 31px;
            font-weight: 500;
            line-height: 1.5;
            margin: 0;
        }

        .page-subtitle {
            font-size: 19px;
            margin: 0 0 9px;
        }

        /* ROOM CARD */
        .room-card {
            padding: 20px 30px 23px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 22px;
        }

        .room-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 17px;
        }

        .room-heading h2 {
            font-size: 21px;
            font-weight: 500;
            color: #363636;
            margin: 0;
        }

        .status {
            background: #d8f5e3;
            color: #008b42;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 13px;
            white-space: nowrap;
        }

        .room-highlight {
            background: var(--soft-pink);
            color: var(--maroon);
            border-radius: 11px;
            padding: 20px;
            margin-bottom: 14px;
        }

        .room-highlight svg {
            width: 28px;
            height: 28px;
            display: block;
            margin-bottom: 9px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .room-label {
            font-size: 16px;
            margin-bottom: 7px;
        }

        .room-number {
            font-size: 29px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .muted {
            color: var(--muted);
            font-size: 15px;
        }

        .room-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
        }

        .room-info {
            border: 1px solid #e6dcd6;
            border-radius: 8px;
            padding: 15px;
        }

        .room-info svg {
            width: 22px;
            height: 22px;
            display: block;
            color: var(--maroon);
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            margin-bottom: 4px;
        }

        .info-label {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 500;
        }

        /* DETAIL */
        .detail-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 11px;
            padding: 12px 15px 17px;
            margin-bottom: 23px;
        }

        .detail-card h2 {
            font-size: 14px;
            font-weight: 500;
            margin: 0 0 9px;
        }

        .detail-row {
            max-width: 580px;
            border-bottom: 1px solid #e8e1dd;
            padding: 10px 0 15px;
            margin-bottom: 3px;
        }

        .detail-label {
            color: #888;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 12px;
            font-weight: 500;
        }

        /* ADDITIONAL INFORMATION */
        .additional-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 11px;
            padding: 16px;
        }

        .additional-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 9px;
        }

        .additional-heading svg {
            width: 18px;
            height: 18px;
            color: var(--maroon);
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }

        .additional-card p {
            color: var(--muted);
            font-size: 11px;
            margin: 0;
        }

        @media (max-width: 1050px) {
            .sidebar {
                width: 270px;
                flex-basis: 270px;
                gap: 24px;
            }

            .main-content {
                padding: 20px;
            }

            .brand {
                font-size: 26px;
            }
        }

        @media (max-width: 700px) {
            .topbar {
                height: 70px;
                padding: 0 12px;
            }

            .brand {
                font-size: 22px;
                gap: 6px;
            }

            .brand img {
                width: 42px;
                height: 42px;
            }

            .avatar {
                width: 42px;
                height: 42px;
            }

            .avatar svg {
                width: 32px;
                height: 32px;
            }

            .header-actions {
                gap: 12px;
            }

            .layout {
                display: block;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                padding: 12px;
                background-position: center;
                gap: 10px;
            }

            .nav-item {
                font-size: 15px;
            }

            .logout {
                margin-top: 0;
            }

            .main-content {
                padding: 18px 14px;
            }

            .breadcrumb {
                font-size: 15px;
            }

            .page-title {
                font-size: 26px;
            }

            .page-subtitle {
                font-size: 14px;
                line-height: 1.6;
            }

            .room-card {
                padding: 15px;
            }

            .room-heading h2 {
                font-size: 18px;
            }

            .room-grid {
                grid-template-columns: 1fr;
            }

            .muted {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="brand">
            <!-- Sesuaikan nama file logo dengan logo di project kamu -->
            <img src="{{ asset('images/logo_uin.png') }}"
                 alt="Logo SIMAHAD"
                 onerror="this.style.display='none'">
            <span>SIMAHAD</span>
        </div>

        <div class="header-actions">
            <div class="notification" title="Notifikasi">
                <svg viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>
            </div>

            <div class="avatar" aria-label="Profil pengguna">
                <svg viewBox="0 0 48 48">
                    <circle cx="24" cy="16" r="10"/>
                    <path d="M7 43c0-10 7-16 17-16s17 6 17 16z"/>
                </svg>
            </div>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <a href="{{ route('dashboard') }}" class="nav-item">
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

            <a href="{{ route('pembelajaran.mhs') }}" class="nav-item">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 7v14"/>
                        <path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H12v18H5.5A2.5 2.5 0 0 0 3 23z"/>
                        <path d="M21 5.5A2.5 2.5 0 0 0 18.5 3H12v18h6.5a2.5 2.5 0 0 1 2.5 2z"/>
                    </svg>
                </span>
                <span class="nav-label">Pembelajaran</span>
            </a>

            <a href="{{ route('kamar') }}" class="nav-item active">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 18V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v11"/>
                        <path d="M12 11h7a2 2 0 0 1 2 2v5"/>
                        <path d="M3 14h18M3 18v2M21 18v2"/>
                    </svg>
                </span>
                <span class="nav-label">Informasi Kamar</span>
                <span class="nav-arrow">&rsaquo;</span>
            </a>

            <a href="{{ route('pembayaran') }}" class="nav-item">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="15" rx="2"/>
                        <path d="M3 9h18M16 14h5"/>
                        <circle cx="16" cy="14" r="1"/>
                    </svg>
                </span>
                <span class="nav-label">Pembayaran</span>
            </a>

            <a href="#" class="nav-item">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="4" y="4" width="16" height="18" rx="2"/>
                        <path d="M9 4V2h6v2M8 12l3 3 5-5"/>
                    </svg>
                </span>
                <span class="nav-label">Absensi</span>
            </a>

            <a href="#" class="nav-item">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                        <path d="M14 3v5h5M8 13h8M8 17h6"/>
                    </svg>
                </span>
                <span class="nav-label">Surat dan Perizinan</span>
            </a>

            <a href="#" class="nav-item">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 11 21 4l-4 16-5-6-9-3z"/>
                        <path d="m12 14 5-6"/>
                    </svg>
                </span>
                <span class="nav-label">Pengumuman</span>
            </a>

            <a href="#" class="nav-item">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="m19.4 15 .1.1 1.4 1.1-1.5 2.6-1.8-.7a8 8 0 0 1-2 1.1l-.3 1.9h-3l-.3-1.9a8 8 0 0 1-2-1.1l-1.8.7-1.5-2.6 1.5-1.2a8 8 0 0 1 0-2.2l-1.5-1.2 1.5-2.6 1.8.7a8 8 0 0 1 2-1.1l.3-1.9h3l.3 1.9a8 8 0 0 1 2 1.1l1.8-.7 1.5 2.6-1.5 1.2a8 8 0 0 1 .1 2.2z"/>
                    </svg>
                </span>
                <span class="nav-label">Pengaturan Akun</span>
            </a>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="nav-item logout"
                        style="width:100%;cursor:pointer;text-align:left;font-family:inherit;">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 17l5-5-5-5M15 12H3"/>
                            <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                        </svg>
                    </span>
                    <span class="nav-label">Keluar</span>
                </button>
            </form>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">Dashboard / Informasi Kamar</div>
            <h1 class="page-title">Informasi Kamar</h1>
            <p class="page-subtitle">
                Lihat informasi kamar tempat tinggalmu selama di Ma’had.
            </p>

            <section class="room-card">
                <div class="room-heading">
                    <h2>Kamar Saya</h2>
                    <span class="status">Telah Ditetapkan</span>
                </div>

                <div class="room-highlight">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 18V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v11"/>
                        <path d="M12 11h7a2 2 0 0 1 2 2v5"/>
                        <path d="M3 14h18M3 18v2M21 18v2"/>
                    </svg>

                    <div class="room-label">Nomor Kamar</div>
                    <div class="room-number">
                        {{ $kamar->nomor_kamar ?? 'Belum ditetapkan' }}
                    </div>
                    <div class="muted">
                        Gedung dan lantai ditampilkan sesuai data penempatan.
                    </div>
                </div>

                <div class="room-grid">
                    <div class="room-info">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 21V5h12v16M15 10h6v11M1 21h22"/>
                        </svg>
                        <div class="info-label">Gedung / Lantai</div>
                        <div class="info-value">
                            {{ $kamar->gedung ?? 'Sesuai data' }}
                            @if(!empty($kamar->lantai))
                                / Lantai {{ $kamar->lantai }}
                            @endif
                        </div>
                    </div>

                    <div class="room-info">
                        <svg viewBox="0 0 24 24">
                            <circle cx="9" cy="7" r="3"/>
                            <path d="M3 20v-2a6 6 0 0 1 12 0v2"/>
                            <circle cx="18" cy="8" r="2.5"/>
                            <path d="M17 14a5 5 0 0 1 4 5"/>
                        </svg>
                        <div class="info-label">Kapasitas Kamar</div>
                        <div class="info-value">
                            {{ $kamar->kapasitas ?? 'Sesuai data' }}
                        </div>
                    </div>
                </div>
            </section>

            <section class="detail-card">
                <h2>Detail Penempatan</h2>

                <div class="detail-row">
                    <div class="detail-label">Nama Penghuni</div>
                    <div class="detail-value">
                        {{ auth()->user()->name ?? 'Nama mahasiswa yang login' }}
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Tahun Angkatan</div>
                    <div class="detail-value">
                        {{ auth()->user()->tahun_angkatan ?? 'Sesuai data mahasiswa' }}
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Status Penempatan</div>
                    <div class="detail-value">Telah Ditetapkan</div>
                </div>
            </section>

            <section class="additional-card">
                <div class="additional-heading">
                    <svg viewBox="0 0 24 24">
                        <rect x="5" y="4" width="14" height="17" rx="2"/>
                        <path d="M9 4V2h6v2M9 10h6M9 14h6M9 18h3"/>
                    </svg>
                    Informasi Tambahan
                </div>
                <p>
                    Informasi tata tertib dan ketentuan penggunaan kamar dapat
                    ditampilkan jika tersedia dari pengelola Ma’had.
                </p>
            </section>
        </main>
    </div>
</body>
</html>
