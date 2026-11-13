@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    @php
        $peran = $ringkasan['perRole'];
        $konfigurasi = $ringkasan['konfigurasi'];
        $totalAkun = (int) $ringkasan['totalAkun'];
        $akunAktif = (int) $ringkasan['akunAktif'];
        $akunMenunggu = (int) $ringkasan['akunMenunggu'];

        $aktivitas = $ringkasan['aktivitas'];
        $seriAktivitas = $ringkasan['aktivitasHarian'];
        $maksAktivitas = max(1, max(array_column($seriAktivitas, 'jumlah')));
        $rataAktivitas = round($aktivitas['tujuhHari'] / 7, 1);
        $ringkasGrafik = collect($seriAktivitas)
            ->map(fn (array $hari): string => $hari['label'].' '.$hari['jumlah'])
            ->implode(', ');
        $notifAktif = is_array($konfigurasi['notifikasi'])
            ? count(array_filter($konfigurasi['notifikasi']))
            : 0;

        $daftarPeran = [
            ['nama' => 'Mahasiswa', 'jumlah' => (int) $peran['mahasiswa'], 'kelas' => 'mahasiswa'],
            ['nama' => 'Guru', 'jumlah' => (int) $peran['guru'], 'kelas' => 'guru'],
            ['nama' => 'Musyrifah', 'jumlah' => (int) $peran['musyrifah'], 'kelas' => 'musyrifah'],
            ['nama' => 'Pimpinan', 'jumlah' => (int) $peran['pimpinan'], 'kelas' => 'pimpinan'],
            ['nama' => 'Admin Sistem', 'jumlah' => (int) $peran['admin'], 'kelas' => 'admin'],
        ];
        $totalPeran = max(1, array_sum(array_column($daftarPeran, 'jumlah')));

        $inisial = function (?string $nama): string {
            $nama = trim((string) $nama);
            if ($nama === '') {
                return '?';
            }
            $huruf = '';
            foreach (preg_split('/\s+/', $nama) as $kata) {
                if ($kata !== '') {
                    $huruf .= mb_substr($kata, 0, 1);
                }
                if (mb_strlen($huruf) >= 2) {
                    break;
                }
            }
            return mb_strtoupper($huruf);
        };

        $kelasPeran = function (?string $role): string {
            $role = strtolower((string) $role);
            return in_array($role, ['guru', 'musyrifah', 'pimpinan'], true) ? 'is-'.$role : '';
        };

        $namaPeran = function (?string $role): string {
            return match (strtolower((string) $role)) {
                'mahasiswa' => 'Mahasiswa',
                'guru' => 'Guru',
                'musyrifah' => 'Musyrifah',
                'pimpinan' => 'Pimpinan',
                'admin' => 'Admin Sistem',
                default => ucfirst((string) $role) ?: 'Tanpa role',
            };
        };

        $labelModul = function ($log): string {
            $modul = trim((string) ($log->modul ?? ''));
            if ($modul === '') {
                $bagian = preg_split('/·/', (string) ($log->activity ?? ''), 2);
                $modul = trim($bagian[0] ?? '');
            }
            return $modul !== '' ? ucwords(str_replace('_', ' ', $modul)) : 'Aktivitas';
        };

        $labelAksi = function ($log): string {
            $aksi = trim((string) ($log->aksi ?? ''));
            if ($aksi === '') {
                $bagian = preg_split('/·/', (string) ($log->activity ?? ''), 2);
                $aksi = trim($bagian[1] ?? ($bagian[0] ?? ''));
            }
            $peta = [
                'create_user' => 'Tambah pengguna',
                'update_user' => 'Perbarui pengguna',
                'delete_user' => 'Hapus pengguna',
                'verify_user' => 'Verifikasi pengguna',
                'reject_user' => 'Tolak pengguna',
                'send_activation' => 'Kirim aktivasi',
                'activate_user' => 'Aktifkan akun',
                'verifikasi_disetujui' => 'Verifikasi disetujui',
                'verifikasi_ditolak' => 'Verifikasi ditolak',
            ];
            return $peta[strtolower($aksi)] ?? (ucwords(str_replace('_', ' ', $aksi)) ?: 'Aktivitas');
        };

        $formatPersen = function (float $nilai): string {
            return rtrim(rtrim(number_format($nilai, 1, ',', '.'), '0'), ',');
        };
    @endphp

    <style>
        /* Semua aturan di-scope agar sidebar, header, dan dashboard role lain tidak berubah. */
        #simahad-admin-dashboard {
            --sd-maroon: #6b0d1b;
            --sd-maroon-mid: #8d1c2e;
            --sd-maroon-soft: #a12441;
            --sd-gold: #c9a96a;
            --sd-text: #1f2430;
            --sd-soft: #596172;
            --sd-muted: #5f6775;
            --sd-faint: #6b7280;
            --sd-line: #e8eaf0;
            --sd-line-soft: #f2f3f7;
            --sd-card: #ffffff;
            --sd-radius: 16px;
            --sd-shadow: 0 1px 2px rgba(20, 28, 45, .04), 0 14px 32px -26px rgba(20, 28, 45, .42);
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 2px 0 10px;
            color: var(--sd-text);
            font-size: 13px;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }

        #simahad-admin-dashboard *,
        #simahad-admin-dashboard *::before,
        #simahad-admin-dashboard *::after { box-sizing: border-box; }

        #simahad-admin-dashboard a { text-decoration: none; }

        #simahad-admin-dashboard a:focus-visible,
        #simahad-admin-dashboard [tabindex]:focus-visible {
            outline: 2px solid var(--sd-maroon);
            outline-offset: 2px;
            border-radius: 8px;
        }

        /* ---------- Baris atas ---------- */
        #simahad-admin-dashboard .sd-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        #simahad-admin-dashboard .sd-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: var(--sd-muted);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        #simahad-admin-dashboard .sd-eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--sd-maroon);
            box-shadow: 0 0 0 4px #f6e7eb;
        }

        #simahad-admin-dashboard .sd-date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border: 1px solid var(--sd-line);
            border-radius: 999px;
            background: var(--sd-card);
            color: var(--sd-maroon);
            font-size: 12.5px;
            font-weight: 700;
            box-shadow: 0 1px 2px rgba(31, 36, 48, .04);
            white-space: nowrap;
        }

        #simahad-admin-dashboard .sd-date svg { width: 15px; height: 15px; flex-shrink: 0; color: var(--sd-maroon); }

        /* ---------- Hero ---------- */
        #simahad-admin-dashboard .sd-hero {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
            padding: 32px 34px;
            border-radius: 20px;
            background:
                radial-gradient(120% 160% at 100% 0%, rgba(201, 169, 106, .22), transparent 58%),
                linear-gradient(135deg, #7d1022 0%, #5c0a17 100%);
            box-shadow: 0 16px 32px -18px rgba(96, 8, 28, .55);
            color: #fff;
            isolation: isolate;
        }

        #simahad-admin-dashboard .sd-hero::before,
        #simahad-admin-dashboard .sd-hero::after {
            content: "";
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 50%;
            pointer-events: none;
        }

        #simahad-admin-dashboard .sd-hero::before {
            top: -220px;
            right: -96px;
            width: 360px;
            height: 360px;
            box-shadow: 0 0 0 44px rgba(255, 255, 255, .035), 0 0 0 96px rgba(255, 255, 255, .022);
        }

        #simahad-admin-dashboard .sd-hero::after {
            right: 190px;
            bottom: -250px;
            width: 270px;
            height: 270px;
        }

        #simahad-admin-dashboard .sd-hero-content { position: relative; z-index: 1; min-width: 0; }

        #simahad-admin-dashboard .sd-hero-label {
            display: inline-flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 5px 11px;
            border: 1px solid rgba(255, 255, 255, .26);
            border-radius: 999px;
            background: rgba(255, 255, 255, .1);
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        #simahad-admin-dashboard .sd-hero h1 {
            margin: 0 0 8px;
            font-size: clamp(21px, 2vw, 29px);
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -.025em;
            color: #fff;
        }

        #simahad-admin-dashboard .sd-hero p {
            margin: 0;
            max-width: 560px;
            font-size: 12.5px;
            line-height: 1.65;
            color: rgba(255, 255, 255, .78);
        }

        #simahad-admin-dashboard .sd-hero-board {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 2px;
            min-width: 330px;
            padding: 6px;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 15px;
            background: rgba(255, 255, 255, .1);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
        }

        #simahad-admin-dashboard .sd-hero-stat {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 12px 14px;
            border-radius: 11px;
        }

        #simahad-admin-dashboard .sd-hero-stat + .sd-hero-stat { box-shadow: inset 1px 0 0 rgba(255, 255, 255, .16); }

        #simahad-admin-dashboard .sd-hero-stat span {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .01em;
            color: rgba(255, 255, 255, .72);
        }

        #simahad-admin-dashboard .sd-hero-stat strong {
            font-size: clamp(20px, 1.9vw, 26px);
            font-weight: 750;
            letter-spacing: -.02em;
            line-height: 1.15;
            color: #fff;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Aksi cepat ---------- */
        #simahad-admin-dashboard .sd-quick {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 9px;
            margin-top: 14px;
        }

        #simahad-admin-dashboard .sd-quick-link {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
            padding: 9px 12px;
            border: 1px solid var(--sd-line);
            border-radius: 11px;
            background: var(--sd-card);
            color: #333a49;
            font-size: 11.5px;
            font-weight: 700;
            box-shadow: 0 1px 2px rgba(20, 28, 45, .03);
            transition: border-color .18s ease, background .18s ease, transform .18s ease;
        }

        #simahad-admin-dashboard .sd-quick-link:hover {
            border-color: #e3afbc;
            background: #fffafb;
            transform: translateY(-2px);
        }

        #simahad-admin-dashboard .sd-quick-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            border-radius: 8px;
            background: #fbecef;
            color: var(--sd-maroon);
        }

        #simahad-admin-dashboard .sd-quick-icon svg { width: 15px; height: 15px; }

        #simahad-admin-dashboard .sd-quick-link > span:last-child {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #simahad-admin-dashboard .sd-quick-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 19px;
            height: 19px;
            margin-left: auto;
            padding: 0 5px;
            border-radius: 999px;
            background: #fff0d9;
            color: #aa6700;
            font-size: 10px;
            font-weight: 800;
            flex-shrink: 0;
        }

        /* ---------- Section ---------- */
        #simahad-admin-dashboard .sd-section { margin-top: 24px; }

        #simahad-admin-dashboard .sd-section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }

        #simahad-admin-dashboard .sd-section-head h2 {
            margin: 0;
            font-size: 15.5px;
            font-weight: 800;
            letter-spacing: -.02em;
            color: #232838;
        }

        #simahad-admin-dashboard .sd-section-head p {
            margin: 3px 0 0;
            font-size: 11.5px;
            color: var(--sd-muted);
        }

        #simahad-admin-dashboard .sd-link {
            display: inline-flex;
            align-items: center;
            color: var(--sd-maroon);
            font-size: 11.5px;
            font-weight: 750;
            white-space: nowrap;
        }

        #simahad-admin-dashboard .sd-link::after {
            content: "";
            display: inline-block;
            width: 6px;
            height: 6px;
            margin-left: 7px;
            border-top: 1.6px solid currentColor;
            border-right: 1.6px solid currentColor;
            transform: rotate(45deg) translateY(-1px);
        }

        #simahad-admin-dashboard .sd-link:hover { text-decoration: underline; }

        /* ---------- KPI ---------- */
        #simahad-admin-dashboard .sd-kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        #simahad-admin-dashboard .sd-kpi {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
            padding: 18px;
            border: 1px solid var(--sd-line);
            border-radius: var(--sd-radius);
            background: var(--sd-card);
            box-shadow: var(--sd-shadow);
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        #simahad-admin-dashboard a.sd-kpi:hover {
            transform: translateY(-3px);
            border-color: #e2c3cb;
            box-shadow: 0 3px 6px rgba(38, 29, 42, .05), 0 18px 32px -20px rgba(107, 13, 27, .32);
        }

        #simahad-admin-dashboard .sd-kpi-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: var(--sd-soft);
            font-size: 11.5px;
            font-weight: 700;
        }

        #simahad-admin-dashboard .sd-kpi-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 11px;
            background: #fbe9ed;
            color: #9e2744;
        }

        #simahad-admin-dashboard .sd-kpi-icon svg { width: 18px; height: 18px; }

        #simahad-admin-dashboard .sd-kpi.is-green .sd-kpi-icon { background: #e9f8ef; color: #178154; }
        #simahad-admin-dashboard .sd-kpi.is-blue .sd-kpi-icon { background: #eaf0fa; color: #3d5f8f; }
        #simahad-admin-dashboard .sd-kpi.is-amber .sd-kpi-icon { background: #fff4df; color: #c58113; }

        #simahad-admin-dashboard .sd-kpi-value {
            margin: 0;
            font-size: clamp(28px, 2.4vw, 34px);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -.045em;
            color: #1e2533;
            font-variant-numeric: tabular-nums;
        }

        #simahad-admin-dashboard .sd-kpi-foot {
            margin-top: auto;
            color: var(--sd-faint);
            font-size: 10.5px;
            line-height: 1.45;
        }

        /* ---------- Grid utama ---------- */
        #simahad-admin-dashboard .sd-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.62fr) minmax(292px, 1fr);
            gap: 15px;
            align-items: start;
        }

        #simahad-admin-dashboard .sd-col {
            display: flex;
            flex-direction: column;
            gap: 15px;
            min-width: 0;
        }

        #simahad-admin-dashboard .sd-card {
            display: flex;
            flex-direction: column;
            min-width: 0;
            border: 1px solid var(--sd-line);
            border-radius: var(--sd-radius);
            background: var(--sd-card);
            box-shadow: var(--sd-shadow);
            overflow: hidden;
        }

        #simahad-admin-dashboard .sd-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 15px 18px 12px;
            border-bottom: 1px solid var(--sd-line-soft);
        }

        #simahad-admin-dashboard .sd-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        #simahad-admin-dashboard .sd-card-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 33px;
            height: 33px;
            flex-shrink: 0;
            border-radius: 10px;
            background: #f8ecf0;
            color: var(--sd-maroon);
        }

        #simahad-admin-dashboard .sd-card-mark svg { width: 16px; height: 16px; }

        #simahad-admin-dashboard .sd-card-head h3 {
            margin: 0;
            font-size: 12.5px;
            font-weight: 800;
            letter-spacing: -.01em;
            color: #252c3a;
        }

        #simahad-admin-dashboard .sd-card-head p {
            margin: 2px 0 0;
            color: var(--sd-faint);
            font-size: 10.5px;
        }

        #simahad-admin-dashboard .sd-card-tools {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        #simahad-admin-dashboard .sd-chip {
            padding: 3px 8px;
            border-radius: 999px;
            background: #f3f4f8;
            color: #7b8391;
            font-size: 10px;
            font-weight: 750;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        #simahad-admin-dashboard .sd-card-link {
            color: var(--sd-maroon);
            font-size: 10.5px;
            font-weight: 750;
            white-space: nowrap;
        }

        #simahad-admin-dashboard .sd-card-link:hover { text-decoration: underline; }

        #simahad-admin-dashboard .sd-card-body { padding: 14px 18px 16px; }

        /* ---------- Grafik ---------- */
        #simahad-admin-dashboard .sd-chart {
            display: flex;
            align-items: stretch;
            gap: 8px;
            height: 168px;
            padding: 4px 2px 0;
        }

        #simahad-admin-dashboard .sd-chart-col {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }

        #simahad-admin-dashboard .sd-chart-value {
            color: #303747;
            font-size: 10.5px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        #simahad-admin-dashboard .sd-chart-col.is-empty .sd-chart-value { color: #b0b6c0; }

        #simahad-admin-dashboard .sd-chart-track {
            display: flex;
            flex: 1;
            align-items: flex-end;
            justify-content: center;
            width: 100%;
            border-radius: 0;
            background: transparent;
            border-bottom: 1px solid #e9ebf1;
        }

        #simahad-admin-dashboard .sd-chart-bar {
            display: block;
            width: min(38px, 74%);
            height: var(--sd-bar, 6%);
            min-height: 4px;
            border-radius: 7px 7px 3px 3px;
            background: linear-gradient(180deg, #c45c73, #8d1c2e);
            transition: height .35s ease;
        }

        #simahad-admin-dashboard .sd-chart-col.is-today .sd-chart-bar {
            background: linear-gradient(180deg, #a12441, #5f0a19);
            box-shadow: 0 6px 14px -6px rgba(107, 13, 27, .55);
        }

        #simahad-admin-dashboard .sd-chart-col.is-empty .sd-chart-bar {
            background: #e5e8ef;
            box-shadow: none;
        }

        #simahad-admin-dashboard .sd-chart-day {
            color: #6b7382;
            font-size: 10px;
            font-weight: 750;
        }

        #simahad-admin-dashboard .sd-chart-col.is-today .sd-chart-day { color: var(--sd-maroon); }

        #simahad-admin-dashboard .sd-chart-date {
            color: #a5abb5;
            font-size: 9px;
            font-variant-numeric: tabular-nums;
        }

        #simahad-admin-dashboard .sd-chart-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
            padding-top: 11px;
            border-top: 1px solid var(--sd-line-soft);
            color: var(--sd-faint);
            font-size: 10.5px;
        }

        #simahad-admin-dashboard .sd-chart-foot strong { color: #333b4c; font-weight: 800; }

        #simahad-admin-dashboard .sd-legend {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        #simahad-admin-dashboard .sd-legend i {
            width: 8px;
            height: 8px;
            border-radius: 3px;
            background: linear-gradient(180deg, #a12441, #5f0a19);
        }

        /* ---------- Feed aktivitas ---------- */
        #simahad-admin-dashboard .sd-feed {
            position: relative;
            max-height: 430px;
            padding: 2px 18px 10px;
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: #dcbcc4 transparent;
            background:
                linear-gradient(#fff 30%, rgba(255, 255, 255, 0)) center top,
                linear-gradient(rgba(255, 255, 255, 0), #fff 70%) center bottom,
                radial-gradient(farthest-side at 50% 0, rgba(32, 20, 24, .1), transparent) center top,
                radial-gradient(farthest-side at 50% 100%, rgba(32, 20, 24, .1), transparent) center bottom;
            background-repeat: no-repeat;
            background-size: 100% 34px, 100% 34px, 100% 11px, 100% 11px;
            background-attachment: local, local, scroll, scroll;
        }

        #simahad-admin-dashboard .sd-feed::-webkit-scrollbar { width: 8px; }
        #simahad-admin-dashboard .sd-feed::-webkit-scrollbar-track { background: transparent; }
        #simahad-admin-dashboard .sd-feed::-webkit-scrollbar-thumb {
            border: 2px solid #fff;
            border-radius: 999px;
            background: #e3c4cb;
        }

        #simahad-admin-dashboard .sd-feed-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 11px 0;
            border-top: 1px solid var(--sd-line-soft);
        }

        #simahad-admin-dashboard .sd-feed-item:first-child { border-top: 0; }

        #simahad-admin-dashboard .sd-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            border-radius: 10px;
            background: #f7eaee;
            color: #96314c;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .02em;
        }

        #simahad-admin-dashboard .sd-avatar.is-guru { background: #fdf3df; color: #96793c; }
        #simahad-admin-dashboard .sd-avatar.is-musyrifah { background: #e9f6ef; color: #2f7d54; }
        #simahad-admin-dashboard .sd-avatar.is-pimpinan { background: #eef2fa; color: #3d5f8f; }

        #simahad-admin-dashboard .sd-feed-copy {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        

        #simahad-admin-dashboard .sd-feed-sub {
            display: block;
            overflow: hidden;
            color: var(--sd-faint);
            font-size: 10.5px;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #simahad-admin-dashboard .sd-feed-side {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
            flex-shrink: 0;
            padding-left: 6px;
            color: var(--sd-faint);
            font-size: 10px;
            white-space: nowrap;
        }

        #simahad-admin-dashboard .sd-feed-time {
            color: #a5abb5;
            font-size: 9.5px;
        }

        /* ---------- Komposisi role ---------- */
        #simahad-admin-dashboard .sd-role { margin-top: 12px; }
        #simahad-admin-dashboard .sd-role:first-child { margin-top: 2px; }

        #simahad-admin-dashboard .sd-role-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 5px;
            font-size: 11px;
        }

        #simahad-admin-dashboard .sd-role-head > span:first-child { color: #555d6d; font-weight: 650; }

        #simahad-admin-dashboard .sd-role-head > span:last-child {
            display: inline-flex;
            align-items: baseline;
            gap: 6px;
            font-variant-numeric: tabular-nums;
        }

        #simahad-admin-dashboard .sd-role-head strong { color: #303747; font-size: 11.5px; font-weight: 800; }
        #simahad-admin-dashboard .sd-role-head small { color: var(--sd-faint); font-size: 9.5px; font-weight: 650; }

        #simahad-admin-dashboard .sd-role-track {
            display: block;
            height: 6px;
            border-radius: 999px;
            background: #f0f1f5;
            overflow: hidden;
        }

        #simahad-admin-dashboard .sd-role-track span {
            display: block;
            height: 100%;
            width: var(--sd-pct, 0%);
            border-radius: inherit;
            background: #982c49;
        }

        #simahad-admin-dashboard .sd-role.is-guru .sd-role-track span { background: #c45c73; }
        #simahad-admin-dashboard .sd-role.is-musyrifah .sd-role-track span { background: #da9b4b; }
        #simahad-admin-dashboard .sd-role.is-pimpinan .sd-role-track span { background: #6c86b6; }
        #simahad-admin-dashboard .sd-role.is-admin .sd-role-track span { background: #7f6ea2; }

        /* ---------- Pengguna terbaru ---------- */
        #simahad-admin-dashboard .sd-mini {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
            padding: 8px 0;
            border-top: 1px solid var(--sd-line-soft);
        }

        #simahad-admin-dashboard .sd-mini:first-child { border-top: 0; padding-top: 2px; }

        #simahad-admin-dashboard .sd-mini .sd-avatar { width: 30px; height: 30px; border-radius: 9px; font-size: 10px; }

        #simahad-admin-dashboard .sd-mini-copy { display: flex; flex: 1; flex-direction: column; gap: 1px; min-width: 0; }

        #simahad-admin-dashboard .sd-mini-name {
            overflow: hidden;
            color: #2f3645;
            font-size: 11px;
            font-weight: 750;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #simahad-admin-dashboard .sd-mini-sub {
            overflow: hidden;
            color: var(--sd-faint);
            font-size: 10px;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #simahad-admin-dashboard .sd-mini-side {
            flex-shrink: 0;
            color: #a5abb5;
            font-size: 9.5px;
            white-space: nowrap;
        }

        /* ---------- Status sistem ---------- */
        #simahad-admin-dashboard .sd-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 9px 0;
            border-bottom: 1px solid var(--sd-line-soft);
            font-size: 11px;
        }

        #simahad-admin-dashboard .sd-row:first-child { padding-top: 0; }
        #simahad-admin-dashboard .sd-row:last-child { border-bottom: 0; padding-bottom: 0; }

        #simahad-admin-dashboard .sd-row-key { flex-shrink: 0; color: var(--sd-muted); }

        #simahad-admin-dashboard .sd-row-val {
            color: #333b4c;
            font-weight: 700;
            text-align: right;
            overflow-wrap: anywhere;
        }

        #simahad-admin-dashboard .sd-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 12px;
            padding-top: 11px;
            border-top: 1px solid var(--sd-line-soft);
        }

        #simahad-admin-dashboard .sd-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #637082;
            font-size: 9.5px;
            font-weight: 800;
            line-height: 1.2;
            white-space: nowrap;
            text-transform: uppercase;
        }

        #simahad-admin-dashboard .sd-pill::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        #simahad-admin-dashboard .sd-pill.is-green { background: #e8f7ee; color: #147b4d; }
        #simahad-admin-dashboard .sd-pill.is-yellow { background: #fff5e0; color: #a96b06; }
        #simahad-admin-dashboard .sd-pill.is-red { background: #fcebed; color: #ba3c53; }

        /* ---------- Kondisi kosong ---------- */
        #simahad-admin-dashboard .sd-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            padding: 30px 14px;
            color: var(--sd-faint);
            text-align: center;
        }

        #simahad-admin-dashboard .sd-empty-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #f5f4f6;
            color: #a3a6b1;
        }

        #simahad-admin-dashboard .sd-empty-icon svg { width: 19px; height: 19px; }

        #simahad-admin-dashboard .sd-empty strong { color: #656f7e; font-size: 11.5px; font-weight: 750; }
        #simahad-admin-dashboard .sd-empty small { max-width: 260px; font-size: 10.5px; line-height: 1.5; }

        /* ---------- Footer ---------- */
        #simahad-admin-dashboard .sd-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
            padding: 12px 2px 4px;
            border-top: 1px solid var(--sd-line);
            color: var(--sd-faint);
            font-size: 10.5px;
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 1250px) {
            #simahad-admin-dashboard .sd-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            #simahad-admin-dashboard .sd-quick { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 1050px) {
            #simahad-admin-dashboard .sd-grid { grid-template-columns: minmax(0, 1fr); }
        }

        @media (max-width: 860px) {
            #simahad-admin-dashboard .sd-hero {
                flex-direction: column;
                align-items: stretch;
                gap: 20px;
                padding: 25px;
            }

            #simahad-admin-dashboard .sd-hero-board { min-width: 0; }
        }

        @media (max-width: 620px) {
            #simahad-admin-dashboard .sd-hero { padding: 22px 20px; border-radius: 16px; }
            #simahad-admin-dashboard .sd-hero h1 { font-size: 22px; }
            #simahad-admin-dashboard .sd-hero-stat { padding: 10px 10px; }
            #simahad-admin-dashboard .sd-hero-stat span { font-size: 10.5px; }
            #simahad-admin-dashboard .sd-quick { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            #simahad-admin-dashboard .sd-kpis { gap: 10px; }
            #simahad-admin-dashboard .sd-kpi { padding: 14px; border-radius: 13px; }
            #simahad-admin-dashboard .sd-kpi-value { font-size: 27px; }
            #simahad-admin-dashboard .sd-card-head { padding: 13px 15px 11px; }
            #simahad-admin-dashboard .sd-card-body { padding: 12px 15px 14px; }
            #simahad-admin-dashboard .sd-feed { max-height: 360px; padding: 2px 15px 8px; }
            #simahad-admin-dashboard .sd-chart { height: 150px; gap: 5px; }
            #simahad-admin-dashboard .sd-card-head p { display: none; }
            #simahad-admin-dashboard .sd-feed-side { max-width: 84px; white-space: normal; }
        }

        @media (prefers-reduced-motion: reduce) {
            #simahad-admin-dashboard .sd-kpi,
            #simahad-admin-dashboard .sd-quick-link,
            #simahad-admin-dashboard .sd-chart-bar { transition: none; }
        }
    </style>

    <main id="simahad-admin-dashboard">
        <div class="sd-topline">
            
            <span class="sd-date">
                <x-panel.ikon nama="jadwal" />
                {{ now()->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <header class="sd-hero">
            <div class="sd-hero-content">
                <span class="sd-hero-label">Admin Sistem</span>
                <h1>Assalamu’alaikum, {{ auth()->user()->name ?? 'Admin' }}</h1>
                <p>Kelola pengguna, pantau aktivitas terbaru, dan pastikan pengaturan SIMAHAD berjalan sesuai kebutuhan.</p>
            </div>
            <div class="sd-hero-board" role="group" aria-label="Ringkasan aktivitas hari ini">
                <div class="sd-hero-stat">
                    <span>Aktivitas</span>
                    <strong>{{ number_format($aktivitas['hariIni'], 0, ',', '.') }}</strong>
                </div>
                <div class="sd-hero-stat">
                    <span>Login</span>
                    <strong>{{ number_format($aktivitas['loginHariIni'], 0, ',', '.') }}</strong>
                </div>
                <div class="sd-hero-stat">
                    <span>Akun baru</span>
                    <strong>{{ number_format($aktivitas['akunBaruTujuhHari'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </header>

        <nav class="sd-quick" aria-label="Aksi cepat">
            <a class="sd-quick-link" href="{{ route('admin.manajemen-pengguna.create') }}">
                <span class="sd-quick-icon"><x-panel.ikon nama="pengguna" /></span>
                <span>Tambah Pengguna</span>
            </a>
            <a class="sd-quick-link" href="{{ route('admin.manajemen-pengguna') }}">
                <span class="sd-quick-icon"><x-panel.ikon nama="pegawai" /></span>
                <span>Kelola Pengguna</span>
                @if ($akunMenunggu > 0)
                    <span class="sd-quick-badge" title="Akun menunggu aktivasi">{{ $akunMenunggu }}</span>
                @endif
            </a>
            <a class="sd-quick-link" href="{{ route('admin.data-pegawai.index') }}">
                <span class="sd-quick-icon"><x-panel.ikon nama="pegawai" /></span>
                <span>Data Pegawai</span>
            </a>
            <a class="sd-quick-link" href="{{ route('admin.data-mahasiswa.index') }}">
                <span class="sd-quick-icon"><x-panel.ikon nama="mahasiswa" /></span>
                <span>Data Mahasiswa</span>
            </a>
            <a class="sd-quick-link" href="{{ route('admin.log-aktivitas') }}">
                <span class="sd-quick-icon"><x-panel.ikon nama="log" /></span>
                <span>Log Aktivitas</span>
            </a>
            <a class="sd-quick-link" href="{{ route('admin.pengaturan-sistem') }}">
                <span class="sd-quick-icon"><x-panel.ikon nama="sistem" /></span>
                <span>Pengaturan Sistem</span>
            </a>
        </nav>

        <section class="sd-section" aria-labelledby="sd-kpi-heading">
            <div class="sd-section-head">
                <div>
                    <h2 id="sd-kpi-heading">Ringkasan Aktivitas</h2>
                </div>
                <a class="sd-link" href="{{ route('admin.log-aktivitas') }}">Buka log aktivitas</a>
            </div>
            <div class="sd-kpis">
                <a class="sd-kpi is-maroon" href="{{ route('admin.log-aktivitas') }}">
                    <div class="sd-kpi-top">
                        <span>Aktivitas Hari Ini</span>
                        <span class="sd-kpi-icon"><x-panel.ikon nama="log" /></span>
                    </div>
                    <strong class="sd-kpi-value">{{ number_format($aktivitas['hariIni'], 0, ',', '.') }}</strong>
                    <span class="sd-kpi-foot">{{ number_format($aktivitas['tujuhHari'], 0, ',', '.') }} aktivitas dalam 7 hari terakhir</span>
                </a>
                <a class="sd-kpi is-green" href="{{ route('admin.manajemen-pengguna') }}">
                    <div class="sd-kpi-top">
                        <span>Login Hari Ini</span>
                        <span class="sd-kpi-icon"><x-panel.ikon nama="profil" /></span>
                    </div>
                    <strong class="sd-kpi-value">{{ number_format($aktivitas['loginHariIni'], 0, ',', '.') }}</strong>
                    <span class="sd-kpi-foot">dari {{ number_format($akunAktif, 0, ',', '.') }} akun aktif</span>
                </a>
                <a class="sd-kpi is-blue" href="{{ route('admin.manajemen-pengguna') }}">
                    <div class="sd-kpi-top">
                        <span>Akun Baru (7 Hari)</span>
                        <span class="sd-kpi-icon"><x-panel.ikon nama="pendaftaran" /></span>
                    </div>
                    <strong class="sd-kpi-value">{{ number_format($aktivitas['akunBaruTujuhHari'], 0, ',', '.') }}</strong>
                    <span class="sd-kpi-foot">total {{ number_format($totalAkun, 0, ',', '.') }} pengguna terdaftar</span>
                </a>
                <a class="sd-kpi is-amber" href="{{ route('admin.manajemen-pengguna', ['status' => 'menunggu']) }}">
                    <div class="sd-kpi-top">
                        <span>Menunggu Aktivasi</span>
                        <span class="sd-kpi-icon"><x-panel.ikon nama="keluar" /></span>
                    </div>
                    <strong class="sd-kpi-value">{{ number_format($akunMenunggu, 0, ',', '.') }}</strong>
                    <span class="sd-kpi-foot">{{ $akunMenunggu > 0 ? 'perlu ditinjau dan diaktivasi' : 'semua akun sudah teraktivasi' }}</span>
                </a>
            </div>
        </section>

        <section class="sd-section" aria-labelledby="sd-activity-heading">
            <div class="sd-section-head">
                <div>
                    <h2 id="sd-activity-heading">Aktivitas Pengguna</h2>
                    <p>Tren mingguan dan aktivitas terbaru dari seluruh modul SIMAHAD.</p>
                </div>
                <a class="sd-link" href="{{ route('admin.log-aktivitas') }}">Lihat semua log</a>
            </div>
            <div class="sd-grid">
                <div class="sd-col">
                    <article class="sd-card">
                        <div class="sd-card-head">
                            <div class="sd-card-title">
                                <span class="sd-card-mark"><x-panel.ikon nama="laporan" /></span>
                                <div><h3>Tren 7 Hari Terakhir</h3><p>Jumlah aktivitas tercatat per hari</p></div>
                            </div>
                            <div class="sd-card-tools">
                                <span class="sd-chip">{{ number_format($aktivitas['tujuhHari'], 0, ',', '.') }} aktivitas</span>
                            </div>
                        </div>
                        <div class="sd-card-body">
                            <div class="sd-chart" role="img" aria-label="Aktivitas 7 hari terakhir: {{ $ringkasGrafik }}">
                                @foreach ($seriAktivitas as $hari)
                                    @php $tinggi = max(6, (int) round($hari['jumlah'] / $maksAktivitas * 100)); @endphp
                                    <div class="sd-chart-col {{ $loop->last ? 'is-today' : '' }} {{ $hari['jumlah'] === 0 ? 'is-empty' : '' }}">
                                        <span class="sd-chart-value">{{ $hari['jumlah'] }}</span>
                                        <span class="sd-chart-track">
                                            <span class="sd-chart-bar" style="--sd-bar: {{ $tinggi }}%" title="{{ $hari['jumlah'] }} aktivitas · {{ \Illuminate\Support\Carbon::parse($hari['tanggal'])->locale('id')->translatedFormat('l, d F Y') }}"></span>
                                        </span>
                                        <span class="sd-chart-day">{{ $hari['label'] }}</span>
                                        <span class="sd-chart-date">{{ \Illuminate\Support\Carbon::parse($hari['tanggal'])->format('d/m') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="sd-chart-foot">
                                <span>Rata-rata <strong>{{ number_format($rataAktivitas, 1, ',', '.') }}</strong> aktivitas per hari</span>
                                <span class="sd-legend"><i></i> Hari ini</span>
                            </div>
                        </div>
                    </article>

                    <article class="sd-card">
                        <div class="sd-card-head">
                            <div class="sd-card-title">
                                <span class="sd-card-mark"><x-panel.ikon nama="log" /></span>
                                <div><h3>Aktivitas Terbaru</h3><p>Semua aktivitas pengguna lintas modul</p></div>
                            </div>
                            <div class="sd-card-tools">
                                <span class="sd-chip">{{ $ringkasan['aktivitasTerbaru']->count() }} terbaru</span>
                                <a class="sd-card-link" href="{{ route('admin.log-aktivitas') }}">Lihat semua</a>
                            </div>
                        </div>
                        <div class="sd-feed" tabindex="0" role="region" aria-label="Aktivitas pengguna terbaru">
                            @forelse ($ringkasan['aktivitasTerbaru'] as $log)
                                <div class="sd-feed-item">
                                    <span class="sd-avatar {{ $kelasPeran($log->user_role) }}" aria-hidden="true">{{ $inisial($log->user_name ?: 'Sistem') }}</span>
                                    <div class="sd-feed-copy">
                                        <span class="sd-feed-name" title="{{ $log->description ?: $labelAksi($log) }}">{{ $log->description ?: $labelAksi($log) }}</span>
                                        <span class="sd-feed-sub" title="{{ $labelModul($log) }} · {{ $namaPeran($log->user_role) }} · {{ $log->user_name ?: 'Sistem' }}">{{ $labelModul($log) }} · {{ $namaPeran($log->user_role) }} · {{ $log->user_name ?: 'Sistem' }}</span>
                                    </div>
                                    <div class="sd-feed-side">
                                        <time datetime="{{ $log->created_at?->toIso8601String() }}" title="{{ $log->created_at?->locale('id')->translatedFormat('d M Y, H:i') }}">{{ $log->created_at?->locale('id')->diffForHumans() ?? '-' }}</time>
                                        @if ($log->created_at)
                                            <span class="sd-feed-time">{{ $log->created_at->locale('id')->translatedFormat('d M, H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="sd-empty">
                                    <span class="sd-empty-icon"><x-panel.ikon nama="log" /></span>
                                    <strong>Belum ada aktivitas tercatat</strong>
                                    <small>Aktivitas pengguna akan tampil di sini setelah tercatat oleh sistem.</small>
                                </div>
                            @endforelse
                        </div>
                    </article>
                </div>

                <aside class="sd-col">
                    <article class="sd-card">
                        <div class="sd-card-head">
                            <div class="sd-card-title">
                                <span class="sd-card-mark"><x-panel.ikon nama="pengguna" /></span>
                                <div><h3>Komposisi Pengguna</h3><p>Distribusi akun per peran</p></div>
                            </div>
                            <span class="sd-chip">{{ number_format($totalAkun, 0, ',', '.') }} akun</span>
                        </div>
                        <div class="sd-card-body">
                            @foreach ($daftarPeran as $item)
                                @php $persentase = round($item['jumlah'] / $totalPeran * 100, 1); @endphp
                                <div class="sd-role is-{{ $item['kelas'] }}">
                                    <div class="sd-role-head">
                                        <span>{{ $item['nama'] }}</span>
                                        <span>
                                            <strong>{{ number_format($item['jumlah'], 0, ',', '.') }}</strong>
                                            <small>{{ $formatPersen($persentase) }}%</small>
                                        </span>
                                    </div>
                                    <span class="sd-role-track" role="img" aria-label="{{ $item['nama'] }}: {{ $item['jumlah'] }} akun ({{ $formatPersen($persentase) }} persen)">
                                        <span style="--sd-pct: {{ $persentase }}%"></span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <article class="sd-card">
                        <div class="sd-card-head">
                            <div class="sd-card-title">
                                <span class="sd-card-mark"><x-panel.ikon nama="pendaftaran" /></span>
                                <div><h3>Pengguna Terbaru</h3><p>Akun yang baru ditambahkan</p></div>
                            </div>
                            <span class="sd-chip">{{ $ringkasan['akunTerbaru']->count() }} akun</span>
                        </div>
                        <div class="sd-card-body">
                            @forelse ($ringkasan['akunTerbaru'] as $akun)
                                <div class="sd-mini">
                                    <span class="sd-avatar {{ $kelasPeran($akun->role) }}" aria-hidden="true">{{ $inisial($akun->name) }}</span>
                                    <div class="sd-mini-copy">
                                        <span class="sd-mini-name" title="{{ $akun->name }}">{{ $akun->name }}</span>
                                        <span class="sd-mini-sub" title="{{ $akun->email }}">{{ $namaPeran($akun->role) }} · {{ $akun->email }}</span>
                                    </div>
                                    <span class="sd-mini-side">{{ $akun->created_at?->locale('id')->diffForHumans() }}</span>
                                </div>
                            @empty
                                <div class="sd-empty"><strong>Belum ada akun baru</strong></div>
                            @endforelse
                            <a class="sd-card-link" href="{{ route('admin.manajemen-pengguna') }}">Kelola pengguna</a>
                        </div>
                    </article>

                    <article class="sd-card">
                        <div class="sd-card-head">
                            <div class="sd-card-title">
                                <span class="sd-card-mark"><x-panel.ikon nama="sistem" /></span>
                                <div><h3>Status Sistem</h3><p>Konfigurasi yang sedang digunakan</p></div>
                            </div>
                            <a class="sd-card-link" href="{{ route('admin.pengaturan-sistem') }}">Ubah</a>
                        </div>
                        <div class="sd-card-body">
                            <div class="sd-row"><span class="sd-row-key">Nama sistem</span><strong class="sd-row-val">{{ $konfigurasi['nama_sistem'] }}</strong></div>
                            <div class="sd-row"><span class="sd-row-key">Institusi</span><strong class="sd-row-val">{{ $konfigurasi['nama_institusi'] }}</strong></div>
                            <div class="sd-row"><span class="sd-row-key">Durasi sesi</span><strong class="sd-row-val">{{ $konfigurasi['durasi_sesi'] ? $konfigurasi['durasi_sesi'].' menit' : 'Bawaan' }}</strong></div>
                            <div class="sd-row"><span class="sd-row-key">Pengiriman email</span><strong class="sd-row-val">{{ $konfigurasi['mail'] }}</strong></div>
                            <div class="sd-pills">
                                <span class="sd-pill {{ $konfigurasi['google_login'] ? 'is-green' : 'is-yellow' }}">Google {{ $konfigurasi['google_login'] ? 'aktif' : 'belum diatur' }}</span>
                                <span class="sd-pill {{ $konfigurasi['debug'] ? 'is-red' : 'is-green' }}">Debug {{ $konfigurasi['debug'] ? 'aktif' : 'nonaktif' }}</span>
                                <span class="sd-pill {{ $notifAktif === 3 ? 'is-green' : ($notifAktif > 0 ? 'is-yellow' : 'is-red') }}">Notifikasi {{ $notifAktif }}/3</span>
                            </div>
                        </div>
                    </article>
                </aside>
            </div>
        </section>

        
    </main>
@endsection
