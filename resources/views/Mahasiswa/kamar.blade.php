@extends('layouts.mahasiswa')

@section('title', 'Informasi Kamar')

@push('styles')

<style>

.mhs-kamar-page,

.mhs-kamar-page * {

    box-sizing: border-box;

}

.mhs-kamar-page {

    --maroon: #790c20;

    --border: #8a1428;

    --soft-pink: #f8edef;

    --muted: #777;

}

.mhs-kamar-page .breadcrumb {

    color: var(--maroon);

    font-size: 20px;

    margin: 0;

}

.mhs-kamar-page .page-title {

    font-size: 31px;

    font-weight: 500;

    line-height: 1.5;

    margin: 0;

}

.mhs-kamar-page .page-subtitle {

    font-size: 19px;

    margin: 0 0 9px;

}

.mhs-kamar-page .room-card {

    padding: 20px 30px 23px;

    background: #fff;

    border: 1px solid var(--border);

    border-radius: 12px;

    margin-bottom: 22px;

}

.mhs-kamar-page .room-heading {

    display: flex;

    align-items: center;

    justify-content: space-between;

    flex-wrap: wrap;

    gap: 10px 12px;

    margin-bottom: 17px;

}

.mhs-kamar-page .room-heading h2 {

    font-size: 21px;

    font-weight: 500;

    color: #363636;

    margin: 0;

}

.mhs-kamar-page .status {

    background: #d8f5e3;

    color: #008b42;

    border-radius: 20px;

    padding: 4px 12px;

    font-size: 13px;

    white-space: nowrap;

}

.mhs-kamar-page .room-highlight {

    background: var(--soft-pink);

    color: var(--maroon);

    border-radius: 11px;

    padding: 20px;

    margin-bottom: 14px;

}

.mhs-kamar-page .room-highlight svg {

    width: 28px;

    height: 28px;

    display: block;

    margin-bottom: 9px;

    fill: none;

    stroke: currentColor;

    stroke-width: 2;

    stroke-linecap: round;

    stroke-linejoin: round;

}

.mhs-kamar-page .room-label {

    font-size: 16px;

    margin-bottom: 7px;

}

.mhs-kamar-page .room-number {

    font-size: 29px;

    font-weight: 500;

    margin-bottom: 5px;

}

.mhs-kamar-page .muted {

    color: var(--muted);

    font-size: 15px;

}

.mhs-kamar-page .room-grid {

    display: grid;

    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 9px;

}

.mhs-kamar-page .room-info {

    border: 1px solid #e6dcd6;

    border-radius: 8px;

    padding: 15px;

}

.mhs-kamar-page .room-info svg {

    width: 22px;

    height: 22px;

    display: block;

    color: var(--maroon);

    fill: none;

    stroke: currentColor;

    stroke-width: 2;

    margin-bottom: 4px;

}

.mhs-kamar-page .info-label {

    color: var(--muted);

    font-size: 13px;

    margin-bottom: 5px;

}

.mhs-kamar-page .info-value {

    font-size: 15px;

    font-weight: 500;

}

.mhs-kamar-page .detail-card {

    background: #fff;

    border: 1px solid var(--border);

    border-radius: 11px;

    padding: 12px 15px 17px;

    margin-bottom: 23px;

}

.mhs-kamar-page .detail-card h2 {

    font-size: 14px;

    font-weight: 500;

    margin: 0 0 9px;

}

.mhs-kamar-page .detail-row {

    max-width: 580px;

    border-bottom: 1px solid #e8e1dd;

    padding: 10px 0 15px;

    margin-bottom: 3px;

}

.mhs-kamar-page .detail-label {

    color: #888;

    font-size: 11px;

    margin-bottom: 4px;

}

.mhs-kamar-page .detail-value {

    font-size: 13px;

    font-weight: 500;

    overflow-wrap: break-word;

}

.mhs-kamar-page .additional-card {

    background: #fff;

    border: 1px solid var(--border);

    border-radius: 11px;

    padding: 16px;

}

.mhs-kamar-page .additional-heading {

    display: flex;

    align-items: center;

    gap: 8px;

    font-size: 14px;

    font-weight: 500;

    margin-bottom: 9px;

}

.mhs-kamar-page .additional-heading svg {

    width: 18px;

    height: 18px;

    color: var(--maroon);

    fill: none;

    stroke: currentColor;

    stroke-width: 2;

}

.mhs-kamar-page .additional-card p {

    color: var(--muted);

    font-size: 11px;

    margin: 0;

}

.mhs-kamar-page .alert {

    border-radius: 10px;

    padding: 12px 16px;

    font-size: 13px;

    line-height: 1.6;

    margin-bottom: 16px;

    border: 1px solid transparent;

}

.mhs-kamar-page .alert-success {

    background: #e7f7ee;

    border-color: #b7e4c9;

    color: #166534;

}

.mhs-kamar-page .alert-error {

    background: #fdecef;

    border-color: #f3c2cc;

    color: #8a1428;

}

