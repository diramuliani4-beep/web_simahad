@extends('layouts.guru')

@section('title', $mode === 'edit' ? 'Edit Tugas' : 'Buat Tugas')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .guru-form {
        font-family: 'Poppins', sans-serif;
        color: #292929;
        width: 100%;
        box-sizing: border-box;
    }

    .guru-form *,
    .guru-form *::before,
    .guru-form *::after {
        box-sizing: border-box;
    }

    .guru-form .page-header {
        margin-bottom: 18px;
    }

    .guru-form h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 600;
        color: #080808;
    }

    .guru-form .page-subtitle {
        margin: 6px 0 0;
        font-size: 14px;
        color: #5b5b5b;
    }

    .guru-form .card {
        padding: 22px;
        background: #fff;
        border: 1px solid #eadfda;
        border-radius: 12px;
    }

    .guru-form .grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .guru-form .field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .guru-form .field-full {
        grid-column: 1 / -1;
    }

    .guru-form label {
        font-size: 13.5px;
        font-weight: 600;
        color: #43302b;
    }

    .guru-form input[type="text"],
    .guru-form input[type="date"],
    .guru-form input[type="datetime-local"],
    .guru-form input[type="file"],
    .guru-form select,
    .guru-form textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d9c9c4;
        border-radius: 9px;
        background: #fff;
        font-family: inherit;
        font-size: 14px;
        color: #292929;
    }

    .guru-form textarea {
        min-height: 150px;
        resize: vertical;
        line-height: 1.55;
    }

    .guru-form input:focus,
    .guru-form select:focus,
    .guru-form textarea:focus {
        outline: 2px solid rgba(128, 13, 34, 0.2);
        border-color: var(--maroon);
    }

    .guru-form .hint {
        font-size: 12px;
        color: #7a7a7a;
    }

    .guru-form .error {
        font-size: 12.5px;
        color: #b91c1c;
    }

    .guru-form .existing-file {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #43302b;
    }

    .guru-form .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid #f2e9e5;
    }

    .guru-form .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 1px solid var(--maroon);
        border-radius: 10px;
        background: #fff;
        color: var(--maroon);
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .guru-form .btn:hover {
        background: var(--maroon);
        color: #fff;
    }

    .guru-form .btn-primary {
        background: var(--maroon);
        color: #fff;
    }

    .guru-form .alert-danger {
        margin-bottom: 16px;
        padding: 12px 16px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 13.5px;
    }

    @media (max-width: 760px) {
        .guru-form .grid {
            grid-template-columns: 1fr;
        }

        .guru-form h1 {
            font-size: 21px;
        }
    }
</style>

@php
    $penugasanTersedia = collect($penugasan);
    $mapelTerpilih = old('mata_pelajaran_id', $tugas?->mata_pelajaran_id);
    $kelompokTerpilih = old('kelompok', $tugas?->kelompok);

    if ($kelompokTerpilih && ! $penugasanTersedia->contains(
        fn ($item) => (string) $item['mata_pelajaran_id'] === (string) $mapelTerpilih
            && $item['kelompok'] === $kelompokTerpilih
    )) {
        $penugasanTersedia->push([
            'mata_pelajaran_id' => $mapelTerpilih,
            'nama_mata_pelajaran' => $tugas?->namaMataPelajaran() ?? 'Mata Pelajaran',
            'kelompok' => $kelompokTerpilih,
        ]);
    }

    $pasanganTerpilih = $mapelTerpilih !== null && $kelompokTerpilih !== null
        ? $mapelTerpilih.'|'.$kelompokTerpilih
        : '';
@endphp

