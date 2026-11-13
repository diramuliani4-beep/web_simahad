@extends('layouts.musyrifah')

@section('title', 'Pembayaran Mahasiswa')

@section('content')

<style>
    .mp {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--ink);
        min-width: 0;
    }

    .mp *,
    .mp *::before,
    .mp *::after {
        box-sizing: border-box;
    }

    .mp .page-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
    }

    .mp .page-head h1 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 800;
    }

    .mp .page-head p {
        margin: 4px 0 0;
        font-size: 0.8rem;
        line-height: 1.6;
        color: var(--muted);
    }

    .mp .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 38px;
        padding: 8px 14px;
        border: 1px solid var(--maroon);
        border-radius: 9px;
        background: var(--maroon);
        color: #fff;
        font-family: inherit;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
    }

    .mp .btn:hover {
        background: var(--maroon-dark);
    }

    .mp .btn--garis {
        background: #fff;
        color: var(--maroon);
    }

    .mp .btn--garis:hover {
        background: var(--maroon-tint);
    }

    .mp .btn--hijau {
        border-color: #15803d;
        background: #15803d;
    }

    .mp .btn--hijau:hover {
        background: #166534;
    }

    .mp .btn--merah {
        border-color: #b91c1c;
        background: #b91c1c;
    }

    .mp .btn--merah:hover {
        background: #991b1b;
    }

    .mp .btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .mp .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }

    .mp .stat {
        min-width: 0;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
    }

    .mp .stat .label {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .mp .stat .nilai {
        display: block;
        margin-top: 4px;
        font-size: 1.1rem;
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .mp .stat--maroon {
        border-color: #e3c9cf;
        background: linear-gradient(135deg, #fff, var(--maroon-tint));
    }

    .mp .stat--maroon .nilai {
        color: var(--maroon);
    }

    .mp .panel {
        min-width: 0;
        margin-bottom: 14px;
        padding: 14px 16px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #fff;
    }

    .mp .panel-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .mp .panel-head h2 {
        margin: 0;
        font-size: 0.94rem;
        font-weight: 800;
    }

    .mp .panel-head span {
        font-size: 0.73rem;
        color: var(--muted);
    }

    .mp .toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }

    .mp .toolbar input,
    .mp .toolbar select {
        min-height: 38px;
        padding: 8px 11px;
        border: 1px solid #e0d5d0;
        border-radius: 9px;
        background: #fff;
        font-family: inherit;
        font-size: 0.8rem;
        color: var(--ink);
    }

    .mp .toolbar input {
        flex: 1 1 220px;
    }

    .mp .toolbar select {
        flex: 0 1 180px;
    }

    .mp .tabel-gulir {
        width: 100%;
        overflow-x: auto;
    }

    .mp table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }

    .mp thead th {
        padding: 8px 9px;
        border-bottom: 2px solid var(--line);
        text-align: left;
        font-size: 0.67rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
        white-space: nowrap;
    }

    .mp tbody td {
        padding: 9px;
        border-bottom: 1px solid #f3ebe8;
        vertical-align: top;
    }

    .mp tbody tr:last-child td {
        border-bottom: none;
    }

    .mp .nama {
        display: block;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .mp .sub {
        display: block;
        margin-top: 2px;
        font-size: 0.7rem;
        color: var(--muted);
        overflow-wrap: anywhere;
    }

    .mp .nominal {
        font-weight: 700;
        white-space: nowrap;
    }

    .mp .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.66rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .mp .badge.lunas {
        background: #dcfce7;
        color: #15803d;
    }

    .mp .badge.menunggu {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .mp .badge.rektorat {
        background: #ede9fe;
        color: #5b21b6;
    }

    .mp .badge.ditolak,
    .mp .badge.terlambat {
        background: #fee2e2;
        color: #b91c1c;
    }

    .mp .badge.belum {
        background: #fef3c7;
        color: #92400e;
    }

    .mp .badge.verifikasi-menunggu {
        background: #e0f2fe;
        color: #075985;
    }

    .mp .badge.verifikasi-diterima {
        background: #dcfce7;
        color: #166534;
    }

    .mp .badge.verifikasi-ditolak {
        background: #fee2e2;
        color: #991b1b;
    }

    .mp .badge.verifikasi-kosong {
        background: #f3f4f6;
        color: #4b5563;
    }

    .mp .tautan-bukti {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--maroon);
    }

    .mp .tautan-bukti:hover {
        text-decoration: underline;
    }

    .mp .dua-kolom {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr);
        gap: 14px;
        align-items: start;
    }

    .mp .dua-kolom .panel {
        margin-bottom: 0;
    }

    .mp .jadwal-baris {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 9px 10px;
        padding: 10px 0;
        border-top: 1px dashed var(--line);
    }

    .mp .jadwal-baris:first-of-type {
        border-top: none;
        padding-top: 0;
    }

    .mp .field label {
        display: block;
        margin-bottom: 4px;
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--text-secondary);
    }

    .mp .field input,
    .mp .field textarea {
        width: 100%;
        min-height: 38px;
        padding: 8px 10px;
        border: 1px solid #e0d5d0;
        border-radius: 8px;
        background: #fff;
        font-family: inherit;
        font-size: 0.8rem;
        color: var(--ink);
    }

    .mp .field textarea {
        min-height: 70px;
        resize: vertical;
    }

    .mp .field input:focus,
    .mp .field textarea:focus {
        outline: 2px solid rgba(107, 13, 27, .16);
        border-color: var(--maroon);
    }

    .mp .hint {
        margin-top: 6px;
        font-size: 0.7rem;
        line-height: 1.55;
        color: var(--muted);
    }

    .mp .pengajuan-item {
        padding: 10px 0;
        border-top: 1px dashed var(--line);
    }

    .mp .pengajuan-item:first-of-type {
        border-top: none;
        padding-top: 0;
    }

    .mp .pengajuan-atas {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }

    .mp .pengajuan-nomor {
        font-size: 0.76rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .mp .pengajuan-teks {
        margin-top: 3px;
        font-size: 0.73rem;
        line-height: 1.55;
        color: var(--muted);
        overflow-wrap: anywhere;
    }

    .mp .pengajuan-aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 7px;
    }

    .mp .pengajuan-aksi .btn {
        min-height: 34px;
        padding: 6px 11px;
        font-size: 0.72rem;
    }

    .mp .kosong {
        padding: 16px 4px;
        text-align: center;
        font-size: 0.76rem;
        color: var(--muted);
    }

    /* Panel periode pembayaran */
    .mp .periode-form {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr) auto;
        gap: 9px 10px;
        align-items: end;
        margin-bottom: 8px;
        padding: 10px 12px;
        border: 1px dashed var(--line);
        border-radius: 11px;
        background: #fdfbfa;
    }

    .mp .periode-list {
        display: grid;
        gap: 8px;
    }

    .mp .periode-item {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 9px 11px;
        border: 1px solid var(--border);
        border-radius: 11px;
        background: #fff;
    }

    .mp .periode-item--aktif {
        border-color: #e3c9cf;
        background: linear-gradient(135deg, #fff, var(--maroon-tint));
    }

    .mp .periode-info {
        min-width: 0;
        flex: 1 1 220px;
    }

    .mp .periode-nama {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .mp .periode-aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .mp .periode-aksi .btn {
        min-height: 34px;
        padding: 6px 11px;
        font-size: 0.72rem;
    }

    .mp .badge.dibuka {
        background: #dcfce7;
        color: #15803d;
    }

    .mp .badge.terjadwal {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .mp .badge.ditutup {
        background: #f3f4f6;
        color: #4b5563;
    }

    /* Modal */
    .mp-modal {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: none;
        align-items: flex-end;
        justify-content: center;
        padding: 0;
        background: rgba(33, 10, 14, .48);
    }

    .mp-modal.terbuka {
        display: flex;
    }

    .mp-modal .kotak {
        width: 100%;
        max-width: 640px;
        max-height: 92vh;
        overflow-y: auto;
        overscroll-behavior: contain;
        padding: 18px 18px 22px;
        border-radius: 18px 18px 0 0;
        background: #fff;
    }

    .mp-modal .tutup-baris {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .mp-modal h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
    }

    .mp-modal .tutup {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border: 1px solid var(--line);
        border-radius: 9px;
        background: #fff;
        color: var(--muted);
        cursor: pointer;
        flex-shrink: 0;
    }

    .mp-modal .info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 12px;
        margin-bottom: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #faf7f6;
    }

    .mp-modal .info-label {
        display: block;
        font-size: 0.63rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #9b8a8e;
    }

    .mp-modal .info-nilai {
        display: block;
        margin-top: 2px;
        font-size: 0.78rem;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .mp-modal .seksi {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed var(--line);
    }

    .mp-modal .seksi h4 {
        margin: 0 0 7px;
        font-size: 0.8rem;
        font-weight: 800;
    }

    .mp-modal .aksi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
    }

    .mp-modal .aksi-grid .btn {
        width: 100%;
    }

    .mp-modal .riwayat-baris {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        padding: 6px 0;
        border-bottom: 1px solid #f5eeeb;
        font-size: 0.75rem;
    }

    .mp-modal .riwayat-baris:last-child {
        border-bottom: none;
    }

    .mp-modal .aktivitas {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .mp-modal .aktivitas li {
        padding: 7px 0 7px 14px;
        border-left: 2px solid var(--line);
        font-size: 0.73rem;
        line-height: 1.5;
        color: var(--text-secondary);
        overflow-wrap: anywhere;
    }

    .mp .pesan-json {
        margin-top: 8px;
        padding: 9px 11px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 600;
        line-height: 1.5;
        display: none;
    }

    .mp .pesan-json.sukses {
        display: block;
        background: #eef7f0;
        border: 1px solid #cfe6d5;
        color: #22653a;
    }

    .mp .pesan-json.gagal {
        display: block;
        background: #fdeef0;
        border: 1px solid #f2ccd2;
        color: #8a1224;
    }

    @media (min-width: 641px) {
        .mp-modal {
            align-items: center;
            padding: 20px;
        }

        .mp-modal .kotak {
            border-radius: 16px;
            padding: 20px 22px 24px;
        }
    }

    @media (max-width: 992px) {
        .mp .dua-kolom {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 780px) {
        .mp thead {
            display: none;
        }

        .mp table,
        .mp tbody,
        .mp tr,
        .mp td {
            display: block;
            width: 100%;
        }

        .mp tbody tr {
            margin-bottom: 9px;
            padding: 10px 11px;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fff;
        }

        .mp tbody td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 4px 0;
            border-bottom: none;
            text-align: right;
        }

        .mp tbody td::before {
            content: attr(data-label);
            flex: 0 0 40%;
            text-align: left;
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .mp tbody td[data-label="Mahasiswa"] {
            display: block;
            text-align: left;
        }

        .mp tbody td[data-label="Mahasiswa"]::before {
            display: none;
        }

        .mp .tabel-gulir {
            overflow-x: visible;
        }
    }

    @media (max-width: 640px) {
        .mp .jadwal-baris,
        .mp .periode-form,
        .mp-modal .info-grid,
        .mp-modal .aksi-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .mp .toolbar select {
            flex: 1 1 100%;
        }

        .mp .stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

@php
    $pilihanStatus = [
        'semua' => 'Semua status',
        'menunggu' => 'Menunggu verifikasi bukti',
        'menunggu_konfirmasi' => 'Menunggu konfirmasi Rektorat',
        'belum_lunas' => 'Belum lunas',
        'lunas' => 'Lunas',
    ];

    // Pembukaan dan pengaturan periode hanya untuk Musyrifah.
    $bolehAturPeriode = strtolower(trim((string) auth()->user()?->role)) === 'musyrifah';
@endphp

<div class="mp">
    <div class="page-head">
        <div>
            <h1>Pembayaran Mahasiswa</h1>
            <p>
                Periksa bukti transfer, konfirmasi dana Rektorat, atur jadwal angsuran, dan tindak lanjuti pengajuan keterlambatan.
                @if ($pengaturan && $pengaturan->sudahDibuka())
                    Periode aktif: <strong>{{ $pengaturan->periode }}</strong>.
                @elseif ($pengaturan)
                    Periode <strong>{{ $pengaturan->periode }}</strong> dijadwalkan dibuka {{ $pengaturan->periodeDibukaTeks() }}.
                @else
                    <strong>Belum ada periode pembayaran aktif.</strong>
                @endif
            </p>
        </div>

        <button type="button" class="btn btn--garis" id="btnMuatUlang">Muat Ulang</button>
    </div>

    <div class="stat-grid">
        <div class="stat stat--maroon">
            <span class="label">Total Tagihan</span>
            <span class="nilai">{{ $totalNominalTagihanRupiah }}</span>
        </div>
        <div class="stat">
            <span class="label">Mahasiswa</span>
            <span class="nilai">{{ $totalMahasiswa }}</span>
        </div>
        <div class="stat">
            <span class="label">Menunggu Bukti</span>
            <span class="nilai">{{ $totalMenungguVerifikasi }}</span>
        </div>
        <div class="stat">
            <span class="label">Menunggu Rektorat</span>
            <span class="nilai">{{ $totalMenungguRektorat }}</span>
        </div>
        <div class="stat">
            <span class="label">Lunas</span>
            <span class="nilai">{{ $totalLunas }}</span>
        </div>
        <div class="stat">
            <span class="label">Dana Terkonfirmasi</span>
            <span class="nilai">{{ $totalNominalTerkumpulRupiah }}</span>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Periode Pembayaran</h2>
            <span>Pembukaan dan pengaturan periode adalah kewenangan Musyrifah</span>
        </div>

        @if ($bolehAturPeriode)
            <form class="periode-form" action="{{ route('musyrifah.pembayaran.periode.simpan') }}" method="POST">
                @csrf

                <div class="field">
                    <label for="periodeBaru">Nama periode</label>
                    <input type="text" id="periodeBaru" name="periode" placeholder="Contoh: Januari 2027" maxlength="60" required>
                </div>

                <div class="field">
                    <label for="periodeMulai">Tanggal mulai</label>
                    <input type="date" id="periodeMulai" name="tanggal_mulai" value="{{ now()->toDateString() }}" required>
                </div>

                <div class="field">
                    <button type="submit" class="btn">Buat Periode</button>
                </div>
            </form>

            <div class="hint" style="margin:0 0 10px;">
                Periode baru otomatis menjadi periode aktif. Atur nominal angsuran, jatuh tempo, dan jadwal pengingat pada panel Jadwal Angsuran.
                Hanya satu periode yang dapat dibuka pada satu waktu.
            </div>
        @else
            <div class="hint" style="margin:0 0 10px;">
                Pembukaan dan pengaturan periode pembayaran hanya dapat dilakukan Musyrifah.
            </div>
        @endif

        @if (count($daftarPeriode) > 0)
            <div class="periode-list">
                @foreach ($daftarPeriode as $periode)
                    <div class="periode-item {{ $periode['aktif'] ? 'periode-item--aktif' : '' }}">
                        <div class="periode-info">
                            <span class="periode-nama">
                                {{ $periode['periode'] }}
                                <span class="badge {{ $periode['kelas_status'] }}">{{ $periode['status_label'] }}</span>
                            </span>
                            <span class="sub">
                                Mulai {{ $periode['tanggal_mulai_teks'] }} &middot; {{ $periode['jumlah_tagihan'] }} tagihan
                                @if ($periode['jatuh_tempo_teks'] !== '-')
                                    &middot; jatuh tempo tahap pertama {{ $periode['jatuh_tempo_teks'] }}
                                @endif
                            </span>
                        </div>

                        <div class="periode-aksi">
                            @if ($bolehAturPeriode)
                                @if ($periode['aktif'])
                                    <form action="{{ route('musyrifah.pembayaran.periode.tutup', $periode['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn--garis">Tutup Periode</button>
                                    </form>
                                @else
                                    <form action="{{ route('musyrifah.pembayaran.periode.buka', $periode['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn--hijau">Buka Periode</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="kosong">Belum ada periode pembayaran. Buat periode pertama pada formulir di atas.</div>
        @endif
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Pembayaran</h2>
            <span>Rekening resmi: {{ $rekening['bank'] }} {{ $rekening['nomor'] }} a.n. {{ $rekening['nama'] }}</span>
        </div>

        <div class="toolbar">
            <input type="search" id="cariPembayaran" placeholder="Cari nama atau NIM mahasiswa..." aria-label="Cari nama atau NIM mahasiswa">
            <select id="filterStatus" aria-label="Filter status pembayaran">
                @foreach ($pilihanStatus as $nilai => $label)
                    <option value="{{ $nilai }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="pesan-json" id="pesanDaftar" role="status"></div>

        <div class="tabel-gulir">
            <table>
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Tahap</th>
                        <th>Nominal</th>
                        <th>Jatuh Tempo</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabelPembayaran">
                    @forelse ($daftarPembayaran as $pembayaran)
                        <tr data-cari="{{ strtolower($pembayaran['nama'].' '.$pembayaran['nim']) }}"
                            data-status="{{ $pembayaran['raw_status'] }}">
                            <td data-label="Mahasiswa">
                                <span class="nama">{{ $pembayaran['nama'] }}</span>
                                <span class="sub">{{ $pembayaran['nim'] }} &middot; {{ $pembayaran['periode'] }}</span>
                            </td>
                            <td data-label="Tahap">{{ $pembayaran['tahap'] }}</td>
                            <td data-label="Nominal"><span class="nominal">{{ $pembayaran['nominal_rupiah'] }}</span></td>
                            <td data-label="Jatuh Tempo">
                                {{ $pembayaran['jatuh_tempo'] }}
                                @if ($pembayaran['kelas_status'] === 'terlambat')
                                    <span class="badge terlambat">Lewat</span>
                                @endif
                            </td>
                            <td data-label="Bukti">
                                @if ($pembayaran['bukti_url'])
                                    <a class="tautan-bukti" href="{{ $pembayaran['bukti_url'] }}" target="_blank" rel="noopener">Lihat</a>
                                    <span class="sub">{{ $pembayaran['verifikasi_label'] }}</span>
                                @else
                                    <span class="sub">Belum ada</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                <span class="badge {{ $pembayaran['kelas_status'] }}">{{ $pembayaran['status'] }}</span>
                            </td>
                            <td data-label="Aksi">
                                <button type="button"
                                    class="btn btn--garis"
                                    data-detail="{{ route('musyrifah.pembayaran.detail', $pembayaran['id']) }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="kosong">Belum ada tagihan pembayaran.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="dua-kolom">
        <div class="panel">
            <div class="panel-head">
                <h2>Jadwal Angsuran</h2>
                <span>Total bawaan Rp5.000.000 per tahun</span>
            </div>

            @if ($bolehAturPeriode && $periodeKerja && count($jadwal) > 0)
                <form action="{{ route('musyrifah.pembayaran.jadwal') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pengaturan_id" value="{{ $periodeKerja->id }}">

                    <div class="field" style="margin-bottom:10px;">
                        <label for="jadwalTanggalMulai">Tanggal mulai periode {{ $periodeKerja->periode }}</label>
                        <input type="date"
                            id="jadwalTanggalMulai"
                            name="tanggal_mulai"
                            value="{{ $periodeKerja->periode_dibuka_pada?->toDateString() }}"
                            required>
                    </div>

                    @foreach ($jadwal as $indeks => $baris)
                        <div class="jadwal-baris">
                            <input type="hidden" name="angsuran[{{ $indeks }}][angsuran_ke]" value="{{ $baris['angsuran_ke'] }}">

                            <div class="field">
                                <label for="jadwal-nama-{{ $baris['angsuran_ke'] }}">Nama tahap {{ $baris['angsuran_ke'] }}</label>
                                <input type="text"
                                    id="jadwal-nama-{{ $baris['angsuran_ke'] }}"
                                    name="angsuran[{{ $indeks }}][nama_tahap]"
                                    value="{{ $baris['nama_tahap'] }}"
                                    maxlength="80">
                            </div>

                            <div class="field">
                                <label for="jadwal-nominal-{{ $baris['angsuran_ke'] }}">Nominal (Rp)</label>
                                <input type="number"
                                    id="jadwal-nominal-{{ $baris['angsuran_ke'] }}"
                                    name="angsuran[{{ $indeks }}][nominal]"
                                    value="{{ (int) $baris['nominal'] }}"
                                    min="{{ $baris['angsuran_ke'] === 1 ? 1000000 : 1 }}"
                                    step="1000"
                                    required>
                            </div>

                            <div class="field">
                                <label for="jadwal-tempo-{{ $baris['angsuran_ke'] }}">Jatuh tempo</label>
                                <input type="date"
                                    id="jadwal-tempo-{{ $baris['angsuran_ke'] }}"
                                    name="angsuran[{{ $indeks }}][jatuh_tempo]"
                                    value="{{ $baris['jatuh_tempo'] }}"
                                    required>
                            </div>

                            <div class="field">
                                <label for="jadwal-reminder-{{ $baris['angsuran_ke'] }}">Pengingat (hari sebelum)</label>
                                <input type="number"
                                    id="jadwal-reminder-{{ $baris['angsuran_ke'] }}"
                                    name="angsuran[{{ $indeks }}][reminder_hari]"
                                    value="{{ $baris['reminder_hari'] }}"
                                    min="0"
                                    max="30"
                                    required>
                            </div>
                        </div>
                    @endforeach

                    <div class="hint">
                        Angsuran pertama minimal Rp1.000.000. Total biaya umum Rp5.000.000 per tahun dan dapat berbeda sesuai kategori mahasiswa.
                        Perubahan jadwal diterapkan pada tagihan yang belum dibayar.
                    </div>

                    <div class="pengajuan-aksi" style="margin-top:10px;">
                        <button type="submit" class="btn">Simpan Periode &amp; Jadwal</button>
                    </div>
                </form>
            @elseif (count($jadwal) > 0)
                @foreach ($jadwal as $baris)
                    <div class="jadwal-baris" style="grid-template-columns: minmax(0, 1fr);">
                        <div class="sub">
                            <strong>{{ $baris['nama_tahap'] }}</strong> &middot; {{ $baris['nominal_rupiah'] }} &middot;
                            jatuh tempo {{ $baris['jatuh_tempo_teks'] }} &middot; pengingat {{ $baris['reminder_hari'] }} hari
                        </div>
                    </div>
                @endforeach
                <div class="hint">Jadwal angsuran hanya dapat diubah oleh Musyrifah.</div>
            @else
                <div class="kosong">Belum ada periode pembayaran. Buat periode pada panel Periode Pembayaran terlebih dahulu.</div>
            @endif
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Pengajuan Keterlambatan</h2>
                <span>{{ $totalPengajuan }} menunggu keputusan</span>
            </div>

            <div class="pesan-json" id="pesanPengajuan" role="status"></div>

            @forelse ($pengajuan as $item)
                <div class="pengajuan-item" data-pengajuan="{{ $item['id'] }}">
                    <div class="pengajuan-atas">
                        <span class="pengajuan-nomor">{{ $item['nomor_surat'] }}</span>
                        <span class="badge {{ $item['kelas_status'] === 'disetujui' ? 'lunas' : ($item['kelas_status'] === 'ditolak' ? 'ditolak' : 'menunggu') }}">
                            {{ $item['status_label'] }}
                        </span>
                    </div>
                    <div class="pengajuan-teks">
                        {{ $item['nama'] }} ({{ $item['nim'] }}) &middot; {{ $item['tahap'] }} &middot; {{ $item['nominal_rupiah'] }}<br>
                        Rencana bayar <strong>{{ $item['tanggal_rencana'] }}</strong> &middot; {{ $item['alasan'] }}
                    </div>

                    @if (filled($item['catatan_musyrifah']))
                        <div class="pengajuan-teks">Catatan: {{ $item['catatan_musyrifah'] }}</div>
                    @endif

                    <div class="pengajuan-aksi">
                        <a class="btn btn--garis" href="{{ $item['surat_url'] }}" target="_blank" rel="noopener">Surat PDF</a>

                        @if ($item['menunggu'])
                            <button type="button" class="btn btn--hijau" data-putuskan="setuju" data-id="{{ $item['id'] }}">Setujui</button>
                            <button type="button" class="btn btn--merah" data-putuskan="tolak" data-id="{{ $item['id'] }}">Tolak</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="kosong">Belum ada pengajuan keterlambatan.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="mp-modal" id="modalDetail" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="judulModal">
    <div class="kotak">
        <div class="tutup-baris">
            <div>
                <h3 id="judulModal">Detail Pembayaran</h3>
                <span class="sub" id="mdMahasiswa">-</span>
            </div>
            <button type="button" class="tutup" data-tutup-modal aria-label="Tutup detail">&times;</button>
        </div>

        <div class="pesan-json" id="pesanModal" role="status"></div>

        <div class="info-grid">
            <div>
                <span class="info-label">NIM</span>
                <span class="info-nilai" id="mdNim">-</span>
            </div>
            <div>
                <span class="info-label">Kontak</span>
                <span class="info-nilai" id="mdKontak">-</span>
            </div>
            <div>
                <span class="info-label">Tahap</span>
                <span class="info-nilai" id="mdTahap">-</span>
            </div>
            <div>
                <span class="info-label">Jatuh Tempo</span>
                <span class="info-nilai" id="mdJatuhTempo">-</span>
            </div>
            <div>
                <span class="info-label">Status Tagihan</span>
                <span class="info-nilai" id="mdStatus">-</span>
            </div>
            <div>
                <span class="info-label">Pemeriksaan Bukti</span>
                <span class="info-nilai" id="mdVerifikasi">-</span>
            </div>
        </div>

        <div class="seksi">
            <h4>Nominal &amp; Bukti Transfer</h4>
            <div class="field" style="margin-bottom:8px;">
                <label for="mdNominal">Nominal tagihan (Rp)</label>
                <input type="number" id="mdNominal" min="1" step="1000">
                <div class="hint">Gunakan untuk menyesuaikan nominal sesuai kategori mahasiswa. Angsuran pertama minimal Rp1.000.000.</div>
            </div>

            <div id="mdBuktiBaris" style="margin-bottom:8px;"></div>

            <div class="field">
                <label for="mdCatatan">Catatan pemeriksaan (wajib saat menolak bukti)</label>
                <textarea id="mdCatatan" maxlength="500" placeholder="Contoh: nominal transfer tidak sesuai atau bukti tidak terbaca."></textarea>
            </div>

            <div class="aksi-grid">
                <button type="button" class="btn" id="btnTerimaBukti">Terima Bukti</button>
                <button type="button" class="btn btn--merah" id="btnTolakBukti">Tolak Bukti</button>
                <button type="button" class="btn btn--hijau" id="btnLunas">Konfirmasi Dana &rarr; Lunas</button>
                <button type="button" class="btn btn--garis" id="btnBelumLunas">Tandai Belum Lunas</button>
            </div>
            <div class="hint">
                Lunas hanya boleh ditetapkan setelah penerimaan dana dikonfirmasi kampus/Rektorat. Menerima bukti tidak otomatis membuat tagihan Lunas.
            </div>
        </div>

        <div class="seksi">
            <h4>Angsuran Mahasiswa</h4>
            <div id="mdRiwayatAngsuran"></div>
        </div>

        <div class="seksi">
            <h4>Pengajuan Keterlambatan</h4>
            <div id="mdPengajuan"></div>
        </div>

        <div class="seksi">
            <h4>Riwayat Aktivitas</h4>
            <ul class="aktivitas" id="mdAktivitas"></ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('modalDetail');
        const token = document.querySelector('meta[name="csrf-token"]');
        let idAktif = null;

        const rupiah = (nilai) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(Number(nilai) || 0));

        function pesan(sel, teks, sukses) {
            const kotak = document.getElementById(sel);

            if (!kotak) {
                return;
            }

            kotak.textContent = teks;
            kotak.classList.remove('sukses', 'gagal');
            kotak.classList.add(sukses ? 'sukses' : 'gagal');
        }

        async function kirim(url, data) {
            const respons = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token ? token.content : '',
                },
                body: JSON.stringify(data || {}),
            });

            let hasil = {};

            try {
                hasil = await respons.json();
            } catch (error) {
                hasil = { success: false, message: 'Respons server tidak dapat dibaca.' };
            }

            if (!respons.ok || hasil.success === false) {
                throw new Error(hasil.message || 'Permintaan gagal diproses.');
            }

            return hasil;
        }

        function muatUlangSetelah(jeda) {
            window.setTimeout(function () {
                window.location.reload();
            }, jeda || 900);
        }

        // Filter tabel
        const cari = document.getElementById('cariPembayaran');
        const filter = document.getElementById('filterStatus');
        const barisTabel = Array.from(document.querySelectorAll('#tabelPembayaran tr[data-cari]'));

        function saring() {
            const kata = (cari ? cari.value : '').toLowerCase().trim();
            const status = filter ? filter.value : 'semua';

            barisTabel.forEach(function (baris) {
                const cocokKata = kata === '' || baris.dataset.cari.indexOf(kata) !== -1;
                const cocokStatus = status === 'semua' || baris.dataset.status === status;
                baris.style.display = cocokKata && cocokStatus ? '' : 'none';
            });
        }

        if (cari) {
            cari.addEventListener('input', saring);
        }

        if (filter) {
            filter.addEventListener('change', saring);
        }

        const btnMuatUlang = document.getElementById('btnMuatUlang');

        if (btnMuatUlang) {
            btnMuatUlang.addEventListener('click', function () {
                window.location.reload();
            });
        }

        // Modal detail
        function bukaModal() {
            modal.classList.add('terbuka');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function tutupModal() {
            modal.classList.remove('terbuka');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            idAktif = null;
        }

        modal.querySelectorAll('[data-tutup-modal]').forEach(function (tombol) {
            tombol.addEventListener('click', tutupModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                tutupModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('terbuka')) {
                tutupModal();
            }
        });

        function isiBadge(elemen, kelas, teks) {
            elemen.innerHTML = '<span class="badge ' + kelas + '">' + teks + '</span>';
        }

        function tampilkanDetail(data) {
            const p = data.pembayaran;
            const m = data.mahasiswa;

            idAktif = p.id;

            document.getElementById('judulModal').textContent = 'Detail Pembayaran ' + p.tahap;
            document.getElementById('mdMahasiswa').textContent = m.name + ' - ' + m.nim;
            document.getElementById('mdNim').textContent = m.nim;
            document.getElementById('mdKontak').textContent = (m.no_hp || '-') + ' / ' + (m.email || '-');
            document.getElementById('mdTahap').textContent = p.tahap + ' (' + p.periode + ')';
            document.getElementById('mdJatuhTempo').textContent = p.jatuh_tempo;
            isiBadge(document.getElementById('mdStatus'), p.kelas_status, p.status_label);
            isiBadge(document.getElementById('mdVerifikasi'), 'verifikasi-' + (p.kelas_verifikasi || 'kosong'), p.verifikasi_label);

            document.getElementById('mdNominal').value = Math.round(Number(p.nominal) || 0);
            document.getElementById('mdCatatan').value = p.catatan_verifikasi || '';

            const buktiBaris = document.getElementById('mdBuktiBaris');
            buktiBaris.innerHTML = p.bukti_url
                ? '<a class="tautan-bukti" href="' + p.bukti_url + '" target="_blank" rel="noopener">Buka bukti transfer</a>' +
                    '<span class="sub">' + (p.bukti_nama || '') + (p.tanggal_upload ? ' - diunggah ' + p.tanggal_upload : '') + '</span>'
                : '<span class="sub">Belum ada bukti transfer yang diunggah.</span>';

            const riwayat = document.getElementById('mdRiwayatAngsuran');
            riwayat.innerHTML = (data.riwayat_angsuran || []).map(function (item) {
                return '<div class="riwayat-baris">' +
                    '<span>' + item.tahap + ' - ' + item.nominal_rupiah + '</span>' +
                    '<span class="badge ' + item.kelas_status + '">' + item.status_label + '</span>' +
                    '</div>';
            }).join('') || '<span class="sub">Tidak ada angsuran lain.</span>';

            const pengajuan = document.getElementById('mdPengajuan');
            pengajuan.innerHTML = (data.pengajuan || []).map(function (item) {
                return '<div class="riwayat-baris">' +
                    '<span>' + item.nomor_surat + ' - rencana ' + item.tanggal_rencana + '</span>' +
                    '<span class="badge ' + (item.status === 'disetujui' ? 'lunas' : (item.status === 'ditolak' ? 'ditolak' : 'menunggu')) + '">' + item.status_label + '</span>' +
                    '</div>';
            }).join('') || '<span class="sub">Belum ada pengajuan.</span>';

            const aktivitas = document.getElementById('mdAktivitas');
            aktivitas.innerHTML = (data.riwayat_aktivitas || []).map(function (log) {
                return '<li><strong>' + log.activity + '</strong><br>' + log.description + '<br><em>' + log.time_formatted + '</em></li>';
            }).join('') || '<li>Belum ada aktivitas.</li>';

            const menunggu = p.verifikasi === 'menunggu';
            const lunas = p.raw_status === 'lunas' || p.raw_status === 'paid' || p.raw_status === 'terkonfirmasi';

            document.getElementById('btnTerimaBukti').disabled = !menunggu;
            document.getElementById('btnTolakBukti').disabled = !menunggu;
            document.getElementById('btnLunas').disabled = lunas || menunggu || p.verifikasi === 'ditolak';
            document.getElementById('btnBelumLunas').disabled = !lunas;

            document.getElementById('pesanModal').classList.remove('sukses', 'gagal');
            document.getElementById('pesanModal').textContent = '';
        }

        document.querySelectorAll('[data-detail]').forEach(function (tombol) {
            tombol.addEventListener('click', async function () {
                bukaModal();
                document.getElementById('mdMahasiswa').textContent = 'Memuat data...';

                try {
                    const respons = await fetch(tombol.dataset.detail, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!respons.ok) {
                        throw new Error('Detail tidak dapat dimuat.');
                    }

                    tampilkanDetail(await respons.json());
                } catch (error) {
                    pesan('pesanModal', error.message, false);
                }
            });
        });

        async function aksiModal(url, data, suksesPesan) {
            try {
                const hasil = await kirim(url, data);
                pesan('pesanModal', hasil.message || suksesPesan, true);
                muatUlangSetelah();
            } catch (error) {
                pesan('pesanModal', error.message, false);
            }
        }

        const urlVerifikasi = (id) => '{{ url('/musyrifah/pembayaran') }}/' + id + '/verifikasi';
        const urlStatus = (id) => '{{ url('/musyrifah/pembayaran') }}/' + id + '/status';
        const urlNominal = (id) => '{{ url('/musyrifah/pembayaran') }}/' + id + '/nominal';

        document.getElementById('btnTerimaBukti').addEventListener('click', function () {
            if (!idAktif) {
                return;
            }

            aksiModal(urlVerifikasi(idAktif), {
                keputusan: 'terima',
                catatan: document.getElementById('mdCatatan').value,
            }, 'Bukti transfer diterima.');
        });

        document.getElementById('btnTolakBukti').addEventListener('click', function () {
            if (!idAktif) {
                return;
            }

            const catatan = (document.getElementById('mdCatatan').value || '').trim();

            if (catatan.length < 3) {
                pesan('pesanModal', 'Isi catatan/alasan penolakan terlebih dahulu.', false);
                return;
            }

            aksiModal(urlVerifikasi(idAktif), {
                keputusan: 'tolak',
                catatan: catatan,
            }, 'Bukti transfer ditolak.');
        });

        document.getElementById('btnLunas').addEventListener('click', function () {
            if (!idAktif) {
                return;
            }

            if (!window.confirm('Tetapkan Lunas? Pastikan dana sudah dikonfirmasi diterima kampus/Rektorat.')) {
                return;
            }

            aksiModal(urlStatus(idAktif), {
                status: 'lunas',
                catatan: document.getElementById('mdCatatan').value,
            }, 'Status tagihan menjadi Lunas.');
        });

        document.getElementById('btnBelumLunas').addEventListener('click', function () {
            if (!idAktif) {
                return;
            }

            aksiModal(urlStatus(idAktif), {
                status: 'belum_lunas',
                catatan: document.getElementById('mdCatatan').value,
            }, 'Status tagihan dikembalikan menjadi Belum Lunas.');
        });

        document.getElementById('mdNominal').addEventListener('change', function () {
            if (!idAktif) {
                return;
            }

            aksiModal(urlNominal(idAktif), {
                nominal: Number(this.value),
            }, 'Nominal tagihan diperbarui.');
        });

        // Keputusan pengajuan keterlambatan
        document.querySelectorAll('[data-putuskan]').forEach(function (tombol) {
            tombol.addEventListener('click', async function () {
                const id = tombol.dataset.id;
                const keputusan = tombol.dataset.putuskan;
                let catatan = '';

                if (keputusan === 'tolak') {
                    catatan = window.prompt('Alasan penolakan pengajuan keterlambatan:') || '';

                    if (catatan.trim().length < 3) {
                        pesan('pesanPengajuan', 'Alasan penolakan wajib diisi.', false);
                        return;
                    }
                } else if (!window.confirm('Setujui pengajuan? Jatuh tempo akan diperbarui dan status tagihan tetap belum Lunas.')) {
                    return;
                }

                try {
                    const hasil = await kirim('{{ url('/musyrifah/pembayaran/keterlambatan') }}/' + id, {
                        keputusan: keputusan,
                        catatan: catatan,
                    });

                    pesan('pesanPengajuan', hasil.message || 'Pengajuan diproses.', true);
                    muatUlangSetelah();
                } catch (error) {
                    pesan('pesanPengajuan', error.message, false);
                }
            });
        });
    })();
</script>
@endpush

@endsection
