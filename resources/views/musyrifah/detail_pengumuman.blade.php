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
    .detail-pengumuman {
        padding: 26px 30px;
        color: #171717;
        font-family: inherit;
    }

    .detail-header {
        margin: 0 0 34px 12px;
    }

    .detail-header h1 {
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .detail-header p {
        font-size: 20px;
        margin: 0 0 0 30px;
        color: #6f6668;
    }

    .detail-alert {
        margin: 0 0 22px;
        padding: 14px 20px;
        border-radius: 14px;
        border: 1px solid #C9A96A;
        background: #fdf6e8;
        color: #6B0D1B;
        font-size: 17px;
        font-weight: 600;
    }

    .detail-alert.info {
        border-color: #d8c3c7;
        background: #f5e9ec;
        color: #424242;
        font-weight: 500;
    }

    .ringkasan-pengumuman {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .pengumuman-tag {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 700;
        background: #fff;
        border: 1px solid #C9A96A;
        color: #6B0D1B;
    }

    .pengumuman-status {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 700;
    }

    .pengumuman-status.terbit {
        background: #e7f5ec;
        color: #1f7a4a;
    }

    .pengumuman-status.draft {
        background: #fdefd6;
        color: #96610a;
    }

    .meta-pengumuman {
        color: #6f6668;
        font-size: 15px;
    }

    .judul-pengumuman {
        font-size: 26px;
        font-weight: 700;
        line-height: 1.35;
        margin: 0 0 18px;
        color: #171717;
    }

    .isi-pengumuman {
        background: #f5e7ea;
        border-radius: 20px;
        padding: 24px 26px;
        font-size: 18px;
        line-height: 1.75;
        color: #2f2b2c;
        overflow-wrap: anywhere;
    }

    .gambar-pengumuman {
        display: block;
        width: 100%;
        max-width: 520px;
        border-radius: 18px;
        border: 1px solid #d8c3c7;
        margin-bottom: 22px;
    }

    .berkas-pengumuman {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-top: 22px;
        font-size: 16px;
        color: #424242;
    }

    .btn-berkas {
        padding: 10px 20px;
        border-radius: 10px;
        border: 1px solid #6B0D1B;
        background: #fff;
        color: #6B0D1B;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none;
    }

    .btn-berkas:hover {
        background: #6B0D1B;
        color: #fff;
    }

    .form-pengumuman {
        width: 100%;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        display: block;
        margin: 0 0 6px 12px;
        font-size: 20px;
        color: #424242;
    }

    .form-control-pengumuman {
        width: 100%;
        min-height: 66px;
        padding: 17px 20px;
        background: #f5e7ea;
        border: 1px solid transparent;
        border-radius: 20px;
        outline: none;
        color: #333;
        font: inherit;
        font-size: 20px;
        box-sizing: border-box;
    }

    .form-control-pengumuman:focus {
        border-color: #6B0D1B;
    }

    .form-control-pengumuman::placeholder {
        color: #81797b;
    }

    select.form-control-pengumuman {
        appearance: auto;
        cursor: pointer;
    }

    textarea.form-control-pengumuman {
        min-height: 210px;
        resize: vertical;
        line-height: 1.6;
    }

    .bantuan-form {
        display: block;
        margin: 6px 0 0 14px;
        font-size: 14px;
        color: #81797b;
    }

    .pesan-galat {
        display: block;
        margin: 6px 0 0 14px;
        font-size: 15px;
        color: #a3162b;
        font-weight: 600;
    }

    .lampiran-label {
        margin-bottom: 7px !important;
    }

    .upload-area {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70%;
        min-height: 132px;
        padding: 20px;
        box-sizing: border-box;
        background: #f5e7ea;
        border-radius: 20px;
        text-align: center;
        cursor: pointer;
        transition: background 0.2s;
    }

    .upload-area:hover {
        background: #f0dce1;
    }

    .upload-area strong {
        display: block;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .upload-area span {
        color: #81797b;
        font-size: 15px;
        font-weight: 600;
    }

    .upload-area input {
        display: none;
    }

    .nama-file {
        display: block;
        margin-top: 8px;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .berkas-tersimpan {
        margin-top: 10px;
        font-size: 15px;
        color: #424242;
    }

    .berkas-tersimpan a {
        color: #6B0D1B;
        font-weight: 700;
    }

    .cek-hapus {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        font-size: 14px;
        color: #a3162b;
        font-weight: 600;
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        margin-top: 27px;
    }

    .btn-form {
        min-width: 187px;
        min-height: 47px;
        padding: 0 22px;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-batal {
        background: #cecece;
        color: #111;
    }

    .btn-simpan {
        background: #6B0D1B;
        color: #fff;
    }

    .btn-batal:hover {
        background: #bdbdbd;
    }

    .btn-simpan:hover {
        background: #4d0813;
    }

    .btn-hapus-pengumuman {
        background: #a3162b;
        color: #fff;
    }

    .btn-hapus-pengumuman:hover {
        background: #7d1020;
    }

    .aksi-detail {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 30px;
    }

    .aksi-detail form {
        margin: 0;
    }

    @media (max-width: 768px) {
        .detail-pengumuman {
            padding: 20px 14px;
        }

        .detail-header {
            margin: 0 0 26px;
        }

        .detail-header h1 {
            font-size: 27px;
        }

        .detail-header p {
            font-size: 16px;
            margin-left: 0;
        }

        .form-group label {
            font-size: 17px;
        }

        .form-control-pengumuman {
            font-size: 16px;
        }

        .upload-area {
            width: 100%;
        }

        .btn-form {
            width: 100%;
        }
    }
</style>

<div class="detail-pengumuman">

    @if (session('success'))
        <div class="detail-alert" role="status">{{ session('success') }}</div>
    @endif

    @if ($modeLihat)
        <div class="detail-header">
            <h1>Detail Pengumuman</h1>
            <p>Informasi lengkap pengumuman yang dikelola Musyrifah</p>
        </div>

        <div class="ringkasan-pengumuman">
            <span class="pengumuman-tag">{{ $pengumuman->kategori }}</span>
            <span class="pengumuman-status {{ $pengumuman->kelasStatus() }}">
                {{ $pengumuman->labelStatus() }}
            </span>
            <span class="meta-pengumuman">Publikasi: {{ $pengumuman->tanggalTeks() }}</span>
            <span class="meta-pengumuman">Penulis: {{ $pengumuman->penulisTeks() }}</span>
            @if ($pengumuman->dipublikasikan_pada)
                <span class="meta-pengumuman">
                    Terbit pada: {{ $pengumuman->dipublikasikan_pada->locale('id')->translatedFormat('d F Y H:i') }}
                </span>
            @endif
        </div>

        @if ($pengumuman->isDraft())
            <div class="detail-alert info">
                Pengumuman ini masih berstatus draft sehingga belum tampil pada halaman Pengumuman mahasiswa.
            </div>
        @endif

        <h2 class="judul-pengumuman">{{ $pengumuman->judul }}</h2>

        @if ($pengumuman->punyaGambar())
            <img
                src="{{ route('musyrifah.pengumuman.berkas', [$pengumuman, 'gambar']) }}"
                alt="Gambar pengumuman {{ $pengumuman->judul }}"
                class="gambar-pengumuman"
            >
        @endif

        <div class="isi-pengumuman">{!! nl2br(e($pengumuman->isi)) !!}</div>

        @if ($pengumuman->punyaDokumen())
            <div class="berkas-pengumuman">
                <span>Dokumen pendukung: {{ $pengumuman->namaBerkas('dokumen') }}</span>
                <a
                    href="{{ route('musyrifah.pengumuman.berkas', [$pengumuman, 'dokumen']) }}"
                    class="btn-berkas"
                >
                    Unduh Dokumen
                </a>
            </div>
        @endif

        <div class="aksi-detail">
            <a href="{{ route('musyrifah.pengumuman.edit', $pengumuman) }}" class="btn-form btn-simpan">
                Edit Pengumuman
            </a>

            <form method="POST" action="{{ route('musyrifah.pengumuman.status', $pengumuman) }}">
                @csrf
                <input
                    type="hidden"
                    name="status"
                    value="{{ $pengumuman->isDipublikasikan() ? \App\Models\Pengumuman::STATUS_DRAFT : \App\Models\Pengumuman::STATUS_DIPUBLIKASIKAN }}"
                >
                <button type="submit" class="btn-form btn-batal">
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
                <button type="submit" class="btn-form btn-hapus-pengumuman">Hapus</button>
            </form>

            <a href="{{ route('musyrifah.pengumuman') }}" class="btn-form btn-batal">
                Kembali
            </a>
        </div>
    @else
        <div class="detail-header">
            <h1>{{ $modeEdit ? 'Edit Pengumuman' : '+ Pengumuman' }}</h1>
            <p>
                {{ $modeEdit
                    ? 'Perbarui Informasi Pengumuman Untuk Mahasantri'
                    : 'Buat Pengumuman Baru Untuk Mahasantri' }}
            </p>
        </div>

        <form
            class="form-pengumuman"
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

            <div class="form-group">
                <label for="judul">Judul Pengumuman</label>
                <input
                    type="text"
                    id="judul"
                    name="judul"
                    class="form-control-pengumuman"
                    placeholder="Masukan Judul Pengumuman"
                    value="{{ old('judul', $pengumuman?->judul) }}"
                    maxlength="150"
                    required
                >
                @error('judul')
                    <span class="pesan-galat">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" class="form-control-pengumuman" required>
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
                    <span class="pesan-galat">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" id="grupKategoriBaru" hidden>
                <label for="kategori_baru">Nama Kategori Baru</label>
                <input
                    type="text"
                    id="kategori_baru"
                    name="kategori_baru"
                    class="form-control-pengumuman"
                    placeholder="Contoh: Beasiswa"
                    value="{{ old('kategori_baru') }}"
                    maxlength="60"
                >
                <span class="bantuan-form">
                    Kategori baru akan tersimpan dan tersedia pada filter berikutnya.
                </span>
                @error('kategori_baru')
                    <span class="pesan-galat">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="isi">Isi Pengumuman</label>
                <textarea
                    id="isi"
                    name="isi"
                    class="form-control-pengumuman"
                    placeholder="Tuliskan Isi pengumuman Secara Lengkap"
                    required
                >{{ old('isi', $pengumuman?->isi) }}</textarea>
                @error('isi')
                    <span class="pesan-galat">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="gambar" class="lampiran-label">Gambar Pengumuman (Opsional)</label>

                <label for="gambar" class="upload-area">
                    <div>
                        <strong>Unggah Gambar</strong>
                        <span>JPG, JPEG, PNG, WEBP (maks. 5 MB)</span>
                        <span class="nama-file" id="namaGambar">
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
                    <div class="berkas-tersimpan">
                        Gambar tersimpan:
                        <a
                            href="{{ route('musyrifah.pengumuman.berkas', [$pengumuman, 'gambar']) }}"
                            target="_blank"
                            rel="noopener"
                        >{{ $pengumuman->namaBerkas('gambar') }}</a>

                        <label class="cek-hapus" for="hapus_gambar">
                            <input type="checkbox" id="hapus_gambar" name="hapus_gambar" value="1">
                            Hapus gambar ini
                        </label>
                    </div>
                @endif

                @error('gambar')
                    <span class="pesan-galat">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="dokumen" class="lampiran-label">Dokumen Pendukung (Opsional)</label>

                <label for="dokumen" class="upload-area">
                    <div>
                        <strong>Unggah Dokumen</strong>
                        <span>PDF, JPG, PNG (maks. 5 MB)</span>
                        <span class="nama-file" id="namaDokumen">
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
                    <div class="berkas-tersimpan">
                        Dokumen tersimpan:
                        <a
                            href="{{ route('musyrifah.pengumuman.berkas', [$pengumuman, 'dokumen']) }}"
                            target="_blank"
                            rel="noopener"
                        >{{ $pengumuman->namaBerkas('dokumen') }}</a>

                        <label class="cek-hapus" for="hapus_dokumen">
                            <input type="checkbox" id="hapus_dokumen" name="hapus_dokumen" value="1">
                            Hapus dokumen ini
                        </label>
                    </div>
                @endif

                @error('dokumen')
                    <span class="pesan-galat">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_publikasi">Tanggal Publikasi</label>
                <input
                    type="date"
                    id="tanggal_publikasi"
                    name="tanggal_publikasi"
                    class="form-control-pengumuman"
                    value="{{ $tanggalTerpilih }}"
                    required
                >
                @error('tanggal_publikasi')
                    <span class="pesan-galat">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status Pengumuman</label>
                <select id="status" name="status" class="form-control-pengumuman" required>
                    @foreach ($statusTersedia as $nilai => $label)
                        <option value="{{ $nilai }}" @selected($statusTerpilih === $nilai)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <span class="bantuan-form">
                    Pengumuman berstatus Draft hanya terlihat oleh Musyrifah.
                </span>
                @error('status')
                    <span class="pesan-galat">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('musyrifah.pengumuman') }}" class="btn-form btn-batal">
                    Batal
                </a>

                <button type="submit" class="btn-form btn-simpan">
                    Simpan
                </button>
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
