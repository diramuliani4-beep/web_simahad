@extends('layouts.mahasiswa')

@section('title', 'Pembayaran')

@section('content')

<style>
    .bayar-page {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--ink);
        min-width: 0;
    }

    .bayar-page *,
    .bayar-page *::before,
    .bayar-page *::after {
        box-sizing: border-box;
    }

    .bayar-page .page-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 16px;
    }

    .bayar-page .page-head h1 {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .bayar-page .page-head p {
        margin: 4px 0 0;
        max-width: 640px;
        font-size: 0.82rem;
        line-height: 1.6;
        color: var(--muted);
    }

    .bayar-page .periode-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 13px;
        border-radius: 999px;
        border: 1px solid var(--line);
        background: #fff;
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--maroon);
        white-space: nowrap;
    }

    .bayar-page .info-note {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
        padding: 12px 14px;
        border: 1px solid #e8d8a8;
        border-radius: 12px;
        background: #fdf7e6;
        font-size: 0.79rem;
        line-height: 1.6;
        color: #6d5410;
    }

    .bayar-page .info-note svg {
        flex-shrink: 0;
        margin-top: 2px;
    }

    /* Ringkasan */
    .bayar-page .ringkasan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .bayar-page .stat-card {
        min-width: 0;
        padding: 14px 16px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(59, 7, 15, .04);
    }

    .bayar-page .stat-card .stat-label {
        display: block;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .bayar-page .stat-card .stat-nilai {
        display: block;
        margin-top: 5px;
        font-size: 1.22rem;
        font-weight: 800;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .bayar-page .stat-card .stat-sub {
        display: block;
        margin-top: 3px;
        font-size: 0.72rem;
        color: var(--muted);
    }

    .bayar-page .stat-card--utama {
        border-color: #e3c9cf;
        background: linear-gradient(135deg, #fff 0%, var(--maroon-tint) 100%);
    }

    .bayar-page .stat-card--utama .stat-nilai {
        color: var(--maroon);
    }

    .bayar-page .progres {
        height: 6px;
        margin-top: 9px;
        border-radius: 999px;
        background: #f0e2e5;
        overflow: hidden;
    }

    .bayar-page .progres span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--maroon), var(--gold));
    }

    /* Rekening */
    .bayar-page .rekening-card {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        padding: 16px 18px;
        border: 1px solid #d9c8c1;
        border-radius: 16px;
        background: #fffdfc;
    }

    .bayar-page .rekening-info {
        min-width: 0;
        flex: 1 1 260px;
    }

    .bayar-page .rekening-bank {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        background: var(--maroon-tint);
        color: var(--maroon);
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .bayar-page .rekening-nomor {
        margin: 8px 0 2px;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: .06em;
        overflow-wrap: anywhere;
    }

    .bayar-page .rekening-nama {
        font-size: 0.78rem;
        line-height: 1.55;
        color: var(--muted);
        overflow-wrap: anywhere;
    }

    .bayar-page .btn-salin {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 42px;
        padding: 10px 18px;
        border: none;
        border-radius: 10px;
        background: var(--maroon);
        color: #fff;
        font-family: inherit;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .16s ease;
    }

    .bayar-page .btn-salin:hover {
        background: var(--maroon-dark);
    }

    .bayar-page .btn-salin.is-copied {
        background: #15803d;
    }

    /* Angsuran */
    .bayar-page .bagian-judul {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin: 0 0 10px;
    }

    .bayar-page .bagian-judul h2 {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 800;
    }

    .bayar-page .bagian-judul span {
        font-size: 0.74rem;
        color: var(--muted);
    }

    .bayar-page .angsuran-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .bayar-page .angsuran-card {
        display: flex;
        flex-direction: column;
        min-width: 0;
        padding: 15px 16px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(59, 7, 15, .04);
    }

    .bayar-page .angsuran-card.is-lunas {
        border-color: #cfe6d5;
        background: #fbfdfb;
    }

    .bayar-page .angsuran-card.is-terlambat {
        border-color: #f2ccd2;
    }

    .bayar-page .angsuran-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .bayar-page .angsuran-tahap {
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--maroon);
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .bayar-page .angsuran-nominal {
        margin: 6px 0 2px;
        font-size: 1.3rem;
        font-weight: 800;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .bayar-page .angsuran-meta {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 8px 10px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--line);
    }

    .bayar-page .meta-label {
        display: block;
        font-size: 0.66rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #9b8a8e;
    }

    .bayar-page .meta-nilai {
        display: block;
        margin-top: 2px;
        font-size: 0.79rem;
        font-weight: 600;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .bayar-page .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .bayar-page .badge.lunas {
        background: #dcfce7;
        color: #15803d;
    }

    .bayar-page .badge.menunggu {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .bayar-page .badge.rektorat {
        background: #ede9fe;
        color: #5b21b6;
    }

    .bayar-page .badge.ditolak,
    .bayar-page .badge.terlambat {
        background: #fee2e2;
        color: #b91c1c;
    }

    .bayar-page .badge.belum {
        background: #fef3c7;
        color: #92400e;
    }

    .bayar-page .badge.verifikasi-menunggu {
        background: #e0f2fe;
        color: #075985;
    }

    .bayar-page .badge.verifikasi-diterima {
        background: #dcfce7;
        color: #166534;
    }

    .bayar-page .badge.verifikasi-ditolak {
        background: #fee2e2;
        color: #991b1b;
    }

    .bayar-page .badge.verifikasi-kosong {
        background: #f3f4f6;
        color: #4b5563;
    }

    .bayar-page .catatan-verifikasi {
        margin-top: 10px;
        padding: 9px 11px;
        border-left: 3px solid #f0a6b1;
        border-radius: 6px;
        background: #fdf2f4;
        font-size: 0.76rem;
        line-height: 1.55;
        color: #8a1224;
        overflow-wrap: anywhere;
    }

    .bayar-page .bukti-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 8px;
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--maroon);
        overflow-wrap: anywhere;
    }

    .bayar-page .bukti-link:hover {
        text-decoration: underline;
    }

    .bayar-page .aksi-baris {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .bayar-page .btn-aksi {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 40px;
        padding: 9px 14px;
        border-radius: 9px;
        border: 1px solid var(--maroon);
        background: var(--maroon);
        color: #fff;
        font-family: inherit;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .16s ease, color .16s ease;
    }

    .bayar-page .btn-aksi:hover {
        background: var(--maroon-dark);
    }

    .bayar-page .btn-aksi--garis {
        background: #fff;
        color: var(--maroon);
    }

    .bayar-page .btn-aksi--garis:hover {
        background: var(--maroon-tint);
    }

    .bayar-page .form-sembunyi {
        display: none;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed var(--line);
    }

    .bayar-page .form-sembunyi.terbuka {
        display: block;
    }

    .bayar-page .form-field {
        margin-bottom: 9px;
    }

    .bayar-page .form-field label {
        display: block;
        margin-bottom: 4px;
        font-size: 0.73rem;
        font-weight: 700;
        color: var(--text-secondary);
    }

    .bayar-page .form-field input,
    .bayar-page .form-field textarea {
        width: 100%;
        min-height: 40px;
        padding: 9px 11px;
        border: 1px solid #e0d5d0;
        border-radius: 9px;
        background: #fff;
        font-family: inherit;
        font-size: 0.82rem;
        color: var(--ink);
    }

    .bayar-page .form-field textarea {
        min-height: 74px;
        resize: vertical;
    }

    .bayar-page .form-field input:focus,
    .bayar-page .form-field textarea:focus {
        outline: 2px solid rgba(107, 13, 27, .18);
        border-color: var(--maroon);
    }

    .bayar-page .hint {
        margin-top: 4px;
        font-size: 0.7rem;
        line-height: 1.5;
        color: var(--muted);
    }

    /* Riwayat */
    .bayar-page .riwayat-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 12px;
    }

    .bayar-page .panel {
        min-width: 0;
        padding: 15px 16px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #fff;
    }

    .bayar-page .panel h2 {
        margin: 0 0 10px;
        font-size: 0.92rem;
        font-weight: 800;
    }

    .bayar-page .riwayat-item {
        padding: 10px 0;
        border-top: 1px dashed var(--line);
    }

    .bayar-page .riwayat-item:first-of-type {
        border-top: none;
        padding-top: 0;
    }

    .bayar-page .riwayat-atas {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }

    .bayar-page .riwayat-nama {
        font-size: 0.8rem;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .bayar-page .riwayat-sub {
        margin-top: 3px;
        font-size: 0.73rem;
        line-height: 1.5;
        color: var(--muted);
        overflow-wrap: anywhere;
    }

    .bayar-page .kosong {
        padding: 18px 4px;
        text-align: center;
        font-size: 0.78rem;
        color: var(--muted);
    }

    .bayar-page .langkah {
        margin: 0;
        padding-left: 18px;
        font-size: 0.79rem;
        line-height: 1.75;
        color: var(--text-secondary);
    }

    .bayar-page .mati {
        padding: 34px 18px;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: #fff;
        text-align: center;
    }

    .bayar-page .mati h2 {
        margin: 0 0 6px;
        font-size: 1rem;
        font-weight: 800;
    }

    .bayar-page .mati p {
        margin: 0 auto;
        max-width: 460px;
        font-size: 0.82rem;
        line-height: 1.65;
        color: var(--muted);
    }

    @media (max-width: 992px) {
        .bayar-page .riwayat-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 640px) {
        .bayar-page .page-head h1 {
            font-size: 1.15rem;
        }

        .bayar-page .angsuran-grid,
        .bayar-page .ringkasan-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .bayar-page .angsuran-meta {
            grid-template-columns: minmax(0, 1fr);
        }

        .bayar-page .rekening-nomor {
            font-size: 1.2rem;
        }

        .bayar-page .btn-salin,
        .bayar-page .btn-aksi {
            width: 100%;
        }

        .bayar-page .periode-chip {
            width: 100%;
            justify-content: center;
        }
    }
</style>

@php
    $pengaturan = $ringkasan['pengaturan'];
    $dibuka = (bool) $ringkasan['dibuka'];
    $rekening = $ringkasan['rekening'];
    $daftarAngsuran = $ringkasan['daftar_angsuran'];
    $lunasRiwayat = collect($daftarAngsuran)->where('status', 'lunas');
@endphp

<div class="bayar-page">
    <div class="page-head">
        <div>
            <h1>Pembayaran Mahasiswa</h1>
            <p>Tagihan Ma'had dibayar maksimal tiga angsuran. Seluruh transfer dilakukan langsung ke rekening resmi kampus.</p>
        </div>

        @if ($pengaturan)
            <span class="periode-chip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Periode {{ $pengaturan->periode }}
            </span>
        @endif
    </div>

    <div class="info-note">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>
        <span>
            Transfer langsung ke rekening resmi kampus. SIMAHAD tidak menerima atau mengelola dana.
            Status <strong>Lunas</strong> ditetapkan setelah penerimaan dana dikonfirmasi kampus/Rektorat, bukan otomatis setelah bukti diunggah.
        </span>
    </div>

    @if (! $dibuka)
        <div class="mati">
            <h2>Periode Pembayaran</h2>
            <p>Belum ada periode pembayaran aktif. Silakan menunggu Musyrifah membuka periode pembayaran.</p>
            @if ($pengaturan)
                <p>
                    Periode <strong>{{ $pengaturan->periode }}</strong> akan dibuka pada
                    <strong>{{ $pengaturan->periodeDibukaTeks() }}</strong>. Tagihan dan rekening transfer tampil setelah periode dibuka.
                </p>
            @endif
        </div>
    @else
        <div class="ringkasan-grid">
            <div class="stat-card stat-card--utama">
                <span class="stat-label">Total Tagihan</span>
                <span class="stat-nilai">{{ $ringkasan['total_rupiah'] }}</span>
                <span class="stat-sub">{{ $daftarAngsuran->count() }} angsuran &middot; kategori {{ ucfirst($ringkasan['kategori']) }}</span>
            </div>

            <div class="stat-card">
                <span class="stat-label">Sudah Dibayar</span>
                <span class="stat-nilai">{{ $ringkasan['dibayar_rupiah'] }}</span>
                <span class="stat-sub">{{ $ringkasan['progres'] }}% dari total tagihan</span>
                <div class="progres"><span style="width: {{ $ringkasan['progres'] }}%"></span></div>
            </div>

            <div class="stat-card">
                <span class="stat-label">Sisa Tagihan</span>
                <span class="stat-nilai">{{ $ringkasan['sisa_rupiah'] }}</span>
                <span class="stat-sub">
                    @if ($ringkasan['menunggu'] > 0)
                        {{ $ringkasan['menunggu_rupiah'] }} menunggu pemeriksaan/konfirmasi
                    @else
                        Belum termasuk tagihan yang menunggu konfirmasi
                    @endif
                </span>
            </div>

            <div class="stat-card">
                <span class="stat-label">Status Keseluruhan</span>
                <span class="stat-nilai" style="font-size:1rem;">
                    {{ $ringkasan['lunas'] ? 'Lunas' : 'Belum Lunas' }}
                </span>
                <span class="stat-sub">
                    @if ($ringkasan['jatuh_tempo_terdekat'])
                        Jatuh tempo terdekat: {{ $ringkasan['jatuh_tempo_terdekat']['jatuh_tempo'] }}
                    @else
                        Semua angsuran sudah dikonfirmasi
                    @endif
                </span>
            </div>
        </div>

        <div class="rekening-card">
            <div class="rekening-info">
                <span class="rekening-bank">{{ $rekening['bank'] }} &middot; Rekening Resmi</span>
                <div class="rekening-nomor" id="nomorRekening">{{ $rekening['nomor'] }}</div>
                <div class="rekening-nama">Atas nama: <strong>{{ $rekening['nama'] }}</strong></div>
            </div>

            <button type="button" class="btn-salin" id="btnSalinRekening" data-nomor="{{ $rekening['nomor'] }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                <span id="labelSalinRekening">Salin Nomor Rekening</span>
            </button>
        </div>

        <div class="bagian-judul">
            <h2>Rincian Angsuran</h2>
            <span>Maksimal 3 angsuran per periode</span>
        </div>

        <div class="angsuran-grid">
            @foreach ($daftarAngsuran as $angsuran)
                @php
                    $kelasKartu = $angsuran['status'] === 'lunas' ? 'is-lunas' : ($angsuran['terlambat'] ? 'is-terlambat' : '');
                @endphp

                <article class="angsuran-card {{ $kelasKartu }}">
                    <div class="angsuran-head">
                        <span class="angsuran-tahap">{{ $angsuran['label'] }}</span>
                        <span class="badge {{ $angsuran['kelas_status'] }}">{{ $angsuran['status_label'] }}</span>
                    </div>

                    <div class="angsuran-nominal">{{ $angsuran['nominal_rupiah'] }}</div>

                    <div class="angsuran-meta">
                        <div>
                            <span class="meta-label">Jatuh Tempo</span>
                            <span class="meta-nilai">{{ $angsuran['jatuh_tempo'] }}</span>
                        </div>
                        <div>
                            <span class="meta-label">Sisa Hari</span>
                            <span class="meta-nilai">
                                @if ($angsuran['sisa_hari'] === null)
                                    -
                                @elseif ($angsuran['sisa_hari'] < 0)
                                    Lewat {{ abs($angsuran['sisa_hari']) }} hari
                                @elseif ($angsuran['sisa_hari'] === 0)
                                    Jatuh tempo hari ini
                                @else
                                    {{ $angsuran['sisa_hari'] }} hari lagi
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="meta-label">Pemeriksaan Bukti</span>
                            <span class="meta-nilai">
                                <span class="badge verifikasi-{{ $angsuran['kelas_verifikasi'] }}">{{ $angsuran['verifikasi_label'] }}</span>
                            </span>
                        </div>
                        <div>
                            <span class="meta-label">Konfirmasi Dana</span>
                            <span class="meta-nilai">{{ $angsuran['tanggal_konfirmasi'] ?? '-' }}</span>
                        </div>
                    </div>

                    @if (filled($angsuran['catatan_verifikasi']))
                        <div class="catatan-verifikasi">Catatan Musyrifah: {{ $angsuran['catatan_verifikasi'] }}</div>
                    @endif

                    @if ($angsuran['bukti_url'])
                        <a class="bukti-link" href="{{ $angsuran['bukti_url'] }}" target="_blank" rel="noopener">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                            </svg>
                            Lihat bukti transfer{{ $angsuran['tanggal_upload'] ? ' ('.$angsuran['tanggal_upload'].')' : '' }}
                        </a>
                    @endif

                    @if ($angsuran['pengajuan_menunggu'])
                        <div class="catatan-verifikasi" style="border-left-color:#93c5fd;background:#eff6ff;color:#1d4ed8;">
                            Pengajuan keterlambatan sedang menunggu pemeriksaan Musyrifah.
                        </div>
                    @endif

                    @if ($angsuran['dapat_unggah'] || $angsuran['dapat_ajukan'])
                        <div class="aksi-baris">
                            @if ($angsuran['dapat_unggah'])
                                <button type="button" class="btn-aksi" data-toggle-form="form-bukti-{{ $angsuran['id'] }}">
                                    Unggah Bukti
                                </button>
                            @endif

                            @if ($angsuran['dapat_ajukan'] && ! $angsuran['pengajuan_menunggu'])
                                <button type="button" class="btn-aksi btn-aksi--garis" data-toggle-form="form-tunda-{{ $angsuran['id'] }}">
                                    Ajukan Keterlambatan
                                </button>
                            @endif
                        </div>

                        @if ($angsuran['dapat_unggah'])
                            <form class="form-sembunyi"
                                id="form-bukti-{{ $angsuran['id'] }}"
                                action="{{ route('pembayaran.bukti', $angsuran['id']) }}"
                                method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-field">
                                    <label for="bukti-{{ $angsuran['id'] }}">Bukti transfer {{ $angsuran['label'] }}</label>
                                    <input type="file" id="bukti-{{ $angsuran['id'] }}" name="bukti" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div class="hint">PDF/JPG/PNG maksimal 5 MB. Pastikan nominal {{ $angsuran['nominal_rupiah'] }} dan tujuan rekening terlihat jelas.</div>
                                </div>
                                <button type="submit" class="btn-aksi">Kirim Bukti</button>
                            </form>
                        @endif

                        @if ($angsuran['dapat_ajukan'] && ! $angsuran['pengajuan_menunggu'])
                            <form class="form-sembunyi"
                                id="form-tunda-{{ $angsuran['id'] }}"
                                action="{{ route('pembayaran.keterlambatan', $angsuran['id']) }}"
                                method="POST">
                                @csrf
                                <div class="form-field">
                                    <label for="alasan-{{ $angsuran['id'] }}">Alasan keterlambatan</label>
                                    <textarea id="alasan-{{ $angsuran['id'] }}" name="alasan" minlength="10" maxlength="1000" required placeholder="Contoh: dana kiriman orang tua baru masuk awal bulan depan."></textarea>
                                </div>
                                <div class="form-field">
                                    <label for="rencana-{{ $angsuran['id'] }}">Tanggal rencana pembayaran</label>
                                    <input type="date" id="rencana-{{ $angsuran['id'] }}" name="tanggal_rencana_bayar" min="{{ now()->addDay()->toDateString() }}" max="{{ now()->addMonths(6)->toDateString() }}" required>
                                    <div class="hint">Surat pengajuan PDF dibuat otomatis dan diperiksa Musyrifah. Persetujuan hanya memperbarui jadwal, status tetap belum Lunas.</div>
                                </div>
                                <button type="submit" class="btn-aksi">Kirim Pengajuan</button>
                            </form>
                        @endif
                    @endif
                </article>
            @endforeach
        </div>

        <div class="riwayat-grid">
            <div class="panel">
                <h2>Riwayat Pembayaran</h2>

                @forelse ($lunasRiwayat as $riwayat)
                    <div class="riwayat-item">
                        <div class="riwayat-atas">
                            <span class="riwayat-nama">{{ $riwayat['label'] }} &middot; {{ $riwayat['nominal_rupiah'] }}</span>
                            <span class="badge lunas">Lunas</span>
                        </div>
                        <div class="riwayat-sub">
                            Dikonfirmasi Rektorat: {{ $riwayat['tanggal_konfirmasi'] ?? $riwayat['tanggal_bayar'] ?? '-' }}
                        </div>
                    </div>
                @empty
                    <div class="kosong">Belum ada angsuran yang dikonfirmasi Lunas.</div>
                @endforelse

                <div class="riwayat-sub" style="margin-top:12px;">
                    Tagihan yang sudah diunggah buktinya tetap berstatus menunggu sampai dana dikonfirmasi Rektorat.
                </div>
            </div>

            <div class="panel">
                <h2>Riwayat Pengajuan Keterlambatan</h2>

                @forelse ($ringkasan['pengajuan'] as $pengajuan)
                    <div class="riwayat-item">
                        <div class="riwayat-atas">
                            <span class="riwayat-nama">{{ $pengajuan->nomor_surat }}</span>
                            <span class="badge {{ $pengajuan->kelasStatus() === 'disetujui' ? 'lunas' : ($pengajuan->kelasStatus() === 'ditolak' ? 'ditolak' : 'menunggu') }}">
                                {{ $pengajuan->statusLabel() }}
                            </span>
                        </div>
                        <div class="riwayat-sub">
                            {{ $pengajuan->pembayaran?->labelTahap() ?? 'Tagihan' }} &middot;
                            rencana bayar {{ $pengajuan->tanggalRencanaTeks() }}
                        </div>
                        @if (filled($pengajuan->catatan_musyrifah))
                            <div class="riwayat-sub">Catatan Musyrifah: {{ $pengajuan->catatan_musyrifah }}</div>
                        @endif
                        <a class="bukti-link" href="{{ route('pembayaran.keterlambatan.surat', $pengajuan->id) }}" target="_blank" rel="noopener">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Lihat Surat PDF
                        </a>
                    </div>
                @empty
                    <div class="kosong">Belum ada pengajuan keterlambatan.</div>
                @endforelse
            </div>
        </div>

        <div class="panel" style="margin-top:12px;">
            <h2>Cara Pembayaran</h2>
            <ol class="langkah">
                <li>Salin nomor rekening {{ $rekening['bank'] }} resmi kampus di atas.</li>
                <li>Transfer sesuai nominal angsuran melalui ATM, mobile banking, atau teller {{ $rekening['bank'] }}.</li>
                <li>Simpan bukti transfer, lalu unggah pada angsuran yang dibayar.</li>
                <li>Tunggu pemeriksaan bukti oleh Musyrifah dan konfirmasi penerimaan dana oleh Rektorat.</li>
            </ol>
        </div>
    @endif
</div>

@push('scripts')
<script>
    (function () {
        const tombolSalin = document.getElementById('btnSalinRekening');

        if (tombolSalin) {
            const label = document.getElementById('labelSalinRekening');

            tombolSalin.addEventListener('click', async function () {
                const nomor = tombolSalin.dataset.nomor || '';

                if (!nomor) {
                    return;
                }

                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(nomor);
                    } else {
                        const area = document.createElement('textarea');
                        area.value = nomor;
                        area.setAttribute('readonly', 'readonly');
                        area.style.position = 'fixed';
                        area.style.opacity = '0';
                        document.body.appendChild(area);
                        area.select();
                        document.execCommand('copy');
                        document.body.removeChild(area);
                    }

                    tombolSalin.classList.add('is-copied');
                    label.textContent = 'Nomor Tersalin';

                    window.setTimeout(function () {
                        tombolSalin.classList.remove('is-copied');
                        label.textContent = 'Salin Nomor Rekening';
                    }, 2000);
                } catch (error) {
                    label.textContent = 'Gagal menyalin, salin manual';
                }
            });
        }

        document.querySelectorAll('[data-toggle-form]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                const form = document.getElementById(tombol.dataset.toggleForm);

                if (!form) {
                    return;
                }

                const terbuka = form.classList.toggle('terbuka');
                tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');

                if (terbuka) {
                    const fokus = form.querySelector('input, textarea');
                    if (fokus) {
                        fokus.focus({ preventScroll: true });
                    }
                }
            });
        });
    })();
</script>
@endpush

@endsection
