
@extends('layouts.musyrifah')

@section('title', 'Surat & Perizinan')

@section('content')

<style>
    .izin-page {
        width: 100%;
        color: #080808;
    }

    .izin-heading {
        margin: 0 20px 62px;
    }

    .izin-heading h1 {
        font-size: 30px;
        line-height: 1.4;
        font-weight: 700;
        color: #080808;
    }

    .izin-table-wrapper {
        width: 100%;
        overflow-x: auto;
        background: #f5e6e9;
        border-radius: 0 0 25px 25px;
        box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
    }

    .izin-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
        table-layout: auto;
    }

    .izin-table thead {
        background: #590018;
        color: #fff;
    }

    .izin-table th {
        padding: 8px 12px;
        font-size: 20px;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
    }

    .izin-table td {
        height: 78px;
        padding: 12px 15px;
        font-size: 19px;
        text-align: center;
        border-right: 1px solid #c5b4b8;
        border-bottom: 1px solid #b8a6aa;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        white-space: nowrap;
    }

    .izin-table td:nth-child(2) {
        text-align: left;
    }

    .izin-table th:last-child,
    .izin-table td:last-child {
        border-right: none;
    }

    .izin-table tbody tr:last-child td {
        border-bottom: none;
    }

    .izin-table tbody tr:hover {
        background: #eed7dd;
    }

    .izin-detail {
        padding: 5px 8px;
        color: #111;
        font-size: 18px;
        text-decoration: none;
        border-radius: 5px;
        transition: 0.2s;
    }

    .izin-detail:hover {
        color: #800d22;
        text-decoration: underline;
    }

    @media (max-width: 1100px) {
        .izin-heading {
            margin: 0 10px 45px;
        }

        .izin-table th {
            font-size: 18px;
        }

        .izin-table td {
            font-size: 17px;
        }
    }

    @media (max-width: 768px) {
        .izin-heading {
            margin: 0 5px 35px;
        }

        .izin-heading h1 {
            font-size: 24px;
        }

        .izin-table th {
            font-size: 16px;
        }

        .izin-table td {
            height: 65px;
            padding: 10px;
            font-size: 15px;
        }

        .izin-detail {
            font-size: 15px;
        }
    }
</style>

<div class="izin-page">

    <section class="izin-heading">
        <h1>← Pengajuan Perizinan</h1>
    </section>

    <section class="izin-table-wrapper">

        <table class="izin-table">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Jenis</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($pengajuan as $izin)
                    <tr>
                        <td>{{ $izin['no'] }}</td>
                        <td>{{ $izin['nama'] }}</td>
                        <td>{{ $izin['nim'] }}</td>
                        <td>{{ $izin['jenis'] }}</td>
                        <td>{{ $izin['periode'] }}</td>
                        <td>{{ $izin['status'] }}</td>
                        <td>
                            <a href="#"
                               class="izin-detail"
                               aria-label="Detail pengajuan {{ $izin['nama'] }}">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            Belum ada pengajuan perizinan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </section>

</div>

@endsection