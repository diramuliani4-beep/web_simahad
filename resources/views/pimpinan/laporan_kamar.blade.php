
@extends('layouts.pimpinan')

@section('title', 'Laporan Kamar')

@section('content')
<style>
    .laporan-page {
        font-family: 'Poppins', sans-serif;
        color: #29252a;
        padding: 8px 0 28px;
    }

    .laporan-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .laporan-heading h1 {
        margin: 0 0 7px;
        font-size: 26px;
        font-weight: 700;
        color: #780d20;
    }

    .laporan-heading p {
        margin: 0;
        color: #81777d;
        font-size: 13px;
    }

    .tanggal {
        padding: 10px 14px;
        border: 1px solid #eadde0;
        border-radius: 10px;
        color: #780d20;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
    }

    /* Statistik */
    .statistik-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .statistik-card {
        display: flex;
        align-items: center;
        gap: 17px;
        padding: 22px;
        border: 1px solid #eee7e9;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(50,20,30,.04);
        min-width: 0;
    }

    .statistik-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        border-radius: 12px;
        background: #f7e9ec;
        color: #780d20;
    }

    .statistik-icon svg {
        width: 25px;
        height: 25px;
    }

    .statistik-card h3 {
        margin: 0 0 5px;
        color: #81777d;
        font-size: 13px;
        font-weight: 500;
    }

    .statistik-card h2 {
        margin: 0;
        color: #780d20;
        font-size: 25px;
        font-weight: 700;
    }

    /* Tabel */
    .tabel-container {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding: 22px;
        border: 1px solid #eee7e9;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(50,20,30,.04);
        overflow: hidden;
    }

    .tabel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }

    .tabel-header h2 {
        margin: 0;
        color: #30272b;
        font-size: 17px;
        font-weight: 700;
    }

    .form-cari {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 290px;
        max-width: 100%;
        box-sizing: border-box;
        padding: 9px 12px;
        border: 1px solid #e8dfe2;
        border-radius: 9px;
        background: #fff;
    }

    .form-cari:focus-within {
        border-color: #780d20;
    }

    .form-cari svg {
        width: 18px;
        height: 18px;
        color: #96878d;
        flex-shrink: 0;
    }

    .form-cari input {
        width: 100%;
        min-width: 0;
        border: none;
        outline: none;
        color: #30272b;
        font-family: inherit;
        font-size: 12px;
        background: transparent;
    }

    .table-responsive {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }

    .tabel-kamar {
        width: 100%;
        min-width: 650px;
        border-collapse: collapse;
    }

    .tabel-kamar thead {
        background: #f8f0f2;
    }

    .tabel-kamar th {
        padding: 14px 10px;
        color: #780d20;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        border-bottom: 1px solid #eee1e4;
    }

    .tabel-kamar td {
        padding: 14px 10px;
        color: #51474c;
        font-size: 12px;
        border-bottom: 1px solid #f1ebed;
        white-space: nowrap;
    }

    .tabel-kamar tbody tr:hover {
        background: #fffafb;
    }

    .nomor {
        color: #a3969b !important;
        text-align: center;
    }

    .nama-kamar {
        color: #780d20 !important;
        font-weight: 600;
    }

    .badge-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-terisi {
        background: #eaf5e6;
        color: #4e8c35;
    }

    .status-kosong {
        background: #fbe9e9;
        color: #c0392b;
    }

    .status-penuh {
        background: #e8efff;
        color: #3458a5;
    }

    .kosong {
        padding: 35px !important;
        text-align: center;
        color: #91858a !important;
    }

    /* Tombol */
    .aksi-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 22px;
    }

    .btn-aksi {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 17px;
        border: 1px solid #780d20;
        border-radius: 9px;
        color: #780d20;
        background: #fff;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: .2s;
    }

    .btn-aksi:hover {
        color: #fff;
        background: #780d20;
        text-decoration: none;
    }

    @media(max-width: 900px) {
        .statistik-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 650px) {
        .laporan-heading h1 {
            font-size: 22px;
        }

        .statistik-grid {
            grid-template-columns: 1fr;
        }

        .tabel-container {
            padding: 12px;
        }

        .tabel-header {
            align-items: stretch;
        }

        .form-cari {
            width: 100%;
        }

        .aksi-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-aksi {
            width: 100%;
            box-sizing: border-box;
        }
    }

    /* Cetak hanya tabel */
    @media print {
        @page {
            size: landscape;
            margin: 1cm;
        }

        body * {
            visibility: hidden !important;
        }

        #area-laporan,
        #area-laporan * {
            visibility: visible !important;
        }

        #area-laporan {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            overflow: visible;
        }

        #area-laporan .tabel-kamar {
            width: 100%;
            min-width: 0;
            border-collapse: collapse;
        }

        #area-laporan th,
        #area-laporan td {
            border: 1px solid #333;
            padding: 8px;
            color: #000;
        }

        #area-laporan th {
            background: #eee !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="laporan-page">

    {{-- Judul halaman --}}
    <div class="laporan-heading">
        <div>
            <h1>Laporan Kamar</h1>
            <p>Informasi lengkap data kamar Ma’had.</p>
        </div>

        <div class="tanggal">
            <i class="far fa-calendar-alt"></i>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- Statistik kamar --}}
    <div class="statistik-grid">

        <div class="statistik-card">
            <div class="statistik-icon">
                <i class="fas fa-door-open fa-lg"></i>
            </div>
            <div>
                <h3>Total Kamar</h3>
                <h2>{{ $totalKamar }}</h2>
            </div>
        </div>

        <div class="statistik-card">
            <div class="statistik-icon">
                <i class="fas fa-bed fa-lg"></i>
            </div>
            <div>
                <h3>Kamar Terisi</h3>
                <h2>{{ $kamarTerisi }}</h2>
            </div>
        </div>

        <div class="statistik-card">
            <div class="statistik-icon">
                <i class="fas fa-door-closed fa-lg"></i>
            </div>
            <div>
                <h3>Kamar Kosong</h3>
                <h2>{{ $kamarKosong }}</h2>
            </div>
        </div>

    </div>

    {{-- Tabel kamar --}}
    <div class="tabel-container">

        <div class="tabel-header">
            <h2>Data Seluruh Kamar</h2>

            <form action="{{ route('pimpinan.laporan-kamar') }}"
                  method="GET"
                  class="form-cari no-print">

                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>

                <input type="text"
                       name="cari"
                       value="{{ $keyword ?? '' }}"
                       placeholder="Cari nomor kamar atau lantai...">
            </form>
        </div>

        <div id="area-laporan">
            <div class="table-responsive">
                <table class="tabel-kamar">
                    <thead>
                        <tr>
                            <th style="text-align:center;">No</th>
                            <th>Lantai</th>
                            <th>No Kamar</th>
                            <th>Kapasitas</th>
                            <th>Jumlah Penghuni</th>
                            <th>Status Kamar</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($kamar as $index => $item)
                            <tr>
                                <td class="nomor">
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    {{ $item['lantai'] }}
                                </td>

                                <td class="nama-kamar">
                                    {{ $item['nomor_kamar'] }}
                                </td>

                                <td>
                                    {{ $item['kapasitas'] }}
                                </td>

                                <td>
                                    {{ $item['jumlah_penghuni'] }}
                                </td>

                                <td>
                                    @php
                                        $status = $item['status'];
                                    @endphp

                                    <span class="badge-status
                                        {{ $status == 'Kosong' ? 'status-kosong' : ($status == 'Penuh' ? 'status-penuh' : 'status-terisi') }}">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="kosong">
                                    <i class="fas fa-search"
                                       style="font-size:25px; margin-bottom:10px;"></i>
                                    <br>
                                    Data kamar tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tombol bawah tabel --}}
        <div class="aksi-footer no-print">

            <button type="button"
                    onclick="unduhLaporan()"
                    class="btn-aksi">
                <i class="fas fa-download"></i>
                Unduh Laporan
            </button>

            <a href="{{ route('pimpinan.laporan-mahad') }}"
               class="btn-aksi">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

        </div>

    </div>
</div>

<script>
    function unduhLaporan() {
        window.print();
    }
</script>
@endsection
