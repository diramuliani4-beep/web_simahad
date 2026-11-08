@extends('layouts.musyrifah')

@section('title', 'Surat & Perizinan')

@section('content')

<style>
    .izin-page {
        width: 100%;
        color: #080808;
    }

    .izin-heading {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin: 0 20px 26px;
    }

    .izin-heading h1 {
        font-size: 30px;
        line-height: 1.35;
        font-weight: 700;
        color: #080808;
    }

    .izin-heading p {
        margin-top: 5px;
        font-size: 14px;
        color: #555;
    }

    .izin-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border: 1px solid #800d22;
        border-radius: 999px;
        background: #fff;
        color: #800d22;
        font-size: 13px;
        font-weight: 700;
    }

    .izin-alert {
        margin: 0 20px 18px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
    }

    .izin-alert--sukses {
        background: #eaf6ee;
        border: 1px solid #2f7d4f;
        color: #1c5334;
    }

    .izin-alert--gagal {
        background: #fdeff1;
        border: 1px solid #800d22;
        color: #800d22;
    }

    .izin-filter {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
        margin: 0 20px 22px;
        padding: 18px;
        border: 1px solid #d9c2c8;
        border-radius: 14px;
        background: #fff;
    }

    .izin-filter label {
        display: block;
        margin-bottom: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: #374151;
    }

    .izin-filter input,
    .izin-filter select {
        width: 100%;
        padding: 9px 11px;
        border: 1px solid #cdb4ba;
        border-radius: 9px;
        font-family: inherit;
        font-size: 13.5px;
        background: #fff;
    }

    .izin-filter input:focus,
    .izin-filter select:focus {
        outline: none;
        border-color: #800d22;
        box-shadow: 0 0 0 3px rgba(128, 13, 34, .12);
    }

    .izin-filter__aksi {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }

    .izin-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 16px;
        border: 1px solid #800d22;
        border-radius: 9px;
        background: #fff;
        color: #800d22;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }

    .izin-btn--primary {
        background: #800d22;
        color: #fff;
    }

    .izin-btn--gold {
        border-color: #C9A96A;
        background: #C9A96A;
        color: #4a1720;
    }

    .izin-section-title {
        margin: 0 20px 12px;
        font-size: 18px;
        font-weight: 700;
        color: #800d22;
    }

    .izin-table-wrapper {
        width: 100%;
        margin: 0 20px 26px;
        overflow-x: auto;
        background: #fff;
        border: 1px solid #d9c2c8;
        border-radius: 14px;
    }

    .izin-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .izin-table thead {
        background: #800d22;
        color: #fff;
    }

    .izin-table th {
        padding: 11px 12px;
        font-size: 13.5px;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }

    .izin-table td {
        padding: 11px 12px;
        font-size: 13.5px;
        text-align: left;
        border-bottom: 1px solid #eddde1;
        vertical-align: middle;
    }

    .izin-table tbody tr:last-child td {
        border-bottom: none;
    }

    .izin-table tbody tr:hover {
        background: #fdf6f7;
    }

    .izin-badge {
        display: inline-block;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .izin-badge--menunggu { background: #fdf1d8; color: #8a5a00; border: 1px solid #e0b658; }
    .izin-badge--disetujui { background: #e7f6ec; color: #1e6b3a; border: 1px solid #7fbf95; }
    .izin-badge--ditolak { background: #fdeaea; color: #a01c2c; border: 1px solid #e2969f; }
    .izin-badge--perbaikan { background: #fdf1d8; color: #8a4b00; border: 1px solid #e0a558; }
    .izin-badge--selesai { background: #e8eefb; color: #22457f; border: 1px solid #93a9d6; }
    .izin-badge--dibatalkan { background: #eef0f3; color: #4b5563; border: 1px solid #b9c0ca; }

    .izin-detail {
        display: inline-block;
        padding: 5px 12px;
        border: 1px solid #800d22;
        border-radius: 8px;
        color: #800d22;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .izin-detail:hover {
        background: #800d22;
        color: #fff;
    }

    .izin-empty {
        text-align: center;
        color: #6b7280;
        padding: 22px 12px;
    }

    .izin-kuota-penuh {
        color: #a01c2c;
        font-weight: 700;
    }

    @media (max-width: 1200px) {
        .izin-filter {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .izin-heading,
        .izin-filter,
        .izin-table-wrapper,
        .izin-section-title,
        .izin-alert {
            margin-left: 0;
            margin-right: 0;
        }

        .izin-heading h1 {
            font-size: 24px;
        }

        .izin-filter {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="izin-page">

    <section class="izin-heading">
        <div>
            <h1>Surat &amp; Perizinan</h1>
            <p>Periksa pengajuan izin kegiatan, izin pulang mingguan, dan surat pulang semester mahasiswa.</p>
        </div>
        <span class="izin-chip">
            {{ $jumlahMenunggu }} pengajuan menunggu tindakan
        </span>
    </section>

    @if (session('success'))
        <div class="izin-alert izin-alert--sukses">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="izin-alert izin-alert--gagal">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="izin-alert izin-alert--gagal">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('musyrifah.surat_perizinan') }}" class="izin-filter">
        <div>
            <label for="filterJenis">Jenis Izin</label>
            <select id="filterJenis" name="jenis">
                <option value="">Semua jenis</option>
                @foreach ($jenisPilihan as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(($filter['jenis'] ?? '') === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="filterStatus">Status</label>
            <select id="filterStatus" name="status">
                <option value="">Semua status</option>
                @foreach ($statusPilihan as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(($filter['status'] ?? '') === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="filterNama">Nama / NIM Mahasiswa</label>
            <input type="text" id="filterNama" name="nama" value="{{ $filter['nama'] ?? '' }}" placeholder="Cari nama atau NIM">
        </div>

        <div>
            <label for="filterDari">Periode Mulai</label>
            <input type="date" id="filterDari" name="periode_dari" value="{{ $filter['periode_dari'] ?? '' }}">
        </div>

        <div>
            <label for="filterSampai">Periode Sampai</label>
            <input type="date" id="filterSampai" name="periode_sampai" value="{{ $filter['periode_sampai'] ?? '' }}">
        </div>

        <div class="izin-filter__aksi" style="grid-column: 1 / -1;">
            <button type="submit" class="izin-btn izin-btn--primary">
                Terapkan Filter
            </button>
            <a href="{{ route('musyrifah.surat_perizinan') }}" class="izin-btn">Reset</a>
        </div>
    </form>

    <h2 class="izin-section-title">Monitoring Kuota Izin Pulang &mdash; {{ $bulanBerjalan }}</h2>

    <div class="izin-table-wrapper">
        <table class="izin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Terpakai</th>
                    <th>Sisa Kuota</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kuotaMonitoring as $indeks => $baris)
                    <tr>
                        <td>{{ $indeks + 1 }}</td>
                        <td>{{ $baris['nama'] }}</td>
                        <td>{{ $baris['nim'] }}</td>
                        <td>{{ $baris['terpakai'] }} / {{ \App\Models\SuratPerizinan::KUOTA_PULANG_PER_BULAN }}</td>
                        <td>
                            @if ($baris['sisa'] > 0)
                                {{ $baris['sisa'] }} slot
                            @else
                                <span class="izin-kuota-penuh">Kuota habis</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="izin-empty">Belum ada pemakaian kuota izin pulang pada bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="izin-section-title">Daftar Pengajuan</h2>

    <div class="izin-table-wrapper">
        <table class="izin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Jenis</th>
                    <th>Tanggal Ajuan</th>
                    <th>Periode</th>
                    <th>Tujuan / Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengajuans as $indeks => $izin)
                    <tr>
                        <td>{{ $indeks + 1 }}</td>
                        <td>{{ $izin->user?->name ?? '-' }}</td>
                        <td>{{ $izin->user?->nim ?: '-' }}</td>
                        <td>{{ $izin->labelJenis() }}</td>
                        <td>{{ $izin->created_at?->locale('id')->translatedFormat('d M Y') }}</td>
                        <td>{{ $izin->periodeTeks() }}</td>
                        <td>{{ $izin->tujuanTeks() }}</td>
                        <td>
                            <span class="izin-badge izin-badge--{{ $izin->kelasStatus() }}">
                                {{ $izin->labelStatus() }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('musyrifah.surat_perizinan.show', $izin) }}" class="izin-detail">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="izin-empty">Belum ada pengajuan perizinan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
