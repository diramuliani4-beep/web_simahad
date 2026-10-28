
@extends('layouts.musyrifah')

@section('title', 'Pendaftaran Mahasiswa')

@section('content')

<style>
    .pendaftaran-page {
        font-family: 'Poppins', Arial, sans-serif;
        color: #111;
        width: 100%;
    }

    .pendaftaran-page * {
        box-sizing: border-box;
    }

    /* JUDUL HALAMAN */

    .pendaftaran-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 48px;
    }

    .pendaftaran-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #111;
        font-size: 34px;
        line-height: 1;
        text-decoration: none;
        flex-shrink: 0;
    }

    .pendaftaran-back:hover {
        color: #800d22;
    }

    .pendaftaran-title {
        font-size: 32px;
        font-weight: 700;
        line-height: 1.3;
        margin: 0;
    }

    /* PENCARIAN */

    .pendaftaran-search-wrap {
        max-width: 620px;
        margin: 0 auto 115px;
    }

    .pendaftaran-search {
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
        height: 52px;
        padding: 0 20px;
        background: #f5e6e9;
        border-radius: 13px;
    }

    .pendaftaran-search svg {
        width: 25px;
        height: 25px;
        flex-shrink: 0;
        color: #111;
    }

    .pendaftaran-search input {
        width: 100%;
        height: 100%;
        border: none;
        outline: none;
        background: transparent;
        color: #111;
        font-family: inherit;
        font-size: 19px;
    }

    .pendaftaran-search input::placeholder {
        color: #111;
        opacity: 1;
    }

    /* TABEL PENDAFTARAN */

    .pendaftaran-table-wrap {
        width: 100%;
        overflow-x: auto;
        border-radius: 18px;
        box-shadow: 0 3px 4px rgba(0, 0, 0, .17);
    }

    .pendaftaran-table {
        width: 100%;
        min-width: 650px;
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        background: #f5e6e9;
        border-radius: 18px;
    }

    .pendaftaran-table thead th {
        padding: 8px 12px;
        background: #650019;
        color: #fff;
        font-size: 19px;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
    }

    .pendaftaran-table thead th:first-child {
        border-top-left-radius: 15px;
    }

    .pendaftaran-table thead th:last-child {
        border-top-right-radius: 15px;
    }

    .pendaftaran-table tbody td {
        padding: 16px 12px;
        height: 73px;
        text-align: center;
        vertical-align: middle;
        font-size: 19px;
        border-right: 1px solid rgba(80, 50, 50, .22);
        border-bottom: 1px solid rgba(80, 50, 50, .35);
        box-shadow: 0 2px 3px rgba(0, 0, 0, .04);
    }

    .pendaftaran-table tbody td:last-child {
        border-right: none;
    }

    .pendaftaran-table tbody tr:last-child td {
        border-bottom: none;
    }

    .pendaftaran-table tbody tr:hover {
        background: #f0dce1;
    }

    .pendaftaran-table th:nth-child(1),
    .pendaftaran-table td:nth-child(1) {
        width: 8%;
    }

    .pendaftaran-table th:nth-child(2),
    .pendaftaran-table td:nth-child(2) {
        width: 20%;
    }

    .pendaftaran-table th:nth-child(3),
    .pendaftaran-table td:nth-child(3) {
        width: 20%;
    }

    .pendaftaran-table th:nth-child(4),
    .pendaftaran-table td:nth-child(4) {
        width: 24%;
    }

    .pendaftaran-table th:nth-child(5),
    .pendaftaran-table td:nth-child(5) {
        width: 28%;
    }

    .pendaftaran-detail {
        display: inline-block;
        padding: 5px 12px;
        color: #111;
        text-decoration: none;
        border-radius: 7px;
        transition: background .2s, color .2s;
    }

    .pendaftaran-detail:hover {
        background: #800d22;
        color: #fff;
    }

    .pendaftaran-empty {
        display: none;
        padding: 25px;
        text-align: center;
        color: #777;
    }

    /* RESPONSIVE */

    @media (max-width: 768px) {
        .pendaftaran-heading {
            gap: 8px;
            margin-bottom: 35px;
        }

        .pendaftaran-title {
            font-size: 25px;
        }

        .pendaftaran-back {
            font-size: 28px;
        }

        .pendaftaran-search-wrap {
            margin-bottom: 65px;
        }

        .pendaftaran-search {
            height: 48px;
            padding: 0 14px;
        }

        .pendaftaran-search input {
            font-size: 16px;
        }

        .pendaftaran-table thead th {
            font-size: 16px;
        }

        .pendaftaran-table tbody td {
            height: 60px;
            padding: 12px 8px;
            font-size: 16px;
        }
    }

    @media (max-width: 420px) {
        .pendaftaran-title {
            font-size: 21px;
        }

        .pendaftaran-search-wrap {
            margin-bottom: 45px;
        }
    }
</style>

<div class="pendaftaran-page">

    <!-- JUDUL DAN TOMBOL KEMBALI -->
    <div class="pendaftaran-heading">

        <a
            href="{{ route('musyrifah.dashboard') }}"
            class="pendaftaran-back"
            aria-label="Kembali ke dashboard"
            title="Kembali ke Dashboard"
        >
            ←
        </a>

        <h1 class="pendaftaran-title">
            Pendaftaran Mahasiswa
        </h1>

    </div>

    <!-- PENCARIAN MAHASISWA -->
    <div class="pendaftaran-search-wrap">

        <label class="pendaftaran-search">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.3"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m16 16 4 4"></path>
            </svg>

            <input
                type="search"
                id="cariMahasiswa"
                placeholder="Cari Mahasiswa"
                aria-label="Cari mahasiswa berdasarkan nama, NIM, atau status"
            >

        </label>

    </div>

    <!-- TABEL PENDAFTARAN -->
    <div class="pendaftaran-table-wrap">

        <table class="pendaftaran-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody id="daftarPendaftar">

                @forelse ($daftarPendaftar as $index => $pendaftar)

                    <tr class="baris-pendaftar">

                        <td>{{ $index + 1 }}</td>

                        <td>{{ $pendaftar['nama'] }}</td>

                        <td>{{ $pendaftar['nim'] }}</td>

                        <td>{{ $pendaftar['status'] }}</td>

                        <td>
                            <a
                                href="#"
                                class="pendaftaran-detail"
                                onclick="event.preventDefault(); alert('Halaman detail pendaftar akan dibuat pada tahap berikutnya.');"
                            >
                                Detail
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5">Belum ada data pendaftaran.</td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="pendaftaran-empty" id="hasilTidakDitemukan">
            Mahasiswa yang dicari tidak ditemukan.
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputCari = document.getElementById('cariMahasiswa');
        const barisPendaftar = document.querySelectorAll('.baris-pendaftar');
        const pesanKosong = document.getElementById('hasilTidakDitemukan');

        inputCari.addEventListener('input', function () {
            const kataKunci = this.value.trim().toLowerCase();
            let jumlahDitemukan = 0;

            barisPendaftar.forEach(function (baris) {
                const teksBaris = baris.textContent.toLowerCase();
                const cocok = teksBaris.includes(kataKunci);

                baris.style.display = cocok ? '' : 'none';

                if (cocok) {
                    jumlahDitemukan++;
                }
            });

            pesanKosong.style.display =
                jumlahDitemukan === 0 ? 'block' : 'none';
        });
    });
</script>

@endsection