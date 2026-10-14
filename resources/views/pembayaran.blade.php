<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pembayaran - Sistem Informasi Asrama Mahasiswa</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary-maroon: #6e001b;
            --primary-maroon-hover: #580015;
            --primary-maroon-active: #450011;
            --text-dark: #1a1a1a;
            --text-muted: #596573;
            --bg-main: #fbfbfa;
            --bg-card: #f8eaec;
            --bg-card-hover: #f5e4e7;
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

        .logo-emblem:hover {
            transform: scale(1.04);
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

        .nav-item.active .icon-circle-blue {
            background: var(--primary-maroon);
            box-shadow: 0 2px 6px rgba(110, 0, 27, 0.35);
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
            display: inline-flex;
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
            padding: 3.5rem 4.5rem 4rem 4.5rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            background-color: var(--bg-main);
            overflow-y: auto;
            position: relative;
        }

        @media (max-width: 1024px) {
            .main-content {
                padding: 2.5rem 2rem 3rem 2rem;
            }
        }

        .pembayaran-container {
            width: 100%;
            max-width: 1080px;
        }

        /* Page Title */
        .page-title {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 2.75rem;
            font-weight: 800;
            color: #111111;
            letter-spacing: -0.03em;
            line-height: 1.15;
            margin-bottom: 3.25rem;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2.2rem;
                margin-bottom: 2rem;
            }
        }

        /* Section Group */
        .section-group {
            margin-bottom: 3.5rem;
        }

        .section-label {
            font-family: 'Lora', Georgia, 'Times New Roman', serif;
            font-size: 1.1rem;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 0.95rem;
            letter-spacing: -0.01em;
        }

        /* Payment Card - Exactly matching the reference image */
        .payment-card {
            background-color: var(--bg-card);
            border-radius: 8px;
            padding: 2.25rem 3rem 2.25rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        @media (max-width: 768px) {
            .payment-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.75rem 1.5rem;
                gap: 1.25rem;
            }
        }

        .card-left {
            display: flex;
            flex-direction: column;
        }

        .bill-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.15rem;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 1.15rem;
            line-height: 1.2;
        }

        .bill-row {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.1rem;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 1.15rem;
            line-height: 1.2;
        }

        .bill-row:last-child {
            margin-bottom: 0;
        }

        /* Bayar Sekarang Button */
        .btn-bayar {
            display: inline-block;
            background-color: var(--primary-maroon);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.65rem 1.85rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 0.65rem;
            transition: background-color 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
            box-shadow: 0 2px 6px rgba(110, 0, 27, 0.2);
            text-align: center;
            text-decoration: none;
            width: fit-content;
        }

        .btn-bayar:hover {
            background-color: var(--primary-maroon-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(110, 0, 27, 0.3);
        }

        .btn-bayar:active {
            background-color: var(--primary-maroon-active);
            transform: translateY(0);
        }

        /* Right Side Status */
        .card-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-right: 5rem;
        }

        @media (max-width: 992px) {
            .card-right {
                margin-right: 2rem;
            }
        }

        @media (max-width: 768px) {
            .card-right {
                margin-right: 0;
                width: 100%;
                justify-content: flex-start;
                padding-top: 0.5rem;
                border-top: 1px dashed rgba(0, 0, 0, 0.1);
            }
        }

        .status-text {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.15rem;
            font-weight: 500;
            color: #1a1a1a;
        }

        .status-text.status-lunas {
            color: #15803d;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Alert Status / Flash notification */
        .alert-status {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            color: #065f46;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-status-content {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        /* Simulation Reset Toolbar */
        .simulation-bar {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px dashed rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
        }

        .btn-reset-simulasi {
            background: transparent;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 6px;
            padding: 0.4rem 0.85rem;
            font-size: 0.82rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
        }

        .btn-reset-simulasi:hover {
            background: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        /* ================= PAYMENT MODAL ================= */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(3px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .payment-modal-box {
            background: #ffffff;
            border-radius: 16px;
            max-width: 580px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .modal-header-close {
            background: transparent;
            border: none;
            font-size: 1.6rem;
            color: #94a3b8;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.15s ease;
        }

        .modal-header-close:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .modal-body-content {
            padding: 1.75rem;
        }

        /* Order Summary Card inside modal */
        .order-summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            color: #475569;
        }

        .order-row:last-child {
            margin-bottom: 0;
            padding-top: 0.65rem;
            border-top: 1px dashed #cbd5e1;
            font-weight: 700;
            color: #0f172a;
            font-size: 1.15rem;
        }

        .order-total-amount {
            color: var(--primary-maroon);
            font-weight: 800;
        }

        /* Payment Methods list */
        .methods-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .payment-methods-grid {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 1.5rem;
        }

        .method-choice {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .method-choice:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .method-choice.selected {
            border-color: var(--primary-maroon);
            background: #fdf2f4;
        }

        .method-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .method-radio {
            accent-color: var(--primary-maroon);
            width: 18px;
            height: 18px;
        }

        .method-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .method-badge {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary-maroon);
            background: rgba(110, 0, 27, 0.08);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }

        /* Virtual Account Info Box */
        .va-display-box {
            background: #faf5f7;
            border: 1px solid #f2d2dc;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .va-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.35rem;
        }

        .va-number-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .va-number {
            font-family: 'Plus Jakarta Sans', monospace;
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: var(--primary-maroon);
        }

        .btn-copy {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-copy:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .btn-copy.copied {
            background: #10b981;
            color: #ffffff;
            border-color: #10b981;
        }

        .va-account-name {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Instructions list */
        .instructions-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.88rem;
            color: #475569;
            line-height: 1.6;
        }

        .instructions-box ol {
            padding-left: 1.2rem;
            margin-top: 0.35rem;
        }

        .modal-footer {
            padding: 1rem 1.75rem 1.5rem;
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            border-top: 1px solid #f1f5f9;
        }

        .btn-modal-cancel {
            background: #f1f5f9;
            color: #475569;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1.25rem;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-modal-cancel:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-modal-confirm {
            background: var(--primary-maroon);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-modal-confirm:hover {
            background: var(--primary-maroon-hover);
            transform: translateY(-1px);
        }

        /* Generic Info Modal (Sidebar links) */
        .info-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(2px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .info-modal-backdrop.active {
            display: flex;
        }

        .info-modal-box {
            background: #ffffff;
            border-radius: 12px;
            max-width: 440px;
            width: 100%;
            padding: 1.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .btn-info-close {
            position: absolute;
            top: 1rem;
            right: 1.25rem;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
        }

        .info-modal-title {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            color: #0f172a;
        }

        .info-modal-body {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 1.25rem;
        }

        .btn-info-ok {
            background: var(--primary-maroon);
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 0.6rem 1.25rem;
            font-weight: 700;
            cursor: pointer;
            float: right;
        }

        /* Mobile Responsive Sidebar Toggle */
        .mobile-nav-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 50;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 8px;
            padding: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9;
        }

        @media (max-width: 900px) {
            .mobile-nav-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar-backdrop.active {
                display: block;
            }

            .main-content {
                padding-top: 5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Hamburger Button & Backdrop -->
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

            <div class="sidebar-watermark">
                <svg class="watermark-emblem" width="64" height="64" viewBox="0 0 100 100" fill="none">
                    <polygon points="50,4 92,25 92,75 50,96 8,75 8,25" stroke="#a16207" stroke-width="3" fill="none" />
                    <polygon points="50,14 82,30 82,70 50,86 18,70 18,30" stroke="#64748b" stroke-width="2.5" fill="none" />
                    <path d="M50 20 L50 80 M25 50 L75 50" stroke="#64748b" stroke-width="2" />
                </svg>
                <div class="watermark-text-1">ma'had al-jami'ah</div>
                <div class="watermark-text-2">UIN Imam Bonjol</div>
            </div>

            <div class="sidebar-content">
                <div>
                    <!-- Top Logo Emblem -->
                    <div class="sidebar-logo">
                        <svg class="logo-emblem" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M60 4 L110 24 L110 74 C110 98 60 116 60 116 C60 116 10 98 10 74 L10 24 Z" fill="#800020" stroke="#500014" stroke-width="4" />
                            <path d="M60 16 L98 32 L98 70 C98 88 60 104 60 104 C60 104 22 88 22 70 L22 32 Z" fill="#18181b" />
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
                        <a href="{{ route('pendaftaran.pribadi') }}" class="nav-item">
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

                        <!-- 3. Kamar -->
                        <a href="{{ route('kamar') }}" class="nav-item">
                            <span class="nav-icon">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                    <rect x="3" y="16" width="17" height="10" rx="1.5" stroke="#1a1a1a" stroke-width="2" />
                                    <rect x="5" y="18" width="5" height="4" rx="1" fill="#1a1a1a" />
                                    <line x1="3" y1="12" x2="3" y2="26" stroke="#1a1a1a" stroke-width="2.5" stroke-linecap="round" />
                                    <rect x="22" y="18" width="7" height="8" rx="1" stroke="#1a1a1a" stroke-width="1.8" />
                                    <line x1="22" y1="22" x2="29" y2="22" stroke="#1a1a1a" stroke-width="1.5" />
                                    <polygon points="23.5,15 27.5,15 28.5,12 22.5,12" fill="#1a1a1a" />
                                    <line x1="25.5" y1="15" x2="25.5" y2="18" stroke="#1a1a1a" stroke-width="1.5" />
                                </svg>
                            </span>
                            <span class="nav-text">Kamar</span>
                        </a>

                        <!-- 4. Pembayaran (ACTIVE) -->
                        <a href="{{ route('pembayaran') }}" class="nav-item active">
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
            <div class="pembayaran-container">
                <!-- Flash Notification Banner -->
                @if (session('status'))
                    <div class="alert-status">
                        <div class="alert-status-content">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Page Header: Pembayaran -->
                <h1 class="page-title">Pembayaran</h1>

                <!-- Section 1: Tagihan saat ini -->
                <section class="section-group">
                    <h2 class="section-label">Tagihan saat ini</h2>

                    <div class="payment-card">
                        <div class="card-left">
                            <div class="bill-name">{{ $tagihan['nama'] }}</div>
                            <div class="bill-row">Periode : {{ $tagihan['periode'] }}</div>
                            <div class="bill-row">RP: {{ $tagihan['nominal'] }}</div>
                            <div class="card-action">
                                @if ($status === 'lunas')
                                    <button type="button" class="btn-bayar" style="background-color: #15803d; cursor: default;" disabled>
                                        Sudah Dibayar
                                    </button>
                                @else
                                    <button type="button" class="btn-bayar" onclick="openPaymentModal()">
                                        Bayar Sekarang
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="card-right">
                            @if ($status === 'lunas')
                                <span class="status-text status-lunas">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Lunas
                                </span>
                            @else
                                <span class="status-text">
                                    Belum lunas
                                </span>
                            @endif
                        </div>
                    </div>
                </section>

                <!-- Section 2: Riwayat Pembayaran -->
                <section class="section-group">
                    <h2 class="section-label">Riwayat Pembayaran</h2>

                    @foreach ($riwayat as $item)
                        <div class="payment-card" style="margin-bottom: 1.25rem;">
                            <div class="card-left">
                                <div class="bill-row">Periode : {{ $item['periode'] }}</div>
                                <div class="bill-row">Nominal: {{ $item['nominal'] }}</div>
                                <div class="bill-row">Status: {{ $item['status'] }}</div>
                            </div>
                        </div>
                    @endforeach

                    @if ($status === 'lunas')
                        <div class="payment-card" style="margin-bottom: 1.25rem;">
                            <div class="card-left">
                                <div class="bill-row">Periode : {{ $tagihan['periode'] }}</div>
                                <div class="bill-row">Nominal: 2.500.000,00</div>
                                <div class="bill-row" style="color: #15803d; font-weight: 700;">Status: Lunas</div>
                            </div>
                        </div>
                    @endif
                </section>

                <!-- Simulasi Helper Toolbar (opsional jika pengguna ingin reset ke belum lunas untuk demo) -->
                @if ($status === 'lunas')
                    <div class="simulation-bar">
                        <span style="font-size: 0.85rem; color: #64748b;">Mode Simulasi Aktif:</span>
                        <form action="{{ route('pembayaran.reset') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-reset-simulasi">
                                ↺ Reset ke "Belum lunas"
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- ================= PAYMENT INSTRUCTION MODAL ================= -->
    <div class="modal-backdrop" id="paymentModal" onclick="closePaymentModalOnBackdrop(event)">
        <div class="payment-modal-box">
            <!-- Modal Header -->
            <div class="modal-header">
                <div class="modal-header-title">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary-maroon);">
                        <rect x="2" y="5" width="20" height="14" rx="2" />
                        <line x1="2" y1="10" x2="22" y2="10" />
                    </svg>
                    <span>Pembayaran Asrama</span>
                </div>
                <button type="button" class="modal-header-close" onclick="closePaymentModal()">&times;</button>
            </div>

            <!-- Modal Content -->
            <div class="modal-body-content">
                <!-- Summary Card -->
                <div class="order-summary-box">
                    <div class="order-row">
                        <span>Jenis Tagihan</span>
                        <span style="font-weight: 600;">Pembayaran Asrama Mahasiswa</span>
                    </div>
                    <div class="order-row">
                        <span>Periode Tagihan</span>
                        <span style="font-weight: 600;">Januari 2027</span>
                    </div>
                    <div class="order-row">
                        <span>Nama Mahasiswa</span>
                        <span style="font-weight: 600;">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="order-row">
                        <span>Total Pembayaran</span>
                        <span class="order-total-amount">Rp 2.500.000</span>
                    </div>
                </div>

                <!-- Payment Methods Selection -->
                <div class="methods-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    <span>Pilih Metode Pembayaran Virtual Account</span>
                </div>

                <div class="payment-methods-grid">
                    <label class="method-choice selected" onclick="selectPaymentMethod('bsi', '900 2317 0201 3401', 'Bank Syariah Indonesia (BSI)')">
                        <div class="method-left">
                            <input type="radio" name="payment_method_radio" value="BSI" class="method-radio" checked>
                            <div>
                                <div class="method-name">BSI Virtual Account</div>
                                <div style="font-size: 0.8rem; color: #64748b;">Bank Syariah Indonesia</div>
                            </div>
                        </div>
                        <span class="method-badge">Otomatis Terverifikasi</span>
                    </label>

                    <label class="method-choice" onclick="selectPaymentMethod('mandiri', '88708 2317020134', 'Bank Mandiri')">
                        <div class="method-left">
                            <input type="radio" name="payment_method_radio" value="Mandiri" class="method-radio">
                            <div>
                                <div class="method-name">Mandiri Virtual Account</div>
                                <div style="font-size: 0.8rem; color: #64748b;">Bank Mandiri</div>
                            </div>
                        </div>
                    </label>

                    <label class="method-choice" onclick="selectPaymentMethod('bri', '12890 2317020134', 'Bank BRI')">
                        <div class="method-left">
                            <input type="radio" name="payment_method_radio" value="BRI" class="method-radio">
                            <div>
                                <div class="method-name">BRIVA (BRI Virtual Account)</div>
                                <div style="font-size: 0.8rem; color: #64748b;">Bank Rakyat Indonesia</div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Dynamic Virtual Account Display -->
                <div class="va-display-box">
                    <div class="va-label" id="vaBankLabel">Nomor Virtual Account BSI:</div>
                    <div class="va-number-row">
                        <span class="va-number" id="vaNumberText">900 2317 0201 3401</span>
                        <button type="button" class="btn-copy" id="btnCopyVA" onclick="copyVirtualAccount()">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <span id="copyBtnText">Salin</span>
                        </button>
                    </div>
                    <div class="va-account-name">Atas Nama: <strong>MAHAD UIN IB - {{ strtoupper(Auth::user()->name) }}</strong></div>
                </div>

                <!-- Payment Instructions -->
                <div class="instructions-box">
                    <strong style="color: #0f172a;">Petunjuk Pembayaran Mobile Banking:</strong>
                    <ol>
                        <li>Buka aplikasi Mobile Banking pilihan Anda (misal: BSI Mobile).</li>
                        <li>Pilih menu <strong>Bayar / Pembayaran</strong> &gt; <strong>Akademik / Institusi</strong>.</li>
                        <li>Masukkan kode institusi Ma'had atau nomor Virtual Account di atas.</li>
                        <li>Periksa rincian tagihan (Nominal: <strong>Rp 2.500.000</strong>).</li>
                        <li>Masukkan PIN dan simpan bukti transaksi Anda.</li>
                    </ol>
                </div>
            </div>

            <!-- Modal Action Footer -->
            <form action="{{ route('pembayaran.bayar') }}" method="POST" id="confirmPaymentForm">
                @csrf
                <input type="hidden" name="metode" id="selectedMetodeInput" value="Virtual Account BSI">
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" onclick="closePaymentModal()">Tutup</button>
                    <button type="submit" class="btn-modal-confirm">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Konfirmasi Sudah Bayar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= GENERIC INFO MODAL (Sidebar Features) ================= -->
    <div class="info-modal-backdrop" id="infoModal" onclick="closeInfoModalOnBackdrop(event)">
        <div class="info-modal-box">
            <button type="button" class="btn-info-close" onclick="closeInfoModal()">&times;</button>
            <h3 class="info-modal-title" id="infoModalTitle">Informasi</h3>
            <div class="info-modal-body" id="infoModalBody">Konten</div>
            <button type="button" class="btn-info-ok" onclick="closeInfoModal()">Tutup</button>
        </div>
    </div>

    <script>
        // Modal Payment logic
        function openPaymentModal() {
            document.getElementById('paymentModal').classList.add('active');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('active');
        }

        function closePaymentModalOnBackdrop(e) {
            if (e.target === document.getElementById('paymentModal')) {
                closePaymentModal();
            }
        }

        function selectPaymentMethod(key, vaNumber, bankName) {
            document.getElementById('vaBankLabel').innerText = 'Nomor Virtual Account ' + bankName + ':';
            document.getElementById('vaNumberText').innerText = vaNumber;
            document.getElementById('selectedMetodeInput').value = 'Virtual Account ' + bankName;

            const choices = document.querySelectorAll('.method-choice');
            choices.forEach(c => c.classList.remove('selected'));

            const selectedChoice = event.currentTarget;
            if (selectedChoice) {
                selectedChoice.classList.add('selected');
                const radio = selectedChoice.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            }
        }

        function copyVirtualAccount() {
            const rawText = document.getElementById('vaNumberText').innerText.replace(/\s+/g, '');
            navigator.clipboard.writeText(rawText).then(() => {
                const btn = document.getElementById('btnCopyVA');
                const textSpan = document.getElementById('copyBtnText');
                btn.classList.add('copied');
                textSpan.innerText = 'Tersalin!';
                setTimeout(() => {
                    btn.classList.remove('copied');
                    textSpan.innerText = 'Salin';
                }, 2000);
            }).catch(() => {
                alert('Nomor VA: ' + rawText);
            });
        }

        // Generic Info modal for sidebar links
        function openInfoModal(title, content) {
            document.getElementById('infoModalTitle').innerHTML = title;
            document.getElementById('infoModalBody').innerHTML = content;
            document.getElementById('infoModal').classList.add('active');
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.remove('active');
        }

        function closeInfoModalOnBackdrop(e) {
            if (e.target === document.getElementById('infoModal')) {
                closeInfoModal();
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
