<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Status Pendaftaran - SIMAHAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {

            --maroon: #6e001b;

            --maroon-hover: #580015;

            --maroon-border: #7c1a2e;

            --text-dark: #111827;

            --text-muted: #64748b;

        }

        body {

            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 2.5rem 1rem;

            position: relative;

            background-image: url('{{ asset("images/mahad_al_jamiah.jpeg") }}');

            background-size: cover;

            background-position: center;

            background-attachment: fixed;

        }

        .page-overlay {

            position: fixed;

            inset: 0;

            z-index: 0;

            background: linear-gradient(135deg, rgba(230,240,255,.48), rgba(255,255,255,.68) 52%, rgba(255,255,255,.83));

            backdrop-filter: blur(3px);

        }

        .card {

            position: relative;

            z-index: 1;

            width: 100%;

            max-width: 740px;

            padding: 2.6rem 3rem;

            background: #fff;

            border: 1.5px solid var(--maroon-border);

            border-radius: 16px;

            box-shadow: 0 12px 36px rgba(110,0,27,.08);

            text-align: center;

        }

        .status-icon {

            width: 62px;

            height: 62px;

            border-radius: 50%;

            margin: 0 auto 1.1rem;

            display: grid;

            place-items: center;

            background: #dcfce7;

            color: #16a34a;

            border: 1.5px solid #86efac;

        }

        .status-icon.rejected { background: #fee2e2; color: #b42318; border-color: #fca5a5; }

        h1 { color: var(--maroon); font-size: 1.45rem; font-weight: 800; letter-spacing: -.02em; line-height: 1.35; }

        .subtitle { color: #555f70; font-size: .85rem; line-height: 1.65; margin: .45rem auto 0; max-width: 600px; }

        .divider { height: 1px; background: #e5e7eb; margin: 1.5rem 0; }

        .alert { text-align: left; border-radius: 11px; padding: 1.1rem 1.25rem; margin: 1.3rem 0 1.5rem; }

        .alert.waiting { background: #fff4cb; border: 1px solid #f5d777; color: #92400e; }

        .alert.approved { background: #e6f6eb; border: 1px solid #b5e6c5; color: #166534; }

        .alert.rejected { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; }

        .alert-title { display: flex; align-items: center; gap: .6rem; font-weight: 700; font-size: .92rem; }

        .alert-desc { font-size: .83rem; line-height: 1.65; padding-left: 1.65rem; margin-top: .35rem; }

        .heading-small { text-align: left; color: var(--text-dark); font-size: .9rem; font-weight: 700; margin-bottom: .9rem; }

        .steps { list-style: none; text-align: left; display: grid; gap: .7rem; margin-bottom: 1.7rem; }

        .steps li { display: flex; align-items: flex-start; gap: .65rem; color: #374151; font-size: .83rem; line-height: 1.6; }

        .check { width: 19px; height: 19px; flex: none; display: grid; place-items: center; background: #dcfce7; border-radius: 50%; color: #16a34a; font-weight: 800; font-size: .75rem; margin-top: 1px; }

        .summary { margin: 1.35rem 0 1.6rem; padding: .75rem 1rem; background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 11px; text-align: left; }

        .summary-row { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; padding: .55rem 0; font-size: .8rem; }

        .summary-row + .summary-row { border-top: 1px solid #e5e7eb; }

        .summary-label { color: #667085; }

        .summary-value { color: #202635; font-weight: 700; text-align: right; overflow-wrap: anywhere; }

        .status-panel { border: 1px solid #e0b3bd; border-radius: 11px; text-align: left; overflow: hidden; margin: 1.55rem 0; }

        .status-head { display: flex; gap: .8rem; align-items: center; border-bottom: 1px solid #e5e7eb; padding: .85rem 1.05rem; }

        .status-head svg { color: #ad740c; flex: none; }

        .status-head .eyebrow { color: #374151; font-size: .69rem; text-transform: uppercase; font-weight: 700; }

        .status-head .status-name { font-size: .89rem; color: #92400e; font-weight: 700; margin-top: .15rem; }

        .status-detail { padding: .85rem 1.05rem; display: grid; gap: .75rem; }

        .status-detail .label { color: #64748b; font-size: .74rem; }

        .status-detail .value { font-size: .87rem; color: #252c36; font-weight: 700; margin-top: .15rem; overflow-wrap: anywhere; }

        .info-banner { text-align: left; background: #fff4df; border: 1px solid #f4cf9b; border-radius: 9px; padding: .95rem 1.05rem; margin: 1.25rem 0; color: #92400e; }

        .info-banner strong { font-size: .83rem; }

        .info-banner p { font-size: .77rem; line-height: 1.55; margin-top: .3rem; }

        .btn { width: 100%; min-height: 44px; display: flex; align-items: center; justify-content: center; border: 0; border-radius: 10px; padding: .7rem 1rem; background: var(--maroon); color: #fff; font: 700 .88rem 'Plus Jakarta Sans', system-ui, sans-serif; text-decoration: none; cursor: pointer; box-shadow: 0 4px 12px rgba(110,0,27,.15); transition: background .2s, transform .2s; }

        .btn:hover { background: var(--maroon-hover); transform: translateY(-1px); }

        .btn.outline { color: var(--maroon); background: #fff; border: 1px solid var(--maroon-border); box-shadow: none; }

        .btn.outline:hover { background: #faedf0; }

        .actions { display: flex; gap: 1rem; margin-top: 1.5rem; }

        .actions > * { flex: 1; min-width: 0; }

        .link-form { margin-top: 1rem; }

        .link-neutral { border: 0; background: transparent; color: var(--text-muted); font: 500 .82rem 'Plus Jakarta Sans', system-ui, sans-serif; cursor: pointer; padding: .25rem; display: inline-block; text-decoration: none; }

        .link-neutral:hover { color: var(--maroon); text-decoration: underline; }

        @media (max-width: 640px) {

            .card { padding: 1.8rem 1.2rem; }

            h1 { font-size: 1.25rem; }

            .summary-row { align-items: flex-start; }

            .actions { flex-direction: column; gap: .7rem; }

            .actions > * { width: 100%; }

        }

    </style>

</head>

<body>

<div class="page-overlay"></div>

@php

    // Nilai ini hanya berasal dari database; mahasiswa tidak boleh mengirim status sendiri.

    $statusPendaftaran = $pendaftaran->status;

    $baruDikirim = (bool) ($baruDikirim ?? false);

    $nomorPendaftaran = str_pad((string) $pendaftaran->id, 6, '0', STR_PAD_LEFT);

@endphp

<main class="card">

    {{-- Tampilan pertama: tepat setelah formulir berhasil tersimpan. --}}

    @if ($baruDikirim && $statusPendaftaran === 'menunggu')

        <div class="status-icon" aria-hidden="true">

            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17 4 12"/></svg>

        </div>

        <h1>Pendaftaran Berhasil Dikirim!</h1>

        <p class="subtitle">Terima kasih, <strong>{{ $pendaftaran->nama_lengkap }}</strong>. Pendaftaran sebagai calon penghuni Ma'had Al-Jami'ah Tuanku Imam Bonjol berhasil dicatat.</p>

        <div class="divider"></div>

        <section class="alert waiting" aria-label="Status verifikasi">

            <div class="alert-title">

                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>

                <span>Menunggu Verifikasi</span>

            </div>

            <p class="alert-desc">Data pendaftaran dan bukti pembayaran sedang menunggu pemeriksaan pengelola Ma'had.</p>

        </section>

        <div class="summary" aria-label="Ringkasan pengajuan">

            <div class="summary-row"><span class="summary-label">Nomor Pendaftaran</span><span class="summary-value">#{{ $nomorPendaftaran }}</span></div>

            <div class="summary-row"><span class="summary-label">Tanggal Dikirim</span><span class="summary-value">{{ $pendaftaran->created_at?->format('d/m/Y H:i') ?? '-' }}</span></div>

            <div class="summary-row"><span class="summary-label">Pembayaran Awal Dilaporkan</span><span class="summary-value">Rp{{ number_format((float) $pendaftaran->nominal_pembayaran, 0, ',', '.') }}</span></div>

        </div>

        <h2 class="heading-small">Yang perlu kamu lakukan</h2>

        <ul class="steps">

            <li><span class="check">✓</span><span>Simpan nomor pendaftaran sebagai referensi saat menghubungi pengelola.</span></li>

            <li><span class="check">✓</span><span>Pantau hasil verifikasi pendaftaran secara berkala.</span></li>

            <li><span class="check">✓</span><span>Siapkan dokumen fisik jika diminta oleh pengelola Ma'had.</span></li>

        </ul>

        <a class="btn" href="{{ route('pendaftaran.konfirmasi', ['lihat' => 'status']) }}">Lihat Status Pendaftaran</a>

        <div class="link-form">
            <a class="link-neutral" href="{{ route('dashboard') }}">Kembali</a>
        </div>

    {{-- Status setelah divalidasi oleh pengelola. --}}

    @elseif ($statusPendaftaran === 'disetujui')

        <div class="status-icon" aria-hidden="true">

            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17 4 12"/></svg>

        </div>

        <h1>Pendaftaran Disetujui!</h1>

        <p class="subtitle">Selamat, <strong>{{ $pendaftaran->nama_lengkap }}</strong>. Pendaftaran sebagai calon penghuni Ma'had telah disetujui oleh pengelola.</p>

        <section class="alert approved" aria-label="Status disetujui">

            <div class="alert-title">Status Pendaftaran: Diterima</div>

            <p class="alert-desc" style="padding-left:0">Kamu dapat melanjutkan ke pemilihan kamar yang tersedia.</p>

        </section>

        <p class="subtitle" style="margin-bottom:1.5rem">Penempatan kamar tetap mengikuti ketersediaan dan ketentuan pengelola Ma'had.</p>

        <div class="actions">

            <a class="btn outline" href="{{ route('dashboard') }}">Kembali</a>

            <a class="btn" href="{{ route('kamar') }}">Lanjut Pilih Kamar</a>

        </div>

    @elseif ($statusPendaftaran === 'ditolak')

        <div class="status-icon rejected" aria-hidden="true">

            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/></svg>

        </div>

        <h1>Hasil Verifikasi Pendaftaran</h1>

        <p class="subtitle">Pengajuan atas nama <strong>{{ $pendaftaran->nama_lengkap }}</strong> telah diperiksa oleh pengelola Ma'had.</p>

        <section class="alert rejected" aria-label="Status ditolak">

            <div class="alert-title">Pendaftaran Belum Disetujui</div>

            <p class="alert-desc" style="padding-left:0">Silakan menghubungi pengelola Ma'had untuk mengetahui informasi dan langkah selanjutnya.</p>

        </section>

        <a class="btn" href="{{ route('dashboard') }}">Kembali</a>

    {{-- Semua kunjungan berikutnya saat status masih menunggu. --}}

    @else

        <h1>Status Pendaftaran</h1>

        <p class="subtitle">Pantau proses pendaftaranmu di Ma'had Al-Jami'ah Tuanku Imam Bonjol.</p>

        <section class="status-panel" aria-label="Rincian status pendaftaran">

            <div class="status-head">

                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>

                <div>

                    <div class="eyebrow">Status saat ini</div>

                    <div class="status-name">Menunggu Verifikasi</div>

                </div>

            </div>

            <div class="status-detail">

                <div><div class="label">Nama Pendaftar</div><div class="value">{{ $pendaftaran->nama_lengkap }}</div></div>

                <div><div class="label">Tahun Angkatan</div><div class="value">{{ $pendaftaran->tahun_angkatan }}</div></div>

                <div><div class="label">Nomor Pendaftaran</div><div class="value">#{{ $nomorPendaftaran }}</div></div>

                <div><div class="label">Keterangan</div><div class="value" style="font-weight:500">Data dan bukti pembayaran sedang diperiksa oleh pengelola Ma'had.</div></div>

            </div>

        </section>

        <aside class="info-banner">

            <strong>Informasi untuk kamu</strong>

            <p>Silakan pantau status secara berkala dan siapkan dokumen fisik untuk proses verifikasi jika diminta.</p>

        </aside>

        <a class="btn" href="{{ route('pendaftaran.konfirmasi', ['lihat' => 'status']) }}">Perbarui Status Pendaftaran</a>

        <div class="link-form">
            <a class="link-neutral" href="{{ route('dashboard') }}">Kembali</a>
        </div>

    @endif

</main>

</body>

</html>
