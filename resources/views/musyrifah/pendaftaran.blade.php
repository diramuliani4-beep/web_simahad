@extends('layouts.musyrifah')

@section('title', 'Pendaftaran Mahasiswa')

@section('content')
<style>
    /* Tampilan lama dipertahankan: maroon, pink muda, dan tabel lima kolom. */
    .pendaftaran-page {
        --pm-maroon: #800d22;
        --pm-maroon-dark: #650019;
        --pm-pink: #f5e6e9;
        font-family: 'Poppins', Arial, sans-serif;
        color: #111;
        width: 100%;
    }
    .pendaftaran-page * { box-sizing: border-box; }
    .pendaftaran-heading { display: flex; align-items: center; gap: 12px; margin: 0 0 22px; }
    .pendaftaran-back { display: inline-flex; align-items: center; justify-content: center; color: #111; font-size: 34px; line-height: 1; text-decoration: none; flex-shrink: 0; }
    .pendaftaran-back:hover { color: var(--pm-maroon); }
    .pendaftaran-title { font-size: 32px; font-weight: 700; line-height: 1.3; margin: 0; }
    .pendaftaran-summary { color: #5e5255; font-size: 14px; margin: 0 0 22px; }

    /* Notifikasi validasi dari controller. */
    .pendaftaran-alert { padding: 13px 16px; margin: 0 0 18px; border-radius: 12px; font-size: 14px; line-height: 1.6; }
    .pendaftaran-alert--success { color: #14532d; background: #dcfce7; border: 1px solid #86efac; }
    .pendaftaran-alert--error { color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; }
    .pendaftaran-alert ul { margin: 8px 0 0 20px; }

    /* Pencarian. */
    .pendaftaran-search-wrap { max-width: 620px; margin: 0 auto 35px; }
    .pendaftaran-search { display: flex; align-items: center; gap: 15px; width: 100%; height: 52px; padding: 0 20px; background: var(--pm-pink); border-radius: 13px; }
    .pendaftaran-search:focus-within { outline: 2px solid var(--pm-maroon); outline-offset: 2px; }
    .pendaftaran-search svg { width: 25px; height: 25px; flex-shrink: 0; color: #111; }
    .pendaftaran-search input { width: 100%; height: 100%; border: none; outline: none; background: transparent; color: #111; font-family: inherit; font-size: 17px; }
    .pendaftaran-search input::placeholder { color: #54464a; }

    /* Tabel pendaftaran. */
    .pendaftaran-table-wrap { width: 100%; overflow-x: auto; border-radius: 18px; box-shadow: 0 3px 8px rgba(0, 0, 0, .12); }
    .pendaftaran-table { width: 100%; min-width: 780px; border-collapse: separate; border-spacing: 0; background: var(--pm-pink); border-radius: 18px; }
    .pendaftaran-table thead th { padding: 15px 12px; background: var(--pm-maroon-dark); color: #fff; font-size: 16px; font-weight: 700; text-align: center; white-space: nowrap; }
    .pendaftaran-table thead th:first-child { border-top-left-radius: 15px; }
    .pendaftaran-table thead th:last-child { border-top-right-radius: 15px; }
    .pendaftaran-table tbody td { padding: 15px 12px; min-height: 68px; text-align: center; vertical-align: middle; font-size: 15px; border-right: 1px solid rgba(80, 50, 50, .18); border-bottom: 1px solid rgba(80, 50, 50, .25); }
    .pendaftaran-table tbody td:last-child { border-right: none; }
    .pendaftaran-table tbody tr:last-child td { border-bottom: none; }
    .pendaftaran-table tbody tr:hover { background: #f0dce1; }
    .pendaftaran-table th:nth-child(1) { width: 8%; }
    .pendaftaran-table th:nth-child(2) { width: 24%; }
    .pendaftaran-table th:nth-child(3) { width: 17%; }
    .pendaftaran-table th:nth-child(4) { width: 28%; }
    .pendaftaran-table th:nth-child(5) { width: 23%; }
    .pendaftaran-name { font-weight: 600; }
    .pendaftaran-status { display: inline-block; border-radius: 999px; padding: 5px 12px; font-size: 12px; font-weight: 700; }
    .pendaftaran-status--menunggu { color: #92400e; background: #fef3c7; }
    .pendaftaran-status--disetujui { color: #166534; background: #dcfce7; }
    .pendaftaran-status--ditolak { color: #991b1b; background: #fee2e2; }
    .pendaftaran-status--perbaikan { color: #c2410c; background: #ffedd5; border: 1px solid #fed7aa; }
    .pendaftaran-badge-kategori { display: inline-block; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 6px; text-transform: uppercase; margin-top: 4px; }
    .pendaftaran-badge-kategori--umum { background: #e0e7ff; color: #3730a3; }
    .pendaftaran-badge-kategori--kip { background: #fef3c7; color: #92400e; }
    .pendaftaran-validator-mini { display: block; margin-top: 7px; font-size: 12px; color: #655b5e; overflow-wrap: anywhere; }

    /* Aksi tabel / dialog. */
    .pendaftaran-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; border-radius: 9px; border: 1px solid transparent; padding: 10px 14px; font: 600 13px 'Poppins', Arial, sans-serif; text-decoration: none; cursor: pointer; transition: background .2s ease, transform .2s ease; }
    .pendaftaran-btn:hover { transform: translateY(-1px); }
    .pendaftaran-btn:focus-visible { outline: 3px solid #dbacb7; outline-offset: 2px; }
    .pendaftaran-btn--detail { background: var(--pm-maroon); color: #fff; }
    .pendaftaran-btn--detail:hover { background: #590018; }
    .pendaftaran-btn--approve { background: #166534; color: #fff; }
    .pendaftaran-btn--approve:hover { background: #14532d; }
    .pendaftaran-btn--revise { background: #d97706; color: #fff; }
    .pendaftaran-btn--revise:hover { background: #b45309; }
    .pendaftaran-btn--reject { background: #991b1b; color: #fff; }
    .pendaftaran-btn--reject:hover { background: #7f1d1d; }
    .pendaftaran-btn--outline { border-color: #c3a4ac; background: #fff; color: var(--pm-maroon); }
    .pendaftaran-btn--outline:hover { background: var(--pm-pink); }
    .pendaftaran-btn--document { background: #fcebef; color: var(--pm-maroon); border-color: #e3b6c1; }
    .pendaftaran-btn--document:hover { background: #f3d8df; }
    .pendaftaran-empty { display: none; padding: 24px; text-align: center; color: #6b5d61; }
    .pendaftaran-no-data { padding: 30px 12px !important; color: #6b5d61; }

    /* Modal detail: satu untuk setiap pendaftar, di luar tabel. */
    .pendaftaran-modal { width: min(860px, calc(100% - 28px)); max-height: min(90vh, 900px); margin: auto; padding: 0; border: none; border-radius: 18px; color: #222; box-shadow: 0 22px 65px rgba(0, 0, 0, .28); overflow: auto; }
    .pendaftaran-modal::backdrop { background: rgba(16, 10, 12, .6); }
    .pendaftaran-modal-content { font-family: 'Poppins', Arial, sans-serif; }
    .pendaftaran-modal-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 20px 24px; background: var(--pm-maroon-dark); color: white; }
    .pendaftaran-modal-head h2 { margin: 0 0 5px; font-size: 21px; }
    .pendaftaran-modal-head p { margin: 0; font-size: 13px; opacity: .92; }
    .pendaftaran-modal-close { display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(255, 255, 255, .6); border-radius: 50%; background: transparent; color: white; font-size: 25px; cursor: pointer; }
    .pendaftaran-modal-body { padding: 22px 24px 28px; }
    .pendaftaran-section { margin-bottom: 23px; }
    .pendaftaran-section-title { color: var(--pm-maroon); border-bottom: 1px solid #ecd8de; margin: 0 0 13px; padding-bottom: 8px; font-size: 17px; font-weight: 700; }
    .pendaftaran-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 13px 18px; }
    .pendaftaran-detail-cell { min-width: 0; padding: 12px 14px; border: 1px solid #eee0e4; border-radius: 9px; background: #fffafa; }
    .pendaftaran-detail-cell--full { grid-column: 1 / -1; }
    .pendaftaran-detail-label { display: block; margin-bottom: 5px; color: #6f6064; font-size: 12px; }
    .pendaftaran-detail-value { display: block; color: #25171b; font-size: 14px; font-weight: 600; overflow-wrap: anywhere; white-space: pre-line; }
    .pendaftaran-documents { display: flex; gap: 10px; flex-wrap: wrap; }
    .pendaftaran-notice { padding: 12px 14px; border-radius: 10px; background: #fff7ed; border: 1px solid #fed7aa; color: #92400e; font-size: 13px; line-height: 1.6; }
    .pendaftaran-action-area { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; padding-top: 16px; }
    .pendaftaran-action-form { padding: 16px; border: 1px solid #ead8dd; background: #fffafb; border-radius: 12px; display: flex; flex-direction: column; }
    .pendaftaran-action-form h4 { margin: 0 0 8px; color: #382329; font-size: 15px; }
    .pendaftaran-action-form p { margin: 0 0 12px; color: #6b5d61; font-size: 12px; line-height: 1.6; }
    .pendaftaran-action-form label { display: block; margin-bottom: 7px; font-size: 12px; font-weight: 600; }
    .pendaftaran-action-form textarea { display: block; width: 100%; min-height: 86px; resize: vertical; padding: 10px 12px; margin-bottom: 13px; border: 1px solid #cdb6bd; border-radius: 8px; font: 13px 'Poppins', Arial, sans-serif; }
    .pendaftaran-action-form textarea:focus { outline: 2px solid #af6174; outline-offset: 1px; }
    .pendaftaran-action-form .pendaftaran-btn { width: 100%; margin-top: auto; }
    .pendaftaran-finished { background: #f8f7f7; border: 1px solid #e9e4e5; border-radius: 12px; padding: 15px; color: #554b4e; font-size: 14px; }

    @media (max-width: 768px) {
        .pendaftaran-heading { gap: 8px; }
        .pendaftaran-title { font-size: 25px; }
        .pendaftaran-back { font-size: 28px; }
        .pendaftaran-search-wrap { margin-bottom: 24px; }
        .pendaftaran-search { height: 48px; padding: 0 14px; }
        .pendaftaran-search input { font-size: 16px; }
        .pendaftaran-modal-head { padding: 17px 16px; }
        .pendaftaran-modal-body { padding: 18px 16px; }
        .pendaftaran-action-area { grid-template-columns: 1fr; }
    }
    @media (max-width: 520px) {
        .pendaftaran-title { font-size: 21px; }
        .pendaftaran-detail-grid { grid-template-columns: 1fr; }
        .pendaftaran-detail-cell--full { grid-column: auto; }
        .pendaftaran-modal-head h2 { font-size: 18px; }
    }
</style>

<div class="pendaftaran-page">
    <div class="pendaftaran-heading">
        <a href="{{ route('musyrifah.dashboard') }}" class="pendaftaran-back" aria-label="Kembali ke dashboard" title="Kembali ke Dashboard">←</a>
        <h1 class="pendaftaran-title">Pendaftaran Mahasiswa</h1>
    </div>

    <p class="pendaftaran-summary">
        Total {{ $pendaftarans->count() }} pendaftar &bull;
        {{ $pendaftarans->where('status', 'menunggu')->count() }} menunggu validasi
        @if ($pendaftarans->where('status', 'perlu_perbaikan')->count() > 0)
            &bull; {{ $pendaftarans->where('status', 'perlu_perbaikan')->count() }} perlu perbaikan
        @endif
    </p>

    @if (session('success'))
        <div class="pendaftaran-alert pendaftaran-alert--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="pendaftaran-alert pendaftaran-alert--error" role="alert">
            @if (session('error'))
                <div>{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <strong>Periksa kembali data validasi:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="pendaftaran-search-wrap">
        <label class="pendaftaran-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m16 16 4 4"></path>
            </svg>
            <input type="search" id="cariMahasiswa" placeholder="Cari Mahasiswa" aria-label="Cari mahasiswa berdasarkan nama, NIM, status atau validator">
        </label>
    </div>

    <div class="pendaftaran-table-wrap">
        <table class="pendaftaran-table">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama & Kategori</th>
                    <th scope="col">NIM</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody id="daftarPendaftar">
                @forelse ($pendaftarans as $index => $pendaftaran)
                    <tr class="baris-pendaftar">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="pendaftaran-name">{{ $pendaftaran->nama_lengkap ?: '-' }}</div>
                            <span class="pendaftaran-badge-kategori pendaftaran-badge-kategori--{{ ($pendaftaran->kategori ?? 'umum') === 'kip' ? 'kip' : 'umum' }}">
                                {{ ($pendaftaran->kategori ?? 'umum') === 'kip' ? 'Mahasiswa KIP' : 'Mahasiswa Umum' }}
                            </span>
                        </td>
                        <td>{{ $pendaftaran->user?->nim ?: '-' }}</td>
                        <td>
                            @if ($pendaftaran->status === 'disetujui')
                                <span class="pendaftaran-status pendaftaran-status--disetujui">Disetujui</span>
                            @elseif ($pendaftaran->status === 'ditolak')
                                <span class="pendaftaran-status pendaftaran-status--ditolak">Ditolak</span>
                            @elseif ($pendaftaran->status === 'perlu_perbaikan')
                                <span class="pendaftaran-status pendaftaran-status--perbaikan">Perlu Perbaikan</span>
                            @else
                                <span class="pendaftaran-status pendaftaran-status--menunggu">Menunggu</span>
                            @endif

                            @if ($pendaftaran->validator_nama)
                                <span class="pendaftaran-validator-mini">
                                    Oleh: {{ $pendaftaran->validator_nama }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="pendaftaran-btn pendaftaran-btn--detail pendaftaran-open-detail"
                                data-target="detail-pendaftaran-{{ $pendaftaran->id }}"
                                aria-haspopup="dialog">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="pendaftaran-no-data">Belum ada data pendaftaran mahasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pendaftaran-empty" id="hasilTidakDitemukan" role="status">
        Mahasiswa yang dicari tidak ditemukan.
    </div>

    {{-- Dialog di luar tabel agar struktur HTML tetap valid. --}}
    @foreach ($pendaftarans as $pendaftaran)
        <dialog class="pendaftaran-modal" id="detail-pendaftaran-{{ $pendaftaran->id }}"
            aria-labelledby="judul-detail-{{ $pendaftaran->id }}">
            <div class="pendaftaran-modal-content">
                <div class="pendaftaran-modal-head">
                    <div>
                        <h2 id="judul-detail-{{ $pendaftaran->id }}">Detail Pendaftaran #{{ str_pad((string) $pendaftaran->id, 6, '0', STR_PAD_LEFT) }}</h2>
                        <p>{{ $pendaftaran->nama_lengkap }} &bull; Diajukan {{ $pendaftaran->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                    </div>
                    <button type="button" class="pendaftaran-modal-close" data-close-detail aria-label="Tutup detail">&times;</button>
                </div>

                <div class="pendaftaran-modal-body">
                    <section class="pendaftaran-section">
                        <h3 class="pendaftaran-section-title">Data Pribadi</h3>
                        <div class="pendaftaran-detail-grid">
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Nama lengkap</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->nama_lengkap ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">NIM</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->user?->nim ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Email akun</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->user?->email ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Jenis kelamin</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->jenis_kelamin ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">No. WhatsApp</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->no_wa ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Asal sekolah</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->asal_sekolah ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Nama orang tua/wali</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->nama_ortu ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">No. HP orang tua/wali</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->no_hp_ortu ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Kategori</span>
                                <span class="pendaftaran-detail-value">{{ strtoupper($pendaftaran->kategori ?: '-') }}</span>
                            </div>
                        </div>
                    </section>

                    <section class="pendaftaran-section">
                        <h3 class="pendaftaran-section-title">Data Akademik</h3>
                        <div class="pendaftaran-detail-grid">
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Tahun angkatan</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->tahun_angkatan ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Fakultas</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->fakultas ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell pendaftaran-program-studi">
                                <span class="pendaftaran-detail-label">Program studi</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->program_studi ?: '-' }}</span>
                            </div>
                        </div>
                    </section>

                    <section class="pendaftaran-section">
                        <h3 class="pendaftaran-section-title">Persyaratan dan Pembayaran</h3>
                        <div class="pendaftaran-detail-grid">
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Nominal pembayaran yang dilaporkan</span>
                                <span class="pendaftaran-detail-value">Rp{{ number_format((float) $pendaftaran->nominal_pembayaran, 0, ',', '.') }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Konfirmasi kebenaran data</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->konfirmasi_data ? 'Sudah dikonfirmasi' : 'Belum dikonfirmasi' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell pendaftaran-reason pendaftaran-detail-cell--full">
                                <span class="pendaftaran-detail-label">Alasan mendaftar</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->alasan_daftar ?: '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell pendaftaran-detail-cell--full">
                                <span class="pendaftaran-detail-label">Berkas pendukung</span>
                                <div class="pendaftaran-documents">
                                    @if ($pendaftaran->bukti_penerimaan)
                                        <a class="pendaftaran-btn pendaftaran-btn--document" target="_blank" rel="noopener noreferrer"
                                            href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'penerimaan']) }}">
                                            Lihat Bukti Penerimaan UIN
                                        </a>
                                    @else
                                        <span class="pendaftaran-detail-value">Bukti penerimaan belum diunggah.</span>
                                    @endif

                                    @if ($pendaftaran->bukti_pembayaran)
                                        <a class="pendaftaran-btn pendaftaran-btn--document" target="_blank" rel="noopener noreferrer"
                                            href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'pembayaran']) }}">
                                            Lihat Bukti Pembayaran
                                        </a>
                                    @else
                                        <span class="pendaftaran-detail-value">Bukti pembayaran belum diunggah.</span>
                                    @endif

                                    @if ($pendaftaran->ijazah_sma)
                                        <a class="pendaftaran-btn pendaftaran-btn--document" target="_blank" rel="noopener noreferrer"
                                            href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'ijazah']) }}">
                                            Lihat Ijazah SMA
                                        </a>
                                    @endif

                                    @if ($pendaftaran->ktp)
                                        <a class="pendaftaran-btn pendaftaran-btn--document" target="_blank" rel="noopener noreferrer"
                                            href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'ktp']) }}">
                                            Lihat KTP
                                        </a>
                                    @endif

                                    @if ($pendaftaran->kk)
                                        <a class="pendaftaran-btn pendaftaran-btn--document" target="_blank" rel="noopener noreferrer"
                                            href="{{ route('musyrifah.pendaftaran.berkas', ['pendaftaran' => $pendaftaran->id, 'jenis' => 'kk']) }}">
                                            Lihat Kartu Keluarga
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="pendaftaran-section">
                        <h3 class="pendaftaran-section-title">Status dan Riwayat Validasi</h3>
                        <div class="pendaftaran-detail-grid">
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Status pendaftaran</span>
                                <span class="pendaftaran-detail-value">
                                    @if ($pendaftaran->status === 'menunggu')
                                        Menunggu Verifikasi
                                    @elseif ($pendaftaran->status === 'disetujui')
                                        Disetujui
                                    @elseif ($pendaftaran->status === 'ditolak')
                                        Ditolak
                                    @elseif ($pendaftaran->status === 'perlu_perbaikan')
                                        Perlu Perbaikan
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', (string) $pendaftaran->status)) }}
                                    @endif
                                </span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Divalidasi oleh</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->validator_nama ?: 'Belum divalidasi' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell">
                                <span class="pendaftaran-detail-label">Waktu validasi</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->divalidasi_pada?->format('d/m/Y H:i') ?? '-' }}</span>
                            </div>
                            <div class="pendaftaran-detail-cell pendaftaran-detail-cell--full">
                                <span class="pendaftaran-detail-label">Catatan validator</span>
                                <span class="pendaftaran-detail-value">{{ $pendaftaran->catatan_validasi ?: '-' }}</span>
                            </div>
                        </div>
                    </section>

                    @if (in_array($pendaftaran->status, ['menunggu', 'perlu_perbaikan'], true))
                        <div class="pendaftaran-notice">
                            Periksa kelengkapan berkas fisik/digital dan pembayaran. Anda dapat <strong>Menyetujui</strong>, meminta <strong>Perbaikan</strong>, atau <strong>Menolak</strong> pengajuan pendaftaran ini.
                        </div>
                        <div class="pendaftaran-action-area">
                            <form class="pendaftaran-action-form" method="POST"
                                action="{{ route('musyrifah.pendaftaran.status', $pendaftaran->id) }}"
                                onsubmit="return confirm('Yakin menyetujui pendaftaran ini? Pastikan dokumen dan pembayaran sudah diverifikasi.');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="disetujui">
                                <h4>Setujui Pendaftaran</h4>
                                <p>Mahasiswa dinyatakan diterima dan dapat melanjutkan ke tahap pemilihan kamar.</p>
                                <label for="catatan-setujui-{{ $pendaftaran->id }}">Catatan (opsional)</label>
                                <textarea id="catatan-setujui-{{ $pendaftaran->id }}" name="catatan_validasi" maxlength="2000" placeholder="Misalnya: Dokumen lengkap dan pembayaran telah diverifikasi."></textarea>
                                <button type="submit" class="pendaftaran-btn pendaftaran-btn--approve">✓ Setujui</button>
                            </form>

                            <form class="pendaftaran-action-form" method="POST"
                                action="{{ route('musyrifah.pendaftaran.status', $pendaftaran->id) }}"
                                onsubmit="return confirm('Yakin meminta perbaikan untuk pendaftaran ini? Tuliskan instruksi perbaikan dengan jelas.');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="perlu_perbaikan">
                                <h4>Perlu Perbaikan</h4>
                                <p>Mahasiswa diminta memperbaiki data atau mengunggah ulang dokumen yang belum sesuai.</p>
                                <label for="catatan-perbaikan-{{ $pendaftaran->id }}">Instruksi Perbaikan <span aria-hidden="true">*</span></label>
                                <textarea id="catatan-perbaikan-{{ $pendaftaran->id }}" name="catatan_validasi" maxlength="2000" required placeholder="Contoh: Bukti pembayaran terpotong, mohon unggah ulang bukti transfer yang jelas."></textarea>
                                <button type="submit" class="pendaftaran-btn pendaftaran-btn--revise">⚠ Minta Perbaikan</button>
                            </form>

                            <form class="pendaftaran-action-form" method="POST"
                                action="{{ route('musyrifah.pendaftaran.status', $pendaftaran->id) }}"
                                onsubmit="return confirm('Yakin menolak pendaftaran ini? Alasan penolakan akan dicatat.');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="ditolak">
                                <h4>Tolak Pendaftaran</h4>
                                <p>Pendaftaran dihentikan karena tidak memenuhi kriteria penerimaan Ma'had.</p>
                                <label for="catatan-tolak-{{ $pendaftaran->id }}">Alasan Penolakan <span aria-hidden="true">*</span></label>
                                <textarea id="catatan-tolak-{{ $pendaftaran->id }}" name="catatan_validasi" maxlength="2000" required placeholder="Tuliskan alasan penolakan secara jelas."></textarea>
                                <button type="submit" class="pendaftaran-btn pendaftaran-btn--reject">✕ Tolak</button>
                            </form>
                        </div>
                    @else
                        <div class="pendaftaran-finished">
                            Pendaftaran sudah diproses dan tidak dapat divalidasi ulang melalui halaman ini.
                            @if ($pendaftaran->validator_nama)
                                Keputusan tercatat atas nama <strong>{{ $pendaftaran->validator_nama }}</strong>.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </dialog>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputCari = document.getElementById('cariMahasiswa');
        const barisPendaftar = document.querySelectorAll('.baris-pendaftar');
        const pesanKosong = document.getElementById('hasilTidakDitemukan');

        if (inputCari && pesanKosong) {
            inputCari.addEventListener('input', function () {
                const kataKunci = inputCari.value.trim().toLowerCase();
                let jumlahDitemukan = 0;

                barisPendaftar.forEach(function (baris) {
                    const cocok = baris.textContent.toLowerCase().includes(kataKunci);
                    baris.style.display = cocok ? '' : 'none';
                    if (cocok) jumlahDitemukan++;
                });

                pesanKosong.style.display = barisPendaftar.length > 0 && jumlahDitemukan === 0 ? 'block' : 'none';
            });
        }

        document.querySelectorAll('.pendaftaran-open-detail').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                const dialog = document.getElementById(tombol.dataset.target);
                if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
            });
        });

        document.querySelectorAll('.pendaftaran-modal').forEach(function (dialog) {
            dialog.querySelectorAll('[data-close-detail]').forEach(function (tombol) {
                tombol.addEventListener('click', function () { dialog.close(); });
            });
        });
    });
</script>
@endsection
