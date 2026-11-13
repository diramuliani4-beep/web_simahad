@props([
    'ragam' => 'panel',
    'atas' => '0px',
    'tombolBuka' => false,
])

@php
    $penggunaSidebar = auth()->user();
    $menuUtamaSidebar = \App\Support\MenuPeran::utama($penggunaSidebar);
    $menuBawahSidebar = \App\Support\MenuPeran::bawah($penggunaSidebar);
    $labelPeranSidebar = \App\Support\MenuPeran::labelPeran($penggunaSidebar);
    $berandaSidebar = isset($menuUtamaSidebar[0]) ? route($menuUtamaSidebar[0]['route']) : url('/');
    $fotoSidebar = $penggunaSidebar?->urlFotoProfil();
    $inisialSidebar = $penggunaSidebar ? $penggunaSidebar->inisial() : 'S';
@endphp

@once
    <style>
        :root {
            --sb-w: 280px;
            --sb-w-sempit: 84px;
        }

        .sb {
            position: fixed;
            top: var(--sb-atas, 0px);
            left: 0;
            bottom: 0;
            width: var(--sb-w);
            z-index: 900;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            color: #fdf6ea;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #4a0a14;
            background-image: url('{{ asset('images/mahad.jpeg') }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            border-right: 1px solid rgba(201, 169, 106, .35);
            box-shadow: 8px 0 26px rgba(43, 4, 9, .16);
            transition: width .22s ease, transform .26s ease;
        }

        /* Overlay maroon transparan agar teks dan ikon tetap terbaca. */
        .sb::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background: linear-gradient(
                180deg,
                rgba(107, 13, 27, .90) 0%,
                rgba(88, 9, 20, .93) 45%,
                rgba(54, 5, 12, .96) 100%
            );
        }

        .sb--panel {
            top: var(--header-height, 0px);
        }

        .sb--tetap {
            top: var(--sb-atas, 0px);
        }

        /* Sidebar mengikuti alur flex halaman (bukan fixed). */
        .sb--alir {
            position: sticky;
            top: var(--sb-atas, 0px);
            bottom: auto;
            align-self: flex-start;
            flex: 0 0 var(--sb-w);
            height: calc(100vh - var(--sb-atas, 0px));
            max-height: calc(100vh - var(--sb-atas, 0px));
        }

        body.sb-mandiri {
            padding-left: var(--sb-w);
        }

        body.sb-sempit .sb {
            width: var(--sb-w-sempit);
        }

        body.sb-sempit.sb-mandiri {
            padding-left: var(--sb-w-sempit);
        }

        /* ---------------- Identitas Ma'had ---------------- */
        .sb-kepala {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 13px 13px;
            border-bottom: 1px solid rgba(201, 169, 106, .28);
            flex-shrink: 0;
        }

        .sb-merek {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex: 1 1 auto;
            color: inherit;
            text-decoration: none;
        }

        .sb-logo {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            object-fit: contain;
            padding: 3px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 0 0 1px rgba(201, 169, 106, .7), 0 4px 10px rgba(20, 2, 5, .35);
        }

        .sb-judul {
            display: block;
            font-size: 15.5px;
            font-weight: 800;
            letter-spacing: .06em;
            line-height: 1.1;
            color: #fff;
        }

        .sb-sub {
            display: block;
            margin-top: 2px;
            font-size: 9.4px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--gold, #C9A96A);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sb-kerut {
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            padding: 0;
            border: 1px solid rgba(201, 169, 106, .38);
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
            color: #f3e7df;
            cursor: pointer;
            transition: background .16s ease, color .16s ease;
        }

        .sb-kerut:hover {
            background: rgba(201, 169, 106, .28);
            color: #fff;
        }

        .sb-kerut svg {
            width: 14px;
            height: 14px;
            transition: transform .2s ease;
        }

        body.sb-sempit .sb-kerut svg {
            transform: rotate(180deg);
        }

        /* ---------------- Kartu pengguna ---------------- */
        .sb-pengguna {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 12px 11px 4px;
            padding: 9px 10px;
            border: 1px solid rgba(201, 169, 106, .24);
            border-radius: 12px;
            background: rgba(255, 255, 255, .08);
            flex-shrink: 0;
        }

        .sb-avatar {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            border-radius: 50%;
            overflow: hidden;
            background: var(--gold, #C9A96A);
            color: #3b060d;
            font-size: 12.5px;
            font-weight: 800;
            letter-spacing: .03em;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, .35);
        }

        .sb-avatar img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sb-pengguna .sb-teks {
            min-width: 0;
        }

        .sb-peran {
            display: block;
            font-size: 9.4px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--gold, #C9A96A);
        }

        .sb-nama {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ---------------- Navigasi ---------------- */
        .sb-gulir {
            position: relative;
            z-index: 2;
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 6px 10px 10px;
            scrollbar-width: thin;
            scrollbar-color: rgba(201, 169, 106, .45) transparent;
        }

        .sb-gulir::-webkit-scrollbar {
            width: 6px;
        }

        .sb-gulir::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(201, 169, 106, .45);
        }

        .sb-label-seksi {
            display: block;
            padding: 9px 8px 6px;
            font-size: 9.3px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(253, 246, 234, .55);
        }

        .sb-nav {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .sb-tautan {
            position: relative;
            display: flex;
            align-items: center;
            gap: 11px;
            width: 100%;
            min-height: 42px;
            padding: 9px 11px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #f1e4dc;
            font-family: inherit;
            font-size: 13.4px;
            font-weight: 600;
            line-height: 1.25;
            text-align: left;
            cursor: pointer;
            transition: background .16s ease, color .16s ease;
        }

        .sb-tautan .ikon-menu {
            width: 19px;
            height: 19px;
            flex-shrink: 0;
            color: rgba(253, 246, 234, .72);
            transition: color .16s ease;
        }

        .sb-tautan:hover {
            background: rgba(255, 255, 255, .10);
            color: #fff;
        }

        .sb-tautan:hover .ikon-menu {
            color: var(--gold, #C9A96A);
        }

        .sb-tautan.active,
        .sb-tautan.is-active {
            background: linear-gradient(90deg, rgba(201, 169, 106, .28), rgba(201, 169, 106, .06));
            color: #fff;
            font-weight: 700;
        }

        .sb-tautan.active .ikon-menu,
        .sb-tautan.is-active .ikon-menu {
            color: var(--gold, #C9A96A);
        }

        .sb-tautan.active::before,
        .sb-tautan.is-active::before {
            content: "";
            position: absolute;
            left: -10px;
            top: 50%;
            width: 4px;
            height: 21px;
            border-radius: 0 4px 4px 0;
            background: var(--gold, #C9A96A);
            transform: translateY(-50%);
        }

        .sb-teks {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        /* Grup menu dengan submenu */
        .sb-grup {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sb-grup-toggle {
            border: 0;
        }

        .sb-panah {
            width: 15px;
            height: 15px;
            margin-left: auto;
            flex-shrink: 0;
            color: rgba(253, 246, 234, .6);
            transition: transform .2s ease, color .16s ease;
        }

        .sb-grup.is-open > .sb-grup-toggle .sb-panah {
            transform: rotate(180deg);
            color: var(--gold, #C9A96A);
        }

        .sb-sub {
            display: none;
            flex-direction: column;
            gap: 2px;
            margin: 2px 0 6px;
            padding-left: 12px;
            border-left: 1px solid rgba(201, 169, 106, .28);
        }

        .sb-grup.is-open .sb-sub {
            display: flex;
        }

        .sb-sub-tautan {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px;
            border-radius: 9px;
            color: rgba(253, 246, 234, .84);
            font-size: 12.6px;
            font-weight: 600;
            line-height: 1.25;
            transition: background .16s ease, color .16s ease;
        }

        .sb-sub-tautan .ikon-menu {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            color: rgba(253, 246, 234, .6);
        }

        .sb-sub-tautan:hover {
            background: rgba(255, 255, 255, .10);
            color: #fff;
        }

        .sb-sub-tautan:hover .ikon-menu,
        .sb-sub-tautan.active .ikon-menu {
            color: var(--gold, #C9A96A);
        }

        .sb-sub-tautan.active {
            background: rgba(201, 169, 106, .20);
            color: #fff;
            font-weight: 700;
        }

        /* ---------------- Bagian bawah ---------------- */
        .sb-bawah {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 8px 10px 12px;
            border-top: 1px solid rgba(201, 169, 106, .28);
            background: linear-gradient(180deg, rgba(46, 4, 10, 0), rgba(40, 4, 9, .35));
            flex-shrink: 0;
        }

        .sb-tautan--keluar {
            color: #ffd9d2;
        }

        .sb-tautan--keluar .ikon-menu {
            color: rgba(255, 217, 210, .8);
        }

        .sb-tautan--keluar:hover {
            background: rgba(255, 255, 255, .12);
            color: #fff;
        }

        .sb-tautan--keluar:hover .ikon-menu {
            color: #fff;
        }

        /* ---------------- Status sempit (collapse) ---------------- */
        body.sb-sempit .sb-kepala {
            justify-content: center;
            padding-left: 8px;
            padding-right: 8px;
        }

        body.sb-sempit .sb-merek {
            flex: 0 0 auto;
        }

        body.sb-sempit .sb-kepala .sb-teks,
        body.sb-sempit .sb-pengguna .sb-teks,
        body.sb-sempit .sb-tautan .sb-teks,
        body.sb-sempit .sb-tautan .sb-panah,
        body.sb-sempit .sb-label-seksi {
            display: none;
        }

        body.sb-sempit .sb-pengguna {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
            margin-left: 8px;
            margin-right: 8px;
        }

        body.sb-sempit .sb-tautan {
            justify-content: center;
            gap: 0;
            padding-left: 0;
            padding-right: 0;
        }

        body.sb-sempit .sb-tautan.active::before {
            left: 0;
        }

        body.sb-sempit .sb-sub {
            display: none !important;
        }

        body.sb-sempit .sb-bawah .sb-tautan {
            justify-content: center;
        }

        /* ---------------- Laci (mobile) ---------------- */
        .sb-tirai {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 890;
            background: rgba(30, 6, 10, .52);
            opacity: 0;
            transition: opacity .2s ease;
        }

        body.sb-laci .sb-tirai {
            display: block;
            opacity: 1;
        }

        body.sb-laci {
            overflow: hidden;
        }

        .sb-tombol {
            display: none;
            position: fixed;
            top: 12px;
            left: 12px;
            z-index: 880;
            width: 42px;
            height: 42px;
            padding: 0;
            place-items: center;
            border: 1px solid rgba(201, 169, 106, .55);
            border-radius: 11px;
            background: var(--maroon, #6B0D1B);
            color: #fff;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(43, 4, 9, .25);
        }

        .sb-tombol svg {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 991px) {
            .sb {
                top: 0;
                width: min(280px, 82vw);
                transform: translateX(-104%);
                z-index: 940;
                box-shadow: 0 22px 50px rgba(30, 6, 10, .38);
            }

            .sb--panel {
                top: var(--header-height, 0px);
                z-index: 899;
            }

            .sb--alir {
                position: fixed;
                height: auto;
                max-height: none;
                align-self: auto;
                flex: 0 0 auto;
            }

            .sb.aktif,
            .sb.active,
            .sb.open {
                transform: translateX(0);
            }

            .sb-tombol {
                display: grid;
            }

            .sb-kerut {
                display: none;
            }

            body.sb-mandiri,
            body.sb-sempit.sb-mandiri {
                padding-left: 0;
            }

            body.sb-sempit .sb {
                width: min(280px, 82vw);
            }

            body.sb-sempit .sb-kepala {
                justify-content: flex-start;
                padding-left: 13px;
                padding-right: 13px;
            }

            body.sb-sempit .sb-kepala .sb-teks,
            body.sb-sempit .sb-pengguna .sb-teks,
            body.sb-sempit .sb-tautan .sb-teks,
            body.sb-sempit .sb-tautan .sb-panah,
            body.sb-sempit .sb-label-seksi {
                display: block;
            }

            body.sb-sempit .sb-pengguna {
                justify-content: flex-start;
                padding: 9px 10px;
                margin-left: 11px;
                margin-right: 11px;
            }

            body.sb-sempit .sb-tautan {
                justify-content: flex-start;
                gap: 11px;
                padding: 9px 11px;
            }

            body.sb-sempit .sb-grup.is-open .sb-sub {
                display: flex !important;
            }
        }

        @media print {
            .sb,
            .sb-tirai,
            .sb-tombol {
                display: none !important;
            }

            body.sb-mandiri {
                padding-left: 0 !important;
            }
        }
    </style>
@endonce

<aside class="sb sb--{{ $ragam }}"
    id="sidebar"
    style="--sb-atas: {{ $atas }};"
    aria-label="Menu utama">

    <div class="sb-kepala">
        <a href="{{ $berandaSidebar }}" class="sb-merek" aria-label="{{ $namaSistem ?? 'SIMAHAD' }} - Beranda">
            <img src="{{ asset('images/logo_uin.png') }}"
                alt="Logo Ma'had Al-Jami'ah"
                class="sb-logo"
                onerror="this.style.display='none'">

            <span class="sb-teks">
                <strong class="sb-judul">{{ $namaSistem ?? 'SIMAHAD' }}</strong>
                <small class="sb-sub">Ma'had Al-Jami'ah UIN Imam Bonjol</small>
            </span>
        </a>

        <button type="button"
            class="sb-kerut"
            data-sb-kerut
            aria-label="Perkecil atau lebarkan menu"
            aria-expanded="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m15 6-6 6 6 6" />
            </svg>
        </button>
    </div>

    <div class="sb-pengguna">
        <span class="sb-avatar">
            @if (filled($fotoSidebar))
                <img src="{{ $fotoSidebar }}" alt="Foto {{ $penggunaSidebar?->name }}">
            @else
                {{ $inisialSidebar }}
            @endif
        </span>

        <span class="sb-teks">
            <span class="sb-peran">{{ $labelPeranSidebar }}</span>
            <span class="sb-nama">{{ $penggunaSidebar?->name ?? 'Pengguna' }}</span>
        </span>
    </div>

    <div class="sb-gulir">
        <span class="sb-label-seksi">Menu {{ $labelPeranSidebar }}</span>

        <nav class="sb-nav">
            @foreach ($menuUtamaSidebar as $menu)
                @if (! empty($menu['anak']))
                    @php
                        $grupAktif = request()->routeIs(...$menu['aktif']);
                    @endphp

                    <div class="sb-grup {{ $grupAktif ? 'is-active is-open' : '' }}" data-menu-grup>
                        <button type="button"
                            class="sb-tautan sb-grup-toggle menu-grup-toggle"
                            data-menu-toggle
                            aria-expanded="{{ $grupAktif ? 'true' : 'false' }}">
                            <x-panel.ikon :nama="$menu['ikon']" />
                            <span class="sb-teks">{{ $menu['label'] }}</span>
                            <svg class="sb-panah menu-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div class="sb-sub">
                            @foreach ($menu['anak'] as $anak)
                                <a href="{{ route($anak['route']) }}"
                                    class="sb-sub-tautan {{ request()->routeIs(...$anak['aktif']) ? 'active' : '' }}"
                                    @if (request()->routeIs(...$anak['aktif'])) aria-current="page" @endif>
                                    <x-panel.ikon :nama="$anak['ikon']" />
                                    <span class="sb-teks">{{ $anak['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route($menu['route']) }}"
                        class="sb-tautan {{ request()->routeIs(...$menu['aktif']) ? 'active' : '' }}"
                        @if (request()->routeIs(...$menu['aktif'])) aria-current="page" @endif>
                        <x-panel.ikon :nama="$menu['ikon']" />
                        <span class="sb-teks">{{ $menu['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </div>

    <div class="sb-bawah">
        @foreach ($menuBawahSidebar as $menu)
            <a href="{{ route($menu['route']) }}"
                class="sb-tautan {{ request()->routeIs(...$menu['aktif']) ? 'active' : '' }}"
                @if (request()->routeIs(...$menu['aktif'])) aria-current="page" @endif>
                <x-panel.ikon :nama="$menu['ikon']" />
                <span class="sb-teks">{{ $menu['label'] }}</span>
            </a>
        @endforeach

        <a href="{{ route('logout.confirm') }}" class="sb-tautan sb-tautan--keluar">
            <x-panel.ikon nama="keluar" />
            <span class="sb-teks">Keluar</span>
        </a>
    </div>
</aside>

<div class="sb-tirai" data-sb-tirai aria-hidden="true"></div>

@if ($tombolBuka)
    <button type="button" class="sb-tombol" data-sb-buka aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
            <path d="M3 6h18" />
            <path d="M3 12h18" />
            <path d="M3 18h18" />
        </svg>
    </button>
@endif

@once
    <script>
        (function () {
            const badan = document.body;
            const sidebar = document.getElementById('sidebar');

            if (!sidebar || !sidebar.classList.contains('sb')) {
                return;
            }

            const tirai = document.querySelector('[data-sb-tirai]');

            // Status sempit (collapse) disimpan agar tetap sama antarhalaman.
            try {
                if (window.localStorage.getItem('simahad-sidebar-sempit') === '1') {
                    badan.classList.add('sb-sempit');
                }
            } catch (error) {
                // Penyimpanan lokal tidak tersedia: abaikan.
            }

            const tombolKerut = sidebar.querySelector('[data-sb-kerut]');

            if (tombolKerut) {
                tombolKerut.addEventListener('click', function () {
                    const sempit = badan.classList.toggle('sb-sempit');
                    tombolKerut.setAttribute('aria-expanded', sempit ? 'false' : 'true');

                    try {
                        window.localStorage.setItem('simahad-sidebar-sempit', sempit ? '1' : '0');
                    } catch (error) {
                        // Abaikan bila penyimpanan lokal diblokir.
                    }
                });
            }

            // Tombol pembuka laci: tombol bawaan halaman atau tombol komponen.
            // Halaman yang sudah mengelola lacinya sendiri (#mobileMenuBtn) tidak
            // didaftarkan ganda agar tirai tidak bertumpuk.
            const pembuka = document.querySelectorAll('#menuToggle, [data-sb-buka]');

            function bukaLaci() {
                badan.classList.add('sb-laci');
                sidebar.classList.add('aktif');
                pembuka.forEach(function (tombol) {
                    tombol.setAttribute('aria-expanded', 'true');
                });
            }

            function tutupLaci() {
                badan.classList.remove('sb-laci');
                sidebar.classList.remove('aktif', 'active', 'open');
                pembuka.forEach(function (tombol) {
                    tombol.setAttribute('aria-expanded', 'false');
                });
            }

            pembuka.forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    if (badan.classList.contains('sb-laci')) {
                        tutupLaci();
                    } else {
                        bukaLaci();
                    }
                });
            });

            if (tirai) {
                tirai.addEventListener('click', tutupLaci);
            }

            // Tombol tutup khusus halaman tertentu tetap berfungsi.
            document.querySelectorAll('#sidebarClose').forEach(function (tombol) {
                tombol.addEventListener('click', tutupLaci);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && badan.classList.contains('sb-laci')) {
                    tutupLaci();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 991 && badan.classList.contains('sb-laci')) {
                    tutupLaci();
                }
            });
        })();
    </script>
@endonce
