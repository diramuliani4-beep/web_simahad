

@extends('layouts.guru')

@section('title', 'Isi Absensi')

@section('content')

@php

    /*

    |--------------------------------------------------------------------------

    | DATA CONTOH MAHASANTRI

    |--------------------------------------------------------------------------

    | Data sementara untuk desain antarmuka.

    | Nantinya dihubungkan dengan CRUD dan database.

    */

    $daftarMahasantri = [

        ['nama' => 'Aisyah Putri', 'nim' => '2401001', 'status' => 'Hadir'],

        ['nama' => 'Nurul Azzahra', 'nim' => '2401002', 'status' => 'Hadir'],

        ['nama' => 'Siti Rahma', 'nim' => '2401003', 'status' => 'Izin'],

        ['nama' => 'Khairunnisa', 'nim' => '2401004', 'status' => 'Hadir'],

        ['nama' => 'Zahra Amelia', 'nim' => '2401005', 'status' => 'Sakit'],

    ];

@endphp

<style>

    /* ==================================================

       DASAR HALAMAN

       ================================================== */

    .isi-absensi {

        width: 100%;

        max-width: 100%;

        min-width: 0;

        padding: 10px 10px 16px;

        color: #292929;

        font-family: 'Poppins', sans-serif;

        font-size: 12px;

        box-sizing: border-box;

    }

    .isi-absensi \*,

    .isi-absensi \*::before,

    .isi-absensi \*::after {

        box-sizing: border-box;

    }

    .isi-absensi .page-header {

        margin-bottom: 14px;

    }

    .isi-absensi .page-title {

        margin: 0 0 5px;

        color: #171717;

        font-size: clamp(19px, 2vw, 23px);

        font-weight: 600;

        line-height: 1.4;

    }

    .isi-absensi .page-description {

        margin: 0;

        color: #777;

        font-size: 12px;

        line-height: 1.6;

    }

    .isi-absensi .section-title {

        margin: 0 0 7px;

        color: #292929;

        font-size: 12px;

        font-weight: 600;

        line-height: 1.5;

    }

    /* ==================================================

       INFORMASI PERTEMUAN

       ================================================== */

    .isi-absensi .info-card {

        width: 100%;

        padding: 12px;

        margin-bottom: 13px;

        background: #f8f7f6;

        border: 1px solid #eee7e3;

        border-radius: 6px;

    }

    .isi-absensi .info-card .section-title {

        margin-bottom: 4px;

    }

    .isi-absensi .info-row {

        display: grid;

        grid-template-columns: minmax(0, 1fr) minmax(0, 1.5fr);

        align-items: center;

        gap: 12px;

        min-height: 40px;

        padding: 9px 0;

        border-bottom: 1px solid #e9e5e2;

        font-size: 11px;

    }

    .isi-absensi .info-row:last-child {

        padding-bottom: 3px;

        border-bottom: none;

    }

    .isi-absensi .info-label {

        color: #85817f;

    }

    .isi-absensi .info-value {

        color: #333;

        font-weight: 600;

        text-align: right;

        overflow-wrap: anywhere;

    }

    /* ==================================================

       TABEL KEHADIRAN

       ================================================== */

    .isi-absensi .attendance-section {

        width: 100%;

        min-width: 0;

        margin-bottom: 12px;

    }

    .isi-absensi .table-wrapper {

        width: 100%;

        min-width: 0;

        overflow-x: auto;

        border-radius: 4px;

    }

    .isi-absensi .attendance-table {

        width: 100%;

        border-collapse: collapse;

        table-layout: auto;

        background: #fff;

    }

    .isi-absensi .attendance-table th,

    .isi-absensi .attendance-table td {

        padding: 7px 6px;

        border-bottom: 1px solid #ededed;

        text-align: left;

        vertical-align: middle;

        font-size: 11px;

        line-height: 1.45;

    }

    .isi-absensi .attendance-table th {

        color: #555;

        background: #fdfdfd;

        font-weight: 500;

        white-space: nowrap;

    }

    .isi-absensi .attendance-table tbody tr:last-child td {

        border-bottom: none;

    }

    .isi-absensi .attendance-table th:first-child,

    .isi-absensi .attendance-table td:first-child {

        width: 30px;

        padding-left: 4px;

    }

    .isi-absensi .attendance-table th:nth-child(2),

    .isi-absensi .attendance-table td:nth-child(2) {

        width: 34%;

    }

    .isi-absensi .attendance-table th:nth-child(3),

    .isi-absensi .attendance-table td:nth-child(3) {

        width: 20%;

    }

    .isi-absensi .attendance-table th:last-child,

    .isi-absensi .attendance-table td:last-child {

        width: 34%;

    }

    .isi-absensi .status-select {

        display: block;

        width: 100%;

        min-width: 90px;

        height: 27px;

        padding: 4px 9px;

        border: 1px solid #e1e1e1;

        border-radius: 20px;

        background: #fff;

        color: #555;

        font-family: inherit;

        font-size: 11px;

        cursor: pointer;

    }

    .isi-absensi .status-select:focus {

        outline: 1px solid #790d20;

        border-color: #790d20;

    }

    /* ==================================================

       RINGKASAN DAN CATATAN

       ================================================== */

    .isi-absensi .bottom-grid {

        display: grid;

        grid-template-columns: minmax(150px, 0.65fr) minmax(0, 1.35fr);

        gap: 12px;

        align-items: start;

        width: 100%;

        margin-bottom: 12px;

    }

    .isi-absensi .summary-section,

    .isi-absensi .notes-section {

        min-width: 0;

    }

    .isi-absensi .summary-grid {

        display: grid;

        grid-template-columns: repeat(4, minmax(0, 1fr));

        gap: 5px;

    }

    .isi-absensi .summary-box {

        min-width: 0;

        padding: 7px 3px;

        border: 1px solid #e5e0dd;

        border-radius: 5px;

        background: #fff;

        text-align: center;

    }

    .isi-absensi .summary-label {

        margin-bottom: 5px;

        color: #777;

        font-size: 9px;

    }

    .isi-absensi .summary-number {

        font-size: 13px;

        font-weight: 600;

    }

    .isi-absensi .summary-box.hadir {

        color: #39835a;

        border-color: #c6e4d1;

    }

    .isi-absensi .summary-box.izin {

        color: #b17b21;

        border-color: #f0dfb9;

    }

    .isi-absensi .summary-box.sakit {

        color: #4777b9;

        border-color: #d2e0f5;

    }

    .isi-absensi .summary-box.alpa {

        color: #c34d4d;

        border-color: #f0d0d0;

    }

    /* ==================================================

       CATATAN PERTEMUAN

       ================================================== */

    .isi-absensi .notes-label {

        display: block;

        margin: 0 0 7px;

        color: #333;

        font-size: 11px;

        font-weight: 600;

        line-height: 1.5;

    }

    .isi-absensi .notes-input {

        display: block;

        width: 100%;

        min-width: 0;

        min-height: 36px;

        padding: 9px 10px;

        border: 1px solid #d9a3ac;

        border-radius: 5px;

        background: #fff;

        color: #333;

        font-family: inherit;

        font-size: 10px;

        line-height: 1.5;

        resize: vertical;

        outline: none;

    }

    .isi-absensi .notes-input:focus {

        border-color: #790d20;

        box-shadow: 0 0 0 2px rgba(121, 13, 32, 0.06);

    }

    /* ==================================================

       TOMBOL AKSI

       ================================================== */

    .isi-absensi .button-row {

        display: flex;

        flex-wrap: wrap;

        justify-content: space-between;

        align-items: center;

        gap: 9px;

        width: 100%;

    }

    .isi-absensi .btn {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        min-height: 35px;

        padding: 8px 13px;

        border: 1px solid #790d20;

        border-radius: 5px;

        font-family: inherit;

        font-size: 11px;

        font-weight: 600;

        line-height: 1.4;

        text-align: center;

        text-decoration: none;

        cursor: pointer;

        transition: background 0.2s ease;

    }

    .isi-absensi .btn-outline {

        background: #fff;

        color: #790d20;

    }

    .isi-absensi .btn-outline:hover {

        background: #faf0f2;

    }

    .isi-absensi .btn-primary {

        margin-left: auto;

        background: #790d20;

        color: #fff;

    }

    .isi-absensi .btn-primary:hover {

        background: #5e0a19;

    }

    /* ==================================================

       HALAMAN KONFIRMASI

       ================================================== */

    .isi-absensi .confirmation-page {

        display: none;

    }

    .isi-absensi .confirmation-page.active {

        display: block;

    }

    .isi-absensi .input-page.hidden {

        display: none;

    }

    .isi-absensi .confirmation-description {

        margin: 0 0 13px;

        color: #777;

        font-size: 11px;

        line-height: 1.6;

    }

    .isi-absensi .status-text {

        font-size: 11px;

        font-weight: 500;

    }

    .isi-absensi .status-text.hadir {

        color: #39835a;

    }

    .isi-absensi .status-text.izin {

        color: #b17b21;

    }

    .isi-absensi .status-text.sakit {

        color: #4777b9;

    }

    .isi-absensi .status-text.alpa {

        color: #c34d4d;

    }

    .isi-absensi .warning-box {

        display: flex;

        align-items: flex-start;

        gap: 9px;

        padding: 11px;

        margin-top: 12px;

        border: 1px solid #f0d7da;

        border-radius: 5px;

        background: #fff0f1;

        color: #8b2435;

        font-size: 11px;

        line-height: 1.6;

    }

    .isi-absensi .warning-box strong {

        display: block;

        margin-bottom: 3px;

    }

    .isi-absensi .note-preview {

        min-height: 32px;

        padding: 9px 0;

        border-bottom: 1px solid #eee;

        color: #555;

        font-size: 11px;

        overflow-wrap: anywhere;

    }

    /* Pesan simulasi simpan */

    .isi-absensi .result-message {

        display: none;

        width: 100%;

        padding: 11px;

        margin-top: 12px;

        border: 1px solid #b7dec7;

        border-radius: 5px;

        background: #effaf3;

        color: #276749;

        font-size: 11px;

        line-height: 1.6;

    }

    .isi-absensi .result-message.show {

        display: block;

    }

    /* ==================================================

       RESPONSIVE: TABLET DAN LAYAR KECIL

       ================================================== */

    @media (max-width: 700px) {

        .isi-absensi {

            padding: 10px 6px 15px;

        }

        .isi-absensi .bottom-grid {

            grid-template-columns: minmax(0, 1fr);

            gap: 13px;

        }

        .isi-absensi .summary-grid {

            grid-template-columns: repeat(4, minmax(0, 1fr));

        }

        .isi-absensi .summary-box {

            padding: 8px 4px;

        }

        .isi-absensi .summary-label {

            font-size: 10px;

        }

        .isi-absensi .summary-number {

            font-size: 14px;

        }

    }

    @media (max-width: 420px) {

        .isi-absensi {

            padding: 8px 3px 12px;

        }

        .isi-absensi .info-card {

            padding: 9px;

        }

        .isi-absensi .info-row {

            grid-template-columns: minmax(0, 0.85fr) minmax(0, 1.15fr);

            gap: 8px;

        }

        .isi-absensi .attendance-table th,

        .isi-absensi .attendance-table td {

            padding: 6px 3px;

            font-size: 10px;

        }

        .isi-absensi .status-select {

            min-width: 85px;

            padding: 3px 5px;

            font-size: 10px;

        }

        .isi-absensi .button-row {

            gap: 7px;

        }

        .isi-absensi .btn {

            padding: 8px 9px;

            font-size: 10px;

        }

    }

    /* ==================================================
       POPUP KONFIRMASI SIMPAN ABSENSI
       ================================================== */
    .isi-absensi .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(20, 20, 20, 0.30);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
    }

    .isi-absensi .modal-overlay.show {
        display: flex;
    }

    .isi-absensi .modal-box {
        width: 100%;
        max-width: 560px;
        min-height: 300px;
        padding: 34px 24px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #fff;
        border: 1px solid #790d20;
        border-radius: 8px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        text-align: center;
        animation: modalMasuk 0.2s ease-out;
    }

    .isi-absensi .modal-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 56px;
        height: 56px;
        margin-bottom: 14px;
        border-radius: 50%;
        background: #f9edef;
        color: #790d20;
    }

    .isi-absensi .modal-icon svg {
        width: 27px;
        height: 27px;
    }

    .isi-absensi .modal-title {
        margin: 0 0 12px;
        color: #292929;
        font-size: 20px;
        font-weight: 600;
    }

    .isi-absensi .modal-description {
        max-width: 460px;
        margin: 0 0 24px;
        color: #85817f;
        font-size: 12px;
        line-height: 1.7;
    }

    .isi-absensi .modal-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        width: 100%;
    }

    .isi-absensi .modal-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: 10px 12px;
        border: 1px solid #e4dedd;
        border-radius: 7px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .isi-absensi .modal-btn-cancel {
        background: #fff;
        color: #790d20;
    }

    .isi-absensi .modal-btn-cancel:hover {
        background: #faf0f2;
    }

    .isi-absensi .modal-btn-confirm {
        border-color: #790d20;
        background: #790d20;
        color: #fff;
    }

    .isi-absensi .modal-btn-confirm:hover {
        background: #5e0a19;
    }

    @keyframes modalMasuk {
        from {
            opacity: 0;
            transform: translateY(8px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (max-width: 480px) {
        .isi-absensi .modal-box {
            min-height: 250px;
            padding: 25px 16px;
        }

        .isi-absensi .modal-title {
            font-size: 18px;
        }

        .isi-absensi .modal-description {
            font-size: 11px;
        }

        .isi-absensi .modal-btn {
            min-height: 44px;
            padding: 9px 6px;
            font-size: 11px;
        }
    }

</style>

<div class="isi-absensi">

    {{-- ================================================

         TAHAP 1: FORM PENGISIAN ABSENSI

         ================================================ --}}

    <section id="halaman-input" class="input-page">

        {{-- Judul langsung ditampilkan tanpa breadcrumb --}}

        <header class="page-header">

            <h1 class="page-title">Isi Absensi</h1>

            <p class="page-description">

                Catat kehadiran Mahasantri sesuai pertemuan yang sedang diajar.

            </p>

        </header>

        {{-- INFORMASI PERTEMUAN --}}

        <section class="info-card">

            <h2 class="section-title">Informasi Pertemuan</h2>

            <div class="info-row">

                <span class="info-label">Mata Pelajaran</span>

                <span class="info-value">Akhlak dan Adab</span>

            </div>

            <div class="info-row">

                <span class="info-label">Kelompok</span>

                <span class="info-value">Kelompok A</span>

            </div>

            <div class="info-row">

                <span class="info-label">Pertemuan</span>

                <span class="info-value">Ke-8 dari 16</span>

            </div>

            <div class="info-row">

                <span class="info-label">Tanggal</span>

                <span class="info-value">26 Oktober 2026</span>

            </div>

        </section>

        {{-- TABEL KEHADIRAN --}}

        <section class="attendance-section">

            <h2 class="section-title">Tabel Kehadiran</h2>

            <div class="table-wrapper">

                <table class="attendance-table">

                    <thead>

                        <tr>

                            <th>No.</th>

                            <th>Nama Mahasantri</th>

                            <th>NIM</th>

                            <th>Status Kehadiran</th>

                        </tr>

                    </thead>

                    <tbody id="daftar-absensi">

                        @foreach ($daftarMahasantri as $index => $mahasantri)

                            <tr>

                                <td>{{ $index + 1 }}</td>

                                <td>{{ $mahasantri['nama'] }}</td>

                                <td>{{ $mahasantri['nim'] }}</td>

                                <td>

                                    <select

                                        class="status-select status-absensi"

                                        aria-label="Status kehadiran {{ $mahasantri['nama'] }}"

                                    >

                                        <option value="Hadir"

                                            {{ $mahasantri['status'] === 'Hadir' ? 'selected' : '' }}>

                                            Hadir

                                        </option>

                                        <option value="Izin"

                                            {{ $mahasantri['status'] === 'Izin' ? 'selected' : '' }}>

                                            Izin

                                        </option>

                                        <option value="Sakit"

                                            {{ $mahasantri['status'] === 'Sakit' ? 'selected' : '' }}>

                                            Sakit

                                        </option>

                                        <option value="Alpa"

                                            {{ $mahasantri['status'] === 'Alpa' ? 'selected' : '' }}>

                                            Alpa

                                        </option>

                                    </select>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </section>

        {{-- RINGKASAN DAN CATATAN --}}

        <div class="bottom-grid">

            <section class="summary-section">

                <h2 class="section-title">Ringkasan Kehadiran</h2>

                <div class="summary-grid">

                    <div class="summary-box hadir">

                        <div class="summary-label">Hadir</div>

                        <div class="summary-number" id="jumlah-hadir">0</div>

                    </div>

                    <div class="summary-box izin">

                        <div class="summary-label">Izin</div>

                        <div class="summary-number" id="jumlah-izin">0</div>

                    </div>

                    <div class="summary-box sakit">

                        <div class="summary-label">Sakit</div>

                        <div class="summary-number" id="jumlah-sakit">0</div>

                    </div>

                    <div class="summary-box alpa">

                        <div class="summary-label">Alpa</div>

                        <div class="summary-number" id="jumlah-alpa">0</div>

                    </div>

                </div>

            </section>

            <section class="notes-section">

                <label class="notes-label" for="catatan-pertemuan">

                    Catatan Pertemuan (Opsional)

                </label>

                <textarea

                    id="catatan-pertemuan"

                    class="notes-input"

                    placeholder="Materi yang dipelajari atau catatan pertemuan..."

                ></textarea>

            </section>

        </div>

        {{-- TOMBOL AKSI --}}

        <div class="button-row">

            <button

                type="button"

                class="btn btn-outline"

                id="tombol-periksa"

            >

                Periksa &amp; Konfirmasi

            </button>

            <button

                type="button"

                class="btn btn-primary"

                id="tombol-simpan-langsung"

            >

                Simpan Absensi

            </button>

        </div>

    </section>

    {{-- ================================================

         TAHAP 2: PERIKSA DAN KONFIRMASI

         ================================================ --}}

    <section id="halaman-konfirmasi" class="confirmation-page">

        <header class="page-header">

            <h1 class="page-title">Periksa &amp; Konfirmasi Absensi</h1>

            <p class="confirmation-description">

                Periksa kembali data kehadiran sebelum menyimpan absensi.

            </p>

        </header>

        {{-- INFORMASI PERTEMUAN --}}

        <section class="info-card">

            <h2 class="section-title">Informasi Pertemuan</h2>

            <div class="info-row">

                <span class="info-label">Mata Pelajaran</span>

                <span class="info-value">Akhlak dan Adab</span>

            </div>

            <div class="info-row">

                <span class="info-label">Kelompok</span>

                <span class="info-value">Kelompok A</span>

            </div>

            <div class="info-row">

                <span class="info-label">Pertemuan</span>

                <span class="info-value">Ke-8 dari 16</span>

            </div>

            <div class="info-row">

                <span class="info-label">Tanggal</span>

                <span class="info-value">26 Oktober 2026</span>

            </div>

        </section>

        {{-- TABEL KONFIRMASI --}}

        <section class="attendance-section">

            <h2 class="section-title">Periksa Kehadiran Mahasantri</h2>

            <div class="table-wrapper">

                <table class="attendance-table">

                    <thead>

                        <tr>

                            <th>No.</th>

                            <th>Nama Mahasantri</th>

                            <th>NIM</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody id="daftar-konfirmasi"></tbody>

                </table>

            </div>

        </section>

        {{-- PERINGATAN --}}

        <div class="warning-box">

            <span aria-hidden="true">ⓘ</span>

            <div>

                <strong>Pastikan data sudah benar</strong>

                Setelah disimpan, absensi akan tercatat sebagai data

                pertemuan. Perubahan berikutnya mengikuti aturan koreksi

                yang berlaku.

            </div>

        </div>

        {{-- CATATAN KONFIRMASI --}}

        <section style="margin-top: 12px;">

            <label class="notes-label">Catatan Pertemuan</label>

            <div id="catatan-konfirmasi" class="note-preview">

                Tidak ada catatan.

            </div>

        </section>

        {{-- TOMBOL KONFIRMASI --}}

        <div class="button-row">

            <button

                type="button"

                class="btn btn-outline"

                id="tombol-kembali"

            >

                Kembali Periksa

            </button>

            <button

                type="button"

                class="btn btn-primary"

                id="tombol-konfirmasi-simpan"

            >

                Simpan Absensi

            </button>

        </div>

    </section>

    {{-- PESAN HASIL SIMULASI --}}

    <div
        id="pesan-absensi"
        class="result-message"
        role="status"
        aria-live="polite"
    ></div>

    {{-- POPUP KONFIRMASI SIMPAN ABSENSI --}}
    <div
        id="modal-konfirmasi-absensi"
        class="modal-overlay"
        role="dialog"
        aria-modal="true"
        aria-labelledby="judul-modal-absensi"
        aria-describedby="deskripsi-modal-absensi"
    >
        <div class="modal-box">
            {{-- Ikon clipboard --}}
            <div class="modal-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                    <path d="M9 4.5h6a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1Z"></path>
                    <path d="m9 13 2 2 4-4"></path>
                </svg>
            </div>

            {{-- Judul dan pesan konfirmasi --}}
            <h2 id="judul-modal-absensi" class="modal-title">
                Simpan Absensi?
            </h2>

            <p id="deskripsi-modal-absensi" class="modal-description">
                Pastikan status kehadiran seluruh Mahasantri sudah benar.
                Lanjutkan menyimpan absensi pertemuan ke-8?
            </p>

            {{-- Tombol batal dan konfirmasi --}}
            <div class="modal-actions">
                <button
                    type="button"
                    id="tombol-batal-simpan"
                    class="modal-btn modal-btn-cancel"
                >
                    Batal
                </button>

                <button
                    type="button"
                    id="tombol-ya-simpan"
                    class="modal-btn modal-btn-confirm"
                >
                    Ya, Simpan
                </button>
            </div>
        </div>
    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*

    |--------------------------------------------------------------------------

    | REFERENSI ELEMEN

    |--------------------------------------------------------------------------

    */

    const halamanInput = document.getElementById('halaman-input');

    const halamanKonfirmasi = document.getElementById('halaman-konfirmasi');

    const daftarInput = document.getElementById('daftar-absensi');

    const daftarKonfirmasi = document.getElementById('daftar-konfirmasi');

    const statusSelect = daftarInput.querySelectorAll('.status-absensi');

    const catatanInput = document.getElementById('catatan-pertemuan');

    const catatanKonfirmasi = document.getElementById('catatan-konfirmasi');

    const pesan = document.getElementById('pesan-absensi');

    /*

    |--------------------------------------------------------------------------

    | REKAP KEHADIRAN OTOMATIS

    |--------------------------------------------------------------------------

    */

    function hitungRekap() {

        const rekap = {

            Hadir: 0,

            Izin: 0,

            Sakit: 0,

            Alpa: 0

        };

        statusSelect.forEach(function (select) {

            if (Object.prototype.hasOwnProperty.call(rekap, select.value)) {

                rekap[select.value]++;

            }

        });

        document.getElementById('jumlah-hadir').textContent = rekap.Hadir;

        document.getElementById('jumlah-izin').textContent = rekap.Izin;

        document.getElementById('jumlah-sakit').textContent = rekap.Sakit;

        document.getElementById('jumlah-alpa').textContent = rekap.Alpa;

    }

    statusSelect.forEach(function (select) {

        select.addEventListener('change', hitungRekap);

    });

    /*

    |--------------------------------------------------------------------------

    | MENYIAPKAN DATA KONFIRMASI

    |--------------------------------------------------------------------------

    */

    function siapkanKonfirmasi() {

        daftarKonfirmasi.replaceChildren();

        daftarInput.querySelectorAll('tr').forEach(function (baris, index) {

            const kolom = baris.querySelectorAll('td');

            const status = baris.querySelector('.status-absensi').value;

            const barisBaru = document.createElement('tr');

            const nomor = document.createElement('td');

            nomor.textContent = index + 1;

            const nama = document.createElement('td');

            nama.textContent = kolom[1].textContent.trim();

            const nim = document.createElement('td');

            nim.textContent = kolom[2].textContent.trim();

            const statusKolom = document.createElement('td');

            const statusTeks = document.createElement('span');

            statusTeks.className = 'status-text ' + status.toLowerCase();

            statusTeks.textContent = status;

            statusKolom.appendChild(statusTeks);

            barisBaru.append(nomor, nama, nim, statusKolom);

            daftarKonfirmasi.appendChild(barisBaru);

        });

        catatanKonfirmasi.textContent =

            catatanInput.value.trim() || 'Tidak ada catatan.';

    }

    /*

    |--------------------------------------------------------------------------

    | BUKA HALAMAN KONFIRMASI

    |--------------------------------------------------------------------------

    */

    document.getElementById('tombol-periksa')

        .addEventListener('click', function () {

            siapkanKonfirmasi();

            halamanInput.classList.add('hidden');

            halamanKonfirmasi.classList.add('active');

            pesan.classList.remove('show');

            window.scrollTo({

                top: 0,

                behavior: 'smooth'

            });

        });

    /*

    |--------------------------------------------------------------------------

    | KEMBALI KE FORM PENGISIAN

    |--------------------------------------------------------------------------

    */

    document.getElementById('tombol-kembali')

        .addEventListener('click', function () {

            halamanKonfirmasi.classList.remove('active');

            halamanInput.classList.remove('hidden');

            pesan.classList.remove('show');

            window.scrollTo({

                top: 0,

                behavior: 'smooth'

            });

        });

    /*
    |--------------------------------------------------------------------------
    | POPUP KONFIRMASI SIMPAN ABSENSI
    |--------------------------------------------------------------------------
    | Tombol Simpan membuka popup.
    | Batal menutup popup tanpa menyimpan.
    | Ya, Simpan menjalankan simulasi sementara karena CRUD database
    | belum dihubungkan.
    */
    const modalAbsensi = document.getElementById('modal-konfirmasi-absensi');
    const tombolBatalSimpan = document.getElementById('tombol-batal-simpan');
    const tombolYaSimpan = document.getElementById('tombol-ya-simpan');
    const deskripsiModal = document.getElementById('deskripsi-modal-absensi');

    function bukaModalAbsensi() {
        // Ambil nomor pertemuan dari informasi yang ditampilkan.
        const nilaiPertemuan = halamanInput.querySelectorAll('.info-value')[2];
        const teksPertemuan = nilaiPertemuan
            ? nilaiPertemuan.textContent.trim()
            : 'Ke-8 dari 16';

        const cocok = teksPertemuan.match(/\d+/);
        const nomorPertemuan = cocok ? cocok[0] : '8';

        deskripsiModal.textContent =
            'Pastikan status kehadiran seluruh Mahasantri sudah benar. ' +
            'Lanjutkan menyimpan absensi pertemuan ke-' + nomorPertemuan + '?';

        modalAbsensi.classList.add('show');
        tombolBatalSimpan.focus();
    }

    function tutupModalAbsensi() {
        modalAbsensi.classList.remove('show');
    }

    // Tombol Simpan Absensi di halaman pengisian membuka popup.
    document.getElementById('tombol-simpan-langsung')
        .addEventListener('click', bukaModalAbsensi);

    // Tombol Simpan Absensi di halaman pemeriksaan juga membuka popup.
    document.getElementById('tombol-konfirmasi-simpan')
        .addEventListener('click', bukaModalAbsensi);

    // Batal menutup popup tanpa menyimpan data.
    tombolBatalSimpan.addEventListener('click', tutupModalAbsensi);

    // Klik area gelap di luar kotak popup akan menutup popup.
    modalAbsensi.addEventListener('click', function (event) {
        if (event.target === modalAbsensi) {
            tutupModalAbsensi();
        }
    });

    // Tombol Escape menutup popup.
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modalAbsensi.classList.contains('show')) {
            tutupModalAbsensi();
        }
    });

    // Konfirmasi akhir. Saat ini masih simulasi dan belum menyimpan ke database.
    tombolYaSimpan.addEventListener('click', function () {
        tutupModalAbsensi();

        pesan.textContent =
            'Konfirmasi berhasil. Data absensi belum tersimpan permanen ' +
            'karena proses penyimpanan database belum dihubungkan.';

        pesan.classList.add('show');

        pesan.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    });

    // Menghitung status awal saat halaman dibuka.

    hitungRekap();

});

</script>

@endsection