<div class="guru-form">
    <div class="page-header">
        <h1>{{ $mode === 'edit' ? 'Edit Tugas' : 'Buat Tugas Baru' }}</h1>
        <p class="page-subtitle">
            Tugas hanya dapat diberikan pada mata pelajaran dan kelompok yang ditugaskan kepada Anda.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert-danger">{{ $errors->first() }}</div>
    @endif

    <form
        method="POST"
        action="{{ $mode === 'edit' ? route('guru.tugas.update', $tugas) : route('guru.tugas.store') }}"
        enctype="multipart/form-data"
        class="card"
    >
        @csrf
        @if ($mode === 'edit')
            @method('PUT')
        @endif

        <div class="grid">
            <div class="field field-full">
                <label for="judul">Judul Tugas</label>
                <input
                    type="text"
                    id="judul"
                    name="judul"
                    value="{{ old('judul', $tugas?->judul) }}"
                    maxlength="180"
                    required
                >
                @error('judul')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field field-full">
                <label for="pilihPenugasan">Mata Pelajaran &amp; Kelompok Tujuan</label>
                <select id="pilihPenugasan" required>
                    <option value="">Pilih mata pelajaran dan kelompok</option>
                    @foreach ($penugasanTersedia as $item)
                        @php $nilai = $item['mata_pelajaran_id'].'|'.$item['kelompok']; @endphp
                        <option
                            value="{{ $nilai }}"
                            data-mapel="{{ $item['mata_pelajaran_id'] }}"
                            data-kelompok="{{ $item['kelompok'] }}"
                            @selected($pasanganTerpilih === $nilai)
                        >
                            {{ $item['nama_mata_pelajaran'] }} — {{ $item['kelompok'] }}
                        </option>
                    @endforeach
                </select>

                @if ($penugasanTersedia->isEmpty())
                    <span class="hint">
                        Anda belum memiliki penugasan jadwal. Hubungi Musyrifah untuk penetapan jadwal
                        mata pelajaran dan kelompok.
                    </span>
                @endif

                <input type="hidden" name="mata_pelajaran_id" id="inputMataPelajaran" value="{{ $mapelTerpilih }}">
                <input type="hidden" name="kelompok" id="inputKelompok" value="{{ $kelompokTerpilih }}">

                @error('mata_pelajaran_id')
                    <span class="error">{{ $message }}</span>
                @enderror
                @error('kelompok')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field field-full">
                <label for="deskripsi">Deskripsi dan Instruksi Tugas</label>
                <textarea id="deskripsi" name="deskripsi" maxlength="20000" required>{{ old('deskripsi', $tugas?->deskripsi) }}</textarea>
                @error('deskripsi')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field field-full">
                <label for="lampiran">Lampiran Tugas (opsional)</label>
                <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png">
                <span class="hint">Format PDF, JPG, JPEG, atau PNG. Maksimal 5 MB.</span>

                @if ($tugas?->punyaLampiran())
                    <div class="existing-file">
                        <span>Lampiran saat ini:</span>
                        <a href="{{ route('guru.tugas.lampiran', $tugas) }}">{{ $tugas->lampiran_nama }}</a>
                    </div>
                @endif

                @error('lampiran')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input
                    type="date"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ old('tanggal_mulai', $tugas?->tanggal_mulai?->toDateString() ?? now()->toDateString()) }}"
                    required
                >
                @error('tanggal_mulai')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="batas_pengumpulan">Batas Pengumpulan</label>
                <input
                    type="datetime-local"
                    id="batas_pengumpulan"
                    name="batas_pengumpulan"
                    value="{{ old('batas_pengumpulan', $tugas?->batas_pengumpulan?->format('Y-m-d\TH:i')) }}"
                    required
                >
                @error('batas_pengumpulan')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    @foreach ($statusPilihan as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('status', $tugas?->status ?? \App\Models\Tugas::STATUS_DRAFT) === $nilai)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <span class="hint">Tugas berstatus Draft tidak tampil pada akun mahasiswa.</span>
                @error('status')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Simpan Tugas</button>
            <a href="{{ route('guru.tugas.index') }}" class="btn">Batal</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pilih = document.getElementById('pilihPenugasan');
        const inputMapel = document.getElementById('inputMataPelajaran');
        const inputKelompok = document.getElementById('inputKelompok');

        if (!pilih || !inputMapel || !inputKelompok) return;

        function sinkronkan() {
            const opsi = pilih.options[pilih.selectedIndex];

            inputMapel.value = opsi ? (opsi.dataset.mapel || '') : '';
            inputKelompok.value = opsi ? (opsi.dataset.kelompok || '') : '';
        }

        pilih.addEventListener('change', sinkronkan);
        sinkronkan();
    });
</script>

@endsection
