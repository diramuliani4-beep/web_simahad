
@extends('layouts.musyrifah')

@section('title', 'Pembayaran')

@section('content')

<style>
    .musyrifah-pembayaran {
        width: 100%;
        font-family: 'Poppins', Arial, sans-serif;
        color: #111;
    }

    .musyrifah-pembayaran * {
        box-sizing: border-box;
    }

    /* JUDUL HALAMAN */

    .pembayaran-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 48px;
    }

    .pembayaran-back {
        display: inline-flex;
        align-items: center;
        color: #111;
        font-size: 34px;
        line-height: 1;
        text-decoration: none;
        flex-shrink: 0;
    }

    .pembayaran-back:hover {
        color: #800d22;
    }

    .pembayaran-title {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        line-height: 1.3;
    }

    /* KOLOM PENCARIAN */

    .pembayaran-search-wrap {
        width: 100%;
        max-width: 620px;
        margin: 0 auto 114px;
    }

    .pembayaran-search {
        display: flex;
        align-items: center;
        gap: 15px;
        height: 52px;
        width: 100%;
        padding: 0 20px;
        border-radius: 13px;
        background: #f5e6e9;
    }

    .pembayaran-search svg {
        width: 25px;
        height: 25px;
        flex-shrink: 0;
    }

    .pembayaran-search input {
        width: 100%;
        height: 100%;
        padding: 0;
        border: none;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-size: 19px;
        color: #111;
    }

    .pembayaran-search input::placeholder {
        color: #111;
        opacity: 1;
    }

    /* TABEL PEMBAYARAN */

    .pembayaran-table-wrap {
        width: 100%;
        overflow-x: auto;
        border-radius: 18px;
        box-shadow: 0 3px 4px rgba(0, 0, 0, .2);
    }

    .pembayaran-table {
        width: 100%;
        min-width: 760px;
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        border-radius: 18px;
        background: #f5e6e9;
    }

    .pembayaran-table thead th {
        padding: 8px 10px;
        background: #650019;
        color: #fff;
        font-size: 19px;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
    }

    .pembayaran-table thead th:first-child {
        border-top-left-radius: 15px;
    }

    .pembayaran-table thead th:last-child {
        border-top-right-radius: 15px;
    }

    .pembayaran-table tbody td {
        height: 64px;
        padding: 12px 10px;
        text-align: center;
        vertical-align: middle;
        font-size: 19px;
        border-right: 1px solid rgba(80, 50, 50, .22);
        border-bottom: 1px solid rgba(80, 50, 50, .22);
        overflow-wrap: anywhere;
    }

    .pembayaran-table tbody td:last-child {
        border-right: none;
    }

    .pembayaran-table tbody tr:last-child td {
        border-bottom: none;
    }

    .pembayaran-table tbody tr:hover {
        background: #f0dce1;
    }

    .pembayaran-table th:nth-child(1),
    .pembayaran-table td:nth-child(1) {
        width: 8%;
    }

    .pembayaran-table th:nth-child(2),
    .pembayaran-table td:nth-child(2) {
        width: 20%;
    }

    .pembayaran-table th:nth-child(3),
    .pembayaran-table td:nth-child(3) {
        width: 17%;
    }

    .pembayaran-table th:nth-child(4),
    .pembayaran-table td:nth-child(4) {
        width: 18%;
    }

    .pembayaran-table th:nth-child(5),
    .pembayaran-table td:nth-child(5) {
        width: 18%;
    }

    .pembayaran-table th:nth-child(6),
    .pembayaran-table td:nth-child(6) {
        width: 19%;
    }

    .pembayaran-detail {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 7px;
        color: #111;
        text-decoration: none;
        transition: background .2s, color .2s;
    }

    .pembayaran-detail:hover {
        background: #800d22;
        color: #fff;
    }

    .pembayaran-empty {
        display: none;
        padding: 25px;
        text-align: center;
        color: #777;
        background: #fff;
    }

    /* RESPONSIVE */

    @media (max-width: 768px) {
        .pembayaran-heading {
            gap: 8px;
            margin-bottom: 35px;
        }

        .pembayaran-title {
            font-size: 25px;
        }

        .pembayaran-back {
            font-size: 28px;
        }

        .pembayaran-search-wrap {
            margin-bottom: 65px;
        }

        .pembayaran-search {
            height: 48px;
            padding: 0 14px;
        }

        .pembayaran-search input {
            font-size: 16px;
        }

        .pembayaran-table thead th,
        .pembayaran-table tbody td {
            font-size: 16px;
        }

        .pembayaran-table tbody td {
            height: 58px;
            padding: 10px 8px;
        }
    }

    @media (max-width: 420px) {
        .pembayaran-title {
            font-size: 21px;
        }

        .pembayaran-search-wrap {
            margin-bottom: 45px;
        }
    }
</style>

<div class="musyrifah-pembayaran">

    <!-- JUDUL DAN TOMBOL KEMBALI -->
    <div class="pembayaran-heading">

        <a
            href="{{ route('musyrifah.dashboard') }}"
            class="pembayaran-back"
            aria-label="Kembali ke Dashboard"
            title="Kembali ke Dashboard"
        >
            ←
        </a>

        <h1 class="pembayaran-title">Pembayaran</h1>

    </div>

    <!-- PENCARIAN MAHASISWA -->
    <div class="pembayaran-search-wrap">

        <label class="pembayaran-search">

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
                id="cariPembayaran"
                placeholder="Cari Mahasiswa"
                aria-label="Cari berdasarkan nama, NIM, tagihan, atau status"
            >

        </label>

    </div>

    <!-- TABEL DATA PEMBAYARAN -->
    <div class="pembayaran-table-wrap">

        <table class="pembayaran-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Tagihan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody id="daftarPembayaran">

                @forelse ($daftarPembayaran as $index => $pembayaran)

                    <tr class="baris-pembayaran">

                        <td>{{ $index + 1 }}</td>

                        <td>{{ $pembayaran['nama'] }}</td>

                        <td>{{ $pembayaran['nim'] }}</td>

                        <td>
                            Rp. {{ number_format($pembayaran['tagihan'], 0, ',', '.') }}
                        </td>

                        <td>{{ $pembayaran['status'] }}</td>

                        <td>
                            <a
                                href="#"
                                class="pembayaran-detail"
                                onclick="event.preventDefault(); alert('Halaman detail pembayaran akan dibuat pada tahap berikutnya.');"
                            >
                                Detail
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">Belum ada data pembayaran.</td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="pembayaran-empty" id="pembayaranTidakDitemukan">
            Data pembayaran tidak ditemukan.
        </div>

    </div>

</div>

<!-- FITUR PENCARIAN -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputCari = document.getElementById('cariPembayaran');
        const barisPembayaran = document.querySelectorAll('.baris-pembayaran');
        const pesanKosong = document.getElementById('pembayaranTidakDitemukan');

        inputCari.addEventListener('input', function () {
            const kataKunci = this.value.trim().toLowerCase();
            let jumlahDitemukan = 0;

            barisPembayaran.forEach(function (baris) {
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