<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Surat & Perizinan') - {{ $namaSistem ?? 'SIMAHAD' }}</title>

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
            --maroon: #6B0D1B;
            --maroon-dark: #4f0a15;
            --gold: #C9A96A;
            --gold-soft: #f3e3bf;
            --tint: #fdf1f3;
            --border: #e3cdd3;
            --muted: #6b7280;
            --text: #111827;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background: #fff;
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button, input, select, textarea {
            font: inherit;
        }

        .sp-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 28px;
            border-bottom: 2px solid var(--maroon);
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .sp-topbar__brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--maroon);
        }

        .sp-topbar__brand img {
            height: 38px;
            width: auto;
        }

        .sp-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border: 1px solid var(--maroon);
            border-radius: 999px;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--maroon);
            background: #fff;
            transition: background .2s ease, color .2s ease;
        }

        .sp-back:hover {
            background: var(--maroon);
            color: #fff;
        }

        .sp-wrap {
            max-width: 1080px;
            margin: 0 auto;
            padding: 26px 20px 60px;
        }

        .sp-head {
            margin-bottom: 22px;
        }

        .sp-head h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--maroon);
            letter-spacing: -.01em;
        }

        .sp-head p {
            margin-top: 6px;
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
        }

        .sp-alert {
            margin-bottom: 18px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            line-height: 1.55;
        }

        .sp-alert ul {
            margin: 6px 0 0 18px;
        }

        .sp-alert--sukses {
            background: #eaf6ee;
            border: 1px solid #2f7d4f;
            color: #1c5334;
        }

        .sp-alert--gagal {
            background: var(--tint);
            border: 1px solid var(--maroon);
            color: var(--maroon);
        }

        .sp-kuota {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 22px;
            padding: 18px 22px;
            border-radius: 16px;
            border: 1px solid var(--gold);
            background: linear-gradient(135deg, var(--maroon) 0%, #800d22 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(107, 13, 27, .22);
        }

        .sp-kuota__judul {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 16px;
            font-weight: 700;
            color: var(--gold-soft);
        }

        .sp-kuota p {
            margin-top: 5px;
            font-size: 13px;
            color: #f6e7ea;
        }

        .sp-kuota__angka {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }

        .sp-kuota__angka span {
            font-size: 24px;
            font-weight: 800;
            color: var(--gold);
        }

        .sp-kuota__angka small {
            font-size: 12.5px;
            color: #f6e7ea;
        }

        .sp-card {
            margin-bottom: 22px;
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(107, 13, 27, .06);
        }

        .sp-card h2 {
            margin-bottom: 16px;
            font-size: 17px;
            font-weight: 800;
            color: var(--maroon);
        }

        .sp-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .sp-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .sp-field--full {
            grid-column: 1 / -1;
        }

        .sp-field label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .sp-field label span {
            color: #b00020;
        }

        .sp-field input,
        .sp-field select,
        .sp-field textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #cdb4ba;
            border-radius: 10px;
            font-size: 14px;
            background: #fff;
            color: var(--text);
        }

        .sp-field input:focus,
        .sp-field select:focus,
        .sp-field textarea:focus {
            outline: none;
            border-color: var(--maroon);
            box-shadow: 0 0 0 3px rgba(107, 13, 27, .12);
        }

        .sp-field textarea {
            min-height: 96px;
            resize: vertical;
        }

        .sp-hint {
            font-size: 12px;
            color: var(--muted);
        }

        .sp-error {
            font-size: 12.5px;
            color: #b00020;
        }

        .sp-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
        }

        .sp-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border: 1px solid var(--maroon);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            background: #fff;
            color: var(--maroon);
            transition: opacity .2s ease;
        }

        .sp-btn:hover {
            opacity: .88;
        }

        .sp-btn--primary {
            background: var(--maroon);
            color: #fff;
        }

        .sp-btn--gold {
            background: var(--gold);
            border-color: var(--gold);
            color: #4a1720;
        }

        .sp-btn--danger {
            border-color: #a01c2c;
            color: #a01c2c;
        }

        .sp-table-wrap {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .sp-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
        }

        .sp-table thead {
            background: var(--maroon);
            color: #fff;
        }

        .sp-table th,
        .sp-table td {
            padding: 11px 13px;
            font-size: 13.5px;
            text-align: left;
            border-bottom: 1px solid #eddde1;
            vertical-align: middle;
        }

        .sp-table th {
            font-weight: 700;
            white-space: nowrap;
        }

        .sp-table tbody tr:last-child td {
            border-bottom: none;
        }

        .sp-table tbody tr:hover {
            background: #fdf6f7;
        }

        .sp-badge {
            display: inline-block;
            padding: 4px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .sp-badge--menunggu { background: #fdf1d8; color: #8a5a00; border: 1px solid #e0b658; }
        .sp-badge--disetujui { background: #e7f6ec; color: #1e6b3a; border: 1px solid #7fbf95; }
        .sp-badge--ditolak { background: #fdeaea; color: #a01c2c; border: 1px solid #e2969f; }
        .sp-badge--perbaikan { background: #fdf1d8; color: #8a4b00; border: 1px solid #e0a558; }
        .sp-badge--selesai { background: #e8eefb; color: #22457f; border: 1px solid #93a9d6; }
        .sp-badge--dibatalkan { background: #eef0f3; color: #4b5563; border: 1px solid #b9c0ca; }

        .sp-empty {
            text-align: center;
            padding: 20px 12px;
            color: var(--muted);
        }

        .sp-inline-form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .sp-inline-form input[type="file"] {
            font-size: 12px;
            max-width: 210px;
        }

        .sp-mini {
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            border: 1px solid var(--maroon);
            background: #fff;
            color: var(--maroon);
            cursor: pointer;
            white-space: nowrap;
        }

        .sp-mini--gold {
            background: var(--gold);
            border-color: var(--gold);
            color: #4a1720;
        }

        .sp-mini--danger {
            border-color: #a01c2c;
            color: #a01c2c;
        }

        .sp-menu-toggle {
            display: none;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            padding: 0;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--maroon);
            cursor: pointer;
        }

        .sp-menu-toggle svg {
            width: 19px;
            height: 19px;
        }

        @media (max-width: 991px) {
            .sp-menu-toggle {
                display: grid;
            }
        }

        @media (max-width: 760px) {
            .sp-topbar {
                padding: 12px 16px;
                flex-wrap: wrap;
            }

            .sp-grid {
                grid-template-columns: 1fr;
            }

            .sp-kuota__angka {
                align-items: flex-start;
            }

            .sp-wrap {
                padding: 20px 14px 44px;
            }
        }

        @media print {
            .sp-topbar,
            .sp-back,
            .sp-actions {
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="sb-mandiri">
    {{-- Sidebar bersama seluruh role SIMAHAD. --}}
    <x-panel.sidebar ragam="mandiri" />

    <header class="sp-topbar">
        <div class="sp-topbar__brand">
            <button type="button"
                class="sp-menu-toggle"
                data-sb-buka
                aria-label="Buka menu navigasi"
                aria-controls="sidebar"
                aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <img src="{{ asset('images/logo_uin.png') }}" alt="Logo SIMAHAD" onerror="this.style.display='none'">
            <span>SIMAHAD</span>
        </div>
        <a href="{{ route('surat.perizinan.mhs') }}" class="sp-back">
            <i class="fa-solid fa-arrow-left"></i> Surat &amp; Perizinan
        </a>
    </header>

    <div class="sp-wrap">
        <div class="sp-head">
            <h1>@yield('judul')</h1>
            <p>@yield('keterangan')</p>
        </div>

        @if (session('success'))
            <div class="sp-alert sp-alert--sukses">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="sp-alert sp-alert--gagal">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="sp-alert sp-alert--gagal">
                <strong>Periksa kembali data pengajuan:</strong>
                <ul>
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('konten')
    </div>

    @stack('scripts')
</body>
</html>
