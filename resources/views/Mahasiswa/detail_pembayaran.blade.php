@extends('layouts.mahasiswa')

@section('title', 'Detail Pembayaran')

@section('content')

<style>
    .detail-bayar {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #111827;
    }

    .detail-bayar * ,
    .detail-bayar *::before,
    .detail-bayar *::after {
        box-sizing: border-box;
    }

    .detail-bayar .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 14px;
        font-size: 0.83rem;
        font-weight: 600;
        color: #6b7280;
        text-decoration: none;
    }

    .detail-bayar .back-link:hover {
        color: var(--primary-maroon);
    }

    .detail-bayar .layout {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .detail-bayar .card {
        border: 1px solid #eadfda;
        border-radius: 14px;
        background: #fff;
        padding: 20px 22px;
    }

    .detail-bayar .card + .card {
        margin-top: 16px;
    }

    .detail-bayar .card-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 14px;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .detail-bayar .card-title svg {
        color: var(--primary-maroon);
    }

    .detail-bayar .periode-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--primary-maroon);
    }

    .detail-bayar .nominal {
        margin: 6px 0 2px;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.15;
        color: #111827;
        word-break: break-word;
    }

    .detail-bayar .tahap {
        font-size: 0.88rem;
        font-weight: 600;
        color: #4b5563;
    }

    .detail-bayar .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 12px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
    }

    .detail-bayar .badge.belum {
        background: #fef3c7;
        color: #92400e;
    }

    .detail-bayar .badge.menunggu {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .detail-bayar .badge.terlambat {
        background: #fee2e2;
        color: #b91c1c;
    }

    .detail-bayar .badge.lunas {
        background: #dcfce7;
        color: #15803d;
    }

    .detail-bayar .meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px dashed #eee4df;
    }

    .detail-bayar .meta-label {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #9ca3af;
    }

    .detail-bayar .meta-value {
        margin-top: 2px;
        font-size: 0.88rem;
        font-weight: 600;
        color: #1f2937;
    }

    /* VIRTUAL ACCOUNT */
    .detail-bayar .va-box {
        border: 1px solid #d9c8c1;
        border-radius: 12px;
        background: #fffdfc;
        padding: 16px 18px;
        text-align: center;
    }

    .detail-bayar .va-bank {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        background: var(--maroon-tint);
        color: var(--primary-maroon);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .detail-bayar .va-nomor {
        margin: 10px 0 12px;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #111827;
        word-break: break-all;
    }

    .detail-bayar .btn-copy {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        padding: 10px 16px;
        border: none;
        border-radius: 10px;
        background: var(--primary-maroon);
        color: #fff;
        font-family: inherit;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
    }

    .detail-bayar .btn-copy:hover {
        background: var(--maroon-dark);
    }

    .detail-bayar .btn-copy.is-copied {
        background: #15803d;
    }

    .detail-bayar .va-note {
        margin-top: 10px;
        font-size: 0.75rem;
        line-height: 1.55;
        color: #6b7280;
    }

    .detail-bayar .alert {
        padding: 12px 14px;
        border-radius: 10px;
        font-size: 0.83rem;
        font-weight: 600;
        line-height: 1.6;
    }

    .detail-bayar .alert-info {
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .detail-bayar .alert-warning {
        border: 1px solid #fcd34d;
        background: #fffbeb;
        color: #92400e;
    }

    .detail-bayar .alert-danger {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .detail-bayar .alert-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .detail-bayar .alert + .alert {
        margin-top: 10px;
    }

    .detail-bayar .info-line {
        margin-top: 9px;
        font-size: 0.78rem;
        line-height: 1.6;
        color: #6b7280;
    }

    .detail-bayar .link-halaman {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 14px;
        padding: 9px 15px;
        border: 1px solid var(--primary-maroon);
        border-radius: 9px;
        color: var(--primary-maroon);
        font-size: 0.83rem;
        font-weight: 700;
        text-decoration: none;
    }

    .detail-bayar .link-halaman:hover {
        background: var(--maroon-tint);
    }

    .detail-bayar .belum-dibuka {
        text-align: center;
        padding: 18px 4px;
    }

    .detail-bayar .belum-dibuka svg {
        color: var(--gold);
        margin-bottom: 10px;
    }

    .detail-bayar .belum-dibuka h2 {
        margin: 0 0 6px;
        font-size: 1.05rem;
        font-weight: 800;
    }

    .detail-bayar .belum-dibuka p {
        margin: 0 auto;
        max-width: 380px;
        font-size: 0.86rem;
        line-height: 1.65;
        color: #6b7280;
    }

    @media (max-width: 992px) {
        .detail-bayar .layout {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 480px) {
        .detail-bayar .meta-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .detail-bayar .nominal {
            font-size: 1.65rem;
        }
    }
</style>

@php
    $pengaturan = $ringkasan['pengaturan'] ?? null;
    $dibuka = (bool) ($ringkasan['dibuka'] ?? false);
    $status = $ringkasan['status'] ?? 'belum_dibuka';
@endphp

<div class="detail-bayar">
    <a href="{{ route('pembayaran') }}" class="back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"></path>
        </svg>
        Kembali ke halaman pembayaran
    </a>

    <div class="page-heading">
        <h1>Detail Pembayaran</h1>
        <p>Rincian tagihan periode pembayaran aktif dan nomor Virtual Account BSI Ma'had.</p>
    </div>

    @if (! $dibuka)
        <div class="card">
            <div class="belum-dibuka">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <h2>Periode pembayaran berikutnya belum dimulai</h2>
                <p>
                    @if ($pengaturan)
                        Periode <strong>{{ $pengaturan->periode }}</strong> ({{ $pengaturan->nama_tahap }})
                        akan dibuka pada <strong>{{ $pengaturan->periodeDibukaTeks() }}</strong>.
                        Nomor Virtual Account dan nominal tagihan akan tampil setelah periode dibuka.
                    @else
                        Belum ada periode pembayaran yang dibuka oleh pengelola Ma'had. Silakan periksa kembali secara berkala.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="layout">
            <div>
                <div class="card">
                    <div class="periode-label">Periode {{ $pengaturan->periode }}</div>
                    <div class="nominal">{{ $ringkasan['nominal_rupiah'] }}</div>
                    <div class="tahap">{{ $ringkasan['tahap'] }}</div>
                    <span class="badge {{ $ringkasan['kelas_status'] }}">{{ $ringkasan['label_status'] }}</span>

                    <div class="meta-grid">
                        <div>
                            <div class="meta-label">Tahap Pembayaran</div>
                            <div class="meta-value">{{ $ringkasan['tahap'] }}</div>
                        </div>
                        <div>
                            <div class="meta-label">Tanggal Jatuh Tempo</div>
                            <div class="meta-value">{{ $ringkasan['jatuh_tempo'] }}</div>
                        </div>
                        <div>
                            <div class="meta-label">Periode Dibuka</div>
                            <div class="meta-value">{{ $pengaturan->periodeDibukaTeks() }}</div>
                        </div>
                        <div>
                            <div class="meta-label">Sisa Hari</div>
                            <div class="meta-value">
                                @if ($ringkasan['sisa_hari'] === null)
                                    -
                                @elseif ($ringkasan['sisa_hari'] < 0)
                                    Lewat {{ abs($ringkasan['sisa_hari']) }} hari
                                @elseif ($ringkasan['sisa_hari'] === 0)
                                    Jatuh tempo hari ini
                                @else
                                    {{ $ringkasan['sisa_hari'] }} hari lagi
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($status === 'lunas')
                        <div class="alert alert-success" style="margin-top:16px;">
                            Pembayaran Anda telah dikonfirmasi Lunas oleh Rektorat. Pengingat pembayaran dihentikan.
                        </div>
                    @elseif ($status === 'terlambat')
                        <div class="alert alert-danger" style="margin-top:16px;">
                            Tagihan ini telah melewati jatuh tempo {{ $ringkasan['jatuh_tempo'] }}. Segera lakukan pembayaran melalui Virtual Account BSI.
                        </div>
                    @elseif ($status === 'menunggu')
                        <div class="alert alert-info" style="margin-top:16px;">
                            Pembayaran Anda sedang menunggu konfirmasi Rektorat. Status akan berubah menjadi Lunas setelah rekap pembayaran resmi diterima.
                        </div>
                    @elseif ($ringkasan['pengingat'])
                        <div class="alert alert-danger" style="margin-top:16px;">
                            Jatuh tempo tinggal {{ $ringkasan['sisa_hari'] }} hari lagi ({{ $ringkasan['jatuh_tempo'] }}). Segera lakukan pembayaran.
                        </div>
                    @else
                        <div class="alert alert-warning" style="margin-top:16px;">
                            Tagihan periode ini belum dibayar. Lakukan pembayaran sebelum {{ $ringkasan['jatuh_tempo'] }}.
                        </div>
                    @endif
                </div>

                <div class="card">
                    <h2 class="card-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 16v-4"></path>
                            <path d="M12 8h.01"></path>
                        </svg>
                        Cara Pembayaran
                    </h2>
                    <div style="font-size:0.85rem; line-height:1.75; color:#374151;">
                        1. Salin nomor Virtual Account BSI Ma'had di samping.<br>
                        2. Lakukan transfer melalui ATM, mobile banking, atau teller BSI.<br>
                        3. Simpan bukti transfer.<br>
                        4. Status tagihan berubah menjadi <strong>Lunas</strong> setelah Rektorat mengonfirmasi rekap pembayaran resmi.
                    </div>
                    <div class="info-line">
                        Ma'had menggunakan satu nomor Virtual Account BSI yang sama untuk seluruh mahasiswa.
                        Nomor ini bukan nomor pribadi dan tidak dibuat otomatis per mahasiswa.
                    </div>
                </div>
            </div>

            <div>
                <div class="card">
                    <h2 class="card-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        Virtual Account
                    </h2>

                    @if (filled($ringkasan['va_nomor']))
                        <div class="va-box">
                            <span class="va-bank">{{ $pengaturan->va_bank ?: 'BSI' }} &middot; Virtual Account</span>
                            <div class="va-nomor" id="vaNomor">{{ $ringkasan['va_nomor'] }}</div>

                            <button type="button" class="btn-copy" id="btnSalinVa" data-va="{{ $ringkasan['va_nomor'] }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                                <span id="btnSalinVaLabel">Salin Nomor VA</span>
                            </button>

                            <div class="va-note">
                                Atas nama: <strong>Ma'had Al-Jami'ah UIN Imam Bonjol</strong><br>
                                Nominal: <strong>{{ $ringkasan['nominal_rupiah'] }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Nomor Virtual Account belum tersedia. Hubungi pengelola Ma'had.
                        </div>
                    @endif
                </div>

                @if ($ringkasan['tagihan'])
                    <div class="card">
                        <h2 class="card-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Data Tagihan
                        </h2>
                        <div class="meta-label">Nama Tagihan</div>
                        <div class="meta-value">{{ $ringkasan['tagihan']->nama_tagihan }}</div>
                        <div class="meta-label" style="margin-top:10px;">Kode Transaksi</div>
                        <div class="meta-value">{{ $ringkasan['tagihan']->kode_transaksi }}</div>
                        <div class="meta-label" style="margin-top:10px;">Status Tercatat</div>
                        <div class="meta-value">{{ $ringkasan['tagihan']->status_label }}</div>

                        @if ($ringkasan['tagihan']->tanggal_bayar)
                            <div class="meta-label" style="margin-top:10px;">Tanggal Konfirmasi</div>
                            <div class="meta-value">{{ $ringkasan['tagihan']->tanggal_bayar->locale('id')->translatedFormat('d F Y, H:i') }} WIB</div>
                        @endif
                    </div>
                @endif

                <a href="{{ route('pembayaran') }}" class="link-halaman">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
                        <path d="M16 3H4a2 2 0 0 0-2 2v2"></path>
                    </svg>
                    Buka Halaman Pembayaran
                </a>
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    (function () {
        const tombol = document.getElementById('btnSalinVa');

        if (! tombol) {
            return;
        }

        const label = document.getElementById('btnSalinVaLabel');

        tombol.addEventListener('click', async function () {
            const nomor = tombol.dataset.va || '';

            if (! nomor) {
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

                tombol.classList.add('is-copied');
                label.textContent = 'Nomor VA Tersalin';

                setTimeout(function () {
                    tombol.classList.remove('is-copied');
                    label.textContent = 'Salin Nomor VA';
                }, 2000);
            } catch (error) {
                label.textContent = 'Gagal menyalin, salin manual';
            }
        });
    })();
</script>
@endpush
