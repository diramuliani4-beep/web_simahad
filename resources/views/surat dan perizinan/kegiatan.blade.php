@extends('layouts.surat-perizinan')

@php
    $labelJenis = 'Izin Kegiatan Fakultas/Prodi';
@endphp

@section('title', $labelJenis)
@section('judul', $labelJenis)
@section('keterangan', 'Lengkapi data kegiatan dan wajib unggah surat resmi Fakultas/Prodi sebagai dokumen pendukung. Berkas PDF/JPG/PNG maksimal 5 MB.')

@section('konten-form')
    <div class="sp-card">
        <h2>Formulir {{ $labelJenis }}</h2>

        <form method="POST" action="{{ route('surat.perizinan.mhs.kegiatan.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="sp-grid">
                <input type="hidden" name="jenis" value="{{ \App\Models\SuratPerizinan::JENIS_KEGIATAN_FAKULTAS }}">

                <div class="sp-field sp-field--full">
                    <label for="nama_kegiatan">Nama Kegiatan <span>*</span></label>
                    <input
                        type="text"
                        id="nama_kegiatan"
                        name="nama_kegiatan"
                        value="{{ old('nama_kegiatan') }}"
                        maxlength="255"
                        placeholder="Contoh: Seminar Nasional Pendidikan"
                        required
                    >
                    @error('nama_kegiatan')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="penyelenggara">Penyelenggara <span>*</span></label>
                    <input
                        type="text"
                        id="penyelenggara"
                        name="penyelenggara"
                        value="{{ old('penyelenggara') }}"
                        maxlength="255"
                        placeholder="Nama Fakultas / Program Studi"
                        required
                    >
                    @error('penyelenggara')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="lokasi">Lokasi Kegiatan <span>*</span></label>
                    <input
                        type="text"
                        id="lokasi"
                        name="lokasi"
                        value="{{ old('lokasi') }}"
                        maxlength="255"
                        placeholder="Contoh: Aula Kampus UIN Imam Bonjol"
                        required
                    >
                    @error('lokasi')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="tanggal_mulai">Tanggal Mulai <span>*</span></label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="{{ old('tanggal_mulai') }}"
                        min="{{ now()->toDateString() }}"
                        required
                    >
                    @error('tanggal_mulai')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="tanggal_selesai">Tanggal Selesai <span>*</span></label>
                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        value="{{ old('tanggal_selesai') }}"
                        min="{{ now()->toDateString() }}"
                        required
                    >
                    @error('tanggal_selesai')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="waktu_mulai">Waktu Mulai</label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai') }}">
                    @error('waktu_mulai')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field">
                    <label for="waktu_selesai">Waktu Selesai</label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai') }}">
                    @error('waktu_selesai')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field sp-field--full">
                    <label for="alasan">Alasan Izin <span>*</span></label>
                    <textarea
                        id="alasan"
                        name="alasan"
                        maxlength="2000"
                        placeholder="Jelaskan alasan izin mengikuti kegiatan ini (minimal 10 karakter)."
                        required
                    >{{ old('alasan') }}</textarea>
                    @error('alasan')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field sp-field--full">
                    <label for="dokumen">Surat Resmi Fakultas/Prodi <span>*</span></label>
                    <input type="file" id="dokumen" name="dokumen" accept=".pdf,.jpg,.jpeg,.png" required>
                    <span class="sp-hint">Format PDF, JPG, JPEG, atau PNG. Ukuran maksimal 5 MB.</span>
                    @error('dokumen')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="sp-actions">
                <button type="submit" class="sp-btn sp-btn--primary">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan
                </button>
                <a href="{{ route('surat.perizinan.mhs') }}" class="sp-btn">Batal</a>
            </div>
        </form>
    </div>

    <div class="sp-card">
        <h2>Riwayat Izin Kegiatan</h2>

        <div class="sp-table-wrap">
            <table class="sp-table sp-table--kegiatan">
                <thead>
                    <tr>
                        <th>Tanggal Ajuan</th>
                        <th>Jenis</th>
                        <th>Kegiatan</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Berkas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatKegiatan as $izin)
                        <tr>
                            <td data-label="Tanggal Ajuan">{{ $izin->created_at?->locale('id')->translatedFormat('d M Y') }}</td>
                            <td data-label="Jenis">{{ $izin->labelJenis() }}</td>
                            <td data-label="Kegiatan">{{ $izin->nama_kegiatan ?: '-' }}</td>
                            <td data-label="Periode">{{ $izin->periodeTeks() }}</td>
                            <td data-label="Status">
                                <span class="sp-badge sp-badge--{{ $izin->kelasStatus() }}">
                                    {{ $izin->labelStatus() }}
                                </span>
                            </td>
                            <td data-label="Berkas">
                                @if ($izin->punyaDokumen())
                                    <a class="sp-mini" href="{{ route('surat.perizinan.mhs.dokumen', [$izin, 'pendukung']) }}" target="_blank" rel="noopener">
                                        Lihat Surat
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="sp-empty-row">
                            <td colspan="6" class="sp-empty">Belum ada pengajuan izin kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
