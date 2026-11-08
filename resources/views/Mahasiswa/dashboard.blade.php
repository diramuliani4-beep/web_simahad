<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard - SIMAHAD UIN Imam Bonjol</title>

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
            width: 100%;
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
                rgba(219, 234, 254, 0.45) 0%,
                rgba(241, 245, 249, 0.55) 30%,
                rgba(255, 255, 255, 0.85) 65%,
                rgba(255, 255, 255, 0.95) 100%
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

        .nav-pill.active {
            background-color: var(--maroon-tint);
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

        .nav-pill.active .nav-icon svg {
            stroke: var(--primary-maroon);
        }

        .nav-label {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--primary-maroon);
            color: #fff;
            font-size: 0.68rem;
            font-weight: 800;
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
            text-decoration: none;
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
            min-width: 0;
        }

        .welcome-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 0.35rem;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .welcome-sub {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 1.35rem;
            line-height: 1.5;
        }

        .simahad-card {
            border: 1.5px solid var(--maroon-border);
            border-radius: 16px;
            background-color: #ffffff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .simahad-card:hover {
            box-shadow: 0 4px 14px rgba(110, 0, 27, 0.06);
        }

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
            flex-wrap: wrap;
        }

        .student-avatar-box {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background-color: #fdf2f4;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--primary-maroon);
            overflow: hidden;
        }

        .student-avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-info-col {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            min-width: 0;
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

        .status-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 1rem;
            padding-top: 0.95rem;
            border-top: 1px dashed #eadfda;
        }

        .status-chip {
            display: inline-flex;
            flex-direction: column;
            gap: 0.15rem;
            min-width: 132px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #f0e2dc;
            border-radius: 10px;
            background: #fffdfc;
        }

        .status-chip .chip-label {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .status-chip .chip-value {
            font-size: 0.84rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.35;
        }

        .status-chip.ok .chip-value {
            color: #15803d;
        }

        .status-chip.warning .chip-value {
            color: #b45309;
        }

        .status-chip.danger .chip-value {
            color: #b91c1c;
        }

        /* ================= GRID ================= */
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

        .grid-card.full-width {
            grid-column: 1 / -1;
        }

        .card-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

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

        /* ================= TAGIHAN ================= */
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

        .tagihan-body {
            flex-grow: 1;
            padding-top: 0.35rem;
        }

        .tagihan-nominal {
            font-size: 1.75rem;
            font-weight: 800;
            color: #111827;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }

        .tagihan-sub {
            margin-top: 0.15rem;
            font-size: 0.83rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .tagihan-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem 1.15rem;
            margin-top: 0.75rem;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .tagihan-meta-row strong {
            color: #374151;
            font-weight: 700;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.23rem 0.65rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .badge-status.belum {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-status.menunggu {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-status.terlambat {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-status.lunas {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-status.terkumpul {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-status.dinilai {
            background: #dcfce7;
            color: #15803d;
        }

        .reminder-alert {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-top: 0.85rem;
            padding: 0.6rem 0.75rem;
            border-radius: 9px;
            font-size: 0.78rem;
            font-weight: 600;
            line-height: 1.5;
            text-decoration: none;
        }

        .reminder-alert.danger {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .reminder-alert.info {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .reminder-alert.success {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .va-inline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding: 0.55rem 0.7rem;
            border: 1px dashed #d9c8c1;
            border-radius: 9px;
            background: #fffdfc;
        }

        .va-inline .va-label {
            font-size: 0.66rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .va-inline .va-number {
            font-size: 0.92rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            color: #111827;
            word-break: break-all;
        }

        .btn-copy-mini {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.35rem 0.6rem;
            border: 1px solid var(--maroon-border);
            border-radius: 7px;
            background: #fff;
            color: var(--primary-maroon);
            font-family: inherit;
            font-size: 0.7rem;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-copy-mini:hover {
            background: var(--maroon-tint);
        }

        .btn-copy-mini.is-copied {
            border-color: #15803d;
            color: #15803d;
            background: #f0fdf4;
        }

        /* ================= ABSENSI ================= */
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

        /* ================= KALENDER ================= */
        .kalender-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 0.85rem;
        }

        .kalender-bulan {
            font-size: 0.95rem;
            font-weight: 800;
            color: #111827;
        }

        .kalender-nav {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .kalender-nav a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border: 1.2px solid var(--maroon-border);
            border-radius: 8px;
            color: var(--primary-maroon);
            text-decoration: none;
            font-weight: 800;
            line-height: 1;
        }

        .kalender-nav a:hover {
            background: var(--maroon-tint);
        }

        .kalender-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 4px;
        }

        .kalender-hari {
            text-align: center;
            padding: 0.3rem 0;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .kalender-cell {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            min-height: 42px;
            border-radius: 8px;
            border: 1px solid transparent;
            background: #fbf9f8;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
        }

        a.kalender-cell:hover {
            border-color: var(--maroon-border);
            background: #fff8f9;
        }

        .kalender-cell.luar {
            background: transparent;
            color: #cbd5e1;
        }

        .kalender-cell.hari-ini {
            border-color: var(--gold, #c9a96a);
        }

        .kalender-cell.terpilih {
            background: var(--primary-maroon);
            border-color: var(--primary-maroon);
            color: #ffffff;
        }

        .kalender-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--primary-maroon);
        }

        .kalender-cell.terpilih .kalender-dot {
            background: #ffffff;
        }

        .kalender-detail {
            margin-top: 1rem;
            padding-top: 0.9rem;
            border-top: 1px dashed #eadfda;
        }

        .kalender-detail-title {
            font-size: 0.78rem;
            font-weight: 800;
            color: var(--primary-maroon);
            margin-bottom: 0.5rem;
        }

        .kalender-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.6rem;
            padding: 0.5rem 0.65rem;
            border: 1px solid #f0e2dc;
            border-radius: 9px;
            background: #fffdfc;
        }

        .kalender-item + .kalender-item {
            margin-top: 0.4rem;
        }

        .kalender-item-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1f2937;
        }

        .kalender-item-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .kalender-item-jam {
            font-size: 0.72rem;
            font-weight: 800;
            color: var(--primary-maroon);
            white-space: nowrap;
        }

        /* ================= DAFTAR RINGKAS ================= */
        .ringkas-list {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .ringkas-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.6rem;
            padding: 0.55rem 0.7rem;
            border: 1px solid #f0e2dc;
            border-radius: 9px;
            background: #fffdfc;
            text-decoration: none;
            color: inherit;
        }

        .ringkas-item:hover {
            border-color: #d9c8c1;
            background: #fff8f9;
        }

        .ringkas-item-main {
            min-width: 0;
        }

        .ringkas-mapel {
            font-size: 0.66rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--primary-maroon);
        }

        .ringkas-title {
            font-size: 0.84rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.35;
            word-break: break-word;
        }

        .ringkas-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            line-height: 1.4;
            margin-top: 1px;
        }

        .ringkas-nilai {
            display: inline-flex;
            align-items: baseline;
            gap: 3px;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: #dcfce7;
            color: #15803d;
            font-size: 0.68rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .ringkas-nilai span {
            font-size: 0.86rem;
        }

        .ringkas-kosong {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            padding: 1.1rem 0.75rem;
            border: 1px dashed #eadfda;
            border-radius: 10px;
            background: #fffdfc;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            line-height: 1.5;
        }

        .izin-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            margin-top: 0.35rem;
        }

        .izin-chip {
            flex: 1 1 88px;
            padding: 0.45rem 0.5rem;
            border-radius: 9px;
            border: 1px solid #f0e2dc;
            background: #fffdfc;
            text-align: center;
        }

        .izin-chip .angka {
            display: block;
            font-size: 1.05rem;
            font-weight: 800;
            color: #1f2937;
            line-height: 1.2;
        }

        .izin-chip .teks {
            font-size: 0.66rem;
            font-weight: 700;
            color: var(--text-muted);
            line-height: 1.2;
        }

        .izin-chip.menunggu .angka { color: #b45309; }
        .izin-chip.disetujui .angka { color: #15803d; }
        .izin-chip.ditolak .angka { color: #b91c1c; }
        .izin-chip.selesai .angka { color: #1d4ed8; }

        /* ================= NOTIFIKASI ================= */
        .notif-list {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .notif-form {
            margin: 0;
        }

        .notif-button {
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
            width: 100%;
            padding: 0.55rem 0.7rem;
            border: 1px solid #f0e2dc;
            border-radius: 9px;
            background: #fffdfc;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
        }

        .notif-button:hover {
            background: #fff8f9;
            border-color: #d9c8c1;
        }

        .notif-button.belum {
            background: #fff7f2;
            border-color: #e9d3c6;
        }

        .notif-ikon {
            flex-shrink: 0;
            font-size: 0.95rem;
            line-height: 1.35;
        }

        .notif-body {
            min-width: 0;
            flex: 1;
        }

        .notif-judul {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.81rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.35;
        }

        .notif-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #dc2626;
            flex-shrink: 0;
        }

        .notif-pesan {
            margin-top: 1px;
            font-size: 0.73rem;
            color: var(--text-muted);
            line-height: 1.45;
        }

        .notif-waktu {
            margin-top: 2px;
            font-size: 0.67rem;
            font-weight: 700;
            color: #9ca3af;
        }

        /* ================= MODAL & BACKDROP ================= */
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
                box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
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

        @media (max-width: 560px) {
            .brand-title {
                font-size: 1.15rem;
            }

            .brand-logo-img {
                height: 34px;
            }

            .top-navbar {
                padding: 0 0.9rem;
            }

            .welcome-heading {
                font-size: 1.45rem;
            }

            .kalender-cell {
                min-height: 36px;
                font-size: 0.72rem;
            }

            .kalender-hari {
                font-size: 0.6rem;
            }

            .va-inline {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
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
            <x-notifikasi-dropdown />
            <x-mahasiswa.profile-dropdown />
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
                    <polygon points="50,4 92,25 92,75 50,96 8,75 8,25" stroke="#a16207" stroke-width="3" fill="none" />
                    <polygon points="50,14 82,30 82,70 50,86 18,70 18,30" stroke="#64748b" stroke-width="2.5" fill="none" />
                    <path d="M50 20 L50 80 M25 50 L75 50" stroke="#64748b" stroke-width="2" />
                </svg>
                <div class="watermark-text-2">UIN Imam Bonjol</div>
            </div>

            <div class="sidebar-content">
                <nav class="sidebar-nav">
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

                    <a href="{{ route('tugas.mhs.index') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <path d="M8 8h8M8 12h8M8 16h5"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Tugas</span>
                        @if ($jumlahTugasBelum > 0)
                            <span class="nav-badge-count">{{ $jumlahTugasBelum }}</span>
                        @endif
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

                    <a href="{{ route('surat.perizinan.mhs') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </span>
                        <span class="nav-label">Surat dan Perizinan</span>
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
                    <a href="{{ route('profil.mhs') }}" class="nav-pill">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pengaturan Akun</span>
                    </a>

                    <a href="{{ route('logout.confirm') }}" class="btn-keluar-pill">
                        <span class="nav-icon">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 17l5-5-5-5"/>
                                <path d="M15 12H3"/>
                                <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                            </svg>
                        </span>
                        <span class="nav-label">Keluar</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT ================= -->
        <main class="main-content">
            @if (session('success'))
                <div style="margin-bottom:1rem; padding:0.7rem 0.9rem; border:1px solid #bbf7d0; border-radius:10px; background:#f0fdf4; color:#166534; font-size:0.82rem; font-weight:600;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="margin-bottom:1rem; padding:0.7rem 0.9rem; border:1px solid #fecaca; border-radius:10px; background:#fef2f2; color:#991b1b; font-size:0.82rem; font-weight:600;">
                    {{ $errors->first() }}
                </div>
            @endif

            <h1 class="welcome-heading">
                {{ $sapaan }}, {{ $user->name }} <span>👋</span>
            </h1>
            <p class="welcome-sub">
                Assalamu'alaikum. Berikut ringkasan aktivitas pembelajaran dan keasramaan Anda di Ma'had Al-Jami'ah.
            </p>

            @php
                $pembayaran = $ringkasanPembayaran;
                $pengaturanBayar = $pembayaran['pengaturan'] ?? null;
                $statusPendaftaran = $pendaftaran?->status ? ucwords(str_replace('_', ' ', $pendaftaran->status)) : 'Belum Mendaftar';
                $statusAkun = $user->status ? ucwords(str_replace('_', ' ', $user->status)) : 'Belum Diketahui';
                $kelasStatusAkun = in_array(strtolower((string) $user->status), ['aktif'], true) ? 'ok' : 'warning';
                $programStudi = $pendaftaran?->program_studi ?: 'Mahasiswa Ma\'had';
                $angkatan = $pendaftaran?->tahun_angkatan ?: '-';
            @endphp

            <!-- SECTION 1: RINGKASAN MAHASISWA -->
            <section class="simahad-card student-summary-card">
                <div class="card-heading-maroon">Ringkasan Mahasiswa</div>

                <div class="student-profile-row">
                    <div class="student-avatar-box">
                        @if ($user->foto_profil)
                            <img src="{{ asset('storage/'.$user->foto_profil) }}" alt="Foto {{ $user->name }}">
                        @else
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        @endif
                    </div>

                    <div class="student-info-col">
                        <div class="student-name">{{ $user->name }}</div>
                        <div class="student-program">{{ $programStudi }} &middot; Angkatan {{ $angkatan }}</div>
                        <div class="student-status">{{ $kelompok }}</div>
                    </div>
                </div>

                <div class="status-chip-row">
                    <div class="status-chip {{ $statusPendaftaran === 'Disetujui' ? 'ok' : 'warning' }}">
                        <span class="chip-label">Status Pendaftaran</span>
                        <span class="chip-value">{{ $statusPendaftaran }}</span>
                    </div>

                    <div class="status-chip {{ $kelasStatusAkun }}">
                        <span class="chip-label">Status Keaktifan</span>
                        <span class="chip-value">{{ $statusAkun }}</span>
                    </div>

                    <div class="status-chip {{ $kamar ? 'ok' : 'warning' }}">
                        <span class="chip-label">Kamar &amp; Lantai</span>
                        <span class="chip-value">
                            @if ($kamar)
                                {{ $kamar->nama_kamar }}@if ($kamar->gedung) &middot; {{ $kamar->gedung }}@endif
                                @if ($kamar->lantai) &middot; Lantai {{ $kamar->lantai }}@endif
                            @else
                                Belum Mendapatkan Kamar
                            @endif
                        </span>
                    </div>

                    <div class="status-chip">
                        <span class="chip-label">Kelompok Belajar</span>
                        <span class="chip-value">{{ $kelompok }}</span>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: PEMBAYARAN & ABSENSI -->
            <div class="dashboard-grid">
                <!-- Kartu 1: Ringkasan Pembayaran -->
                <div class="simahad-card grid-card">
                    <div>
                        <div class="card-heading-maroon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                                <path d="M16 3H4a2 2 0 0 0-2 2v2"></path>
                                <circle cx="16" cy="14" r="1.5" fill="currentColor"></circle>
                            </svg>
                            <span>Ringkasan Pembayaran</span>
                        </div>

                        @if (! $pembayaran['dibuka'])
                            <div class="tagihan-center-wrap">
                                <div class="tagihan-empty-title">Periode Belum Dibuka</div>
                                <div class="tagihan-empty-desc">
                                    @if ($pengaturanBayar)
                                        Periode {{ $pengaturanBayar->periode }} ({{ $pengaturanBayar->nama_tahap }}) akan dibuka pada {{ $pengaturanBayar->periodeDibukaTeks() }}.
                                    @else
                                        Belum ada periode pembayaran yang dibuka oleh Ma'had.
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="tagihan-body">
                                <div class="tagihan-nominal">{{ $pembayaran['nominal_rupiah'] }}</div>
                                <div class="tagihan-sub">
                                    {{ $pembayaran['tahap'] }} &middot; {{ $pengaturanBayar->periode }}
                                </div>

                                <div class="tagihan-meta-row">
                                    <span>Jatuh tempo: <strong>{{ $pembayaran['jatuh_tempo'] }}</strong></span>
                                    <span class="badge-status {{ $pembayaran['kelas_status'] }}">{{ $pembayaran['label_status'] }}</span>
                                </div>

                                @if (filled($pembayaran['va_nomor']))
                                    <div class="va-inline">
                                        <div>
                                            <div class="va-label">Virtual Account {{ $pengaturanBayar->va_bank ?: 'BSI' }}</div>
                                            <div class="va-number">{{ $pembayaran['va_nomor'] }}</div>
                                        </div>
                                        <button type="button" class="btn-copy-mini" data-copy-va="{{ $pembayaran['va_nomor'] }}">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                            Salin VA
                                        </button>
                                    </div>
                                @endif

                                @if ($pembayaran['status'] === 'lunas')
                                    <a href="{{ route('pembayaran.detail') }}" class="reminder-alert success">
                                        Tagihan sudah dikonfirmasi Lunas oleh Rektorat. Pengingat dihentikan.
                                    </a>
                                @elseif ($pembayaran['status'] === 'terlambat')
                                    <a href="{{ route('pembayaran.detail') }}" class="reminder-alert danger">
                                        Tagihan melewati jatuh tempo {{ $pembayaran['jatuh_tempo'] }}. Klik untuk detail pembayaran.
                                    </a>
                                @elseif ($pembayaran['status'] === 'menunggu')
                                    <a href="{{ route('pembayaran.detail') }}" class="reminder-alert info">
                                        Menunggu konfirmasi Rektorat. Status Lunas mengikuti rekap pembayaran resmi.
                                    </a>
                                @elseif ($pembayaran['pengingat'])
                                    <a href="{{ route('pembayaran.detail') }}" class="reminder-alert danger">
                                        Pengingat: jatuh tempo tinggal {{ $pembayaran['sisa_hari'] }} hari ({{ $pembayaran['jatuh_tempo'] }}).
                                    </a>
                                @else
                                    <a href="{{ route('pembayaran.detail') }}" class="reminder-alert info">
                                        Tagihan belum dibayar. Bayar sebelum {{ $pembayaran['jatuh_tempo'] }}.
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('pembayaran.detail') }}" class="link-card-detail">Detail pembayaran &rarr;</a>
                    </div>
                </div>

                <!-- Kartu 2: Ringkasan Absensi -->
                <div class="simahad-card grid-card">
                    <div>
                        <div class="card-heading-maroon" style="margin-bottom: 0.45rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="8" y="2" width="8" height="4" rx="1"></rect>
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <path d="m9 14 2 2 4-4"></path>
                            </svg>
                            <span>Ringkasan Absensi</span>
                        </div>

                        <div class="absensi-badges-row">
                            <div class="stat-pill hadir">
                                <span class="stat-number">{{ $absensi['hadir'] }}</span>
                                <span class="stat-label">Hadir</span>
                            </div>
                            <div class="stat-pill izin">
                                <span class="stat-number">{{ $absensi['izin'] + $absensi['sakit'] }}</span>
                                <span class="stat-label">Izin/Sakit</span>
                            </div>
                            <div class="stat-pill alpa">
                                <span class="stat-number">{{ $absensi['alpa'] }}</span>
                                <span class="stat-label">Alpa</span>
                            </div>
                        </div>

                        <div class="absensi-meeting-count">Total pertemuan: {{ $absensi['total'] }} kali</div>
                        <div class="attendance-progress-track">
                            <div class="attendance-progress-bar" style="width: {{ min(100, $absensi['persentase']) }}%;"></div>
                        </div>
                        <div class="attendance-progress-footer">
                            <span>Persentase kehadiran</span>
                            <span class="attendance-percentage-val">{{ $absensi['persentase'] }}%</span>
                        </div>
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('absensi.mhs') }}" class="link-card-detail">Detail absensi &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: KALENDER JADWAL PEMBELAJARAN -->
            <section class="simahad-card grid-card full-width" style="margin-bottom: 1.35rem;">
                <div class="kalender-header">
                    <div class="card-heading-maroon" style="margin-bottom: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Kalender Jadwal Pembelajaran</span>
                    </div>

                    <div class="kalender-nav">
                        <span class="kalender-bulan">{{ ucfirst($kalender['bulan_format']) }}</span>
                        <a href="{{ route('dashboard', ['bulan' => $kalender['bulan_sebelumnya']]) }}" title="Bulan sebelumnya" aria-label="Bulan sebelumnya">&lsaquo;</a>
                        <a href="{{ route('dashboard', ['bulan' => $kalender['bulan_berikutnya']]) }}" title="Bulan berikutnya" aria-label="Bulan berikutnya">&rsaquo;</a>
                    </div>
                </div>

                <div class="kalender-grid">
                    @foreach ($kalender['hari'] as $namaHari)
                        <div class="kalender-hari">{{ $namaHari }}</div>
                    @endforeach

                    @foreach ($kalender['minggu'] as $pekan)
                        @foreach ($pekan as $sel)
                            @if ($sel['dalam_bulan'])
                                <a href="{{ route('dashboard', ['bulan' => $kalender['bulan']->format('Y-m'), 'tanggal' => $sel['tanggal']->format('Y-m-d')]) }}"
                                   class="kalender-cell {{ $sel['hari_ini'] ? 'hari-ini' : '' }} {{ $sel['terpilih'] ? 'terpilih' : '' }}"
                                   title="{{ $sel['tanggal']->locale('id')->translatedFormat('d F Y') }}">
                                    <span>{{ $sel['tanggal']->day }}</span>
                                    @if ($sel['jadwal']->isNotEmpty())
                                        <span class="kalender-dot"></span>
                                    @endif
                                </a>
                            @else
                                <span class="kalender-cell luar">{{ $sel['tanggal']->day }}</span>
                            @endif
                        @endforeach
                    @endforeach
                </div>

                <div class="kalender-detail">
                    @if ($kalender['tanggal_dipilih'])
                        <div class="kalender-detail-title">
                            Jadwal {{ $kalender['tanggal_dipilih']->locale('id')->translatedFormat('l, d F Y') }}
                        </div>

                        @forelse ($kalender['jadwal_tanggal_dipilih'] as $jadwal)
                            <div class="kalender-item">
                                <div>
                                    <div class="kalender-item-title">{{ $jadwal->namaMataPelajaran() }}</div>
                                    <div class="kalender-item-sub">
                                        {{ $jadwal->guru_pengampu ?: 'Pengajar Ma\'had' }} &middot; {{ $jadwal->kelompok }}
                                    </div>
                                </div>
                                <span class="kalender-item-jam">{{ $jadwal->jamTeks() }}</span>
                            </div>
                        @empty
                            <div class="kalender-item-sub">Tidak ada jadwal pembelajaran pada tanggal ini.</div>
                        @endforelse
                    @else
                        <div class="kalender-item-sub">
                            Klik tanggal yang memiliki penanda untuk melihat mata pelajaran, guru, waktu, dan kelompok.
                        </div>
                    @endif
                </div>

                <div class="card-detail-footer">
                    <a href="{{ route('pembelajaran.mhs') }}" class="link-card-detail">Lihat pembelajaran &rarr;</a>
                </div>
            </section>

            <!-- SECTION 4: TUGAS & NILAI -->
            <div class="dashboard-grid">
                <!-- Kartu 3: Ringkasan Tugas -->
                <div class="simahad-card grid-card">
                    <div>
                        <div class="card-header-flex">
                            <div class="card-heading-maroon" style="margin-bottom: 0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                    <path d="M8 8h8M8 12h8M8 16h5"></path>
                                </svg>
                                <span>Tugas Terbaru</span>
                            </div>
                            @if ($jumlahTugasBelum > 0)
                                <span class="badge-status belum">{{ $jumlahTugasBelum }} belum dikumpulkan</span>
                            @endif
                        </div>

                        <div class="ringkas-list" style="margin-top: 0.7rem;">
                            @forelse ($tugasTerbaru as $item)
                                @php
                                    $tugasItem = $item['tugas'];
                                @endphp
                                <a href="{{ route('tugas.mhs.show', $tugasItem) }}" class="ringkas-item">
                                    <div class="ringkas-item-main">
                                        <div class="ringkas-mapel">{{ $tugasItem->namaMataPelajaran() }}</div>
                                        <div class="ringkas-title">{{ \Illuminate\Support\Str::limit($tugasItem->judul, 62) }}</div>
                                        <div class="ringkas-sub">
                                            {{ $tugasItem->namaGuru() }} &middot; Batas {{ $tugasItem->batasTeks() }}
                                        </div>
                                    </div>
                                    <span class="badge-status {{ $item['status']['kelas'] }}">{{ $item['status']['label'] }}</span>
                                </a>
                            @empty
                                <div class="ringkas-kosong">
                                    Belum ada tugas yang dipublikasikan untuk {{ $kelompok }}.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('tugas.mhs.index') }}" class="link-card-detail">Lihat semua tugas &rarr;</a>
                    </div>
                </div>

                <!-- Kartu 4: Ringkasan Nilai -->
                <div class="simahad-card grid-card">
                    <div>
                        <div class="card-heading-maroon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01z"></path>
                            </svg>
                            <span>Nilai Terbaru</span>
                        </div>

                        <div class="ringkas-list" style="margin-top: 0.7rem;">
                            @forelse ($nilaiTerbaru as $nilai)
                                <a href="{{ $nilai->tugas ? route('tugas.mhs.show', $nilai->tugas) : route('tugas.mhs.index') }}" class="ringkas-item">
                                    <div class="ringkas-item-main">
                                        <div class="ringkas-mapel">{{ $nilai->tugas?->namaMataPelajaran() ?? 'Mata Pelajaran' }}</div>
                                        <div class="ringkas-title">{{ \Illuminate\Support\Str::limit($nilai->tugas?->judul ?? 'Tugas', 62) }}</div>
                                        <div class="ringkas-sub">Dinilai {{ $nilai->dinilaiTeks() }}</div>
                                    </div>
                                    <span class="ringkas-nilai"><span>{{ $nilai->nilai }}</span>/100</span>
                                </a>
                            @empty
                                <div class="ringkas-kosong">
                                    Belum ada nilai yang dipublikasikan oleh Guru.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('tugas.mhs.index') }}" class="link-card-detail">Lihat semua tugas &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: PENGUMUMAN TERBARU -->
            <section class="simahad-card grid-card full-width" style="margin-bottom: 1.35rem;">
                <div>
                    <div class="card-heading-maroon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 11 18-5v12L3 14v-3z"></path>
                            <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                        </svg>
                        <span>Pengumuman Terbaru</span>
                    </div>

                    <div class="ringkas-list">
                        @forelse ($pengumumanTerbaru as $pengumuman)
                            <a href="{{ route('pengumuman.mhs.show', $pengumuman) }}" class="ringkas-item">
                                <div class="ringkas-item-main">
                                    <div class="ringkas-mapel">{{ $pengumuman->kategori }}</div>
                                    <div class="ringkas-title">{{ $pengumuman->judul }}</div>
                                    <div class="ringkas-sub">{{ $pengumuman->ringkasan(150) }}</div>
                                </div>
                                <span class="ringkas-sub" style="white-space: nowrap;">{{ $pengumuman->tanggalTeks() }}</span>
                            </a>
                        @empty
                            <div class="ringkas-kosong">Belum ada pengumuman yang dipublikasikan Musyrifah.</div>
                        @endforelse
                    </div>
                </div>

                <div class="card-detail-footer">
                    <a href="{{ route('pengumuman.mhs') }}" class="link-card-detail">Lihat semua pengumuman &rarr;</a>
                </div>
            </section>

            <!-- SECTION 6: PERIZINAN & NOTIFIKASI -->
            <div class="dashboard-grid" style="margin-bottom: 0;">
                <!-- Kartu 5: Status Perizinan -->
                <div class="simahad-card grid-card compact-height">
                    <div>
                        <div class="card-heading-maroon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            <span>Status Perizinan</span>
                        </div>

                        <div class="ringkas-sub">Total {{ $perizinan['total'] }} pengajuan surat dan perizinan.</div>

                        <div class="izin-chips">
                            <div class="izin-chip menunggu">
                                <span class="angka">{{ $perizinan['menunggu'] }}</span>
                                <span class="teks">Menunggu</span>
                            </div>
                            <div class="izin-chip disetujui">
                                <span class="angka">{{ $perizinan['disetujui'] }}</span>
                                <span class="teks">Disetujui</span>
                            </div>
                            <div class="izin-chip ditolak">
                                <span class="angka">{{ $perizinan['ditolak'] }}</span>
                                <span class="teks">Ditolak</span>
                            </div>
                            <div class="izin-chip selesai">
                                <span class="angka">{{ $perizinan['selesai'] }}</span>
                                <span class="teks">Selesai</span>
                            </div>
                        </div>

                        @if ($perizinan['terakhir'])
                            <div class="ringkas-sub" style="margin-top: 0.6rem;">
                                Terakhir: {{ $perizinan['terakhir']->labelStatus() }} &middot;
                                {{ $perizinan['terakhir']->created_at?->locale('id')->translatedFormat('d F Y') }}
                            </div>
                        @endif
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('surat.perizinan.mhs') }}" class="link-card-detail">Buka surat &amp; perizinan &rarr;</a>
                    </div>
                </div>

                <!-- Kartu 6: Notifikasi Terbaru -->
                <div class="simahad-card grid-card compact-height">
                    <div>
                        <div class="card-header-flex">
                            <div class="card-heading-maroon" style="margin-bottom: 0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <span>Notifikasi Terbaru</span>
                            </div>
                            @if ($jumlahNotifikasiBelumDibaca > 0)
                                <span class="badge-status terlambat">{{ $jumlahNotifikasiBelumDibaca }} belum dibaca</span>
                            @endif
                        </div>

                        <div class="notif-list" style="margin-top: 0.7rem;">
                            @forelse ($notifikasi as $itemNotif)
                                <form method="POST" action="{{ route('notifikasi.baca', $itemNotif) }}" class="notif-form">
                                    @csrf
                                    <button type="submit" class="notif-button {{ $itemNotif->sudahDibaca() ? '' : 'belum' }}">
                                        <span class="notif-ikon">{{ $itemNotif->ikon() }}</span>
                                        <span class="notif-body">
                                            <span class="notif-judul">
                                                @unless ($itemNotif->sudahDibaca())
                                                    <span class="notif-dot"></span>
                                                @endunless
                                                {{ $itemNotif->judul }}
                                            </span>
                                            @if (filled($itemNotif->pesan))
                                                <span class="notif-pesan">{{ \Illuminate\Support\Str::limit($itemNotif->pesan, 90) }}</span>
                                            @endif
                                            <span class="notif-waktu">{{ $itemNotif->created_at?->locale('id')->diffForHumans() }}</span>
                                        </span>
                                    </button>
                                </form>
                            @empty
                                <div class="ringkas-kosong">Belum ada notifikasi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Tombol salin nomor Virtual Account BSI.
        document.querySelectorAll('[data-copy-va]').forEach(function (tombol) {
            tombol.addEventListener('click', async function () {
                const nomor = tombol.dataset.copyVa || '';

                if (! nomor) {
                    return;
                }

                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(nomor);
                    } else {
                        const area = document.createElement('textarea');
                        area.value = nomor;
                        area.setAttribute('readonly', 'readonly');
                        area.style.position = 'fixed';
                        area.style.opacity = '0';
                        document.body.appendChild(area);
                        area.select();
                        document.execCommand('copy');
                        document.body.removeChild(area);
                    }

                    const teksAsli = tombol.innerHTML;
                    tombol.classList.add('is-copied');
                    tombol.textContent = 'Tersalin';
                    setTimeout(function () {
                        tombol.classList.remove('is-copied');
                        tombol.innerHTML = teksAsli;
                    }, 1800);
                } catch (error) {
                    tombol.textContent = 'Salin manual';
                }
            });
        });

        // Sidebar mobile.
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
</body>
</html>