.mhs-kamar-page .status-banner {

    border-radius: 11px;

    padding: 15px 18px;

    margin-bottom: 18px;

    border: 1px solid #f0d9a8;

    background: #fff8e6;

    color: #8a5c04;

    font-size: 13px;

    line-height: 1.6;

}

.mhs-kamar-page .status-banner strong {

    display: block;

    font-size: 15px;

    margin-bottom: 3px;

}

.mhs-kamar-page .persetujuan-detail {
    display: block;
    margin: 10px 0 8px;
    padding: 9px 12px;
    border: 1px solid #e5d6b1;
    border-radius: 8px;
    background: #fffdf5;
    font-size: 12px;
}

.mhs-kamar-page .status-banner--rejected {

    border-color: #f3c2cc;

    background: #fdecef;

    color: #8a1428;

}

.mhs-kamar-page .room-list {

    display: grid;

    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 14px;

    margin-top: 14px;

}

.mhs-kamar-page .room-item {

    display: flex;

    flex-direction: column;

    gap: 12px;

    border: 1px solid #e6dcd6;

    border-radius: 11px;

    padding: 16px;

    background: #fff;

}

.mhs-kamar-page .room-item.is-selected {

    border-color: var(--maroon);

    box-shadow: 0 0 0 2px rgba(121, 12, 32, .12);

}

.mhs-kamar-page .room-item-head {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    flex-wrap: wrap;

    gap: 10px;

}

.mhs-kamar-page .room-item-title {

    font-size: 17px;

    font-weight: 600;

    margin: 0;

}

.mhs-kamar-page .room-item-sub {

    color: var(--muted);

    font-size: 12px;

    margin: 2px 0 0;

}

.mhs-kamar-page .badge {

    border-radius: 20px;

    padding: 3px 10px;

    font-size: 11px;

    font-weight: 600;

    white-space: nowrap;

}

.mhs-kamar-page .badge-available {

    background: #d8f5e3;

    color: #008b42;

}

.mhs-kamar-page .badge-full {

    background: #fdecef;

    color: #8a1428;

}

.mhs-kamar-page .badge-pending {

    background: #fff4d6;

    color: #a16207;

}

.mhs-kamar-page .badge-closed {

    background: #eeeeee;

    color: #666666;

}

.mhs-kamar-page .room-item-specs {

    display: grid;

    grid-template-columns: repeat(3, minmax(0, 1fr));

    gap: 8px;

    font-size: 12px;

}

.mhs-kamar-page .room-item-specs strong {

    display: block;

    color: var(--muted);

    font-size: 10px;

    font-weight: 500;

    text-transform: uppercase;

    letter-spacing: .03em;

    margin-bottom: 2px;

}

.mhs-kamar-page .room-item-fasilitas {

    font-size: 12px;

    color: var(--muted);

    line-height: 1.5;

    margin: 0;

    overflow-wrap: break-word;

}

.mhs-kamar-page .btn-ajukan {

    width: 100%;

    border: none;

    border-radius: 8px;

    background: var(--maroon);

    color: #fff;

    font-family: inherit;

    font-size: 13px;

    font-weight: 600;

    padding: 10px 16px;

    cursor: pointer;

}

.mhs-kamar-page .btn-ajukan:hover {

    background: #5f0918;

}

.mhs-kamar-page .btn-ajukan[disabled] {

    background: #e5e0dd;

    color: #9a9a9a;

    cursor: not-allowed;

}

.mhs-kamar-page .empty-state {

    grid-column: 1 / -1;

    border: 1px dashed #d9cdc8;

    border-radius: 11px;

    padding: 24px;

    text-align: center;

    color: var(--muted);

    font-size: 13px;

    background: #faf7f8;

}

.mhs-kamar-page .room-toolbar {

    display: grid;

    grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr);

    gap: 10px;

    margin-top: 14px;

}

.mhs-kamar-page .form-control {

    width: 100%;

    border: 1px solid #e0d6d1;

    border-radius: 8px;

    padding: 9px 12px;

    font-family: inherit;

    font-size: 13px;

    color: #171717;

    background: #fff;

}

.mhs-kamar-page .form-control:focus {

    outline: none;

    border-color: var(--maroon);

    box-shadow: 0 0 0 2px rgba(121, 12, 32, .1);

}

.mhs-kamar-page textarea.form-control {

    resize: vertical;

    line-height: 1.6;

}

.mhs-kamar-page .room-photo {

    width: 100%;

    height: 150px;

    object-fit: cover;

    border-radius: 9px;

    display: block;

    background: #f4efec;

}

.mhs-kamar-page .room-photo--empty {

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 6px;

    border: 1px dashed #d9cdc8;

    color: var(--muted);

    font-size: 11px;

    text-align: center;

}

.mhs-kamar-page .room-photo--empty svg {

    width: 34px;

    height: 34px;

    stroke: #c9b9b2;

    fill: none;

    stroke-width: 1.5;

}

.mhs-kamar-page .room-galeri {

    position: relative;

    border-radius: 9px;

    overflow: hidden;

    background: #f4efec;

}

