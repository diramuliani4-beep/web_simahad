
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Mahasiswa - SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --maroon: #790d20;
            --maroon-dark: #650919;
            --border: #8e1b2b;
            --page: #f8f8f8;
            --text: #171717;
            --muted: #777;
            --green: #2f925c;
            --orange: #c58a24;
            --red: #d64b4b;
            --blue: #5798ed;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background: var(--page);
        }

        .topbar {
            height: 89px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 31px 0 21px;
            background: #fff;
            border: 1px solid var(--border);
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 20;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 13px;
            color: var(--maroon);
            font-size: 31px;
            font-weight: 700;
        }

        .brand img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 30px;
            color: var(--maroon);
        }

        .notification {
            font-size: 28px;
        }

        .avatar {
            width: 61px;
            height: 61px;
            border: 1.5px solid #222;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #40454a;
            font-size: 46px;
            overflow: hidden;
        }

        .sidebar {
            position: fixed;
            top: 89px;
            left: 0;
            bottom: 0;
            width: 300px;
            overflow-y: auto;
            border: 1px solid var(--border);
            background: #b8d7ef;
            z-index: 10;
        }

        .sidebar-bg {
            position: absolute;
            inset: 0;
            z-index: -1;
            background-image:
                linear-gradient(
                    rgba(222,237,248,.56),
                    rgba(255,255,255,.62)
                ),
                url('{{ asset("images/mahad_al_jamiah.jpeg") }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
        }

        .nav-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 22px 12px;
        }

        .nav-link {
            min-height: 46px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 11px;
            border: 1px solid #202020;
            border-radius: 12px;
            background: #fff;
            color: #111;
            text-decoration: none;
            font-size: 14px;
            transition: .18s ease;
        }

        .nav-link i {
            color: #667080;
            width: 20px;
            text-align: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .nav-link:hover {
            border-color: var(--maroon);
            background: #fff7f8;
            color: var(--maroon);
        }

        .nav-link.active {
            border-color: var(--maroon);
            color: var(--maroon);
            background: #faeef0;
            box-shadow: 0 2px 4px #0002;
            font-weight: 600;
        }

        .nav-link.active i {
            color: var(--maroon);
        }

        .nav-arrow {
            margin-left: auto;
        }

        .nav-link.logout {
            font-weight: 600;
        }

        .logout-form {
            margin: 0;
        }

        .logout-form button {
            width: 100%;
            font-family: inherit;
            text-align: left;
            cursor: pointer;
        }

        .main {
            margin-left: 300px;
            padding-top: 89px;
            min-height: 100vh;
        }

        .page-heading {
            padding: 27px 35px 12px;
            border-bottom: 1px solid var(--border);
            background: #f8f8f8;
        }

        .breadcrumb {
            color: var(--maroon);
            font-size: 19px;
            margin-bottom: 8px;
        }

        h1 {
            font-size: 32px;
            line-height: 1.25;
            margin: 0 0 3px;
            font-weight: 600;
        }

        .subtitle {
            margin: 0;
            font-size: 17px;
        }

        .content {
            padding: 15px 35px 35px;
        }

        .section-title {
            font-size: 19px;
            font-weight: 600;
            margin: 0 0 12px;
        }

        .info-note {
            font-size: 11px;
            color: var(--maroon);
            background: #f9eff1;
            padding: 10px 12px;
            margin: 0 0 18px;
            border-radius: 7px;
        }

        .info-note strong {
            display: block;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 13px 25px;
        }

        .summary-card {
            min-height: 136px;
            padding: 16px 25px 12px;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fff;
            position: relative;
        }

        .summary-icon {
            font-size: 20px;
            margin-bottom: 12px;
        }

        .summary-card strong {
            display: block;
            font-size: 25px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .summary-card p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .pill {
            position: absolute;
            top: 16px;
            right: 20px;
            border-radius: 18px;
            padding: 2px 9px;
            font-size: 12px;
            font-weight: 600;
        }

        .green { color: var(--green); }
        .orange { color: var(--orange); }
        .red { color: var(--red); }
        .blue { color: var(--blue); }

        .pill.green { background: #dcf5e7; }
        .pill.orange { background: #ffeadf; }
        .pill.red { background: #ffe1e1; }
        .pill.blue { background: #e0efff; }

        .progress-card {
            border: 1px solid #bd6a77;
            border-radius: 11px;
            padding: 15px 25px;
            background: #fff;
            margin: 15px 0 20px;
        }

        .progress-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #444;
            margin-bottom: 13px;
            font-size: 14px;
        }

        .progress-top span:last-child {
            color: var(--maroon);
        }

        .progress-track {
            height: 10px;
            border-radius: 20px;
            background: #e8e4e2;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 75%;
            border-radius: 20px;
            background: var(--maroon);
        }

        .filter-panel {
            border: 1px solid #e7dfe0;
            border-radius: 11px;
            background: #fff;
            padding: 15px;
            margin: 0 0 20px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: 600;
            color: #444;
            margin: 0 0 14px;
        }

        .filters {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .filter-field label {
            display: block;
            color: #777;
            font-size: 13px;
            margin-bottom: 5px;
        }

        select {
            font-family: inherit;
            width: 100%;
            border: 1px solid #ded8d4;
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
            color: #444;
            background: #fff;
            outline-color: var(--maroon);
        }

        .history {
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fff;
            padding: 0 13px;
        }

        .history-item {
            min-height: 95px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid #e4d8d8;
            padding: 13px 0;
        }

        .history-item:last-of-type {
            border-bottom: 0;
        }

        .calendar-box {
            width: 51px;
            height: 51px;
            flex: 0 0 51px;
            display: grid;
            place-items: center;
            border-radius: 9px;
            background: #f9ecef;
            color: var(--maroon);
            font-size: 21px;
        }

        .history-detail {
            flex: 1;
            min-width: 0;
        }

        .history-detail strong {
            display: block;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .history-detail small {
            color: var(--muted);
            font-size: 12px;
        }

        .status {
            font-size: 14px;
            font-weight: 600;
            align-self: flex-start;
            padding-top: 3px;
        }

        .empty {
            display: none;
            padding: 25px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 1100px) {
            .sidebar {
                width: 260px;
            }

            .main {
                margin-left: 260px;
            }

            .page-heading,
            .content {
                padding-left: 25px;
                padding-right: 25px;
            }

            .summary-grid {
                gap: 12px;
            }
        }

        @media (max-width: 760px) {
            .topbar {
                height: 72px;
                padding: 0 14px;
            }

            .brand {
                font-size: 22px;
            }

            .brand img {
                width: 45px;
                height: 45px;
            }

            .top-actions {
                gap: 14px;
            }

            .avatar {
                width: 43px;
                height: 43px;
                font-size: 32px;
            }

            .sidebar {
                position: relative;
                top: 72px;
                width: 100%;
                height: auto;
                bottom: auto;
            }

            .nav-list {
                padding: 12px;
                gap: 8px;
            }

            .nav-link {
                min-height: 43px;
            }

            .main {
                margin: 72px 0 0;
                padding-top: 0;
            }

            .page-heading {
                padding: 18px 16px;
            }

            .content {
                padding: 14px 16px 25px;
            }

            .summary-grid,
            .filters {
                grid-template-columns: 1fr;
            }

            .subtitle {
                font-size: 15px;
            }

            h1 {
                font-size: 27px;
            }

            .history-item {
                gap: 10px;
            }

            .history-detail strong {
                font-size: 14px;
            }

            .history-detail small {
                font-size: 11px;
            }

            .status {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="brand">
            <img
                src="{{ asset('images/logo_uin.png') }}"
                alt="Logo SIMAHAD"
                onerror="this.style.display='none'"
            >
            <span>SIMAHAD</span>
        </div>

        <div class="top-actions">
            <i class="fa-regular fa-bell notification" aria-label="Notifikasi"></i>
            <div class="avatar">
                <i class="fa-solid fa-circle-user"></i>
            </div>
        </div>
    </header>

    <aside class="sidebar">
        <div class="sidebar-bg"></div>

        <nav class="nav-list" aria-label="Menu utama">

            <a
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                href="{{ route('dashboard') }}"
            >
                <i class="fa-solid fa-border-all"></i>
                <span>Dashboard</span>
            </a>

            <a
                class="nav-link {{ request()->routeIs('pendaftaran.*') ? 'active' : '' }}"
                href="{{ route('pendaftaran.pribadi') }}"
            >
                <i class="fa-solid fa-user-pen"></i>
                <span>Pendaftaran</span>
            </a>

            <a
                class="nav-link {{ request()->routeIs('pembelajaran.mhs') ? 'active' : '' }}"
                href="{{ route('pembelajaran.mhs') }}"
            >
                <i class="fa-solid fa-book-open"></i>
                <span>Pembelajaran</span>
            </a>

            <a
                class="nav-link {{ request()->routeIs('kamar') ? 'active' : '' }}"
                href="{{ route('kamar') }}"
            >
                <i class="fa-solid fa-bed"></i>
                <span>Informasi Kamar</span>
            </a>

            <a
                class="nav-link {{ request()->routeIs('pembayaran') ? 'active' : '' }}"
                href="{{ route('pembayaran') }}"
            >
                <i class="fa-solid fa-wallet"></i>
                <span>Pembayaran</span>
            </a>

            <a
                class="nav-link {{ request()->routeIs('absensi.mhs') ? 'active' : '' }}"
                href="{{ route('absensi.mhs') }}"
                @if(request()->routeIs('absensi.mhs')) aria-current="page" @endif
            >
                <i class="fa-regular fa-clipboard"></i>
                <span>Absensi</span>
                @if(request()->routeIs('absensi.mhs'))
                    <i class="fa-solid fa-angle-right nav-arrow"></i>
                @endif
            </a>

            <a
                class="nav-link {{ request()->routeIs('pengumuman.mhs') ? 'active' : '' }}"
                href="{{ route('pengumuman.mhs') }}"
            >
                <i class="fa-solid fa-bullhorn"></i>
                <span>Pengumuman</span>
            </a>

            {{-- Aktifkan tautan ini jika route surat dan perizinan sudah tersedia. --}}
            
<a
    class="nav-link {{ request()->routeIs('surat.perizinan.mhs*') ? 'active' : '' }}"
    href="{{ route('surat.perizinan.mhs') }}"
>
    <i class="fa-regular fa-file-lines"></i>
    <span>Surat dan Perizinan</span>
</a>


            {{-- Aktifkan tautan ini jika route pengaturan akun sudah tersedia. --}}
            <a
                class="nav-link"
                href="#"
                title="Halaman Pengaturan Akun belum memiliki route"
                onclick="event.preventDefault(); alert('Halaman Pengaturan Akun belum tersedia.');"
            >
                <i class="fa-solid fa-gear"></i>
                <span>Pengaturan Akun</span>
            </a>

            <form
                class="logout-form"
                action="{{ route('logout') }}"
                method="POST"
                onsubmit="return confirm('Apakah kamu yakin ingin keluar?')"
            >
                @csrf
                <button type="submit" class="nav-link logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar</span>
                </button>
            </form>

        </nav>
    </aside>

    <main class="main">
        <section class="page-heading">
            <div class="breadcrumb">Dashboard / Absensi</div>
            <h1>Absensi</h1>
            <p class="subtitle">
                Pantau kehadiran dan riwayat pembelajaranmu di Ma’had.
            </p>
        </section>

        <section class="content">
            <div class="info-note">
                <strong>
                    <i class="fa-solid fa-circle-info"></i>
                    &nbsp;Informasi
                </strong>
                Jika terdapat ketidaksesuaian data kehadiran, hubungi pengajar
                atau musyrifah yang bertanggung jawab.
            </div>

            <h2 class="section-title">Ringkasan Kehadiran</h2>

            <div class="summary-grid">
                <article class="summary-card">
                    <div class="summary-icon green">
                        <i class="fa-regular fa-circle-check"></i>
                    </div>
                    <span class="pill green">Hadir</span>
                    <strong>10</strong>
                    <p>Pertemuan hadir</p>
                </article>

                <article class="summary-card">
                    <div class="summary-icon orange">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <span class="pill orange">Izin</span>
                    <strong>1</strong>
                    <p>Pertemuan izin</p>
                </article>

                <article class="summary-card">
                    <div class="summary-icon red">
                        <i class="fa-regular fa-circle-xmark"></i>
                    </div>
                    <span class="pill red">Alpa</span>
                    <strong>1</strong>
                    <p>Pertemuan alpa</p>
                </article>

                <article class="summary-card">
                    <div class="summary-icon" style="color:var(--maroon)">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <span class="pill blue">83,3%</span>
                    <strong>83,3%</strong>
                    <p>Persentase kehadiran</p>
                </article>
            </div>

            <div class="progress-card">
                <div class="progress-top">
                    <span>Progres Pertemuan</span>
                    <span>12 dari 16</span>
                </div>

                <div
                    class="progress-track"
                    role="progressbar"
                    aria-label="Progres pertemuan"
                    aria-valuenow="75"
                    aria-valuemin="0"
                    aria-valuemax="100"
                >
                    <div class="progress-fill"></div>
                </div>
            </div>

            <section class="filter-panel">
                <h2 class="filter-title">
                    <i
                        class="fa-solid fa-clock-rotate-left"
                        style="color:var(--maroon); margin-right:8px"
                    ></i>
                    Riwayat Absensi
                </h2>

                <div class="filters">
                    <div class="filter-field">
                        <label for="filter-mapel">Filter Mata Pelajaran</label>

                        <select id="filter-mapel">
                            <option value="">Semua Mata Pelajaran</option>
                            <option value="Pembelajaran Ma’had">Pembelajaran Ma’had</option>
                            <option value="Tahsin">Tahsin</option>
                            <option value="Tahfiz">Tahfiz</option>
                            <option value="Bahasa Arab">Bahasa Arab</option>
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="filter-status">Filter Status</label>

                        <select id="filter-status">
                            <option value="">Semua Status</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Alpa">Alpa</option>
                        </select>
                    </div>
                </div>
            </section>

            <section
                class="history"
                id="history-list"
                aria-label="Daftar riwayat absensi"
            >
                <article
                    class="history-item"
                    data-mapel="Pembelajaran Ma’had"
                    data-status="Hadir"
                >
                    <div class="calendar-box">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div class="history-detail">
                        <strong>Pembelajaran Ma’had</strong>
                        <small>10 Oktober 2026 · Pertemuan 12</small>
                    </div>
                    <span class="status green">Hadir</span>
                </article>

                <article
                    class="history-item"
                    data-mapel="Pembelajaran Ma’had"
                    data-status="Izin"
                >
                    <div class="calendar-box">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div class="history-detail">
                        <strong>Pembelajaran Ma’had</strong>
                        <small>8 Oktober 2026 · Pertemuan 11</small>
                    </div>
                    <span class="status orange">Izin</span>
                </article>

                <article
                    class="history-item"
                    data-mapel="Tahsin"
                    data-status="Hadir"
                >
                    <div class="calendar-box">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div class="history-detail">
                        <strong>Tahsin</strong>
                        <small>6 Oktober 2026 · Pertemuan 10</small>
                    </div>
                    <span class="status green">Hadir</span>
                </article>

                <article
                    class="history-item"
                    data-mapel="Pembelajaran Ma’had"
                    data-status="Alpa"
                >
                    <div class="calendar-box">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div class="history-detail">
                        <strong>Pembelajaran Ma’had</strong>
                        <small>3 Oktober 2026 · Pertemuan 9</small>
                    </div>
                    <span class="status red">Alpa</span>
                </article>

                <article
                    class="history-item"
                    data-mapel="Tahfiz"
                    data-status="Hadir"
                >
                    <div class="calendar-box">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div class="history-detail">
                        <strong>Tahfiz</strong>
                        <small>1 Oktober 2026 · Pertemuan 8</small>
                    </div>
                    <span class="status green">Hadir</span>
                </article>

                <div class="empty" id="empty-state">
                    Tidak ada riwayat absensi yang sesuai dengan filter.
                </div>
            </section>
        </section>
    </main>

    <script>
        const mapelFilter = document.getElementById('filter-mapel');
        const statusFilter = document.getElementById('filter-status');
        const items = [...document.querySelectorAll('.history-item')];
        const emptyState = document.getElementById('empty-state');

        function filterHistory() {
            let visible = 0;

            items.forEach(item => {
                const mapelMatch =
                    !mapelFilter.value ||
                    item.dataset.mapel === mapelFilter.value;

                const statusMatch =
                    !statusFilter.value ||
                    item.dataset.status === statusFilter.value;

                const isVisible = mapelMatch && statusMatch;

                item.style.display = isVisible ? 'flex' : 'none';

                if (isVisible) {
                    visible++;
                }
            });

            emptyState.style.display = visible === 0 ? 'block' : 'none';
        }

        mapelFilter.addEventListener('change', filterHistory);
        statusFilter.addEventListener('change', filterHistory);
    </script>
</body>
</html>
