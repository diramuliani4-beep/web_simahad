@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@push('styles')
<style>
    /* Gaya hanya berlaku untuk halaman Manajemen Pengguna. */
    .um-page,
    .um-page * {
        box-sizing: border-box;
    }

    .um-page {
        --um-wine: #800b22;
        --um-wine-dark: #620819;
        --um-border: #ecdee1;
        --um-muted: #837b80;
        --um-text: #292427;
        width: 100%;
        min-width: 0;
        color: var(--um-text);
        font-family: inherit;
    }

    .um-page a {
        text-decoration: none;
    }

    /* Judul dan navigasi */
    .um-header {
        padding-bottom: 19px;
        margin-bottom: 25px;
        border-bottom: 1px solid #e8cfd6;
    }

    .um-breadcrumb {
        margin-bottom: 11px;
        font-size: 12px;
        color: #8e8487;
    }

    .um-breadcrumb a {
        color: var(--um-wine);
    }

    .um-header h1 {
        margin: 0 0 6px;
        font-size: clamp(22px, 2.4vw, 27px);
        font-weight: 700;
    }

    .um-header p {
        margin: 0;
        color: #787175;
        font-size: 13px;
        line-height: 1.7;
    }

    /* Pesan dan panel */
    .um-alert {
        padding: 13px 16px;
        margin-bottom: 16px;
        border: 1px solid #bce2ca;
        border-radius: 9px;
        background: #e8f8ed;
        color: #1c7138;
        font-size: 12px;
    }

    .um-alert-error {
        border-color: #f1c2cc;
        background: #fff0f2;
        color: #a21e3c;
    }

    .um-card {
        overflow: hidden;
        border: 1px solid #ece1e1;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 3px 14px rgba(62, 10, 25, .035);
    }

    .um-card-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 24px;
        border-bottom: 1px solid #f0e8e9;
    }

    .um-card-header h2 {
        margin: 0 0 5px;
        font-size: 16px;
        font-weight: 700;
    }

    .um-card-header p {
        margin: 0;
        color: var(--um-muted);
        font-size: 12px;
    }

    /* Tombol */
    .um-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 41px;
        padding: 10px 17px;
        border: 1px solid #e4d6d8;
        border-radius: 8px;
        background: #fff;
        color: #52454a;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .um-btn:hover {
        background: #fcf1f4;
        color: var(--um-wine);
    }

    .um-btn:focus-visible,
    .um-pill:focus-visible {
        outline: 2px solid var(--um-wine);
        outline-offset: 2px;
    }

    .um-btn:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .um-btn-primary {
        border-color: var(--um-wine);
        background: var(--um-wine);
        color: #fff;
    }

    .um-btn-primary:hover {
        border-color: var(--um-wine-dark);
        background: var(--um-wine-dark);
        color: #fff;
    }

    .um-btn-danger {
        border-color: #e9bac5;
        background: #fff6f7;
        color: #a52540;
    }

    .um-btn-danger:hover {
        background: #a52540;
        color: #fff;
    }

    .um-btn svg {
        width: 15px;
        height: 15px;
    }

    /* Pencarian dan filter */
    .um-toolbar {
        padding: 18px 24px 12px;
    }

    .um-search {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 53px;
        padding: 5px 5px 5px 13px;
        border: 1px solid #e5d8db;
        border-radius: 10px;
    }

    .um-search:focus-within {
        border-color: #a56070;
        box-shadow: 0 0 0 3px rgba(128, 11, 34, .07);
    }

    .um-search input[type="search"] {
        flex: 1;
        min-width: 0;
        border: 0;
        outline: none;
        background: transparent;
        font: inherit;
        font-size: 12px;
    }

    .um-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        padding: 13px 0 6px;
    }

    .um-pill {
        padding: 7px 14px;
        border-radius: 22px;
        background: #f0edef;
        color: #494047;
        font-size: 12px;
        font-weight: 600;
    }

    .um-pill:hover {
        background: #eedfe3;
        color: var(--um-wine);
    }

    .um-pill.current {
        background: var(--um-wine);
        color: #fff;
    }

    .um-hint {
        margin: 0;
        padding: 3px 24px 14px;
        color: #999095;
        font-size: 11px;
    }

    /* Tabel pengguna */
    .um-table-wrap {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }

    .um-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        font-size: 12px;
    }

    .um-table th {
        padding: 14px 16px;
        background: var(--um-wine);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
    }

    .um-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0e8e9;
        color: #574f53;
        vertical-align: middle;
    }

    .um-table tbody tr:nth-child(even) {
        background: #fdfafb;
    }

    .um-table tbody tr:hover {
        background: #fcf4f6;
    }

    .um-profile {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 220px;
    }

    .um-avatar {
        display: grid;
        flex: none;
        place-items: center;
        width: 42px;
        height: 42px;
        overflow: hidden;
        border-radius: 11px;
        background: #f8e9ed;
        color: var(--um-wine);
    }

    .um-avatar svg {
        width: 23px;
        height: 23px;
    }

    .um-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .um-name {
        margin-bottom: 3px;
        color: #2b2528;
        font-weight: 700;
    }

    .um-email {
        max-width: 265px;
        overflow-wrap: anywhere;
        color: #8f8589;
        font-size: 11px;
    }

    .um-role {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 7px;
        background: #f5eff1;
        color: #744252;
        font-weight: 600;
        white-space: nowrap;
    }

    .um-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .um-status::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        content: '';
    }

    .um-status.active {
        background: #e0f8e8;
        color: #147439;
    }

    .um-status.inactive {
        background: #ffe9eb;
        color: #a52a40;
    }

    .um-status.unknown {
        background: #fff2d8;
        color: #986416;
    }

    .um-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .um-actions .um-btn {
        min-height: 34px;
        padding: 8px 10px;
        font-size: 11px;
    }

    .um-inline {
        display: inline-flex;
        margin: 0;
    }

    .um-empty {
        padding: 38px 15px !important;
        text-align: center;
        color: #8b8286;
    }

    /* Footer tabel */
    .um-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 17px 24px;
        color: #827a7e;
        font-size: 11px;
    }

    .um-footer a {
        color: var(--um-wine);
        font-weight: 600;
    }

    .um-pagination {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Modal hapus seperti konfirmasi keluar akun */
    .um-modal[hidden] {
        display: none !important;
    }

    .um-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(12, 20, 32, .72);
        backdrop-filter: blur(5px);
    }

    .um-dialog {
        width: min(100%, 460px);
        max-height: calc(100dvh - 36px);
        overflow-y: auto;
        padding: 22px 24px 20px;
        border: 1px solid var(--um-wine);
        border-radius: 11px;
        background: #fff;
        text-align: center;
        box-shadow: 0 20px 70px rgba(0, 0, 0, .2);
    }

    .um-dialog-icon {
        display: grid;
        place-items: center;
        width: 54px;
        height: 54px;
        margin: 0 auto 13px;
        border-radius: 50%;
        background: #f8edef;
        color: var(--um-wine);
    }

    .um-dialog-icon svg {
        width: 25px;
        height: 25px;
    }

    .um-dialog h2 {
        margin: 0 0 8px;
        font-size: 17px;
        line-height: 1.35;
    }

    .um-dialog p {
        margin: 0;
        color: #797377;
        font-size: 12.5px;
        line-height: 1.55;
    }

    .um-dialog strong {
        color: #333;
        overflow-wrap: anywhere;
    }

    .um-dialog-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 18px;
    }

    .um-dialog-actions .um-btn {
        min-height: 38px;
        padding: 7px 12px;
        font-size: 12px;
    }

    .um-note {
        margin-top: 10px !important;
        padding: 9px 11px;
        border: 1px solid #ebd2d8;
        border-radius: 9px;
        background: #fcf3f5;
        color: #714151;
        font-size: 11.5px !important;
        line-height: 1.55;
    }

    @media (max-width: 720px) {
        .um-card-header,
        .um-toolbar,
        .um-footer {
            padding-right: 16px;
            padding-left: 16px;
        }

        .um-header h1 {
            font-size: 22px;
        }

        .um-dialog {
            padding: 18px 16px 16px;
        }

        .um-dialog-icon {
            width: 46px;
            height: 46px;
            margin-bottom: 11px;
        }

        .um-dialog-icon svg {
            width: 22px;
            height: 22px;
        }

        .um-dialog h2 {
            font-size: 16px;
        }

        .um-dialog p {
            font-size: 12px;
        }

        .um-dialog-actions {
            gap: 8px;
        }

        .um-dialog-actions .um-btn {
            min-height: 36px;
            font-size: 11.5px;
        }
    }
