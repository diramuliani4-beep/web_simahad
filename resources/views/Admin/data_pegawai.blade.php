@extends('layouts.admin')

@section('title', 'Data Pegawai')

@push('styles')
<style>
    /* Semua class diprefix dp- agar tidak mengubah halaman Admin lainnya. */
    .dp-page, .dp-page * { box-sizing: border-box; }
    .dp-page { width: 100%; min-width: 0; color: #292929; }
    .dp-header { padding-bottom: 22px; margin-bottom: 28px; border-bottom: 1px solid #e7cfd5; }
    .dp-breadcrumb { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; margin-bottom: 12px; font-size: 12px; color: #8d8587; }
    .dp-breadcrumb a { color: #8d8587; text-decoration: none; }
    .dp-breadcrumb a:hover, .dp-breadcrumb .dp-current { color: #800b22; }
    .dp-header h1 { margin: 0 0 7px; font-size: 25px; line-height: 1.3; font-weight: 700; }
    .dp-header p { margin: 0; font-size: 13px; color: #777; line-height: 1.7; }

    .dp-panel { overflow: hidden; width: 100%; background: #fff; border: 1px solid #efe2df; border-radius: 12px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .dp-panel-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 22px 24px; border-bottom: 1px solid #f0e8e7; }
    .dp-panel-top h2 { margin: 0; font-size: 15px; font-weight: 700; }
    .dp-panel-top .dp-subtitle { margin: 5px 0 0; color: #888; font-size: 11px; }
    .dp-btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 11px 17px; min-height: 39px; background: #800b22; border: 1px solid #800b22; border-radius: 8px; color: #fff; font-family: inherit; font-size: 12px; font-weight: 600; text-decoration: none; transition: background .2s; }
    .dp-btn-primary:hover { background: #65091b; color: #fff; }
    .dp-btn-primary svg, .dp-btn-action svg { width: 15px; height: 15px; }

    .dp-tools { padding: 18px 24px 12px; }
    .dp-search-row { display: flex; width: 100%; min-height: 54px; align-items: center; padding: 5px; border: 1px solid #e7dbd9; border-radius: 10px; background: #fff; }
    .dp-search-row:focus-within { border-color: #800b22; box-shadow: 0 0 0 3px rgba(128, 11, 34, .06); }
    .dp-search-icon { width: 19px; height: 19px; margin: 0 14px 0 11px; color: #9a9a9a; flex: none; }
    .dp-search-row input { flex: 1; min-width: 0; height: 42px; border: 0; outline: 0; font: inherit; font-size: 12px; color: #333; }
    .dp-search-row input::placeholder { color: #aaa; }
    .dp-search-row button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 41px; padding: 0 16px; border: 0; border-radius: 7px; background: #800b22; color: white; font-family: inherit; font-size: 12px; font-weight: 600; cursor: pointer; }
    .dp-search-row button:hover { background: #65091b; }
    .dp-search-row button svg { width: 15px; height: 15px; }
    .dp-filter-row { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin: 13px 0 2px; }
    .dp-filter { display: inline-flex; justify-content: center; align-items: center; min-height: 31px; padding: 6px 15px; background: #f0eeee; border-radius: 999px; color: #333; font-size: 12px; text-decoration: none; font-weight: 600; }
    .dp-filter:hover { background: #eadfe2; color: #800b22; }
    .dp-filter.active { background: #800b22; color: #fff; }
    .dp-clear { margin-left: auto; font-size: 11px; color: #800b22; text-decoration: none; }
    .dp-clear:hover { text-decoration: underline; }
    .dp-hint { display: flex; align-items: center; gap: 7px; margin: 0; padding: 5px 24px 15px; color: #888; font-size: 11px; }
    .dp-hint svg { width: 15px; height: 15px; }

    .dp-table-wrap { width: 100%; max-width: 100%; overflow-x: auto; scrollbar-width: thin; }
    .dp-table-wrap:focus-visible { outline: 2px solid #800b22; outline-offset: -2px; }
    .dp-table { border-collapse: collapse; min-width: 1480px; width: 100%; font-size: 12px; }
    .dp-table th { text-align: left; padding: 14px 13px; white-space: nowrap; background: #800b22; color: #fff; font-size: 11px; font-weight: 600; }
    .dp-table td { padding: 14px 13px; white-space: nowrap; border-bottom: 1px solid #f2eaeb; vertical-align: middle; color: #565052; }
    .dp-table tbody tr:hover { background: #fdf8f9; }
    .dp-table tbody tr:nth-child(even) { background: #fcfafb; }
    .dp-table .dp-center { text-align: center; }
    .dp-table .dp-name { color: #2d2829; font-weight: 700; }
    .dp-table .dp-muted { color: #a09a9c; }
    .dp-table .dp-address { display: inline-block; max-width: 230px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; }
    .dp-avatar { display: inline-flex; align-items: center; justify-content: center; width: 43px; height: 43px; overflow: hidden; border-radius: 50%; background: #f8edef; color: #800b22; font-weight: 700; font-size: 17px; }
    .dp-avatar img { display: block; width: 100%; height: 100%; object-fit: cover; }
    .dp-status { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .dp-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .dp-status.on { background: #def8e8; color: #17763e; }
    .dp-status.off { background: #fee8eb; color: #ad3041; }
    .dp-account { display: inline-flex; align-items: center; padding: 6px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .dp-account.linked { background: #eaf1ff; color: #2954a0; }
    .dp-account.unlinked { background: #f1eff0; color: #62585c; }
    .dp-actions { display: flex; align-items: center; justify-content: center; gap: 6px; }
    .dp-btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 5px; min-height: 34px; padding: 7px 11px; border: 1px solid #e6bcc5; border-radius: 8px; background: #fdf3f5; color: #800b22; text-decoration: none; font-family: inherit; font-size: 11px; font-weight: 600; }
    .dp-btn-action:hover:not(:disabled) { background: #800b22; border-color: #800b22; color: #fff; }
    .dp-btn-action.delete { border-color: #f0c5ca; background: #fff6f7; color: #b03044; cursor: pointer; }
    .dp-btn-action.delete:hover:not(:disabled) { background: #a6263e; border-color: #a6263e; color: #fff; }
    .dp-btn-action:disabled { opacity: .50; cursor: not-allowed; }
    .dp-btn-action:focus-visible { outline: 2px solid #800b22; outline-offset: 3px; }

    /* Dialog konfirmasi seperti halaman Keluar Akun SIMAHAD. */
    .dp-dialog-backdrop[hidden] { display: none !important; }
    .dp-dialog-backdrop { position: fixed; inset: 0; z-index: 99999; display: flex; align-items: center; justify-content: center;
        padding: 20px; background: rgba(12, 20, 32, .70); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); }
    .dp-dialog { width: min(100%, 460px); max-height: calc(100dvh - 40px); overflow-y: auto; border: 1px solid #800b22;
        border-radius: 10px; background: #fff; box-shadow: 0 24px 65px rgba(0,0,0,.27); padding: 22px 24px 20px;
        text-align: center; color: #202020; }
    .dp-dialog-icon { display: flex; align-items: center; justify-content: center; width: 54px; height: 54px;
        margin: 0 auto 13px; background: #f9eff1; border-radius: 50%; color: #800b22; }
    .dp-dialog-icon svg { width: 25px; height: 25px; }
    .dp-dialog h2 { font-size: 17px; line-height: 1.35; font-weight: 650; margin: 0 0 8px; }
    .dp-dialog-desc { margin: 0 auto; max-width: 360px; color: #737373; font-size: 12.5px; line-height: 1.55; }
    .dp-dialog-desc strong { color: #333; overflow-wrap: anywhere; }
    .dp-dialog-note { margin: 10px auto 0; max-width: 360px; padding: 9px 11px; border-radius: 8px;
        background: #fbf4f5; color: #74424b; font-size: 11.5px; line-height: 1.55; }
    .dp-dialog-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 18px; }
    .dp-dialog-button { display: inline-flex; justify-content: center; align-items: center; min-height: 38px;
        padding: 8px 12px; border-radius: 9px; font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; }
    .dp-dialog-cancel { border: 1px solid #800b22; background: #fff; color: #800b22; }
    .dp-dialog-cancel:hover { background: #fdf3f5; }
    .dp-dialog-submit { border: 1px solid #800b22; background: #800b22; color: #fff; }
    .dp-dialog-submit:hover { background: #64091a; }
    .dp-dialog-submit:disabled { opacity: .7; cursor: wait; }
    .dp-dialog-button:focus-visible { outline: 3px solid #bd7988; outline-offset: 3px; }
    .dp-empty { padding: 40px 18px !important; text-align: center; white-space: normal !important; color: #777; }
    .dp-empty strong { display: block; margin-bottom: 6px; color: #333; }
    .dp-footer { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 18px 24px; color: #888; font-size: 11px; }
    .dp-footer strong { color: #800b22; }
    .dp-pages { display: flex; gap: 7px; align-items: center; flex-wrap: wrap; }
    .dp-pages a, .dp-pages span { display: inline-flex; padding: 7px 10px; align-items: center; justify-content: center; min-height: 32px; border: 1px solid #ead8dc; border-radius: 7px; font-size: 11px; color: #800b22; text-decoration: none; }
    .dp-pages a:hover { background: #fbf0f2; }
    .dp-pages .disabled { color: #aaa; background: #f7f7f7; cursor: default; }
    .dp-alert { margin-bottom: 18px; padding: 13px 15px; background: #e8f7ed; color: #196a38; border: 1px solid #bfebcb; border-radius: 9px; font-size: 12px; }
    .dp-alert.error { background: #fff0f0; border-color: #f0c5c5; color: #ae3030; }
    @media (max-width: 700px) {
        .dp-header h1 { font-size: 21px; }
        .dp-panel-top, .dp-tools, .dp-footer { padding-left: 16px; padding-right: 16px; }
        .dp-hint { padding-left: 16px; padding-right: 16px; }
        .dp-btn-primary { width: 100%; }
        .dp-search-row button { padding: 0 12px; }
        .dp-search-label { display: none; }
        .dp-dialog { padding: 18px 16px 16px; }
        .dp-dialog-icon { width: 46px; height: 46px; margin-bottom: 11px; }
        .dp-dialog-icon svg { width: 22px; height: 22px; }
        .dp-dialog h2 { font-size: 16px; }
        .dp-dialog-desc { font-size: 12px; }
        .dp-dialog-actions { gap: 8px; margin-top: 16px; }
        .dp-dialog-button { min-height: 36px; font-size: 11.5px; }
    }
</style>
@endpush

@section('content')
@php
    // Nama query sengaja 'q' agar sesuai AdminDataPegawaiController@index.
    $pencarian = request()->query('q', '');
    $pencarian = is_string($pencarian) ? $pencarian : '';
    $statusFilter = request()->query('status', 'semua');
    $statusFilter = in_array($statusFilter, ['semua', 'aktif', 'nonaktif'], true) ? $statusFilter : 'semua';
@endphp

<div class="dp-page">
    <header class="dp-header">
        <nav class="dp-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">›</span>
            <span class="dp-current" aria-current="page">Data Pegawai</span>
        </nav>
        <h1>Data Pegawai</h1>
        <p>Kelola biodata pegawai Ma'had, baik yang sudah maupun yang belum memiliki akun SIMAHAD.</p>
    </header>

    @if (session('success'))
        <div class="dp-alert" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="dp-alert error" role="alert">{{ session('error') }}</div>
    @endif

    <section class="dp-panel" aria-labelledby="dpTitle">
        <div class="dp-panel-top">
            <div>
                <h2 id="dpTitle">Daftar Pegawai</h2>
                <p class="dp-subtitle">{{ $pegawai->total() }} pegawai tercatat</p>
            </div>
            <a class="dp-btn-primary" href="{{ route('admin.data-pegawai.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Pegawai
            </a>
        </div>

        <div class="dp-tools">
            <form action="{{ route('admin.data-pegawai.index') }}" method="GET" role="search">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <div class="dp-search-row">
                    <svg class="dp-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                    <input type="search" name="q" maxlength="150" value="{{ $pencarian }}" placeholder="Cari nama, email, atau nomor HP..." aria-label="Cari pegawai">
                    <button type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                        <span class="dp-search-label">Cari</span>
                    </button>
                </div>
            </form>

            <nav class="dp-filter-row" aria-label="Filter status pegawai">
                <a class="dp-filter {{ $statusFilter === 'semua' ? 'active' : '' }}" href="{{ route('admin.data-pegawai.index', ['q' => $pencarian, 'status' => 'semua']) }}" @if($statusFilter === 'semua') aria-current="page" @endif>Semua</a>
                <a class="dp-filter {{ $statusFilter === 'aktif' ? 'active' : '' }}" href="{{ route('admin.data-pegawai.index', ['q' => $pencarian, 'status' => 'aktif']) }}" @if($statusFilter === 'aktif') aria-current="page" @endif>Aktif</a>
                <a class="dp-filter {{ $statusFilter === 'nonaktif' ? 'active' : '' }}" href="{{ route('admin.data-pegawai.index', ['q' => $pencarian, 'status' => 'nonaktif']) }}" @if($statusFilter === 'nonaktif') aria-current="page" @endif>Nonaktif</a>
                @if ($pencarian !== '' || $statusFilter !== 'semua')
                    <a class="dp-clear" href="{{ route('admin.data-pegawai.index') }}">Reset filter</a>
                @endif
            </nav>
        </div>

        <p class="dp-hint">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 12h16M8 8l-4 4 4 4M16 8l4 4-4 4"/></svg>
            Geser tabel secara horizontal untuk melihat semua kolom pegawai.
        </p>

        <div class="dp-table-wrap" role="region" tabindex="0" aria-label="Daftar pegawai (geser horizontal)">
            <table class="dp-table">
                <thead>
                    <tr>
                        <th class="dp-center" scope="col">No.</th>
                        <th class="dp-center" scope="col">Foto</th>
                        <th scope="col">Nama Pegawai</th>
                        <th scope="col">Jabatan</th>
                        <th scope="col">Kelompok yang Diajar</th>
                        <th scope="col">Lantai yang Dikelola</th>
                        <th scope="col">Jenis Kelamin</th>
                        <th scope="col">Pendidikan</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Email</th>
                        <th scope="col">No. HP</th>
                        <th scope="col">Status Pegawai</th>
                        <th scope="col">Akun Login</th>
                        <th class="dp-center" scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pegawai as $item)
                        {{-- Akun hanya dibaca melalui relasi pivot pegawai_akun, tidak lewat pegawais.user_id. --}}
                        @php $akunTerhubung = $item->user !== null; @endphp
                        <tr>
                            <td class="dp-center">{{ $pegawai->firstItem() + $loop->index }}</td>
                            <td class="dp-center">
                                <span class="dp-avatar">
                                    @if ($item->foto && \Illuminate\Support\Facades\Route::has('admin.data-pegawai.foto'))
                                        <img src="{{ route('admin.data-pegawai.foto', $item->getKey()) }}"
                                             alt="Foto {{ $item->nama_pegawai }}" loading="lazy"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <span style="display:none">
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-5 3-8 8-8s8 3 8 8"/></svg>
                                        </span>
                                    @else
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-label="Belum ada foto"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-5 3-8 8-8s8 3 8 8"/></svg>
                                    @endif
                                </span>
                            </td>
                            <td class="dp-name">{{ $item->nama_pegawai ?: '—' }}</td>
                            {{-- Jabatan, kelompok, dan lantai memerlukan tabel master/penugasan tersendiri. --}}
                            <td class="dp-muted" title="Belum dihubungkan ke master jabatan">—</td>
                            <td class="dp-muted" title="Belum dihubungkan ke penugasan pembelajaran">—</td>
                            <td class="dp-muted" title="Belum dihubungkan ke penugasan lantai">—</td>
                            <td>
                                @if ($item->jenis_kelamin === 'L') Laki-laki
                                @elseif ($item->jenis_kelamin === 'P') Perempuan
                                @else <span class="dp-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $item->pendidikan_terakhir ?: '—' }}</td>
                            <td><span class="dp-address" title="{{ $item->alamat ?? '' }}">{{ $item->alamat ?: '—' }}</span></td>
                            <td>{{ $item->user?->email ?: ($item->email ?: '—') }}</td>
                            <td>{{ $item->no_hp ?: ($item->user?->no_hp ?: '—') }}</td>
                            <td>
                                <span class="dp-status {{ $item->status_pegawai ? 'on' : 'off' }}">
                                    {{ $item->status_pegawai ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                @if ($akunTerhubung)
                                    <span class="dp-account linked">Sudah Punya Akun</span>
                                @else
                                    <span class="dp-account unlinked">Belum Punya Akun</span>
                                @endif
                            </td>
                            <td class="dp-center">
                                <div class="dp-actions">
                                    @if (\Illuminate\Support\Facades\Route::has('admin.data-pegawai.edit'))
                                        <a class="dp-btn-action" href="{{ route('admin.data-pegawai.edit', $item->getKey()) }}" aria-label="Edit biodata {{ $item->nama_pegawai }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20h9M4 20h4l11-11a2 2 0 0 0-4-4L4 16v4z"/></svg>
                                            Edit
                                        </a>
                                    @else
                                        <button type="button" class="dp-btn-action" disabled title="Route edit belum dibuat">Edit</button>
                                    @endif
                                    @if (\Illuminate\Support\Facades\Route::has('admin.data-pegawai.destroy'))
                                        <button type="button" class="dp-btn-action delete dp-delete-trigger"
                                                data-delete-url="{{ route('admin.data-pegawai.destroy', $item->getKey()) }}"
                                                data-pegawai-name="{{ $item->nama_pegawai }}"
                                                data-punya-akun="{{ $akunTerhubung ? '1' : '0' }}"
                                                aria-label="Hapus data pegawai {{ $item->nama_pegawai }}"
                                                aria-haspopup="dialog" aria-controls="dpDeleteDialog">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m5 4v7m4-7v7"/></svg>
                                            Hapus
                                        </button>
                                    @else
                                        <button type="button" class="dp-btn-action delete" disabled title="Route hapus pegawai belum didaftarkan">Hapus</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="dp-empty" colspan="14">
                                <strong>{{ $pencarian !== '' || $statusFilter !== 'semua' ? 'Tidak ada hasil' : 'Belum ada data pegawai' }}</strong>
                                {{ $pencarian !== '' || $statusFilter !== 'semua' ? 'Tidak ditemukan pegawai yang sesuai dengan pencarian atau filter.' : 'Belum ada biodata pegawai. Klik Tambah Pegawai untuk mencatat pegawai, meskipun belum memiliki akun.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <footer class="dp-footer">
            <span>Menampilkan <strong>{{ $pegawai->count() }}</strong> dari <strong>{{ $pegawai->total() }}</strong> pegawai</span>
            <div class="dp-pages" aria-label="Navigasi halaman">
                @if ($pegawai->onFirstPage())
                    <span class="disabled">Sebelumnya</span>
                @else
                    <a href="{{ $pegawai->previousPageUrl() }}">Sebelumnya</a>
                @endif
                <span>Halaman {{ $pegawai->currentPage() }} / {{ $pegawai->lastPage() }}</span>
                @if ($pegawai->hasMorePages())
                    <a href="{{ $pegawai->nextPageUrl() }}">Berikutnya</a>
                @else
                    <span class="disabled">Berikutnya</span>
                @endif
            </div>
        </footer>
    </section>
</div>

{{-- Dialog konfirmasi tunggal untuk seluruh baris tabel; tidak menghapus apa pun sebelum tombol Ya, Hapus. --}}
<div class="dp-dialog-backdrop" id="dpDeleteDialog" hidden>
    <div class="dp-dialog" role="dialog" aria-modal="true" aria-labelledby="dpDeleteTitle" aria-describedby="dpDeleteDescription">
        <span class="dp-dialog-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m5 4v7m4-7v7"/>
            </svg>
        </span>
        <h2 id="dpDeleteTitle">Hapus Data Pegawai?</h2>
        <p class="dp-dialog-desc" id="dpDeleteDescription">
            Apakah Anda yakin ingin menghapus data pegawai <strong id="dpDeleteName"></strong>?
            Data dan foto pegawai akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
        </p>
        <p class="dp-dialog-note" id="dpDeleteAccountNote" hidden>
            Akun pengguna yang sudah terhubung tetap tersedia dan tidak ikut dihapus.
        </p>
        <form id="dpDeleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="dp-dialog-actions">
                <button class="dp-dialog-button dp-dialog-cancel" type="button" id="dpDeleteCancel">Batal</button>
                <button class="dp-dialog-button dp-dialog-submit" type="submit" id="dpDeleteSubmit" disabled>Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('dpDeleteDialog');
    const form = document.getElementById('dpDeleteForm');
    const name = document.getElementById('dpDeleteName');
    const accountNote = document.getElementById('dpDeleteAccountNote');
    const cancel = document.getElementById('dpDeleteCancel');
    const confirm = document.getElementById('dpDeleteSubmit');
    if (!overlay || !form || !name || !accountNote || !cancel || !confirm) return;

    let lastTrigger = null;
    let previousOverflow = '';
    let sending = false;

    function openDialog(button) {
        if (sending) return;
        const url = button.dataset.deleteUrl;
        if (!url) return;

        lastTrigger = button;
        form.action = url;
        name.textContent = button.dataset.pegawaiName || 'ini';
        accountNote.hidden = button.dataset.punyaAkun !== '1';
        confirm.disabled = false;
        confirm.textContent = 'Ya, Hapus';
        overlay.hidden = false;
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        cancel.focus();
    }

    function closeDialog() {
        if (sending) return;
        overlay.hidden = true;
        form.action = '';
        confirm.disabled = true;
        document.body.style.overflow = previousOverflow;
        lastTrigger?.focus();
        lastTrigger = null;
    }

    document.querySelectorAll('.dp-delete-trigger').forEach(function (button) {
        button.addEventListener('click', function () { openDialog(button); });
    });
    cancel.addEventListener('click', closeDialog);
    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) closeDialog();
    });
    document.addEventListener('keydown', function (event) {
        if (overlay.hidden) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeDialog();
        }
        if (event.key !== 'Tab') return;
        // Jaga fokus keyboard tetap pada kedua tombol dialog.
        const first = cancel;
        const last = confirm;
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
    form.addEventListener('submit', function (event) {
        if (sending || !form.action) {
            event.preventDefault();
            return;
        }
        sending = true;
        confirm.disabled = true;
        confirm.textContent = 'Menghapus...';
    });
});
</script>
@endpush
