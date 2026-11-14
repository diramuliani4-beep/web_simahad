
@extends('layouts.musyrifah')

@section('title', 'Laporan Musyrifah')

@section('content')

<style>
    .lap {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        padding-bottom: 24px;
        color: #322a2d;
        font-size: 13px;
    }

    .lap *,
    .lap *::before,
    .lap *::after {
        box-sizing: border-box;
    }

    /* ---------------- Kepala halaman ---------------- */
    .lap-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .lap-head h1 {
        margin: 0;
        color: #211a1c;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -.2px;
    }

    .lap-head p {
        max-width: 580px;
        margin: 4px 0 0;
        color: #8b7e84;
        font-size: 12.5px;
        line-height: 1.55;
    }

    .lap-head-aksi {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* ---------------- Tombol ---------------- */
    .lap-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 34px;
        padding: 7px 13px;
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

    .lap-btn--utama {
        border-color: #6B0D1B;
        background: #6B0D1B;
        color: #fff;
    }

    .lap-btn--utama:hover {
        border-color: #52070f;
        background: #52070f;
    }

    .lap-btn--garis {
        border-color: #e6d7dc;
        background: #fff;
        color: #6B0D1B;
    }

    .lap-btn--garis:hover {
        border-color: #C9A96A;
        background: #F8EDEF;
    }

    .lap-btn--kecil {
        min-height: 26px;
        padding: 4px 9px;
        border-radius: 7px;
        font-size: 11.5px;
    }

    /* ---------------- Kartu ---------------- */
    .lap-kartu {
        margin-bottom: 12px;
        padding: 13px 15px 15px;
        border: 1px solid #efe5e7;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(59, 7, 15, .035);
    }

    .lap-sec-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }

    .lap-sec-head h2 {
        position: relative;
        margin: 0;
        padding-left: 9px;
        color: #6B0D1B;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.3;
    }

    .lap-sec-head h2::before {
        content: "";
        position: absolute;
        top: 2px;
        bottom: 2px;
        left: 0;
        width: 3px;
        border-radius: 2px;
        background: #C9A96A;
    }

    .lap-sec-head p {
        margin: 3px 0 0;
        padding-left: 9px;
        color: #8b7e84;
        font-size: 11.5px;
        line-height: 1.5;
    }

    /* ---------------- Formulir ---------------- */
    .lap-filter {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        align-items: end;
        gap: 9px;
        padding: 10px;
        border: 1px solid #f3ecee;
        border-radius: 10px;
        background: #fdfafb;
    }

    .lap-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
    }

    .lap-field label {
        color: #7d7075;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
    }

    .lap-field select,
    .lap-field input[type="text"] {
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

    .lap-field select:focus,
    .lap-field input[type="text"]:focus {
        border-color: #C9A96A;
        box-shadow: 0 0 0 3px rgba(201, 169, 106, .16);
    }

    .lap-field--aksi {
        flex-direction: row;
        align-items: center;
        gap: 7px;
    }

    .lap-field--lebar {
        grid-column: 1 / -1;
    }

    /* ---------------- Grid dua kolom ---------------- */
    .lap-dua {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.1fr);
        gap: 12px;
    }

    .lap-dua .lap-kartu {
        margin-bottom: 0;
    }

    .lap-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px 10px;
    }

    .lap-form .lap-field--lebar {
        grid-column: 1 / -1;
    }

    .lap-form-aksi {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* ---------------- Kartu kategori ---------------- */
    .lap-kategori {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(212px, 1fr));
        gap: 9px;
        margin-top: 11px;
    }

    .lap-kat {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 11px;
        border: 1px solid #f0e7e9;
        border-radius: 10px;
        background: #fffdfd;
        text-decoration: none;
        transition: border-color .16s, background .16s, box-shadow .16s;
    }

    .lap-kat:hover {
        border-color: #e2c9a6;
        background: #fffaf3;
        box-shadow: 0 3px 10px rgba(107, 13, 27, .06);
    }

    .lap-kat-ikon {
        display: grid;
        place-items: center;
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: #F8EDEF;
        color: #6B0D1B;
        font-size: 15px;
        line-height: 1;
    }

    .lap-kat-isi {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .lap-kat-nama {
        color: #40343a;
        font-size: 12.5px;
        font-weight: 700;
        line-height: 1.35;
    }

    .lap-kat-ket {
        display: -webkit-box;
        overflow: hidden;
        margin-top: 2px;
        color: #8b7e84;
        font-size: 11.5px;
        line-height: 1.45;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .lap-kat-go {
        margin-top: 5px;
        color: #6B0D1B;
        font-size: 11px;
        font-weight: 600;
    }

    /* ---------------- Tabel ---------------- */
    .lap-tabel-bungkus {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #f1e9eb;
        border-radius: 9px;
    }

    .lap-tabel {
        width: 100%;
        min-width: 560px;
        border-collapse: collapse;
    }

    .lap-tabel--ringkas {
        min-width: 440px;
    }

    .lap-tabel th {
        padding: 7px 9px;
        border-bottom: 1px solid #eee1e4;
        background: #FBF6EC;
        color: #6B0D1B;
        text-align: left;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .lap-tabel td {
        padding: 7px 9px;
        border-bottom: 1px solid #f4eef0;
        color: #4f4549;
        font-size: 11.5px;
        vertical-align: middle;
    }

    .lap-tabel tbody tr:last-child td {
        border-bottom: 0;
    }

    .lap-tabel tbody tr:hover {
        background: #fffbfc;
    }

    .lap-sel {
        display: block;
        color: #6B0D1B;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;
    }

    .lap-sub {
        display: block;
        margin-top: 1px;
        color: #8f8288;
        font-size: 10.5px;
        line-height: 1.45;
    }

    .lap-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        background: #F8EDEF;
        color: #6B0D1B;
        font-size: 10.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .lap-kanan {
        text-align: right;
    }

    .lap-aksi {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 5px;
    }

    .lap-aksi form {
        display: inline-flex;
    }

    .lap-kosong {
        padding: 14px;
        border: 1px dashed #e6d7dc;
        border-radius: 9px;
        background: #fdfbfb;
        color: #91858a;
        font-size: 11.5px;
        line-height: 1.55;
        text-align: center;
    }

    .lap-alert {
        margin-bottom: 12px;
        padding: 9px 12px;
        border: 1px solid #ead8dd;
        border-left: 3px solid #6B0D1B;
        border-radius: 9px;
        background: #fff;
        color: #6B0D1B;
        font-size: 12px;
        font-weight: 600;
    }

    /* ---------------- Paginasi ---------------- */
    .lap-paginasi {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 11px;
    }

    .lap-paginasi-info {
        color: #8b7e84;
        font-size: 11px;
    }

    .lap-paginasi-nav {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .lap-page {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        height: 26px;
        padding: 0 7px;
        border: 1px solid #ece2e4;
        border-radius: 7px;
        background: #fff;
        color: #6B0D1B;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
    }

    .lap-page:hover {
        border-color: #C9A96A;
        background: #fffaf3;
    }

    .lap-page.is-active {
        border-color: #6B0D1B;
        background: #6B0D1B;
        color: #fff;
    }

    .lap-page.is-off {
        color: #c8bcc1;
        pointer-events: none;
    }

    #riwayat-periodik {
        scroll-margin-top: calc(var(--header-height, 68px) + 12px);
    }

    @media (max-width: 1280px) {
        .lap-dua {
            grid-template-columns: minmax(0, 1fr);
        }

        .lap-dua .lap-kartu {
            margin-bottom: 12px;
        }
    }

    @media (max-width: 700px) {
        .lap-head h1 {
            font-size: 19px;
        }

        .lap-kartu {
            padding: 11px;
        }

        .lap-form {
            grid-template-columns: minmax(0, 1fr);
        }

        .lap-kategori {
            grid-template-columns: minmax(0, 1fr);
        }

        .lap-field--aksi {
            width: 100%;
        }

        .lap-field--aksi .lap-btn {
            flex: 1;
        }
    }
</style>

<div class="lap">

    {{-- KEPALA HALAMAN --}}
    <div class="lap-head">
        <div>
            <h1>Laporan</h1>
            <p>
                Rekap cepat per kategori, atau dokumen Laporan Bulanan &amp; Tahunan lengkap
                untuk periode tertentu.
            </p>
        </div>

        <div class="lap-head-aksi">
            <a href="#riwayat-periodik" class="lap-btn lap-btn--garis">Riwayat periodik</a>
            <a href="{{ route('musyrifah.laporan.buat') }}" class="lap-btn lap-btn--utama">
                + Buat Laporan Periodik
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="lap-alert">{{ session('status') }}</div>
    @endif

    {{-- REKAP PER KATEGORI --}}
    <section class="lap-kartu">
        <div class="lap-sec-head">
            <div>
                <h2>Rekap per kategori</h2>
                <p>Ringkasan cepat untuk periode {{ $labelPeriode }}.</p>
            </div>
        </div>

        <form action="{{ route('musyrifah.laporan') }}" method="GET" class="lap-filter">
            <div class="lap-field">
                <label for="periode">Periode laporan</label>
                <select name="periode" id="periode">
                    @foreach ($opsiPeriode as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($periode === $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lap-field">
                <label for="jenis">Jenis laporan</label>
                <select name="jenis" id="jenis">
                    <option value="semua" @selected($jenis === 'semua')>Semua laporan</option>
                    <option value="kehadiran" @selected($jenis === 'kehadiran')>Laporan Kehadiran</option>
                    <option value="pembayaran" @selected($jenis === 'pembayaran')>Laporan Pembayaran</option>
                    <option value="pendaftaran" @selected($jenis === 'pendaftaran')>Laporan Pendaftaran</option>
                    <option value="kamar" @selected($jenis === 'kamar')>Laporan Data Kamar</option>
                </select>
            </div>

            <div class="lap-field lap-field--aksi">
                <button type="submit" class="lap-btn lap-btn--utama">Tampilkan</button>
            </div>
        </form>

        <div class="lap-kategori">
            @forelse ($laporan as $item)
                <a href="{{ $item['url'] }}" class="lap-kat">
                    <span class="lap-kat-ikon" aria-hidden="true">
                        @if ($item['jenis'] === 'kehadiran')
                            ♧
                        @elseif ($item['jenis'] === 'pembayaran')
                            ▣
                        @elseif ($item['jenis'] === 'pendaftaran')
                            ▤
                        @else
                            ▦
                        @endif
                    </span>

                    <span class="lap-kat-isi">
                        <span class="lap-kat-nama">{{ $item['judul'] }}</span>
                        <span class="lap-kat-ket">{{ $item['deskripsi'] }}</span>
                        <span class="lap-kat-go">Lihat Laporan &rsaquo;</span>
                    </span>
                </a>
            @empty
                <div class="lap-kosong" style="grid-column: 1 / -1;">
                    Tidak ada jenis laporan yang tersedia.
                </div>
            @endforelse
        </div>
    </section>

    {{-- CATAT LAPORAN & REGISTER PERIODE --}}
    <div class="lap-dua">

        <section class="lap-kartu">
            <div class="lap-sec-head">
                <div>
                    <h2>Catat laporan baru</h2>
                    <p>Draft tercatat atas nama Anda beserta unit sumber laporan.</p>
                </div>
            </div>

            <form action="{{ route('musyrifah.laporan.simpan') }}" method="POST" class="lap-form">
                @csrf

                <div class="lap-field">
                    <label for="reg-jenis-baru">Jenis laporan</label>
                    <select name="jenis" id="reg-jenis-baru" required>
                        @foreach ($opsiJenis as $opsi)
                            <option value="{{ $opsi['value'] }}">{{ $opsi['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lap-field">
                    <label for="reg-periode-baru">Periode laporan</label>
                    <select name="periode" id="reg-periode-baru" required>
                        @foreach ($opsiPeriode as $opsi)
                            <option value="{{ $opsi['value'] }}" @selected($periode === $opsi['value'])>
                                {{ $opsi['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lap-field lap-field--lebar">
                    <label for="reg-lantai-baru">Lantai sumber laporan</label>
                    <select name="lantai" id="reg-lantai-baru">
                        <option value="">Seluruh Ma’had</option>
                        @foreach ($opsiLantai as $opsi)
                            <option value="{{ $opsi['value'] }}">{{ $opsi['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lap-field lap-field--lebar">
                    <label for="reg-catatan-baru">Catatan (opsional)</label>
                    <input
                        type="text"
                        name="catatan"
                        id="reg-catatan-baru"
                        maxlength="500"
                        placeholder="Misalnya: rekap kehadiran pekan ke-2"
                    >
                </div>

                <div class="lap-form-aksi lap-field--lebar">
                    <button type="submit" class="lap-btn lap-btn--utama">Simpan sebagai Draft</button>
                </div>
            </form>
        </section>

        <section class="lap-kartu">
            <div class="lap-sec-head">
                <div>
                    <h2>Register laporan</h2>
                    <p>Periode {{ $labelPeriode }}.</p>
                </div>
            </div>

            @if ($register->isEmpty())
                <div class="lap-kosong">
                    Belum ada laporan yang Anda catat pada periode ini.
                </div>
            @else
                <div class="lap-tabel-bungkus">
                    <table class="lap-tabel lap-tabel--ringkas">
                        <thead>
                            <tr>
                                <th>Laporan</th>
                                <th>Status</th>
                                <th class="lap-kanan">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($register as $item)
                                <tr>
                                    <td>
                                        <span class="lap-sel">{{ $item->judul }}</span>
                                        <span class="lap-sub">
                                            {{ $item->labelUnit() }} · {{ $item->identitasPembuat() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="lap-badge">{{ $item->labelStatus() }} · v{{ (int) $item->versi }}</span>
                                    </td>
                                    <td class="lap-kanan">
                                        <div class="lap-aksi">
                                            @if ($item->status !== \App\Models\LaporanMahad::STATUS_DITERBITKAN)
                                                <form action="{{ route('musyrifah.laporan.terbitkan', $item) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="lap-btn lap-btn--utama lap-btn--kecil">
                                                        Terbitkan
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($item->status !== \App\Models\LaporanMahad::STATUS_DIARSIPKAN)
                                                <form action="{{ route('musyrifah.laporan.arsipkan', $item) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="lap-btn lap-btn--garis lap-btn--kecil">
                                                        Arsipkan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </div>

    {{-- RIWAYAT LAPORAN PERIODIK --}}
    <section class="lap-kartu" id="riwayat-periodik" style="margin-top: 12px;">
        <div class="lap-sec-head">
            <div>
                <h2>Riwayat Laporan Bulanan &amp; Tahunan</h2>
                <p>
                    Dokumen tetap: isinya tidak berubah walaupun data operasional setelah
                    tanggal pembuatan diperbarui.
                </p>
            </div>
        </div>

        <form action="{{ route('musyrifah.laporan') }}#riwayat-periodik" method="GET" class="lap-filter">
            <div class="lap-field">
                <label for="p_jenis">Jenis laporan</label>
                <select name="p_jenis" id="p_jenis">
                    <option value="">Semua jenis</option>
                    @foreach ($opsiJenisPeriodik as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($filterPeriodik['jenis'] === $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lap-field">
                <label for="p_tahun">Tahun</label>
                <select name="p_tahun" id="p_tahun">
                    <option value="">Semua tahun</option>
                    @foreach ($opsiTahun as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($filterPeriodik['tahun'] === (string) $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lap-field">
                <label for="p_bulan">Bulan (bulanan)</label>
                <select name="p_bulan" id="p_bulan">
                    <option value="">Semua bulan</option>
                    @foreach ($opsiBulan as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($filterPeriodik['bulan'] === (string) $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lap-field">
                <label for="p_status">Status</label>
                <select name="p_status" id="p_status">
                    <option value="">Semua status</option>
                    @foreach ($opsiStatusPeriodik as $opsi)
                        <option value="{{ $opsi['value'] }}" @selected($filterPeriodik['status'] === $opsi['value'])>
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lap-field lap-field--aksi">
                <button type="submit" class="lap-btn lap-btn--utama">Terapkan</button>
                <a href="{{ route('musyrifah.laporan') }}#riwayat-periodik" class="lap-btn lap-btn--garis">Reset</a>
            </div>
        </form>

        <div style="margin-top: 11px;">
            @if ($daftarPeriodik->isEmpty())
                <div class="lap-kosong">
                    Belum ada laporan bulanan atau tahunan yang tersimpan. Gunakan tombol
                    “Buat Laporan” untuk menyusun laporan pertama.
                </div>
            @else
                <div class="lap-tabel-bungkus">
                    <table class="lap-tabel">
                        <thead>
                            <tr>
                                <th>Laporan</th>
                                <th>Periode</th>
                                <th>Pembuat</th>
                                <th>Status</th>
                                <th class="lap-kanan">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($daftarPeriodik as $item)
                                <tr>
                                    <td><span class="lap-sel">{{ $item->judul }}</span></td>
                                    <td>{{ $item->labelPeriode() }}</td>
                                    <td>{{ $item->identitasPembuat() }}</td>
                                    <td>
                                        <span class="lap-badge">{{ $item->labelStatus() }} · v{{ (int) $item->versi }}</span>
                                    </td>
                                    <td class="lap-kanan">
                                        <div class="lap-aksi">
                                            <a href="{{ route('musyrifah.laporan.periodik', $item) }}"
                                                class="lap-btn lap-btn--garis lap-btn--kecil">
                                                Buka
                                            </a>
                                            <a href="{{ route('musyrifah.laporan.periodik.pdf', $item) }}"
                                                class="lap-btn lap-btn--garis lap-btn--kecil">
                                                PDF
                                            </a>
                                            <a href="{{ route('musyrifah.laporan.periodik.excel', $item) }}"
                                                class="lap-btn lap-btn--garis lap-btn--kecil">
                                                Excel
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($daftarPeriodik->hasPages())
                    @php
                        $halamanAwal = max(1, $daftarPeriodik->currentPage() - 2);
                        $halamanAkhir = min($daftarPeriodik->lastPage(), $daftarPeriodik->currentPage() + 2);
                    @endphp

                    <div class="lap-paginasi">
                        <span class="lap-paginasi-info">
                            Menampilkan {{ $daftarPeriodik->firstItem() }}–{{ $daftarPeriodik->lastItem() }}
                            dari {{ $daftarPeriodik->total() }} laporan
                        </span>

                        <div class="lap-paginasi-nav">
                            @if ($daftarPeriodik->onFirstPage())
                                <span class="lap-page is-off">&lsaquo;</span>
                            @else
                                <a href="{{ $daftarPeriodik->previousPageUrl() }}#riwayat-periodik" class="lap-page"
                                    rel="prev">&lsaquo;</a>
                            @endif

                            @foreach ($daftarPeriodik->getUrlRange($halamanAwal, $halamanAkhir) as $halaman => $url)
                                @if ($halaman === $daftarPeriodik->currentPage())
                                    <span class="lap-page is-active" aria-current="page">{{ $halaman }}</span>
                                @else
                                    <a href="{{ $url }}#riwayat-periodik" class="lap-page">{{ $halaman }}</a>
                                @endif
                            @endforeach

                            @if ($daftarPeriodik->hasMorePages())
                                <a href="{{ $daftarPeriodik->nextPageUrl() }}#riwayat-periodik" class="lap-page"
                                    rel="next">&rsaquo;</a>
                            @else
                                <span class="lap-page is-off">&rsaquo;</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>

</div>

@endsection
