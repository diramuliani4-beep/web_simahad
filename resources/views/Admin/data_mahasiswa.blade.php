@extends('layouts.admin')

@section('title', 'Data Mahasiswa')

@push('styles')
<style>
    /* Semua class diprefix dm- agar tidak mengubah halaman Admin lainnya. */
    .dm-page, .dm-page * { box-sizing: border-box; }
    .dm-page { width: 100%; min-width: 0; color: #292929; }
    .dm-header { padding-bottom: 22px; margin-bottom: 22px; border-bottom: 1px solid #e7cfd5; }
    .dm-breadcrumb { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; margin-bottom: 12px; font-size: 12px; color: #8d8587; }
    .dm-breadcrumb a { color: #8d8587; text-decoration: none; }
    .dm-breadcrumb a:hover, .dm-breadcrumb .dm-current { color: #800b22; }
    .dm-header h1 { margin: 0 0 7px; font-size: 25px; line-height: 1.3; font-weight: 700; }
    .dm-header p { margin: 0; font-size: 13px; color: #777; line-height: 1.7; }

    .dm-readonly { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 20px; padding: 13px 15px; border: 1px solid #e6d5a8; border-radius: 10px; background: #fdf8ea; color: #7a5b13; font-size: 12px; line-height: 1.65; }
    .dm-readonly svg { width: 17px; height: 17px; flex: none; margin-top: 1px; }
    .dm-readonly strong { color: #6b4d0d; }

    .dm-panel { overflow: hidden; width: 100%; background: #fff; border: 1px solid #efe2df; border-radius: 12px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .dm-panel-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 22px 24px; border-bottom: 1px solid #f0e8e7; }
    .dm-panel-top h2 { margin: 0; font-size: 15px; font-weight: 700; }
    .dm-panel-top .dm-subtitle { margin: 5px 0 0; color: #888; font-size: 11px; }

    .dm-tools { padding: 18px 24px 12px; }
    .dm-search-row { display: flex; width: 100%; min-height: 54px; align-items: center; padding: 5px; border: 1px solid #e7dbd9; border-radius: 10px; background: #fff; }
    .dm-search-row:focus-within { border-color: #800b22; box-shadow: 0 0 0 3px rgba(128, 11, 34, .06); }
    .dm-search-icon { width: 19px; height: 19px; margin: 0 14px 0 11px; color: #9a9a9a; flex: none; }
    .dm-search-row input { flex: 1; min-width: 0; height: 42px; border: 0; outline: 0; font: inherit; font-size: 12px; color: #333; }
    .dm-search-row input::placeholder { color: #aaa; }
    .dm-search-row button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 41px; padding: 0 16px; border: 0; border-radius: 7px; background: #800b22; color: #fff; font-family: inherit; font-size: 12px; font-weight: 600; cursor: pointer; }
    .dm-search-row button:hover { background: #65091b; }
    .dm-search-row button svg { width: 15px; height: 15px; }
    .dm-filter-row { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin: 13px 0 2px; }
    .dm-filter { display: inline-flex; justify-content: center; align-items: center; min-height: 31px; padding: 6px 15px; background: #f0eeee; border-radius: 999px; color: #333; font-size: 12px; text-decoration: none; font-weight: 600; }
    .dm-filter:hover { background: #eadfe2; color: #800b22; }
    .dm-filter.active { background: #800b22; color: #fff; }
    .dm-clear { margin-left: auto; font-size: 11px; color: #800b22; text-decoration: none; }
    .dm-clear:hover { text-decoration: underline; }

    .dm-table-wrap { width: 100%; max-width: 100%; overflow-x: auto; scrollbar-width: thin; }
    .dm-table-wrap:focus-visible { outline: 2px solid #800b22; outline-offset: -2px; }
    .dm-table { border-collapse: collapse; min-width: 1120px; width: 100%; font-size: 12px; }
    .dm-table th { text-align: left; padding: 14px 13px; white-space: nowrap; background: #800b22; color: #fff; font-size: 11px; font-weight: 600; }
    .dm-table td { padding: 14px 13px; border-bottom: 1px solid #f2eaeb; vertical-align: middle; color: #565052; }
    .dm-table tbody tr:hover { background: #fdf8f9; }
    .dm-table tbody tr:nth-child(even) { background: #fcfafb; }
    .dm-table .dm-center { text-align: center; }
    .dm-table .dm-name { color: #2d2829; font-weight: 700; }
    .dm-table .dm-muted { color: #a09a9c; }
    .dm-identity { display: flex; align-items: center; gap: 11px; }
    .dm-avatar { display: inline-flex; align-items: center; justify-content: center; width: 41px; height: 41px; flex: none; border-radius: 50%; background: #f8edef; color: #800b22; font-weight: 700; font-size: 15px; text-transform: uppercase; }
    .dm-badge { display: inline-block; margin-top: 4px; padding: 2px 7px; border-radius: 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
    .dm-badge--umum { background: #e0e7ff; color: #3730a3; }
    .dm-badge--kip { background: #fef3c7; color: #92400e; }
    .dm-academic { display: block; color: #333; font-weight: 600; }
    .dm-academic-sub { display: block; margin-top: 2px; font-size: 11px; color: #8d8587; }
    .dm-verified { display: block; color: #2d2829; font-weight: 600; }
    .dm-verified-sub { display: block; margin-top: 2px; font-size: 11px; color: #8d8587; }
    .dm-btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 5px; min-height: 34px; padding: 7px 12px; border: 1px solid #e6bcc5; border-radius: 8px; background: #fdf3f5; color: #800b22; font-family: inherit; font-size: 11px; font-weight: 600; cursor: pointer; }
    .dm-btn-action:hover { background: #800b22; border-color: #800b22; color: #fff; }
    .dm-btn-action:focus-visible { outline: 2px solid #800b22; outline-offset: 3px; }
    .dm-btn-action svg { width: 14px; height: 14px; }
    .dm-empty { padding: 40px 18px !important; text-align: center; color: #777; }
    .dm-empty strong { display: block; margin-bottom: 6px; color: #333; }
    .dm-footer { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 18px 24px; color: #888; font-size: 11px; }
    .dm-footer strong { color: #800b22; }
    .dm-pages { display: flex; gap: 7px; align-items: center; flex-wrap: wrap; }
    .dm-pages a, .dm-pages span { display: inline-flex; padding: 7px 10px; align-items: center; justify-content: center; min-height: 32px; border: 1px solid #ead8dc; border-radius: 7px; font-size: 11px; color: #800b22; text-decoration: none; }
    .dm-pages a:hover { background: #fbf0f2; }
    .dm-pages .disabled { color: #aaa; background: #f7f7f7; cursor: default; }

    /* Dialog detail: hanya membaca, tanpa aksi perubahan data. */
    .dm-modal { width: min(820px, calc(100% - 28px)); max-height: min(88vh, 860px); margin: auto; padding: 0; border: none; border-radius: 16px; color: #222; box-shadow: 0 22px 65px rgba(0, 0, 0, .28); overflow: auto; }
    .dm-modal::backdrop { background: rgba(16, 10, 12, .6); }
    .dm-modal-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 20px 24px; background: #650019; color: #fff; }
    .dm-modal-head h2 { margin: 0 0 5px; font-size: 19px; }
    .dm-modal-head p { margin: 0; font-size: 12px; opacity: .92; }
    .dm-modal-close { display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; flex: none; border: 1px solid rgba(255, 255, 255, .6); border-radius: 50%; background: transparent; color: #fff; font-size: 24px; line-height: 1; cursor: pointer; }
    .dm-modal-body { padding: 22px 24px 26px; }
    .dm-section { margin-bottom: 22px; }
    .dm-section-title { margin: 0 0 12px; padding-bottom: 8px; border-bottom: 1px solid #ecd8de; color: #800b22; font-size: 15px; font-weight: 700; }
    .dm-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 16px; }
    .dm-detail-cell { min-width: 0; padding: 11px 13px; border: 1px solid #eee0e4; border-radius: 9px; background: #fffafa; }
    .dm-detail-cell--full { grid-column: 1 / -1; }
    .dm-detail-label { display: block; margin-bottom: 5px; color: #6f6064; font-size: 11px; }
    .dm-detail-value { display: block; color: #25171b; font-size: 13px; font-weight: 600; overflow-wrap: anywhere; white-space: pre-line; }
    .dm-note { padding: 12px 14px; border: 1px solid #e6d5a8; border-radius: 10px; background: #fdf8ea; color: #7a5b13; font-size: 12px; line-height: 1.65; }

    @media (max-width: 700px) {
        .dm-header h1 { font-size: 21px; }
        .dm-panel-top, .dm-tools, .dm-footer { padding-left: 16px; padding-right: 16px; }
        .dm-search-row button { padding: 0 12px; }
        .dm-search-label { display: none; }
        .dm-detail-grid { grid-template-columns: 1fr; }
        .dm-detail-cell--full { grid-column: auto; }
        .dm-modal-head { padding: 17px 16px; }
        .dm-modal-body { padding: 18px 16px; }
    }
</style>
@endpush

@section('content')
<div class="dm-page">
    <header class="dm-header">
        <nav class="dm-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">›</span>
            <span class="dm-current" aria-current="page">Data Mahasiswa</span>
        </nav>
        <h1>Data Mahasiswa</h1>
        <p>Daftar mahasiswa yang pendaftarannya sudah diverifikasi dan disetujui Musyrifah.</p>
    </header>

    <div class="dm-readonly" role="note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M12 9v4m0 3h.01M10.3 3.9 2.5 17.3A2 2 0 0 0 4.2 20h15.6a2 2 0 0 0 1.7-2.7L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
        </svg>
        <span>
            <strong>Halaman ini hanya untuk melihat data.</strong>
            Admin Sistem dapat melihat daftar, mencari, memfilter, dan membuka detail mahasiswa.
            Penambahan, perubahan, penghapusan, dan verifikasi pendaftaran tetap menjadi kewenangan Musyrifah.
        </span>
    </div>

    <section class="dm-panel" aria-labelledby="dmTitle">
        <div class="dm-panel-top">
            <div>
                <h2 id="dmTitle">Mahasiswa Terverifikasi</h2>
                <p class="dm-subtitle">{{ $mahasiswa->total() }} mahasiswa dengan pendaftaran disetujui</p>
            </div>
        </div>

        <div class="dm-tools">
            <form action="{{ route('admin.data-mahasiswa.index') }}" method="GET" role="search">
                <input type="hidden" name="kategori" value="{{ $kategoriFilter }}">
                <div class="dm-search-row">
                    <svg class="dm-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                    <input type="search" name="q" id="dmCari" maxlength="150" value="{{ $pencarian }}"
                           placeholder="Cari nama, NIM, email, fakultas, atau program studi..."
                           aria-label="Cari mahasiswa">
                    <button type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                        <span class="dm-search-label">Cari</span>
                    </button>
                </div>
            </form>

            <nav class="dm-filter-row" aria-label="Filter kategori mahasiswa">
                <a class="dm-filter {{ $kategoriFilter === 'semua' ? 'active' : '' }}"
                   href="{{ route('admin.data-mahasiswa.index', ['q' => $pencarian, 'kategori' => 'semua']) }}"
                   @if ($kategoriFilter === 'semua') aria-current="page" @endif>Semua</a>
                <a class="dm-filter {{ $kategoriFilter === 'umum' ? 'active' : '' }}"
                   href="{{ route('admin.data-mahasiswa.index', ['q' => $pencarian, 'kategori' => 'umum']) }}"
                   @if ($kategoriFilter === 'umum') aria-current="page" @endif>Mahasiswa Umum</a>
                <a class="dm-filter {{ $kategoriFilter === 'kip' ? 'active' : '' }}"
                   href="{{ route('admin.data-mahasiswa.index', ['q' => $pencarian, 'kategori' => 'kip']) }}"
                   @if ($kategoriFilter === 'kip') aria-current="page" @endif>Mahasiswa KIP</a>
                @if ($pencarian !== '' || $kategoriFilter !== 'semua')
                    <a class="dm-clear" href="{{ route('admin.data-mahasiswa.index') }}">Reset filter</a>
                @endif
            </nav>
        </div>

        <div class="dm-table-wrap" role="region" tabindex="0" aria-label="Daftar mahasiswa (geser horizontal)">
            <table class="dm-table">
                <thead>
                    <tr>
                        <th class="dm-center" scope="col">No.</th>
                        <th scope="col">Nama &amp; Kategori</th>
                        <th scope="col">NIM</th>
                        <th scope="col">Email</th>
                        <th scope="col">Fakultas / Program Studi</th>
                        <th scope="col">Angkatan</th>
                        <th scope="col">Divalidasi Musyrifah</th>
                        <th class="dm-center" scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswa as $item)
                        @php $kategori = ($item->kategori ?? 'umum') === 'kip' ? 'kip' : 'umum'; @endphp
                        <tr>
                            <td class="dm-center">{{ $mahasiswa->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="dm-identity">
                                    <span class="dm-avatar" aria-hidden="true">{{ $item->user?->inisial() ?: 'M' }}</span>
                                    <span>
                                        <span class="dm-name">{{ $item->nama_lengkap ?: ($item->user?->name ?: '—') }}</span>
                                        <span class="dm-badge dm-badge--{{ $kategori }}">{{ $kategori === 'kip' ? 'KIP' : 'Umum' }}</span>
                                    </span>
                                </div>
                            </td>
                            <td>{{ $item->user?->nim ?: '—' }}</td>
                            <td>{{ $item->user?->email ?: ($item->email_aktif ?: '—') }}</td>
                            <td>
                                <span class="dm-academic">{{ $item->program_studi ?: '—' }}</span>
                                <span class="dm-academic-sub">{{ $item->fakultas ?: 'Fakultas belum diisi' }}</span>
                            </td>
                            <td>{{ $item->tahun_angkatan ?: '—' }}</td>
                            <td>
                                <span class="dm-verified">{{ $item->validator_nama ?: 'Musyrifah' }}</span>
                                <span class="dm-verified-sub">
                                    {{ $item->divalidasi_pada?->format('d/m/Y H:i') ?? 'Tanggal tidak tercatat' }}
                                </span>
                            </td>
                            <td class="dm-center">
                                <button type="button" class="dm-btn-action dm-open-detail"
                                        data-target="dm-detail-{{ $item->id }}" aria-haspopup="dialog">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.6"/></svg>
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="dm-empty" colspan="8">
                                @if ($pencarian !== '' || $kategoriFilter !== 'semua')
                                    <strong>Tidak ada hasil</strong>
                                    Tidak ditemukan mahasiswa terverifikasi yang sesuai dengan pencarian atau filter.
                                @else
                                    <strong>Belum ada mahasiswa terverifikasi</strong>
                                    Data akan muncul setelah Musyrifah menyetujui pendaftaran mahasiswa.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <footer class="dm-footer">
            <span>Menampilkan <strong>{{ $mahasiswa->count() }}</strong> dari <strong>{{ $mahasiswa->total() }}</strong> mahasiswa</span>
            <div class="dm-pages" aria-label="Navigasi halaman">
                @if ($mahasiswa->onFirstPage())
                    <span class="disabled">Sebelumnya</span>
                @else
                    <a href="{{ $mahasiswa->previousPageUrl() }}">Sebelumnya</a>
                @endif
                <span>Halaman {{ $mahasiswa->currentPage() }} / {{ $mahasiswa->lastPage() }}</span>
                @if ($mahasiswa->hasMorePages())
                    <a href="{{ $mahasiswa->nextPageUrl() }}">Berikutnya</a>
                @else
                    <span class="disabled">Berikutnya</span>
                @endif
            </div>
        </footer>
    </section>
</div>

{{-- Dialog detail di luar tabel agar struktur HTML tetap valid. --}}
@foreach ($mahasiswa as $item)
    <dialog class="dm-modal" id="dm-detail-{{ $item->id }}" aria-labelledby="dm-judul-{{ $item->id }}">
        <div class="dm-modal-head">
            <div>
                <h2 id="dm-judul-{{ $item->id }}">Detail Mahasiswa</h2>
                <p>{{ $item->nama_lengkap ?: '—' }} &bull; Disetujui {{ $item->divalidasi_pada?->format('d/m/Y H:i') ?? '-' }}</p>
            </div>
            <button type="button" class="dm-modal-close" data-close-detail aria-label="Tutup detail">&times;</button>
        </div>

        <div class="dm-modal-body">
            <section class="dm-section">
                <h3 class="dm-section-title">Identitas Mahasiswa</h3>
                <div class="dm-detail-grid">
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Nama lengkap</span>
                        <span class="dm-detail-value">{{ $item->nama_lengkap ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">NIM</span>
                        <span class="dm-detail-value">{{ $item->user?->nim ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Email akun</span>
                        <span class="dm-detail-value">{{ $item->user?->email ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Email aktif</span>
                        <span class="dm-detail-value">{{ $item->email_aktif ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Jenis kelamin</span>
                        <span class="dm-detail-value">{{ $item->jenis_kelamin ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">No. WhatsApp</span>
                        <span class="dm-detail-value">{{ $item->no_wa ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Kategori</span>
                        <span class="dm-detail-value">{{ ($item->kategori ?? 'umum') === 'kip' ? 'Mahasiswa KIP' : 'Mahasiswa Umum' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Status akun</span>
                        <span class="dm-detail-value">{{ $item->user?->status ? ucfirst((string) $item->user->status) : '—' }}</span>
                    </div>
                </div>
            </section>

            <section class="dm-section">
                <h3 class="dm-section-title">Data Akademik</h3>
                <div class="dm-detail-grid">
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Tahun angkatan</span>
                        <span class="dm-detail-value">{{ $item->tahun_angkatan ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Fakultas</span>
                        <span class="dm-detail-value">{{ $item->fakultas ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell dm-detail-cell--full">
                        <span class="dm-detail-label">Program studi</span>
                        <span class="dm-detail-value">{{ $item->program_studi ?: '—' }}</span>
                    </div>
                </div>
            </section>

            <section class="dm-section">
                <h3 class="dm-section-title">Data Pendaftaran</h3>
                <div class="dm-detail-grid">
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Asal sekolah</span>
                        <span class="dm-detail-value">{{ $item->asal_sekolah ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Nama orang tua/wali</span>
                        <span class="dm-detail-value">{{ $item->nama_ortu ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">No. HP orang tua/wali</span>
                        <span class="dm-detail-value">{{ $item->no_hp_ortu ?: '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Nominal pembayaran</span>
                        <span class="dm-detail-value">Rp{{ number_format((float) $item->nominal_pembayaran, 0, ',', '.') }}</span>
                    </div>
                </div>
            </section>

            <section class="dm-section">
                <h3 class="dm-section-title">Hasil Verifikasi Musyrifah</h3>
                <div class="dm-detail-grid">
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Status pendaftaran</span>
                        <span class="dm-detail-value">Disetujui</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Divalidasi oleh</span>
                        <span class="dm-detail-value">{{ $item->validator_nama ?: 'Musyrifah' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Waktu validasi</span>
                        <span class="dm-detail-value">{{ $item->divalidasi_pada?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                    <div class="dm-detail-cell">
                        <span class="dm-detail-label">Konfirmasi kebenaran data</span>
                        <span class="dm-detail-value">{{ $item->konfirmasi_data ? 'Sudah dikonfirmasi' : 'Belum dikonfirmasi' }}</span>
                    </div>
                    <div class="dm-detail-cell dm-detail-cell--full">
                        <span class="dm-detail-label">Catatan Musyrifah</span>
                        <span class="dm-detail-value">{{ $item->catatan_validasi ?: '—' }}</span>
                    </div>
                </div>
            </section>

            <p class="dm-note">
                Data ini hanya dapat dibaca oleh Admin Sistem. Perubahan data mahasiswa dan verifikasi berkas
                dilakukan oleh Musyrifah melalui menu Pendaftaran Mahasiswa.
            </p>
        </div>
    </dialog>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.dm-open-detail').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            const dialog = document.getElementById(tombol.dataset.target);
            if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
        });
    });

    document.querySelectorAll('.dm-modal').forEach(function (dialog) {
        dialog.querySelectorAll('[data-close-detail]').forEach(function (tombol) {
            tombol.addEventListener('click', function () { dialog.close(); });
        });
        // Klik di luar kotak dialog menutup detail.
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) dialog.close();
        });
    });
});
</script>
@endpush
