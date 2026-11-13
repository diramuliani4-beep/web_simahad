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
            --primary-maroon: #6b0d1b;
            --primary-maroon-hover: #580a16;
            --maroon-tint: #f8eff2;
            --maroon-border: #ecd9dd;
            --text-dark: #1f2430;
            --text-secondary: #4f5767;
            --text-muted: #6b7280;
            --line: #ecedf2;
            --bg-page: #f7f5f4;
            --header-height: 68px;
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
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 14px rgba(59, 7, 15, 0.05);
        }

        .navbar-brand-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            min-width: 0;
        }

        .brand-logo-img {
            height: 40px;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
        }

        .brand-teks {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            min-width: 0;
        }

        .brand-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 19px;
            font-weight: 800;
            color: var(--primary-maroon);
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .brand-sub {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: #c9a96a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

        /* ================= MAIN CONTENT ================= */
        .main-content {
            flex: 1;
            padding: 1.5rem 2.25rem 3rem 2.25rem;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-page);
            overflow-y: auto;
            min-width: 0;
        }

        .dashboard-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .dashboard-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .dashboard-eyebrow-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--primary-maroon);
        }

        .dashboard-date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #ffffff;
            color: var(--primary-maroon);
            font-size: 12.5px;
            font-weight: 700;
            box-shadow: 0 1px 2px rgba(31, 36, 48, 0.04);
        }

        .dashboard-date svg {
            width: 15px;
            height: 15px;
            color: var(--primary-maroon);
        }

        .mhs-hero {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
            padding: 32px 34px;
            border-radius: 20px;
            background:
                radial-gradient(120% 160% at 100% 0%, rgba(201, 169, 106, 0.22), transparent 58%),
                linear-gradient(135deg, #7d1022 0%, #5c0a17 100%);
            box-shadow: 0 16px 32px -18px rgba(96, 8, 28, 0.55);
            color: #ffffff;
            isolation: isolate;
            margin-bottom: 1.35rem;
        }

        .mhs-hero::before,
        .mhs-hero::after {
            content: "";
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 50%;
            pointer-events: none;
        }

        .mhs-hero::before {
            top: -220px;
            right: -96px;
            width: 360px;
            height: 360px;
            box-shadow: 0 0 0 44px rgba(255, 255, 255, 0.035), 0 0 0 96px rgba(255, 255, 255, 0.022);
        }

        .mhs-hero::after {
            right: 190px;
            bottom: -250px;
            width: 270px;
            height: 270px;
        }

        .mhs-hero-content {
            position: relative;
            z-index: 1;
            min-width: 0;
        }

        .mhs-hero-label {
            display: inline-flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 5px 11px;
            border: 1px solid rgba(255, 255, 255, 0.26);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .mhs-hero h1 {
            margin: 0 0 8px;
            font-size: clamp(21px, 2vw, 29px);
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.025em;
            color: #ffffff;
        }

        .mhs-hero p {
            margin: 0;
            max-width: 560px;
            font-size: 12.5px;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.78);
        }

        .mhs-hero-board {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 2px;
            min-width: 330px;
            padding: 6px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
        }

        .mhs-hero-stat {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 12px 14px;
            border-radius: 11px;
        }

        .mhs-hero-stat + .mhs-hero-stat {
            box-shadow: inset 1px 0 0 rgba(255, 255, 255, 0.16);
        }

        .mhs-hero-stat span {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.01em;
            color: rgba(255, 255, 255, 0.72);
        }

        .mhs-hero-stat strong {
            font-size: clamp(20px, 1.9vw, 26px);
            font-weight: 750;
            letter-spacing: -0.02em;
            line-height: 1.15;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
        }

        .simahad-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            background-color: #ffffff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .simahad-card:hover {
            box-shadow: 0 4px 14px rgba(107, 13, 27, 0.06);
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
            background-color: #f8eff2;
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
            color: #1f2430;
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
            color: #178154;
            line-height: 1.3;
        }

        .status-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 1rem;
            padding-top: 0.95rem;
            border-top: 1px dashed #e6e2dc;
        }

        .status-chip {
            display: inline-flex;
            flex-direction: column;
            gap: 0.15rem;
            min-width: 132px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ecedf2;
            border-radius: 10px;
            background: #ffffff;
        }

        .status-chip .chip-label {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #6b7280;
        }

        .status-chip .chip-value {
            font-size: 0.84rem;
            font-weight: 700;
            color: #1f2430;
            line-height: 1.35;
        }

        .status-chip.ok .chip-value {
            color: #22653a;
        }

        .status-chip.warning .chip-value {
            color: #8a5a0d;
        }

        .status-chip.danger .chip-value {
            color: #8a1224;
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
            color: #1f2430;
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
            color: #1f2430;
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
            color: #4f5767;
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
            background: #fff4df;
            color: #8a5a0d;
        }

        .badge-status.menunggu {
            background: #eaf0fa;
            color: #3d5f8f;
        }

        .badge-status.terlambat {
            background: #fdeef0;
            color: #8a1224;
        }

        .badge-status.lunas {
            background: #eef7f0;
            color: #22653a;
        }

        .badge-status.terkumpul {
            background: #eaf0fa;
            color: #3d5f8f;
        }

        .badge-status.dinilai {
            background: #eef7f0;
            color: #22653a;
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
            border: 1px solid #f2ccd2;
            background: #fdeef0;
            color: #8a1224;
        }

        .reminder-alert.info {
            border: 1px solid #d8e2f0;
            background: #eff4fb;
            color: #3d5f8f;
        }

        .reminder-alert.success {
            border: 1px solid #cfe6d5;
            background: #eef7f0;
            color: #22653a;
        }

        .va-inline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding: 0.55rem 0.7rem;
            border: 1px dashed #ded8d2;
            border-radius: 9px;
            background: #ffffff;
        }

        .va-inline .va-label {
            font-size: 0.66rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #6b7280;
        }

        .va-inline .va-number {
            font-size: 0.92rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            color: #1f2430;
            word-break: break-all;
        }

        .btn-copy-mini {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.35rem 0.6rem;
            border: 1px solid var(--line);
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
            border-color: #22653a;
            color: #22653a;
            background: #eef7f0;
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
            background-color: #cfe6d5;
            border: 1px solid #cfe6d5;
            color: #22653a;
        }

        .stat-pill.izin {
            background-color: #fff4df;
            border: 1px solid #f0dfc2;
            color: #8a5a0d;
        }

        .stat-pill.alpa {
            background-color: #fdeef0;
            border: 1px solid #f2ccd2;
            color: #8a1224;
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
            background-color: #ecedf2;
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 0.3rem;
        }

        .attendance-progress-bar {
            width: 0;
            height: 100%;
            background-color: #22653a;
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
            color: #22653a;
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
            color: #1f2430;
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
            border: 1px solid var(--line);
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
            background: #faf8f6;
            font-size: 0.8rem;
            font-weight: 600;
            color: #4f5767;
            text-decoration: none;
        }

        a.kalender-cell:hover {
            border-color: var(--maroon-border);
            background: #fbf9f7;
        }

        .kalender-cell.luar {
            background: transparent;
            color: #b9bcc6;
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
            border-top: 1px dashed #e6e2dc;
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
            border: 1px solid #ecedf2;
            border-radius: 9px;
            background: #ffffff;
        }

        .kalender-item + .kalender-item {
            margin-top: 0.4rem;
        }

        .kalender-item-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1f2430;
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
            border: 1px solid #ecedf2;
            border-radius: 9px;
            background: #ffffff;
            text-decoration: none;
            color: inherit;
        }

        .ringkas-item:hover {
            border-color: #ded8d2;
            background: #fbf9f7;
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
            color: #1f2430;
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
            background: #eef7f0;
            color: #22653a;
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
            border: 1px dashed #e6e2dc;
            border-radius: 10px;
            background: #ffffff;
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
            border: 1px solid #ecedf2;
            background: #ffffff;
            text-align: center;
        }

        .izin-chip .angka {
            display: block;
            font-size: 1.05rem;
            font-weight: 800;
            color: #1f2430;
            line-height: 1.2;
        }

        .izin-chip .teks {
            font-size: 0.66rem;
            font-weight: 700;
            color: var(--text-muted);
            line-height: 1.2;
        }

        .izin-chip.menunggu .angka { color: #8a5a0d; }
        .izin-chip.disetujui .angka { color: #22653a; }
        .izin-chip.ditolak .angka { color: #8a1224; }
        .izin-chip.selesai .angka { color: #3d5f8f; }

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
            border: 1px solid #ecedf2;
            border-radius: 9px;
            background: #ffffff;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
        }

        .notif-button:hover {
            background: #fbf9f7;
            border-color: #ded8d2;
        }

        .notif-button.belum {
            background: #fdf6f7;
            border-color: #f0dde2;
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
            color: #1f2430;
            line-height: 1.35;
        }

        .notif-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #8d1c2e;
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
            color: #6b7280;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 991px) {
            .mobile-toggle-btn {
                display: block;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 1.5rem 1.25rem 2.5rem 1.25rem;
            }

            .mhs-hero {
                flex-direction: column;
                align-items: stretch;
                gap: 20px;
                padding: 25px;
            }

            .mhs-hero-board {
                min-width: 0;
            }
        }

        @media (max-width: 560px) {
            .brand-title {
                font-size: 17px;
            }

            .brand-sub {
                display: none;
            }

            .brand-logo-img {
                height: 34px;
            }

            .top-navbar {
                padding: 0 14px;
            }

            .dashboard-topline {
                margin-bottom: 14px;
            }

            .dashboard-date {
                padding: 7px 12px;
                font-size: 11.5px;
            }

            .mhs-hero {
                padding: 22px 20px;
                border-radius: 16px;
            }

            .mhs-hero h1 {
                font-size: 22px;
            }

            .mhs-hero-stat {
                padding: 10px 10px;
            }

            .mhs-hero-stat span {
                font-size: 10.5px;
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
            <button type="button" class="mobile-toggle-btn" id="menuToggle" aria-label="Buka atau tutup menu" aria-controls="sidebar" aria-expanded="false">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="navbar-brand-wrap">
                <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD" class="brand-logo-img">
                <span class="brand-teks">
                    <span class="brand-title">SIMAHAD</span>
                    <span class="brand-sub">Ma'had Al-Jami'ah UIN Imam Bonjol</span>
                </span>
            </a>
        </div>

        <div class="navbar-actions">
            <x-notifikasi-dropdown />
            <x-mahasiswa.profile-dropdown />
        </div>
    </header>

    <div class="dashboard-body">
        {{-- Sidebar bersama: identitas, menu aktif, dan laci mobile dikelola komponen panel.sidebar. --}}
        <x-panel.sidebar ragam="alir" atas="68px" />
        <!-- ================= MAIN CONTENT ================= -->
        <main class="main-content">
            <div class="dashboard-topline">
                
                <span class="dashboard-date">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M16 3v4M8 3v4M3 11h18"></path>
                    </svg>
                    {{ now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}
                </span>
            </div>

            @if (session('success'))
                <div style="margin-bottom:1rem; padding:0.7rem 0.9rem; border:1px solid #cfe6d5; border-radius:10px; background:#eef7f0; color:#22653a; font-size:0.82rem; font-weight:600;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="margin-bottom:1rem; padding:0.7rem 0.9rem; border:1px solid #f2ccd2; border-radius:10px; background:#fdeef0; color:#8a1224; font-size:0.82rem; font-weight:600;">
                    {{ $errors->first() }}
                </div>
            @endif

            @php
                $pembayaran = $ringkasanPembayaran;
                $pengaturanBayar = $pembayaran['pengaturan'] ?? null;
                $statusPendaftaran = $pendaftaran?->status ? ucwords(str_replace('_', ' ', $pendaftaran->status)) : 'Belum Mendaftar';
                $statusAkun = $user->status ? ucwords(str_replace('_', ' ', $user->status)) : 'Belum Diketahui';
                $kelasStatusAkun = in_array(strtolower((string) $user->status), ['aktif'], true) ? 'ok' : 'warning';
                $programStudi = $pendaftaran?->program_studi ?: 'Mahasiswa Ma\'had';
                $angkatan = $pendaftaran?->tahun_angkatan ?: '-';
                // Persentase memakai pemisah desimal titik, sama seperti halaman
                // pembelajaran dan absensi mahasiswa lainnya.
                $persentaseKehadiran = $absensi['persentase'];
                $lebarKehadiran = min(100, (float) $absensi['persentase']);
            @endphp

            <header class="mhs-hero">
                <div class="mhs-hero-content">
                    <span class="mhs-hero-label">Mahasiswa</span>
                    <h1>Assalamu’alaikum, {{ $user->name }}</h1>
                    <p>Berikut ringkasan aktivitas pembelajaran dan keasramaan Anda di Ma’had Al-Jami’ah.</p>
                </div>
                <div class="mhs-hero-board" role="group" aria-label="Ringkasan hari ini">
                    <div class="mhs-hero-stat">
                        <span>Kehadiran</span>
                        <strong>{{ $persentaseKehadiran }}%</strong>
                    </div>
                    <div class="mhs-hero-stat">
                        <span>Pertemuan</span>
                        <strong>{{ number_format($absensi['total'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="mhs-hero-stat">
                        <span>Tugas belum</span>
                        <strong>{{ number_format($jumlahTugasBelum, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </header>

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
                                <div class="tagihan-empty-title">Periode Pembayaran</div>
                                <div class="tagihan-empty-desc">
                                    Belum ada periode pembayaran aktif. Silakan menunggu Musyrifah membuka periode pembayaran.
                                </div>
                                @if ($pengaturanBayar)
                                    <div class="tagihan-empty-desc">
                                        Periode {{ $pengaturanBayar->periode }} ({{ $pengaturanBayar->nama_tahap }}) akan dibuka pada {{ $pengaturanBayar->periodeDibukaTeks() }}.
                                    </div>
                                @endif
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
                                            <div class="va-label">Rekening {{ $pengaturanBayar->va_bank ?: 'BSI' }}</div>
                                            <div class="va-number">{{ $pembayaran['va_nomor'] }}</div>
                                        </div>
                                        <button type="button" class="btn-copy-mini" data-copy-va="{{ $pembayaran['va_nomor'] }}">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                            Salin No. Rek
                                        </button>
                                    </div>
                                @endif

                                @if ($pembayaran['status'] === 'lunas')
                                    <a href="{{ route('pembayaran') }}" class="reminder-alert success">
                                        Tagihan sudah dikonfirmasi Lunas oleh Rektorat. Pengingat dihentikan.
                                    </a>
                                @elseif ($pembayaran['status'] === 'terlambat')
                                    <a href="{{ route('pembayaran') }}" class="reminder-alert danger">
                                        Tagihan melewati jatuh tempo {{ $pembayaran['jatuh_tempo'] }}. Klik untuk detail pembayaran.
                                    </a>
                                @elseif ($pembayaran['status'] === 'menunggu')
                                    <a href="{{ route('pembayaran') }}" class="reminder-alert info">
                                        Menunggu konfirmasi Rektorat. Status Lunas mengikuti rekap pembayaran resmi.
                                    </a>
                                @elseif ($pembayaran['pengingat'])
                                    <a href="{{ route('pembayaran') }}" class="reminder-alert danger">
                                        Pengingat: jatuh tempo tinggal {{ $pembayaran['sisa_hari'] }} hari ({{ $pembayaran['jatuh_tempo'] }}).
                                    </a>
                                @else
                                    <a href="{{ route('pembayaran') }}" class="reminder-alert info">
                                        Tagihan belum dibayar. Bayar sebelum {{ $pembayaran['jatuh_tempo'] }}.
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('pembayaran') }}" class="link-card-detail">Detail pembayaran &rarr;</a>
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
                            <div class="attendance-progress-bar" data-lebar="{{ $lebarKehadiran }}"></div>
                        </div>
                        <div class="attendance-progress-footer">
                            <span>Persentase kehadiran</span>
                            <span class="attendance-percentage-val">{{ $persentaseKehadiran }}%</span>
                        </div>
                    </div>

                    <div class="card-detail-footer">
                        <a href="{{ route('pembelajaran.mhs') }}" class="link-card-detail">Rincian kehadiran per mata pelajaran &rarr;</a>
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
        // Lebar bar kehadiran dari data server.
        document.querySelectorAll('[data-lebar]').forEach(function (bar) {
            bar.style.width = bar.dataset.lebar + '%';
        });

        // Tombol salin nomor rekening BSI resmi.
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

        // Buka/tutup laci sidebar ditangani komponen panel.sidebar.
    </script>
</body>
</html>
