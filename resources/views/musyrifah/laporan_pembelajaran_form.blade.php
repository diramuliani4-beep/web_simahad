@extends('layouts.musyrifah')

@section('title', $laporan ? 'Edit Laporan Pembelajaran' : 'Tambah Laporan Pembelajaran')
@section('content')
<style>
    .lp {
        --lp-maroon: #800b22;
        --lp-deep: #620819;
        --lp-ink: #292427;
        --lp-muted: #82797e;
        --lp-border: #eddee1;
        box-sizing: border-box;
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0;
        margin: 0 !important;
        padding: 0 0 28px;
        color: var(--lp-ink);
        font-family: inherit;
        font-size: 13px;
        container-type: inline-size;
    }

    .lp *, .lp *::before, .lp *::after { box-sizing: border-box; }
    .lp a { text-decoration: none; }
    .lp button, .lp input, .lp select, .lp textarea { font-family: inherit; }
    .lp [hidden] { display: none !important; }

    .lp .lp-head { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 15px; margin: 0 0 21px; padding: 0 0 19px; border-bottom: 1px solid #e8cfd6; }
    .lp .lp-head-intro { flex: 1 1 320px; min-width: 0; }
    .lp .lp-breadcrumb { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; margin: 0 0 11px; color: #8e8487; font-size: 12px; }
    .lp .lp-breadcrumb a { color: var(--lp-maroon); }
    .lp .lp-breadcrumb a:hover { text-decoration: underline; }
    .lp .lp-head h1 { margin: 0 0 6px; color: #282126; font-size: clamp(22px, 2.5vw, 27px); font-weight: 750; line-height: 1.3; letter-spacing: -.55px; }
    .lp .lp-head p { margin: 0; max-width: 760px; color: #797076; font-size: 12.5px; line-height: 1.65; }

    .lp .lp-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 40px; padding: 9px 14px; border: 1px solid transparent; border-radius: 9px; background: #fff; color: var(--lp-maroon); font-size: 12px; font-weight: 700; line-height: 1.25; cursor: pointer; transition: background .18s ease, border-color .18s ease; }
    .lp .lp-btn--utama { color: #fff; background: var(--lp-maroon); border-color: var(--lp-maroon); }
    .lp .lp-btn--utama:hover { background: var(--lp-deep); border-color: var(--lp-deep); }
    .lp .lp-btn--garis { color: var(--lp-maroon); border-color: #e2cfd4; }
    .lp .lp-btn--garis:hover { background: #fdf4f6; border-color: #bc9ca5; }
    .lp .lp-btn:focus-visible { outline: 2px solid #9b4962; outline-offset: 2px; }

    .lp .lp-kartu { min-width: 0; margin: 0 0 19px; border: 1px solid var(--lp-border); border-radius: 13px; background: #fff; box-shadow: 0 3px 14px rgba(62,10,25,.035); overflow: hidden; }
    .lp .lp-sec-head { padding: 19px 23px; border-bottom: 1px solid #f0e8e9; }
    .lp .lp-sec-head h2 { margin: 0 0 5px; color: #2c2529; font-size: 16px; font-weight: 750; line-height: 1.3; }
    .lp .lp-sec-head p { margin: 0; color: var(--lp-muted); font-size: 11.5px; line-height: 1.65; }
    .lp .lp-mapel { display: inline-flex; margin-top: 10px; padding: 6px 10px; border-radius: 7px; background: #f8eef1; color: #7c2439; font-size: 11px; font-weight: 750; }

    .lp .lp-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 17px; padding: 21px 23px 23px; }
    .lp .lp-field { display: flex; flex-direction: column; gap: 7px; min-width: 0; }
    .lp .lp-field--full { grid-column: 1 / -1; }
    .lp .lp-field label, .lp .lp-legend { color: #55484d; font-size: 11.5px; font-weight: 700; line-height: 1.4; }
    .lp .lp-wajib { color: #a0223d; }
    .lp .lp-field input, .lp .lp-field select, .lp .lp-field textarea { display: block; width: 100%; min-width: 0; padding: 10px 12px; border: 1px solid #e5d6da; border-radius: 9px; background: #fff; color: #332a2e; font-size: 12px; line-height: 1.5; outline: none; transition: border-color .18s, box-shadow .18s, background .18s; }
    .lp .lp-field input, .lp .lp-field select { height: 43px; }
    .lp .lp-field textarea { min-height: 118px; resize: vertical; }
    .lp .lp-field textarea.lp-textarea-panjang { min-height: 150px; }
    .lp .lp-field input:focus, .lp .lp-field select:focus, .lp .lp-field textarea:focus { border-color: #a96075; box-shadow: 0 0 0 3px rgba(128,11,34,.08); }
    .lp .lp-field input.is-source, .lp .lp-field select.is-source, .lp .lp-field textarea.is-source { border-color: #dbe5ec; background: #f7fafc; color: #59636c; }
    .lp .lp-help { margin: 0; color: #93858c; font-size: 10.5px; line-height: 1.55; }
    .lp .lp-error { color: #a0223d; font-size: 10.5px; font-weight: 650; line-height: 1.45; }

    .lp .lp-source-info { display: flex; align-items: flex-start; gap: 10px; margin: 0 23px 20px; padding: 13px 15px; border: 1px solid #dbe9df; border-radius: 10px; background: #f3faf5; color: #276041; font-size: 11px; line-height: 1.65; }
    .lp .lp-source-info svg { margin-top: 1px; width: 17px; height: 17px; flex: none; }
    .lp .lp-source-info strong { font-weight: 750; }
    .lp .lp-manual-info { margin: 0 23px 20px; padding: 13px 15px; border: 1px solid #eadfcf; border-radius: 10px; background: #fffaf0; color: #795f27; font-size: 11px; line-height: 1.65; }

    .lp fieldset { min-width: 0; margin: 0; padding: 18px; border: 1px solid #eee3e6; border-radius: 11px; background: #fdfafb; }
    .lp .lp-absensi-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 13px; }
    .lp .lp-form-aksi { display: flex; align-items: center; flex-wrap: wrap; gap: 9px; grid-column: 1 / -1; padding-top: 3px; }
    .lp .lp-form-aksi .lp-btn--utama { min-width: 145px; }

    @container (max-width: 760px) {
        .lp .lp-form { grid-template-columns: minmax(0, 1fr); padding: 17px !important; }
        .lp .lp-field--full, .lp .lp-form-aksi { grid-column: auto; }
        .lp .lp-sec-head { padding: 17px !important; }
        .lp .lp-source-info, .lp .lp-manual-info { margin: 0 17px 17px; }
        .lp .lp-absensi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @container (max-width: 390px) {
        .lp .lp-absensi-grid { grid-template-columns: minmax(0, 1fr); }
        .lp .lp-form-aksi { align-items: stretch; flex-direction: column; }
    }
</style>

@php
    $pertemuanId = (string) old('pertemuan_id', $laporan?->pertemuan_id ?? $pertemuanTerpilih?->id ?? '');
    $pertemuanSumber = $daftarPertemuan->firstWhere('id', (int) $pertemuanId);
    $pertemuanId = $pertemuanSumber ? (string) $pertemuanSumber->id : '';
    $tanggal = old('tanggal', $laporan?->tanggal?->format('Y-m-d') ?? $pertemuanSumber?->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d'));
    $kelompok = old('kelompok', $laporan?->kelompok ?? $pertemuanSumber?->kelompok ?? ($filter['kelompok'] ?? ''));
    $periodeSemester = old('periode_semester', $laporan?->periode_semester ?? $pertemuanSumber?->periode ?? '');
    $materi = old('materi', $laporan?->materi ?? $pertemuanSumber?->materi ?? '');
    $instruktur = old('instruktur', $laporan?->instruktur ?? $pertemuanSumber?->guru?->name ?? $pertemuanSumber?->jadwal?->guru_pengampu ?? '');
    $parameterKembali = array_filter([
        'mata_pelajaran' => $mataPelajaran->id,
        'periode' => $filter['periode'] ?? '',
        'kelompok' => $filter['kelompok'] ?? '',
        'cari' => $filter['cari'] ?? '',
    ], fn ($nilai) => filled($nilai));
@endphp

<div class="lp">
    <header class="lp-head">
        <div class="lp-head-intro">
            <nav class="lp-breadcrumb" aria-label="Navigasi halaman">
                <a href="{{ route('musyrifah.dashboard') }}">Dashboard</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('musyrifah.laporan') }}">Laporan</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('musyrifah.laporan.pembelajaran.index', $parameterKembali) }}">Pembelajaran</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $laporan ? 'Edit' : 'Tambah' }}</span>
            </nav>
            <h1>{{ $laporan ? 'Edit Laporan Pembelajaran' : 'Tambah Laporan Pembelajaran' }}</h1>
            <p>{{ $laporan ? 'Perbarui catatan laporan tanpa mengubah sumber data pembelajaran.' : 'Tambahkan laporan manual atau lengkapi tanda tangan dan catatan untuk satu pertemuan.' }}</p>
        </div>
    </header>

    <section class="lp-kartu" aria-labelledby="judul-form">
        <div class="lp-sec-head">
            <div>
                <h2 id="judul-form">Data Laporan</h2>
                <p>Kolom bertanda <span class="lp-wajib">*</span> wajib diisi untuk laporan manual.</p>
                <span class="lp-mapel">{{ $mataPelajaran->nama }}</span>
            </div>
        </div>

        <div class="lp-source-info" id="lp-source-info" @if ($pertemuanId === '') hidden @endif>
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            <span><strong>Laporan terhubung ke pertemuan.</strong> Tanggal, kelompok, periode, materi, instruktur, dan absensi mengikuti data pembelajaran. Isian yang terkunci akan disesuaikan kembali saat laporan disimpan.</span>
        </div>
        <div class="lp-manual-info" id="lp-manual-info" @if ($pertemuanId !== '') hidden @endif>
            Laporan manual digunakan untuk kegiatan atau catatan yang belum memiliki data pertemuan. Data ini hanya tampil pada mata pelajaran yang dipilih.
        </div>

        <form method="POST" action="{{ $formAction }}" class="lp-form" id="lp-form">
            @csrf
            @if ($laporan)
                @method('PUT')
            @endif
            <input type="hidden" name="mata_pelajaran_id" value="{{ $mataPelajaran->id }}">

            <div class="lp-field lp-field--full">
                <label for="lp-pertemuan">Hubungkan ke Pertemuan <span class="lp-help">(opsional)</span></label>
                <select id="lp-pertemuan" name="pertemuan_id" data-lp-meeting>
                    <option value="">Laporan manual / tanpa pertemuan</option>
                    @foreach ($daftarPertemuan as $item)
                        @php
                            $sudahDilaporkan = $item->laporanPembelajaran->isNotEmpty();
                            $laporanIni = $laporan && (int) $item->id === (int) $laporan->pertemuan_id;
                            $instrukturPertemuan = $item->guru?->name ?: ($item->jadwal?->guru_pengampu ?? '');
                        @endphp
                        <option value="{{ $item->id }}"
                                @selected($pertemuanId === (string) $item->id)
                                @disabled($sudahDilaporkan && ! $laporanIni)
                                data-tanggal="{{ $item->tanggal?->format('Y-m-d') }}"
                                data-kelompok="{{ $item->kelompok }}"
                                data-periode="{{ $item->periode }}"
                                data-materi="{{ $item->materi }}"
                                data-instruktur="{{ $instrukturPertemuan }}">
                            Pertemuan {{ $item->pertemuan_ke }} · {{ $item->kelompok }} · {{ $item->tanggal?->format('d/m/Y') }}{{ $sudahDilaporkan ? ' · Sudah memiliki laporan' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="lp-help">Pilih pertemuan untuk melengkapi laporan otomatis. Satu pertemuan hanya dapat memiliki satu laporan tambahan.</p>
                @error('pertemuan_id') <span class="lp-error">{{ $message }}</span> @enderror
            </div>

            <div class="lp-field">
                <label for="lp-tanggal">Tanggal <span class="lp-wajib">*</span></label>
                <input type="date" id="lp-tanggal" name="tanggal" value="{{ $tanggal }}" data-lp-source>
                @error('tanggal') <span class="lp-error">{{ $message }}</span> @enderror
            </div>

            <div class="lp-field">
                <label for="lp-kelompok">Kelompok</label>
                <input type="text" id="lp-kelompok" name="kelompok" value="{{ $kelompok }}" maxlength="60" list="lp-daftar-kelompok" data-lp-source>
                <datalist id="lp-daftar-kelompok">
                    @foreach ($daftarPertemuan->pluck('kelompok')->unique() as $item)
                        <option value="{{ $item }}"></option>
                    @endforeach
                </datalist>
                @error('kelompok') <span class="lp-error">{{ $message }}</span> @enderror
            </div>

            <div class="lp-field">
                <label for="lp-periode-semester">Periode Semester</label>
                <input type="text" id="lp-periode-semester" name="periode_semester" value="{{ $periodeSemester }}" maxlength="100" placeholder="Contoh: Semester Ganjil 2026/2027" data-lp-source>
                @error('periode_semester') <span class="lp-error">{{ $message }}</span> @enderror
            </div>

            <div class="lp-field">
                <label for="lp-instruktur">Instruktur / Pengajar</label>
                <input type="text" id="lp-instruktur" name="instruktur" value="{{ $instruktur }}" maxlength="255" data-lp-source>
                @error('instruktur') <span class="lp-error">{{ $message }}</span> @enderror
            </div>

            <div class="lp-field lp-field--full">
                <label for="lp-materi">Materi <span class="lp-wajib">*</span></label>
                <textarea id="lp-materi" name="materi" maxlength="5000" class="lp-textarea-panjang" data-lp-source>{{ $materi }}</textarea>
                <p class="lp-help">Untuk laporan yang terhubung ke pertemuan, materi ditampilkan dari data pembelajaran dan tidak perlu diubah di sini.</p>
                @error('materi') <span class="lp-error">{{ $message }}</span> @enderror
            </div>

            <fieldset class="lp-field--full">
                <legend class="lp-legend">Rekap Kehadiran Manual</legend>
                <p class="lp-help">Untuk laporan terhubung, rekap ini mengikuti absensi pertemuan dan tidak digunakan sebagai sumber angka laporan.</p>
                <div class="lp-absensi-grid">
                    <div class="lp-field">
                        <label for="lp-hadir">Hadir</label>
                        <input type="number" id="lp-hadir" name="jumlah_hadir" min="0" max="500" value="{{ old('jumlah_hadir', $laporan?->jumlah_hadir ?? 0) }}" data-lp-source>
                        @error('jumlah_hadir') <span class="lp-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="lp-field">
                        <label for="lp-izin">Izin</label>
                        <input type="number" id="lp-izin" name="jumlah_izin" min="0" max="500" value="{{ old('jumlah_izin', $laporan?->jumlah_izin ?? 0) }}" data-lp-source>
                        @error('jumlah_izin') <span class="lp-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="lp-field">
                        <label for="lp-sakit">Sakit</label>
                        <input type="number" id="lp-sakit" name="jumlah_sakit" min="0" max="500" value="{{ old('jumlah_sakit', $laporan?->jumlah_sakit ?? 0) }}" data-lp-source>
                        @error('jumlah_sakit') <span class="lp-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="lp-field">
                        <label for="lp-alpa">Alpa</label>
                        <input type="number" id="lp-alpa" name="jumlah_alpa" min="0" max="500" value="{{ old('jumlah_alpa', $laporan?->jumlah_alpa ?? 0) }}" data-lp-source>
                        @error('jumlah_alpa') <span class="lp-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </fieldset>

            <div class="lp-field">
                <label for="lp-tanda-tangan">Tanda Tangan / Inisial</label>
                <input type="text" id="lp-tanda-tangan" name="tanda_tangan" value="{{ old('tanda_tangan', $laporan?->tanda_tangan) }}" maxlength="255" placeholder="Contoh: IH">
                <p class="lp-help">Ditampilkan pada kolom tanda tangan dokumen cetak.</p>
                @error('tanda_tangan') <span class="lp-error">{{ $message }}</span> @enderror
            </div>

            <div class="lp-field">
                <label for="lp-catatan">Catatan Tambahan</label>
                <textarea id="lp-catatan" name="catatan" maxlength="2000">{{ old('catatan', $laporan?->catatan) }}</textarea>
                @error('catatan') <span class="lp-error">{{ $message }}</span> @enderror
            </div>

            <div class="lp-form-aksi">
                <button type="submit" class="lp-btn lp-btn--utama">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
                    {{ $laporan ? 'Simpan Perubahan' : 'Simpan Laporan' }}
                </button>
                <a href="{{ route('musyrifah.laporan.pembelajaran.index', $parameterKembali) }}" class="lp-btn lp-btn--garis">Batal</a>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('lp-form');
        if (!form) {
            return;
        }

        const meeting = form.querySelector('[data-lp-meeting]');
        const sourceFields = form.querySelectorAll('[data-lp-source]');
        const sourceInfo = document.getElementById('lp-source-info');
        const manualInfo = document.getElementById('lp-manual-info');
        const targets = {
            tanggal: form.querySelector('#lp-tanggal'),
            kelompok: form.querySelector('#lp-kelompok'),
            periode: form.querySelector('#lp-periode-semester'),
            materi: form.querySelector('#lp-materi'),
            instruktur: form.querySelector('#lp-instruktur'),
        };

        function syncSourceFields() {
            const option = meeting.options[meeting.selectedIndex];
            const linked = option.value !== '';

            if (linked) {
                Object.entries(targets).forEach(function ([key, field]) {
                    field.value = option.dataset[key] || '';
                });
            }

            sourceFields.forEach(function (field) {
                field.readOnly = linked;
                field.classList.toggle('is-source', linked);
            });
            sourceInfo.hidden = !linked;
            manualInfo.hidden = linked;
        }

        meeting.addEventListener('change', syncSourceFields);
        syncSourceFields();
    });
</script>
@endpush
