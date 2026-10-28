
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat & Perizinan - SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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
            --sidebar-width: 280px;
            --header-height: 66px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fff;
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button, input, select, textarea {
            font: inherit;
        }

        /* ================= NAVBAR ================= */
        .top-navbar {
            height: var(--header-height);
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
            cursor: pointer;
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
            position: relative;
        }

        .btn-bell-badge svg {
            width: 20px;
            height: 20px;
            stroke: #fff;
        }

        .notification-dot {
            position: absolute;
            top: 5px;
            right: 6px;
            width: 7px;
            height: 7px;
            background: #e6b94f;
            border-radius: 50%;
            border: 1px solid #fff;
        }

        .btn-profile-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #262626;
            background: #fff;
            color: #262626;
        }

        .btn-profile-badge svg {
            width: 26px;
            height: 26px;
        }

        .mobile-toggle-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--primary-maroon);
            cursor: pointer;
            padding: .4rem;
        }

        /* ================= BODY & SIDEBAR ================= */
        .dashboard-body {
            display: flex;
            min-height: calc(100vh - var(--header-height));
            width: 100%;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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
                rgba(219,234,254,.45) 0%,
                rgba(241,245,249,.55) 30%,
                rgba(255,255,255,.85) 65%,
                rgba(255,255,255,.95) 100%
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
            margin: 0 auto 4px;
            display: block;
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
            min-height: calc(100vh - var(--header-height));
            width: 100%;
            justify-content: space-between;
            padding: 1.25rem 1.15rem 1.5rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            flex-grow: 1;
        }

        .nav-pill {
            display: flex;
            align-items: center;
            gap: .85rem;
            min-height: 46px;
            padding: 0 1rem;
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 12px;
            color: #1a1a1a;
            font-size: .92rem;
            font-weight: 700;
            transition: all .2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,.03);
            cursor: pointer;
            width: 100%;
        }

        .nav-pill:hover {
            transform: translateX(3px);
            background: #fff8f9;
        }

        .nav-pill.active {
            background: var(--maroon-tint);
            border-color: var(--primary-maroon);
            color: var(--primary-maroon);
        }

        .nav-pill.active .nav-icon svg {
            stroke: var(--primary-maroon);
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
            stroke: #262626;
        }

        .nav-label {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-chevron {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary-maroon);
            line-height: 1;
            margin-left: auto;
        }

        .sidebar-nav-bottom {
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .btn-keluar-pill {
            display: flex;
            align-items: center;
            gap: .85rem;
            min-height: 46px;
            padding: 0 1rem;
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 12px;
            color: #1a1a1a;
            font-size: .92rem;
            font-weight: 700;
            transition: all .2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,.03);
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .btn-keluar-pill:hover {
            background: #fff1f2;
            color: #be123c;
            transform: translateX(3px);
        }

        .btn-keluar-pill:hover svg {
            stroke: #be123c;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 45;
        }

        /* ================= MAIN CONTENT ================= */
        .main-content {
            flex: 1;
            min-width: 0;
            padding: 2rem 2.75rem 3rem;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .breadcrumb {
            color: var(--primary-maroon);
            font-size: .9rem;
            font-weight: 600;
            margin-bottom: .55rem;
        }

        .page-header h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: .4rem;
        }

        .page-header p {
            font-size: .95rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: .85rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 14px;
            min-height: 115px;
            padding: 1rem 1.15rem;
        }

        .summary-icon {
            font-size: 1.2rem;
            margin-bottom: .35rem;
        }

        .waiting { color: #c9912d; }
        .approved { color: #40956c; }
        .rejected { color: #d65f5f; }

        .summary-number {
            font-size: 1.5rem;
            font-weight: 800;
        }

        .summary-label {
            color: var(--text-muted);
            font-size: .8rem;
            margin-top: .2rem;
        }

        .request-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.6rem;
        }

        .request-card {
            border: 1.5px solid var(--maroon-border);
            border-radius: 14px;
            background: #fff;
            min-height: 185px;
            padding: 1.1rem 1.25rem;
            transition: .2s;
        }

        .request-card:hover {
            box-shadow: 0 4px 14px rgba(110,0,27,.06);
            transform: translateY(-2px);
        }

        .request-icon {
            width: 46px;
            height: 46px;
            background: #fdf2f4;
            color: var(--primary-maroon);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: .8rem;
        }

        .request-card h3 {
            font-size: .95rem;
            font-weight: 800;
            margin-bottom: .45rem;
        }

        .request-card p {
            font-size: .82rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: .65rem;
        }

        .request-link {
            border: none;
            background: none;
            color: var(--primary-maroon);
            font-size: .84rem;
            font-weight: 800;
            cursor: pointer;
        }

        .request-link:hover {
            text-decoration: underline;
        }

        .history-card {
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 14px;
            overflow: hidden;
        }

        .history-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background .2s;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-item:hover {
            background: #fff9fa;
        }

        .history-icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            background: #fdf2f4;
            color: var(--primary-maroon);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.15rem;
        }

        .history-info {
            flex: 1;
            min-width: 0;
        }

        .history-info h3 {
            font-size: .9rem;
            font-weight: 800;
            margin-bottom: .3rem;
        }

        .history-date {
            font-size: .78rem;
            color: var(--text-muted);
        }

        .status {
            font-size: .78rem;
            font-weight: 700;
            margin-top: .25rem;
        }

        .status.pending { color: #c9912d; }
        .status.accepted { color: #40956c; }
        .status.denied { color: #d65f5f; }

        .history-arrow {
            color: #888;
            font-size: .9rem;
        }

        .empty-history {
            padding: 2rem;
            text-align: center;
            color: #888;
            font-size: .85rem;
        }

        /* ================= FORM MODALS ================= */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(2px);
            padding: 20px;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-box {
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 1.6rem;
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid var(--maroon-border);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,.15);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 1.2rem;
        }

        .modal-header h2 {
            color: var(--primary-maroon);
            font-size: 1.25rem;
            font-weight: 800;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: #777;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-size: .85rem;
            font-weight: 700;
            margin-bottom: .45rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: .7rem .75rem;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: .85rem;
            outline: none;
            background: #fff;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-maroon);
            box-shadow: 0 0 0 3px rgba(110,0,27,.08);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 90px;
        }

        .form-note {
            font-size: .75rem;
            color: #888;
            margin-top: .35rem;
        }

        .btn-submit {
            width: 100%;
            padding: .8rem;
            background: var(--primary-maroon);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 800;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: var(--primary-maroon-hover);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: .8rem 0;
            border-bottom: 1px solid #eee;
            font-size: .85rem;
        }

        .detail-row span:first-child {
            color: #777;
            flex-shrink: 0;
        }

        .detail-row span:last-child {
            text-align: right;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .alert {
            display: none;
            margin-bottom: 1rem;
            border-radius: 9px;
            padding: .75rem;
            font-size: .82rem;
        }

        .alert.show { display: block; }

        .alert.success {
            color: #246b46;
            background: #e9f7ef;
            border: 1px solid #b7e3c8;
        }

        .alert.error {
            color: #a21c2e;
            background: #fff0f0;
            border: 1px solid #f2c0c0;
        }

        /* ================= INFO MODAL ================= */
        .info-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(2px);
            z-index: 110;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .info-modal-overlay.active {
            display: flex;
        }

        .info-modal-box {
            background: #fff;
            border-radius: 16px;
            max-width: 480px;
            width: 100%;
            padding: 1.85rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,.15);
            position: relative;
            border: 1.5px solid var(--maroon-border);
        }

        .info-modal-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-maroon);
            margin-bottom: .75rem;
        }

        .info-modal-body {
            font-size: .9rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 1.25rem;
            overflow-wrap: anywhere;
        }

        .btn-info-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #94a3b8;
        }

        .btn-info-action {
            width: 100%;
            background: var(--primary-maroon);
            color: #fff;
            padding: .75rem;
            border-radius: 10px;
            border: none;
            font-weight: 800;
            font-size: .9rem;
            cursor: pointer;
        }

        .btn-info-action:hover {
            background: var(--primary-maroon-hover);
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1100px) {
            .main-content {
                padding: 1.7rem 1.5rem 2.5rem;
            }

            .summary-grid {
                gap: .75rem;
            }

            .request-grid {
                gap: .75rem;
            }
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
                box-shadow: 4px 0 15px rgba(0,0,0,.1);
                overflow-y: auto;
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-content {
                min-height: calc(100vh - var(--header-height));
            }

            .sidebar-backdrop.active {
                display: block;
            }

            .main-content {
                padding: 1.5rem 1.25rem 2.5rem;
            }
        }

        @media (max-width: 650px) {
            .top-navbar {
                padding: 0 1rem;
            }

            .brand-title {
                font-size: 1.25rem;
            }

            .brand-logo-img {
                height: 38px;
                max-width: 42px;
            }

            .navbar-actions {
                gap: .6rem;
            }

            .btn-profile-badge {
                width: 36px;
                height: 36px;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .summary-card {
                min-height: 95px;
            }

            .request-grid {
                grid-template-columns: 1fr;
            }

            .history-item {
                padding: .85rem;
                gap: .7rem;
            }

            .history-icon {
                width: 40px;
                height: 40px;
            }

            .detail-row {
                gap: 10px;
                font-size: .8rem;
            }
        }
    </style>
</head>

<body>

<!-- ================= TOP NAVBAR ================= -->
<header class="top-navbar">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <button type="button" class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Buka Menu">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <a href="{{ route('dashboard') }}" class="navbar-brand-wrap">
            <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD" class="brand-logo-img">
            <span class="brand-title">SIMAHAD</span>
        </a>
    </div>

    <div class="navbar-actions">
        <button type="button" class="btn-circle-action btn-bell-badge"
                title="Pemberitahuan"
                onclick="openInfoModal('Pemberitahuan', 'Tidak ada pemberitahuan baru saat ini.')">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notification-dot"></span>
        </button>

        <button type="button" class="btn-circle-action btn-profile-badge"
                title="Profil Pengguna"
                onclick="openInfoModal('Profil Pengguna', '<strong>Nama:</strong> {{ Auth::user()->name ?? 'Mahasiswa' }}<br><strong>NIM:</strong> {{ Auth::user()->nim ?? '-' }}<br><strong>Email:</strong> {{ Auth::user()->email ?? '-' }}<br><strong>Status:</strong> Penghuni Ma’had Al-Jami’ah')">
            <svg viewBox="0 0 24 24" fill="none" stroke="#262626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </button>
    </div>
</header>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="dashboard-body">

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-bg"></div>
        <div class="sidebar-overlay"></div>

        <div class="sidebar-watermark">
            <svg class="watermark-emblem" width="60" height="60" viewBox="0 0 100 100" fill="none">
                <polygon points="50,4 92,25 92,75 50,96 8,75 8,25" stroke="#a16207" stroke-width="3" fill="none"/>
                <polygon points="50,14 82,30 82,70 50,86 18,70 18,30" stroke="#64748b" stroke-width="2.5" fill="none"/>
                <path d="M50 20 L50 80 M25 50 L75 50" stroke="#64748b" stroke-width="2"/>
            </svg>
            <div class="watermark-text-2">UIN Imam Bonjol</div>
        </div>

        <div class="sidebar-content">
            <nav class="sidebar-nav">

                <a href="{{ route('dashboard') }}" class="nav-pill">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                        </svg>
                    </span>
                    <span class="nav-label">Dashboard</span>
                </a>

                <a href="{{ route('pendaftaran.pribadi') }}" class="nav-pill">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Pendaftaran</span>
                </a>

                <a href="{{ route('pembelajaran.mhs') }}" class="nav-pill">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Pembelajaran</span>
                </a>

                <a href="{{ route('kamar') }}" class="nav-pill">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 4v16"></path>
                            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                            <path d="M2 17h20"></path>
                            <path d="M6 8v9"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Informasi Kamar</span>
                </a>

                <a href="{{ route('pembayaran') }}" class="nav-pill">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                            <path d="M16 3H4a2 2 0 0 0-2 2v2"></path>
                            <circle cx="16" cy="14" r="1.5" fill="#262626"></circle>
                        </svg>
                    </span>
                    <span class="nav-label">Pembayaran</span>
                </a>

                <a href="{{ route('absensi.mhs') }}" class="nav-pill">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="8" y="2" width="8" height="4" rx="1"></rect>
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                            <path d="m9 14 2 2 4-4"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Absensi</span>
                </a>

                <a href="{{ route('surat.perizinan.mhs') }}" class="nav-pill active" aria-current="page">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </span>
                    <span class="nav-label">Surat dan Perizinan</span>
                    <span class="nav-chevron">&gt;</span>
                </a>

                <a href="{{ route('pengumuman.mhs') }}" class="nav-pill">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 11 18-5v12L3 14v-3z"></path>
                            <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Pengumuman</span>
                </a>
            </nav>

            <div class="sidebar-nav-bottom">
                <a href="javascript:void(0)" class="nav-pill"
                   onclick="openInfoModal('Pengaturan Akun', 'Pengaturan Profil Mahasiswa:<br>• Ubah Kata Sandi<br>• Kontak Darurat Wali<br>• Preferensi Notifikasi')">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Pengaturan Akun</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-keluar-pill">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </span>
                        <span class="nav-label">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-content">
        <section class="page-header">
            <div class="breadcrumb">Dashboard / Surat &amp; Perizinan</div>
            <h1>Surat &amp; Perizinan</h1>
            <p>Ajukan surat dan perizinan serta pantau status pengajuanmu.</p>
        </section>

        <section>
            <h2 class="section-title">Ringkasan Pengajuan</h2>

            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-icon waiting"><i class="fa-regular fa-clock"></i></div>
                    <div class="summary-number" id="countPending">1</div>
                    <div class="summary-label">Menunggu</div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon approved"><i class="fa-regular fa-circle-check"></i></div>
                    <div class="summary-number" id="countApproved">1</div>
                    <div class="summary-label">Disetujui</div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon rejected"><i class="fa-regular fa-circle-xmark"></i></div>
                    <div class="summary-number" id="countRejected">1</div>
                    <div class="summary-label">Ditolak</div>
                </div>
            </div>

            <h2 class="section-title">Buat Pengajuan</h2>

            <div class="request-grid">
                <div class="request-card">
                    <div class="request-icon"><i class="fa-regular fa-file-lines"></i></div>
                    <h3>Pengajuan Surat</h3>
                    <p>Ajukan surat untuk keperluan tertentu.</p>
                    <button class="request-link" type="button" onclick="openForm('Pengajuan Surat')">
                        Buat Pengajuan &rarr;
                    </button>
                </div>

                <div class="request-card">
                    <div class="request-icon"><i class="fa-regular fa-calendar-check"></i></div>
                    <h3>Izin Kegiatan</h3>
                    <p>Ajukan izin untuk kegiatan di luar Ma’had.</p>
                    <button class="request-link" type="button" onclick="openForm('Izin Kegiatan')">
                        Buat Pengajuan &rarr;
                    </button>
                </div>

                
<div class="request-card">
    <div class="request-icon"><i class="fa-solid fa-house"></i></div>
    <h3>Izin Pulang</h3>
    <p>Ajukan izin pulang dan isi rencana keberangkatan serta kepulangan.</p>
    <a href="{{ route('surat.perizinan.mhs.pengajuan') }}" class="request-link">
        Buat Pengajuan &rarr;
    </a>
</div>


                <div class="request-card">
                    <div class="request-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <h3>Riwayat Pengajuan</h3>
                    <p>Lihat pengajuan sebelumnya dan statusnya.</p>
                    <button class="request-link" type="button" onclick="showHistory()">
                        Lihat Riwayat &rarr;
                    </button>
                </div>
            </div>

            <h2 class="section-title">Pengajuan Terbaru</h2>

            <div class="history-card" id="historyList"></div>
        </section>
    </main>
</div>

<!-- ================= MODAL PENGAJUAN ================= -->
<div class="modal" id="formModal">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="formTitle">Buat Pengajuan</h2>
            <button type="button" class="close-modal" onclick="closeModal('formModal')">&times;</button>
        </div>

        <div id="formAlert" class="alert"></div>

        <form id="permissionForm" onsubmit="submitForm(event)">
            <div class="form-group">
                <label for="jenis">Jenis Pengajuan</label>
                <select id="jenis" name="jenis" required onchange="updateFormFields()">
                    <option value="">Pilih jenis pengajuan</option>
                    <option value="Pengajuan Surat">Pengajuan Surat</option>
                    <option value="Izin Kegiatan">Izin Kegiatan</option>
                    <option value="Izin Pulang">Izin Pulang</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggalMulai">Tanggal Mulai / Keberangkatan</label>
                <input type="date" id="tanggalMulai" name="tanggalMulai" required>
            </div>

            <div class="form-group" id="tanggalKembaliGroup">
                <label for="tanggalKembali">Tanggal Kembali</label>
                <input type="date" id="tanggalKembali" name="tanggalKembali">
            </div>

            <div class="form-group">
                <label for="keperluan">Keperluan / Alasan</label>
                <textarea id="keperluan" name="keperluan" placeholder="Jelaskan keperluan pengajuan..." required></textarea>
            </div>

            <div class="form-group">
                <label for="lampiran">Lampiran (opsional)</label>
                <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png">
                <div class="form-note">Format yang diperbolehkan: PDF, JPG, JPEG, PNG. Maksimal 5 MB.</div>
            </div>

            <button type="submit" class="btn-submit">Kirim Pengajuan</button>
        </form>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div class="modal" id="detailModal">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Detail Pengajuan</h2>
            <button type="button" class="close-modal" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div id="detailContent"></div>
    </div>
</div>

<!-- ================= MODAL INFORMASI ================= -->
<div class="info-modal-overlay" id="infoModal" onclick="closeInfoModalOnBackdrop(event)">
    <div class="info-modal-box">
        <button type="button" class="btn-info-close" onclick="closeInfoModal()">&times;</button>
        <h3 class="info-modal-title" id="infoModalTitle">Informasi</h3>
        <div class="info-modal-body" id="infoModalBody">Konten</div>
        <button type="button" class="btn-info-action" onclick="closeInfoModal()">Tutup</button>
    </div>
</div>

<script>
    // Contoh data tampilan. Belum terhubung ke database.
    let submissions = [
        {
            jenis: 'Izin Pulang',
            tanggal: '12 Oktober 2026',
            mulai: '2026-10-12',
            kembali: '',
            keperluan: 'Keperluan keluarga',
            status: 'Menunggu Persetujuan'
        },
        {
            jenis: 'Pengajuan Surat',
            tanggal: '10 Oktober 2026',
            mulai: '2026-10-10',
            kembali: '',
            keperluan: 'Keperluan administrasi',
            status: 'Disetujui'
        },
        {
            jenis: 'Izin Kegiatan',
            tanggal: '8 Oktober 2026',
            mulai: '2026-10-08',
            kembali: '',
            keperluan: 'Mengikuti kegiatan di luar Ma’had',
            status: 'Ditolak'
        }
    ];

    function openForm(jenis) {
        document.getElementById('permissionForm').reset();
        document.getElementById('formTitle').textContent = jenis;
        document.getElementById('jenis').value = jenis;
        document.getElementById('formAlert').className = 'alert';
        updateFormFields();
        document.getElementById('formModal').classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    function updateFormFields() {
        const jenis = document.getElementById('jenis').value;
        const kembaliGroup = document.getElementById('tanggalKembaliGroup');
        const kembaliInput = document.getElementById('tanggalKembali');

        if (jenis === 'Izin Pulang') {
            kembaliGroup.style.display = 'block';
            kembaliInput.required = true;
        } else {
            kembaliGroup.style.display = 'none';
            kembaliInput.required = false;
            kembaliInput.value = '';
        }
    }

    function submitForm(event) {
        event.preventDefault();

        const jenis = document.getElementById('jenis').value;
        const mulai = document.getElementById('tanggalMulai').value;
        const kembali = document.getElementById('tanggalKembali').value;
        const keperluan = document.getElementById('keperluan').value.trim();
        const lampiran = document.getElementById('lampiran').files[0];

        if (!jenis || !mulai || !keperluan) {
            showFormAlert('Mohon lengkapi data pengajuan.', 'error');
            return;
        }

        if (jenis === 'Izin Pulang' && kembali && kembali < mulai) {
            showFormAlert('Tanggal kembali tidak boleh sebelum tanggal keberangkatan.', 'error');
            return;
        }

        if (lampiran && lampiran.size > 5 * 1024 * 1024) {
            showFormAlert('Ukuran lampiran maksimal 5 MB.', 'error');
            return;
        }

        const date = new Date(mulai + 'T00:00:00');
        const formattedDate = date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        submissions.unshift({
            jenis: jenis,
            tanggal: formattedDate,
            mulai: mulai,
            kembali: kembali,
            keperluan: keperluan,
            status: 'Menunggu Persetujuan'
        });

        renderHistory();
        updateSummary();
        closeModal('formModal');

        alert('Pengajuan ditambahkan pada tampilan ini. Data belum disimpan ke database.');
    }

    function showFormAlert(message, type) {
        const alertBox = document.getElementById('formAlert');
        alertBox.textContent = message;
        alertBox.className = 'alert show ' + type;
    }

    function updateSummary() {
        const pending = submissions.filter(item => item.status === 'Menunggu Persetujuan').length;
        const approved = submissions.filter(item => item.status === 'Disetujui').length;
        const rejected = submissions.filter(item => item.status === 'Ditolak').length;

        document.getElementById('countPending').textContent = pending;
        document.getElementById('countApproved').textContent = approved;
        document.getElementById('countRejected').textContent = rejected;
    }

    function renderHistory() {
        const list = document.getElementById('historyList');

        if (submissions.length === 0) {
            list.innerHTML = '<div class="empty-history">Belum ada pengajuan.</div>';
            return;
        }

        list.innerHTML = submissions.map((item, index) => {
            let statusClass = 'pending';

            if (item.status === 'Disetujui') statusClass = 'accepted';
            if (item.status === 'Ditolak') statusClass = 'denied';

            let icon = 'fa-file-lines';
            if (item.jenis === 'Izin Pulang') icon = 'fa-house';
            if (item.jenis === 'Izin Kegiatan') icon = 'fa-calendar-check';

            return `
                <div class="history-item" onclick="showDetail(${index})">
                    <div class="history-icon">
                        <i class="fa-regular ${icon}"></i>
                    </div>
                    <div class="history-info">
                        <h3>${escapeHTML(item.jenis)}</h3>
                        <div class="history-date">${escapeHTML(item.tanggal)}</div>
                        <div class="status ${statusClass}">${escapeHTML(item.status)}</div>
                    </div>
                    <i class="fa-solid fa-chevron-right history-arrow"></i>
                </div>
            `;
        }).join('');
    }

    function showDetail(index) {
        const item = submissions[index];
        if (!item) return;

        document.getElementById('detailContent').innerHTML = `
            <div class="detail-row">
                <span>Jenis Pengajuan</span>
                <span>${escapeHTML(item.jenis)}</span>
            </div>
            <div class="detail-row">
                <span>Tanggal Pengajuan</span>
                <span>${escapeHTML(item.tanggal)}</span>
            </div>
            <div class="detail-row">
                <span>Tanggal Mulai</span>
                <span>${escapeHTML(item.mulai || '-')}</span>
            </div>
            <div class="detail-row">
                <span>Tanggal Kembali</span>
                <span>${escapeHTML(item.kembali || '-')}</span>
            </div>
            <div class="detail-row">
                <span>Keperluan</span>
                <span>${escapeHTML(item.keperluan)}</span>
            </div>
            <div class="detail-row">
                <span>Status</span>
                <span>${escapeHTML(item.status)}</span>
            </div>
        `;

        document.getElementById('detailModal').classList.add('show');
    }

    function showHistory() {
        document.getElementById('historyList').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    function openInfoModal(title, content) {
        document.getElementById('infoModalTitle').textContent = title;
        document.getElementById('infoModalBody').innerHTML = content;
        document.getElementById('infoModal').classList.add('active');
    }

    function closeInfoModal() {
        document.getElementById('infoModal').classList.remove('active');
    }

    function closeInfoModalOnBackdrop(event) {
        if (event.target === document.getElementById('infoModal')) {
            closeInfoModal();
        }
    }

    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(char) {
            const entities = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return entities[char];
        });
    }

    // Tutup modal jika pengguna menekan area di luar kotak modal.
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        });
    });

    // Tutup modal dengan tombol Escape.
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal.show').forEach(modal => {
                modal.classList.remove('show');
            });
            closeInfoModal();
        }
    });

    // Menu sidebar pada layar kecil.
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarBackdrop.classList.toggle('active');
        });

        sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
        });

        sidebar.querySelectorAll('a.nav-pill').forEach(link => {
            link.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
            });
        });
    }

    // Tampilkan contoh riwayat dan hitung ringkasan saat halaman dibuka.
    updateFormFields();
    renderHistory();
    updateSummary();
</script>

</body>
</html>