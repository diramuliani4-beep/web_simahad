@props([
    'ragam' => 'panel',
    'atas' => '0px',
    'tombolBuka' => false,
])

@php
    // Tetap memakai sumber menu, route, dan profil yang sudah ada di SIMAHAD.
    $penggunaSidebar = auth()->user();
    $menuUtamaSidebar = \App\Support\MenuPeran::utama($penggunaSidebar);
    $menuBawahSidebar = \App\Support\MenuPeran::bawah($penggunaSidebar);
    $labelPeranSidebar = \App\Support\MenuPeran::labelPeran($penggunaSidebar);
    $gambarSidebar = asset('images/mahad.jpeg');
@endphp

@once
<style>
    :root {
        --sb-w: 276px;
        --sb-w-sempit: 78px;
        --sb-emas: #e8bd7d;
    }

    .sb {
        position: fixed;
        inset: var(--sb-atas, 0px) auto 0 0;
        width: var(--sb-w);
        z-index: 900;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        color: #ffffff;
        background-color: #4b1020;
        background-image: var(--sb-bg, none);
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        border-right: 1px solid rgba(255, 255, 255, 0.16);
        box-shadow: 5px 0 24px rgba(31, 4, 13, 0.18);
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        transition: width 0.22s ease, transform 0.26s ease;
    }

    /* Foto gedung tetap terlihat dengan lapisan maroon untuk kontras teks. */
    .sb::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        background: linear-gradient(
            180deg,
            rgba(80, 12, 31, 0.43) 0%,
            rgba(70, 11, 29, 0.52) 55%,
            rgba(43, 5, 17, 0.63) 100%
        );
    }

    .sb > * {
        position: relative;
        z-index: 1;
    }

    .sb--panel { top: var(--header-height, 0px); }
    .sb--tetap { top: var(--sb-atas, 0px); }
    .sb--alir {
        position: sticky;
        top: var(--sb-atas, 0px);
        bottom: auto;
        flex: 0 0 var(--sb-w);
        align-self: flex-start;
        height: calc(100vh - var(--sb-atas, 0px));
        max-height: calc(100vh - var(--sb-atas, 0px));
    }
    body.sb-mandiri { padding-left: var(--sb-w); }
    body.sb-sempit .sb { width: var(--sb-w-sempit); }
    body.sb-sempit .sb--alir { flex-basis: var(--sb-w-sempit); }
    body.sb-sempit.sb-mandiri { padding-left: var(--sb-w-sempit); }

    /* Header aplikasi berada di layout utama; sidebar hanya navigasi. */
    .sb-topbar {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        min-height: 62px;
        padding: 12px 15px 10px 19px;
        border-bottom: 1px solid rgba(255, 255, 255, .18);
        flex-shrink: 0;
    }
    .sb-topbar-title {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .18em;
        color: #ffe2b4;
    }
    .sb-kerut {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        padding: 0;
        border: 1px solid rgba(255, 255, 255, .33);
        border-radius: 11px;
        background: rgba(35, 4, 17, .26);
        color: #fff;
        cursor: pointer;
        transition: background .18s ease, transform .18s ease;
    }
    .sb-kerut:hover { background: rgba(255, 255, 255, .18); }
    .sb-kerut svg { width: 16px; height: 16px; transition: transform .2s ease; }
    body.sb-sempit .sb-kerut svg { transform: rotate(180deg); }

    .sb-gulir {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 8px 10px 12px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.35) transparent;
    }
    .sb-gulir::-webkit-scrollbar { width: 5px; }
    .sb-gulir::-webkit-scrollbar-thumb {
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.35);
    }
    .sb-label-seksi {
        display: block;
        padding: 10px 9px 8px;
        color: #f2d8c8;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.11em;
        text-transform: uppercase;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    .sb-nav { display: flex; flex-direction: column; gap: 4px; }
    .sb-tautan {
        position: relative;
        display: flex;
        align-items: center;
        gap: 11px;
        width: 100%;
        min-height: 43px;
        padding: 10px 12px;
        border: 0;
        border-radius: 10px;
        background: rgba(28, 3, 12, 0.13);
        color: #ffffff;
        text-decoration: none;
        font: 600 13px/1.3 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
        text-align: left;
        text-shadow: 0 1px 3px rgba(13, 1, 5, 0.33);
        cursor: pointer;
        transition: background 0.16s ease, color 0.16s ease;
    }
    .sb-tautan .ikon-menu {
        width: 19px;
        height: 19px;
        flex-shrink: 0;
        color: #ffe2be;
    }
    .sb-tautan:hover { background: rgba(31, 2, 12, 0.4); }
    .sb-tautan.active,
    .sb-tautan.is-active,
    .sb-grup.is-active > .sb-grup-toggle {
        background: rgba(35, 3, 15, 0.51);
        box-shadow: inset 0 0 0 1px rgba(255, 228, 177, 0.22);
        color: #ffffff;
    }
    .sb-tautan.active::before,
    .sb-tautan.is-active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 9px;
        bottom: 9px;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: var(--sb-emas);
    }
    .sb-tautan.active .ikon-menu,
    .sb-tautan.is-active .ikon-menu { color: var(--sb-emas); }
    .sb-teks { min-width: 0; overflow-wrap: anywhere; }

    /* Submenu. Nama .sb-submenu dibedakan dari .sb-subjudul. */
    .sb-grup { display: flex; flex-direction: column; gap: 2px; }
    .sb-panah {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
        margin-left: auto;
        color: #fff2dc;
        transition: transform 0.2s ease;
    }
    .sb-grup.is-open > .sb-grup-toggle .sb-panah { transform: rotate(180deg); }
    .sb-submenu {
        display: flex;
        flex-direction: column;
        gap: 3px;
        margin: 2px 0 7px 22px;
        padding: 3px 0 3px 10px;
        border-left: 1px solid rgba(255, 222, 170, 0.4);
    }
    .sb-submenu[hidden] { display: none !important; }
    .sb-sub-tautan {
        display: flex;
        align-items: center;
        gap: 9px;
        min-height: 35px;
        padding: 8px 10px;
        border-radius: 8px;
        color: #fff2eb;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    .sb-sub-tautan .ikon-menu { width: 15px; height: 15px; flex-shrink: 0; }
    .sb-sub-tautan:hover { background: rgba(36, 2, 12, 0.38); }
    .sb-sub-tautan.active { background: rgba(35, 3, 15, 0.5); color: #ffe1aa; }

    /* Menu paling bawah. */
    .sb-bawah {
        display: flex;
        flex-direction: column;
        gap: 3px;
        flex-shrink: 0;
        padding: 9px 10px 13px;
        border-top: 1px solid rgba(255, 255, 255, 0.22);
        background: rgba(28, 3, 11, 0.2);
    }
    .sb-tautan--keluar { color: #ffe2dc; }
    .sb-tautan--keluar:hover { background: rgba(92, 8, 23, 0.45); }
    .sb-tautan:focus-visible,
    .sb-sub-tautan:focus-visible,
    .sb-kerut:focus-visible,
    .sb-tombol:focus-visible {
        outline: 2px solid var(--sb-emas);
        outline-offset: 2px;
    }

    /* Collapse: hanya ikon; tombol tetap terlihat. */
    body.sb-sempit .sb-topbar { justify-content: center; padding: 12px 7px; }
    body.sb-sempit .sb-topbar-title,
    body.sb-sempit .sb-tautan .sb-teks,
    body.sb-sempit .sb-tautan .sb-panah,
    body.sb-sempit .sb-label-seksi { display: none; }
    body.sb-sempit .sb-gulir { padding-left: 9px; padding-right: 9px; }
    body.sb-sempit .sb--panel .sb-gulir { padding-top: 16px; }
    body.sb-sempit .sb--panel .sb-label-seksi { display: none; }
    body.sb-sempit .sb-tautan { justify-content: center; padding: 10px 0; }
    body.sb-sempit .sb-submenu { display: none !important; }

    /* Penutup laci di HP. */
    .sb-tirai {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 950;
        background: rgba(21, 3, 9, 0.55);
    }
    body.sb-laci .sb-tirai { display: block; }
    body.sb-laci { overflow: hidden; }
    .sb-tombol {
        display: none;
        position: fixed;
        top: 12px;
        left: 12px;
        z-index: 940;
        width: 42px;
        height: 42px;
        padding: 0;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 10px;
        background: #800b22;
        color: #ffffff;
        cursor: pointer;
    }
    .sb-tombol svg { width: 20px; height: 20px; }
    body:has(#menuToggle) .sb-tombol { display: none !important; }

    @media (max-width: 991px) {
        .sb--panel ~ .sb-tirai { top: var(--header-height, 62px); }
        .sb,
        .sb--panel,
        .sb--tetap,
        .sb--alir {
            position: fixed;
            top: var(--header-height, 62px);
            bottom: 0;
            left: 0;
            width: min(280px, 86vw);
            height: auto;
            max-height: none;
            transform: translateX(-105%);
            z-index: 960;
        }
        .sb:not(.sb--panel) { top: 0; }
        .sb.aktif,
        .sb.active,
        .sb.open { transform: translateX(0); }
        body.sb-sempit .sb { width: min(280px, 86vw); }
        .sb-tombol { display: grid; }
        body.sb-mandiri,
        body.sb-sempit.sb-mandiri { padding-left: 0; }
        .sb-topbar { min-height: 58px; padding: 11px 16px; }
        .sb-kerut { display: grid; margin-left: auto; }
        .sb-kerut svg, body.sb-sempit .sb-kerut svg { transform: rotate(180deg); }
        body.sb-sempit .sb-topbar { justify-content: space-between; padding: 11px 16px; }
        body.sb-sempit .sb-topbar-title,
        body.sb-sempit .sb-tautan .sb-teks,
        body.sb-sempit .sb-tautan .sb-panah,
        body.sb-sempit .sb-label-seksi { display: block; }
        body.sb-sempit .sb-gulir { padding: 8px 10px 12px; }
        .sb--panel .sb-gulir,
        body.sb-sempit .sb--panel .sb-gulir { padding-top: 18px; }
        body.sb-sempit .sb--panel .sb-label-seksi { display: flex; }
        body.sb-sempit .sb-tautan { justify-content: flex-start; gap: 11px; padding: 10px 12px; }
        body.sb-sempit .sb-submenu { display: flex !important; }
        body.sb-sempit .sb-submenu[hidden] { display: none !important; }
    }
    @media print {
        .sb,
        .sb-tirai,
        .sb-tombol { display: none !important; }
        body.sb-mandiri { padding-left: 0 !important; }
    }
    @media (prefers-reduced-motion: reduce) {
        .sb,
        .sb-kerut svg,
        .sb-panah { transition: none !important; }
    }

    /* SIMAHAD NAV 2026: identitas global hanya di header. */
    .sb {
        --sb-emas: #edcf9b;
        background-position: center bottom;
        border-right: 1px solid rgba(233, 214, 203, .22);
    }
    .sb::before {
        background: linear-gradient(180deg,
            rgba(48, 16, 37, .92) 0%,
            rgba(61, 20, 44, .82) 38%,
            rgba(51, 18, 38, .54) 74%,
            rgba(34, 10, 25, .70) 100%);
    }
    .sb--panel .sb-topbar { display: none; }
    .sb--panel .sb-gulir { padding: 24px 13px 16px; }
    .sb-section-heading {
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding: 0 10px 16px;
        margin-bottom: 5px;
    }
    .sb-section-eyebrow {
        color: rgba(255, 228, 187, .65);
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .23em;
    }
    .sb-section-name {
        color: #fff9f6;
        font-size: 15px;
        font-weight: 750;
        line-height: 1.35;
        letter-spacing: -.025em;
    }
    .sb-nav { gap: 5px; }
    .sb .sb-tautan {
        min-height: 46px;
        padding: 11px 13px;
        border: 1px solid transparent;
        border-radius: 12px;
        background: transparent;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: -.01em;
        text-shadow: none;
        color: rgba(255, 249, 248, .88);
        transition: background .18s ease, border-color .18s ease, transform .18s ease, color .18s ease;
    }
    .sb .sb-tautan .ikon-menu { color: rgba(255, 234, 215, .78); width: 19px; height: 19px; }
    .sb .sb-tautan:hover {
        background: rgba(255, 255, 255, .12);
        border-color: rgba(255, 255, 255, .09);
        color: white;
        transform: translateX(2px);
    }
    .sb .sb-tautan.active,
    .sb .sb-tautan.is-active,
    .sb .sb-grup.is-active > .sb-grup-toggle {
        background: #fff8f4;
        border-color: rgba(255, 255, 255, .9);
        color: #74132c;
        font-weight: 800;
        box-shadow: 0 7px 18px rgba(20, 5, 16, .18);
    }
    .sb .sb-tautan.active .ikon-menu,
    .sb .sb-tautan.is-active .ikon-menu,
    .sb .sb-grup.is-active > .sb-grup-toggle .ikon-menu { color: #861b37; }
    .sb .sb-grup.is-active > .sb-grup-toggle .sb-panah { color: #861b37; }
    .sb .sb-tautan.active::before,
    .sb .sb-tautan.is-active::before { display: none; }
    .sb .sb-submenu { margin-left: 22px; border-left-color: rgba(255, 225, 186, .28); }
    .sb .sb-sub-tautan { border-radius: 9px; color: rgba(255, 255, 255, .88); text-shadow: none; }
    .sb .sb-sub-tautan.active { background: rgba(255, 255, 255, .16); color: #fff; }
    .sb .sb-bawah {
        padding: 12px 13px 16px;
        border-top: 1px solid rgba(255, 255, 255, .12);
        background: linear-gradient(180deg, rgba(34, 10, 25, .15), rgba(34, 10, 25, .62));
    }
    .sb .sb-tautan--keluar { color: #ffe3e3; }
    body.sb-sempit .sb-section-heading { display: none; }
    body.sb-sempit .sb--panel .sb-gulir { padding: 16px 9px; }
    body.sb-sempit .sb .sb-tautan { justify-content: center; padding: 11px 0; }
    body.sb-sempit .sb .sb-tautan .sb-teks,
    body.sb-sempit .sb .sb-tautan .sb-panah { display: none; }
    @media (max-width: 991px) {
        .sb--panel .sb-gulir,
        body.sb-sempit .sb--panel .sb-gulir { padding: 20px 13px 16px; }
        body.sb-sempit .sb-section-heading { display: flex; }
        body.sb-sempit .sb .sb-tautan { justify-content: flex-start; padding: 11px 13px; }
        body.sb-sempit .sb .sb-tautan .sb-teks,
        body.sb-sempit .sb .sb-tautan .sb-panah { display: block; }
    }
    @media (prefers-reduced-motion: reduce) {
        .sb .sb-tautan { transition: none; }
        .sb .sb-tautan:hover { transform: none; }
    }
</style>
@endonce

<aside
    id="sidebar"
    class="sb sb--{{ $ragam }}"
    style="--sb-atas: {{ $atas }}; --sb-bg: url('{{ $gambarSidebar }}');"
    aria-label="Menu utama"
>
    {{-- Identitas aplikasi dan akun sudah ditampilkan di header global. --}}
    <div class="sb-topbar">
        <span class="sb-topbar-title sb-teks">NAVIGASI</span>
        @if ($ragam !== 'panel')
        <button
            type="button"
            class="sb-kerut"
            data-sb-kerut
            aria-label="Perkecil sidebar"
            aria-expanded="true"
            aria-controls="sidebar"
            title="Perkecil sidebar"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m15 6-6 6 6 6" />
            </svg>
        </button>
        @endif
    </div>

    <div class="sb-gulir">
        <div class="sb-section-heading" aria-label="Navigasi {{ $labelPeranSidebar }}">
            <span class="sb-section-name">Menu {{ $labelPeranSidebar }}</span>
        </div>
        <nav class="sb-nav" aria-label="Navigasi {{ $labelPeranSidebar }}">
            @foreach ($menuUtamaSidebar as $menu)
                @if (! empty($menu['anak']))
                    @php
                        $grupAktif = request()->routeIs(...$menu['aktif']);
                    @endphp
                    <div class="sb-grup {{ $grupAktif ? 'is-active is-open' : '' }}" data-menu-grup>
                        <button
                            type="button"
                            class="sb-tautan sb-grup-toggle {{ $grupAktif ? 'is-active' : '' }}"
                            data-menu-toggle
                            aria-expanded="{{ $grupAktif ? 'true' : 'false' }}"
                            aria-label="Buka atau tutup {{ $menu['label'] }}"
                        >
                            <x-panel.ikon :nama="$menu['ikon']" />
                            <span class="sb-teks">{{ $menu['label'] }}</span>
                            <svg class="sb-panah" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div class="sb-submenu" @if (! $grupAktif) hidden @endif>
                            @foreach ($menu['anak'] as $anak)
                                <a
                                    href="{{ route($anak['route']) }}"
                                    class="sb-sub-tautan {{ request()->routeIs(...$anak['aktif']) ? 'active' : '' }}"
                                    @if (request()->routeIs(...$anak['aktif'])) aria-current="page" @endif
                                >
                                    <x-panel.ikon :nama="$anak['ikon']" />
                                    <span class="sb-teks">{{ $anak['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a
                        href="{{ route($menu['route']) }}"
                        class="sb-tautan {{ request()->routeIs(...$menu['aktif']) ? 'active' : '' }}"
                        @if (request()->routeIs(...$menu['aktif'])) aria-current="page" @endif
                    >
                        <x-panel.ikon :nama="$menu['ikon']" />
                        <span class="sb-teks">{{ $menu['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </div>

    <div class="sb-bawah">
        @foreach ($menuBawahSidebar as $menu)
            <a
                href="{{ route($menu['route']) }}"
                class="sb-tautan {{ request()->routeIs(...$menu['aktif']) ? 'active' : '' }}"
                @if (request()->routeIs(...$menu['aktif'])) aria-current="page" @endif
            >
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

@if ($tombolBuka && $ragam !== 'panel')
    <button
        type="button"
        class="sb-tombol"
        data-sb-buka
        aria-label="Buka menu"
        aria-controls="sidebar"
        aria-expanded="false"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
            <path d="M3 6h18M3 12h18M3 18h18" />
        </svg>
    </button>
@endif

@once
<script>
(function () {
    'use strict';
    const sidebar = document.getElementById('sidebar');
    if (!sidebar || !sidebar.classList.contains('sb')) return;

    const badan = document.body;
    const tirai = document.querySelector('[data-sb-tirai]');
    const tombolKerut = sidebar.querySelector('[data-sb-kerut]');
    const pembuka = document.querySelectorAll('#menuToggle, [data-sb-buka]');

    try {
        if (localStorage.getItem('simahad-sidebar-sempit') === '1') {
            badan.classList.add('sb-sempit');
        }
    } catch (error) {
        // Browser memblokir penyimpanan lokal.
    }

    function sinkronKerut() {
        if (tombolKerut) {
            const sempit = badan.classList.contains('sb-sempit');
            tombolKerut.setAttribute('aria-expanded', sempit ? 'false' : 'true');
            tombolKerut.setAttribute('aria-label', sempit ? 'Perlebar sidebar' : 'Perkecil sidebar');
            tombolKerut.title = sempit ? 'Perlebar sidebar' : 'Perkecil sidebar';
        }
    }
    function aturKerut(sempit) {
        badan.classList.toggle('sb-sempit', sempit);
        sinkronKerut();
        sinkronTombolHeader();
        try {
            localStorage.setItem('simahad-sidebar-sempit', sempit ? '1' : '0');
        } catch (error) {
            // Tidak masalah bila localStorage tidak tersedia.
        }
    }
    sinkronKerut();
    if (tombolKerut) {
        tombolKerut.addEventListener('click', function () {
            if (window.innerWidth <= 991) {
                tutupLaci();
            } else {
                aturKerut(!badan.classList.contains('sb-sempit'));
            }
        });
    }

    // Bukaan submenu aktif dipertahankan saat berpindah halaman.
    sidebar.querySelectorAll('[data-menu-grup]').forEach(function (grup) {
        const tombol = grup.querySelector('[data-menu-toggle]');
        const submenu = grup.querySelector('.sb-submenu');
        if (!tombol || !submenu) return;

        function setBuka(terbuka) {
            grup.classList.toggle('is-open', terbuka);
            submenu.hidden = !terbuka;
            tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
        }
        setBuka(grup.classList.contains('is-open'));
        tombol.addEventListener('click', function () {
            const akanBuka = !grup.classList.contains('is-open');
            if (akanBuka && window.innerWidth > 991 && badan.classList.contains('sb-sempit')) {
                aturKerut(false);
            }
            setBuka(akanBuka);
        });
    });

    function bukaLaci() {
        badan.classList.add('sb-laci');
        sidebar.classList.add('aktif');
        sinkronTombolHeader();
    }
    function tutupLaci() {
        badan.classList.remove('sb-laci');
        sidebar.classList.remove('aktif', 'active', 'open');
        sinkronTombolHeader();
    }
    function sinkronTombolHeader() {
        pembuka.forEach(function (tombol) {
            if (window.innerWidth > 991) {
                const sempit = badan.classList.contains('sb-sempit');
                tombol.setAttribute('aria-expanded', sempit ? 'false' : 'true');
                tombol.setAttribute('aria-label', sempit ? 'Perlebar sidebar' : 'Perkecil sidebar');
                tombol.title = sempit ? 'Perlebar sidebar' : 'Perkecil sidebar';
            } else {
                const terbuka = badan.classList.contains('sb-laci');
                tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
                tombol.setAttribute('aria-label', terbuka ? 'Tutup menu navigasi' : 'Buka menu navigasi');
                tombol.title = terbuka ? 'Tutup menu navigasi' : 'Buka menu navigasi';
            }
        });
    }
    pembuka.forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            if (window.innerWidth > 991) {
                aturKerut(!badan.classList.contains('sb-sempit'));
            } else {
                badan.classList.contains('sb-laci') ? tutupLaci() : bukaLaci();
            }
            sinkronTombolHeader();
        });
    });
    sinkronTombolHeader();
    if (tirai) tirai.addEventListener('click', tutupLaci);
    document.querySelectorAll('#sidebarClose').forEach(function (tombol) {
        tombol.addEventListener('click', tutupLaci);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && badan.classList.contains('sb-laci')) tutupLaci();
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 991 && badan.classList.contains('sb-laci')) tutupLaci();
        sinkronTombolHeader();
    });
})();
</script>
@endonce
