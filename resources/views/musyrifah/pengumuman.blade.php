@extends('layouts.musyrifah')
@section('title', 'Pengumuman')
@section('content')
<style>
    /* SIMAHAD — Pengumuman Musyrifah | styling terisolasi dari layout/sidebar */
    .pm {
        --pm-maroon:#800b22;
        --pm-maroon-dark:#66091d;
        --pm-text:#251c25;
        --pm-muted:#887b83;
        --pm-border:#eadde0;
        width:100% !important;
        max-width:100% !important;
        min-width:0 !important;
        margin:0 !important;
        padding:0 0 28px !important;
        display:block !important;
        clear:both;
        position:relative;
        color:var(--pm-text);
        font-family:'Plus Jakarta Sans','Poppins',Arial,sans-serif;
        font-size:13px;
        container-type:inline-size;
    }
    .pm *, .pm *::before, .pm *::after { box-sizing:border-box; }
    .pm > * { min-width:0; }
    .pm .pm-breadcrumb {
        display:flex; align-items:center; flex-wrap:wrap; gap:6px;
        margin:0 0 12px; color:#93858c; font-size:12px;
    }
    .pm .pm-breadcrumb a { color:var(--pm-maroon); font-weight:600; text-decoration:none; }
    .pm .pm-breadcrumb a:hover { text-decoration:underline; }
    .pm .pm-head {
        display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between;
        gap:12px 18px; padding:0 0 20px;
        margin:0 0 21px; border-bottom:1px solid #e9ced4;
    }
    .pm .pm-head > div { flex:1 1 350px; min-width:0; }
    .pm .pm-head h1 {
        margin:0; font-size:clamp(22px,2.4vw,28px);
        line-height:1.22; font-weight:800; letter-spacing:-.035em;
        color:#201921;
    }
    .pm .pm-head p { margin:7px 0 0; color:var(--pm-muted); font-size:12.5px; line-height:1.65; }
    .pm .pm-btn {
        appearance:none; display:inline-flex; align-items:center; justify-content:center;
        gap:7px; min-height:37px; padding:8px 13px;
        border:1px solid #e4cdd3; border-radius:9px;
        background:#fff; color:var(--pm-maroon);
        font-family:inherit; line-height:1.4; font-weight:700; font-size:12px;
        cursor:pointer; text-decoration:none; text-align:center;
        transition:background-color .16s,border-color .16s,color .16s;
    }
    .pm .pm-btn--utama { background:var(--pm-maroon); color:#fff; border-color:var(--pm-maroon); }
    .pm .pm-btn--utama:hover { background:var(--pm-maroon-dark); border-color:var(--pm-maroon-dark); color:#fff; }
    .pm .pm-btn--garis { background:#fff; color:var(--pm-maroon); }
    .pm .pm-btn--garis:hover { background:#fff6f8; border-color:#c9a4ae; }
    .pm .pm-btn--bahaya { background:#fff; border-color:#f3c9d1; color:#aa2138; }
    .pm .pm-btn--bahaya:hover { background:#fff1f3; border-color:#d890a0; }
    .pm .pm-btn--kecil { min-height:32px; padding:6px 10px; font-size:11.5px; }
    .pm .pm-btn:focus-visible, .pm .pm-field :focus-visible, .pm .pm-thumb:focus-visible {
        outline:3px solid rgba(128,11,34,.22); outline-offset:2px;
    }
    .pm .pm-alert {
        margin:0 0 16px; padding:12px 15px; border:1px solid #b8e2c4;
        border-radius:10px; background:#f0fbf3; color:#166534;
        font-size:12.5px; font-weight:600;
    }
    /* Kartu statistik: ringkas dan seragam dengan modul admin. */
    .pm .pm-stat {
        display:grid; grid-template-columns:repeat(3,minmax(0,1fr));
        gap:13px; margin:0 0 21px;
    }
    .pm .pm-stat-item {
        display:flex; align-items:center; gap:13px;
        min-width:0; min-height:86px; padding:14px 16px;
        background:#fff; border:1px solid var(--pm-border); border-radius:13px;
        box-shadow:0 3px 12px rgba(53,24,33,.025);
    }
    .pm .pm-stat-icon {
        flex:0 0 42px; display:grid; place-items:center; width:42px; height:42px;
        border-radius:11px; background:#f8e9ed; color:var(--pm-maroon);
    }
    .pm .pm-stat-item:nth-child(2) .pm-stat-icon { color:#15804a; background:#e9f8ee; }
    .pm .pm-stat-item:nth-child(3) .pm-stat-icon { color:#a36a15; background:#fff3df; }
    .pm .pm-stat-icon svg { width:21px; height:21px; }
    .pm .pm-stat-copy { min-width:0; display:grid; gap:4px; }
    .pm .pm-stat-label { font-size:11.5px; font-weight:600; color:var(--pm-muted); }
    .pm .pm-stat-sub { color:#2b2028; font-size:20px; font-weight:800; line-height:1.2; overflow-wrap:anywhere; }
    .pm .pm-stat-item:nth-child(2) .pm-stat-sub { color:#146b3d; }
    /* Kontainer daftar lengkap: judul, filter, dan daftar dalam satu kartu. */
    .pm .pm-kartu {
        width:100%; max-width:100%; min-width:0; margin:0 0 18px;
        background:#fff; border:1px solid var(--pm-border); border-radius:15px;
        box-shadow:0 3px 14px rgba(53,24,33,.025); overflow:hidden;
    }
    .pm .pm-sec-head {
        display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;
        gap:9px; padding:20px 22px; border-bottom:1px solid #f0e5e8;
    }
    .pm .pm-sec-head h2 { margin:0; font-size:16px; font-weight:800; color:#291e27; }
    .pm .pm-count { font-size:11.5px; color:var(--pm-muted); }
    .pm .pm-filter {
        display:grid; align-items:end;
        grid-template-columns:minmax(0,2fr) minmax(160px,.9fr) minmax(150px,.9fr) auto;
        gap:12px; padding:18px 20px 20px;
    }
    .pm .pm-field { display:flex; flex-direction:column; gap:7px; min-width:0; }
    .pm .pm-field label { color:#5a5057; font-size:11.5px; font-weight:650; }
    .pm .pm-field input, .pm .pm-field select {
        display:block; width:100%; min-width:0; height:41px; padding:0 12px;
        border:1px solid #e4d6db; border-radius:9px;
        background:#fff; color:#2d262a; font-family:inherit; font-weight:400; line-height:1.4; font-size:12.5px;
    }
    .pm .pm-field input:focus, .pm .pm-field select:focus {
        outline:none; border-color:#b98090; box-shadow:0 0 0 3px rgba(128,11,34,.09);
    }
    .pm .pm-filter-aksi { display:flex; gap:7px; align-items:center; }
    .pm .pm-filter-aksi .pm-btn { min-height:41px; }
    .pm .pm-list { width:100%; min-width:0; }
    .pm .pm-list-head {
        display:flex; justify-content:space-between; gap:12px;
        padding:12px 21px; background:var(--pm-maroon); color:#fff;
        font-size:11.5px; font-weight:700;
    }
    .pm .pm-item {
        display:grid; grid-template-columns:74px minmax(0,1fr) auto;
        align-items:center; gap:15px; min-width:0;
        padding:17px 20px; background:#fff;
        border-bottom:1px solid #efe6e8;
    }
    .pm .pm-item:nth-child(even) { background:#fcfafb; }
    .pm .pm-item:hover { background:#fff8f9; }
    .pm .pm-item:last-child { border-bottom:0; }
    .pm .pm-thumb {
        display:grid; place-items:center; flex-shrink:0;
        width:74px; height:66px; overflow:hidden;
        border:1px solid #ebdce0; border-radius:11px;
        background:#faedef; color:var(--pm-maroon);
        font-size:22px; text-decoration:none;
    }
    .pm .pm-thumb img { display:block; width:100%; height:100%; object-fit:cover; }
    .pm .pm-isi { min-width:0; display:grid; gap:7px; }
    .pm .pm-judul-baris { display:flex; flex-wrap:wrap; align-items:center; gap:7px 11px; }
    .pm .pm-judul-baris h3 {
        margin:0; min-width:0; font-size:13.5px; font-weight:750; line-height:1.5;
        overflow-wrap:anywhere;
    }
    .pm .pm-judul-baris h3 a { color:#251c24; text-decoration:none; }
    .pm .pm-judul-baris h3 a:hover { color:var(--pm-maroon); text-decoration:underline; }
    .pm .pm-status {
        display:inline-flex; align-items:center; gap:5px; padding:4px 10px;
        border-radius:100px; font-size:10.5px; font-weight:700; white-space:nowrap;
    }
    .pm .pm-status::before { content:""; display:inline-block; flex:0 0 6px; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .pm .pm-status.terbit { background:#e6f8ed; color:#13713d; }
    .pm .pm-status.draft { background:#fff4d9; color:#9b6815; }
    .pm .pm-meta { display:flex; flex-wrap:wrap; align-items:center; gap:6px 11px; color:#8c8086; font-size:11.5px; line-height:1.5; }
    .pm .pm-tag, .pm .pm-doc {
        display:inline-flex; align-items:center; padding:3px 8px;
        border-radius:6px; font-size:10.5px; font-weight:650;
        background:#f7edf0; color:var(--pm-maroon);
    }
    .pm .pm-doc { background:#f3f2f5; color:#55505d; }
    .pm .pm-aksi {
        display:flex; align-items:center; flex-wrap:wrap; justify-content:flex-end;
        gap:7px; min-width:0;
    }
    .pm .pm-aksi form { display:inline-flex; margin:0; }
    .pm .pm-kosong {
        padding:32px 18px; text-align:center; font-size:12.5px; color:var(--pm-muted);
    }
    /* Container queries membuat grid tetap rapi walau sidebar dibuka. */
    @container (max-width:1050px) {
        .pm .pm-filter { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .pm .pm-field--cari { grid-column:1/-1; }
        .pm .pm-filter-aksi { grid-column:1/-1; justify-content:flex-start; }
    }
    @container (max-width:880px) {
        .pm .pm-item { grid-template-columns:70px minmax(0,1fr); gap:10px 13px; }
        .pm .pm-thumb { width:70px; height:64px; }
        .pm .pm-aksi { grid-column:2; justify-content:flex-start; }
    }
    @container (max-width:590px) {
        .pm .pm-head { align-items:flex-start; }
        .pm .pm-head > .pm-btn { width:100%; }
        .pm .pm-stat { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        .pm .pm-stat-item { min-height:74px; gap:8px; padding:12px 11px; }
        .pm .pm-stat-icon { flex-basis:34px; width:34px; height:34px; border-radius:9px; }
        .pm .pm-stat-icon svg { width:18px; height:18px; }
        .pm .pm-stat-sub { font-size:17px; }
        .pm .pm-stat-item:last-child { grid-column:1/-1; }
        .pm .pm-sec-head { padding:16px 14px; }
        .pm .pm-filter { padding:15px 14px 16px; grid-template-columns:minmax(0,1fr); gap:12px; }
        .pm .pm-field--cari, .pm .pm-filter-aksi { grid-column:auto; }
        .pm .pm-filter-aksi .pm-btn { flex:1; }
        .pm .pm-list-head { display:none; }
        .pm .pm-item { grid-template-columns:54px minmax(0,1fr); padding:15px 13px; gap:9px 12px; }
        .pm .pm-thumb { width:54px; height:53px; border-radius:9px; }
        .pm .pm-judul-baris h3 { font-size:13px; }
        .pm .pm-aksi { grid-column:1/-1; justify-content:flex-start; padding-top:10px; border-top:1px solid #f1e7e9; }
        .pm .pm-aksi .pm-btn { min-height:34px; }
    }
    @container (max-width:300px) {
        .pm .pm-stat { grid-template-columns:minmax(0,1fr); }
        .pm .pm-stat-item:last-child { grid-column:auto; }
        .pm .pm-aksi { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); }
        .pm .pm-aksi form, .pm .pm-aksi .pm-btn { width:100%; }
    }
</style>
<div class="pm">
    <nav class="pm-breadcrumb" aria-label="Navigasi halaman">
        <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Pengumuman</span>
    </nav>
    {{-- KEPALA HALAMAN --}}
    <div class="pm-head">
        <div>
            <h1>Pengumuman</h1>
            <p>Kelola informasi, publikasi, dan dokumen pengumuman untuk mahasiswa Ma'had.</p>
        </div>
        <a href="{{ route('musyrifah.detail-pengumuman') }}" class="pm-btn pm-btn--utama">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pengumuman
        </a>
    </div>
    {{-- STATISTIK RINGKAS --}}
    <div class="pm-stat" aria-label="Ringkasan pengumuman">
        <div class="pm-stat-item">
            <span class="pm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></span>
            <div class="pm-stat-copy"><span class="pm-stat-label">Total Pengumuman</span><strong class="pm-stat-sub">{{ $statistik['total'] }}</strong></div>
        </div>
        <div class="pm-stat-item">
            <span class="pm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg></span>
            <div class="pm-stat-copy"><span class="pm-stat-label">Dipublikasikan</span><strong class="pm-stat-sub">{{ $statistik['terbit'] }}</strong></div>
        </div>
        <div class="pm-stat-item">
            <span class="pm-stat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h10l3 3v13H6z"/><path d="M16 4v4h4M9 12h7M9 16h5"/></svg></span>
            <div class="pm-stat-copy"><span class="pm-stat-label">Masih Draft</span><strong class="pm-stat-sub">{{ $statistik['draft'] }}</strong></div>
        </div>
    </div>
    {{-- FILTER DAN DAFTAR DALAM SATU PANEL --}}
    <div class="pm-kartu">
        <div class="pm-sec-head">
            <h2>Daftar Pengumuman</h2>
            <span class="pm-count">{{ $pengumumans->count() }} pengumuman ditampilkan</span>
        </div>
        <form method="GET" action="{{ route('musyrifah.pengumuman') }}" class="pm-filter">
            <div class="pm-field pm-field--cari">
                <label for="cariPengumuman">Cari pengumuman</label>
                <input
                    type="text"
                    id="cariPengumuman"
                    name="cari"
                    value="{{ $filter['cari'] ?? '' }}"
                    placeholder="Cari judul pengumuman"
                    autocomplete="off"
                >
            </div>
            <div class="pm-field">
                <label for="filterKategori">Kategori</label>
                <select id="filterKategori" name="kategori">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoriPilihan as $kategori)
                        <option value="{{ $kategori }}" @selected(($filter['kategori'] ?? '') === $kategori)>
                            {{ $kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="pm-field">
                <label for="filterStatus">Status</label>
                <select id="filterStatus" name="status">
                    <option value="">Semua status</option>
                    @foreach ($statusPilihan as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="pm-filter-aksi">
                <button type="submit" class="pm-btn pm-btn--utama">Terapkan</button>
                <a href="{{ route('musyrifah.pengumuman') }}" class="pm-btn pm-btn--garis">Reset</a>
            </div>
        </form>
        <div class="pm-list">
            <div class="pm-list-head" aria-hidden="true"><span>Informasi Pengumuman</span><span>Tindakan</span></div>
            @forelse ($pengumumans as $item)
                <article class="pm-item">
                    <a
                        href="{{ $item->punyaGambar() ? route('musyrifah.pengumuman.berkas', [$item, 'gambar']) : route('musyrifah.pengumuman.show', $item) }}"
                        class="pm-thumb"
                        @if ($item->punyaGambar()) target="_blank" rel="noopener" @endif
                        aria-label="Buka pengumuman {{ $item->judul }}"
                    >
                        @if ($item->punyaGambar())
                            <img
                                src="{{ route('musyrifah.pengumuman.berkas', [$item, 'gambar']) }}"
                                alt="Gambar pengumuman {{ $item->judul }}"
                                loading="lazy"
                            >
                        @else
                            <svg viewBox="0 0 24 24" width="25" height="25" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 3v18M5 5h14l-3 5 3 5H5"/></svg>
                        @endif
                    </a>
                    <div class="pm-isi">
                        <div class="pm-judul-baris">
                            <h3>
                                <a href="{{ route('musyrifah.pengumuman.show', $item) }}">
                                    {{ $item->judul }}
                                </a>
                            </h3>
                            <span class="pm-status {{ $item->kelasStatus() }}">
                                {{ $item->labelStatus() }}
                            </span>
                        </div>
                        <div class="pm-meta">
                            <span class="pm-tag">{{ $item->kategori }}</span>
                            <span>Publikasi: {{ $item->tanggalTeks() }}</span>
                            <span>Penulis: {{ $item->penulisTeks() }}</span>
                            @if ($item->punyaDokumen())
                                <span class="pm-doc">Dokumen terlampir</span>
                            @endif
                        </div>
                    </div>
                    <div class="pm-aksi">
                            <a href="{{ route('musyrifah.pengumuman.show', $item) }}" class="pm-btn pm-btn--garis pm-btn--kecil">
                                Detail
                            </a>
                            <a href="{{ route('musyrifah.pengumuman.edit', $item) }}" class="pm-btn pm-btn--garis pm-btn--kecil">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('musyrifah.pengumuman.status', $item) }}">
                                @csrf
                                <input
                                    type="hidden"
                                    name="status"
                                    value="{{ $item->isDipublikasikan() ? \App\Models\Pengumuman::STATUS_DRAFT : \App\Models\Pengumuman::STATUS_DIPUBLIKASIKAN }}"
                                >
                                <button type="submit" class="pm-btn pm-btn--utama pm-btn--kecil">
                                    {{ $item->isDipublikasikan() ? 'Jadikan Draft' : 'Publikasikan' }}
                                </button>
                            </form>
                            <form
                                method="POST"
                                action="{{ route('musyrifah.pengumuman.destroy', $item) }}"
                                data-sui-confirm="Hapus pengumuman ini? Data yang dihapus tidak dapat dikembalikan."
                                data-sui-confirm-title="Hapus pengumuman?"
                                data-sui-confirm-label="Ya, Hapus"
                                data-sui-confirm-tone="danger"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="pm-btn pm-btn--bahaya pm-btn--kecil">Hapus</button>
                            </form>
                    </div>
                </article>
            @empty
                <div class="pm-kosong">
                    Belum ada pengumuman yang sesuai dengan pencarian atau filter.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