</style>
@endpush

@section('content')
<div class="um-page">
    <header class="um-header">
        <nav class="um-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span aria-hidden="true"> / </span>
            <span aria-current="page">Manajemen Pengguna</span>
        </nav>

        <h1>Manajemen Pengguna</h1>
        <p>Kelola akun login, hak akses, dan status aktif atau nonaktif pengguna SIMAHAD.</p>
    </header>

    @if (session('success'))
        <div class="um-alert" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="um-alert um-alert-error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="um-alert um-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="um-card" aria-labelledby="umDaftarTitle">
        <div class="um-card-header">
            <div>
                <h2 id="umDaftarTitle">Daftar Pengguna</h2>
                <p>{{ $pengguna->total() }} akun terdaftar</p>
            </div>

            <a class="um-btn um-btn-primary" href="{{ route('admin.manajemen-pengguna.create') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 4v16M4 12h16" />
                </svg>
                Tambah Pengguna
            </a>
        </div>

        <div class="um-toolbar">
            <form method="GET" action="{{ route('admin.manajemen-pengguna') }}" class="um-search" role="search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#998a90" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="m16 16 5 5" />
                </svg>

                <input
                    type="search"
                    name="search"
                    maxlength="150"
                    value="{{ $search ?? request('search', '') }}"
                    placeholder="Cari nama, email, atau nomor HP..."
                    aria-label="Cari pengguna"
                >

                <input type="hidden" name="status" value="{{ $status ?? request('status', '') }}">
                <button type="submit" class="um-btn um-btn-primary">Cari</button>
            </form>

            <nav class="um-filters" aria-label="Filter status akun">
                @foreach (['' => 'Semua', 'aktif' => 'Aktif', 'menunggu' => 'Menunggu Aktivasi', 'nonaktif' => 'Nonaktif'] as $filter => $label)
                    <a
                        href="{{ route('admin.manajemen-pengguna', array_filter([
                            'search' => $search ?? '',
                            'status' => $filter,
                        ], fn ($value) => $value !== '')) }}"
                        class="um-pill {{ ($status ?? '') === $filter ? 'current' : '' }}"
                        @if (($status ?? '') === $filter) aria-current="page" @endif
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        <p class="um-hint">Geser tabel ke kanan pada layar kecil untuk melihat semua kolom dan aksi.</p>

        <div class="um-table-wrap" role="region" tabindex="0" aria-label="Daftar akun pengguna">
            <table class="um-table">
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Pengguna</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status Akun</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengguna as $user)
                        @php
                            $statusAkun = strtolower((string) $user->status);
                            $stateClass = match ($statusAkun) {
                                'aktif' => 'active',
                                'nonaktif' => 'inactive',
                                default => 'unknown',
                            };
                            $stateLabel = match ($statusAkun) {
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                                'menunggu aktivasi', 'menunggu', 'pending' => 'Menunggu Aktivasi',
                                default => 'Status perlu diatur',
                            };

                            $pegawaiTerkait = $user->pegawaiAkun?->pegawai;
                            $punyaFoto = !empty($pegawaiTerkait?->foto);
                            $roleLabel = match ($user->role) {
                                'musyrifah' => 'Musyrifah',
                                default => ucfirst((string) $user->role),
                            };
                        @endphp

                        <tr>
                            <td>{{ $pengguna->firstItem() + $loop->index }}</td>

                            <td>
                                <div class="um-profile">
                                    <span class="um-avatar">
                                        @if ($punyaFoto && \Illuminate\Support\Facades\Route::has('admin.data-pegawai.foto'))
                                            <img
                                                src="{{ route('admin.data-pegawai.foto', $pegawaiTerkait->getKey()) }}"
                                                alt="Foto {{ $user->name }}"
                                                loading="lazy"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                                            >
                                            <span style="display: none;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                    <circle cx="12" cy="8" r="4" />
                                                    <path d="M4 21c0-5 3-8 8-8s8 3 8 8" />
                                                </svg>
                                            </span>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                <circle cx="12" cy="8" r="4" />
                                                <path d="M4 21c0-5 3-8 8-8s8 3 8 8" />
                                            </svg>
                                        @endif
                                    </span>

                                    <div>
                                        <div class="um-name">{{ $user->name }}</div>
                                        <div class="um-email">{{ $user->email }}</div>
                                        <div class="um-email">{{ $user->no_hp ?: '—' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="um-role">{{ $roleLabel }}</span>
                            </td>

                            <td>
                                <span class="um-status {{ $stateClass }}">{{ $stateLabel }}</span>
                            </td>

                            <td>
                                <div class="um-actions">
                                    @if (in_array($statusAkun, ['menunggu aktivasi', 'menunggu', 'pending'], true))
                                        <form method="POST" action="{{ route('admin.manajemen-pengguna.aktivasi', $user->getKey()) }}" class="um-inline">
                                            @csrf
                                            <button type="submit" class="um-btn" title="Kirim tautan aktivasi baru ke email pengguna">
                                                Kirim Aktivasi
                                            </button>
                                        </form>
                                    @endif

                                    <a
                                        class="um-btn"
                                        href="{{ route('admin.manajemen-pengguna.edit', $user->getKey()) }}"
                                    >
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        class="um-btn um-btn-danger um-delete"
                                        data-url="{{ route('admin.manajemen-pengguna.destroy', $user->getKey()) }}"
                                        data-name="{{ $user->name }}"
                                        data-linked="{{ $pegawaiTerkait ? '1' : '0' }}"
                                        title="Hapus akun; data pegawai tetap disimpan"
                                        @disabled($user->getKey() === auth()->id())
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="um-empty">
                                Tidak ada pengguna sesuai pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="um-footer">
            <span>
                Menampilkan <strong>{{ $pengguna->count() }}</strong>
                dari <strong>{{ $pengguna->total() }}</strong> pengguna
            </span>

            <nav class="um-pagination" aria-label="Navigasi halaman">
                @if ($pengguna->onFirstPage())
                    <span>Sebelumnya</span>
                @else
                    <a href="{{ $pengguna->previousPageUrl() }}">Sebelumnya</a>
                @endif

                <span>{{ $pengguna->currentPage() }} / {{ $pengguna->lastPage() }}</span>

                @if ($pengguna->hasMorePages())
                    <a href="{{ $pengguna->nextPageUrl() }}">Berikutnya</a>
                @else
                    <span>Berikutnya</span>
                @endif
            </nav>
        </div>
    </section>
</div>

{{-- Konfirmasi penghapusan akun, tanpa menghapus biodata pegawai. --}}
<div class="um-page um-modal" id="umDeleteOverlay" hidden>
    <div
        class="um-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="umDeleteTitle"
        aria-describedby="umDeleteText"
    >
        <div class="um-dialog-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m5 4v7m4-7v7" />
            </svg>
        </div>

        <h2 id="umDeleteTitle">Hapus Akun Pengguna?</h2>

        <p id="umDeleteText">
            Apakah Anda yakin ingin menghapus akun
            <strong id="umDeleteName"></strong>?
            Akses login akun tersebut akan dihapus secara permanen.
        </p>

        <p id="umLinkedNotice" class="um-note" hidden>
            Biodata di Data Pegawai tetap disimpan. Hanya hubungan dan akun login yang dihapus.
        </p>

        <form method="POST" id="umDeleteForm" action="">
            @csrf
            @method('DELETE')

            <div class="um-dialog-actions">
                <button type="button" id="umCancel" class="um-btn">Batal</button>
                <button type="submit" id="umConfirm" class="um-btn um-btn-primary">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const overlay = document.getElementById('umDeleteOverlay');
        const deleteForm = document.getElementById('umDeleteForm');
        const deleteName = document.getElementById('umDeleteName');
        const linkedNotice = document.getElementById('umLinkedNotice');
        const cancelButton = document.getElementById('umCancel');
        const confirmButton = document.getElementById('umConfirm');

        if (!overlay || !deleteForm || !deleteName || !linkedNotice || !cancelButton || !confirmButton) {
            return;
        }

        let previousButton = null;
        let previousOverflow = '';
        let isSubmitting = false;

        function closeDialog() {
            if (isSubmitting) return;

            overlay.hidden = true;
            document.body.style.overflow = previousOverflow;
            deleteForm.action = '';
            previousButton?.focus();
        }

        document.querySelectorAll('.um-delete').forEach(function (button) {
            button.addEventListener('click', function () {
                if (isSubmitting || button.disabled) return;

                const actionUrl = button.dataset.url;
                if (!actionUrl) return;

                previousButton = button;
                previousOverflow = document.body.style.overflow;

                deleteForm.action = actionUrl;
                deleteName.textContent = button.dataset.name || 'ini';
                linkedNotice.hidden = button.dataset.linked !== '1';

                confirmButton.disabled = false;
                confirmButton.textContent = 'Ya, Hapus';
                overlay.hidden = false;
                document.body.style.overflow = 'hidden';
                cancelButton.focus();
            });
        });

        cancelButton.addEventListener('click', closeDialog);

        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) closeDialog();
        });

        document.addEventListener('keydown', function (event) {
            if (overlay.hidden) return;

            if (event.key === 'Escape') {
                event.preventDefault();
                closeDialog();
                return;
            }

            if (event.key === 'Tab' && event.shiftKey && document.activeElement === cancelButton) {
                event.preventDefault();
                confirmButton.focus();
            } else if (event.key === 'Tab' && !event.shiftKey && document.activeElement === confirmButton) {
                event.preventDefault();
                cancelButton.focus();
            }
        });

        deleteForm.addEventListener('submit', function (event) {
            if (isSubmitting || !deleteForm.action) {
                event.preventDefault();
                return;
            }

            isSubmitting = true;
            confirmButton.disabled = true;
            confirmButton.textContent = 'Menghapus...';
        });
    });
</script>
@endpush