.mhs-kamar-page .room-galeri__foto {

    border-radius: 0;

}

.mhs-kamar-page .room-galeri__foto:not(.is-active) {

    display: none;

}

.mhs-kamar-page .room-galeri__nav {

    position: absolute;

    top: 50%;

    transform: translateY(-50%);

    width: 26px;

    height: 26px;

    border: none;

    border-radius: 50%;

    background: rgba(0, 0, 0, .55);

    color: #fff;

    font-size: 18px;

    line-height: 1;

    display: grid;

    place-items: center;

    cursor: pointer;

    z-index: 2;

}

.mhs-kamar-page .room-galeri__nav:hover {

    background: rgba(0, 0, 0, .78);

}

.mhs-kamar-page .room-galeri__nav--prev {

    left: 8px;

}

.mhs-kamar-page .room-galeri__nav--next {

    right: 8px;

}

.mhs-kamar-page .room-galeri__counter {

    position: absolute;

    right: 8px;

    bottom: 8px;

    padding: 2px 8px;

    border-radius: 999px;

    background: rgba(0, 0, 0, .6);

    color: #fff;

    font-size: 10px;

    font-weight: 600;

    z-index: 2;

}

.mhs-kamar-page .room-galeri__dots {

    display: flex;

    justify-content: center;

    gap: 5px;

    margin-top: 6px;

}

.mhs-kamar-page .room-galeri__dot {

    width: 7px;

    height: 7px;

    padding: 0;

    border: none;

    border-radius: 50%;

    background: #d8c6cb;

    cursor: pointer;

}

.mhs-kamar-page .room-galeri__dot.is-active {

    background: var(--maroon);

}

.mhs-kamar-page .room-item.is-current {

    border-color: var(--maroon);

    border-style: dashed;

    background: #fdf8f9;

}

.mhs-kamar-page .badge-current {

    background: #f7e6ea;

    color: var(--maroon);

}

.mhs-kamar-page .pindah-form {

    display: grid;

    gap: 6px;

    margin-top: 14px;

}

.mhs-kamar-page .form-label {

    font-size: 12px;

    font-weight: 600;

    color: #171717;

    margin-top: 6px;

}

.mhs-kamar-page .form-hint {

    font-size: 11px;

    color: var(--muted);

    margin: 0 0 4px;

    line-height: 1.5;

}

.mhs-kamar-page .pindah-form .btn-ajukan {

    margin-top: 10px;

}

.mhs-kamar-page .pindah-ringkas {

    display: grid;

    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 10px;

    margin-top: 14px;

    font-size: 12px;

}

.mhs-kamar-page .pindah-ringkas strong {

    display: block;

    color: var(--muted);

    font-size: 10px;

    font-weight: 500;

    text-transform: uppercase;

    letter-spacing: .03em;

    margin-bottom: 2px;

}

.mhs-kamar-page .catatan-penolakan {

    margin-top: 10px;

    padding: 10px 12px;

    border-radius: 8px;

    background: rgba(255, 255, 255, .72);

    border: 1px solid #f3c2cc;

    font-size: 12px;

    line-height: 1.6;

}

.mhs-kamar-page .room-item[hidden],

.mhs-kamar-page .empty-state[hidden] {

    display: none;

}

@media (max-width: 992px) {

.mhs-kamar-page .room-toolbar {

    grid-template-columns: repeat(2, minmax(0, 1fr));

}

.mhs-kamar-page .room-toolbar .form-control:first-child {

    grid-column: 1 / -1;

}

.mhs-kamar-page .detail-row {

    max-width: none;

}

}

@media (max-width: 820px) {

.mhs-kamar-page .room-list {

    grid-template-columns: 1fr;

}

.mhs-kamar-page .room-photo {

    height: 200px;

}

}

