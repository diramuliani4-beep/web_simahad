<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard - Sistem Informasi Asrama Mahasiswa</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary-maroon: #6e001b;
            --primary-maroon-hover: #580015;
            --text-dark: #1a1a1a;
            --text-muted: #596573;
            --bg-main: #fbfbfa;
            --bg-schedule: #faedf0;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Dashboard Container */
        .dashboard-layout {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 1.25rem 2rem 1.5rem;
            z-index: 10;
            overflow: hidden;
            border-right: 1px solid rgba(0, 0, 0, 0.08);
            background-color: #dbeafe;
        }

        /* Realistic Campus Building & Sky Backdrop */
        .sidebar-bg {
            position: absolute;
            inset: 0;
            z-index: 1;
            background-image: url('{{ asset("images/sidebar_bg.jpg") }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            opacity: 0.92;
        }

        /* Light gradient tint to ensure maximum text readability */
        .sidebar-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: linear-gradient(
                180deg, 
                rgba(219, 234, 254, 0.45) 0%, 
                rgba(241, 245, 249, 0.65) 35%, 
                rgba(255, 255, 255, 0.82) 65%, 
                rgba(255, 255, 255, 0.94) 100%
            );
            backdrop-filter: blur(0.5px);
        }

        /* Campus Watermark in center of sidebar */
        .sidebar-watermark {
            position: absolute;
            top: 54%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            pointer-events: none;
            text-align: center;
            opacity: 0.35;
            width: 100%;
            padding: 0 10px;
        }

        .watermark-emblem {
            margin: 0 auto 6px;
            display: block;
        }

        .watermark-text-1 {
            font-family: 'Lora', Georgia, serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: #334155;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .watermark-text-2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            color: #334155;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .sidebar-content {
            position: relative;
            z-index: 4;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
        }

        /* Logo Emblem Top */
        .sidebar-logo {
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }

        .logo-emblem {
            width: 58px;
            height: 58px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));
            transition: transform 0.2s ease;
        }

        .logo-emblem:hover {
            transform: scale(1.04);
        }

        /* Navigation List */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
            flex-grow: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1.1rem;
            text-decoration: none;
            color: #1a1a1a;
            padding: 0.25rem 0.4rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .nav-item:hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, 0.4);
        }

        .nav-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-icon svg {
            display: block;
        }

        /* Specific icon badges */
        .icon-circle-blue {
            width: 34px;
            height: 34px;
            background: #1e70bf;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 2px 5px rgba(30, 112, 191, 0.35);
        }

        .icon-circle-cyan {
            width: 34px;
            height: 34px;
            background: #0ea5e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 2px 5px rgba(14, 165, 233, 0.35);
        }

        .icon-circle-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .nav-text {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.01em;
        }

        /* Logout button at bottom */
        .sidebar-footer {
            margin-top: 2rem;
            padding-top: 1rem;
        }

        .btn-keluar {
            background: transparent;
            border: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #d90429;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-keluar:hover {
            color: #9f021c;
            transform: translateX(4px);
        }

        /* ================= MAIN CONTENT ================= */
        .main-content {
            flex: 1;
            padding: 3.5rem 4rem 3rem 4.5rem;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-main);
            overflow-y: auto;
        }

        @media (max-width: 1024px) {
            .main-content {
                padding: 2rem 2rem 2rem 2.5rem;
            }
        }

        /* Welcome Header */
        .welcome-title {
            font-family: 'Bebas Neue', 'Oswald', sans-serif;
            font-size: clamp(2.4rem, 4.5vw, 3.6rem);
            color: #1a1a1a;
            letter-spacing: 0.03em;
            margin-bottom: 2.8rem;
            text-transform: uppercase;
            line-height: 1.1;
        }

        /* 3 Maroon Status Cards */
        .cards-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.2rem;
            margin-bottom: 5.5rem;
            max-width: 1050px;
        }

        @media (max-width: 960px) {
            .cards-row {
                grid-template-columns: 1fr;
                gap: 1.25rem;
                margin-bottom: 3.5rem;
            }
        }

        .status-card {
            background-color: var(--primary-maroon);
            border-radius: 6px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.24);
            height: 145px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.25rem 1.5rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .status-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 22px rgba(110, 0, 27, 0.35);
            background-color: var(--primary-maroon-hover);
        }

        .card-inner-text {
            font-family: 'Lora', Georgia, serif;
            color: #ffffff;
            font-size: 1.18rem;
            font-weight: 500;
            line-height: 1.45;
            letter-spacing: -0.01em;
        }

        /* Schedule Section */
        .schedule-container {
            max-width: 1050px;
        }

        .schedule-heading {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        /* Soft Pastel Pink Schedule Card */
        .schedule-card {
            background-color: var(--bg-schedule);
            border-radius: 20px;
            padding: 2.2rem 3rem;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
        }

        @media (max-width: 768px) {
            .schedule-card {
                grid-template-columns: 1fr;
                gap: 1rem;
                padding: 1.5rem;
            }
        }

        .schedule-item {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #526071;
            letter-spacing: -0.01em;
        }

        .schedule-item.time {
            text-align: left;
            padding-left: 1.5rem;
        }

        .schedule-item.subject {
            text-align: center;
        }

        .schedule-item.room {
            text-align: right;
            padding-right: 1.5rem;
        }

        @media (max-width: 768px) {
            .schedule-item.time,
            .schedule-item.subject,
            .schedule-item.room {
                text-align: center;
                padding: 0;
            }
        }

        /* Responsive Mobile Toggle */
        .mobile-nav-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 99;
            background: var(--primary-maroon);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 820px) {
            .mobile-nav-toggle {
                display: block;
            }
            .sidebar {
                position: fixed;
                left: -270px;
                transition: left 0.3s ease;
            }
            .sidebar.active {
                left: 0;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.4);
                z-index: 9;
            }
            .sidebar-backdrop.active {
                display: block;
            }
            .main-content {
                padding: 4.5rem 1.5rem 2rem 1.5rem;
            }
        }

        /* Modal Dialog for info */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
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
            padding: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);
            position: relative;
            animation: modalFadeIn 0.2s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(10px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-title {
            font-family: 'Lora', Georgia, serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }

        .modal-body {
            font-size: 0.95rem;
            color: #475569;
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
            cursor: pointer;
            transition: background-color 0.2s;
        }
    </style>
</head>
<body>
    <!-- Mobile Hamburger Toggle -->
    <button type="button" class="mobile-nav-toggle" id="mobileMenuBtn" aria-label="Toggle Menu">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="dashboard-layout">
        <!-- ================= SIDEBAR ================= -->
        <aside class="sidebar" id="sidebar">
            <!-- Architectural Campus Photo Background -->
            <div class="sidebar-bg"></div>
            <!-- Translucent Overlay for readability -->
            <div class="sidebar-overlay"></div>

            <!-- Campus Watermark matching the design -->
            <div class="sidebar-watermark">
                <svg class="watermark-emblem" width="64" height="64" viewBox="0 0 100 100" fill="none">
                    <polygon points="50,4 92,25 92,75 50,96 8,75 8,25" stroke="#a16207" stroke-width="3" fill="none" />
                    <polygon points="50,14 82,30 82,70 50,86 18,70 18,30" stroke="#64748b" stroke-width="2.5" fill="none" />
                    <path d="M50 20 L50 80 M25 50 L75 50" stroke="#64748b" stroke-width="2" />
                </svg>
                <div class="watermark-text-1">ma'had al-jami'ah</div>
                <div class="watermark-text-2">UIN Imam Bonjol</div>
            </div>

            <!-- Sidebar Navigation Content -->
            <div class="sidebar-content">
                <div>
                    <!-- Top Logo Emblem -->
                    <div class="sidebar-logo">
                        <svg class="logo-emblem" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Outer Red Shield -->
                            <path d="M60 4 L110 24 L110 74 C110 98 60 116 60 116 C60 116 10 98 10 74 L10 24 Z" fill="#800020" stroke="#500014" stroke-width="4" />
                            <!-- Inner Black Shield Cut -->
                            <path d="M60 16 L98 32 L98 70 C98 88 60 104 60 104 C60 104 22 88 22 70 L22 32 Z" fill="#18181b" />
                            <!-- Geometric Monogram (UIN / IB) -->
                            <path d="M36 38 L48 38 L48 68 C48 74 54 78 60 78 C66 78 72 74 72 68 L72 38 L84 38 L84 68 C84 82 72 90 60 90 C48 90 36 82 36 68 Z" fill="#ffffff" />
                            <polygon points="60,26 68,38 52,38" fill="#eab308" />
                        </svg>
                    </div>

                    <!-- Navigation Items List -->
                    <nav class="sidebar-nav">
                        <!-- 1. Home -->
                        <a href="{{ route('dashboard') }}" class="nav-item">
                            <span class="nav-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="#1a1a1a">
                                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                </svg>
                            </span>
                            <span class="nav-text">Home</span>
                        </a>

                        <!-- 2. Pendaftaran -->
                        <a href="javascript:void(0)" class="nav-item" onclick="openInfoModal('Pendaftaran Asrama', 'Status pendaftaran Anda telah disetujui. Anda terdaftar sebagai mahasiswa aktif Ma\'had Al-Jami\'ah.')">
                            <span class="nav-icon">
                                <svg width="32" height="32" viewBox="0 0 36 36" fill="none">
                                    <circle cx="18" cy="18" r="16" fill="#ffffff" stroke="#1a1a1a" stroke-width="2.2" />
                                    <rect x="12" y="10" width="12" height="15" rx="1.5" stroke="#1a1a1a" stroke-width="2" />
                                    <line x1="15" y1="14" x2="21" y2="14" stroke="#1a1a1a" stroke-width="1.8" stroke-linecap="round" />
                                    <line x1="15" y1="18" x2="21" y2="18" stroke="#1a1a1a" stroke-width="1.8" stroke-linecap="round" />
                                    <line x1="15" y1="22" x2="19" y2="22" stroke="#1a1a1a" stroke-width="1.8" stroke-linecap="round" />
                                    <!-- Pencil -->
                                    <path d="M22 23 L26 19 L28 21 L24 25 Z" fill="#1a1a1a" />
                                </svg>
                            </span>
                            <span class="nav-text">Pendaftaran</span>
                        </a>

                        <!-- 3. Kamar -->
                        <a href="{{ route('kamar') }}" class="nav-item">
                            <span class="nav-icon">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                    <!-- Bed frame -->
                                    <rect x="3" y="16" width="17" height="10" rx="1.5" stroke="#1a1a1a" stroke-width="2" />
                                    <!-- Pillow -->
                                    <rect x="5" y="18" width="5" height="4" rx="1" fill="#1a1a1a" />
                                    <!-- Bed headboard -->
                                    <line x1="3" y1="12" x2="3" y2="26" stroke="#1a1a1a" stroke-width="2.5" stroke-linecap="round" />
                                    <!-- Nightstand with lamp -->
                                    <rect x="22" y="18" width="7" height="8" rx="1" stroke="#1a1a1a" stroke-width="1.8" />
                                    <line x1="22" y1="22" x2="29" y2="22" stroke="#1a1a1a" stroke-width="1.5" />
                                    <!-- Lamp -->
                                    <polygon points="23.5,15 27.5,15 28.5,12 22.5,12" fill="#1a1a1a" />
                                    <line x1="25.5" y1="15" x2="25.5" y2="18" stroke="#1a1a1a" stroke-width="1.5" />
                                </svg>
                            </span>
                            <span class="nav-text">Kamar</span>
                        </a>

                        <!-- 4. Pembayaran -->
                        <a href="javascript:void(0)" class="nav-item" onclick="openInfoModal('Status Pembayaran', 'Status: <strong>Pembayaran Belum Lunas</strong>.<br>Tagihan: Uang Asrama & Program Pembinaan Semester Ini.<br>Batas Pembayaran: 15 Oktober 2026.')">
                            <span class="nav-icon">
                                <span class="icon-circle-blue">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="2" y="5" width="20" height="14" rx="2" />
                                        <line x1="2" y1="10" x2="22" y2="10" />
                                    </svg>
                                </span>
                            </span>
                            <span class="nav-text">Pembayaran</span>
                        </a>

                        <!-- 5. Pembalajaran (sesuai teks referensi) -->
                        <a href="javascript:void(0)" class="nav-item" onclick="openInfoModal('Pembalajaran Asrama', 'Program Taklim Ma\'had:<br>• Bahasa Arab (Senin - Rabu, 08:00 - 10:00)<br>• Tahsin & Tahfidz Al-Qur\'an (Ba\'da Subuh)<br>• Kajian Fiqih & Akhlak (Ba\'da Maghrib)')">
                            <span class="nav-icon">
                                <span class="icon-circle-cyan">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </span>
                            </span>
                            <span class="nav-text">Pembalajaran</span>
                        </a>

                        <!-- 6. Perizinan -->
                        <a href="javascript:void(0)" class="nav-item" onclick="openInfoModal('Perizinan Keluar Asrama', 'Ajukan perizinan keluar malam, izin pulang kampung (weekend), atau dispensasi kegiatan kampus melalui bagian musyrif/musyrifah.')">
                            <span class="nav-icon">
                                <svg width="30" height="30" fill="none" stroke="#1a1a1a" stroke-width="2.2" viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <polyline points="3 7 12 13 21 7" />
                                </svg>
                            </span>
                            <span class="nav-text">Perizinan</span>
                        </a>

                        <!-- 7. Profil -->
                        <a href="javascript:void(0)" class="nav-item" onclick="openInfoModal('Profil Mahasiswa', 'Nama: {{ Auth::user()->name }}<br>NIM: {{ Auth::user()->nim ?? '-' }}<br>Email: {{ Auth::user()->email }}<br>Status: Mahasiswa Aktif')">
                            <span class="nav-icon">
                                <span class="icon-circle-avatar">
                                    <svg width="24" height="24" fill="#1a1a1a" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 4c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm0 14c-2.03 0-4.43-.82-6.14-2.88C7.55 15.8 9.68 15 12 15s4.45.8 6.14 2.12C16.43 19.18 14.03 20 12 20z" />
                                    </svg>
                                </span>
                            </span>
                            <span class="nav-text">Profil</span>
                        </a>

                        <!-- 8. Pengaturan -->
                        <a href="javascript:void(0)" class="nav-item" onclick="openInfoModal('Pengaturan Akun', 'Ubah kata sandi, pengaturan notifikasi pengumuman asrama, dan sinkronisasi kontak darurat wali mahasiswa.')">
                            <span class="nav-icon">
                                <svg width="28" height="28" fill="#1a1a1a" viewBox="0 0 24 24">
                                    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                                </svg>
                            </span>
                            <span class="nav-text">Pengaturan</span>
                        </a>
                    </nav>
                </div>

                <!-- Keluar (Logout) -->
                <div class="sidebar-footer">
                    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                        @csrf
                        <button type="submit" class="btn-keluar">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT ================= -->
        <main class="main-content">
            <!-- Header Welcome matching visual: SELAMAT DATANG, (NAMA MAHASISWA) -->
            <h1 class="welcome-title">
                SELAMAT DATANG, {{ strtoupper(Auth::user()->name ?? '(NAMA MAHASISWA)') }}
            </h1>

            <!-- 3 Maroon Status Cards Row -->
            <section class="cards-row">
                <!-- Card 1: Pendaftaran Disetujui -->
                <div class="status-card" onclick="openInfoModal('Pendaftaran Asrama', 'Pendaftaran Anda telah diverifikasi dan <strong>Disetujui</strong> oleh pihak administrasi asrama.')">
                    <div class="card-inner-text">
                        <div>Pendaftaran</div>
                        <div>Disetujui</div>
                    </div>
                </div>

                <!-- Card 2: Kamar A-102 -->
                <div class="status-card" onclick="window.location.href='{{ route('kamar') }}'">
                    <div class="card-inner-text">
                        <div>Kamar A-102</div>
                    </div>
                </div>

                <!-- Card 3: Pembayaran Belum Lunas -->
                <div class="status-card" onclick="openInfoModal('Pembayaran Asrama', 'Status: <strong>Pembayaran Belum Lunas</strong>.<br>Silakan lakukan pelunasan sebelum tanggal 15 bulan ini melalui nomor Virtual Account yang telah dikirimkan.')">
                    <div class="card-inner-text">
                        <div>Pembayaran Belum Lunas</div>
                    </div>
                </div>
            </section>

            <!-- Jadwal Hari Ini Section -->
            <section class="schedule-container">
                <h2 class="schedule-heading">Jadwal Hari Ini</h2>

                <div class="schedule-card">
                    <div class="schedule-item time">08:00-10:00</div>
                    <div class="schedule-item subject">Bahasa Arab</div>
                    <div class="schedule-item room">Kelas-A</div>
                </div>
            </section>
        </main>
    </div>

    <!-- Interactive Info Modal -->
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
