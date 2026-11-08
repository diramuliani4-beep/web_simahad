@extends('layouts.mahasiswa')

@section('title', 'Detail Tugas')

@section('content')

<style>
    .detail-tugas {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #111827;
    }

    .detail-tugas * ,
    .detail-tugas *::before,
    .detail-tugas *::after {
        box-sizing: border-box;
    }

    .detail-tugas .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 14px;
        font-size: 0.83rem;
        font-weight: 600;
        color: #6b7280;
        text-decoration: none;
    }

    .detail-tugas .back-link:hover {
        color: var(--primary-maroon);
    }

    .detail-tugas .layout {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .detail-tugas .card {
        border: 1px solid #eadfda;
        border-radius: 14px;
        background: #fff;
        padding: 20px 22px;
    }

    .detail-tugas .card + .card {
        margin-top: 16px;
    }

    .detail-tugas .card-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 14px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #111827;
    }

    .detail-tugas .card-title svg {
        color: var(--primary-maroon);
    }

    .detail-tugas .tugas-mapel {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--primary-maroon);
    }

    .detail-tugas .tugas-judul {
        margin: 4px 0 10px;
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.35;
        color: #111827;
        word-break: break-word;
    }

    .detail-tugas .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .detail-tugas .badge.belum {
        background: #fef3c7;
        color: #92400e;
    }

    .detail-tugas .badge.terkumpul {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .detail-tugas .badge.terlambat {
        background: #fee2e2;
        color: #b91c1c;
    }

    .detail-tugas .badge.dinilai {
        background: #dcfce7;
        color: #15803d;
    }

    .detail-tugas .meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin: 16px 0 4px;
        padding: 14px 0 0;
        border-top: 1px dashed #eee4df;
    }

    .detail-tugas .meta-item .meta-label {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #9ca3af;
    }

    .detail-tugas .meta-item .meta-value {
        margin-top: 2px;
        font-size: 0.87rem;
        font-weight: 600;
        color: #1f2937;
        word-break: break-word;
    }

    .detail-tugas .deskripsi {
        font-size: 0.9rem;
        line-height: 1.7;
        color: #374151;
        white-space: pre-line;
        word-break: break-word;
    }

    .detail-tugas .lampiran-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 16px;
        padding: 12px 14px;
        border: 1px dashed #d9c8c1;
        border-radius: 11px;
        background: #fffdfc;
    }

    .detail-tugas .lampiran-info {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .detail-tugas .lampiran-info svg {
        color: var(--primary-maroon);
        flex-shrink: 0;
    }

    .detail-tugas .lampiran-nama {
        font-size: 0.84rem;
        font-weight: 600;
        color: #1f2937;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .detail-tugas .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 13px;
        border: 1px solid var(--primary-maroon);
        border-radius: 8px;
        background: #fff;
        color: var(--primary-maroon);
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .detail-tugas .btn-outline:hover {
        background: var(--maroon-tint);
    }

    /* PENGUMPULAN */
    .detail-tugas .field {
        margin-bottom: 12px;
    }

    .detail-tugas .field label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #374151;
    }

    .detail-tugas .field input[type="file"] {
        width: 100%;
        padding: 9px 10px;
        border: 1px solid #e0d5d0;
        border-radius: 9px;
        background: #fff;
        font-family: inherit;
        font-size: 0.8rem;
        color: #374151;
    }

    .detail-tugas .field textarea {
        width: 100%;
        min-height: 76px;
        padding: 10px 12px;
        border: 1px solid #e0d5d0;
        border-radius: 9px;
        font-family: inherit;
        font-size: 0.85rem;
        color: #111827;
        resize: vertical;
    }

    .detail-tugas .field textarea:focus,
    .detail-tugas .field input[type="file"]:focus {
        outline: 2px solid rgba(107, 13, 27, 0.25);
        border-color: var(--primary-maroon);
    }

    .detail-tugas .hint {
        margin-top: 5px;
        font-size: 0.75rem;
        color: #6b7280;
    }

    .detail-tugas .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        padding: 11px 16px;
        border: none;
        border-radius: 10px;
        background: var(--primary-maroon);
        color: #fff;
        font-family: inherit;
        font-size: 0.86rem;
        font-weight: 700;
        cursor: pointer;
    }

    .detail-tugas .btn-primary:hover {
        background: var(--maroon-dark);
    }

    .detail-tugas .alert {
        padding: 10px 13px;
        margin-bottom: 12px;
        border-radius: 9px;
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .detail-tugas .alert-info {
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .detail-tugas .alert-warning {
        border: 1px solid #fcd34d;
        background: #fffbeb;
        color: #92400e;
    }

    .detail-tugas .alert-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .detail-tugas .alert-danger {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .detail-tugas .berkas-aktif {
        padding: 12px 14px;
        margin-bottom: 12px;
        border: 1px solid #dbeafe;
        border-radius: 10px;
        background: #f8fbff;
    }

    .detail-tugas .berkas-aktif .berkas-nama {
        font-size: 0.84rem;
        font-weight: 700;
        color: #1e3a8a;
        word-break: break-word;
    }

    .detail-tugas .berkas-aktif .berkas-waktu {
        margin-top: 3px;
        font-size: 0.76rem;
        color: #4b5563;
    }

    .detail-tugas .berkas-aksi {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 9px;
    }

    /* NILAI */
    .detail-tugas .nilai-box {
        text-align: center;
        padding: 6px 0 2px;
    }

    .detail-tugas .nilai-angka {
        font-size: 2.6rem;
        font-weight: 800;
        line-height: 1;
        color: var(--primary-maroon);
    }

    .detail-tugas .nilai-skala {
        font-size: 0.78rem;
        font-weight: 600;
        color: #6b7280;
    }

    .detail-tugas .komentar-guru {
        margin-top: 14px;
        padding: 12px 14px;
        border-left: 3px solid var(--gold);
        border-radius: 0 10px 10px 0;
        background: #fffaf0;
        font-size: 0.85rem;
        line-height: 1.6;
        color: #4b5563;
        white-space: pre-line;
    }

    .detail-tugas .komentar-label {
        display: block;
        margin-bottom: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #92400e;
    }

    .detail-tugas .empty-note {
        font-size: 0.85rem;
        line-height: 1.6;
        color: #6b7280;
    }

    @media (max-width: 992px) {
        .detail-tugas .layout {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 480px) {
        .detail-tugas .meta-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .detail-tugas .lampiran-box {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="detail-tugas">
    <a href="{{ route('tugas.mhs.index') }}" class="back-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"></path>
        </svg>
        Kembali ke daftar tugas
    </a>

    <div class="layout">
        <div>
            <div class="card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <div style="min-width:0;">
                        <div class="tugas-mapel">{{ $tugas->namaMataPelajaran() }}</div>
                        <h1 class="tugas-judul">{{ $tugas->judul }}</h1>
                    </div>
                    <span class="badge {{ $status['kelas'] }}">{{ $status['label'] }}</span>
                </div>

                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-label">Guru Pengampu</div>
                        <div class="meta-value">{{ $tugas->namaGuru() }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Kelompok</div>
                        <div class="meta-value">{{ $tugas->kelompok }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Tanggal Pemberian</div>
                        <div class="meta-value">{{ $tugas->tanggalMulaiTeks() }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Batas Pengumpulan</div>
                        <div class="meta-value">{{ $tugas->batasTeks() }}</div>
                    </div>
                </div>

                @if ($tugas->punyaLampiran())
                    <div class="lampiran-box">
                        <div class="lampiran-info">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                            </svg>
                            <span class="lampiran-nama">{{ $tugas->lampiran_nama ?: 'Lampiran tugas' }}</span>
                        </div>
                        <a href="{{ route('tugas.mhs.lampiran', $tugas) }}" class="btn-outline">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Unduh
                        </a>
                    </div>
                @endif

                <h2 class="card-title" style="margin-top:20px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    Deskripsi dan Instruksi
                </h2>
                <div class="deskripsi">{{ $tugas->deskripsi }}</div>
            </div>
        </div>

        <div>
            {{-- PENGUMPULAN --}}
            <div class="card">
                <h2 class="card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Pengumpulan Tugas
                </h2>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
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
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Lihat Berkas
                            </a>
                        </div>
                    </div>
                @endif

                @if ($tugas->masihDapatDikumpulkan())
                    @if ($pengumpulan)
                        <div class="alert alert-info">
                            Anda masih dapat mengganti berkas sebelum batas pengumpulan.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('tugas.mhs.kumpulkan', $tugas) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="field">
                            <label for="file">{{ $pengumpulan ? 'Ganti Berkas Tugas' : 'Unggah Berkas Tugas' }}</label>
                            <input type="file" name="file" id="file" accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="hint">Format JPG, JPEG, PNG, atau PDF. Maksimal 5 MB.</div>
                        </div>

                        <div class="field">
                            <label for="keterangan">Catatan untuk Guru (opsional)</label>
                            <textarea name="keterangan" id="keterangan" maxlength="1000" placeholder="Tulis catatan bila diperlukan...">{{ old('keterangan', $pengumpulan->keterangan ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            </svg>
                            {{ $pengumpulan ? 'Kirim Ulang Berkas' : 'Kumpulkan Tugas' }}
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning">
                        Batas pengumpulan tugas ini telah berakhir pada {{ $tugas->batasTeks() }}.
                    </div>

                    @unless ($pengumpulan)
                        <p class="empty-note">Anda belum mengumpulkan tugas ini dan batas waktu sudah terlewati.</p>
                    @endunless
                @endif
            </div>

            {{-- NILAI --}}
            <div class="card">
                <h2 class="card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01z"></path>
                    </svg>
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

                    <div class="hint" style="text-align:center;">
                        Dinilai {{ $pengumpulan->dinilaiTeks() }}
                    </div>
                @elseif ($pengumpulan && $pengumpulan->sudahDinilai())
                    <div class="alert alert-info">
                        Nilai sudah diberikan oleh Guru dan akan tampil setelah dipublikasikan.
                    </div>
                @elseif ($pengumpulan)
                    <p class="empty-note">Tugas Anda sedang menunggu penilaian Guru.</p>
                @else
                    <p class="empty-note">Nilai akan tampil setelah Anda mengumpulkan tugas dan Guru memublikasikan penilaian.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
