<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pilih Kamar - Sistem Informasi Asrama Mahasiswa</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

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
            --bg-pastel-pink: #f8eaec;
            --bg-card: #f9eaec;
            --color-green: #16a34a;
            --color-green-hover: #15803d;
            --color-orange: #f59e0b;
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

        /* Layout */
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
            padding: 0.35rem 0.6rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .nav-item:hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, 0.5);
        }

        .nav-item.active {
            background: rgba(110, 0, 27, 0.12);
            color: var(--primary-maroon);
        }

        .nav-item.active .nav-text {
            color: var(--primary-maroon);
        }

        .nav-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

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
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.01em;
        }

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
            padding: 3rem 4rem 3rem 4.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: var(--bg-main);
            overflow-y: auto;
            position: relative;
        }

        @media (max-width: 1024px) {
            .main-content {
                padding: 2rem 2rem 2.5rem 2rem;
            }
        }

        /* Filter Controls Top Row */
        .filter-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            max-width: 1050px;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
                align-items: stretch;
                gap: 1.25rem;
                margin-bottom: 2rem;
            }
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .filter-label {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.55rem;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.02em;
            white-space: nowrap;
        }

        .search-pill-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-pill {
            background-color: var(--bg-pastel-pink);
            border: none;
            border-radius: 9999px;
            height: 44px;
            padding: 0 1.25rem 0 2.6rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: #334155;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            width: 250px;
        }

        .search-pill.floor-input {
            width: 170px;
        }

        .search-pill:focus {
            box-shadow: 0 0 0 3px rgba(248, 234, 236, 0.9), 0 4px 10px rgba(0, 0, 0, 0.08);
            background-color: #f7dfe3;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            color: #2b2b2b;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 3 Large Room Cards Grid */
        .room-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
            max-width: 1050px;
            margin-bottom: 3.5rem;
        }

        @media (max-width: 960px) {
            .room-cards-grid {
                grid-template-columns: 1fr;
                gap: 1.75rem;
            }
        }

        .room-card {
            background-color: var(--bg-card);
            border-radius: 24px;
            height: 440px;
            padding: 3rem 2rem 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
            border: 3px solid transparent;
            position: relative;
        }

        .room-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        }

        .room-card.selected {
            border-color: var(--primary-maroon);
            background-color: #fae4e8;
            box-shadow: 0 12px 28px rgba(110, 0, 27, 0.18);
        }

        .room-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.01em;
        }

        .room-info-block {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .room-capacity {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.01em;
        }

        .room-status {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: -0.01em;
        }

        /* Buttons & Badges */
        .btn-pilih {
            background-color: var(--color-green);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.8rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 3px 8px rgba(22, 163, 74, 0.3);
            transition: all 0.2s ease;
        }

        .btn-pilih:hover {
            background-color: var(--color-green-hover);
            transform: scale(1.05);
        }

        .btn-pilih.btn-chosen {
            background-color: var(--primary-maroon);
            box-shadow: 0 3px 8px rgba(110, 0, 27, 0.35);
        }

        .badge-penuh {
            background-color: var(--color-orange);
            color: #ffffff;
            border-radius: 8px;
            padding: 0.5rem 1.6rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            display: inline-block;
            box-shadow: 0 3px 8px rgba(245, 158, 11, 0.3);
            cursor: not-allowed;
            user-select: none;
        }

        /* Bottom Section: Lanjut */
        .bottom-action-area {
            text-align: center;
            padding: 1.5rem 0 0.5rem 0;
            max-width: 1050px;
        }

        .btn-lanjut {
            background: transparent;
            border: 2px solid transparent;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: #cbd5e1;
            padding: 0.65rem 2.8rem;
            border-radius: 12px;
            cursor: not-allowed;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-lanjut.active {
            background-color: var(--primary-maroon);
            color: #ffffff;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(110, 0, 27, 0.3);
        }

        .btn-lanjut.active:hover {
            background-color: var(--primary-maroon-hover);
            transform: translateY(-2px);
        }

        /* Mobile sidebar toggle */
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

        /* Flash message */
        .alert-status {
            background-color: #ecfdf5;
            border-left: 4px solid #059669;
            color: #065f46;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            max-width: 1050px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Modal Dialog */
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
            border-radius: 18px;
            max-width: 460px;
            width: 100%;
            padding: 2.2rem;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);
            position: relative;
            animation: modalFadeIn 0.2s ease-out;
            text-align: center;
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
            font-size: 0.98rem;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 1.75rem;
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

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-modal-cancel {
            background-color: #f1f5f9;
            color: #475569;
            padding: 0.65rem 1.5rem;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-modal-confirm {
            background-color: var(--primary-maroon);
            color: #ffffff;
            padding: 0.65rem 1.8rem;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(110, 0, 27, 0.25);
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
            <div class="sidebar-bg"></div>
            <div class="sidebar-overlay"></div>

            <!-- Campus Watermark -->
            <div class="sidebar-watermark">
                <svg class="watermark-emblem" width="64" height="64" viewBox="0 0 100 100" fill="none">
                    <polygon points="50,4 92,25 92,75 50,96 8,75 8,25" stroke="#a16207" stroke-width="3" fill="none" />
                    <polygon points="50,14 82,30 82,70 50,86 18,70 18,30" stroke="#64748b" stroke-width="2.5" fill="none" />
                    <path d="M50 20 L50 80 M25 50 L75 50" stroke="#64748b" stroke-width="2" />
                </svg>
                <div class="watermark-text-1">ma'had al-jami'ah</div>
                <div class="watermark-text-2">UIN Imam Bonjol</div>
            </div>

            <!-- Navigation Content -->
            <div class="sidebar-content">
                <div>
                    <!-- Logo Emblem -->
                    <div class="sidebar-logo">
                        <svg class="logo-emblem" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M60 4 L110 24 L110 74 C110 98 60 116 60 116 C60 116 10 98 10 74 L10 24 Z" fill="#800020" stroke="#500014" stroke-width="4" />
                            <path d="M60 16 L98 32 L98 70 C98 88 60 104 60 104 C60 104 22 88 22 70 L22 32 Z" fill="#18181b" />
                            <path d="M36 38 L48 38 L48 68 C48 74 54 78 60 78 C66 78 72 74 72 68 L72 38 L84 38 L84 68 C84 82 72 90 60 90 C48 90 36 82 36 68 Z" fill="#ffffff" />
                            <polygon points="60,26 68,38 52,38" fill="#eab308" />
                        </svg>
                    </div>

                    <!-- Navigation Items -->
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
                        <a href="{{ route('dashboard') }}" class="nav-item">
                            <span class="nav-icon">
                                <svg width="32" height="32" viewBox="0 0 36 36" fill="none">
                                    <circle cx="18" cy="18" r="16" fill="#ffffff" stroke="#1a1a1a" stroke-width="2.2" />
                                    <rect x="12" y="10" width="12" height="15" rx="1.5" stroke="#1a1a1a" stroke-width="2" />
                                    <line x1="15" y1="14" x2="21" y2="14" stroke="#1a1a1a" stroke-width="1.8" stroke-linecap="round" />
                                    <line x1="15" y1="18" x2="21" y2="18" stroke="#1a1a1a" stroke-width="1.8" stroke-linecap="round" />
                                    <line x1="15" y1="22" x2="19" y2="22" stroke="#1a1a1a" stroke-width="1.8" stroke-linecap="round" />
                                    <path d="M22 23 L26 19 L28 21 L24 25 Z" fill="#1a1a1a" />
                                </svg>
                            </span>
                            <span class="nav-text">Pendaftaran</span>
                        </a>

                        <!-- 3. Kamar (Active) -->
                        <a href="{{ route('kamar') }}" class="nav-item active">
                            <span class="nav-icon">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                    <rect x="3" y="16" width="17" height="10" rx="1.5" stroke="#6e001b" stroke-width="2.3" />
                                    <rect x="5" y="18" width="5" height="4" rx="1" fill="#6e001b" />
                                    <line x1="3" y1="12" x2="3" y2="26" stroke="#6e001b" stroke-width="2.5" stroke-linecap="round" />
                                    <rect x="22" y="18" width="7" height="8" rx="1" stroke="#6e001b" stroke-width="2" />
                                    <line x1="22" y1="22" x2="29" y2="22" stroke="#6e001b" stroke-width="1.5" />
                                    <polygon points="23.5,15 27.5,15 28.5,12 22.5,12" fill="#6e001b" />
                                    <line x1="25.5" y1="15" x2="25.5" y2="18" stroke="#6e001b" stroke-width="1.5" />
                                </svg>
                            </span>
                            <span class="nav-text">Kamar</span>
                        </a>

                        <!-- 4. Pembayaran -->
                        <a href="{{ route('dashboard') }}" class="nav-item">
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

                        <!-- 5. Pembalajaran -->
                        <a href="{{ route('dashboard') }}" class="nav-item">
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
                        <a href="{{ route('dashboard') }}" class="nav-item">
                            <span class="nav-icon">
                                <svg width="30" height="30" fill="none" stroke="#1a1a1a" stroke-width="2.2" viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <polyline points="3 7 12 13 21 7" />
                                </svg>
                            </span>
                            <span class="nav-text">Perizinan</span>
                        </a>

                        <!-- 7. Profil -->
                        <a href="{{ route('dashboard') }}" class="nav-item">
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
                        <a href="{{ route('dashboard') }}" class="nav-item">
                            <span class="nav-icon">
                                <svg width="28" height="28" fill="#1a1a1a" viewBox="0 0 24 24">
                                    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                                </svg>
                            </span>
                            <span class="nav-text">Pengaturan</span>
                        </a>
                    </nav>
                </div>

                <!-- Keluar -->
                <div class="sidebar-footer">
                    <form action="{{ route('logout') }}" method="POST">
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
            <div>
                <!-- Flash Status -->
                @if (session('status'))
                    <div class="alert-status">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Filter Row matching the reference image: Cari Kamar & Lantai -->
                <div class="filter-row">
                    <!-- Left: Cari Kamar -->
                    <div class="filter-group">
                        <label for="searchRoom" class="filter-label">Cari Kamar</label>
                        <div class="search-pill-wrapper">
                            <span class="search-icon">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                id="searchRoom" 
                                class="search-pill" 
                                placeholder="Contoh: 101"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <!-- Right: Lantai -->
                    <div class="filter-group">
                        <label for="searchFloor" class="filter-label">Lantai</label>
                        <div class="search-pill-wrapper">
                            <span class="search-icon">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                id="searchFloor" 
                                class="search-pill floor-input" 
                                placeholder="1 atau 2"
                                autocomplete="off"
                            >
                        </div>
                    </div>
                </div>

                <!-- 3 Large Room Cards Grid matching the design -->
                <div class="room-cards-grid" id="roomCardsContainer">
                    <!-- Card 1: Kamar 101 -->
                    <div class="room-card" data-room-name="Kamar 101" data-floor="1" data-status="Tersedia">
                        <div class="room-title">Kamar 101</div>
                        <div class="room-info-block">
                            <div class="room-capacity">Kapasitas 4</div>
                            <div class="room-status">Tersedia</div>
                        </div>
                        <div>
                            <button type="button" class="btn-pilih" onclick="selectRoom('Kamar 101', '1', this)">
                                Pilih
                            </button>
                        </div>
                    </div>

                    <!-- Card 2: Kamar 102 -->
                    <div class="room-card" data-room-name="Kamar 102" data-floor="1" data-status="Penuh">
                        <div class="room-title">Kamar 102</div>
                        <div class="room-info-block">
                            <div class="room-capacity">Kapasitas 4</div>
                            <div class="room-status">Penuh</div>
                        </div>
                        <div>
                            <span class="badge-penuh">
                                Penuh
                            </span>
                        </div>
                    </div>

                    <!-- Card 3: Kamar 103 (Pastel Card matching the 3rd column in screenshot) -->
                    <div class="room-card" data-room-name="Kamar 103" data-floor="1" data-status="Tersedia">
                        <div class="room-title">Kamar 103</div>
                        <div class="room-info-block">
                            <div class="room-capacity">Kapasitas 4</div>
                            <div class="room-status">Tersedia</div>
                        </div>
                        <div>
                            <button type="button" class="btn-pilih" onclick="selectRoom('Kamar 103', '1', this)">
                                Pilih
                            </button>
                        </div>
                    </div>
                </div>

                <!-- No Results fallback -->
                <div id="noResults" style="display: none; text-align: center; padding: 3rem 1rem; color: #64748b; font-size: 1.1rem; font-weight: 600;">
                    Kamar yang Anda cari tidak ditemukan.
                </div>
            </div>

            <!-- Bottom Action: Lanjut matching the subtle 'Lanjut' text in screenshot -->
            <div class="bottom-action-area">
                <button type="button" class="btn-lanjut" id="btnLanjut" onclick="proceedSelection()">
                    Lanjut
                </button>
            </div>
        </main>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal-overlay" onclick="closeModalOnBackdrop(event)">
        <div class="modal-box">
            <button type="button" class="btn-modal-close" onclick="closeModal()">&times;</button>
            <h3 class="modal-title">Konfirmasi Pemilihan Kamar</h3>
            <p class="modal-body" id="modalText">
                Apakah Anda yakin ingin memilih kamar ini?
            </p>
            <form action="{{ route('kamar.pilih') }}" method="POST" id="confirmForm">
                @csrf
                <input type="hidden" name="room_name" id="selectedRoomInput" value="">
                <div class="modal-actions">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-modal-confirm">Ya, Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentSelectedRoom = null;

        // Select a room
        function selectRoom(roomName, floor, btnElement) {
            // Unselect previous
            document.querySelectorAll('.room-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.querySelectorAll('.btn-pilih').forEach(btn => {
                btn.classList.remove('btn-chosen');
                btn.innerText = 'Pilih';
            });

            // Mark new selection
            const card = btnElement.closest('.room-card');
            card.classList.add('selected');
            btnElement.classList.add('btn-chosen');
            btnElement.innerText = 'Dipilih ✓';

            currentSelectedRoom = {
                name: roomName,
                floor: floor
            };

            // Activate 'Lanjut' button
            const btnLanjut = document.getElementById('btnLanjut');
            btnLanjut.classList.add('active');
        }

        // Proceed selection
        function proceedSelection() {
            if (!currentSelectedRoom) {
                alert('Silakan pilih salah satu kamar yang tersedia terlebih dahulu.');
                return;
            }

            document.getElementById('modalText').innerHTML = 
                'Anda memilih <strong>' + currentSelectedRoom.name + '</strong> (Lantai ' + currentSelectedRoom.floor + ').<br>Kapasitas kamar: 4 Mahasiswa.<br><br>Lanjutkan konfirmasi pendaftaran kamar?';
            document.getElementById('selectedRoomInput').value = currentSelectedRoom.name;
            document.getElementById('confirmModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('confirmModal').classList.remove('active');
        }

        function closeModalOnBackdrop(event) {
            if (event.target === document.getElementById('confirmModal')) {
                closeModal();
            }
        }

        // Live Filter for Cari Kamar & Lantai
        const searchRoomInput = document.getElementById('searchRoom');
        const searchFloorInput = document.getElementById('searchFloor');
        const roomCards = document.querySelectorAll('.room-card');
        const noResults = document.getElementById('noResults');

        function filterRooms() {
            const queryRoom = searchRoomInput.value.toLowerCase().trim();
            const queryFloor = searchFloorInput.value.toLowerCase().trim();

            let visibleCount = 0;

            roomCards.forEach(card => {
                const roomName = card.getAttribute('data-room-name').toLowerCase();
                const floor = card.getAttribute('data-floor').toLowerCase();

                const matchRoom = !queryRoom || roomName.includes(queryRoom);
                const matchFloor = !queryFloor || floor.includes(queryFloor);

                if (matchRoom && matchFloor) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }

        searchRoomInput.addEventListener('input', filterRooms);
        searchFloorInput.addEventListener('input', filterRooms);

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
