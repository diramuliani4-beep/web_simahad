<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Pengumuman Mahasiswa | SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
            --border: #e5ded9;
            --sidebar-width: 280px;
            --header-height: 66px;
        }

        html {
            min-height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #fff;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        button, input {
            font: inherit;
        }

        /* ================= HEADER ================= */

        .top-navbar {
            height: var(--header-height);
            min-height: var(--header-height);
            background: #fff;
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
            gap: .85rem;
            text-decoration: none;
        }

        .brand-logo-img {
            height: 44px;
            width: auto;
            object-fit: contain;
        }

        .brand-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--primary-maroon);
            letter-spacing: -.01em;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1.15rem;
        }

        .btn-circle-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            cursor: pointer;
            text-decoration: none;
            transition: transform .15s ease;
        }

        .btn-circle-action:hover {
            transform: scale(1.05);
        }

        .btn-bell-badge {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--primary-maroon);
            color: #fff;
        }

        .btn-bell-badge svg {
            width: 21px;
            height: 21px;
            stroke: #fff;
        }

        .btn-profile-badge {
            width: 44px;
            height: 44px;
            border: 2px solid #262626;
            border-radius: 50%;
            color: #262626;
            background: #fff;
        }

        .btn-profile-badge svg {
            width: 28px;
            height: 28px;
        }

        .mobile-toggle-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--primary-maroon);
            cursor: pointer;
            padding: .4rem;
        }

        /* ================= LAYOUT ================= */

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
            border-right: 1.5px solid var(--maroon-border);
            overflow: hidden;
            background: #f1f5f9;
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
                rgba(219, 234, 254, .43) 0%,
                rgba(241, 245, 249, .55) 35%,
                rgba(255, 255, 255, .80) 70%,
                rgba(255, 255, 255, .93) 100%
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
            opacity: .28;
            width: 100%;
            padding: 0 10px;
        }

        .watermark-emblem {
            display: block;
            margin: 0 auto 4px;
        }

        .watermark-text-2 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #334155;
            line-height: 1.2;
        }

        .sidebar-content {
            position: relative;
            z-index: 4;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: calc(100vh - var(--header-height));
            justify-content: space-between;
            padding: 1.25rem 1.15rem 1.5rem;
        }

        .sidebar-nav,
        .sidebar-nav-bottom {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .sidebar-nav {
            flex-grow: 1;
        }

        .sidebar-nav-bottom {
            margin-top: 1.5rem;
        }

        .nav-pill,
        .btn-keluar-pill {
            display: flex;
            align-items: center;
            gap: .85rem;
            min-height: 46px;
            padding: 0 1rem;
            background: #fff;
            border: 1.5px solid var(--maroon-border);
            border-radius: 12px;
            text-decoration: none;
            color: #1a1a1a;
            font-size: .92rem;
            font-weight: 700;
            transition: all .2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .03);
            cursor: pointer;
            width: 100%;
        }

        .nav-pill:hover,
        .btn-keluar-pill:hover {
            transform: translateX(3px);
            background: #fff8f9;
        }

        .nav-pill.active {
            background: var(--maroon-tint);
            border-color: var(--primary-maroon);
            color: var(--primary-maroon);
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
            width: 21px;
            height: 21px;
            stroke: #667085;
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

        .nav-chevron {
            margin-left: auto;
            color: var(--primary-maroon);
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1;
        }

        .btn-keluar-pill {
            text-align: left;
            font-family: inherit;
        }

        .btn-keluar-pill:hover {
            color: var(--primary-maroon);
        }

        /* ================= KONTEN PENGUMUMAN ================= */

        .main-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .page-heading {
            padding: 1.9rem 2.75rem 1rem;
            border-bottom: 1.5px solid var(--maroon-border);
            background: #fff;
        }

        .breadcrumb {
            color: var(--primary-maroon);
            font-size: 1.15rem;
            font-weight: 500;
            margin-bottom: .75rem;
        }

        .page-heading h1 {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: .35rem;
            color: #111;
        }

        .subtitle {
            font-size: 1.05rem;
            color: #333;
            line-height: 1.5;
        }

        .content {
            padding: 1.25rem 2.75rem 2.5rem;
        }

        /* ================= PENCARIAN ================= */

        .pencarian {
            display: flex;
            gap: .65rem;
            margin-bottom: 1rem;
        }

        .pencarian input {
            flex: 1;
            min-width: 0;
            padding: .8rem 1.1rem;
            border: 1px solid var(--border);
            border-radius: 30px;
            font-size: .95rem;
            color: var(--text-dark);
            outline: none;
        }

        .pencarian input:focus {
            border-color: var(--primary-maroon);
        }

        .pencarian button {
            padding: .8rem 1.6rem;
            border: 1px solid var(--primary-maroon);
            border-radius: 30px;
            background: var(--primary-maroon);
            color: #fff;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
        }

        .pencarian button:hover {
            background: var(--primary-maroon-hover);
        }

        /* ================= FILTER KATEGORI ================= */

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .filter-btn {
            border: 1px solid var(--border);
            background: #fff;
            color: #555;
            border-radius: 30px;
            padding: .7rem 1rem;
            font-size: .95rem;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .filter-btn:hover {
            border-color: var(--primary-maroon);
            color: var(--primary-maroon);
        }

        .filter-btn.active {
            background: var(--primary-maroon);
            color: #fff;
            border-color: var(--primary-maroon);
        }

        /* ================= ITEM PENGUMUMAN ================= */

        .announcement {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border);
        }

        .announcement[hidden] {
            display: none !important;
        }

        .announcement-icon {
            width: 92px;
            height: 76px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            background: #f8ecef;
            color: var(--primary-maroon);
            border-radius: 12px;
            overflow: hidden;
        }

        .announcement-icon svg {
            width: 25px;
            height: 25px;
        }

        .announcement-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .announcement-body {
            flex: 1;
            min-width: 0;
        }

        .tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        .tag-purple {
            color: #8250c8;
            background: #eee1fa;
        }

        .tag-orange {
            color: #e66b1d;
            background: #ffeadb;
        }

        .tag-blue {
            color: #3185f6;
            background: #dcecff;
        }

        .tag-green {
            color: #21865b;
            background: #dff5e8;
        }

        .tag-pink {
            color: #be185d;
            background: #fce7f3;
        }

        .announcement h2 {
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.45;
            margin: 0 0 .45rem;
            color: #171717;
        }

        .description {
            font-size: .95rem;
            line-height: 1.6;
            color: #77716d;
            margin: 0 0 .4rem;
        }

        .date {
            font-size: .85rem;
            color: #88817d;
            margin-bottom: .55rem;
        }

        .read-more {
            color: var(--primary-maroon);
            font-size: .95rem;
            font-weight: 700;
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        .empty {
            display: none;
            padding: 2rem 0;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ================= MODAL ================= */

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(2px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            max-width: 520px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            padding: 1.85rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, .15);
            position: relative;
            border: 1.5px solid var(--maroon-border);
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--primary-maroon);
            margin: 0 2rem .75rem 0;
        }

        .modal-body {
            font-size: .95rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            overflow-wrap: anywhere;
        }

        .btn-modal-x {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: transparent;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: #94a3b8;
        }

        .btn-modal-action {
            width: 100%;
            background: var(--primary-maroon);
            color: #fff;
            padding: .8rem;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-modal-action:hover {
            background: var(--primary-maroon-hover);
        }

        /* ================= RESPONSIVE ================= */

        .sidebar-backdrop {
            display: none;
        }

        @media (max-width: 992px) {
            .mobile-toggle-btn {
                display: block;
            }

            .sidebar {
                position: fixed;
                top: var(--header-height);
                bottom: 0;
                left: calc(-1 * var(--sidebar-width) - 10px);
                z-index: 46;
                transition: left .3s ease;
                overflow-y: auto;
                box-shadow: 4px 0 15px rgba(0, 0, 0, .1);
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-content {
                min-height: calc(100vh - var(--header-height));
            }

            .sidebar-backdrop.active {
                display: block;
                position: fixed;
                inset: var(--header-height) 0 0;
                background: rgba(0, 0, 0, .4);
                z-index: 45;
            }

            .main-content {
                width: 100%;
            }

            .page-heading {
                padding: 1.5rem 1.5rem 1rem;
            }

            .content {
                padding: 1.25rem 1.5rem 2rem;
            }
        }

        @media (max-width: 600px) {
            .top-navbar {
                padding: 0 1rem;
            }

            .brand-title {
                font-size: 1.2rem;
            }

            .brand-logo-img {
                height: 36px;
            }

            .navbar-actions {
                gap: .65rem;
            }

            .btn-profile-badge {
                width: 38px;
                height: 38px;
            }

            .page-heading {
                padding: 1.25rem 1rem 1rem;
            }

            .breadcrumb {
                font-size: .95rem;
            }

            .page-heading h1 {
                font-size: 1.65rem;
            }

            .subtitle {
                font-size: .9rem;
            }

            .content {
                padding: 1rem;
            }

            .filters {
                gap: .5rem;
            }

            .filter-btn {
                padding: .6rem .8rem;
                font-size: .85rem;
            }

            .announcement {
                gap: .75rem;
                padding: 1.15rem 0;
            }

            .announcement-icon {
                width: 45px;
                height: 45px;
            }

            .announcement-icon svg {
                width: 21px;
                height: 21px;
            }

            .announcement h2 {
                font-size: .95rem;
            }

            .description {
                font-size: .85rem;
            }

            .date,
            .read-more {
                font-size: .8rem;
            }

            .modal-box {
                padding: 1.4rem;
            }
        }
    </style>
</head>

<body>

    <!-- ================= HEADER ================= -->

    <header class="top-navbar">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <button
                type="button"
                class="mobile-toggle-btn"
                id="mobileMenuBtn"
                aria-label="Buka menu"
            >
                <svg width="26" height="26" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="navbar-brand-wrap">
                <img
                    src="{{ asset('images/logo_uin.png') }}"
                    alt="Logo SIMAHAD"
                    class="brand-logo-img"
                    onerror="this.style.display='none'"
                >
                <span class="brand-title">SIMAHAD</span>
            </a>
        </div>

        <div class="navbar-actions">
            <button
                type="button"
                class="btn-circle-action btn-bell-badge"
                title="Pemberitahuan"
                aria-label="Pemberitahuan"
                onclick="openInfoModal('Pemberitahuan', 'Belum ada pengumuman mendesak. Pantau selalu informasi kegiatan asrama di sini.')"
            >
                <svg viewBox="0 0 24 24" fill="none"
                     stroke-width="2.2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </button>

            <x-mahasiswa.profile-dropdown />
        </div>
    </header>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ================= LAYOUT ================= -->

    <div class="dashboard-body">

        {{-- Sidebar bersama seluruh role SIMAHAD. --}}
        <x-panel.sidebar ragam="alir" atas="66px" />

                    


        <!-- ================= KONTEN UTAMA ================= -->

        <main class="main-content">

            <section class="page-heading">
                <div class="breadcrumb">Dashboard / Pengumuman</div>
                <h1>Pengumuman</h1>
                <p class="subtitle">
                    Informasi terbaru seputar kegiatan dan aktivitas Ma’had.
                </p>
            </section>

            <section class="content">

                <form method="GET" action="{{ route('pengumuman.mhs') }}" class="pencarian">
                    <input
                        type="text"
                        name="cari"
                        value="{{ $filter['cari'] ?? '' }}"
                        placeholder="Cari judul pengumuman"
                        autocomplete="off"
                    >

                    @if (filled($filter['kategori'] ?? null))
                        <input type="hidden" name="kategori" value="{{ $filter['kategori'] }}">
                    @endif

                    <button type="submit">Cari</button>
                </form>

                <div class="filters">
                    <a
                        href="{{ route('pengumuman.mhs', array_filter(['cari' => $filter['cari'] ?? null])) }}"
                        class="filter-btn {{ blank($filter['kategori'] ?? null) ? 'active' : '' }}"
                    >
                        Semua
                    </a>

                    @foreach ($kategoriPilihan as $kategori)
                        <a
                            href="{{ route('pengumuman.mhs', array_filter(['cari' => $filter['cari'] ?? null, 'kategori' => $kategori])) }}"
                            class="filter-btn {{ ($filter['kategori'] ?? '') === $kategori ? 'active' : '' }}"
                        >
                            {{ $kategori }}
                        </a>
                    @endforeach
                </div>

                <div id="announcement-list">

                    @forelse ($pengumuman as $item)
                        <article class="announcement" data-category="{{ $item->kategori }}">

                            <div class="announcement-icon">
                                @if ($item->punyaGambar())
                                    <img
                                        src="{{ route('pengumuman.mhs.berkas', [$item, 'gambar']) }}"
                                        alt="Gambar pengumuman {{ $item->judul }}"
                                    >
                                @else
                                    <svg viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m3 11 18-5v12L3 14v-3z"/>
                                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="announcement-body">

                                <span class="tag tag-purple">{{ $item->kategori }}</span>

                                <h2>{{ $item->judul }}</h2>

                                <p class="description">
                                    {{ $item->ringkasan(200) }}
                                </p>

                                <div class="date">
                                    Tanggal publikasi: {{ $item->tanggalTeks() }}
                                </div>

                                <a
                                    class="read-more"
                                    href="{{ route('pengumuman.mhs.show', $item) }}"
                                >
                                    Baca selengkapnya →
                                </a>

                            </div>
                        </article>
                    @empty
                        <div class="empty" style="display: block;">
                            Belum ada pengumuman yang sesuai dengan pencarian atau filter kategori.
                        </div>
                    @endforelse

                </div>

            </section>
        </main>
    </div>

    <!-- ================= MODAL INFORMASI ================= -->

    <div class="modal-overlay" id="infoModal" role="dialog"
         aria-modal="true" aria-labelledby="modalTitle"
         onclick="closeModalOnBackdrop(event)">

        <div class="modal-box">
            <button type="button" class="btn-modal-x"
                    aria-label="Tutup" onclick="closeInfoModal()">
                &times;
            </button>

            <h2 class="modal-title" id="modalTitle">Informasi</h2>
            <div class="modal-body" id="modalBody"></div>

            <button type="button" class="btn-modal-action"
                    onclick="closeInfoModal()">
                Tutup
            </button>
        </div>
    </div>

    <!-- ================= JAVASCRIPT ================= -->

    <script>
        // Buka dan tutup sidebar pada layar kecil
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarBackdrop.classList.toggle('active');
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarBackdrop.classList.remove('active');
        }

        mobileMenuBtn.addEventListener('click', toggleSidebar);
        sidebarBackdrop.addEventListener('click', closeSidebar);

        sidebar.querySelectorAll('a.nav-pill').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 992) closeSidebar();
            });
        });

        // Modal informasi
        function openInfoModal(title, message) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').innerHTML = message;
            document.getElementById('infoModal').classList.add('active');
        }

        function closeInfoModal() {
            document.getElementById('infoModal').classList.remove('active');
        }

        function closeModalOnBackdrop(event) {
            if (event.target.id === 'infoModal') {
                closeInfoModal();
            }
        }

        // Tutup modal dengan tombol Escape
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closeInfoModal();
                closeSidebar();
            }
        });
    </script>

</body>
</html>