@media (max-width: 640px) {

.mhs-kamar-page .breadcrumb {

    font-size: 14px;

}

.mhs-kamar-page .page-title {

    font-size: 24px;

    line-height: 1.35;

}

.mhs-kamar-page .page-subtitle {

    font-size: 13.5px;

    line-height: 1.55;

    margin-bottom: 8px;

}

.mhs-kamar-page .alert,

.mhs-kamar-page .status-banner {

    padding: 12px 14px;

    font-size: 12.5px;

}

.mhs-kamar-page .status-banner strong {

    font-size: 14px;

}

.mhs-kamar-page .room-card {

    padding: 15px;

    margin-bottom: 16px;

}

.mhs-kamar-page .room-heading {

    margin-bottom: 12px;

}

.mhs-kamar-page .room-heading h2 {

    font-size: 17px;

}

.mhs-kamar-page .room-highlight {

    padding: 16px;

    margin-bottom: 12px;

}

.mhs-kamar-page .room-number {

    font-size: 24px;

    overflow-wrap: anywhere;

}

.mhs-kamar-page .room-grid {

    grid-template-columns: 1fr;

}

.mhs-kamar-page .room-info {

    padding: 12px 14px;

}

.mhs-kamar-page .detail-card {

    padding: 12px 14px 14px;

    margin-bottom: 16px;

}

.mhs-kamar-page .detail-row {

    padding: 8px 0 12px;

    margin-bottom: 0;

}

.mhs-kamar-page .room-toolbar {

    grid-template-columns: 1fr;

    gap: 8px;

    margin-top: 12px;

}

.mhs-kamar-page .form-control {

    font-size: 16px;

    padding: 10px 12px;

}

.mhs-kamar-page .room-item {

    padding: 14px;

    gap: 10px;

}

.mhs-kamar-page .room-item-head {

    gap: 8px;

}

.mhs-kamar-page .room-item-title {

    font-size: 16px;

}

.mhs-kamar-page .room-item-specs {

    grid-template-columns: repeat(2, minmax(0, 1fr));

}

.mhs-kamar-page .room-photo {

    height: 172px;

}

.mhs-kamar-page .btn-ajukan {

    padding: 11px 14px;

}

.mhs-kamar-page .pindah-ringkas {

    grid-template-columns: 1fr;

    gap: 8px;

}

.mhs-kamar-page .additional-card {

    padding: 14px;

}

.mhs-kamar-page .additional-card p {

    font-size: 12px;

    line-height: 1.6;

}

.mhs-kamar-page .empty-state {

    padding: 20px 14px;

}

}

@media (max-width: 400px) {

.mhs-kamar-page .room-item-specs {

    grid-template-columns: 1fr;

}

}

</style>

@endpush

@section('content')

