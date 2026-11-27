@extends('layouts.surat-perizinan')

@php
    $namaSurat = $user->name ?: ($pendaftaran?->nama_lengkap ?: '-');
    $nimSurat = $user->nim ?: ($pendaftaran?->nim ?: '-');
    $formatWaktuWib = static fn ($waktu, string $format = 'd M Y H:i'): string => $waktu
        ? $waktu->copy()->timezone('Asia/Jakarta')->locale('id')->translatedFormat($format).' WIB'
        : '-';
@endphp

@section('title', 'Izin Pulang Semester')
@section('judul', 'Izin Pulang Semester')
@section('keterangan', 'Buat surat pulang semester, cetak dan mintakan tanda tangan orang tua, lalu unggah kembali surat yang sudah ditandatangani untuk diperiksa Musyrifah.')

@section('konten-form')
    <div class="sp-card">
        <h2>Identitas pada Surat</h2>
        <p class="sp-hint">
            Nama, NIM, dan program studi diisi otomatis dari data mahasiswa yang telah disetujui:
            <strong>{{ $namaSurat }}</strong> &middot; NIM. {{ $nimSurat }} &middot; {{ $pendaftaran?->program_studi ?: 'Program studi belum tersedia' }}.
        </p>
    </div>

    <div class="sp-card">
        <h2>Ajukan Izin Pulang Semester</h2>

        <form method="POST" action="{{ route('surat.perizinan.mhs.semester.store') }}">
            @csrf

            <div class="sp-grid">
                <div class="sp-field">
                    <label for="tanggal_mulai">Tanggal Pulang <span>*</span></label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" min="{{ now()->toDateString() }}" required>
                    @error('tanggal_mulai')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="tanggal_selesai">Tanggal Kembali <span>*</span></label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" min="{{ now()->toDateString() }}" required>
                    @error('tanggal_selesai')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field sp-field--full">
                    <label for="lokasi">Tujuan Pulang <span>*</span></label>
                    <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" maxlength="255" placeholder="Alamat tujuan pulang semester" required>
                    @error('lokasi')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field sp-field--full">
                    <label for="alasan">Alasan Pulang <span>*</span></label>
                    <textarea id="alasan" name="alasan" maxlength="2000" placeholder="Contoh: pulang semester ganjil tahun akademik 2026/2027." required>{{ old('alasan') }}</textarea>
                    @error('alasan')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="keterangan">Keterangan Tambahan</label>
                    <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" maxlength="255">
                    @error('keterangan')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="transportasi">Penjemputan / Transportasi</label>
                    <input type="text" id="transportasi" name="transportasi" value="{{ old('transportasi') }}" maxlength="255" placeholder="Contoh: dijemput keluarga">
                    @error('transportasi')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="sp-actions">
                <button type="submit" class="sp-btn sp-btn--primary">
                    <i class="fa-solid fa-file-circle-plus"></i> Buat Surat
                </button>
                <a href="{{ route('surat.perizinan.mhs') }}" class="sp-btn">Kembali</a>
            </div>
        </form>
    </div>

    <div class="sp-card">
        <h2>Daftar Surat Pulang Semester</h2>

        <div class="sp-table-wrap">
            <table class="sp-table sp-table--semester">
                <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Periode / Tujuan</th>
                        <th>Status & Musyrifah</th>
                        <th>Surat Bertanda Tangan</th>
                        <th>Waktu Keluar / Kembali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuans as $surat)
                        <tr>
                            <td data-label="Nomor Surat">
                                {{ $surat->nomor_surat ?: '-' }}
                                <div class="sp-hint" style="margin-top:6px">Dibuat {{ $formatWaktuWib($surat->created_at, 'd M Y') }}</div>
                            </td>
                            <td data-label="Periode / Tujuan">
                                {{ $surat->periodeTeks() }}
                                <div class="sp-hint" style="margin-top:6px">{{ $surat->lokasi ?: '-' }}</div>
                                @if ($surat->transportasi)
                                    <div class="sp-hint">Transportasi: {{ $surat->transportasi }}</div>
                                @endif
                            </td>
                            <td data-label="Status & Musyrifah">
                                <span class="sp-badge sp-badge--{{ $surat->kelasStatus() }}">
                                    {{ $surat->labelStatus() }}
                                </span>
                                <div class="sp-hint" style="margin-top:6px">Musyrifah: {{ $surat->musyrifah?->name ?: '-' }}</div>
                                @if ($surat->keputusan_nama)
                                    <div class="sp-hint">{{ $surat->keputusan_nama }} &middot; {{ $formatWaktuWib($surat->keputusan_pada) }}</div>
                                @endif
                                @if ($surat->catatan_musyrifah)
                                    <div class="sp-hint" style="margin-top:6px">Catatan: {{ $surat->catatan_musyrifah }}</div>
                                @endif
                                <details style="margin-top:8px">
                                    <summary class="sp-hint">Lihat riwayat status</summary>
                                    @foreach ($surat->riwayats as $riwayat)
                                        <div class="sp-hint" style="margin-top:6px">
                                            <strong>{{ $riwayat->labelStatus() }}</strong> &middot; {{ $formatWaktuWib($riwayat->created_at) }}
                                            @if ($riwayat->nama_pengubah)
                                                &middot; {{ $riwayat->nama_pengubah }}
                                            @endif
                                            @if ($riwayat->catatan)
                                                <br>{{ $riwayat->catatan }}
                                            @endif
                                        </div>
                                    @endforeach
                                </details>
                            </td>
                            <td data-label="Surat Bertanda Tangan">
                                @if ($surat->punyaSuratTertanda())
                                    <a class="sp-mini" href="{{ route('surat.perizinan.mhs.dokumen', [$surat, 'tertanda']) }}" target="_blank" rel="noopener">
                                        Lihat Berkas
                                    </a>
                                    <div class="sp-hint" style="margin-top:6px">
                                        Diunggah {{ $formatWaktuWib($surat->surat_diunggah_pada) }}
                                    </div>
                                @else
                                    <span class="sp-hint">Belum diunggah</span>
                                @endif
                            </td>
                            <td data-label="Waktu Keluar / Kembali">
                                <div class="sp-hint">Keluar: {{ $formatWaktuWib($surat->waktu_keluar) }}</div>
                                <div class="sp-hint" style="margin-top:6px">Kembali: {{ $formatWaktuWib($surat->waktu_kembali) }}</div>
                            </td>
                            <td data-label="Aksi">
                                <div class="sp-inline-form">
                                    @if ($surat->nomor_surat)
                                        <a class="sp-mini" href="{{ route('surat.perizinan.mhs.semester.pdf', $surat) }}">Unduh PDF</a>
                                        <a class="sp-mini sp-mini--gold" href="{{ route('surat.perizinan.mhs.semester.cetak', $surat) }}" target="_blank" rel="noopener">
                                            Cetak
                                        </a>
                                    @endif
                                </div>
                                @if (in_array($surat->status, ['menunggu', 'perlu_perbaikan'], true))
                                    <form
                                        method="POST"
                                        action="{{ route('surat.perizinan.mhs.semester.unggah', $surat) }}"
                                        enctype="multipart/form-data"
                                        class="sp-inline-form"
                                        style="margin-top:8px"
                                    >
                                        @csrf
                                        <input type="file" name="surat_tertanda" accept=".pdf,.jpg,.jpeg,.png" required aria-label="Unggah surat bertanda tangan">
                                        <button type="submit" class="sp-mini">Unggah</button>
                                    </form>
                                    <div class="sp-hint" style="margin-top:6px">
                                        Unggah surat yang telah ditandatangani, format PDF/JPG/PNG maksimal 5 MB.
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="sp-empty-row">
                            <td colspan="6" class="sp-empty">Belum ada surat pulang semester.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
