@extends('layouts.mahasiswa')

@section('title', 'Pengumuman Mahasiswa | SIMAHAD')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Pengumuman Mahasiswa — gaya yang konsisten dengan halaman Musyrifah. */
    .pengumuman-page,
    .pengumuman-modal {
        --ann-maroon: #800b22;
        --ann-maroon-dark: #65071a;
        --ann-ink: #251f28;
        --ann-muted: #80747c;
        --ann-line: #eadde1;
        --ann-soft: #fff6f8;
        font-family: 'Plus Jakarta Sans', Arial, sans-serif;
        color: var(--ann-ink);
    }
    .pengumuman-page,
    .pengumuman-page *,
    .pengumuman-page *::before,
    .pengumuman-page *::after,
    .pengumuman-modal,
    .pengumuman-modal *,
    .pengumuman-modal *::before,
    .pengumuman-modal *::after { box-sizing: border-box; }
    .pengumuman-page {
        display: block;
        clear: both;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        padding: 0 0 28px;
        margin: 0;
        container-type: inline-size;
    }
    .pengumuman-page a { text-decoration: none; }
    .pengumuman-page button,
    .pengumuman-page input { font-family: inherit; }
    .pengumuman-page svg { flex-shrink: 0; }

    /* Navigasi dan kepala halaman. */
    .pengumuman-page .ann-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
        margin-bottom: 10px;
        font-size: 11.5px;
        line-height: 1.5;
        color: #97868f;
    }
    .pengumuman-page .ann-breadcrumb a { color: var(--ann-maroon); font-weight: 700; }
    .pengumuman-page .ann-breadcrumb a:hover { text-decoration: underline; }
    .pengumuman-page .ann-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 12px 18px;
        padding: 0 0 17px;
        margin-bottom: 17px;
        border-bottom: 1px solid #e7cfd5;
    }
    .pengumuman-page .ann-heading-text { flex: 1 1 340px; min-width: 0; }
    .pengumuman-page .ann-header h1 {
        margin: 0;
        color: #211b23;
        font-size: clamp(22px, 2vw, 28px);
        font-weight: 800;
        letter-spacing: -.035em;
        line-height: 1.25;
    }
    .pengumuman-page .ann-subtitle {
        margin: 5px 0 0;
        font-size: 12.5px;
        color: var(--ann-muted);
        line-height: 1.65;
    }

    /* Area filter + daftar, satu panel utuh. */
    .pengumuman-page .ann-panel {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--ann-line);
        border-radius: 15px;
        box-shadow: 0 3px 16px rgba(48, 16, 30, .03);
    }
    .pengumuman-page .ann-panel-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 6px 16px;
        padding: 16px 19px 13px;
        border-bottom: 1px solid #f0e6e9;
    }
    .pengumuman-page .ann-panel-head h2 {
        margin: 0;
        color: #2b2028;
        font-size: 15px;
        font-weight: 800;
    }
    .pengumuman-page .ann-total {
        font-size: 11.5px;
        color: var(--ann-muted);
        line-height: 1.5;
    }
    .pengumuman-page .ann-controls {
        display: grid;
        gap: 12px;
        padding: 15px 19px;
        border-bottom: 1px solid #f0e6e9;
        background: #fff;
        min-width: 0;
    }
    .pengumuman-page .ann-search {
        display: flex;
        align-items: stretch;
        flex-wrap: wrap;
        gap: 8px;
        width: 100%;
        margin: 0;
    }
    .pengumuman-page .ann-search-field {
        position: relative;
        display: flex;
        flex: 1 1 240px;
        min-width: 0;
        align-items: center;
    }
    .pengumuman-page .ann-search-field svg {
        position: absolute;
        left: 12px;
        width: 17px;
        height: 17px;
        pointer-events: none;
        color: #998890;
    }
    .pengumuman-page .ann-search input {
        width: 100%;
        min-width: 0;
        min-height: 40px;
        padding: 9px 12px 9px 39px;
        border: 1px solid #e2d1d7;
        border-radius: 9px;
        background: #fff;
        color: #30262d;
        font-size: 12px;
        outline: 0;
    }
    .pengumuman-page .ann-search input::placeholder { color: #9a8e96; }
    .pengumuman-page .ann-search input:focus {
        border-color: #bd8f9c;
        box-shadow: 0 0 0 3px rgba(128, 11, 34, .09);
    }
    .pengumuman-page .ann-search-submit,
    .pengumuman-page .ann-reset {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 9px 17px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
    }
    .pengumuman-page .ann-search-submit {
        border: 1px solid var(--ann-maroon);
        background: var(--ann-maroon);
        color: #fff;
    }
    .pengumuman-page .ann-search-submit:hover { background: var(--ann-maroon-dark); }
    .pengumuman-page .ann-reset {
        border: 1px solid #e5cfd5;
        background: #fff;
        color: var(--ann-maroon);
    }
    .pengumuman-page .ann-reset:hover { background: var(--ann-soft); }
    .pengumuman-page .ann-category-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        min-width: 0;
    }
    .pengumuman-page .ann-category-label {
        margin-right: 5px;
        color: #80757d;
        font-size: 11.5px;
        font-weight: 650;
    }
    .pengumuman-page .ann-category {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 33px;
        padding: 6px 12px;
        border: 1px solid #ebdfe2;
        border-radius: 8px;
        background: #fff;
        color: #675c63;
        font-size: 11px;
        font-weight: 650;
        line-height: 1.4;
        white-space: nowrap;
        transition: background-color .16s, border-color .16s;
    }
    .pengumuman-page .ann-category:hover { color: var(--ann-maroon); border-color: #c9a4ac; }
    .pengumuman-page .ann-category.is-active {
        border-color: var(--ann-maroon);
        background: var(--ann-maroon);
        color: #fff;
    }

    /* Grid pengumuman: dua kartu pada desktop, satu pada area sempit. */
    .pengumuman-page .ann-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: stretch;
        gap: 11px;
        min-width: 0;
        padding: 16px 19px 20px;
    }
    .pengumuman-page .ann-item {
        display: flex;
        align-items: flex-start;
        min-width: 0;
        gap: 13px;
        padding: 14px;
        border: 1px solid #ede2e6;
        border-radius: 11px;
        background: #fff;
        transition: border-color .18s, background-color .18s, box-shadow .18s;
    }
    .pengumuman-page .ann-item:hover {
        border-color: #d9b8c1;
        background: #fffafb;
        box-shadow: 0 4px 14px rgba(68, 24, 37, .045);
    }
    .pengumuman-page .ann-thumb {
        display: grid;
        place-items: center;
        flex: 0 0 86px;
        width: 86px;
        height: 86px;
        overflow: hidden;
        border: 1px solid #f0e0e5;
        border-radius: 9px;
        background: #faedf1;
        color: var(--ann-maroon);
    }
    .pengumuman-page .ann-thumb img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .pengumuman-page .ann-thumb svg { width: 27px; height: 27px; }
    .pengumuman-page .ann-item-body {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1 1 auto;
        align-items: flex-start;
    }
    .pengumuman-page .ann-tag {
        display: inline-flex;
        max-width: 100%;
        padding: 3px 8px;
        border-radius: 6px;
        background: #f7eff3;
        color: #8b2241;
        font-size: 10px;
        font-weight: 750;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }
    .pengumuman-page .ann-item h3 {
        margin: 7px 0 6px;
        color: #251d25;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }
    .pengumuman-page .ann-description {
        display: -webkit-box;
        width: 100%;
        margin: 0 0 7px;
        color: #756b73;
        font-size: 11.5px;
        line-height: 1.65;
        line-clamp: 3;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        overflow-wrap: anywhere;
    }
    .pengumuman-page .ann-date {
        display: flex;
        align-items: center;
        gap: 5px;
        margin: 0 0 9px;
        color: #8e8189;
        font-size: 10.5px;
        line-height: 1.5;
    }
    .pengumuman-page .ann-date svg { width: 13px; height: 13px; }
    .pengumuman-page .ann-more {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: auto;
        color: var(--ann-maroon);
        font-size: 11.5px;
        font-weight: 750;
        line-height: 1.5;
    }
    .pengumuman-page .ann-more:hover { text-decoration: underline; }
    .pengumuman-page .ann-empty {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        min-height: 170px;
        justify-content: center;
        padding: 26px 18px;
        border: 1px dashed #e5cfd5;
        border-radius: 11px;
        background: #fffafb;
        color: #857981;
        text-align: center;
        font-size: 12px;
        line-height: 1.6;
    }
    .pengumuman-page .ann-empty svg { color: var(--ann-maroon); width: 30px; height: 30px; }
    .pengumuman-page :is(a, button, input):focus-visible {
        outline: 3px solid rgba(201, 169, 106, .4);
        outline-offset: 2px;
    }

    /* Modal berada di luar container agar selalu berpusat pada viewport. */
    .pengumuman-modal.modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 3000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(26, 12, 19, .56);
        backdrop-filter: blur(3px);
    }
    .pengumuman-modal.modal-overlay.active { display: flex; }
    .pengumuman-modal .modal-box {
        position: relative;
        width: min(560px, 100%);
        max-height: min(88dvh, 850px);
        padding: 23px;
        overflow-y: auto;
        overscroll-behavior: contain;
        border: 1px solid #e9d8df;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 28px 75px rgba(22, 10, 18, .22);
    }
    .pengumuman-modal .modal-title {
        margin: 0 42px 13px 0;
        color: var(--ann-maroon);
        font-size: 17px;
        font-weight: 800;
        line-height: 1.35;
    }
    .pengumuman-modal .modal-body {
        margin-bottom: 20px;
        color: #514550;
        font-size: 12.5px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }
    .pengumuman-modal .btn-modal-x {
        position: absolute;
        top: 13px;
        right: 15px;
        display: grid;
        place-items: center;
        width: 33px;
        height: 33px;
        border: 1px solid #ebdce0;
        border-radius: 8px;
        background: #fff;
        color: #776b72;
        font-size: 20px;
        cursor: pointer;
    }
    .pengumuman-modal .btn-modal-action {
        width: 100%;
        min-height: 40px;
        border: 1px solid var(--ann-maroon);
        border-radius: 9px;
        background: var(--ann-maroon);
        color: #fff;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }
    .pengumuman-modal .btn-modal-action:hover { background: var(--ann-maroon-dark); }

    @container (max-width: 1020px) {
        .pengumuman-page .ann-list { grid-template-columns: minmax(0, 1fr); }
    }
    @container (max-width: 620px) {
        .pengumuman-page .ann-panel-head { padding: 14px 14px 12px; }
        .pengumuman-page .ann-controls { padding: 12px 14px; }
        .pengumuman-page .ann-list { padding: 12px 14px 16px; }
        .pengumuman-page .ann-header { align-items: flex-start; }
        .pengumuman-page .ann-category-row { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 5px; overscroll-behavior-x: contain; }
        .pengumuman-page .ann-category { flex: 0 0 auto; }
        .pengumuman-page .ann-category-label { flex: 0 0 auto; }
        .pengumuman-page .ann-thumb { flex-basis: 72px; width: 72px; height: 72px; }
        .pengumuman-page .ann-item { gap: 11px; padding: 12px; }
        .pengumuman-page .ann-item h3 { font-size: 12.5px; }
    }
    @container (max-width: 390px) {
        .pengumuman-page .ann-search-field { flex-basis: 100%; }
        .pengumuman-page .ann-search-submit { flex: 1; }
        .pengumuman-page .ann-reset { flex: 1; }
        .pengumuman-page .ann-thumb { flex-basis: 58px; width: 58px; height: 62px; }
        .pengumuman-page .ann-item { gap: 9px; padding: 10px; }
    }
    @media (max-width: 480px) {
        .pengumuman-modal.modal-overlay { padding: 0; align-items: flex-end; }
        .pengumuman-modal .modal-box { width: 100%; max-height: 91dvh; border-radius: 15px 15px 0 0; padding: 18px 16px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .pengumuman-page .ann-item,
        .pengumuman-page .ann-category { transition: none; }
    }
</style>
@endpush

@section('content')
<div class="pengumuman-page">
    <nav class="ann-breadcrumb" aria-label="Navigasi halaman">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Pengumuman</span>
    </nav>

    <header class="ann-header">
        <div class="ann-heading-text">
            <h1>Pengumuman</h1>
            <p class="ann-subtitle">Informasi terbaru seputar kegiatan dan aktivitas Ma’had Al-Jami’ah.</p>
        </div>
    </header>

    <section class="ann-panel" aria-labelledby="pengumumanDaftarTitle">
        <div class="ann-panel-head">
            <h2 id="pengumumanDaftarTitle">Daftar Pengumuman</h2>
            <span class="ann-total">{{ $pengumuman->count() }} pengumuman ditampilkan</span>
        </div>

        <div class="ann-controls">
            <form method="GET" action="{{ route('pengumuman.mhs') }}" class="ann-search" role="search">
                <label class="ann-search-field" for="cariPengumumanMhs">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3"/><path d="m16 16 4.5 4.5"/></svg>
                    <input type="search" name="cari" id="cariPengumumanMhs" value="{{ $filter['cari'] ?? '' }}" placeholder="Cari judul pengumuman..." autocomplete="off" aria-label="Cari judul pengumuman">
                </label>
                @if (filled($filter['kategori'] ?? null))
                    <input type="hidden" name="kategori" value="{{ $filter['kategori'] }}">
                @endif
                <button type="submit" class="ann-search-submit">Cari</button>
                @if (filled($filter['cari'] ?? null) || filled($filter['kategori'] ?? null))
                    <a href="{{ route('pengumuman.mhs') }}" class="ann-reset">Reset</a>
                @endif
            </form>

            <div class="ann-category-row" aria-label="Filter kategori pengumuman">
                <span class="ann-category-label">Kategori:</span>
                <a href="{{ route('pengumuman.mhs', array_filter(['cari' => $filter['cari'] ?? null])) }}"
                   class="ann-category {{ blank($filter['kategori'] ?? null) ? 'is-active' : '' }}">Semua</a>
                @foreach ($kategoriPilihan as $kategori)
                    <a href="{{ route('pengumuman.mhs', array_filter(['cari' => $filter['cari'] ?? null, 'kategori' => $kategori])) }}"
                       class="ann-category {{ ($filter['kategori'] ?? '') === $kategori ? 'is-active' : '' }}">{{ $kategori }}</a>
                @endforeach
            </div>
        </div>

        <div class="ann-list" id="announcement-list">
            @forelse ($pengumuman as $item)
                <article class="ann-item" data-category="{{ $item->kategori }}">
                    <div class="ann-thumb">
                        @if ($item->punyaGambar())
                            <img src="{{ route('pengumuman.mhs.berkas', [$item, 'gambar']) }}"
                                 alt="Gambar pengumuman {{ $item->judul }}" loading="lazy">
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                        @endif
                    </div>
                    <div class="ann-item-body">
                        <span class="ann-tag">{{ $item->kategori }}</span>
                        <h3>{{ $item->judul }}</h3>
                        <p class="ann-description">{{ $item->ringkasan(200) }}</p>
                        <div class="ann-date">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                            {{ $item->tanggalTeks() }}
                        </div>
                        <a class="ann-more" href="{{ route('pengumuman.mhs.show', $item) }}">
                            Baca selengkapnya <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="ann-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    <strong>Belum ada pengumuman</strong>
                    <span>Belum ada pengumuman yang sesuai dengan pencarian atau kategori yang dipilih.</span>
                </div>
            @endforelse
        </div>
    </section>
</div>

{{-- Modal informasi lama tetap disediakan untuk fungsi JavaScript yang sudah ada. --}}
<div class="pengumuman-modal modal-overlay" id="infoModal" role="dialog"
     aria-modal="true" aria-labelledby="modalTitle" onclick="closeModalOnBackdrop(event)">
    <div class="modal-box">
        <button type="button" class="btn-modal-x" aria-label="Tutup" onclick="closeInfoModal()">&times;</button>
        <h2 class="modal-title" id="modalTitle">Informasi</h2>
        <div class="modal-body" id="modalBody"></div>
        <button type="button" class="btn-modal-action" onclick="closeInfoModal()">Tutup</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal informasi (fungsi asal dipertahankan).
    function openInfoModal(title, message) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalBody').innerHTML = message;
        document.getElementById('infoModal').classList.add('active');
    }
    function closeInfoModal() {
        document.getElementById('infoModal').classList.remove('active');
    }
    function closeModalOnBackdrop(event) {
        if (event.target.id === 'infoModal') closeInfoModal();
    }
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeInfoModal();
    });
</script>
@endpush
