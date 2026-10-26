
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pembayaran - SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --maroon: #79051d;
            --maroon-dark: #5f0016;
            --text: #171717;
            --muted: #737373;
            --page: #f8f7f6;
            --soft-maroon: #f9ecef;
            --line: #8a0b22;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            color: var(--text);
            background: var(--page);
            min-height: 100vh;
            overflow-x: hidden;
        }

        button, input { font: inherit; }

        .top-header {
            height: 88px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px 0 20px;
            background: #fff;
            border: 1px solid var(--line);
            position: relative;
            z-index: 20;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--maroon);
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -.7px;
        }

        .brand img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 26px;
        }

        .notification-button {
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 50%;
            background: var(--maroon);
            color: white;
            display: grid;
            place-items: center;
            cursor: pointer;
        }

        .profile-button {
            width: 60px;
            height: 60px;
            border: 1.5px solid #333;
            border-radius: 50%;
            background: #fff;
            color: #444;
            display: grid;
            place-items: center;
        }

        .app-content {
            display: flex;
            min-height: calc(100vh - 88px);
        }

        /* SIDEBAR */
        .sidebar {
            width: 300px;
            flex: 0 0 300px;
            min-height: calc(100vh - 88px);
            padding: 22px 14px 24px;
            position: relative;
            overflow: hidden;
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            background: #dbeafe;
        }

        .sidebar-bg {
            position: absolute;
            inset: 0;
            z-index: 0;
            background-image: url('{{ asset("images/mahad_al_jamiah.jpeg") }}');
            background-position: center top;
            background-size: cover;
            background-repeat: no-repeat;
        }

        .sidebar-overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(
                180deg,
                rgba(219,234,254,.45),
                rgba(255,255,255,.72) 48%,
                rgba(255,255,255,.88)
            );
        }

        .sidebar-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex: 1;
            flex-direction: column;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .nav-item {
            min-height: 48px;
            padding: 9px 12px;
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
            color: #171717;
            background: #fff;
            border: 1px solid #222;
            border-radius: 12px;
            transition: background .18s ease, border-color .18s ease;
        }

        .nav-item:hover {
            background: #fff4f5;
            border-color: var(--maroon);
        }

        .nav-item.active {
            background: #f9ecef;
            border-color: var(--maroon);
            color: var(--maroon);
            font-weight: 700;
        }

        .nav-icon {
            width: 24px;
            height: 24px;
            flex: 0 0 24px;
            display: grid;
            place-items: center;
            color: #5c6677;
        }

        .nav-item.active .nav-icon {
            color: var(--maroon);
        }

        .nav-text {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.35;
        }

        .nav-item.active .nav-text {
            font-weight: 700;
        }

        .nav-arrow {
            margin-left: auto;
            color: var(--maroon);
            font-size: 23px;
            font-weight: 800;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 24px;
        }

        .btn-keluar {
            width: 100%;
            min-height: 48px;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            background: #fff;
            color: #171717;
            border: 1px solid #222;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-keluar:hover {
            background: #fff4f5;
            color: var(--maroon);
            border-color: var(--maroon);
        }

        /* KONTEN PEMBAYARAN */
        .main-content {
            flex: 1;
            min-width: 0;
            padding: 29px 42px 34px;
        }

        .breadcrumb {
            font-size: 20px;
            font-weight: 600;
            color: var(--maroon);
            margin-bottom: 12px;
        }

        .page-title {
            font-size: 32px;
            line-height: 1.25;
            font-weight: 800;
            margin-bottom: 3px;
            color: #111;
        }

        .page-description {
            font-size: 19px;
            line-height: 1.5;
            margin-bottom: 28px;
        }

        .payment-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .summary-card {
            min-height: 98px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            padding: 13px 14px;
        }

        .summary-card:first-child {
            background: #fff0f2;
            color: var(--maroon);
        }

        .summary-label {
            color: #737373;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .summary-card:first-child .summary-label {
            color: var(--maroon);
            font-weight: 600;
        }

        .summary-value {
            font-size: 25px;
            line-height: 1.3;
            font-weight: 700;
        }

        .summary-icon {
            display: inline-block;
            margin-right: 6px;
            font-weight: 800;
        }

        .section-title {
            font-size: 19px;
            font-weight: 600;
            color: #414141;
            margin: 25px 5px 16px;
        }

        .payment-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 20px 16px;
            margin-bottom: 16px;
            background: #fff;
            border-radius: 12px;
        }

        .card-left { min-width: 0; }

        .bill-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .bill-description {
            font-size: 15px;
            color: #777;
            margin-bottom: 12px;
        }

        .bill-row {
            font-size: 15px;
            color: #777;
            margin-bottom: 8px;
        }

        .bill-row:last-child { margin-bottom: 0; }

        .card-right {
            flex: 0 0 auto;
            text-align: right;
        }

        .amount {
            font-size: 18px;
            font-weight: 700;
            color: #222;
            margin-bottom: 12px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 20px;
            padding: 5px 10px;
            background: #ffe6d9;
            color: #e65b13;
            font-size: 13px;
            font-weight: 700;
        }

        .status-pill.paid {
            background: #dcfce7;
            color: #15803d;
        }

        .btn-bayar {
            display: inline-block;
            margin-top: 12px;
            padding: 10px 18px;
            border: 0;
            border-radius: 8px;
            background: var(--maroon);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-bayar:hover { background: var(--maroon-dark); }

        .history-card {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 16px;
            background: #fff;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .history-icon {
            width: 50px;
            height: 50px;
            flex: 0 0 50px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: #f9ecef;
            color: var(--maroon);
        }

        .history-content {
            flex: 1;
            min-width: 0;
        }

        .history-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .history-detail {
            color: #777;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .history-arrow {
            font-size: 28px;
            color: #777;
        }

        .history-note {
            border-top: 1px solid #e5dedc;
            padding-top: 22px;
            margin-top: 24px;
            color: #777;
            font-size: 13px;
            line-height: 1.6;
        }

        .information-box {
            background: #f8ecef;
            color: #505050;
            border-radius: 12px;
            padding: 18px 16px;
            line-height: 1.6;
            margin-top: 14px;
        }

        .information-box h3 {
            color: var(--maroon);
            font-size: 18px;
            margin-bottom: 7px;
        }

        .alert-status {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 12px 16px;
            border-radius: 8px;
            color: #065f46;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .simulation-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px dashed #ddd;
            margin-top: 16px;
        }

        .btn-reset-simulasi {
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #fff;
            padding: 8px 12px;
            cursor: pointer;
            color: #64748b;
        }

        /* MODAL PEMBAYARAN DAN INFORMASI */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0,0,0,.55);
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-backdrop.active { display: flex; }

        .payment-modal-box {
            width: 100%;
            max-width: 580px;
            max-height: 90vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,.25);
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header-title {
            font-size: 20px;
            font-weight: 800;
            color: #111;
        }

        .modal-header-close {
            border: 0;
            background: transparent;
            font-size: 28px;
            color: #777;
            cursor: pointer;
        }

        .modal-body-content { padding: 24px; }

        .order-summary-box {
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 22px;
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            font-size: 14px;
            color: #475569;
            margin-bottom: 12px;
        }

        .order-row:last-child {
            border-top: 1px dashed #cbd5e1;
            padding-top: 12px;
            margin-bottom: 0;
            font-weight: 700;
            color: #111;
        }

        .order-total-amount {
            color: var(--maroon);
            font-size: 17px;
        }

        .methods-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .payment-methods-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .method-choice {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 13px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            gap: 10px;
        }

        .method-choice.selected {
            border-color: var(--maroon);
            background: #fdf2f4;
        }

        .method-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .method-radio { accent-color: var(--maroon); }
        .method-name { font-weight: 700; font-size: 14px; }

        .method-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .method-badge {
            color: var(--maroon);
            background: #f9e7eb;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 7px;
            border-radius: 5px;
        }

        .va-display-box {
            background: #faf5f7;
            border: 1px solid #f2d2dc;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 18px;
            text-align: center;
        }

        .va-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .va-number-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .va-number {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: .04em;
            color: var(--maroon);
        }

        .btn-copy {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #fff;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-copy.copied {
            background: #10b981;
            color: #fff;
        }

        .va-account-name {
            color: #64748b;
            font-size: 12px;
        }

        .instructions-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 14px 16px;
            color: #475569;
            font-size: 13px;
            line-height: 1.65;
        }

        .instructions-box ol {
            padding-left: 20px;
            margin-top: 6px;
        }

        .modal-footer {
            padding: 16px 24px 22px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #eee;
        }

        .btn-modal-cancel,
        .btn-modal-confirm {
            border: 0;
            border-radius: 8px;
            padding: 11px 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-modal-cancel {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-modal-confirm {
            background: var(--maroon);
            color: #fff;
        }

        .mobile-nav-toggle { display: none; }
        .sidebar-backdrop { display: none; }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .sidebar {
                width: 260px;
                flex-basis: 260px;
            }

            .main-content { padding: 25px 24px; }
            .payment-summary { gap: 12px; }
            .summary-value { font-size: 21px; }
        }

        @media (max-width: 800px) {
            .top-header {
                height: 74px;
                padding: 0 14px;
            }

            .brand {
                font-size: 22px;
                gap: 8px;
            }

            .brand img {
                width: 46px;
                height: 46px;
            }

            .header-actions { gap: 12px; }

            .profile-button {
                width: 44px;
                height: 44px;
            }

            .app-content { min-height: calc(100vh - 74px); }

            .sidebar {
                position: fixed;
                top: 74px;
                bottom: 0;
                left: 0;
                height: calc(100vh - 74px);
                min-height: 0;
                z-index: 40;
                transform: translateX(-105%);
                transition: transform .25s ease;
                overflow-y: auto;
                width: 280px;
                flex-basis: 280px;
            }

            .sidebar.active { transform: translateX(0); }

            .sidebar-backdrop.active {
                display: block;
                position: fixed;
                inset: 74px 0 0;
                z-index: 35;
                background: rgba(0,0,0,.35);
            }

            .mobile-nav-toggle {
                display: grid;
                place-items: center;
                position: fixed;
                z-index: 50;
                left: 12px;
                bottom: 14px;
                width: 46px;
                height: 46px;
                border: 0;
                border-radius: 50%;
                background: var(--maroon);
                color: white;
                box-shadow: 0 3px 12px #0003;
                cursor: pointer;
            }

            .main-content {
                padding: 24px 16px 80px;
                width: 100%;
            }

            .breadcrumb { font-size: 16px; }
            .page-title { font-size: 28px; }

            .page-description {
                font-size: 15px;
                margin-bottom: 20px;
            }

            .payment-summary { grid-template-columns: 1fr; }

            .payment-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .card-right { text-align: left; }
        }

        @media (max-width: 480px) {
            .brand { font-size: 19px; }
            .notification-button { width: 36px; height: 36px; }
            .profile-button { width: 40px; height: 40px; }
            .page-title { font-size: 25px; }

            .history-card { gap: 10px; }
            .history-icon {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
            }

            .modal-header,
            .modal-body-content {
                padding: 16px;
            }

            .modal-footer {
                padding: 14px 16px 18px;
                flex-wrap: wrap;
            }

            .va-number { font-size: 17px; }
            .simulation-bar { flex-wrap: wrap; }
        }
    </style>
</head>

<body>
    <header class="top-header">
        <div class="brand">
            <img src="{{ asset('images/logo_uin.png') }}" alt="Logo UIN Imam Bonjol">
            <span>SIMAHAD</span>
        </div>

        <div class="header-actions">
            <button class="notification-button" type="button"
                    aria-label="Notifikasi" title="Notifikasi">
                <svg width="25" height="25" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>
            </button>

            <button class="profile-button" type="button"
                    aria-label="Profil pengguna"
                    title="{{ Auth::user()->name }}">
                <svg width="42" height="42" viewBox="0 0 24 24" fill="#444">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4 21a8 8 0 0 1 16 0z"></path>
                </svg>
            </button>
        </div>
    </header>

    <button type="button" class="mobile-nav-toggle" id="mobileMenuBtn"
            aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
        <svg width="24" height="24" fill="none" stroke="currentColor"
             stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="app-content">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-bg"></div>
            <div class="sidebar-overlay"></div>

            <div class="sidebar-content">
                <nav class="sidebar-nav" aria-label="Navigasi utama">

                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                            </svg>
                        </span>
                        <span class="nav-text">Dashboard</span>
                    </a>

                    <!-- Pendaftaran -->
                    <a href="{{ route('pendaftaran.pribadi') }}"
                       class="nav-item {{ request()->routeIs('pendaftaran.*') ? 'active' : '' }}"
                       @if(request()->routeIs('pendaftaran.*')) aria-current="page" @endif>
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="10" cy="7" r="4"></circle>
                                <path d="M19 8v6M22 11h-6"></path>
                            </svg>
                        </span>
                        <span class="nav-text">Pendaftaran</span>
                    </a>

                    <!-- Pembelajaran -->
                    <a href="{{ route('pembelajaran.mhs') }}"
                       class="nav-item {{ request()->routeIs('pembelajaran.*') ? 'active' : '' }}"
                       @if(request()->routeIs('pembelajaran.*')) aria-current="page" @endif>
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 7v14"></path>
                                <path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H12v18H5.5A2.5 2.5 0 0 1 3 18.5z"></path>
                                <path d="M21 5.5A2.5 2.5 0 0 0 18.5 3H12v18h6.5a2.5 2.5 0 0 0 2.5-2.5z"></path>
                            </svg>
                        </span>
                        <span class="nav-text">Pembelajaran</span>
                    </a>

                    <!-- Informasi Kamar -->
                    <a href="{{ route('kamar') }}"
                       class="nav-item {{ request()->routeIs('kamar') ? 'active' : '' }}"
                       @if(request()->routeIs('kamar')) aria-current="page" @endif>
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 18v-5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5"></path>
                                <path d="M3 18v3M21 18v3M3 15h18"></path>
                                <path d="M5 11V7a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v4"></path>
                                <path d="M12 11V9a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </span>
                        <span class="nav-text">Informasi Kamar</span>
                    </a>

                    <!-- Pembayaran -->
                    <a href="{{ route('pembayaran') }}"
                       class="nav-item {{ request()->routeIs('pembayaran') || request()->routeIs('pembayaran.*') ? 'active' : '' }}"
                       @if(request()->routeIs('pembayaran') || request()->routeIs('pembayaran.*')) aria-current="page" @endif>
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <path d="M8 7h8M8 11h5M8 15h5"></path>
                            </svg>
                        </span>
                        <span class="nav-text">Pembayaran</span>
                        <span class="nav-arrow">&rsaquo;</span>
                    </a>

                    <!-- Absensi -->
                    <a href="{{ route('absensi.mhs') }}"
                       class="nav-item {{ request()->routeIs('absensi.mhs') ? 'active' : '' }}"
                       @if(request()->routeIs('absensi.mhs')) aria-current="page" @endif>
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                <path d="M8 3v4M16 3v4M8 13l2.5 2.5L16 10"></path>
                            </svg>
                        </span>
                        <span class="nav-text">Absensi</span>
                    </a>

                    <!-- Surat dan Perizinan: modal informasi sementara -->
                    <a href="#"
                       class="nav-item"
                       onclick="openInfoModal('Surat dan Perizinan', 'Fitur pengajuan surat dan perizinan Ma’had akan ditampilkan di bagian ini.'); return false;">
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                                <path d="M14 3v5h5M8 12h8M8 16h6"></path>
                            </svg>
                        </span>
                        <span class="nav-text">Surat dan Perizinan</span>
                    </a>

                    <!-- Pengumuman -->
                    <a href="{{ route('pengumuman.mhs') }}"
                       class="nav-item {{ request()->routeIs('pengumuman.mhs') ? 'active' : '' }}"
                       @if(request()->routeIs('pengumuman.mhs')) aria-current="page" @endif>
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 11v2a2 2 0 0 0 2 2h2l11 4V5L7 9H5a2 2 0 0 0-2 2z"></path>
                                <path d="M7 15l2 5h4l-2-6"></path>
                                <path d="M21 9l2-2M21 15l2 2"></path>
                            </svg>
                        </span>
                        <span class="nav-text">Pengumuman</span>
                    </a>

                    <!-- Pengaturan Akun: modal informasi sementara -->
                    <a href="#"
                       class="nav-item"
                       onclick="openInfoModal('Pengaturan Akun', 'Halaman pengaturan akun belum tersedia. Silakan gunakan fitur profil yang tersedia.'); return false;">
                        <span class="nav-icon">
                            <svg width="23" height="23" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 3.1-.2-.1a1.7 1.7 0 0 0-1.8-.1 1.7 1.7 0 0 0-1 1.5v.2h-3.6v-.2a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.1l-.2.1-1.8-3.1.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H5v-3.6h.2a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 1.8-3.1.2.1a1.7 1.7 0 0 0 1.8.1 1.7 1.7 0 0 0 1-1.5v-.2h3.6v.2a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.1l.2-.1 1.8 3.1-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.5 1h.2V14h-.2a1.7 1.7 0 0 0-1.5 1z"></path>
                            </svg>
                        </span>
                        <span class="nav-text">Pengaturan Akun</span>
                    </a>
                </nav>

                <div class="sidebar-footer">
                    <form action="{{ route('logout') }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                        @csrf
                        <button type="submit" class="btn-keluar">
                            <span class="nav-icon">
                                <svg width="23" height="23" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10 17l5-5-5-5M15 12H3"></path>
                                    <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"></path>
                                </svg>
                            </span>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="breadcrumb">Dashboard / Pembayaran</div>
            <h1 class="page-title">Pembayaran</h1>
            <p class="page-description">
                Pantau informasi biaya dan status pembayaran Ma’had kamu.
            </p>

            @if (session('status'))
                <div class="alert-status">{{ session('status') }}</div>
            @endif

            <section class="payment-summary" aria-label="Ringkasan pembayaran">
                <div class="summary-card">
                    <div class="summary-label">Total Biaya Ma’had per Tahun</div>
                    <div class="summary-value">Rp5.000.000</div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">
                        <span class="summary-icon" style="color:#258653;">✓</span>
                        Total Terkonfirmasi
                    </div>
                    <div class="summary-value">Rp—</div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">
                        <span class="summary-icon" style="color:#c88718;">◷</span>
                        Belum Terkonfirmasi
                    </div>
                    <div class="summary-value">Rp—</div>
                </div>
            </section>

            <h2 class="section-title">Status Pembayaran</h2>

            <section class="payment-card">
                <div class="card-left">
                    <div class="bill-name">
                        {{ $tagihan['nama'] ?? 'Pembayaran Awal' }}
                    </div>

                    <div class="bill-description">Pembayaran saat pendaftaran</div>

                    <div class="bill-row">
                        Periode: {{ $tagihan['periode'] ?? '—' }}
                    </div>

                    <div class="bill-row">
                        Nominal: Rp{{ $tagihan['nominal'] ?? '1.000.000' }}
                    </div>

                    @if (($status ?? '') !== 'lunas')
                        <button type="button" class="btn-bayar"
                                onclick="openPaymentModal()">
                            Bayar Sekarang
                        </button>
                    @endif
                </div>

                <div class="card-right">
                    <div class="amount">
                        Rp{{ $tagihan['nominal'] ?? '1.000.000' }}
                    </div>

                    <span class="status-pill {{ ($status ?? '') === 'lunas' ? 'paid' : '' }}">
                        {{ ($status ?? '') === 'lunas' ? 'Lunas' : 'Menunggu Konfirmasi' }}
                    </span>
                </div>
            </section>

            <h2 class="section-title">Riwayat Pembayaran</h2>

            <section class="history-card">
                <div class="history-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                        <path d="M9 8h6M9 12h6M9 16h3"></path>
                    </svg>
                </div>

                <div class="history-content">
                    <div class="history-title">Pembayaran Awal Pendaftaran</div>

                    <div class="history-detail">
                        Nominal: Rp{{ $tagihan['nominal'] ?? '1.000.000' }}
                    </div>

                    <div class="history-detail">
                        Bukti transfer:
                        {{ ($status ?? '') === 'lunas' ? 'Sudah dikonfirmasi' : 'Menunggu konfirmasi' }}
                    </div>

                    <span class="status-pill {{ ($status ?? '') === 'lunas' ? 'paid' : '' }}">
                        {{ ($status ?? '') === 'lunas' ? 'Terkonfirmasi' : 'Menunggu Konfirmasi' }}
                    </span>

                    @if (!empty($riwayat))
                        @foreach ($riwayat as $item)
                            <div class="history-note">
                                <strong>{{ $item['periode'] ?? 'Riwayat pembayaran' }}</strong><br>
                                Nominal: Rp{{ $item['nominal'] ?? '—' }}<br>
                                Status: {{ $item['status'] ?? '—' }}
                            </div>
                        @endforeach
                    @endif

                    <div class="history-note">
                        Riwayat dan status pembayaran diperbarui berdasarkan data
                        yang tersedia dari pihak kampus.
                    </div>
                </div>

                <div class="history-arrow">&rsaquo;</div>
            </section>

            @if (($status ?? '') === 'lunas')
                <div class="simulation-bar">
                    <span style="font-size:13px;color:#64748b;">
                        Mode Simulasi Aktif:
                    </span>

                    <form action="{{ route('pembayaran.reset') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-reset-simulasi">
                            ↺ Reset ke "Belum lunas"
                        </button>
                    </form>
                </div>
            @endif

            <section class="information-box">
                <h3>ⓘ &nbsp;Informasi</h3>
                <p>
                    SIMAHAD hanya menampilkan informasi pembayaran. Transaksi
                    dan konfirmasi pembayaran ditangani oleh pihak kampus/rektorat.
                </p>
            </section>
        </main>
    </div>

    <!-- MODAL PEMBAYARAN -->
    <div class="modal-backdrop" id="paymentModal"
         onclick="closePaymentModalOnBackdrop(event)">
        <div class="payment-modal-box" role="dialog" aria-modal="true"
             aria-labelledby="paymentModalTitle"
             onclick="event.stopPropagation()">

            <div class="modal-header">
                <div class="modal-header-title" id="paymentModalTitle">
                    Pembayaran Ma’had
                </div>

                <button type="button" class="modal-header-close"
                        onclick="closePaymentModal()" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <div class="modal-body-content">
                <div class="order-summary-box">
                    <div class="order-row">
                        <span>Jenis Tagihan</span>
                        <strong>Pembayaran Awal Pendaftaran</strong>
                    </div>

                    <div class="order-row">
                        <span>Periode Tagihan</span>
                        <strong>{{ $tagihan['periode'] ?? '—' }}</strong>
                    </div>

                    <div class="order-row">
                        <span>Nama Mahasiswa</span>
                        <strong>{{ Auth::user()->name }}</strong>
                    </div>

                    <div class="order-row">
                        <span>Total Pembayaran</span>
                        <span class="order-total-amount">
                            Rp{{ $tagihan['nominal'] ?? '1.000.000' }}
                        </span>
                    </div>
                </div>

                <div class="methods-title">Pilih Metode Pembayaran</div>

                <div class="payment-methods-grid">
                    <label class="method-choice selected"
                           onclick="selectPaymentMethod(this, '900 2317 0201 3401', 'Bank Syariah Indonesia (BSI)')">
                        <div class="method-left">
                            <input type="radio" name="payment_method_radio"
                                   class="method-radio" value="BSI" checked>
                            <div>
                                <div class="method-name">BSI Virtual Account</div>
                                <div class="method-sub">Bank Syariah Indonesia</div>
                            </div>
                        </div>
                        <span class="method-badge">Virtual Account</span>
                    </label>

                    <label class="method-choice"
                           onclick="selectPaymentMethod(this, '88708 2317020134', 'Bank Mandiri')">
                        <div class="method-left">
                            <input type="radio" name="payment_method_radio"
                                   class="method-radio" value="Mandiri">
                            <div>
                                <div class="method-name">Mandiri Virtual Account</div>
                                <div class="method-sub">Bank Mandiri</div>
                            </div>
                        </div>
                    </label>

                    <label class="method-choice"
                           onclick="selectPaymentMethod(this, '12890 2317020134', 'Bank BRI')">
                        <div class="method-left">
                            <input type="radio" name="payment_method_radio"
                                   class="method-radio" value="BRI">
                            <div>
                                <div class="method-name">BRIVA</div>
                                <div class="method-sub">Bank Rakyat Indonesia</div>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="va-display-box">
                    <div class="va-label" id="vaBankLabel">
                        Nomor Virtual Account Bank Syariah Indonesia (BSI):
                    </div>

                    <div class="va-number-row">
                        <span class="va-number" id="vaNumberText">
                            900 2317 0201 3401
                        </span>

                        <button type="button" class="btn-copy" id="btnCopyVA"
                                onclick="copyVirtualAccount()">
                            <span id="copyBtnText">Salin</span>
                        </button>
                    </div>

                    <div class="va-account-name">
                        Atas Nama:
                        <strong>MAHAD UIN IB - {{ strtoupper(Auth::user()->name) }}</strong>
                    </div>
                </div>

                <div class="instructions-box">
                    <strong>Petunjuk Pembayaran:</strong>
                    <ol>
                        <li>Buka aplikasi mobile banking sesuai metode yang dipilih.</li>
                        <li>Pilih menu pembayaran atau Virtual Account.</li>
                        <li>Masukkan nomor Virtual Account yang ditampilkan.</li>
                        <li>Periksa rincian pembayaran sebelum melanjutkan.</li>
                        <li>Simpan bukti transaksi untuk keperluan konfirmasi.</li>
                    </ol>

                    <p style="margin-top:8px;">
                        Nomor Virtual Account di atas merupakan contoh tampilan/demo.
                        Pastikan nomor resmi diperoleh dari pihak kampus sebelum
                        melakukan transaksi.
                    </p>
                </div>
            </div>

            <form action="{{ route('pembayaran.bayar') }}" method="POST"
                  id="confirmPaymentForm">
                @csrf

                <input type="hidden" name="metode" id="selectedMetodeInput"
                       value="Virtual Account Bank Syariah Indonesia (BSI)">

                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel"
                            onclick="closePaymentModal()">
                        Tutup
                    </button>

                    <button type="submit" class="btn-modal-confirm">
                        Konfirmasi Sudah Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL INFORMASI MENU YANG BELUM MEMILIKI HALAMAN -->
    <div class="modal-backdrop" id="infoModal"
         onclick="closeInfoModalOnBackdrop(event)">
        <div class="payment-modal-box" role="dialog" aria-modal="true"
             aria-labelledby="infoModalTitle"
             onclick="event.stopPropagation()">

            <div class="modal-header">
                <h2 class="modal-header-title" id="infoModalTitle">Informasi</h2>

                <button type="button" class="modal-header-close"
                        onclick="closeInfoModal()" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <div class="modal-body-content" id="infoModalBody">
                Informasi menu.
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal-confirm"
                        onclick="closeInfoModal()">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        /* MODAL PEMBAYARAN */
        function openPaymentModal() {
            document.getElementById('paymentModal').classList.add('active');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('active');
        }

        function closePaymentModalOnBackdrop(e) {
            if (e.target.id === 'paymentModal') {
                closePaymentModal();
            }
        }

        /* PILIH METODE PEMBAYARAN */
        function selectPaymentMethod(element, vaNumber, bankName) {
            document.querySelectorAll('.method-choice').forEach(function (choice) {
                choice.classList.remove('selected');
            });

            element.classList.add('selected');

            const radio = element.querySelector('input[type="radio"]');

            if (radio) {
                radio.checked = true;
            }

            document.getElementById('vaBankLabel').textContent =
                'Nomor Virtual Account ' + bankName + ':';

            document.getElementById('vaNumberText').textContent = vaNumber;

            document.getElementById('selectedMetodeInput').value =
                'Virtual Account ' + bankName;
        }

        /* SALIN NOMOR VIRTUAL ACCOUNT */
        function copyVirtualAccount() {
            const value = document.getElementById('vaNumberText')
                .textContent.replace(/\s+/g, '');

            const button = document.getElementById('btnCopyVA');
            const label = document.getElementById('copyBtnText');

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(value)
                    .then(function () {
                        button.classList.add('copied');
                        label.textContent = 'Tersalin!';

                        setTimeout(function () {
                            button.classList.remove('copied');
                            label.textContent = 'Salin';
                        }, 1800);
                    })
                    .catch(function () {
                        window.prompt('Salin nomor Virtual Account:', value);
                    });
            } else {
                window.prompt('Salin nomor Virtual Account:', value);
            }
        }

        /* MODAL INFORMASI */
        function openInfoModal(title, content) {
            document.getElementById('infoModalTitle').textContent = title;

            // Gunakan textContent agar teks tidak diperlakukan sebagai HTML.
            document.getElementById('infoModalBody').textContent = content;

            document.getElementById('infoModal').classList.add('active');
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.remove('active');
        }

        function closeInfoModalOnBackdrop(e) {
            if (e.target.id === 'infoModal') {
                closeInfoModal();
            }
        }

        /* SIDEBAR RESPONSIF */
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function closeMobileSidebar() {
            if (!sidebar || !sidebarBackdrop || !mobileMenuBtn) return;

            sidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
        }

        if (mobileMenuBtn && sidebar && sidebarBackdrop) {
            mobileMenuBtn.addEventListener('click', function () {
                const isOpen = sidebar.classList.toggle('active');

                sidebarBackdrop.classList.toggle('active', isOpen);
                mobileMenuBtn.setAttribute('aria-expanded', String(isOpen));
            });

            sidebarBackdrop.addEventListener('click', closeMobileSidebar);

            // Tutup sidebar setelah memilih tautan navigasi di perangkat mobile.
            sidebar.querySelectorAll('a.nav-item').forEach(function (link) {
                link.addEventListener('click', function () {
                    closeMobileSidebar();
                });
            });

            // Tutup sidebar menggunakan tombol Escape.
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMobileSidebar();
                    closePaymentModal();
                    closeInfoModal();
                }
            });
        }
    </script>
</body>
</html>
