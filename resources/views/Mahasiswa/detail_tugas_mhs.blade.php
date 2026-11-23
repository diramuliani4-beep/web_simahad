@extends('layouts.mahasiswa')

@section('title', 'Detail Tugas')

@section('content')
<style>
    /* SIMAHAD — Detail Tugas Mahasiswa.
       Semua gaya dibatasi pada halaman ini. Tidak mengubah route atau proses unggah. */
    .detail-tugas {
        --dt-maroon: #800b22;
        --dt-maroon-dark: #64081b;
        --dt-tint: #fff6f8;
        --dt-gold: #c19245;
        --dt-line: #eadfe3;
        font-family: 'Plus Jakarta Sans', 'Poppins', Arial, sans-serif;
        font-size: 13px;
        line-height: 1.55;
        color: #292229;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        padding-bottom: 30px;
        container-type: inline-size;
    }
    .detail-tugas *, .detail-tugas *::before, .detail-tugas *::after { box-sizing: border-box; }
    .detail-tugas .back-link {
        display: inline-flex; align-items: center; gap: 7px;
        margin: 0 0 17px; font-size: 12px; font-weight: 700;
        color: var(--dt-maroon); text-decoration: none;
    }
    .detail-tugas .back-link:hover { color: var(--dt-maroon-dark); text-decoration: underline; }
    .detail-tugas .back-link svg { flex: 0 0 15px; }
    .detail-tugas .layout {
        display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr);
        gap: 16px; align-items: start;
    }
    .detail-tugas .layout > div { min-width: 0; }
    .detail-tugas .card {
        border: 1px solid var(--dt-line); border-radius: 14px;
        background: #fff; padding: 20px 22px; min-width: 0;
        box-shadow: 0 3px 13px rgba(77, 22, 39, .025);
    }
    .detail-tugas .card + .card { margin-top: 16px; }
    .detail-tugas .card-title {
        display: flex; align-items: center; gap: 8px;
        margin: 0 0 14px; color: #292229; font-size: 15px; font-weight: 800;
    }
    .detail-tugas .card-title svg { color: var(--dt-maroon); flex: 0 0 16px; }
    .detail-tugas .tugas-mapel {
        color: var(--dt-maroon); font-size: 11px; font-weight: 800;
        letter-spacing: .04em; line-height: 1.6; text-transform: uppercase;
    }
    .detail-tugas .tugas-heading { min-width: 0; flex: 1 1 260px; }
    .detail-tugas .tugas-judul {
        margin: 4px 0 9px; color: #261c24; font-size: clamp(19px, 2.1vw, 23px);
        font-weight: 800; line-height: 1.4; overflow-wrap: anywhere;
    }
    .detail-tugas .kelas-info {
        display: inline-flex; align-items: center; gap: 6px;
        border: 1px solid #ead5dc; border-radius: 8px; padding: 5px 9px;
        background: var(--dt-tint); color: #80213b; font-size: 11px; font-weight: 700;
        max-width: 100%; overflow-wrap: anywhere;
    }
    .detail-tugas .badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 800; line-height: 1.45;
        white-space: normal; text-align: center;
    }
    .detail-tugas .badge.belum { background: #fff2d9; color: #986310; }
    .detail-tugas .badge.terkumpul { background: #e6f0ff; color: #245c9e; }
    .detail-tugas .badge.terlambat { background: #ffebed; color: #ad2635; }
    .detail-tugas .badge.dinilai { background: #e9f7ee; color: #167445; }
    .detail-tugas .meta-grid {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px; margin: 17px 0 4px; padding: 16px 0 0;
        border-top: 1px dashed #eadbe0;
    }
    .detail-tugas .meta-item { min-width: 0; }
    .detail-tugas .meta-label {
        display: block; margin-bottom: 4px; color: #93838c;
        font-size: 10.5px; font-weight: 700; line-height: 1.4;
    }
    .detail-tugas .meta-value {
        display: block; color: #493a43; font-size: 12px;
        font-weight: 700; line-height: 1.5; overflow-wrap: anywhere;
    }
    .detail-tugas .deskripsi {
        color: #554950; font-size: 12.5px; line-height: 1.75;
        white-space: pre-line; overflow-wrap: anywhere;
    }
    .detail-tugas .lampiran-box {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px; margin: 17px 0 0;
        padding: 12px 14px; border: 1px dashed #ddc7ce;
        border-radius: 10px; background: #fffafc;
    }
    .detail-tugas .lampiran-info { display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1 1 180px; }
    .detail-tugas .lampiran-info svg { flex: 0 0 20px; color: var(--dt-maroon); }
    .detail-tugas .lampiran-nama {
        color: #493a43; font-size: 12px; font-weight: 700;
        overflow-wrap: anywhere; min-width: 0;
    }
    .detail-tugas .btn-outline {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        min-height: 36px; padding: 8px 12px;
        border: 1px solid #e1c8d1; border-radius: 9px;
        background: #fff; color: var(--dt-maroon);
        font-family: inherit; font-size: 11.5px; font-weight: 700;
        line-height: 1.4; text-decoration: none;
        cursor: pointer;
    }
    .detail-tugas .btn-outline:hover { background: var(--dt-tint); border-color: #bd95a2; }
    .detail-tugas .field { margin: 0 0 13px; }
    .detail-tugas .field label { display: block; margin-bottom: 6px; color: #493a43; font-size: 11.5px; font-weight: 700; }
    .detail-tugas .field input[type="file"], .detail-tugas .field textarea {
        display: block; width: 100%; min-width: 0;
        padding: 10px 12px; border: 1px solid #e2d3d8;
        border-radius: 9px; background: #fff; color: #342a30;
        font-family: inherit; font-size: 12px;
    }
    .detail-tugas .field textarea { min-height: 82px; resize: vertical; }
    .detail-tugas .field textarea:focus, .detail-tugas .field input[type="file"]:focus {
        outline: 3px solid rgba(128,11,34,.10); border-color: #b98596;
    }
    .detail-tugas .hint { margin-top: 6px; color: #81757e; font-size: 11px; line-height: 1.55; }
    .detail-tugas .btn-primary {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 8px; width: 100%; min-height: 40px; padding: 10px 15px;
        border: 1px solid var(--dt-maroon); border-radius: 9px;
        background: var(--dt-maroon); color: #fff;
        font-family: inherit; font-size: 12px; font-weight: 700; cursor: pointer;
    }
    .detail-tugas .btn-primary:hover { background: var(--dt-maroon-dark); border-color: var(--dt-maroon-dark); }
    .detail-tugas .back-link:focus-visible, .detail-tugas a:focus-visible,
    .detail-tugas button:focus-visible { outline: 3px solid rgba(193,146,69,.4); outline-offset: 2px; }
    .detail-tugas .alert {
        margin: 0 0 13px; padding: 11px 13px; border: 1px solid transparent;
        border-radius: 9px; font-size: 12px; font-weight: 600; line-height: 1.6;
    }
    .detail-tugas .alert-info { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
    .detail-tugas .alert-warning { background: #fffbeb; border-color: #fcd34d; color: #92400e; }
    .detail-tugas .alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
    .detail-tugas .alert-danger { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
    .detail-tugas .berkas-aktif {
        margin-bottom: 14px; padding: 12px 14px;
        border: 1px solid #dbeafe; border-radius: 10px; background: #f8fbff;
    }
    .detail-tugas .berkas-nama { color: #1e3a8a; font-size: 12px; font-weight: 700; overflow-wrap: anywhere; }
    .detail-tugas .berkas-waktu { margin-top: 4px; color: #4b5563; font-size: 11px; line-height: 1.6; }
    .detail-tugas .berkas-aksi { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 9px; }
    .detail-tugas .nilai-box { padding: 6px 0 3px; text-align: center; }
    .detail-tugas .nilai-angka { color: var(--dt-maroon); font-size: 42px; font-weight: 800; line-height: 1.12; }
    .detail-tugas .nilai-skala { color: #81757e; font-size: 11px; font-weight: 600; }
    .detail-tugas .komentar-guru {
        margin-top: 15px; padding: 12px 14px;
        border-left: 3px solid var(--dt-gold); border-radius: 0 9px 9px 0;
        background: #fffaf0; color: #554950;
        font-size: 12px; line-height: 1.7; white-space: pre-line; overflow-wrap: anywhere;
    }
    .detail-tugas .komentar-label {
        display: block; margin-bottom: 4px;
        color: #94611a; font-size: 10.5px; font-weight: 800; text-transform: uppercase;
    }
    .detail-tugas .empty-note { color: #81757e; font-size: 12px; line-height: 1.7; }
    @container (max-width: 850px) {
        .detail-tugas .layout { grid-template-columns: minmax(0, 1fr); }
    }
    @container (max-width: 460px) {
        .detail-tugas .card { padding: 16px; }
        .detail-tugas .meta-grid { grid-template-columns: minmax(0, 1fr); }
        .detail-tugas .lampiran-box { align-items: flex-start; }
        .detail-tugas .lampiran-box .btn-outline { width: 100%; }
    }
</style>

<div class="detail-tugas">
    <a href="{{ route('tugas.mhs.index') }}" class="back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Kembali ke daftar tugas
    </a>

    <div class="layout">
        <div>
            <section class="card" aria-labelledby="dt-judul">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <div class="tugas-heading">
                        <div class="tugas-mapel">{{ $tugas->namaMataPelajaran() }}</div>
                        <h1 class="tugas-judul" id="dt-judul">{{ $tugas->judul }}</h1>
                        <span class="kelas-info">{{ $tugas->namaMataPelajaran() }} · {{ $tugas->kelompok }}</span>
                    </div>
                    <span class="badge {{ $status['kelas'] }}">{{ $status['label'] }}</span>
                </div>

                <div class="meta-grid">
                    <div class="meta-item">
                        <span class="meta-label">Guru Pengampu</span>
                        <strong class="meta-value">{{ $tugas->namaGuru() }}</strong>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Kelompok Mata Pelajaran</span>
                        <strong class="meta-value">{{ $tugas->kelompok }}</strong>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Tanggal Pemberian</span>
                        <strong class="meta-value">{{ $tugas->tanggalMulaiTeks() }}</strong>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Batas Pengumpulan</span>
                        <strong class="meta-value">{{ $tugas->batasTeks() }}</strong>
                    </div>
                </div>

                @if ($tugas->punyaLampiran())
                    <div class="lampiran-box">
                        <div class="lampiran-info">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                            <span class="lampiran-nama">{{ $tugas->lampiran_nama ?: 'Lampiran tugas' }}</span>
                        </div>
                        <a href="{{ route('tugas.mhs.lampiran', $tugas) }}" class="btn-outline">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5M12 15V3"/></svg>
                            Unduh Lampiran
                        </a>
                    </div>
                @endif

                <h2 class="card-title" style="margin-top:21px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                    Deskripsi dan Instruksi
                </h2>
                <div class="deskripsi">{{ $tugas->deskripsi }}</div>
            </section>
        </div>

        <div>
            <section class="card" aria-labelledby="dt-pengumpulan">
                <h2 class="card-title" id="dt-pengumpulan">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5M12 3v12"/></svg>
                    Pengumpulan Tugas
                </h2>

                @if (session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
                @endif

                @if ($pengumpulan)
                    <div class="berkas-aktif">
                        <div class="berkas-nama">{{ $pengumpulan->file_nama ?: 'Berkas tugas' }}</div>
                        <div class="berkas-waktu">Dikumpulkan {{ $pengumpulan->dikumpulkanTeks() }}</div>
                        @if ($pengumpulan->terlambat())
                            <div class="berkas-waktu" style="color:#b91c1c; font-weight:700;">Dikumpulkan melewati batas waktu.</div>
                        @endif
                        @if (filled($pengumpulan->keterangan))
                            <div class="berkas-waktu">Catatan: {{ $pengumpulan->keterangan }}</div>
                        @endif
                        <div class="berkas-aksi">
                            <a href="{{ route('tugas.mhs.berkas', $tugas) }}" class="btn-outline">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5M12 15V3"/></svg>
                                Lihat Berkas
                            </a>
                        </div>
                    </div>
                @endif

                @if ($tugas->masihDapatDikumpulkan())
                    @if ($pengumpulan)
                        <div class="alert alert-info">Anda masih dapat mengganti berkas sebelum batas pengumpulan.</div>
                    @endif
                    <form method="POST" action="{{ route('tugas.mhs.kumpulkan', $tugas) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="field">
                            <label for="dt-file">{{ $pengumpulan ? 'Ganti Berkas Tugas' : 'Unggah Berkas Tugas' }}</label>
                            <input type="file" name="file" id="dt-file" accept=".jpg,.jpeg,.png,.pdf" aria-describedby="dt-file-hint" required>
                            <div class="hint" id="dt-file-hint">Format JPG, JPEG, PNG, atau PDF. Maksimal 5 MB.</div>
                        </div>
                        <div class="field">
                            <label for="dt-keterangan">Catatan untuk Guru (opsional)</label>
                            <textarea name="keterangan" id="dt-keterangan" maxlength="1000" placeholder="Tulis catatan bila diperlukan...">{{ old('keterangan', $pengumpulan->keterangan ?? '') }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m17 8-5-5-5 5M12 3v12M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/></svg>
                            {{ $pengumpulan ? 'Kirim Ulang Berkas' : 'Kumpulkan Tugas' }}
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning">Batas pengumpulan tugas ini telah berakhir pada {{ $tugas->batasTeks() }}.</div>
                    @unless ($pengumpulan)
                        <p class="empty-note">Anda belum mengumpulkan tugas ini dan batas waktu sudah terlewati.</p>
                    @endunless
                @endif
            </section>

            <section class="card" aria-labelledby="dt-nilai">
                <h2 class="card-title" id="dt-nilai">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01z"/></svg>
                    Nilai Tugas
                </h2>
                @if ($pengumpulan && $pengumpulan->nilaiDipublikasikan())
                    <div class="nilai-box">
                        <div class="nilai-angka">{{ $pengumpulan->nilai }}</div>
                        <div class="nilai-skala">dari 100</div>
                    </div>
                    @if (filled($pengumpulan->komentar_guru))
                        <div class="komentar-guru">
                            <span class="komentar-label">Komentar Guru</span>
                            {{ $pengumpulan->komentar_guru }}
                        </div>
                    @endif
                    <div class="hint" style="text-align:center;">Dinilai {{ $pengumpulan->dinilaiTeks() }}</div>
                @elseif ($pengumpulan && $pengumpulan->sudahDinilai())
                    <div class="alert alert-info">Nilai sudah diberikan oleh Guru dan akan tampil setelah dipublikasikan.</div>
                @elseif ($pengumpulan)
                    <p class="empty-note">Tugas Anda sedang menunggu penilaian Guru.</p>
                @else
                    <p class="empty-note">Nilai akan tampil setelah Anda mengumpulkan tugas dan Guru memublikasikan penilaian.</p>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
