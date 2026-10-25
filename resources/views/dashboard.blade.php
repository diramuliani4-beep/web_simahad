<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard - SIMAHAD UIN Imam Bonjol</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Lora:ital,wght@0,600;1,400&display=swap" rel="stylesheet">

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
            --bg-page: #ffffff;
            --sidebar-width: 280px;
            --header-height: 66px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ================= TOP NAVBAR ================= */
        .top-navbar {
            height: var(--header-height);
            background-color: #ffffff;
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
            gap: 0.85rem;
            text-decoration: none;
        }

        .brand-logo-img {
            height: 44px;
            width: auto;
            object-fit: contain;
        }

        .brand-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--primary-maroon);
            letter-spacing: -0.01em;
            text-transform: uppercase;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1.15rem;
        }

        /* Circular Action Buttons */
        .btn-circle-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: transform 0.15s ease, opacity 0.15s ease;
            text-decoration: none;
        }

        .btn-circle-action:hover {
            transform: scale(1.05);
        }

        .btn-bell-badge {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: var(--primary-maroon);
            color: #ffffff;
            position: relative;
        }

        .btn-bell-badge svg {
            width: 20px;
            height: 20px;
            stroke: #ffffff;
        }

        .btn-profile-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #262626;
            color: #262626;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }

        .btn-profile-badge svg {
            width: 26px;
            height: 26px;
        }

        /* Hamburger for mobile */
        .mobile-toggle-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--primary-maroon);
            cursor: pointer;
            padding: 0.4rem;
        }

        /* ================= BODY WRAPPER ================= */
        .dashboard-body {
            display: flex;
            flex: 1;
            min-height: calc(100vh - var(--header-height));
            width: 100vw;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1.5px solid var(--maroon-border);
            overflow: hidden;
            background-color: #f1f5f9;
        }

        /* Campus building background image */
        .sidebar-bg {
            position: absolute;
            inset: 0;
            z-index: 1;
            background-image: url('{{ asset("images/mahad_al_jamiah.jpeg") }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
        }

        /* Semi-transparent overlay to keep sky & building visible while ensuring text contrast */
        .sidebar-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: linear-gradient(
                180deg, 
                rgba(219, 234, 254, 0.45) 0%, 
                rgba(241, 245, 249, 0.55) 30%, 
                rgba(255, 255, 255, 0.85) 65%, 
                rgba(255, 255, 255, 0.95) 100%
            );
        }

        /* Campus Watermark */
        .sidebar-watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            pointer-events: none;
            text-align: center;
            opacity: 0.28;
            width: 100%;
            padding: 0 10px;
        }

        .watermark-emblem {
            margin: 0 auto 4px;
            display: block;
        }

        .watermark-text-2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.05rem;
            font-weight: 800;
            color: #334155;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        /* Sidebar Navigation Container */
        .sidebar-content {
            position: relative;
            z-index: 4;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            padding: 1.25rem 1.15rem 1.5rem 1.15rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            flex-grow: 1;
        }

        /* Pill Navigation Button */
        .nav-pill {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            height: 46px;
            padding: 0 1rem;
            background-color: #ffffff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 12px;
            text-decoration: none;
            color: #1a1a1a;
            font-size: 0.92rem;
            font-weight: 700;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            cursor: pointer;
            width: 100%;
        }

        .nav-pill:hover {
            transform: translateX(3px);
            background-color: #fff8f9;
        }

        /* Active Nav Pill (Dashboard) */
        .nav-pill.active {
            background-color: var(--maroon-tint);
            border-color: var(--primary-maroon);
            color: var(--primary-maroon);
        }

        .nav-pill.active .nav-icon svg {
            stroke: var(--primary-maroon);
        }

        .nav-pill.active .nav-chevron {
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
            gap: 0.75rem;
        }

        .btn-keluar-pill {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            height: 46px;
            padding: 0 1rem;
            background-color: #ffffff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 12px;
            color: #1a1a1a;
            font-family: inherit;
            font-size: 0.92rem;
            font-weight: 700;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .btn-keluar-pill:hover {
            background-color: #fff1f2;
            color: #be123c;
            transform: translateX(3px);
        }

        .btn-keluar-pill:hover svg {
            stroke: #be123c;
        }

        /* ================= MAIN CONTENT ================= */
        .main-content {
            flex: 1;
            padding: 2rem 2.75rem 3rem 2.75rem;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            overflow-y: auto;
        }

        /* Assalamu'alaikum Heading */
        .welcome-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 1.35rem;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        /* Card Container with Maroon Outline */
        .simahad-card {
            border: 1.5px solid var(--maroon-border);
            border-radius: 16px;
            background-color: #ffffff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .simahad-card:hover {
            box-shadow: 0 4px 14px rgba(110, 0, 27, 0.06);
        }

        /* Top Student Summary Card */
        .student-summary-card {
            padding: 1.25rem 1.65rem;
            margin-bottom: 1.35rem;
        }

        .card-heading-maroon {
            color: var(--primary-maroon);
            font-size: 0.98rem;
            font-weight: 700;
            margin-bottom: 0.85rem;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .student-profile-row {
            display: flex;
            align-items: center;
            gap: 1.15rem;
        }

        .student-avatar-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background-color: #fdf2f4;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--primary-maroon);
        }

        .student-info-col {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .student-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .student-program {
            font-size: 0.88rem;
            color: var(--text-secondary);
            font-weight: 500;
            line-height: 1.3;
        }

        .student-status {
            font-size: 0.82rem;
            font-weight: 600;
            color: #16a34a;
            line-height: 1.3;
        }

        /* 2-Column Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.35rem;
            margin-bottom: 1.35rem;
        }

        .grid-card {
            padding: 1.25rem 1.45rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 220px;
        }

        .grid-card.compact-height {
            min-height: 175px;
        }

        .card-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .card-date-badge {
            color: var(--primary-maroon);
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            user-select: none;
        }

        /* Jadwal Subcard */
        .jadwal-inner-box {
            border: 1.2px solid var(--maroon-border);
            border-radius: 10px;
            padding: 0.75rem 0.95rem;
            margin-top: 0.95rem;
            background-color: #ffffff;
        }

        .jadwal-inner-header {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--primary-maroon);
            font-size: 0.74rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .jadwal-inner-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.2rem;
            line-height: 1.2;
        }

        .jadwal-inner-subtitle {
            font-size: 0.74rem;
            color: var(--text-muted);
            line-height: 1.35;
        }

        .jadwal-meta-row {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            margin-top: 0.75rem;
        }

        .jadwal-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.76rem;
            color: var(--text-secondary);
        }

        .jadwal-meta-icon {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            background-color: #fdf2f4;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-maroon);
            flex-shrink: 0;
        }

        .jadwal-meta-label {
            font-weight: 700;
            color: #374151;
        }

        .jadwal-meta-val {
            color: var(--text-muted);
        }

        /* Tagihan Center Content */
        .tagihan-center-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.25rem 0.5rem;
            flex-grow: 1;
        }

        .tagihan-empty-title {
            color: var(--primary-maroon);
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 0.4rem;
            letter-spacing: -0.01em;
        }

        .tagihan-empty-desc {
            color: #1f2937;
            font-size: 0.92rem;
            font-weight: 500;
            max-width: 290px;
            line-height: 1.45;
        }

        /* Absensi Badges */
        .absensi-badges-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0.85rem 0 0.65rem 0;
        }

        .stat-pill {
            flex: 1;
            text-align: center;
            padding: 0.45rem 0.5rem;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .stat-pill.hadir {
            background-color: #bbf7d0;
            border: 1.2px solid #22c55e;
            color: #15803d;
        }

        .stat-pill.izin {
            background-color: #fee2e2;
            border: 1.2px solid #f87171;
            color: #b91c1c;
        }

        .stat-pill.alpa {
            background-color: #fce7f3;
            border: 1.2px solid #f472b6;
            color: #9d174d;
        }

        .stat-number {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-label {
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .absensi-meeting-count {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .attendance-progress-track {
            width: 100%;
            height: 5px;
            background-color: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 0.3rem;
        }

        .attendance-progress-bar {
            height: 100%;
            background-color: #15803d;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        .attendance-progress-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .attendance-percentage-val {
            font-weight: 700;
            color: #15803d;
        }

        /* Detail Link in Card Footer */
        .card-detail-footer {
            text-align: right;
            margin-top: 0.75rem;
        }

        .link-card-detail {
            color: var(--primary-maroon);
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: color 0.15s ease, transform 0.15s ease;
        }

        .link-card-detail:hover {
            color: var(--primary-maroon-hover);
            transform: translateX(2px);
            text-decoration: underline;
        }

        /* Bottom Full-width Cards */
        .bottom-card {
            padding: 1.15rem 1.45rem;
            margin-bottom: 1.35rem;
            min-height: 76px;
            display: flex;
            align-items: flex-start;
            cursor: pointer;
        }

        .bottom-card-icon {
            color: var(--primary-maroon);
            width: 26px;
            height: 26px;
            flex-shrink: 0;
        }

        /* ================= MODAL ================= */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
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
            background: #ffffff;
            border-radius: 16px;
            max-width: 480px;
            width: 100%;
            padding: 1.85rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);
            position: relative;
            animation: modalFadeIn 0.2s ease-out;
            border: 1.5px solid var(--maroon-border);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(8px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-maroon);
            margin-bottom: 0.75rem;
        }

        .modal-body {
            font-size: 0.95rem;
            color: #374151;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .btn-modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #94a3b8;
            line-height: 1;
        }

        .btn-modal-action {
            width: 100%;
            background-color: var(--primary-maroon);
            color: #ffffff;
            padding: 0.75rem;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-modal-action:hover {
            background-color: var(--primary-maroon-hover);
        }

        /* Backdrop for Mobile Sidebar */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 45;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 992px) {
            .mobile-toggle-btn {
                display: block;
            }

            .sidebar {
                position: fixed;
                top: var(--header-height);
                bottom: 0;
                left: -300px;
                z-index: 46;
                transition: left 0.3s ease;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-backdrop.active {
                display: block;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 1.5rem 1.25rem 2.5rem 1.25rem;
            }
        }
    </style>
</head>
<body>

    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <!-- Mobile Menu Toggle Button -->
            <button type="button" class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Buka Menu">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Brand SIMAHAD -->
            <a href="{{ route('dashboard') }}" class="navbar-brand-wrap">
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD" class="brand-logo-img">
                <span class="brand-title">SIMAHAD</span>
            </a>
        </div>

        <!-- Right Header Icons -->
        <div class="navbar-actions">
            <!-- Notification Bell Icon (Maroon circle with white bell) -->
            <button type="button" class="btn-circle-action btn-bell-badge" title="Pemberitahuan" onclick="openInfoModal('Pemberitahuan', 'Tidak ada pemberitahuan baru saat ini. Semua tagihan dan absensi Anda dalam keadaan terkini.')">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </button>

            <!-- User Avatar Icon (Dark circle with silhouette) -->
            <button type="button" class="btn-circle-action btn-profile-badge" title="Profil Pengguna" onclick="openInfoModal('Profil Pengguna', '<strong>Nama:</strong> {{ Auth::user()->name }}<br><strong>NIM:</strong> {{ Auth::user()->nim ?? '-' }}<br><strong>Email:</strong> {{ Auth::user()->email }}<br><strong>Status:</strong> Penghuni Ma\'had Al-Jami\'ah')">
                <svg viewBox="0 0 24 24" fill="none" stroke="#262626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </button>
        </div>
    </header>

    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="dashboard-body">
        <!-- ================= SIDEBAR ================= -->
        <aside class="sidebar" id="sidebar">
            <!-- Architectural Campus Photo Background -->
            <div class="sidebar-bg"></div>
            <!-- Translucent Overlay -->
            <div class="sidebar-overlay"></div>

            <!-- Campus Watermark matching the design -->
            <div class="sidebar-watermark">
                <svg class="watermark-emblem" width="60" height="60" viewBox="0 0 100 100" fill="none">
                    <polygon points="50,4 92,25 92,75 50,96 8,75 8,25" stroke="#a16207" stroke-width="3" fill="none" />
                    <polygon points="50,14 82,30 82,70 50,86 18,70 18,30" stroke="#64748b" stroke-width="2.5" fill="none" />
                    <path d="M50 20 L50 80 M25 50 L75 50" stroke="#64748b" stroke-width="2" />
                </svg>
                <div class="watermark-text-2">UIN Imam Bonjol</div>
            </div>

            <!-- Sidebar Navigation Content -->
            <div class="sidebar-content">
                <nav class="sidebar-nav">
                    <!-- 1. Dashboard (Active) -->
                    <a href="{{ route('dashboard') }}" class="nav-pill active">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                            </svg>
                        </span>
                        <span class="nav-label">Dashboard</span>
                        <span class="nav-chevron">&gt;</span>
                    </a>

                    <!-- 2. Pendaftaran -->
                    <a href="{{ route('pendaftaran.pribadi') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pendaftaran</span>
                    </a>

                    
<!-- Pembelajaran -->
<a href="{{ route('pembelajaran.mhs') }}" class="nav-pill">
    <span class="nav-icon">
        <svg width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
    </span>
    <span class="nav-label">Pembelajaran</span>
</a>


                    <!-- 3. Informasi Kamar -->
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

                    <!-- 4. Pembayaran -->
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

                    <!-- 5. Absensi -->
                    
<a href="{{ route('absensi.mhs') }}" class="nav-pill">
    <span class="nav-icon">
        <svg width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
            <rect x="8" y="2" width="8" height="4" rx="1"></rect>
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
            <path d="m9 14 2 2 4-4"></path>
        </svg>
    </span>
    <span class="nav-label">Absensi</span>
</a>


                    <!-- 6. Surat dan Perizinan -->
                    <a href="javascript:void(0)" class="nav-pill" onclick="openInfoModal('Surat dan Perizinan', 'Ajukan surat izin keluar asrama, izin pulang, atau surat keterangan tinggal di asrama.')">
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
                    </a>

                    <!-- 7. Pengumuman -->
                    <a href="javascript:void(0)" class="nav-pill" onclick="openInfoModal('Pengumuman', 'Belum ada pengumuman mendesak. Pantau selalu informasi kegiatan asrama di sini.')">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 11 18-5v12L3 14v-3z"></path>
                                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pengumuman</span>
                    </a>
                </nav>

                <!-- Bottom Section -->
                <div class="sidebar-nav-bottom">
                    <!-- 8. Pengaturan Akun -->
                    <a href="javascript:void(0)" class="nav-pill" onclick="openInfoModal('Pengaturan Akun', 'Pengaturan Profil Mahasiswa:<br>• Ubah Kata Sandi<br>• Kontak Darurat Wali<br>• Preferensi Notifikasi')">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pengaturan Akun</span>
                    </a>

                    <!-- 9. Keluar -->
                    <form action="{{ route('logout') }}" method="POST" id="logoutForm" style="margin: 0;">
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
            <!-- Greeting Header -->
            <h1 class="welcome-heading">
                Assalamu’alaikum <span>👋</span>
            </h1>

            <!-- Section 1: Ringkasan Mahasiswa -->
            <section class="simahad-card student-summary-card">
                <div class="card-heading-maroon">
                    Ringkasan Mahasiswa
                </div>
                <div class="student-profile-row">
                    <div class="student-avatar-box">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="student-info-col">
                        <div class="student-name">
                            {{ Auth::user()->name ?? 'Nama Mahasiswa' }}
                        </div>
                        <div class="student-program">
                            Program studi – Angkatan
                        </div>
                        <div class="student-status">
                            Status: Penghuni Ma'had
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 2: 2x2 Grid of Cards -->
            <div class="dashboard-grid">
                <!-- Card 1: Jadwal Pembelajaran -->
                <div class="simahad-card grid-card">
                    <div>
                        <div class="card-header-flex">
                            <div class="card-heading-maroon" style="margin-bottom: 0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>Jadwal Pembelajaran</span>
                            </div>
                            <div class="card-date-badge" onclick="openInfoModal('Kalender Pembelajaran', 'Jadwal aktif untuk semester ganjil tahun ajaran 2026/2027.')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                                <span style="font-size: 0.8rem;">&#9662;</span>
                            </div>
                        </div>

                        <!-- Inner Box: Belum ada jadwal -->
                        <div class="jadwal-inner-box">
                            <div class="jadwal-inner-header">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>Jadwal Pembelajaran</span>
                            </div>
                            <div class="jadwal-inner-title">Tidak Ada Jadwal Saat Ini</div>
                            <div class="jadwal-inner-subtitle">Belum ada jadwal pembelajaran. Tetap semangat belajar mandiri, ya!</div>
                        </div>

                        <!-- Meta Row -->
                        <div class="jadwal-meta-row">
                            <div class="jadwal-meta-item">
                                <span class="jadwal-meta-icon">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <span class="jadwal-meta-label">Kelompok Belajar</span>
                                <span class="jadwal-meta-val">Kelompok mahasiswa</span>
                            </div>
                            <div class="jadwal-meta-item">
                                <span class="jadwal-meta-icon">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </span>
                                <span class="jadwal-meta-label">Mata Pelajaran</span>
                                <a href="javascript:void(0)" onclick="openInfoModal('Mata Pelajaran Asrama', '1. Bahasa Arab Dasar & Lanjutan<br>2. Tahsin Tilawah Al-Qur\'an<br>3. Fiqih Ibadah & Muamalah<br>4. Akhlak & Adab Santri')" style="color: var(--primary-maroon); text-decoration: none; font-weight: 600;" class="jadwal-meta-val">Lihat Mata Pelajaran</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-detail-footer">
                        <a href="javascript:void(0)" onclick="openInfoModal('Detail Pembelajaran', 'Kalender & Jadwal Pembelajaran Ma\'had:<br>• Subuh: Kajian Al-Qur\'an & Tahsin (05:30 - 06:30)<br>• Pagi: Bahasa Arab (08:00 - 10:00)<br>• Maghrib: Kajian Kitab Kuning / Akhlak (18:45 - 19:45)')" class="link-card-detail">Detail pembelajaran &rarr;</a>
                    </div>
                </div>

                <!-- Card 2: Total Tagihan -->
                <div class="simahad-card grid-card">
                    <div>
                        <div class="card-heading-maroon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                                <path d="M16 3H4a2 2 0 0 0-2 2v2"></path>
                                <circle cx="16" cy="14" r="1.5" fill="currentColor"></circle>
                            </svg>
                            <span>Total Tagihan</span>
                        </div>

                        <!-- Center Empty Notice -->
                        <div class="tagihan-center-wrap">
                            <div class="tagihan-empty-title">Belum Ada Tagihan Baru</div>
                            <div class="tagihan-empty-desc">Terimakasih telah melunasi tagihan periode ini</div>
                        </div>
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('pembayaran') }}" class="link-card-detail">Detail pembayaran &rarr;</a>
                    </div>
                </div>

                <!-- Card 3: Informasi Kamar -->
                <div class="simahad-card grid-card compact-height">
                    <div>
                        <div class="card-heading-maroon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 4v16"></path>
                                <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                                <path d="M2 17h20"></path>
                                <path d="M6 8v9"></path>
                            </svg>
                            <span>Informasi Kamar</span>
                        </div>

                        <!-- Room Content / Empty Placeholder matching screenshot -->
                        <div style="min-height: 48px; display: flex; align-items: center; padding: 0.25rem 0;">
                            @if(isset($kamar) && $kamar)
                                <div style="font-size: 0.92rem; color: #1f2937;">
                                    <strong>{{ $kamar->nama_kamar }}</strong> - Gedung {{ $kamar->gedung ?? 'Utama' }} (Lantai {{ $kamar->lantai ?? '1' }})
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('kamar') }}" class="link-card-detail">Detail informasi kamar &rarr;</a>
                    </div>
                </div>

                <!-- Card 4: Absensi -->
                <div class="simahad-card grid-card compact-height">
                    <div>
                        <div class="card-heading-maroon" style="margin-bottom: 0.45rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="8" y="2" width="8" height="4" rx="1"></rect>
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <path d="m9 14 2 2 4-4"></path>
                            </svg>
                            <span>Absensi</span>
                        </div>

                        <!-- 3 Badges: Hadir, Izin, Alpa -->
                        <div class="absensi-badges-row">
                            <div class="stat-pill hadir">
                                <span class="stat-number">10</span>
                                <span class="stat-label">Hadir</span>
                            </div>
                            <div class="stat-pill izin">
                                <span class="stat-number">2</span>
                                <span class="stat-label">Izin</span>
                            </div>
                            <div class="stat-pill alpa">
                                <span class="stat-number">1</span>
                                <span class="stat-label">Alpa</span>
                            </div>
                        </div>

                        <!-- Progress Bar & Count -->
                        <div class="absensi-meeting-count">Total pertemuan: 16 kali</div>
                        <div class="attendance-progress-track">
                            <div class="attendance-progress-bar" style="width: 75%;"></div>
                        </div>
                        <div class="attendance-progress-footer">
                            <span>Persentase kehadiran</span>
                            <span class="attendance-percentage-val">75%</span>
                        </div>
                    </div>

                    <div class="card-detail-footer">
                        <a href="javascript:void(0)" onclick="openInfoModal('Detail Absensi', 'Statistik Kehadiran Mahasiswa:<br>• Total Pertemuan: 16 Pertemuan<br>• Kehadiran: 10 Pertemuan<br>• Izin: 2 Pertemuan<br>• Alpa: 1 Pertemuan<br>• Persentase: 75% (Status: Memenuhi Syarat Minimal 70%)')" class="link-card-detail">Detail absensi &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Section 3: Bottom Full-Width Card (Pengumuman) -->
            <section class="simahad-card bottom-card" onclick="openInfoModal('Pengumuman Asrama', 'Belum ada pengumuman terbaru hari ini. Silakan periksa kembali secara berkala.')">
                <div class="bottom-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 11 18-5v12L3 14v-3z"></path>
                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                    </svg>
                </div>
            </section>

            <!-- Section 4: Bottom Full-Width Card (Surat dan Perizinan) -->
            <section class="simahad-card bottom-card" onclick="openInfoModal('Surat dan Perizinan', 'Tidak ada pengajuan perizinan aktif. Klik untuk mengajukan izin bermalam atau keluar asrama.')">
                <div class="bottom-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
            </section>
        </main>
    </div>

    <!-- ================= INFO MODAL DIALOG ================= -->
    <div id="infoModal" class="modal-overlay" onclick="closeModalOnBackdrop(event)">
        <div class="modal-box">
            <button type="button" class="btn-modal-close" onclick="closeModal()">&times;</button>
            <h3 class="modal-title" id="modalTitle">Informasi</h3>
            <div class="modal-body" id="modalBody">Konten</div>
            <button type="button" class="btn-modal-action" onclick="closeModal()">Tutup</button>
        </div>
    </div>

    <script>
        // Modal logic
        function openInfoModal(title, content) {
            document.getElementById('modalTitle').innerHTML = title;
            document.getElementById('modalBody').innerHTML = content;
            document.getElementById('infoModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('infoModal').classList.remove('active');
        }

        function closeModalOnBackdrop(event) {
            if (event.target === document.getElementById('infoModal')) {
                closeModal();
            }
        }

        // Mobile sidebar toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (mobileMenuBtn) {
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
</body>
</html>
