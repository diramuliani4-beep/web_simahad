
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

        :root {
            --maroon: #790c20;
            --border: #8a1428;
            --soft-pink: #f8edef;
            --muted: #777;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f8f8f8;
            color: #171717;
        }

        /* HEADER */
        .topbar {
            height: 89px;
            background: #fff;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 30px 0 20px;
            position: relative;
            z-index: 2;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .mobile-toggle-btn {
            display: none;
            width: 42px;
            height: 42px;
            padding: 0;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--maroon);
            cursor: pointer;
            place-items: center;
            flex-shrink: 0;
        }

        .mobile-toggle-btn svg {
            width: 24px;
            height: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--maroon);
            font-size: 32px;
            font-weight: 700;
            min-width: 0;
        }

        .brand span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
            width: 300px;
            flex: 0 0 300px;
            min-height: calc(100vh - 89px);
            padding: 22px 12px 30px;
            border: 1px solid var(--border);
            border-top: 0;
            background:
                linear-gradient(
                    rgba(222,237,248,.56),
                    rgba(255,255,255,.62)
                ),
                url("{{ asset('images/mahad_al_jamiah.jpeg') }}")
                center center / cover no-repeat;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .sidebar-close {
            display: none;
            width: 38px;
            height: 38px;
            padding: 0;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--maroon);
            cursor: pointer;
            place-items: center;
            align-self: flex-end;
        }

        .sidebar-close svg {
            width: 20px;
            height: 20px;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .42);
            z-index: 55;
        }

        .nav-item {
            min-height: 46px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 11px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            text-decoration: none;
            color: #111;
            font-size: 14px;
            font-family: inherit;
            transition: background .2s ease, color .2s ease;
        }

        .nav-item:hover {
            background: var(--soft-pink);
            color: var(--maroon);
        }

        .nav-item.active {
            background: var(--soft-pink);
            color: var(--maroon);
            box-shadow: 0 2px 4px #00000020;
            font-weight: 600;
        }

        .nav-icon {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            color: #606b7e;
        }

        .nav-item.active .nav-icon {
            color: var(--maroon);
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
            font-size: 22px;
            font-weight: 700;
            color: var(--maroon);
        }

        .logout-form {
            margin-top: auto;
            width: 100%;
        }

        .logout {
            margin-top: 0;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
        }

        .logout-form button {
            width: 100%;
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
            flex-wrap: wrap;
            gap: 10px 12px;
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
            font-size: 11px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 13px;
            font-weight: 500;
            overflow-wrap: break-word;
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

        /* NOTIFIKASI */
        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 16px;
            border: 1px solid transparent;
        }

        .alert-success {
            background: #e7f7ee;
            border-color: #b7e4c9;
            color: #166534;
        }

        .alert-error {
            background: #fdecef;
            border-color: #f3c2cc;
            color: #8a1428;
        }

        /* PENGUMUMAN STATUS PENGAJUAN */
        .status-banner {
            border-radius: 11px;
            padding: 15px 18px;
            margin-bottom: 18px;
            border: 1px solid #f0d9a8;
            background: #fff8e6;
            color: #8a5c04;
            font-size: 13px;
            line-height: 1.6;
        }

        .status-banner strong {
            display: block;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .status-banner--rejected {
            border-color: #f3c2cc;
            background: #fdecef;
            color: #8a1428;
        }

        /* DAFTAR KAMAR */
        .room-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .room-item {
            display: flex;
            flex-direction: column;
            gap: 12px;
            border: 1px solid #e6dcd6;
            border-radius: 11px;
            padding: 16px;
            background: #fff;
        }

        .room-item.is-selected {
            border-color: var(--maroon);
            box-shadow: 0 0 0 2px rgba(121, 12, 32, .12);
        }

        .room-item-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .room-item-title {
            font-size: 17px;
            font-weight: 600;
            margin: 0;
        }

        .room-item-sub {
            color: var(--muted);
            font-size: 12px;
            margin: 2px 0 0;
        }

        .badge {
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-available {
            background: #d8f5e3;
            color: #008b42;
        }

        .badge-full {
            background: #fdecef;
            color: #8a1428;
        }

        .badge-pending {
            background: #fff4d6;
            color: #a16207;
        }

        .badge-closed {
            background: #eeeeee;
            color: #666666;
        }

        .room-item-specs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            font-size: 12px;
        }

        .room-item-specs strong {
            display: block;
            color: var(--muted);
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: 2px;
        }

        .room-item-fasilitas {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
            margin: 0;
            overflow-wrap: break-word;
        }

        .btn-ajukan {
            width: 100%;
            border: none;
            border-radius: 8px;
            background: var(--maroon);
            color: #fff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 16px;
            cursor: pointer;
        }

        .btn-ajukan:hover {
            background: #5f0918;
        }

        .btn-ajukan[disabled] {
            background: #e5e0dd;
            color: #9a9a9a;
            cursor: not-allowed;
        }

        .empty-state {
            grid-column: 1 / -1;
            border: 1px dashed #d9cdc8;
            border-radius: 11px;
            padding: 24px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
            background: #faf7f8;
        }

        /* PENCARIAN & FILTER KAMAR */
        .room-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr);
            gap: 10px;
            margin-top: 14px;
        }

        .form-control {
            width: 100%;
            border: 1px solid #e0d6d1;
            border-radius: 8px;
            padding: 9px 12px;
            font-family: inherit;
            font-size: 13px;
            color: #171717;
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--maroon);
            box-shadow: 0 0 0 2px rgba(121, 12, 32, .1);
        }

        textarea.form-control {
            resize: vertical;
            line-height: 1.6;
        }

        /* FOTO KAMAR */
        .room-photo {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 9px;
            display: block;
            background: #f4efec;
        }

        .room-photo--empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1px dashed #d9cdc8;
            color: var(--muted);
            font-size: 11px;
            text-align: center;
        }

        .room-photo--empty svg {
            width: 34px;
            height: 34px;
            stroke: #c9b9b2;
            fill: none;
            stroke-width: 1.5;
        }

        /* GALERI FOTO KAMAR */
        .room-galeri {
            position: relative;
            border-radius: 9px;
            overflow: hidden;
            background: #f4efec;
        }

        .room-galeri__foto {
            border-radius: 0;
        }

        .room-galeri__foto:not(.is-active) {
            display: none;
        }

        .room-galeri__nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 26px;
            height: 26px;
            border: none;
            border-radius: 50%;
            background: rgba(0, 0, 0, .55);
            color: #fff;
            font-size: 18px;
            line-height: 1;
            display: grid;
            place-items: center;
            cursor: pointer;
            z-index: 2;
        }

        .room-galeri__nav:hover {
            background: rgba(0, 0, 0, .78);
        }

        .room-galeri__nav--prev {
            left: 8px;
        }

        .room-galeri__nav--next {
            right: 8px;
        }

        .room-galeri__counter {
            position: absolute;
            right: 8px;
            bottom: 8px;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(0, 0, 0, .6);
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            z-index: 2;
        }

        .room-galeri__dots {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 6px;
        }

        .room-galeri__dot {
            width: 7px;
            height: 7px;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: #d8c6cb;
            cursor: pointer;
        }

        .room-galeri__dot.is-active {
            background: var(--maroon);
        }

        .room-item.is-current {
            border-color: var(--maroon);
            border-style: dashed;
            background: #fdf8f9;
        }

        .badge-current {
            background: #f7e6ea;
            color: var(--maroon);
        }

        /* PENGAJUAN PINDAH KAMAR */
        .pindah-form {
            display: grid;
            gap: 6px;
            margin-top: 14px;
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #171717;
            margin-top: 6px;
        }

        .form-hint {
            font-size: 11px;
            color: var(--muted);
            margin: 0 0 4px;
            line-height: 1.5;
        }

        .pindah-form .btn-ajukan {
            margin-top: 10px;
        }

        .pindah-ringkas {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
            font-size: 12px;
        }

        .pindah-ringkas strong {
            display: block;
            color: var(--muted);
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: 2px;
        }

        .catatan-penolakan {
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .72);
            border: 1px solid #f3c2cc;
            font-size: 12px;
            line-height: 1.6;
        }

        /* Kartu yang tersaring tidak boleh tertimpa display flex/grid */
        .room-item[hidden],
        .empty-state[hidden] {
            display: none;
        }

        @media (max-width: 1200px) {
            .main-content {
                padding: 15px 28px 30px;
            }
        }

        /* Sidebar menjadi laci geser agar konten tetap lega di tablet dan ponsel */
        @media (max-width: 992px) {
            .mobile-toggle-btn {
                display: grid;
            }

            .topbar {
                height: 76px;
                padding: 0 16px;
            }

            .brand {
                font-size: 26px;
            }

            .brand img {
                width: 52px;
                height: 48px;
            }

            .layout {
                display: block;
                min-height: calc(100vh - 76px);
            }

            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                width: 288px;
                flex-basis: 288px;
                height: 100vh;
                height: 100dvh;
                min-height: 0;
                padding: 16px 14px 24px;
                overflow-y: auto;
                overscroll-behavior: contain;
                border-top: 1px solid var(--border);
                z-index: 60;
                transform: translateX(-104%);
                visibility: hidden;
                transition: transform .28s ease, visibility .28s ease;
                box-shadow: 8px 0 24px rgba(0, 0, 0, .18);
            }

            .sidebar.active {
                transform: translateX(0);
                visibility: visible;
            }

            .sidebar-close {
                display: grid;
            }

            .sidebar-backdrop.active {
                display: block;
            }

            .logout-form {
                margin-top: 0;
            }

            .main-content {
                padding: 18px 22px 32px;
            }

            .room-toolbar {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .room-toolbar .form-control:first-child {
                grid-column: 1 / -1;
            }

            .detail-row {
                max-width: none;
            }
        }

        @media (max-width: 820px) {
            .room-list {
                grid-template-columns: 1fr;
            }

            .room-photo {
                height: 200px;
            }
        }

        @media (max-width: 640px) {
            .topbar {
                height: 68px;
                padding: 0 12px;
            }

            .brand {
                font-size: 21px;
                gap: 8px;
            }

            .brand img {
                width: 42px;
                height: 42px;
            }

            .header-actions {
                gap: 10px;
            }

            .mobile-toggle-btn {
                width: 38px;
                height: 38px;
            }

            .sidebar {
                width: min(86vw, 300px);
                flex-basis: min(86vw, 300px);
                gap: 9px;
                padding: 14px 12px 22px;
            }

            .nav-item {
                min-height: 44px;
                font-size: 13.5px;
            }

            .main-content {
                padding: 16px 14px 26px;
            }

            .breadcrumb {
                font-size: 14px;
            }

            .page-title {
                font-size: 24px;
                line-height: 1.35;
            }

            .page-subtitle {
                font-size: 13.5px;
                line-height: 1.55;
                margin-bottom: 8px;
            }

            .alert,
            .status-banner {
                padding: 12px 14px;
                font-size: 12.5px;
            }

            .status-banner strong {
                font-size: 14px;
            }

            .room-card {
                padding: 15px;
                margin-bottom: 16px;
            }

            .room-heading {
                margin-bottom: 12px;
            }

            .room-heading h2 {
                font-size: 17px;
            }

            .room-highlight {
                padding: 16px;
                margin-bottom: 12px;
            }

            .room-number {
                font-size: 24px;
                overflow-wrap: anywhere;
            }

            .room-grid {
                grid-template-columns: 1fr;
            }

            .room-info {
                padding: 12px 14px;
            }

            .detail-card {
                padding: 12px 14px 14px;
                margin-bottom: 16px;
            }

            .detail-row {
                padding: 8px 0 12px;
                margin-bottom: 0;
            }

            .room-toolbar {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-top: 12px;
            }

            .form-control {
                font-size: 16px;
                padding: 10px 12px;
            }

            .room-item {
                padding: 14px;
                gap: 10px;
            }

            .room-item-head {
                gap: 8px;
            }

            .room-item-title {
                font-size: 16px;
            }

            .room-item-specs {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .room-photo {
                height: 172px;
            }

            .btn-ajukan {
                padding: 11px 14px;
            }

            .pindah-ringkas {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .additional-card {
                padding: 14px;
            }

            .additional-card p {
                font-size: 12px;
                line-height: 1.6;
            }

            .empty-state {
                padding: 20px 14px;
            }
        }

        @media (max-width: 400px) {
            .brand {
                font-size: 19px;
            }

            .brand img {
                width: 38px;
                height: 38px;
            }

            .room-item-specs {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="brand-wrap">
            <button
                type="button"
                class="mobile-toggle-btn"
                id="mobileMenuBtn"
                aria-label="Buka menu navigasi"
                aria-controls="sidebar"
                aria-expanded="false"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="brand">
                <img
                    src="{{ asset('images/logo_uin.png') }}"
                    alt="Logo SIMAHAD"
                    onerror="this.style.display='none'"
                >
                <span>SIMAHAD</span>
            </div>
        </div>

        <div class="header-actions">
            <div class="notification" title="Notifikasi">
                <svg viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>
            </div>

            <x-mahasiswa.profile-dropdown />
        </div>
    </header>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="layout">
        {{-- Sidebar bersama seluruh role SIMAHAD. --}}
        <x-panel.sidebar ragam="alir" atas="89px" />

        <main class="main-content">
            <div class="breadcrumb">Dashboard / Informasi Kamar</div>
            <h1 class="page-title">Informasi Kamar</h1>
            <p class="page-subtitle">
                Informasi kamar Ma’had sesuai data yang dikelola Musyrifah.
            </p>

            @if (session('success'))
                <div class="alert alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error') || $errors->any())
                <div class="alert alert-error" role="alert">
                    @if (session('error'))
                        <div>{{ session('error') }}</div>
                    @endif
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @php
                $pengajuanPindahMenunggu = $pengajuanPindah && $pengajuanPindah->masihMenunggu();
                $pengajuanPindahDitolak = $pengajuanPindah
                    && $pengajuanPindah->status === \App\Models\PengajuanKamar::STATUS_DITOLAK;
                $daftarLantai = collect($daftarKamar)->pluck('lantai')->unique()->sort()->values();
            @endphp

            @if ($kamarSaya)
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
                            {{ $kamarSaya->nomor_kamar ?: 'Belum ditetapkan' }}
                        </div>
                        <div class="muted">
                            {{ $kamarSaya->nama_kamar ?: 'Kamar '.$kamarSaya->nomor_kamar }}
                            &bull; Penempatan ditetapkan oleh Musyrifah Ma’had.
                        </div>
                    </div>

                    <div class="room-grid">
                        <div class="room-info">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 21V5h12v16M15 10h6v11M1 21h22"/>
                            </svg>
                            <div class="info-label">Gedung / Lantai</div>
                            <div class="info-value">
                                {{ $kamarSaya->gedung ?: 'Gedung A' }}
                                @if ($kamarSaya->lantai)
                                    / Lantai {{ $kamarSaya->lantai }}
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
                                {{ (int) $kamarSaya->kapasitas }} orang
                                ({{ $kamarSaya->jumlahPenghuni() }} terisi)
                            </div>
                        </div>
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Detail Penempatan</h2>

                    <div class="detail-row">
                        <div class="detail-label">Nama Penghuni</div>
                        <div class="detail-value">{{ auth()->user()->name }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">NIM</div>
                        <div class="detail-value">{{ auth()->user()->nim ?: '-' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Fasilitas Kamar</div>
                        <div class="detail-value">{{ $kamarSaya->fasilitas ?: 'Belum ada rincian fasilitas.' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Status Penempatan</div>
                        <div class="detail-value">Telah Ditetapkan</div>
                    </div>
                </section>

                @if ($pengajuanPindahMenunggu)
                    <div class="status-banner">
                        <strong>Pengajuan pindah kamar menunggu verifikasi Musyrifah.</strong>
                        Kamar asal <strong>Kamar {{ $pengajuanPindah->kamarAsal?->nomor_kamar ?? $kamarSaya->nomor_kamar }}</strong>
                        ke kamar tujuan <strong>Kamar {{ $pengajuanPindah->kamar?->nomor_kamar ?? '-' }}</strong>
                        (diajukan {{ optional($pengajuanPindah->created_at)->translatedFormat('d M Y H:i') }}).
                        Kamu tetap menempati kamar saat ini sampai pengajuan disetujui.

                        @if ($pengajuanPindah->alasan)
                            <div class="catatan-penolakan" style="background:#fff;border-color:#f0d9a8;">
                                <strong>Alasan perpindahan:</strong> {{ $pengajuanPindah->alasan }}
                            </div>
                        @endif
                    </div>
                @elseif ($pengajuanPindahDitolak)
                    <div class="status-banner status-banner--rejected">
                        <strong>Pengajuan pindah kamar ditolak.</strong>
                        Kamu tetap menempati kamar saat ini
                        (Kamar {{ $kamarSaya->nomor_kamar }}).

                        <div class="catatan-penolakan">
                            <strong>Catatan Musyrifah:</strong>
                            {{ $pengajuanPindah->catatan ?: 'Tidak ada catatan penolakan.' }}
                        </div>
                    </div>
                @endif

                @unless ($pengajuanPindahMenunggu)
                    <section class="room-card" id="pengajuan-pindah">
                        <div class="room-heading">
                            <h2>Pengajuan Pindah Kamar</h2>
                            <span class="status">Butuh Verifikasi</span>
                        </div>

                        <p class="muted" style="margin: 0;">
                            Pilih kamar tujuan yang masih memiliki sisa kapasitas dan tuliskan
                            alasan perpindahan. Penempatan kamu tidak berubah sampai Musyrifah
                            menyetujui pengajuan.
                        </p>

                        @php
                            $kamarTujuanTersedia = collect($daftarKamar)
                                ->filter(fn ($kamar) => $kamar['id'] !== (int) $kamarSaya->id && $kamar['bisa_diajukan']);
                        @endphp

                        <form method="POST" action="{{ route('kamar.pindah') }}" class="pindah-form">
                            @csrf

                            <label class="form-label" for="kamarTujuan">Kamar Tujuan</label>
                            <select
                                class="form-control"
                                name="kamar_id"
                                id="kamarTujuan"
                                required
                                @disabled($kamarTujuanTersedia->isEmpty())
                            >
                                <option value="">— Pilih kamar tujuan —</option>
                                @foreach ($kamarTujuanTersedia as $kamar)
                                    <option value="{{ $kamar['id'] }}" @selected((string) old('kamar_id') === (string) $kamar['id'])>
                                        Kamar {{ $kamar['nomor'] }} — {{ $kamar['gedung'] }}, Lantai {{ $kamar['lantai'] }}
                                        (sisa {{ $kamar['sisa'] }} tempat)
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-hint">
                                @if ($kamarTujuanTersedia->isEmpty())
                                    Belum ada kamar lain yang memiliki sisa kapasitas saat ini.
                                @else
                                    Hanya kamar dengan sisa kapasitas yang dapat diajukan.
                                @endif
                            </p>

                            <label class="form-label" for="alasanPindah">Alasan Perpindahan</label>
                            <textarea
                                class="form-control"
                                name="alasan"
                                id="alasanPindah"
                                rows="4"
                                maxlength="1000"
                                required
                                placeholder="Contoh: kamar saat ini terlalu padat dan sulit beristirahat."
                            >{{ old('alasan') }}</textarea>
                            <p class="form-hint">Wajib diisi, minimal 10 karakter.</p>

                            <button
                                type="submit"
                                class="btn-ajukan"
                                @disabled($kamarTujuanTersedia->isEmpty())
                            >
                                Kirim Pengajuan Pindah
                            </button>
                        </form>
                    </section>
                @endunless
            @else
                @if ($pengajuanSaya)
                    @if ($pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU)
                        <div class="status-banner">
                            <strong>Pengajuan kamar sedang menunggu persetujuan.</strong>
                            Kamar pilihanmu:
                            <strong>Kamar {{ $pengajuanSaya->kamar?->nomor_kamar ?? '-' }}</strong>
                            (diajukan {{ optional($pengajuanSaya->created_at)->translatedFormat('d M Y H:i') }}).
                            Kamu masih dapat mengubah pilihan kamar selama belum disetujui Musyrifah.
                        </div>
                    @elseif ($pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_DITOLAK)
                        <div class="status-banner status-banner--rejected">
                            <strong>Pengajuan kamar sebelumnya ditolak.</strong>
                            Silakan ajukan pilihan kamar lain di bawah ini.

                            @if ($pengajuanSaya->catatan)
                                <div class="catatan-penolakan">
                                    <strong>Catatan Musyrifah:</strong> {{ $pengajuanSaya->catatan }}
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            @endif

            <section class="room-card">
                <div class="room-heading">
                    <h2>Daftar Kamar Ma’had</h2>
                    <span class="status">{{ count($daftarKamar) }} Kamar</span>
                </div>

                <p class="muted" style="margin: 0;">
                    Data kamar mengikuti pengelolaan Musyrifah. Gunakan pencarian cepat
                    berdasarkan nomor kamar atau fasilitas, serta filter lantai dan status
                    ketersediaan.
                </p>

                <div class="room-toolbar">
                    <input
                        type="search"
                        id="cariKamar"
                        class="form-control"
                        placeholder="Cari nomor kamar atau fasilitas..."
                        aria-label="Cari nomor kamar atau fasilitas"
                    >

                    <select id="filterLantai" class="form-control" aria-label="Filter lantai kamar">
                        <option value="">Semua Lantai</option>
                        @foreach ($daftarLantai as $lantai)
                            <option value="{{ $lantai }}">Lantai {{ $lantai }}</option>
                        @endforeach
                    </select>

                    <select id="filterStatus" class="form-control" aria-label="Filter status ketersediaan kamar">
                        <option value="">Semua Status</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="penuh">Penuh</option>
                        <option value="lainnya">Tidak Tersedia</option>
                    </select>
                </div>

                <div class="room-list" id="daftarKamarList">
                    @forelse ($daftarKamar as $kamar)
                        @php
                            $kamarSaatIni = $kamarSaya && (int) $kamarSaya->id === (int) $kamar['id'];
                            $statusKamar = strtolower(trim((string) $kamar['status']));
                            $kategoriStatus = $kamar['bisa_diajukan']
                                ? 'tersedia'
                                : (in_array($statusKamar, ['dalam perbaikan', 'tidak tersedia', 'nonaktif'], true) ? 'lainnya' : 'penuh');
                            $sedangDiajukan = ! $kamarSaya
                                && $pengajuanSaya
                                && $pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU
                                && (int) $pengajuanSaya->kamar_id === (int) $kamar['id'];
                            $sedangDiproses = ! $kamarSaya
                                && $pengajuanSaya
                                && $pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU
                                && ! $sedangDiajukan;
                            $kataKunci = strtolower(trim(
                                $kamar['nomor'].' '.($kamar['nama'] ?? '').' '.$kamar['gedung'].' '.($kamar['fasilitas'] ?? '')
                            ));
                        @endphp

                        <div
                            class="room-item {{ $sedangDiajukan ? 'is-selected' : '' }} {{ $kamarSaatIni ? 'is-current' : '' }}"
                            data-lantai="{{ $kamar['lantai'] }}"
                            data-status="{{ $kategoriStatus }}"
                            data-kata="{{ $kataKunci }}"
                        >
                            @php
                                $fotosKamar = $kamar['fotos'] ?? [];
                            @endphp

                            @if (! empty($fotosKamar))
                                <div class="room-galeri" data-galeri>
                                    @foreach ($fotosKamar as $indexFoto => $foto)
                                        <img
                                            class="room-photo room-galeri__foto {{ $indexFoto === 0 ? 'is-active' : '' }}"
                                            src="{{ $foto['url'] }}"
                                            alt="Foto {{ $indexFoto + 1 }} Kamar {{ $kamar['nomor'] }}"
                                            loading="lazy"
                                            data-galeri-slide
                                        >
                                    @endforeach

                                    @if (count($fotosKamar) > 1)
                                        <button type="button" class="room-galeri__nav room-galeri__nav--prev" data-galeri-prev aria-label="Foto sebelumnya">&#8249;</button>
                                        <button type="button" class="room-galeri__nav room-galeri__nav--next" data-galeri-next aria-label="Foto berikutnya">&#8250;</button>
                                        <span class="room-galeri__counter" data-galeri-counter>1 / {{ count($fotosKamar) }}</span>
                                    @endif
                                </div>

                                @if (count($fotosKamar) > 1)
                                    <div class="room-galeri__dots">
                                        @foreach ($fotosKamar as $indexFoto => $foto)
                                            <button
                                                type="button"
                                                class="room-galeri__dot {{ $indexFoto === 0 ? 'is-active' : '' }}"
                                                data-galeri-dot="{{ $indexFoto }}"
                                                aria-label="Tampilkan foto {{ $indexFoto + 1 }}"
                                            ></button>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="room-photo room-photo--empty">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M3 18V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v11"/>
                                        <path d="M12 11h7a2 2 0 0 1 2 2v5"/>
                                        <path d="M3 14h18M3 18v2M21 18v2"/>
                                    </svg>
                                    <span>Foto kamar belum tersedia</span>
                                </div>
                            @endif

                            <div class="room-item-head">
                                <div>
                                    <h3 class="room-item-title">Kamar {{ $kamar['nomor'] }}</h3>
                                    <p class="room-item-sub">
                                        {{ $kamar['gedung'] }} &bull; Lantai {{ $kamar['lantai'] }}
                                    </p>
                                </div>

                                @if ($kamarSaatIni)
                                    <span class="badge badge-current">Kamar Saat Ini</span>
                                @elseif ($sedangDiajukan)
                                    <span class="badge badge-pending">Diajukan</span>
                                @elseif ($kamar['bisa_diajukan'])
                                    <span class="badge badge-available">Tersedia</span>
                                @elseif ($kategoriStatus === 'penuh')
                                    <span class="badge badge-full">Penuh</span>
                                @else
                                    <span class="badge badge-closed">{{ $kamar['status'] }}</span>
                                @endif
                            </div>

                            <div class="room-item-specs">
                                <div><strong>Kapasitas</strong>{{ $kamar['kapasitas'] }} orang</div>
                                <div><strong>Penghuni</strong>{{ $kamar['terisi'] }} orang</div>
                                <div><strong>Sisa Kapasitas</strong>{{ $kamar['sisa'] }} tempat</div>
                            </div>

                            <p class="room-item-fasilitas">
                                <strong>Fasilitas:</strong>
                                {{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}
                            </p>

                            @if ($kamarSaatIni)
                                <button type="button" class="btn-ajukan" disabled>Kamar Saat Ini</button>
                            @elseif ($kamarSaya)
                                <button
                                    type="button"
                                    class="btn-ajukan"
                                    data-kamar-pindah="{{ $kamar['id'] }}"
                                    @disabled(! $kamar['bisa_diajukan'] || $pengajuanPindahMenunggu)
                                >
                                    @if ($pengajuanPindahMenunggu)
                                        Menunggu Verifikasi
                                    @elseif ($kamar['bisa_diajukan'])
                                        Pilih untuk Pindah
                                    @else
                                        Tidak Tersedia
                                    @endif
                                </button>
                            @else
                                <form method="POST" action="{{ route('kamar.pilih') }}" style="margin-top: auto;">
                                    @csrf
                                    <input type="hidden" name="kamar_id" value="{{ $kamar['id'] }}">
                                    <button type="submit" class="btn-ajukan" @disabled(! $kamar['bisa_diajukan'] || $sedangDiajukan)>
                                        @if ($sedangDiajukan)
                                            Sedang Diajukan
                                        @elseif ($kamar['bisa_diajukan'])
                                            {{ $sedangDiproses ? 'Ganti ke Kamar Ini' : 'Ajukan Kamar Ini' }}
                                        @else
                                            Tidak Tersedia
                                        @endif
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state">
                            Belum ada data kamar dari Musyrifah Ma’had.
                        </div>
                    @endforelse

                    <div class="empty-state" id="kamarKosong" hidden>
                        Tidak ada kamar yang sesuai dengan pencarian atau filter.
                    </div>
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
                    Pengajuan kamar dan pengajuan pindah kamar diverifikasi Musyrifah melalui
                    menu Pengajuan Kamar. Selama pengajuan belum disetujui, penempatan kamar
                    kamu tidak berubah.
                </p>
            </section>
        </main>
    </div>

    <script>
        (function () {
            const inputCari = document.getElementById('cariKamar');
            const filterLantai = document.getElementById('filterLantai');
            const filterStatus = document.getElementById('filterStatus');
            const daftarKamar = document.getElementById('daftarKamarList');
            const pesanKosong = document.getElementById('kamarKosong');

            // Menu samping menjadi laci geser pada layar tablet dan ponsel.
            const tombolMenu = document.getElementById('mobileMenuBtn');
            const sidebarNav = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const tombolTutupSidebar = document.getElementById('sidebarClose');

            const tutupSidebar = function () {
                if (!sidebarNav || !sidebarBackdrop) {
                    return;
                }

                sidebarNav.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
                document.body.style.overflow = '';

                if (tombolMenu) {
                    tombolMenu.setAttribute('aria-expanded', 'false');
                }
            };

            if (tombolMenu && sidebarNav && sidebarBackdrop) {
                tombolMenu.addEventListener('click', function () {
                    const terbuka = sidebarNav.classList.toggle('active');

                    sidebarBackdrop.classList.toggle('active', terbuka);
                    document.body.style.overflow = terbuka ? 'hidden' : '';
                    tombolMenu.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
                });

                sidebarBackdrop.addEventListener('click', tutupSidebar);
                tombolTutupSidebar?.addEventListener('click', tutupSidebar);

                sidebarNav.querySelectorAll('a.nav-item').forEach(function (tautan) {
                    tautan.addEventListener('click', tutupSidebar);
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        tutupSidebar();
                    }
                });

                window.addEventListener('resize', function () {
                    if (window.innerWidth > 992) {
                        tutupSidebar();
                    }
                });
            }

            if (daftarKamar) {
                const semuaKamar = Array.from(daftarKamar.querySelectorAll('.room-item'));

                const terapkanFilter = function () {
                    const kata = (inputCari && inputCari.value ? inputCari.value : '').toLowerCase().trim();
                    const lantai = filterLantai ? filterLantai.value : '';
                    const status = filterStatus ? filterStatus.value : '';
                    let jumlahTampil = 0;

                    semuaKamar.forEach(function (item) {
                        const cocokKata = !kata || (item.dataset.kata || '').includes(kata);
                        const cocokLantai = !lantai || item.dataset.lantai === lantai;
                        const cocokStatus = !status || item.dataset.status === status;
                        const tampil = cocokKata && cocokLantai && cocokStatus;

                        item.hidden = !tampil;

                        if (tampil) {
                            jumlahTampil += 1;
                        }
                    });

                    if (pesanKosong) {
                        pesanKosong.hidden = jumlahTampil > 0 || semuaKamar.length === 0;
                    }
                };

                if (inputCari) {
                    inputCari.addEventListener('input', terapkanFilter);
                }

                if (filterLantai) {
                    filterLantai.addEventListener('change', terapkanFilter);
                }

                if (filterStatus) {
                    filterStatus.addEventListener('change', terapkanFilter);
                }
            }

            // Tombol "Pilih untuk Pindah" mengisi form pengajuan pindah kamar.
            const pilihanTujuan = document.getElementById('kamarTujuan');

            document.querySelectorAll('[data-kamar-pindah]').forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    if (! pilihanTujuan) {
                        return;
                    }

                    pilihanTujuan.value = tombol.dataset.kamarPindah;

                    document.getElementById('pengajuan-pindah')
                        .scrollIntoView({ behavior: 'smooth', block: 'start' });
                    document.getElementById('alasanPindah')
                        .focus({ preventScroll: true });
                });
            });

            // Galeri foto: tampilkan foto kamar satu per satu.
            document.querySelectorAll('[data-galeri]').forEach(function (galeri) {
                const slides = Array.from(galeri.querySelectorAll('[data-galeri-slide]'));

                if (slides.length === 0) {
                    return;
                }

                const counter = galeri.querySelector('[data-galeri-counter]');
                const dots = Array.from(galeri.closest('.room-item').querySelectorAll('[data-galeri-dot]'));
                let indeks = 0;

                const tampilkan = function (baru) {
                    indeks = (baru + slides.length) % slides.length;

                    slides.forEach(function (slide, i) {
                        slide.classList.toggle('is-active', i === indeks);
                    });

                    dots.forEach(function (dot, i) {
                        dot.classList.toggle('is-active', i === indeks);
                    });

                    if (counter) {
                        counter.textContent = (indeks + 1) + ' / ' + slides.length;
                    }
                };

                galeri.querySelector('[data-galeri-prev]')?.addEventListener('click', function () {
                    tampilkan(indeks - 1);
                });

                galeri.querySelector('[data-galeri-next]')?.addEventListener('click', function () {
                    tampilkan(indeks + 1);
                });

                dots.forEach(function (dot) {
                    dot.addEventListener('click', function () {
                        tampilkan(parseInt(dot.dataset.galeriDot, 10) || 0);
                    });
                });
            });
        })();
    </script>
</body>
</html>
