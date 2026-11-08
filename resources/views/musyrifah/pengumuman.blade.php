@extends('layouts.musyrifah')

@section('content')
<style>
    .pengumuman-page {
        padding: 28px 30px;
        color: #171717;
        font-family: inherit;
    }

    .pengumuman-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin: 0 0 32px;
    }

    .pengumuman-header h1 {
        margin: 0 0 8px;
        font-size: 32px;
        font-weight: 700;
    }

    .pengumuman-header p {
        margin: 0;
        color: #858585;
        font-size: 20px;
    }

    .btn-tambah-pengumuman {
        background: #6B0D1B;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 17px 45px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        display: inline-block;
        text-align: center;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-tambah-pengumuman:hover {
        background: #4d0813;
    }

    .pengumuman-alert {
        margin: 0 0 22px;
        padding: 14px 20px;
        border-radius: 14px;
        border: 1px solid #C9A96A;
        background: #fdf6e8;
        color: #6B0D1B;
        font-size: 17px;
        font-weight: 600;
    }

    .pengumuman-filter {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 14px;
        align-items: end;
        background: #f5e9ec;
        border-radius: 20px;
        padding: 18px 22px;
        margin-bottom: 28px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 7px;
        font-size: 15px;
        font-weight: 700;
        color: #424242;
    }

    .filter-control {
        width: 100%;
        min-height: 48px;
        padding: 11px 14px;
        background: #fff;
        border: 1px solid transparent;
        border-radius: 12px;
        font: inherit;
        font-size: 16px;
        color: #333;
        box-sizing: border-box;
        outline: none;
    }

    .filter-control:focus {
        border-color: #6B0D1B;
    }

    .filter-aksi {
        display: flex;
        gap: 10px;
    }

    .btn-filter {
        min-height: 48px;
        padding: 0 22px;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-filter-terapkan {
        background: #6B0D1B;
        color: #fff;
    }

    .btn-filter-terapkan:hover {
        background: #4d0813;
    }

    .btn-filter-reset {
        background: #e6d7da;
        color: #6B0D1B;
    }

    .btn-filter-reset:hover {
        background: #d8c3c7;
    }

    .daftar-pengumuman {
        background: #f4e6e9;
        border: 1px solid #333;
        border-radius: 20px;
        box-shadow: 0 3px 4px #00000025;
        padding: 4px 7px;
        margin-bottom: 34px;
        overflow: hidden;
    }

    .pengumuman-item {
        display: flex;
        gap: 18px;
        padding: 18px 12px;
        border-bottom: 1px solid #d8c3c7;
    }

    .pengumuman-item:last-child {
        border-bottom: none;
    }

    .pengumuman-thumb {
        width: 92px;
        height: 74px;
        flex-shrink: 0;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #d8c3c7;
        display: grid;
        place-items: center;
        color: #6B0D1B;
        font-size: 22px;
        text-decoration: none;
    }

    .pengumuman-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .pengumuman-isi {
        flex: 1;
        min-width: 0;
    }

    .pengumuman-isi h3 {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 8px;
        line-height: 1.35;
    }

    .pengumuman-isi h3 a {
        color: #171717;
        text-decoration: none;
    }

    .pengumuman-isi h3 a:hover {
        color: #6B0D1B;
        text-decoration: underline;
    }

    .pengumuman-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        color: #6f6668;
        font-size: 15px;
    }

    .pengumuman-tag {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        background: #fff;
        border: 1px solid #C9A96A;
        color: #6B0D1B;
    }

    .pengumuman-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }

    .pengumuman-status.terbit {
        background: #e7f5ec;
        color: #1f7a4a;
    }

    .pengumuman-status.draft {
        background: #fdefd6;
        color: #96610a;
    }

    .pengumuman-aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .btn-aksi {
        padding: 8px 16px;
        border-radius: 10px;
        border: 1px solid #6B0D1B;
        background: transparent;
        color: #6B0D1B;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-aksi:hover {
        background: #6B0D1B;
        color: #fff;
    }

    .btn-aksi.utama {
        background: #6B0D1B;
        color: #fff;
    }

    .btn-aksi.utama:hover {
        background: #4d0813;
    }

    .btn-aksi.bahaya {
        border-color: #a3162b;
        color: #a3162b;
    }

    .btn-aksi.bahaya:hover {
        background: #a3162b;
        color: #fff;
    }

    .pengumuman-kosong {
        padding: 32px 15px;
        text-align: center;
        color: #77716d;
        font-size: 17px;
    }

    .pengumuman-statistik {
        display: grid;
        grid-template-columns: 1fr 1.5fr 1.35fr;
        background: #f4e6e9;
        border: 1px solid #333;
        border-radius: 20px;
        box-shadow: 0 3px 4px #00000025;
        overflow: hidden;
    }

    .statistik-kolom {
        text-align: center;
        min-width: 0;
    }

    .statistik-kolom + .statistik-kolom {
        border-left: 1px solid #8a8082;
    }

    .statistik-judul {
        padding: 19px 8px;
        border-bottom: 1px solid #8a8082;
        font-size: 20px;
        font-weight: 700;
    }

    .statistik-angka {
        padding: 33px 8px;
        font-size: 18px;
        font-weight: 700;
    }

    @media (max-width: 900px) {
        .pengumuman-filter {
            grid-template-columns: 1fr;
        }

        .filter-aksi {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .pengumuman-page {
            padding: 20px 12px;
        }

        .pengumuman-header {
            margin-bottom: 28px;
            flex-direction: column;
        }

        .pengumuman-header h1 {
            font-size: 27px;
        }

        .pengumuman-header p {
            font-size: 16px;
        }

        .btn-tambah-pengumuman {
            padding: 12px 25px;
        }

        .pengumuman-item {
            flex-direction: column;
        }

        .pengumuman-statistik {
            grid-template-columns: 1fr;
        }

        .statistik-kolom + .statistik-kolom {
            border-left: none;
            border-top: 1px solid #8a8082;
        }

        .statistik-angka {
            padding: 20px 8px;
        }
    }
</style>

<div class="pengumuman-page">

    <div class="pengumuman-header">
        <div>
            <h1>Pengumuman</h1>
            <p>Kelola Informasi Untuk Mahasantri</p>
        </div>

        <a href="{{ route('musyrifah.detail-pengumuman') }}" class="btn-tambah-pengumuman">
            Tambah
        </a>
    </div>

    @if (session('success'))
        <div class="pengumuman-alert" role="status">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('musyrifah.pengumuman') }}" class="pengumuman-filter">
        <div class="filter-group">
            <label for="cariPengumuman">Cari Pengumuman</label>
            <input
                type="text"
                id="cariPengumuman"
                name="cari"
                value="{{ $filter['cari'] ?? '' }}"
                class="filter-control"
                placeholder="Cari Judul Pengumuman"
                autocomplete="off"
            >
        </div>

        <div class="filter-group">
            <label for="filterKategori">Kategori</label>
            <select id="filterKategori" name="kategori" class="filter-control">
                <option value="">Semua Kategori</option>
                @foreach ($kategoriPilihan as $kategori)
                    <option
                        value="{{ $kategori }}"
                        @selected(($filter['kategori'] ?? '') === $kategori)
                    >
                        {{ $kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label for="filterStatus">Status</label>
            <select id="filterStatus" name="status" class="filter-control">
                <option value="">Semua Status</option>
                @foreach ($statusPilihan as $nilai => $label)
                    <option
                        value="{{ $nilai }}"
                        @selected(($filter['status'] ?? '') === $nilai)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-aksi">
            <button type="submit" class="btn-filter btn-filter-terapkan">Terapkan</button>
            <a href="{{ route('musyrifah.pengumuman') }}" class="btn-filter btn-filter-reset">Reset</a>
        </div>
    </form>

    <h2 class="pengumuman-header-label" style="font-size: 20px; margin: 0 0 12px;">Daftar Pengumuman</h2>

    <div class="daftar-pengumuman">
        @forelse ($pengumumans as $item)
            <div class="pengumuman-item">
                <a
                    href="{{ $item->punyaGambar() ? route('musyrifah.pengumuman.berkas', [$item, 'gambar']) : route('musyrifah.pengumuman.show', $item) }}"
                    class="pengumuman-thumb"
                    @if ($item->punyaGambar()) target="_blank" rel="noopener" @endif
                    aria-label="Buka pengumuman {{ $item->judul }}"
                >
                    @if ($item->punyaGambar())
                        <img
                            src="{{ route('musyrifah.pengumuman.berkas', [$item, 'gambar']) }}"
                            alt="Gambar pengumuman {{ $item->judul }}"
                        >
                    @else
                        &#9873;
                    @endif
                </a>

                <div class="pengumuman-isi">
                    <h3>
                        <a href="{{ route('musyrifah.pengumuman.show', $item) }}">
                            {{ $item->judul }}
                        </a>
                    </h3>

                    <div class="pengumuman-meta">
                        <span class="pengumuman-tag">{{ $item->kategori }}</span>
                        <span class="pengumuman-status {{ $item->kelasStatus() }}">
                            {{ $item->labelStatus() }}
                        </span>
                        <span>Publikasi: {{ $item->tanggalTeks() }}</span>
                        <span>Penulis: {{ $item->penulisTeks() }}</span>
                        @if ($item->punyaDokumen())
                            <span>Dokumen terlampir</span>
                        @endif
                    </div>

                    <div class="pengumuman-aksi">
                        <a href="{{ route('musyrifah.pengumuman.show', $item) }}" class="btn-aksi">
                            Detail
                        </a>

                        <a href="{{ route('musyrifah.pengumuman.edit', $item) }}" class="btn-aksi">
                            Edit
                        </a>

                        <form
                            method="POST"
                            action="{{ route('musyrifah.pengumuman.status', $item) }}"
                        >
                            @csrf
                            <input
                                type="hidden"
                                name="status"
                                value="{{ $item->isDipublikasikan() ? \App\Models\Pengumuman::STATUS_DRAFT : \App\Models\Pengumuman::STATUS_DIPUBLIKASIKAN }}"
                            >
                            <button type="submit" class="btn-aksi utama">
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
                            <button type="submit" class="btn-aksi bahaya">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="pengumuman-kosong">
                Belum ada pengumuman yang sesuai dengan pencarian atau filter.
            </div>
        @endforelse
    </div>

    <div class="pengumuman-statistik">
        <div class="statistik-kolom">
            <div class="statistik-judul">Total</div>
            <div class="statistik-angka">{{ $statistik['total'] }} Pengumuman</div>
        </div>

        <div class="statistik-kolom">
            <div class="statistik-judul">Terbit</div>
            <div class="statistik-angka">{{ $statistik['terbit'] }} Dipublikasikan</div>
        </div>

        <div class="statistik-kolom">
            <div class="statistik-judul">Draft</div>
            <div class="statistik-angka">{{ $statistik['draft'] }} Belum Terbit</div>
        </div>
    </div>
</div>
@endsection
