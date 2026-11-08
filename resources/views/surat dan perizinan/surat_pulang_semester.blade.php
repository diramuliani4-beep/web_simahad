@extends('layouts.surat-perizinan')

@section('title', 'Surat Pulang Semester')
@section('judul', 'Surat Pulang Semester')
@section('keterangan', 'Buat surat pulang semester, cetak dan mintakan tanda tangan orang tua, lalu unggah kembali surat yang sudah ditandatangani untuk diperiksa Musyrifah.')

@section('konten')
    <div class="sp-card">
        <h2>Ajukan Surat Pulang Semester</h2>

        <form method="POST" action="{{ route('surat.perizinan.mhs.semester.store') }}">
            @csrf

            <div class="sp-grid">
                <div class="sp-field sp-field--full">
                    <label for="alasan">Keterangan Tambahan (opsional)</label>
                    <textarea
                        id="alasan"
                        name="alasan"
                        maxlength="2000"
                        placeholder="Contoh: Pulang semester ganjil tahun akademik 2026/2027."
                    >{{ old('alasan') }}</textarea>
                    @error('alasan')
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
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Tanggal Dibuat</th>
                        <th>Status</th>
                        <th>Surat Bertanda Tangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuans as $surat)
                        <tr>
                            <td>{{ $surat->nomor_surat ?: '-' }}</td>
                            <td>{{ $surat->created_at?->locale('id')->translatedFormat('d M Y') }}</td>
                            <td>
                                <span class="sp-badge sp-badge--{{ $surat->kelasStatus() }}">
                                    {{ $surat->labelStatus() }}
                                </span>
                                @if ($surat->catatan_musyrifah)
                                    <div class="sp-hint" style="margin-top:6px">{{ $surat->catatan_musyrifah }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($surat->punyaSuratTertanda())
                                    <a class="sp-mini" href="{{ route('surat.perizinan.mhs.dokumen', [$surat, 'tertanda']) }}" target="_blank" rel="noopener">
                                        Lihat Berkas
                                    </a>
                                    <div class="sp-hint" style="margin-top:6px">
                                        {{ $surat->surat_diunggah_pada?->locale('id')->translatedFormat('d M Y H:i') }}
                                    </div>
                                @else
                                    <span class="sp-hint">Belum diunggah</span>
                                @endif
                            </td>
                            <td>
                                <div class="sp-inline-form">
                                    @if ($surat->nomor_surat)
                                        <a class="sp-mini sp-mini--gold" href="{{ route('surat.perizinan.mhs.semester.cetak', $surat) }}" target="_blank" rel="noopener">
                                            Cetak
                                        </a>
                                    @endif

                                    @if (in_array($surat->status, ['menunggu', 'perlu_perbaikan'], true))
                                        <form
                                            method="POST"
                                            action="{{ route('surat.perizinan.mhs.semester.unggah', $surat) }}"
                                            enctype="multipart/form-data"
                                            class="sp-inline-form"
                                        >
                                            @csrf
                                            <input type="file" name="surat_tertanda" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <button type="submit" class="sp-mini">Unggah</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="sp-hint" style="margin-top:6px">
                                    Unggah berkas PDF/JPG/PNG maksimal 5 MB.
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="sp-empty">Belum ada surat pulang semester.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
