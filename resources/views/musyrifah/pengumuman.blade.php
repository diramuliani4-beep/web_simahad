@extends('layouts.musyrifah')

@section('content')
<style>
    .pm {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        color: #322a2d;
        font-size: 13px;
    }

    .pm *,
    .pm *::before,
    .pm *::after {
        box-sizing: border-box;
    }

    /* ---------------- Kepala halaman ---------------- */
    .pm-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px 14px;
        margin-bottom: 12px;
    }

    .pm-head h1 {
        margin: 0;
        color: #211a1c;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -.2px;
    }

    .pm-head p {
        margin: 4px 0 0;
        color: #8b7e84;
        font-size: 12.5px;
        line-height: 1.5;
    }

    /* ---------------- Tombol ---------------- */
    .pm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 32px;
        padding: 6px 13px;
        border: 1px solid transparent;
        border-radius: 9px;
        background: #fff;
        color: #6B0D1B;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background .16s, border-color .16s, color .16s;
    }

    .pm-btn--utama {
        border-color: #6B0D1B;
        background: #6B0D1B;
        color: #fff;
    }

    .pm-btn--utama:hover {
        border-color: #52070f;
        background: #52070f;
    }

    .pm-btn--garis {
        border-color: #e6d7dc;
        background: #fff;
        color: #6B0D1B;
    }

    .pm-btn--garis:hover {
        border-color: #C9A96A;
        background: #F8EDEF;
    }

    .pm-btn--bahaya {
        border-color: #f0d3d8;
        background: #fff;
        color: #a3162b;
    }

    .pm-btn--bahaya:hover {
        border-color: #a3162b;
        background: #a3162b;
        color: #fff;
    }

    .pm-btn--kecil {
        min-height: 26px;
        padding: 4px 9px;
        border-radius: 7px;
        font-size: 11.5px;
    }

    .pm-btn:focus-visible {
        outline: 3px solid rgba(201, 169, 106, .45);
        outline-offset: 1px;
    }

    /* ---------------- Notifikasi ---------------- */
    .pm-alert {
        margin-bottom: 12px;
        padding: 9px 12px;
        border: 1px solid #e9d9b8;
        border-left: 3px solid #C9A96A;
        border-radius: 9px;
        background: #fdf6e8;
        color: #6B0D1B;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    /* ---------------- Statistik ---------------- */
    .pm-stat {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 9px;
        margin-bottom: 12px;
    }

    .pm-stat-item {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
        padding: 10px 12px;
        border: 1px solid #efe5e7;
        border-radius: 11px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(59, 7, 15, .03);
    }

    .pm-stat-label {
        color: #8b7e84;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .pm-stat-sub {
        color: #6B0D1B;
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.3;
    }

    /* ---------------- Kartu ---------------- */
    .pm-kartu {
        margin-bottom: 12px;
        padding: 12px 14px 14px;
        border: 1px solid #efe5e7;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(59, 7, 15, .035);
    }

    .pm-sec-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 6px 10px;
        margin-bottom: 9px;
    }

    .pm-sec-head h2 {
        position: relative;
        margin: 0;
        padding-left: 9px;
        color: #6B0D1B;
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.3;
    }

    .pm-sec-head h2::before {
        content: "";
        position: absolute;
        top: 2px;
        bottom: 2px;
        left: 0;
        width: 3px;
        border-radius: 2px;
        background: #C9A96A;
    }

    .pm-count {
        color: #8b7e84;
        font-size: 11px;
    }

    /* ---------------- Filter ---------------- */
    .pm-filter {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr) auto;
        align-items: end;
        gap: 9px;
    }

    .pm-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
    }

    .pm-field label {
        color: #7d7075;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
    }

    .pm-field input,
    .pm-field select {
        width: 100%;
        height: 34px;
        padding: 0 9px;
        border: 1px solid #e6dde0;
        border-radius: 8px;
        background: #fff;
        color: #3f353a;
        font-family: inherit;
        font-size: 12.5px;
        outline: none;
        transition: border-color .16s, box-shadow .16s;
    }

    .pm-field input:focus,
    .pm-field select:focus {
        border-color: #C9A96A;
        box-shadow: 0 0 0 3px rgba(201, 169, 106, .16);
    }

    .pm-filter-aksi {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    /* ---------------- Daftar pengumuman ---------------- */
    .pm-list {
        border: 1px solid #f1e9eb;
        border-radius: 10px;
        overflow: hidden;
    }

    .pm-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px;
        border-bottom: 1px solid #f4eef0;
        background: #fff;
    }

    .pm-item:last-child {
        border-bottom: 0;
    }

    .pm-item:hover {
        background: #fffbfc;
    }

    .pm-thumb {
        display: grid;
        place-items: center;
        flex: 0 0 58px;
        width: 58px;
        height: 50px;
        overflow: hidden;
        border: 1px solid #efe0e4;
        border-radius: 8px;
        background: #F8EDEF;
        color: #6B0D1B;
        font-size: 18px;
        line-height: 1;
        text-decoration: none;
    }

    .pm-thumb img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pm-isi {
        flex: 1;
        min-width: 0;
    }

    .pm-judul-baris {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 4px 10px;
    }

    .pm-judul-baris h3 {
        margin: 0;
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .pm-judul-baris h3 a {
        color: #2c2226;
        text-decoration: none;
    }

    .pm-judul-baris h3 a:hover {
        color: #6B0D1B;
    }

    .pm-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .pm-status.terbit {
        background: #e7f5ec;
        color: #1f7a4a;
    }

    .pm-status.draft {
        background: #fdefd6;
        color: #96610a;
    }

    .pm-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px 8px;
        margin-top: 4px;
        color: #8b7e84;
        font-size: 11px;
        line-height: 1.5;
    }

    .pm-tag {
        display: inline-block;
        padding: 1px 8px;
        border: 1px solid #e6d7b8;
        border-radius: 999px;
        background: #fff;
        color: #6B0D1B;
        font-size: 10.5px;
        font-weight: 700;
    }

    .pm-doc {
        display: inline-block;
        padding: 1px 7px;
        border-radius: 5px;
        background: #F8EDEF;
        color: #6B0D1B;
        font-size: 10px;
        font-weight: 600;
    }

    .pm-aksi {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 7px;
    }

    .pm-aksi form {
        display: inline-flex;
        margin: 0;
    }

    .pm-kosong {
        padding: 16px;
        color: #91858a;
        font-size: 12px;
        text-align: center;
    }

    .pm-list .pm-kosong {
        border: 0;
    }

    @media (max-width: 900px) {
        .pm-filter {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }

        .pm-field--cari {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 640px) {
        .pm-head h1 {
            font-size: 19px;
        }

        .pm-filter {
            grid-template-columns: minmax(0, 1fr);
        }

        .pm-filter-aksi {
            width: 100%;
        }

        .pm-filter-aksi .pm-btn {
            flex: 1;
        }

        .pm-item {
            gap: 8px;
            padding: 9px;
        }

        .pm-thumb {
            flex-basis: 46px;
            width: 46px;
            height: 44px;
            font-size: 16px;
        }

        .pm-judul-baris h3 {
            font-size: 12.5px;
        }

        .pm-aksi .pm-btn {
            flex: 1;
        }
    }
</style>

<div class="pm">

    {{-- KEPALA HALAMAN --}}
    <div class="pm-head">
        <div>
            <h1>Pengumuman</h1>
            <p>Kelola Informasi Untuk Mahasantri</p>
        </div>

        <a href="{{ route('musyrifah.detail-pengumuman') }}" class="pm-btn pm-btn--utama">
            + Tambah Pengumuman
        </a>
    </div>

    @if (session('success'))
        <div class="pm-alert" role="status">
            {{ session('success') }}
        </div>
    @endif

    {{-- STATISTIK RINGKAS --}}
    <div class="pm-stat">
        <div class="pm-stat-item">
            <span class="pm-stat-label">Total</span>
            <span class="pm-stat-sub">{{ $statistik['total'] }} Pengumuman</span>
        </div>

        <div class="pm-stat-item">
            <span class="pm-stat-label">Terbit</span>
            <span class="pm-stat-sub">{{ $statistik['terbit'] }} Dipublikasikan</span>
        </div>

        <div class="pm-stat-item">
            <span class="pm-stat-label">Draft</span>
            <span class="pm-stat-sub">{{ $statistik['draft'] }} Belum Terbit</span>
        </div>
    </div>

    {{-- FILTER --}}
    <section class="pm-kartu">
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
    </section>

    {{-- DAFTAR PENGUMUMAN --}}
    <section class="pm-kartu">
        <div class="pm-sec-head">
            <h2>Daftar Pengumuman</h2>
            <span class="pm-count">{{ $pengumumans->count() }} item ditampilkan</span>
        </div>

        <div class="pm-list">
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
                            &#9873;
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
                                onsubmit="return confirm('Hapus pengumuman ini? Data yang dihapus tidak dapat dikembalikan.');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="pm-btn pm-btn--bahaya pm-btn--kecil">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="pm-kosong">
                    Belum ada pengumuman yang sesuai dengan pencarian atau filter.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
