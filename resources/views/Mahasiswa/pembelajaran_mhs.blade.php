<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelajaran Mahasiswa | SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --maroon: #790c20;
            --maroon-dark: #5e0817;
            --maroon-light: #f8edef;
            --border: #8a1428;
            --text: #171717;
            --muted: #64748b;
            --page: #f8f8f8;
            --white: #fff;
            --success: #15803d;
            --success-bg: #dcfce7;
            --warning: #b45309;
            --warning-bg: #fef3c7;
            --danger: #b91c1c;
            --danger-bg: #fee2e2;
            --info: #1d4ed8;
            --info-bg: #dbeafe;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--page);
            font-size: 14px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font: inherit;
        }

        /* =========================
           HEADER
        ========================= */
        .topbar {
            height: 89px;
            background: #fff;
            border-bottom: 1px solid #edd5db;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 31px 0 20px;
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(121, 12, 32, 0.05);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 65px;
            height: 62px;
            color: var(--maroon);
        }

        .brand-name {
            font-size: 30px;
            font-weight: 700;
            color: var(--maroon);
            letter-spacing: -.8px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--maroon-light);
            padding: 6px 14px;
            border-radius: 9999px;
            border: 1px solid #f1d5dc;
        }

        .user-name-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--maroon);
        }

        .user-nim-text {
            font-size: 11px;
            color: #666;
        }

        .notification {
            width: 36px;
            height: 36px;
            background: var(--maroon);
            border: none;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: white;
            cursor: pointer;
        }

        .notification svg {
            width: 20px;
            height: 20px;
            stroke: white;
            fill: none;
            stroke-width: 1.8;
        }

        .profile {
            width: 48px;
            height: 48px;
            border: 1.5px solid #222;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .profile svg {
            width: 44px;
            height: 44px;
        }

        /* =========================
           SIDEBAR
        ========================= */
        .sidebar {
            position: fixed;
            top: 89px;
            bottom: 0;
            left: 0;
            width: 300px;
            overflow-y: auto;
            border-right: 1px solid var(--border);
            background:
                linear-gradient(
                    to bottom,
                    rgba(255,255,255,.35),
                    rgba(255,255,255,.68)
                ),
                url("{{ asset('images/mahad_al_jamiah.jpeg') }}");
            background-size: cover;
            background-position: center center;
            display: flex;
            flex-direction: column;
            z-index: 50;
        }

        .sidebar-menu {
            padding: 22px 12px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .nav-pill {
            min-height: 46px;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: rgba(255,255,255,.97);
            color: #111;
            font-size: 14px;
            font-weight: 500;
            transition: background .2s, color .2s;
            cursor: pointer;
        }

        .nav-pill:hover {
            background: var(--maroon-light);
            color: var(--maroon);
        }

        .nav-pill.active {
            background: var(--maroon-light);
            color: var(--maroon);
            box-shadow: 0 3px 5px rgba(0,0,0,.12);
            font-weight: 700;
        }

        .nav-icon {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            color: #626d7e;
        }

        .nav-pill.active .nav-icon {
            color: var(--maroon);
        }

        .nav-icon svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .nav-label {
            flex: 1;
        }

        .nav-arrow {
            color: var(--maroon);
            font-size: 22px;
            line-height: 1;
            font-weight: 700;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 18px 12px 20px;
            border-top: 1px solid var(--border);
        }

        .sidebar-bottom .nav-pill {
            margin-bottom: 10px;
        }

        .sidebar-bottom .nav-pill:last-child {
            margin-bottom: 0;
        }

        /* =========================
           MAIN CONTENT
        ========================= */
        .main {
            margin-left: var(--sb-w, 280px);
            transition: margin-left .22s ease;
            padding: 108px 36px 40px;
            min-height: 100vh;
            min-width: 0;
        }

        body.sb-sempit .main {
            margin-left: var(--sb-w-sempit, 84px);
        }

        .page-header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 22px;
        }

        .breadcrumb {
            font-size: 14px;
            font-weight: 600;
            color: var(--maroon);
            margin-bottom: 4px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.25;
            color: #0f172a;
        }

        .page-description {
            font-size: 14px;
            color: var(--muted);
            margin-top: 4px;
            max-width: 780px;
            line-height: 1.6;
        }

        .sync-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }

        .sync-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        /* =========================
           STATISTIK GRID
        ========================= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 16px 18px;
            border: 1px solid #edd5db;
            box-shadow: 0 2px 6px rgba(121, 12, 32, 0.04);
            border-left: 5px solid var(--maroon);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card:nth-child(2) { border-left-color: #2563eb; }
        .stat-card:nth-child(3) { border-left-color: #10b981; }
        .stat-card:nth-child(4) { border-left-color: #f59e0b; }

        .stat-icon {
            width: 28px;
            height: 28px;
            color: var(--maroon);
            margin-bottom: 8px;
        }

        .stat-icon svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .stat-number {
            font-size: 24px;
            line-height: 1.2;
            font-weight: 700;
            color: #0f172a;
        }

        .stat-label {
            color: var(--muted);
            font-size: 13px;
            margin-top: 4px;
            font-weight: 500;
        }

        .stat-subtext {
            font-size: 11px;
            color: #888;
            margin-top: 4px;
        }

        /* =========================
           TABS NAVIGASI
        ========================= */
        .tabs-header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 24px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .tabs-nav {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 18px;
            border: none;
            border-bottom: 3px solid transparent;
            background: transparent;
            color: #64748b;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
            border-radius: 8px 8px 0 0;
        }

        .tab-btn:hover {
            color: var(--maroon);
            background: #fbf1f3;
        }

        .tab-btn.active {
            color: var(--maroon);
            border-bottom-color: var(--maroon);
            background: #fbf1f3;
        }

        .kelompok-select-form {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .kelompok-select-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .kelompok-select {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            color: var(--maroon);
            background: #fff;
            outline: none;
            cursor: pointer;
        }

        .kelompok-select:focus {
            border-color: var(--maroon);
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.2s ease;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* =========================
           CARDS & CONTENT
        ========================= */
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            margin-bottom: 22px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-desc {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        /* =========================
           GRID 5 MATA PELAJARAN
        ========================= */
        .mapel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 18px;
        }

        .mapel-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            position: relative;
            overflow: hidden;
        }

        .mapel-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(121, 12, 32, 0.08);
            border-color: #edd5db;
        }

        .mapel-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-maroon { background: var(--maroon-light); color: var(--maroon); border: 1px solid #f1d5dc; }
        .badge-blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-green { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-gray { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .mapel-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.4;
            margin-bottom: 6px;
        }

        .mapel-meta-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .mapel-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .mapel-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 14px;
        }

        .progress-block {
            background: #f8fafc;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 14px;
            border: 1px solid #f1f5f9;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .progress-header strong {
            color: #1e293b;
        }

        .progress-bar-wrap {
            height: 8px;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #790c20, #b91c1c);
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        .progress-bar-fill.complete {
            background: linear-gradient(90deg, #15803d, #10b981);
        }

        .progress-pills {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            flex-wrap: wrap;
        }

        .pill-stat {
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        .pill-stat.hadir { background: #dcfce7; color: #166534; }
        .pill-stat.izin { background: #e0f2fe; color: #075985; }
        .pill-stat.sakit { background: #fef3c7; color: #92400e; }
        .pill-stat.alpa { background: #fee2e2; color: #991b1b; }

        .btn-view-detail {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 9px 14px;
            border-radius: 8px;
            border: 1px solid #edd5db;
            background: #fff;
            color: var(--maroon);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-view-detail:hover {
            background: var(--maroon);
            color: #fff;
            border-color: var(--maroon);
        }

        /* =========================
           TABEL & JADWAL
        ========================= */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #edd5db;
        }

        .custom-table {
            width: 100%;
            min-width: 720px;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
        }

        .custom-table thead th {
            padding: 12px 14px;
            background: #790c20;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
        }

        .custom-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            font-size: 13px;
            color: #334155;
        }

        .custom-table tbody tr:hover td {
            background: #fdf8f9;
        }

        /* JADWAL TERDEKAT CARDS */
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .schedule-card-item {
            background: #fff;
            border: 1px solid #edd5db;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow 0.2s;
        }

        .schedule-card-item:hover {
            box-shadow: 0 4px 12px rgba(121, 12, 32, 0.08);
        }

        .schedule-time-box {
            width: 82px;
            min-width: 82px;
            height: 78px;
            border-radius: 10px;
            background: var(--maroon-light);
            color: var(--maroon);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .schedule-day-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .schedule-date-num {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.1;
        }

        .schedule-details h4 {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            line-height: 1.35;
        }

        .schedule-details p {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        /* =========================
           KELULUSAN & SERTIFIKAT
        ========================= */
        .graduation-banner {
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 18px;
        }

        .graduation-banner.lulus {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #86efac;
        }

        .graduation-banner.proses {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #fde68a;
        }

        .grad-icon-wrap {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .graduation-banner.lulus .grad-icon-wrap {
            background: #15803d;
            color: #fff;
        }

        .graduation-banner.proses .grad-icon-wrap {
            background: #b45309;
            color: #fff;
        }

        .grad-content {
            flex: 1;
            min-width: 260px;
        }

        .grad-title {
            font-size: 19px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .graduation-banner.lulus .grad-title { color: #14532d; }
        .graduation-banner.proses .grad-title { color: #78350f; }

        .grad-desc {
            font-size: 13px;
            line-height: 1.6;
        }

        .graduation-banner.lulus .grad-desc { color: #166534; }
        .graduation-banner.proses .grad-desc { color: #92400e; }

        .btn-certificate {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            background: #15803d;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(21, 128, 61, 0.25);
            transition: all 0.2s;
        }

        .btn-certificate:hover {
            background: #166534;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(21, 128, 61, 0.35);
        }

        .btn-certificate:disabled {
            background: #cbd5e1;
            color: #64748b;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* =========================
           KALENDER
        ========================= */
        .calendar-card {
            width: 100%;
            max-width: 680px;
            background: #fff;
            border: 1px solid #edd5db;
            border-radius: 14px;
            padding: 18px 20px;
        }

        .calendar-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .calendar-heading h3 {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .calendar-toolbar {
            display: grid;
            grid-template-columns: 32px 1fr 32px;
            align-items: center;
            text-align: center;
            margin-bottom: 12px;
        }

        .calendar-toolbar strong {
            font-size: 14px;
            font-weight: 700;
            color: var(--maroon);
        }

        .calendar-toolbar button {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: var(--maroon);
            font-size: 18px;
            cursor: pointer;
            height: 32px;
            display: grid;
            place-items: center;
        }

        .calendar-toolbar button:hover {
            background: var(--maroon-light);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
        }

        .day-name {
            text-align: center;
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
            padding: 4px 0 8px;
        }

        .calendar-day {
            min-height: 42px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            border-radius: 8px;
            color: #334155;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s;
        }

        .calendar-day:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .calendar-day.muted {
            color: #cbd5e1;
            cursor: default;
        }

        .calendar-day.today {
            border: 2px solid var(--maroon);
            font-weight: 700;
        }

        .calendar-day.has-event {
            background: var(--maroon-light);
            border-color: #f1d5dc;
            color: var(--maroon);
            font-weight: 700;
        }

        .calendar-day.has-event::after {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--maroon);
            position: absolute;
            bottom: 4px;
        }

        .calendar-legend {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--muted);
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            background: var(--maroon);
            border-radius: 50%;
        }

        .calendar-info-box {
            margin-top: 14px;
            padding: 12px 14px;
            background: #fdf8f9;
            border-radius: 10px;
            border: 1px solid #edd5db;
            font-size: 13px;
        }

        /* =========================
           MODALS
        ========================= */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-card {
            background: #fff;
            border-radius: 18px;
            max-width: 740px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            transform: scale(0.96);
            transition: transform 0.25s ease;
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        .modal-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            background: #fdf8f9;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        .modal-top-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--maroon);
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 26px;
            color: #64748b;
            cursor: pointer;
            line-height: 1;
        }

        .modal-inner {
            padding: 22px 24px;
        }

        .modal-footer-btns {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            border-bottom-left-radius: 18px;
            border-bottom-right-radius: 18px;
        }

        .btn-modal-cancel {
            padding: 9px 18px;
            background: #e2e8f0;
            color: #334155;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-modal-action {
            padding: 9px 18px;
            background: var(--maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .pertemuan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
            margin-top: 14px;
            max-height: 380px;
            overflow-y: auto;
            padding: 4px;
        }

        .pertemuan-card-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            text-align: center;
        }

        .pertemuan-card-num {
            font-size: 13px;
            font-weight: 700;
            color: var(--maroon);
        }

        .pertemuan-card-date {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .pertemuan-card-badge {
            display: inline-block;
            margin-top: 6px;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 9999px;
            font-weight: 700;
        }

        /* RINGKASAN KEHADIRAN PADA MODAL MATA PELAJARAN */
        .abs-ringkasan {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(92px, 1fr));
            gap: 8px;
            margin-bottom: 12px;
        }

        .abs-stat {
            padding: 8px 10px;
            border: 1px solid #e6dde0;
            border-radius: 10px;
            background: #fff;
            text-align: center;
        }

        .abs-stat-label {
            display: block;
            color: #8b7e84;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .abs-stat strong {
            display: block;
            margin-top: 2px;
            color: var(--maroon);
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
        }

        .abs-stat.hadir strong { color: #15803d; }
        .abs-stat.izin strong { color: #1d4ed8; }
        .abs-stat.sakit strong { color: #b45309; }
        .abs-stat.alpa strong { color: #b91c1c; }

        .abs-stat--persen {
            border-color: #e6d7b8;
            background: #fffdf7;
        }

        .abs-stat-note {
            display: block;
            margin-top: 2px;
            color: #8b7e84;
            font-size: 10.5px;
            line-height: 1.35;
        }

        .abs-periode {
            margin-bottom: 12px;
            border: 1px solid #efe5e7;
            border-radius: 10px;
            overflow: hidden;
        }

        .abs-periode-item {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px 10px;
            padding: 7px 11px;
            border-bottom: 1px solid #f3ebed;
            font-size: 11.5px;
        }

        .abs-periode-item:last-child {
            border-bottom: none;
        }

        .abs-periode-nama {
            flex: 1 1 140px;
            min-width: 0;
            color: #322a2d;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .abs-periode-detail {
            color: #8b7e84;
            font-size: 11px;
        }

        .abs-periode-persen {
            padding: 2px 9px;
            border-radius: 999px;
            background: #fdf6e8;
            color: #6B0D1B;
            font-size: 11px;
            font-weight: 700;
        }

        @media (max-width: 560px) {
            .abs-ringkasan {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .modal-inner {
                padding: 16px;
            }
        }

        /* SERTIFIKAT MODAL */
        .certificate-wrapper {
            max-width: 680px;
            text-align: center;
            border: 8px double #800d22;
            background: #fffcf8;
            padding: 30px 40px;
            position: relative;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #printableCertificate, #printableCertificate * {
                visibility: visible;
            }
            #printableCertificate {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: 8px double #800d22 !important;
                background: #fffcf8 !important;
                padding: 40px !important;
            }
            .modal-footer-btns, .modal-close-btn {
                display: none !important;
            }
        }

        /* =========================
           RESPONSIVE MOBILE
        ========================= */
        .mobile-toggle,
        .mobile-overlay {
            display: none;
        }

        @media (max-width: 1100px) {
            .main { padding: 100px 24px 30px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* Di bawah 992px sidebar bersama menjadi laci dan konten memakai lebar penuh. */
        @media (max-width: 991px) {
            .main,
            body.sb-sempit .main {
                margin-left: 0;
            }

            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border: 1px solid #e5d4d8;
                border-radius: 8px;
                padding: 7px 12px;
                background: white;
                color: var(--maroon);
                margin-bottom: 14px;
                cursor: pointer;
                font-weight: 600;
            }

            .mobile-overlay.open {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .4);
                z-index: 930;
            }
        }

        @media (max-width: 768px) {
            .topbar { height: 72px; padding: 0 16px; }
            .brand-name { font-size: 22px; }
            .brand-logo { width: 44px; height: 42px; }
            .user-info-pill { display: none; }
            .main { padding: 86px 16px 30px; }
            .page-title { font-size: 24px; }
            .stats-grid { grid-template-columns: 1fr; }
            .mapel-grid { grid-template-columns: 1fr; }
            .graduation-banner { flex-direction: column; text-align: center; }
            .grad-icon-wrap { margin: 0 auto; }
            .tabs-header-wrap { flex-direction: column; align-items: flex-start; }
            .kelompok-select-form { width: 100%; justify-content: space-between; }
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <header class="topbar">
        <a href="{{ route('dashboard') }}" class="brand" aria-label="SIMAHAD - Dashboard">
            <svg class="brand-logo" viewBox="0 0 70 65" fill="none" aria-label="logo_uin.png">
                <path d="M35 3L64 25L58 53L35 63L12 53L6 25L35 3Z" stroke="#B99124" stroke-width="5"/>
                <path d="M35 13L54 28L50 47L35 56L20 47L16 28L35 13Z" stroke="#790C20" stroke-width="4"/>
                <path d="M24 30H46V45H24Z" fill="#790C20"/>
                <path d="M28 26L35 20L42 26V42L35 47L28 42V26Z" stroke="#fff" stroke-width="2"/>
                <path d="M31 31H39M31 35H39M31 39H39" stroke="#fff" stroke-width="1.5"/>
            </svg>
            <span class="brand-name">SIMAHAD</span>
        </a>

        <div class="top-actions">
            <div class="user-info-pill">
                <div>
                    <div class="user-name-text">{{ $user?->name ?? 'Mahasiswa' }}</div>
                    <div class="user-nim-text">{{ !empty($user?->nim) ? 'NIM. ' . $user->nim : ($kelompokSaya ?? 'Santri Ma\'had') }}</div>
                </div>
            </div>

            <button class="notification" type="button" aria-label="Notifikasi">
                <svg viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>
            </button>

            <x-mahasiswa.profile-dropdown />
        </div>
    </header>

    <!-- OVERLAY MOBILE -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    {{-- Sidebar bersama seluruh role SIMAHAD. --}}
    <x-panel.sidebar ragam="tetap" atas="89px" />

    <!-- KONTEN UTAMA -->
    <main class="main">
        <button class="mobile-toggle" type="button" onclick="toggleSidebar()" aria-controls="sidebar" aria-expanded="false" id="sidebarToggle">
            ☰ Menu
        </button>

        <div class="page-header-row">
            <div>
                <div class="breadcrumb">Dashboard / Pembelajaran Mahasiswa</div>
                <h1 class="page-title">Pembelajaran Mahasiswa</h1>
                <p class="page-description">
                    Informasi 5 mata pelajaran kurikulum Ma'had Al-Jami'ah, jadwal tatap muka kelompok belajar, progres pemantauan 16 pertemuan, dan status sertifikat kelulusan.
                </p>
            </div>
            <div>
                <div class="sync-badge" title="Data tersinkronisasi otomatis dengan pengaturan pembelajaran Musyrifah">
                    <span class="sync-dot"></span>
                    
                </div>
            </div>
        </div>

        <!-- STATISTIK RINGKASAN PEMBELAJARAN SAYA -->
        <section class="stats-grid" aria-label="Statistik Pembelajaran">
            <div class="stat-card">
                <div>
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21z"/><path d="M4 5.5v13A2.5 2.5 0 0 1 6.5 16H20M8 7h8M8 10h8"/></svg>
                    </div>
                    <div class="stat-number">{{ $totalMataPelajaran }}</div>
                    <div class="stat-label">Mata Pelajaran Kurikulum</div>
                </div>
                <div class="stat-subtext">5 Program Pembelajaran Wajib</div>
            </div>

            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="color: #2563eb;">
                        <svg viewBox="0 0 24 24"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20"/><circle cx="10" cy="7" r="3.5"/><path d="M17 4.2a3.5 3.5 0 0 1 0 6.6M17 15h1.5a3.5 3.5 0 0 1 3.5 3.5V20"/></svg>
                    </div>
                    <div class="stat-number">{{ $kelompokSaya }}</div>
                    <div class="stat-label">Kelompok Belajar Saya</div>
                </div>
                <div class="stat-subtext">Dari total 6 kelompok belajar</div>
            </div>

            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="color: #10b981;">
                        <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M12 13v3l2 1"/></svg>
                    </div>
                    <div class="stat-number">16 TM</div>
                    <div class="stat-label">Target per Mata Pelajaran</div>
                </div>
                <div class="stat-subtext">Total Target: {{ $totalTargetKehadiran }} Pertemuan</div>
            </div>

            <div class="stat-card">
                <div>
                    <div class="stat-icon" style="color: #f59e0b;">
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <div class="stat-number">{{ $totalHadirSeluruh }} / {{ $totalTargetKehadiran }}</div>
                    <div class="stat-label">Total Kehadiran Saya</div>
                </div>
                <div class="stat-subtext">
                    @if($isLulus)
                        <span style="color: #15803d; font-weight: 700;">✓ Memenuhi Syarat Kelulusan</span>
                    @else
                        <span style="color: #b45309; font-weight: 700;">⏳ Dalam Proses (Kurang {{ $mapelBelumLulus }} Mapel)</span>
                    @endif
                </div>
            </div>
        </section>

        <!-- TABS NAVIGASI FITUR PEMBELAJARAN SINKRON -->
        <div class="tabs-header-wrap">
            <nav class="tabs-nav" aria-label="Tab Menu Pembelajaran">
                <button type="button" class="tab-btn active" data-tab="tab-mapel" onclick="switchTab('tab-mapel', this)">
                    📚 1. Mata Pelajaran &amp; Progres 16 TM
                </button>
                <button type="button" class="tab-btn" data-tab="tab-jadwal" onclick="switchTab('tab-jadwal', this)">
                    🗓️ 2. Jadwal Pembelajaran Saya
                </button>
                <button type="button" class="tab-btn" data-tab="tab-kelompok" onclick="switchTab('tab-kelompok', this)">
                    👥 3. Kelompok &amp; Pengajar
                </button>
                <button type="button" class="tab-btn" data-tab="tab-kelulusan" onclick="switchTab('tab-kelulusan', this)">
                    🎓 4. Kelulusan &amp; Sertifikat
                </button>
                <button type="button" class="tab-btn" data-tab="tab-kalender" onclick="switchTab('tab-kalender', this)">
                    📅 5. Kalender Pembelajaran
                </button>
            </nav>

            <form action="{{ route('pembelajaran.mhs') }}" method="GET" class="kelompok-select-form">
                <label for="kelompokFilter" class="kelompok-select-label">Pilih Kelompok:</label>
                <select name="kelompok" id="kelompokFilter" class="kelompok-select" onchange="this.form.submit()">
                    @foreach($daftarKelompok as $k)
                        <option value="{{ $k->nama_kelompok }}" {{ $kelompokSaya === $k->nama_kelompok ? 'selected' : '' }}>
                            {{ $k->nama_kelompok }} {{ $k->nama_kelompok === ($user?->kamar ? 'Kelompok ' . $nomorKelompokSaya : 'Kelompok 1') ? '(Saya)' : '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- ==================================================== -->
        <!-- TAB 1: 5 MATA PELAJARAN & PROGRES 16 PERTEMUAN       -->
        <!-- ==================================================== -->
        <section class="tab-pane active" id="tab-mapel">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">5 Mata Pelajaran Kurikulum Ma'had Al-Jami'ah</h2>
                        <p class="card-desc">Pantau capaian 16 pertemuan tatap muka di masing-masing program pembelajaran wajib.</p>
                    </div>
                    <span class="badge-pill badge-maroon" style="font-size: 13px;">Kelompok: {{ $kelompokSaya }}</span>
                </div>

                <div class="mapel-grid">
                    @forelse($daftarMataPelajaran as $m)
                        <div class="mapel-card">
                            <div>
                                <div class="mapel-card-top">
                                    <span class="badge-pill badge-blue">{{ $m['kategori'] }}</span>
                                    <span class="badge-pill badge-maroon" style="font-weight: 800;">{{ $m['kode'] }}</span>
                                </div>

                                <h3 class="mapel-title">{{ $m['nama'] }}</h3>
                                <p class="mapel-desc">{{ $m['deskripsi'] ?: 'Program pembelajaran penguatan keilmuan ma\'had al-jami\'ah.' }}</p>

                                <div class="mapel-meta-row">
                                    <div class="mapel-meta-item">
                                        <span>👤 <strong>{{ $m['guru_pengampu'] }}</strong></span>
                                    </div>
                                    <div class="mapel-meta-item">
                                        <span>🗓️ {{ $m['hari'] }}, {{ $m['jam'] }}</span>
                                    </div>
                                    <div class="mapel-meta-item">
                                        <span>📍 {{ $m['ruangan'] }}</span>
                                    </div>
                                </div>

                                <div class="progress-block">
                                    <div class="progress-header">
                                        <span>Progres Kehadiran 16 TM:</span>
                                        <strong>{{ $m['hadir'] }} / 16 TM ({{ $m['persentase'] }}%)</strong>
                                    </div>
                                    <div class="progress-bar-wrap">
                                        <div class="progress-bar-fill {{ $m['is_lulus'] ? 'complete' : '' }}" style="width: {{ $m['persentase'] }}%;"></div>
                                    </div>
                                    <div class="progress-pills">
                                        <span class="pill-stat hadir">Hadir: {{ $m['hadir'] }}</span>
                                        <span class="pill-stat izin">Izin: {{ $m['izin'] }}</span>
                                        <span class="pill-stat sakit">Sakit: {{ $m['sakit'] }}</span>
                                        <span class="pill-stat alpa">Alpa: {{ $m['alpa'] }}</span>
                                        @if($m['is_lulus'])
                                            <span class="badge-pill badge-green" style="margin-left: auto;">✓ Memenuhi Syarat</span>
                                        @else
                                            <span class="badge-pill badge-amber" style="margin-left: auto;">Kurang {{ $m['sisa_pertemuan'] }} TM</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn-view-detail" onclick="openDetailPertemuan({{ $m['id'] }}, '{{ addslashes($m['nama']) }}', '{{ $m['kode'] }}')">
                                Lihat Kehadiran &amp; Pertemuan
                            </button>
                        </div>
                    @empty
                        <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">
                            Belum ada mata pelajaran terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 2: JADWAL PEMBELAJARAN SAYA & MINGGUAN           -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-jadwal">
            <!-- JADWAL TERDEKAT / HARI INI -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Jadwal Tatap Muka Terdekat (Hari Ini &amp; Mendatang)</h2>
                        <p class="card-desc">Jadwal pembelajaran aktif untuk {{ $kelompokSaya }} yang telah disinkronkan dari Musyrifah.</p>
                    </div>
                </div>

                <div class="schedule-grid">
                    @forelse($jadwalTerdekat as $jdw)
                        <div class="schedule-card-item">
                            <div class="schedule-time-box">
                                <span class="schedule-day-label">{{ $jdw['status_hari'] }}</span>
                                <span class="schedule-date-num">{{ $jdw['tanggal'] }}</span>
                                <span style="font-size: 10px; font-weight: 600;">{{ $jdw['bulan'] }}</span>
                            </div>

                            <div class="schedule-details">
                                <h4>{{ $jdw['nama'] }}</h4>
                                <p>⏰ <strong>{{ $jdw['waktu'] }}</strong></p>
                                <p>👤 Guru: {{ $jdw['guru'] }}</p>
                                <p>📍 Ruangan: {{ $jdw['lokasi'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1/-1; text-align: center; padding: 30px; color: #888;">
                            Belum ada jadwal tatap muka dalam waktu dekat.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- JADWAL LENGKAP KELOMPOK SAYA -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Daftar Jadwal Lengkap: {{ $kelompokSaya }}</h2>
                        <p class="card-desc">Jadwal mingguan mata pelajaran yang diampu pengajar Ma'had.</p>
                    </div>
                    <span class="badge-pill badge-green">{{ count($jadwalSaya) }} Sesi Terjadwal</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelompok</th>
                                <th>Guru Pengampu</th>
                                <th>Ruangan</th>
                                <th>Periode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalSaya as $idx => $js)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td style="font-weight: 700; color: var(--maroon);">{{ $js->hari }}</td>
                                    <td style="font-weight: 600;">{{ $js->jam_mulai }} – {{ $js->jam_selesai }} WIB</td>
                                    <td style="text-align: left; font-weight: 600;">{{ $js->nama_mata_pelajaran }}</td>
                                    <td><span class="badge-pill badge-blue">{{ $js->kelompok }}</span></td>
                                    <td style="text-align: left;">{{ $js->guru_pengampu }}</td>
                                    <td>{{ $js->ruangan }}</td>
                                    <td style="font-size: 12px; color: #64748b;">{{ $js->periode }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding: 30px; color: #888;">Belum ada jadwal untuk kelompok ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 3: KELOMPOK BELAJAR & PENGAJAR                   -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-kelompok">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Daftar 8 Guru / Pengajar Kelompok Belajar</h2>
                        <p class="card-desc">Informasi pengajar yang diatur oleh Musyrifah untuk mengampu 6 kelompok belajar Ma'had.</p>
                    </div>
                    <span class="badge-pill badge-maroon">Kelompok Saya: {{ $kelompokSaya }}</span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pengajar</th>
                                <th>Program Pembelajaran</th>
                                <th>Kelompok Yang Diampu</th>
                                <th>Beban Mengajar</th>
                                <th>Status Pengampu</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($daftarPengajarKelompok as $idx => $p)
                                <tr style="{{ $p['is_kelompok_saya'] ? 'background: #fffcf4;' : '' }}">
                                    <td>{{ $idx + 1 }}</td>
                                    <td style="text-align: left; font-weight: 700;">
                                        {{ $p['nama'] }}
                                    </td>
                                    <td style="text-align: left;">{{ $p['program'] }}</td>
                                    <td>
                                        <span class="badge-pill badge-blue">Kelompok {{ $p['kelompok_teks'] }}</span>
                                    </td>
                                    <td style="font-weight: 600; color: var(--maroon);">{{ $p['jumlah_tm'] }}</td>
                                    <td>
                                        @if($p['is_kelompok_saya'])
                                            <span class="badge-pill badge-green" style="font-weight: 700;">★ Pengajar Saya</span>
                                        @else
                                            <span class="badge-pill badge-gray">Kelompok Lain</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 12px; color: #64748b; text-align: left;">{{ $p['keterangan'] ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 30px; color: #888;">Belum ada data pengajar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 4: KELULUSAN & SERTIFIKAT PEMBELAJARAN           -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-kelulusan">
            <div class="graduation-banner {{ $isLulus ? 'lulus' : 'proses' }}">
                <div class="grad-icon-wrap">
                    @if($isLulus)
                        🎓
                    @else
                        ⏳
                    @endif
                </div>

                <div class="grad-content">
                    <div class="grad-title">
                        @if($isLulus)
                            Selamat! Anda Memenuhi Syarat Kelulusan Pembelajaran Ma'had Al-Jami'ah
                        @else
                            Status Kelulusan: Belum Memenuhi Syarat (Dalam Proses)
                        @endif
                    </div>
                    <p class="grad-desc">
                        @if($isLulus)
                            Anda telah hadir 100% (16 Pertemuan Penuh) di seluruh mata pelajaran yang dipersyaratkan. Sertifikat kelulusan Anda telah diterbitkan dan siap diunduh atau dicetak.
                        @else
                            Syarat Kelulusan: <strong>Wajib hadir minimal 16 pertemuan tatap muka di SELURUH mata pelajaran</strong>. Saat ini capaian Anda: <strong>{{ $totalHadirSeluruh }} / {{ $totalTargetKehadiran }} Pertemuan</strong> (Masih kurang di {{ $mapelBelumLulus }} mata pelajaran).
                        @endif
                    </p>
                </div>

                <div>
                    @if($isLulus)
                        <button type="button" class="btn-certificate" onclick="openModalSertifikat()">
                            🎓 Lihat &amp; Cetak Sertifikat
                        </button>
                    @else
                        <button type="button" class="btn-certificate" disabled title="Sertifikat akan aktif setelah seluruh 5 mata pelajaran mencapai 16 kehadiran">
                            🔒 Sertifikat Terkunci
                        </button>
                    @endif
                </div>
            </div>

            <!-- AUDIT KEHADIRAN PER MATA PELAJARAN -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Audit Capaian Kelulusan per Mata Pelajaran</h2>
                        <p class="card-desc">Rincian pemenuhan syarat 16 kehadiran per program pembelajaran.</p>
                    </div>
                    <span class="badge-pill {{ $isLulus ? 'badge-green' : 'badge-amber' }}">
                        {{ $isLulus ? 'Lulus Penuh (5/5 Mapel)' : 'Progres: ' . (5 - $mapelBelumLulus) . '/5 Mapel Lengkap' }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Mata Pelajaran</th>
                                <th>Target Wajib</th>
                                <th>Kehadiran Saya</th>
                                <th>Izin / Sakit</th>
                                <th>Alpa</th>
                                <th>Progres (%)</th>
                                <th>Status Syarat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daftarMataPelajaran as $idx => $m)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td style="font-weight: 700; color: var(--maroon);">{{ $m['kode'] }}</td>
                                    <td style="text-align: left; font-weight: 600;">{{ $m['nama'] }}</td>
                                    <td>16 Pertemuan</td>
                                    <td style="font-weight: 700; color: #15803d;">{{ $m['hadir'] }} TM</td>
                                    <td>{{ $m['izin'] + $m['sakit'] }} TM</td>
                                    <td>{{ $m['alpa'] }} TM</td>
                                    <td style="font-weight: 700;">{{ $m['persentase'] }}%</td>
                                    <td>
                                        @if($m['is_lulus'])
                                            <span class="badge-pill badge-green">✓ Memenuhi Syarat</span>
                                        @else
                                            <span class="badge-pill badge-amber">Kurang {{ $m['sisa_pertemuan'] }} Pertemuan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ==================================================== -->
        <!-- TAB 5: KALENDER PEMBELAJARAN DINAMIS                -->
        <!-- ==================================================== -->
        <section class="tab-pane" id="tab-kalender">
            <div class="card" style="display: flex; flex-direction: column; align-items: center;">
                <div class="calendar-card">
                    <div class="calendar-heading">
                        <h3>Kalender Jadwal Pembelajaran Mahasiswa</h3>
                        <span class="badge-pill badge-maroon">Kelompok: {{ $kelompokSaya }}</span>
                    </div>

                    <div class="calendar-toolbar">
                        <button type="button" id="prevMonth" aria-label="Bulan sebelumnya">‹</button>
                        <strong id="calendarTitle">Memuat...</strong>
                        <button type="button" id="nextMonth" aria-label="Bulan berikutnya">›</button>
                    </div>

                    <div class="calendar-grid" id="calendarGrid"></div>

                    <div class="calendar-legend">
                        <span class="legend-dot"></span>
                        <span>Hari dengan jadwal tatap muka {{ $kelompokSaya }}</span>
                    </div>

                    <div class="calendar-info-box" id="calendarDayInfo">
                        <em>Klik tanggal yang memiliki titik untuk melihat detail mata pelajaran dan waktu pembelajaran.</em>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ======================================================== -->
    <!-- MODAL 1: KEHADIRAN & RINCIAN PERTEMUAN MAHASISWA         -->
    <!-- ======================================================== -->
    <div class="modal-overlay" id="modalDetailPertemuan" onclick="closeModalOnBackdrop(event, 'modalDetailPertemuan')">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-top">
                <div class="modal-top-title">Kehadiran &amp; Pertemuan Pembelajaran</div>
                <button type="button" class="modal-close-btn" onclick="closeModal('modalDetailPertemuan')">&times;</button>
            </div>
            <div class="modal-inner">
                <div style="background: #fdf8f9; border: 1px solid #f1d5dc; border-radius: 12px; padding: 14px 18px; margin-bottom: 12px;">
                    <div style="font-size: 17px; font-weight: 700; color: var(--maroon);" id="detailMapelTitle">—</div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 3px;" id="detailMapelSub">Target: 16 Pertemuan Tatap Muka</div>
                </div>

                {{-- Ringkasan kehadiran: total per status dan persentase periode berjalan --}}
                <div class="abs-ringkasan" aria-live="polite">
                    <div class="abs-stat hadir">
                        <span class="abs-stat-label">Hadir</span>
                        <strong id="absHadir">0</strong>
                    </div>
                    <div class="abs-stat izin">
                        <span class="abs-stat-label">Izin</span>
                        <strong id="absIzin">0</strong>
                    </div>
                    <div class="abs-stat sakit">
                        <span class="abs-stat-label">Sakit</span>
                        <strong id="absSakit">0</strong>
                    </div>
                    <div class="abs-stat alpa">
                        <span class="abs-stat-label">Alpa</span>
                        <strong id="absAlpa">0</strong>
                    </div>
                    <div class="abs-stat abs-stat--persen">
                        <span class="abs-stat-label">Persentase</span>
                        <strong id="absPersen">0%</strong>
                        <span class="abs-stat-note" id="absTotal">0 pertemuan tercatat</span>
                    </div>
                </div>

                {{-- Rekap persentase per periode pembelajaran --}}
                <div class="abs-periode" id="periodeKehadiran"></div>

                <div style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                    Riwayat Kehadiran Setiap Pertemuan:
                </div>
                <div class="pertemuan-grid" id="gridPertemuanDetail">
                    <div style="grid-column: 1/-1; text-align: center; padding: 20px; color: #64748b;">Memuat data pertemuan...</div>
                </div>
            </div>
            <div class="modal-footer-btns">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalDetailPertemuan')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: SERTIFIKAT KELULUSAN MA'HAD AL-JAMI'AH          -->
    <!-- ======================================================== -->
    <div class="modal-overlay" id="modalSertifikat" onclick="closeModalOnBackdrop(event, 'modalSertifikat')">
        <div class="modal-card certificate-wrapper" id="printableCertificate" onclick="event.stopPropagation()">
            <div style="display: flex; justify-content: flex-end;">
                <button type="button" class="modal-close-btn" onclick="closeModal('modalSertifikat')">&times;</button>
            </div>

            <div style="color: #790c20; font-size: 26px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
                SERTIFIKAT KELULUSAN
            </div>
            <div style="font-size: 14px; font-weight: 700; color: #333; margin-top: 4px; letter-spacing: 0.5px;">
                MA'HAD AL-JAMI'AH UIN IMAM BONJOL PADANG
            </div>
            <div style="font-size: 12px; color: #777; margin-top: 2px;">
                Nomor: {{ $nomorSertifikat }}
            </div>

            <hr style="margin: 22px auto; border: none; border-top: 2px solid #790c20; width: 60%;">

            <p style="font-size: 14px; color: #444; margin-bottom: 8px;">Diberikan dengan penuh kehormatan kepada:</p>
            <div style="font-size: 24px; font-weight: 800; color: #111; text-decoration: underline;">
                {{ $user->name ?? 'Mahasiswa Ma\'had' }}
            </div>
            <div style="font-size: 15px; font-weight: 600; color: #555; margin-top: 4px;">
                NIM: {{ $user->nim ?? '-' }} | Kelompok: {{ $kelompokSaya }}
            </div>

            <p style="font-size: 14px; color: #333; margin-top: 24px; line-height: 1.7; max-width: 580px; margin-left: auto; margin-right: auto;">
                Telah <strong>MEMENUHI SYARAT KELULUSAN</strong> pada seluruh rangkaian 5 program pembelajaran Ma'had Al-Jami'ah dengan tingkat kehadiran 100% (16 Pertemuan Penuh per Mata Pelajaran) yang diselenggarakan pada Semester Ganjil 2026/2027.
            </p>

            <div style="display: flex; justify-content: space-around; margin-top: 40px; padding-top: 10px;">
                <div>
                    <div style="font-size: 12px; color: #777;">Mudir Ma'had Al-Jami'ah</div>
                    <div style="height: 50px;"></div>
                    <div style="font-weight: 700; font-size: 14px; border-top: 1px solid #333; padding-top: 4px;">
                        Dr. Zulkifli, M.A.
                    </div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #777;">Koordinator Musyrifah</div>
                    <div style="height: 50px;"></div>
                    <div style="font-weight: 700; font-size: 14px; border-top: 1px solid #333; padding-top: 4px;">
                        Ustadzah Musyrifah
                    </div>
                </div>
            </div>

            <div class="modal-footer-btns" style="background: transparent; border-top: none; justify-content: center; margin-top: 26px;">
                <button type="button" class="btn-modal-cancel" onclick="closeModal('modalSertifikat')">Tutup</button>
                <button type="button" class="btn-certificate" onclick="window.print()">🖨️ Cetak Sertifikat</button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        // SIDEBAR TOGGLE
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            const toggleButton = document.getElementById('sidebarToggle');

            const isOpen = sidebar.classList.toggle('open');
            overlay.classList.toggle('open', isOpen);

            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', String(isOpen));
            }
        }

        // TAB SWITCHER
        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const target = document.getElementById(tabId);
            if (target) {
                target.classList.add('active');
            }

            if (tabId === 'tab-kalender') {
                renderCalendar();
            }
        }

        // MODAL HELPERS
        function openModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.add('active');
        }

        function closeModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.remove('active');
        }

        function closeModalOnBackdrop(event, id) {
            if (event.target.id === id) {
                closeModal(id);
            }
        }

        function openModalSertifikat() {
            openModal('modalSertifikat');
        }

        // KEHADIRAN PER MATA PELAJARAN (AJAX/FETCH)
        function formatPersen(nilai) {
            return String(Number(nilai || 0)).replace('.', ',') + '%';
        }

        function resetRingkasanKehadiran() {
            ['absHadir', 'absIzin', 'absSakit', 'absAlpa'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.textContent = '0';
            });

            const persen = document.getElementById('absPersen');
            if (persen) persen.textContent = '0%';

            const total = document.getElementById('absTotal');
            if (total) total.textContent = 'Memuat data kehadiran...';

            const periode = document.getElementById('periodeKehadiran');
            if (periode) periode.innerHTML = '';
        }

        function tampilkanRingkasanKehadiran(data) {
            const ringkasan = data.ringkasan || {};

            const jumlah = {
                absHadir: ringkasan.hadir || 0,
                absIzin: ringkasan.izin || 0,
                absSakit: ringkasan.sakit || 0,
                absAlpa: ringkasan.alpa || 0,
            };

            Object.keys(jumlah).forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.textContent = jumlah[id];
            });

            const persen = document.getElementById('absPersen');
            if (persen) persen.textContent = formatPersen(ringkasan.persentase);

            const total = document.getElementById('absTotal');
            if (total) {
                total.textContent = (ringkasan.total || 0) + ' dari ' + (ringkasan.target || 16) + ' pertemuan';
            }

            const wadah = document.getElementById('periodeKehadiran');
            if (!wadah) return;

            wadah.innerHTML = '';
            const daftar = data.periode || [];

            if (!daftar.length) {
                wadah.style.display = 'none';
                return;
            }

            wadah.style.display = 'block';

            daftar.forEach(function (item) {
                const baris = document.createElement('div');
                baris.className = 'abs-periode-item';

                const nama = document.createElement('span');
                nama.className = 'abs-periode-nama';
                nama.textContent = item.periode || 'Periode Pembelajaran';

                const detail = document.createElement('span');
                detail.className = 'abs-periode-detail';
                detail.textContent = 'H ' + (item.hadir || 0) + ' · I ' + (item.izin || 0) +
                    ' · S ' + (item.sakit || 0) + ' · A ' + (item.alpa || 0);

                const persenPeriode = document.createElement('span');
                persenPeriode.className = 'abs-periode-persen';
                persenPeriode.textContent = formatPersen(item.persentase);

                baris.appendChild(nama);
                baris.appendChild(detail);
                baris.appendChild(persenPeriode);
                wadah.appendChild(baris);
            });
        }

        function openDetailPertemuan(mapelId, namaMapel, kodeMapel) {
            document.getElementById('detailMapelTitle').textContent = namaMapel + ' (' + kodeMapel + ')';
            document.getElementById('detailMapelSub').textContent = 'Target: 16 Pertemuan Tatap Muka Ma\'had';

            const grid = document.getElementById('gridPertemuanDetail');
            grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 25px; color: #64748b;">Memuat data kehadiran...</div>';

            resetRingkasanKehadiran();
            openModal('modalDetailPertemuan');

            fetch(`{{ url('/pembelajaran-mhs/detail-pertemuan') }}/${mapelId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.pertemuan) {
                        tampilkanRingkasanKehadiran(data);

                        const mapel = data.mapel || {};
                        document.getElementById('detailMapelSub').textContent =
                            (mapel.periode ? 'Periode ' + mapel.periode + ' · ' : '') +
                            'Target ' + (mapel.target_pertemuan || 16) + ' Pertemuan Tatap Muka';

                        grid.innerHTML = '';
                        data.pertemuan.forEach(p => {
                            let badgeStyle = 'background: #e2e8f0; color: #334155;';
                            let statusText = 'Terjadwal';

                            if (p.status === 'hadir') {
                                badgeStyle = 'background: #dcfce7; color: #15803d;';
                                statusText = 'Hadir';
                            } else if (p.status === 'izin') {
                                badgeStyle = 'background: #dbeafe; color: #1d4ed8;';
                                statusText = 'Izin';
                            } else if (p.status === 'sakit') {
                                badgeStyle = 'background: #fef3c7; color: #b45309;';
                                statusText = 'Sakit';
                            } else if (p.status === 'alpa') {
                                badgeStyle = 'background: #fee2e2; color: #b91c1c;';
                                statusText = 'Alpa';
                            }

                            const card = document.createElement('div');
                            card.className = 'pertemuan-card-item';
                            card.innerHTML = `
                                <div class="pertemuan-card-num">Pertemuan ${p.pertemuan_ke}</div>
                                <div class="pertemuan-card-date">${p.tanggal}</div>
                                <span class="pertemuan-card-badge" style="${badgeStyle}">
                                    ${statusText}
                                </span>
                            `;
                            grid.appendChild(card);
                        });
                    } else {
                        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #ef4444; padding: 20px;">Gagal memuat data kehadiran.</div>';
                    }
                })
                .catch(() => {
                    grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #ef4444; padding: 20px;">Terjadi kesalahan saat memuat data.</div>';
                });
        }

        // ============================================
        // KALENDER DINAMIS
        // ============================================
        const calendarEvents = @json($calendarEvents);

        let shownDate = new Date();
        let shownYear = shownDate.getFullYear();
        let shownMonth = shownDate.getMonth();

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April',
            'Mei', 'Juni', 'Juli', 'Agustus',
            'September', 'Oktober', 'November', 'Desember'
        ];

        function renderCalendar() {
            const grid = document.getElementById('calendarGrid');
            const title = document.getElementById('calendarTitle');
            if (!grid || !title) return;

            const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

            title.textContent = monthNames[shownMonth] + ' ' + shownYear;
            grid.innerHTML = '';

            // Header nama hari
            days.forEach(day => {
                const el = document.createElement('div');
                el.className = 'day-name';
                el.textContent = day;
                grid.appendChild(el);
            });

            const firstDay = new Date(shownYear, shownMonth, 1).getDay();
            const daysInMonth = new Date(shownYear, shownMonth + 1, 0).getDate();
            const daysInPrevMonth = new Date(shownYear, shownMonth, 0).getDate();

            const today = new Date();
            const cells = Math.ceil((firstDay + daysInMonth) / 7) * 7;

            for (let i = 0; i < cells; i++) {
                const element = document.createElement('div');
                element.className = 'calendar-day';

                let dayNum;
                let dateKey = '';

                if (i < firstDay) {
                    dayNum = daysInPrevMonth - firstDay + i + 1;
                    element.classList.add('muted');
                } else if (i >= firstDay + daysInMonth) {
                    dayNum = i - firstDay - daysInMonth + 1;
                    element.classList.add('muted');
                } else {
                    dayNum = i - firstDay + 1;
                    const mm = String(shownMonth + 1).padStart(2, '0');
                    const dd = String(dayNum).padStart(2, '0');
                    dateKey = `${shownYear}-${mm}-${dd}`;

                    if (
                        today.getFullYear() === shownYear &&
                        today.getMonth() === shownMonth &&
                        today.getDate() === dayNum
                    ) {
                        element.classList.add('today');
                    }

                    if (calendarEvents[dateKey] && calendarEvents[dateKey].length > 0) {
                        element.classList.add('has-event');
                        element.title = `${calendarEvents[dateKey].length} kelas terjadwal`;
                        element.addEventListener('click', function () {
                            showDaySchedule(dateKey, calendarEvents[dateKey]);
                        });
                    }
                }

                element.textContent = dayNum;
                grid.appendChild(element);
            }
        }

        function showDaySchedule(dateKey, classes) {
            const box = document.getElementById('calendarDayInfo');
            if (!box) return;

            const parts = dateKey.split('-');
            const formatted = `${parts[2]} ${monthNames[parseInt(parts[1], 10) - 1]} ${parts[0]}`;

            let html = `<strong>Jadwal Pembelajaran pada ${formatted}:</strong><ul style="margin: 8px 0 0 18px; line-height: 1.6;">`;
            classes.forEach(c => {
                html += `<li><strong>${c.nama}</strong> (${c.waktu}) — Guru: ${c.guru} | Ruang: ${c.ruangan}</li>`;
            });
            html += `</ul>`;
            box.innerHTML = html;
        }

        document.getElementById('prevMonth')?.addEventListener('click', function () {
            shownMonth--;
            if (shownMonth < 0) {
                shownMonth = 11;
                shownYear--;
            }
            renderCalendar();
        });

        document.getElementById('nextMonth')?.addEventListener('click', function () {
            shownMonth++;
            if (shownMonth > 11) {
                shownMonth = 0;
                shownYear++;
            }
            renderCalendar();
        });

        renderCalendar();
    </script>
</body>
</html>