<div class="mhs-kamar-page">

            <div class="breadcrumb">Dashboard / Informasi Kamar</div>

            <h1 class="page-title">Informasi Kamar</h1>

            <p class="page-subtitle">

                Informasi kamar Ma’had sesuai data yang dikelola Musyrifah.

            </p>

            @php

                $pengajuanPindahMenunggu = $pengajuanPindah && $pengajuanPindah->masihMenunggu();

                // Sesuai model pengajuan dua persetujuan terbaru.
                // Status tetap "menunggu" sampai keputusan akhir dari Musyrifah tujuan.
                $menungguMusyrifahTujuan = $pengajuanPindahMenunggu
                    && ! is_null($pengajuanPindah->persetujuan_asal_oleh);

                $kamarPindahAsal = $pengajuanPindah?->kamarAsal;
                $kamarPindahTujuan = $pengajuanPindah?->kamar;
                $pindahLintasWilayah = $kamarPindahAsal && $kamarPindahTujuan
                    && (mb_strtolower(trim((string) $kamarPindahAsal->gedung))
                        !== mb_strtolower(trim((string) $kamarPindahTujuan->gedung))
                        || (int) $kamarPindahAsal->lantai !== (int) $kamarPindahTujuan->lantai);

                $pengajuanPindahDitolak = $pengajuanPindah

                    && $pengajuanPindah->status === \App\Models\PengajuanKamar::STATUS_DITOLAK;

                $daftarLantai = collect($daftarKamar)->pluck('lantai')->unique()->sort()->values();

            @endphp

            @if ($kamarSaya)

                <section class="room-card">

                    <div class="room-heading">

                        <h2>Kamar Saya</h2>

                        <span class="status">Telah Ditetapkan</span>

                    </div>

                    <div class="room-highlight">

                        <svg viewBox="0 0 24 24">

                            <path d="M3 18V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v11"/>

                            <path d="M12 11h7a2 2 0 0 1 2 2v5"/>

                            <path d="M3 14h18M3 18v2M21 18v2"/>

                        </svg>

                        <div class="room-label">Nomor Kamar</div>

                        <div class="room-number">

                            {{ $kamarSaya->nomor_kamar ?: 'Belum ditetapkan' }}

                        </div>

                        <div class="muted">

                            {{ $kamarSaya->nama_kamar ?: 'Kamar '.$kamarSaya->nomor_kamar }}

                            &bull; Penempatan ditetapkan oleh Musyrifah Ma’had.

                        </div>

                    </div>

                    <div class="room-grid">

                        <div class="room-info">

                            <svg viewBox="0 0 24 24">

                                <path d="M3 21V5h12v16M15 10h6v11M1 21h22"/>

                            </svg>

                            <div class="info-label">Gedung / Lantai</div>

                            <div class="info-value">

                                {{ $kamarSaya->gedung ?: 'Gedung A' }}

                                @if ($kamarSaya->lantai)

                                    / Lantai {{ $kamarSaya->lantai }}

                                @endif

                            </div>

                        </div>

                        <div class="room-info">

                            <svg viewBox="0 0 24 24">

                                <circle cx="9" cy="7" r="3"/>

                                <path d="M3 20v-2a6 6 0 0 1 12 0v2"/>

                                <circle cx="18" cy="8" r="2.5"/>

                                <path d="M17 14a5 5 0 0 1 4 5"/>

                            </svg>

                            <div class="info-label">Kapasitas Kamar</div>

                            <div class="info-value">

                                {{ (int) $kamarSaya->kapasitas }} orang

                                ({{ $kamarSaya->jumlahPenghuni() }} terisi)

                            </div>

                        </div>

                    </div>

                </section>

                <section class="detail-card">

                    <h2>Detail Penempatan</h2>

                    <div class="detail-row">

                        <div class="detail-label">Nama Penghuni</div>

                        <div class="detail-value">{{ auth()->user()->name }}</div>

                    </div>

                    <div class="detail-row">

                        <div class="detail-label">NIM</div>

                        <div class="detail-value">{{ auth()->user()->nim ?: '-' }}</div>

                    </div>

                    <div class="detail-row">

                        <div class="detail-label">Fasilitas Kamar</div>

                        <div class="detail-value">{{ $kamarSaya->fasilitas ?: 'Belum ada rincian fasilitas.' }}</div>

                    </div>

                    <div class="detail-row">

                        <div class="detail-label">Status Penempatan</div>

                        <div class="detail-value">Telah Ditetapkan</div>

                    </div>

                </section>

                @if ($pengajuanPindahMenunggu)

                    <div class="status-banner">

                        @if ($menungguMusyrifahTujuan)
                            <strong>Tahap 1 selesai — menunggu persetujuan Musyrifah tujuan.</strong>
                            Musyrifah asal sudah menyetujui mahasiswa keluar dari kamar asal.
                            Pengajuan sekarang diperiksa oleh Musyrifah gedung dan lantai tujuan.
                            @if ($pengajuanPindah->persetujuan_asal_pada)
                                <div class="persetujuan-detail">
                                    Persetujuan asal: {{ $pengajuanPindah->persetujuan_asal_pada->translatedFormat('d M Y H:i') }}
                                </div>
                            @endif
                        @elseif ($pindahLintasWilayah)
                            <strong>Tahap 1 — menunggu persetujuan Musyrifah asal.</strong>
                            Setelah Musyrifah asal menyetujui, pengajuan akan diteruskan kepada
                            Musyrifah tujuan untuk keputusan akhir.
                        @else
                            <strong>Menunggu verifikasi Musyrifah wilayah kamar.</strong>
                            Perpindahan dalam gedung dan lantai yang sama cukup melalui satu persetujuan.
                        @endif

                        Kamar asal <strong>Kamar {{ $pengajuanPindah->kamarAsal?->nomor_kamar ?? $kamarSaya->nomor_kamar }}</strong>

                        ke kamar tujuan <strong>Kamar {{ $pengajuanPindah->kamar?->nomor_kamar ?? '-' }}</strong>

                        (diajukan {{ optional($pengajuanPindah->created_at)->translatedFormat('d M Y H:i') }}).

                        Kamu tetap menempati kamar asal sampai persetujuan yang diperlukan selesai
                        dan sistem memperbarui penempatanmu.

                        @if ($pengajuanPindah->alasan)

                            <div class="catatan-penolakan" style="background:#fff;border-color:#f0d9a8;">

                                <strong>Alasan perpindahan:</strong> {{ $pengajuanPindah->alasan }}

                            </div>

                        @endif

                    </div>

                @elseif ($pengajuanPindahDitolak)

                    <div class="status-banner status-banner--rejected">

                        <strong>Pengajuan pindah kamar ditolak.</strong>

                        Kamu tetap menempati kamar saat ini

                        (Kamar {{ $kamarSaya->nomor_kamar }}).

                        <div class="catatan-penolakan">

                            <strong>Catatan Musyrifah:</strong>

                            {{ $pengajuanPindah->catatan ?: 'Tidak ada catatan penolakan.' }}

                        </div>

                    </div>

                @endif

                @unless ($pengajuanPindahMenunggu)

                    <section class="room-card" id="pengajuan-pindah">

                        <div class="room-heading">

                            <h2>Pengajuan Pindah Kamar</h2>

                            <span class="status">Butuh Verifikasi</span>

                        </div>

                        <p class="muted" style="margin: 0;">

                            Pilih kamar tujuan yang masih memiliki sisa kapasitas dan tuliskan
                            alasan perpindahan. Untuk pindah ke gedung atau lantai lain,
                            diperlukan persetujuan Musyrifah asal lalu Musyrifah tujuan.
                            Jika masih di gedung dan lantai yang sama, cukup satu persetujuan.
                            Kamar asal tetap menjadi penempatanmu selama verifikasi.

                        </p>

                        @php

                            $kamarTujuanTersedia = collect($daftarKamar)

                                ->filter(fn ($kamar) => $kamar['id'] !== (int) $kamarSaya->id && $kamar['bisa_diajukan']);

                        @endphp

                        <form method="POST" action="{{ route('kamar.pindah') }}" class="pindah-form">

                            @csrf

                            <label class="form-label" for="kamarTujuan">Kamar Tujuan</label>

                            <select

                                class="form-control"

                                name="kamar_id"

                                id="kamarTujuan"

                                required

                                @disabled($kamarTujuanTersedia->isEmpty())

                            >

                                <option value="">— Pilih kamar tujuan —</option>

                                @foreach ($kamarTujuanTersedia as $kamar)

                                    <option value="{{ $kamar['id'] }}" @selected((string) old('kamar_id') === (string) $kamar['id'])>

                                        Kamar {{ $kamar['nomor'] }} — {{ $kamar['gedung'] }}, Lantai {{ $kamar['lantai'] }}

                                        (sisa {{ $kamar['sisa'] }} tempat)

                                    </option>

                                @endforeach

                            </select>

                            <p class="form-hint">

                                @if ($kamarTujuanTersedia->isEmpty())

                                    Belum ada kamar lain yang memiliki sisa kapasitas saat ini.

                                @else

                                    Hanya kamar dengan sisa kapasitas yang dapat diajukan.

                                @endif

                            </p>

                            <label class="form-label" for="alasanPindah">Alasan Perpindahan</label>

                            <textarea

                                class="form-control"

                                name="alasan"

                                id="alasanPindah"

                                rows="4"

                                maxlength="1000"

                                required

                                placeholder="Contoh: kamar saat ini terlalu padat dan sulit beristirahat."

                            >{{ old('alasan') }}</textarea>

                            <p class="form-hint">Wajib diisi, minimal 10 karakter.</p>

                            <button

                                type="submit"

                                class="btn-ajukan"

                                @disabled($kamarTujuanTersedia->isEmpty())

                            >

                                Kirim Pengajuan Pindah

                            </button>

                        </form>

                    </section>

                @endunless

            @else

                @if ($pengajuanSaya)

                    @if ($pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU)

                        <div class="status-banner">

                            <strong>Pengajuan kamar sedang menunggu persetujuan.</strong>

                            Kamar pilihanmu:

                            <strong>Kamar {{ $pengajuanSaya->kamar?->nomor_kamar ?? '-' }}</strong>

                            (diajukan {{ optional($pengajuanSaya->created_at)->translatedFormat('d M Y H:i') }}).

                            Kamu masih dapat mengubah pilihan kamar selama belum disetujui Musyrifah.

                        </div>

                    @elseif ($pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_DITOLAK)

                        <div class="status-banner status-banner--rejected">

                            <strong>Pengajuan kamar sebelumnya ditolak.</strong>

                            Silakan ajukan pilihan kamar lain di bawah ini.

                            @if ($pengajuanSaya->catatan)

                                <div class="catatan-penolakan">

                                    <strong>Catatan Musyrifah:</strong> {{ $pengajuanSaya->catatan }}

                                </div>

                            @endif

                        </div>

                    @endif

                @endif

            @endif

            <section class="room-card">

                <div class="room-heading">

                    <h2>Daftar Kamar Ma’had</h2>

                    <span class="status">{{ count($daftarKamar) }} Kamar</span>

                </div>

                <p class="muted" style="margin: 0;">

                    Data kamar mengikuti pengelolaan Musyrifah. Gunakan pencarian cepat

                    berdasarkan nomor kamar atau fasilitas, serta filter lantai dan status

                    ketersediaan.

                </p>

                <div class="room-toolbar">

                    <input

                        type="search"

                        id="cariKamar"

                        class="form-control"

                        placeholder="Cari nomor kamar atau fasilitas..."

                        aria-label="Cari nomor kamar atau fasilitas"

                    >

                    <select id="filterLantai" class="form-control" aria-label="Filter lantai kamar">

                        <option value="">Semua Lantai</option>

                        @foreach ($daftarLantai as $lantai)

                            <option value="{{ $lantai }}">Lantai {{ $lantai }}</option>

                        @endforeach

                    </select>

                    <select id="filterStatus" class="form-control" aria-label="Filter status ketersediaan kamar">

                        <option value="">Semua Status</option>

                        <option value="tersedia">Tersedia</option>

                        <option value="penuh">Penuh</option>

                        <option value="lainnya">Tidak Tersedia</option>

                    </select>

                </div>

                <div class="room-list" id="daftarKamarList">

                    @forelse ($daftarKamar as $kamar)

                        @php

                            $kamarSaatIni = $kamarSaya && (int) $kamarSaya->id === (int) $kamar['id'];

                            $statusKamar = strtolower(trim((string) $kamar['status']));

                            $kategoriStatus = $kamar['bisa_diajukan']

                                ? 'tersedia'

                                : (in_array($statusKamar, ['dalam perbaikan', 'tidak tersedia', 'nonaktif'], true) ? 'lainnya' : 'penuh');

                            $sedangDiajukan = ! $kamarSaya

                                && $pengajuanSaya

                                && $pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU

                                && (int) $pengajuanSaya->kamar_id === (int) $kamar['id'];

                            $sedangDiproses = ! $kamarSaya

                                && $pengajuanSaya

                                && $pengajuanSaya->status === \App\Models\PengajuanKamar::STATUS_MENUNGGU

                                && ! $sedangDiajukan;

                            $kataKunci = strtolower(trim(

                                $kamar['nomor'].' '.($kamar['nama'] ?? '').' '.$kamar['gedung'].' '.($kamar['fasilitas'] ?? '')

                            ));

                        @endphp

                        <div

                            class="room-item {{ $sedangDiajukan ? 'is-selected' : '' }} {{ $kamarSaatIni ? 'is-current' : '' }}"

                            data-lantai="{{ $kamar['lantai'] }}"

                            data-status="{{ $kategoriStatus }}"

                            data-kata="{{ $kataKunci }}"

                        >

                            @php

                                $fotosKamar = $kamar['fotos'] ?? [];

                            @endphp

                            @if (! empty($fotosKamar))

                                <div class="room-galeri" data-galeri>

                                    @foreach ($fotosKamar as $indexFoto => $foto)

                                        <img

                                            class="room-photo room-galeri__foto {{ $indexFoto === 0 ? 'is-active' : '' }}"

                                            src="{{ $foto['url'] }}"

                                            alt="Foto {{ $indexFoto + 1 }} Kamar {{ $kamar['nomor'] }}"

                                            loading="lazy"

                                            data-galeri-slide

                                        >

                                    @endforeach

                                    @if (count($fotosKamar) > 1)

                                        <button type="button" class="room-galeri__nav room-galeri__nav--prev" data-galeri-prev aria-label="Foto sebelumnya">&#8249;</button>

                                        <button type="button" class="room-galeri__nav room-galeri__nav--next" data-galeri-next aria-label="Foto berikutnya">&#8250;</button>

                                        <span class="room-galeri__counter" data-galeri-counter>1 / {{ count($fotosKamar) }}</span>

                                    @endif

                                </div>

                                @if (count($fotosKamar) > 1)

                                    <div class="room-galeri__dots">

                                        @foreach ($fotosKamar as $indexFoto => $foto)

                                            <button

                                                type="button"

                                                class="room-galeri__dot {{ $indexFoto === 0 ? 'is-active' : '' }}"

                                                data-galeri-dot="{{ $indexFoto }}"

                                                aria-label="Tampilkan foto {{ $indexFoto + 1 }}"

                                            ></button>

                                        @endforeach

                                    </div>

                                @endif

                            @else

                                <div class="room-photo room-photo--empty">

                                    <svg viewBox="0 0 24 24">

                                        <path d="M3 18V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v11"/>

                                        <path d="M12 11h7a2 2 0 0 1 2 2v5"/>

                                        <path d="M3 14h18M3 18v2M21 18v2"/>

                                    </svg>

                                    <span>Foto kamar belum tersedia</span>

                                </div>

                            @endif

                            <div class="room-item-head">

                                <div>

                                    <h3 class="room-item-title">Kamar {{ $kamar['nomor'] }}</h3>

                                    <p class="room-item-sub">

                                        {{ $kamar['gedung'] }} &bull; Lantai {{ $kamar['lantai'] }}

                                    </p>

                                </div>

                                @if ($kamarSaatIni)

                                    <span class="badge badge-current">Kamar Saat Ini</span>

                                @elseif ($sedangDiajukan)

                                    <span class="badge badge-pending">Diajukan</span>

                                @elseif ($kamar['bisa_diajukan'])

                                    <span class="badge badge-available">Tersedia</span>

                                @elseif ($kategoriStatus === 'penuh')

                                    <span class="badge badge-full">Penuh</span>

                                @else

                                    <span class="badge badge-closed">{{ $kamar['status'] }}</span>

                                @endif

                            </div>

                            <div class="room-item-specs">

                                <div><strong>Kapasitas</strong>{{ $kamar['kapasitas'] }} orang</div>

                                <div><strong>Penghuni</strong>{{ $kamar['terisi'] }} orang</div>

                                <div><strong>Sisa Kapasitas</strong>{{ $kamar['sisa'] }} tempat</div>

                            </div>

                            <p class="room-item-fasilitas">

                                <strong>Fasilitas:</strong>

                                {{ $kamar['fasilitas'] ?: 'Belum ada rincian fasilitas.' }}

                            </p>

                            @if ($kamarSaatIni)

                                <button type="button" class="btn-ajukan" disabled>Kamar Saat Ini</button>

                            @elseif ($kamarSaya)

                                <button

                                    type="button"

                                    class="btn-ajukan"

                                    data-kamar-pindah="{{ $kamar['id'] }}"

                                    @disabled(! $kamar['bisa_diajukan'] || $pengajuanPindahMenunggu)

                                >

                                    @if ($pengajuanPindahMenunggu)

                                        Menunggu Verifikasi

                                    @elseif ($kamar['bisa_diajukan'])

                                        Pilih untuk Pindah

                                    @else

                                        Tidak Tersedia

                                    @endif

                                </button>

                            @else

                                <form method="POST" action="{{ route('kamar.pilih') }}" style="margin-top: auto;">

                                    @csrf

                                    <input type="hidden" name="kamar_id" value="{{ $kamar['id'] }}">

                                    <button type="submit" class="btn-ajukan" @disabled(! $kamar['bisa_diajukan'] || $sedangDiajukan)>

                                        @if ($sedangDiajukan)

                                            Sedang Diajukan

                                        @elseif ($kamar['bisa_diajukan'])

                                            {{ $sedangDiproses ? 'Ganti ke Kamar Ini' : 'Ajukan Kamar Ini' }}

                                        @else

                                            Tidak Tersedia

                                        @endif

                                    </button>

                                </form>

                            @endif

                        </div>

                    @empty

                        <div class="empty-state">

                            Belum ada data kamar dari Musyrifah Ma’had.

                        </div>

                    @endforelse

                    <div class="empty-state" id="kamarKosong" hidden>

                        Tidak ada kamar yang sesuai dengan pencarian atau filter.

                    </div>

                </div>

            </section>

            <section class="additional-card">

                <div class="additional-heading">

                    <svg viewBox="0 0 24 24">

                        <rect x="5" y="4" width="14" height="17" rx="2"/>

                        <path d="M9 4V2h6v2M9 10h6M9 14h6M9 18h3"/>

                    </svg>

                    Informasi Tambahan

                </div>

                <p>

                    Penempatan pertama diverifikasi Musyrifah wilayah kamar tujuan.
                    Pindah lintas gedung atau lantai diverifikasi Musyrifah asal
                    lalu Musyrifah tujuan. Selama pengajuan belum disetujui, penempatan kamar

                    kamu tidak berubah.

                </p>

        </section>

    </div>

