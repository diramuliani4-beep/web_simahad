
@extends('layouts.musyrifah')

@section('title', 'Laporan Musyrifah')

@section('content')

<style>
    .laporan-page {
        width: 100%;
        padding: 0 10px 35px;
        color: #222;
    }

    .laporan-heading {
        margin: 0 15px 38px;
    }

    .laporan-heading h1 {
        font-size: 30px;
        font-weight: 700;
        color: #080808;
    }

    /* FILTER LAPORAN */
    .filter-wrapper {
        width: 100%;
        max-width: 680px;
        margin: 0 auto 45px;
        padding: 8px;
        background: #fafafa;
    }

    .filter-card {
        padding: 20px;
        background: #fff;
        border: 1px solid #ead8dd;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .filter-title {
        margin-bottom: 14px;
        font-size: 18px;
        font-weight: 700;
    }

    .filter-group {
        margin-bottom: 14px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 10px;
        color: #777;
        font-size: 15px;
    }

    .filter-group select {
        width: 100%;
        height: 52px;
        padding: 0 12px;
        border: 1px solid #d8d8d8;
        border-radius: 12px;
        background: #fff;
        color: #444;
        font-family: inherit;
        font-size: 17px;
        outline-color: #800d22;
    }

    .btn-tampilkan {
        width: 100%;
        min-height: 50px;
        border: none;
        border-radius: 11px;
        background: #700b20;
        color: #fff;
        font-family: inherit;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-tampilkan:hover {
        background: #590018;
    }

    /* DAFTAR LAPORAN */
    .laporan-list {
        width: 100%;
        max-width: 850px;
        margin: 0 auto;
        padding: 10px;
        background: #fafafa;
    }

    .laporan-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 18px;
        padding: 24px 20px 20px;
        background: #fff;
        border: 1px solid #ead8dd;
        border-radius: 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .laporan-icon {
        width: 58px;
        height: 58px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        background: #f9edef;
        color: #800d22;
        font-size: 29px;
    }

    .laporan-info {
        flex: 1;
        min-width: 0;
    }

    .laporan-info h2 {
        margin: 1px 0 8px;
        color: #444;
        font-size: 22px;
        font-weight: 700;
    }

    .laporan-info p {
        color: #888;
        font-size: 17px;
        line-height: 1.5;
    }

    .laporan-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 15px;
    }

    .btn-lihat {
        min-height: 48px;
        padding: 10px 15px;
        border: 1px solid #800d22;
        border-radius: 13px;
        background: #fff;
        color: #800d22;
        font-family: inherit;
        font-size: 16px;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-lihat:hover {
        background: #800d22;
        color: #fff;
    }

    .laporan-empty {
        padding: 35px 20px;
        border: 1px solid #ead8dd;
        border-radius: 15px;
        background: #fff;
        color: #777;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .laporan-heading {
            margin-bottom: 30px;
        }

        .laporan-info h2 {
            font-size: 19px;
        }

        .laporan-info p {
            font-size: 15px;
        }
    }

    @media (max-width: 768px) {
        .laporan-page {
            padding: 0 0 25px;
        }

        .laporan-heading {
            margin: 0 5px 28px;
        }

        .laporan-heading h1 {
            font-size: 24px;
        }

        .filter-wrapper {
            margin-bottom: 25px;
            padding: 3px;
        }

        .filter-card {
            padding: 16px;
        }

        .filter-group select {
            font-size: 15px;
        }

        .laporan-list {
            padding: 3px;
        }

        .laporan-card {
            gap: 10px;
            padding: 17px 12px;
            border-radius: 15px;
        }

        .laporan-icon {
            width: 43px;
            height: 43px;
            font-size: 23px;
        }

        .laporan-info h2 {
            font-size: 16px;
        }

        .laporan-info p {
            font-size: 13px;
        }

        .laporan-actions {
            margin-top: 12px;
        }

        .btn-lihat {
            min-height: 40px;
            padding: 8px 10px;
            font-size: 13px;
        }
    }
</style>

<div class="laporan-page">

    <section class="laporan-heading">
        <h1>← Laporan</h1>
    </section>

    {{-- FILTER LAPORAN --}}
    <section class="filter-wrapper">
        <form
            action="{{ route('musyrifah.laporan') }}"
            method="GET"
            class="filter-card"
        >
            <h2 class="filter-title">Filter Laporan</h2>

            <div class="filter-group">
                <label for="periode">Periode laporan</label>

                <select name="periode" id="periode">
                    @foreach ($opsiPeriode as $opsi)
                        <option
                            value="{{ $opsi['value'] }}"
                            @selected($periode === $opsi['value'])
                        >
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="jenis">Jenis laporan</label>

                <select name="jenis" id="jenis">
                    <option value="semua"
                        {{ $jenis === 'semua' ? 'selected' : '' }}>
                        Semua laporan
                    </option>

                    <option value="kehadiran"
                        {{ $jenis === 'kehadiran' ? 'selected' : '' }}>
                        Laporan Kehadiran
                    </option>

                    <option value="pembayaran"
                        {{ $jenis === 'pembayaran' ? 'selected' : '' }}>
                        Laporan Pembayaran
                    </option>

                    <option value="pendaftaran"
                        {{ $jenis === 'pendaftaran' ? 'selected' : '' }}>
                        Laporan Pendaftaran
                    </option>

                    <option value="kamar"
                        {{ $jenis === 'kamar' ? 'selected' : '' }}>
                        Laporan Data Kamar
                    </option>
                </select>
            </div>

            <button type="submit" class="btn-tampilkan">
                Tampilkan Laporan
            </button>
        </form>
    </section>

    {{-- DAFTAR JENIS LAPORAN --}}
    <section class="laporan-list">

        @forelse ($laporan as $item)
            <article class="laporan-card">

                <div class="laporan-icon" aria-hidden="true">
                    @if ($item['jenis'] === 'kehadiran')
                        ♧
                    @elseif ($item['jenis'] === 'pembayaran')
                        ▣
                    @elseif ($item['jenis'] === 'pendaftaran')
                        ▤
                    @else
                        ▦
                    @endif
                </div>

                <div class="laporan-info">
                    <h2>{{ $item['judul'] }}</h2>

                    <p>{{ $item['deskripsi'] }}</p>

                    <div class="laporan-actions">
                        <a href="{{ $item['url'] }}" class="btn-lihat">
                            Lihat Laporan
                        </a>
                    </div>
                </div>

            </article>
        @empty
            <div class="laporan-empty">
                Tidak ada jenis laporan yang tersedia.
            </div>
        @endforelse

    </section>

</div>

@endsection