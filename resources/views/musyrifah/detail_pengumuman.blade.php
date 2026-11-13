@extends('layouts.musyrifah')

@section('content')
@php
    $pengumuman = $pengumuman ?? null;
    $mode = $mode ?? 'tambah';
    $modeLihat = $mode === 'lihat';
    $modeEdit = $mode === 'edit';
    $kategoriTersedia = \App\Models\Pengumuman::kategoriTersedia();
    $statusTersedia = \App\Models\Pengumuman::statusPilihan();
    $kategoriTerpilih = old('kategori', $pengumuman?->kategori ?? '');
    $statusTerpilih = old('status', $pengumuman?->status ?? \App\Models\Pengumuman::STATUS_DRAFT);
    $tanggalTerpilih = old('tanggal_publikasi', $pengumuman?->tanggal_publikasi?->toDateString() ?? now()->toDateString());
@endphp

<style>
    .dp {
        width: 100%;
        max-width: 960px;
        margin: 0 auto;
        color: #322a2d;
        font-size: 13px;
    }

    .dp--baca {
        max-width: 900px;
    }

    .dp *,
    .dp *::before,
    .dp *::after {
        box-sizing: border-box;
    }

    .dp [hidden] {
        display: none !important;
    }

    /* ---------------- Tombol ---------------- */
    .dp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 32px;
        padding: 6px 13px;
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

    .dp-btn--utama {
        border-color: #6B0D1B;
        background: #6B0D1B;
        color: #fff;
    }

    .dp-btn--utama:hover {
        border-color: #52070f;
        background: #52070f;
    }

    .dp-btn--garis {
        border-color: #e6d7dc;
        background: #fff;
        color: #6B0D1B;
    }

    .dp-btn--garis:hover {
        border-color: #C9A96A;
        background: #F8EDEF;
    }

    .dp-btn--bahaya {
        border-color: #f0d3d8;
        background: #fff;
        color: #a3162b;
    }

    .dp-btn--bahaya:hover {
        border-color: #a3162b;
        background: #a3162b;
        color: #fff;
    }

    .dp-btn:focus-visible {
        outline: 3px solid rgba(201, 169, 106, .45);
        outline-offset: 1px;
    }

    /* ---------------- Kepala ---------------- */
    .dp-back {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 8px;
        color: #6B0D1B;
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
    }

    .dp-back:hover {
        text-decoration: underline;
    }

    .dp-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px 12px;
        margin-bottom: 12px;
    }

    .dp-head h1 {
        margin: 0;
        color: #211a1c;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -.2px;
    }

    .dp-head p {
        margin: 4px 0 0;
        color: #8b7e84;
        font-size: 12.5px;
        line-height: 1.5;
    }

    .dp-head-aksi {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    /* ---------------- Notifikasi ---------------- */
    .dp-alert {
        margin-bottom: 12px;
        padding: 9px 12px;
        border: 1px solid #e9d9b8;
        border-left: 3px solid #C9A96A;
        border-radius: 9px;
        background: #fdf6e8;
        color: #6B0D1B;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .dp-alert--info {
        border-color: #e6dde0;
        border-left-color: #C9A96A;
        background: #fdfbf8;
        color: #6f636a;
        font-weight: 500;
    }

    /* ---------------- Kartu ---------------- */
    .dp-kartu {
        margin-bottom: 12px;
        padding: 12px 14px 14px;
        border: 1px solid #efe5e7;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(59, 7, 15, .035);
    }

    .dp-sec-title {
        position: relative;
        margin: 0 0 10px;
        padding-left: 9px;
        color: #6B0D1B;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.3;
    }

    .dp-sec-title::before {
        content: "";
        position: absolute;
        top: 2px;
        bottom: 2px;
        left: 0;
        width: 3px;
        border-radius: 2px;
        background: #C9A96A;
    }

    /* ---------------- Ringkasan detail ---------------- */
    .dp-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px 10px;
        margin-bottom: 12px;
        color: #8b7e84;
        font-size: 11.5px;
        line-height: 1.5;
    }

    .dp-tag {
        display: inline-block;
        padding: 2px 9px;
        border: 1px solid #e6d7b8;
        border-radius: 999px;
        background: #fff;
        color: #6B0D1B;
        font-size: 10.5px;
        font-weight: 700;
    }

    .dp-status {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .dp-status.terbit {
        background: #e7f5ec;
        color: #1f7a4a;
    }

    .dp-status.draft {
        background: #fdefd6;
        color: #96610a;
    }

    .dp-judul {
        margin: 0 0 10px;
        color: #211a1c;
        font-size: 19px;
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .dp-gambar {
        display: block;
        width: 100%;
        max-width: 460px;
        max-height: 320px;
        margin-bottom: 12px;
        border: 1px solid #efe0e4;
        border-radius: 10px;
        object-fit: cover;
    }

    .dp-isi {
        padding: 13px 15px;
        border: 1px solid #f0e7e9;
        border-left: 3px solid #C9A96A;
        border-radius: 9px;
        background: #fdfbf8;
        color: #2f2b2c;
        font-size: 13.5px;
        line-height: 1.75;
        overflow-wrap: anywhere;
    }

    .dp-berkas {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px 10px;
        margin-top: 12px;
        padding: 9px 11px;
        border: 1px solid #efe5e7;
        border-radius: 9px;
        background: #fff;
        color: #6f636a;
        font-size: 11.5px;
        overflow-wrap: anywhere;
    }

    /* ---------------- Formulir ---------------- */
    .dp-form {
        width: 100%;
    }

    .dp-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .dp-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
        margin-bottom: 10px;
    }

    .dp-grid .dp-field {
        margin-bottom: 0;
    }

    .dp-field--full {
        grid-column: 1 / -1;
    }

    .dp-field label {
        color: #7d7075;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.4;
    }

    .dp-input {
        width: 100%;
        height: 38px;
        padding: 0 10px;
        border: 1px solid #e6dde0;
        border-radius: 8px;
        background: #fff;
        color: #3f353a;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color .16s, box-shadow .16s;
    }

    .dp-input:focus {
        border-color: #C9A96A;
        box-shadow: 0 0 0 3px rgba(201, 169, 106, .16);
    }

    .dp-input::placeholder {
        color: #a79ba0;
    }

    select.dp-input {
        cursor: pointer;
    }

    textarea.dp-input {
        height: auto;
        min-height: 160px;
        padding: 9px 10px;
        line-height: 1.6;
        resize: vertical;
    }

    .dp-help {
        color: #8b7e84;
        font-size: 11px;
        line-height: 1.5;
    }

    .dp-error {
        color: #a3162b;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.5;
    }

    /* ---------------- Unggah berkas ---------------- */
    .dp-upload {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 84px;
        padding: 12px;
        border: 1px dashed #e2d2d7;
        border-radius: 9px;
        background: #fdfbfb;
        text-align: center;
        cursor: pointer;
        transition: border-color .16s, background .16s;
    }

    .dp-upload:hover {
        border-color: #C9A96A;
        background: #fffaf3;
    }

    .dp-upload input {
        display: none;
    }

    .dp-upload strong {
        display: block;
        margin-bottom: 2px;
        color: #4d4045;
        font-size: 12px;
        font-weight: 700;
    }

    .dp-upload span {
        display: block;
        color: #8b7e84;
        font-size: 11px;
        line-height: 1.45;
    }

    .dp-upload .dp-nama-file {
        margin-top: 4px;
        color: #6B0D1B;
        font-size: 10.5px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .dp-tersimpan {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 4px 8px;
        margin-top: 7px;
        color: #6f636a;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .dp-tersimpan a {
        color: #6B0D1B;
        font-weight: 600;
        text-decoration: none;
    }

    .dp-tersimpan a:hover {
        text-decoration: underline;
    }

    .dp-hapus {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
        color: #a3162b;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
    }

    .dp-aksi {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding-top: 11px;
        border-top: 1px dashed #f0e7e9;
    }

    .dp-aksi form {
        display: inline-flex;
        margin: 0;
    }

    @media (max-width: 760px) {
        .dp-head h1 {
            font-size: 19px;
        }

        .dp-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .dp-kartu {
            padding: 11px;
        }

        .dp-judul {
            font-size: 17px;
        }

        .dp-aksi .dp-btn {
            flex: 1;
        }
    }
</style>

<div class="dp{{ $modeLihat ? ' dp--baca' : '' }}">

    @if (session('success'))
        <div class="dp-alert" role="status">{{ session('success') }}</div>
    @endif

    @if ($modeLihat)
        <a href="{{ route('musyrifah.pengumuman') }}" class="dp-back">&lsaquo; Kembali ke daftar pengumuman</a>

        <div class="dp-head">
            <div>
                <h1>Detail Pengumuman</h1>
                <p>Informasi lengkap pengumuman yang dikelola Musyrifah</p>
            </div>

            <div class="dp-head-aksi">
                <a href="{{ route('musyrifah.pengumuman.edit', $pengumuman) }}" class="dp-btn dp-btn--utama">
                    Edit Pengumuman
                </a>
            </div>
        </div>

        <div class="dp-meta">
            <span class="dp-tag">{{ $pengumuman->kategori }}</span>
            <span class="dp-status {{ $pengumuman->kelasStatus() }}">
                {{ $pengumuman->labelStatus() }}
            </span>
            <span>Publikasi: {{ $pengumuman->tanggalTeks() }}</span>
            <span>Penulis: {{ $pengumuman->penulisTeks() }}</span>
            @if ($pengumuman->dipublikasikan_pada)
                <span>
                    Terbit pada: {{ $pengumuman->dipublikasikan_pada->locale('id')->translatedFormat('d F Y H:i') }}
                </span>
            @endif
        </div>

        @if ($pengumuman->isDraft())
            <div class="dp-alert dp-alert--info">
                Pengumuman ini masih berstatus draft sehingga belum tampil pada halaman Pengumuman mahasiswa.
            </div>
        @endif

        <div class="dp-kartu">
            <h2 class="dp-judul">{{ $pengumuman->judul }}</h2>

            @if ($pengumuman->punyaGambar())
                <img
                    src="{{ route('musyrifah.pengumuman.berkas', [$pengumuman, 'gambar']) }}"
                    alt="Gambar pengumuman {{ $pengumuman->judul }}"
                    class="dp-gambar"
                >
            @endif

            <div class="dp-isi">{!! nl2br(e($pengumuman->isi)) !!}</div>

            @if ($pengumuman->punyaDokumen())
                <div class="dp-berkas">
                    <span>Dokumen pendukung: {{ $pengumuman->namaBerkas('dokumen') }}</span>
                    <a
                        href="{{ route('musyrifah.pengumuman.berkas', [$pengumuman, 'dokumen']) }}"
                        class="dp-btn dp-btn--garis"
                    >
                        Unduh Dokumen
                    </a>
                </div>
            @endif

            <div class="dp-aksi" style="margin-top: 12px;">
                <form method="POST" action="{{ route('musyrifah.pengumuman.status', $pengumuman) }}">
                    @csrf
                    <input
                        type="hidden"
                        name="status"
                        value="{{ $pengumuman->isDipublikasikan() ? \App\Models\Pengumuman::STATUS_DRAFT : \App\Models\Pengumuman::STATUS_DIPUBLIKASIKAN }}"
                    >
                    <button type="submit" class="dp-btn dp-btn--garis">
                        {{ $pengumuman->isDipublikasikan() ? 'Jadikan Draft' : 'Publikasikan' }}
                    </button>
                </form>

                <form
                    method="POST"
                    action="{{ route('musyrifah.pengumuman.destroy', $pengumuman) }}"
                    onsubmit="return confirm('Hapus pengumuman ini? Data yang dihapus tidak dapat dikembalikan.');"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dp-btn dp-btn--bahaya">Hapus</button>
                </form>
            </div>
        </div>
    @else
        <div class="dp-head">
            <div>
                <h1>{{ $modeEdit ? 'Edit Pengumuman' : '+ Pengumuman' }}</h1>
                <p>
                    {{ $modeEdit
                        ? 'Perbarui Informasi Pengumuman Untuk Mahasantri'
                        : 'Buat Pengumuman Baru Untuk Mahasantri' }}
                </p>
            </div>
        </div>

        <form
            class="dp-form"
            id="formPengumuman"
            method="POST"
            enctype="multipart/form-data"
            action="{{ $modeEdit
                ? route('musyrifah.pengumuman.update', $pengumuman)
                : route('musyrifah.pengumuman.store') }}"
        >
            @csrf
            @if ($modeEdit)
                @method('PUT')
            @endif

            <div class="dp-kartu">
                <h2 class="dp-sec-title">Informasi Pengumuman</h2>

                <div class="dp-field">
                    <label for="judul">Judul Pengumuman</label>
                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        class="dp-input"
                        placeholder="Masukan Judul Pengumuman"
                        value="{{ old('judul', $pengumuman?->judul) }}"
                        maxlength="150"
                        required
                    >
                    @error('judul')
                        <span class="dp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="dp-grid">
                    <div class="dp-field">
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="kategori" class="dp-input" required>
                            <option value="" disabled @selected($kategoriTerpilih === '')>
                                Pilih Kategori Pengumuman
                            </option>
                            @foreach ($kategoriTersedia as $kategori)
                                <option value="{{ $kategori }}" @selected($kategoriTerpilih === $kategori)>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                            <option value="lainnya" @selected($kategoriTerpilih === 'lainnya')>
                                Kategori Lainnya
                            </option>
                        </select>
                        @error('kategori')
                            <span class="dp-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="dp-field" id="grupKategoriBaru" hidden>
                        <label for="kategori_baru">Nama Kategori Baru</label>
                        <input
                            type="text"
                            id="kategori_baru"
                            name="kategori_baru"
                            class="dp-input"
                            placeholder="Contoh: Beasiswa"
                            value="{{ old('kategori_baru') }}"
                            maxlength="60"
                        >
                        <span class="dp-help">Kategori baru akan tersimpan dan tersedia pada filter berikutnya.</span>
                        @error('kategori_baru')
                            <span class="dp-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="dp-field" style="margin-top: 10px;">
                    <label for="isi">Isi Pengumuman</label>
                    <textarea
                        id="isi"
                        name="isi"
                        class="dp-input"
                        placeholder="Tuliskan Isi pengumuman Secara Lengkap"
                        required
                    >{{ old('isi', $pengumuman?->isi) }}</textarea>
                    @error('isi')
                        <span class="dp-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="dp-kartu">
                <h2 class="dp-sec-title">Lampiran</h2>

                <div class="dp-grid">
                    <div class="dp-field">
                        <label for="gambar">Gambar Pengumuman (Opsional)</label>

                        <label for="gambar" class="dp-upload">
                            <div>
                                <strong>Unggah Gambar</strong>
                                <span>JPG, JPEG, PNG, WEBP (maks. 5 MB)</span>
                                <span class="dp-nama-file" id="namaGambar">
                                    {{ $pengumuman?->namaBerkas('gambar') ?? '' }}
                                </span>
                            </div>

                            <input
                                type="file"
                                id="gambar"
                                name="gambar"
                                accept=".jpg,.jpeg,.png,.webp"
                            >
                        </label>

                        @if ($pengumuman?->punyaGambar())
                            <div class="dp-tersimpan">
                                <span>Gambar tersimpan:</span>
                                <a
                                    href="{{ route('musyrifah.pengumuman.berkas', [$pengumuman, 'gambar']) }}"
                                    target="_blank"
                                    rel="noopener"
                                >{{ $pengumuman->namaBerkas('gambar') }}</a>

                                <label class="dp-hapus" for="hapus_gambar">
                                    <input type="checkbox" id="hapus_gambar" name="hapus_gambar" value="1">
                                    Hapus gambar ini
                                </label>
                            </div>
                        @endif

                        @error('gambar')
                            <span class="dp-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="dp-field">
                        <label for="dokumen">Dokumen Pendukung (Opsional)</label>

                        <label for="dokumen" class="dp-upload">
                            <div>
                                <strong>Unggah Dokumen</strong>
                                <span>PDF, JPG, PNG (maks. 5 MB)</span>
                                <span class="dp-nama-file" id="namaDokumen">
                                    {{ $pengumuman?->namaBerkas('dokumen') ?? '' }}
                                </span>
                            </div>

                            <input
                                type="file"
                                id="dokumen"
                                name="dokumen"
                                accept=".pdf,.jpg,.jpeg,.png"
                            >
                        </label>

                        @if ($pengumuman?->punyaDokumen())
                            <div class="dp-tersimpan">
                                <span>Dokumen tersimpan:</span>
                                <a
                                    href="{{ route('musyrifah.pengumuman.berkas', [$pengumuman, 'dokumen']) }}"
                                    target="_blank"
                                    rel="noopener"
                                >{{ $pengumuman->namaBerkas('dokumen') }}</a>

                                <label class="dp-hapus" for="hapus_dokumen">
                                    <input type="checkbox" id="hapus_dokumen" name="hapus_dokumen" value="1">
                                    Hapus dokumen ini
                                </label>
                            </div>
                        @endif

                        @error('dokumen')
                            <span class="dp-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="dp-kartu">
                <h2 class="dp-sec-title">Publikasi</h2>

                <div class="dp-grid">
                    <div class="dp-field">
                        <label for="tanggal_publikasi">Tanggal Publikasi</label>
                        <input
                            type="date"
                            id="tanggal_publikasi"
                            name="tanggal_publikasi"
                            class="dp-input"
                            value="{{ $tanggalTerpilih }}"
                            required
                        >
                        @error('tanggal_publikasi')
                            <span class="dp-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="dp-field">
                        <label for="status">Status Pengumuman</label>
                        <select id="status" name="status" class="dp-input" required>
                            @foreach ($statusTersedia as $nilai => $label)
                                <option value="{{ $nilai }}" @selected($statusTerpilih === $nilai)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="dp-help">Pengumuman berstatus Draft hanya terlihat oleh Musyrifah.</span>
                        @error('status')
                            <span class="dp-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="dp-aksi" style="margin-top: 12px;">
                    <a href="{{ route('musyrifah.pengumuman') }}" class="dp-btn dp-btn--garis">Batal</a>
                    <button type="submit" class="dp-btn dp-btn--utama">Simpan</button>
                </div>
            </div>
        </form>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kategori = document.getElementById('kategori');
        const grupKategoriBaru = document.getElementById('grupKategoriBaru');
        const kategoriBaru = document.getElementById('kategori_baru');

        if (kategori && grupKategoriBaru) {
            const aturKategoriBaru = function () {
                const tampil = kategori.value === 'lainnya';

                grupKategoriBaru.hidden = !tampil;
                kategoriBaru.required = tampil;

                if (tampil) {
                    kategoriBaru.focus();
                }
            };

            kategori.addEventListener('change', aturKategoriBaru);
            aturKategoriBaru();
        }

        const pasanganBerkas = [
            { input: 'gambar', label: 'namaGambar' },
            { input: 'dokumen', label: 'namaDokumen' }
        ];

        pasanganBerkas.forEach(function (pasangan) {
            const inputBerkas = document.getElementById(pasangan.input);
            const namaBerkas = document.getElementById(pasangan.label);

            if (!inputBerkas || !namaBerkas) {
                return;
            }

            inputBerkas.addEventListener('change', function () {
                const file = this.files[0];
                namaBerkas.textContent = file ? file.name : '';
            });
        });
    });
</script>
@endsection