@endsection

@push('scripts')

<script>

        (function () {

            const inputCari = document.getElementById('cariKamar');

            const filterLantai = document.getElementById('filterLantai');

            const filterStatus = document.getElementById('filterStatus');

            const daftarKamar = document.getElementById('daftarKamarList');

            const pesanKosong = document.getElementById('kamarKosong');

            if (daftarKamar) {

                const semuaKamar = Array.from(daftarKamar.querySelectorAll('.room-item'));

                const terapkanFilter = function () {

                    const kata = (inputCari && inputCari.value ? inputCari.value : '').toLowerCase().trim();

                    const lantai = filterLantai ? filterLantai.value : '';

                    const status = filterStatus ? filterStatus.value : '';

                    let jumlahTampil = 0;

                    semuaKamar.forEach(function (item) {

                        const cocokKata = !kata || (item.dataset.kata || '').includes(kata);

                        const cocokLantai = !lantai || item.dataset.lantai === lantai;

                        const cocokStatus = !status || item.dataset.status === status;

                        const tampil = cocokKata && cocokLantai && cocokStatus;

                        item.hidden = !tampil;

                        if (tampil) {

                            jumlahTampil += 1;

                        }

                    });

                    if (pesanKosong) {

                        pesanKosong.hidden = jumlahTampil > 0 || semuaKamar.length === 0;

                    }

                };

                if (inputCari) {

                    inputCari.addEventListener('input', terapkanFilter);

                }

                if (filterLantai) {

                    filterLantai.addEventListener('change', terapkanFilter);

                }

                if (filterStatus) {

                    filterStatus.addEventListener('change', terapkanFilter);

                }

            }

            // Tombol "Pilih untuk Pindah" mengisi form pengajuan pindah kamar.

            const pilihanTujuan = document.getElementById('kamarTujuan');

            document.querySelectorAll('[data-kamar-pindah]').forEach(function (tombol) {

                tombol.addEventListener('click', function () {

                    if (! pilihanTujuan) {

                        return;

                    }

                    pilihanTujuan.value = tombol.dataset.kamarPindah;

                    document.getElementById('pengajuan-pindah')

                        .scrollIntoView({ behavior: 'smooth', block: 'start' });

                    document.getElementById('alasanPindah')

                        .focus({ preventScroll: true });

                });

            });

            // Galeri foto: tampilkan foto kamar satu per satu.

            document.querySelectorAll('[data-galeri]').forEach(function (galeri) {

                const slides = Array.from(galeri.querySelectorAll('[data-galeri-slide]'));

                if (slides.length === 0) {

                    return;

                }

                const counter = galeri.querySelector('[data-galeri-counter]');

                const dots = Array.from(galeri.closest('.room-item').querySelectorAll('[data-galeri-dot]'));

                let indeks = 0;

                const tampilkan = function (baru) {

                    indeks = (baru + slides.length) % slides.length;

                    slides.forEach(function (slide, i) {

                        slide.classList.toggle('is-active', i === indeks);

                    });

                    dots.forEach(function (dot, i) {

                        dot.classList.toggle('is-active', i === indeks);

                    });

                    if (counter) {

                        counter.textContent = (indeks + 1) + ' / ' + slides.length;

                    }

                };

                galeri.querySelector('[data-galeri-prev]')?.addEventListener('click', function () {

                    tampilkan(indeks - 1);

                });

                galeri.querySelector('[data-galeri-next]')?.addEventListener('click', function () {

                    tampilkan(indeks + 1);

                });

                dots.forEach(function (dot) {

                    dot.addEventListener('click', function () {

                        tampilkan(parseInt(dot.dataset.galeriDot, 10) || 0);

                    });

                });

            });

        })();

    </script>

@endpush
