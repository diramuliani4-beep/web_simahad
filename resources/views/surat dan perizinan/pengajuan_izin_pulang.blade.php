@extends('layouts.surat-perizinan')

@php
    $labelJenis = $darurat ? 'Izin Pulang Darurat/Khusus' : 'Izin Pulang Mingguan';
    $routeSimpan = $darurat
        ? route('surat.perizinan.mhs.pengajuan.darurat.store')
        : route('surat.perizinan.mhs.pengajuan.store');
@endphp

@section('title', $labelJenis)
@section('judul', $labelJenis)
@section('keterangan', $darurat
    ? 'Ajukan izin untuk keadaan mendesak seperti kemalangan keluarga. Pengajuan diproses khusus oleh Musyrifah, tetap tersedia saat kuota reguler habis, dan tidak mengurangi kuota izin pulang mingguan.'
    : 'Ajukan izin pulang dengan tanggal mulai, tanggal kembali, alamat tujuan, dan alasan. Maksimal 2 kali per bulan dengan jarak minimum 14 hari; kuota hanya berkurang setelah disetujui.')

@section('konten-form')
    <div class="sp-kuota">
        <div>
            <span class="sp-kuota__judul">
                <i class="fa-solid fa-house"></i> Monitoring Kuota {{ $kuotaPulang['bulan'] }}
            </span>
            <p>
                @if ($darurat)
                    Izin darurat/khusus tidak menggunakan kuota reguler dan tidak terikat jarak antarizin mingguan.
                @else
                    Maksimal {{ $kuotaPulang['maksimal'] }} kali per bulan. Hanya izin yang sudah disetujui atau selesai yang mengurangi kuota; jarak minimum antarizin adalah 14 hari.
                @endif
            </p>
        </div>
        <div class="sp-kuota__angka">
            <span>{{ $kuotaPulang['terpakai'] }} / {{ $kuotaPulang['maksimal'] }}</span>
            <small>Sisa {{ $kuotaPulang['sisa'] }} slot</small>
        </div>
    </div>

    <div class="sp-card">
        <h2>Formulir {{ $labelJenis }}</h2>

        @if ($darurat)
            <div class="sp-alert sp-alert--sukses" style="margin-bottom:18px">
                Izin darurat diproses secara khusus. Jelaskan keadaan mendesak dan lampirkan bukti pendukung bila tersedia.
            </div>
        @elseif ($kuotaPulang['sisa'] <= 0)
            <div class="sp-alert sp-alert--gagal" style="margin-bottom:18px">
                Kuota izin pulang bulan {{ $kuotaPulang['bulan'] }} sudah habis. Kamu tetap dapat mengajukan tanggal pada bulan berikutnya,
                atau menggunakan <a href="{{ route('surat.perizinan.mhs.pengajuan.darurat') }}"><strong>Izin Pulang Darurat/Khusus</strong></a> untuk keadaan mendesak.
            </div>
        @endif

        <form method="POST" action="{{ $routeSimpan }}" enctype="multipart/form-data">
            @csrf

            <div class="sp-grid">
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
                    <label for="tanggal_selesai">Tanggal Kembali <span>*</span></label>
                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        value="{{ old('tanggal_selesai') }}"
                        min="{{ now()->toDateString() }}"
                        required
                    >
                    <span class="sp-hint" id="hintDurasi">
                        {{ $darurat ? 'Durasi disesuaikan dengan keadaan mendesak.' : 'Durasi maksimal 14 hari.' }}
                    </span>
                    @error('tanggal_selesai')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field sp-field--full">
                    <label for="lokasi">Alamat / Tujuan Pulang <span>*</span></label>
                    <input
                        type="text"
                        id="lokasi"
                        name="lokasi"
                        value="{{ old('lokasi') }}"
                        maxlength="255"
                        placeholder="Contoh: Jl. Raya Padang - Bukittinggi, Kota Padang"
                        required
                    >
                    @error('lokasi')
                        <span class="sp-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sp-field sp-field--full">
                    <label for="alasan">{{ $darurat ? 'Alasan Mendesak' : 'Alasan Izin' }} <span>*</span></label>
                    <textarea
                        id="alasan"
                        name="alasan"
                        maxlength="2000"
                        placeholder="{{ $darurat ? 'Contoh: keluarga mengalami kemalangan dan memerlukan kehadiran segera.' : 'Tuliskan alasan izin pulang secara jelas (minimal 10 karakter).' }}"
                        required
                    >{{ old('alasan') }}</textarea>
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

                @if ($darurat)
                    <div class="sp-field sp-field--full">
                        <label for="dokumen">Bukti Pendukung (opsional)</label>
                        <input type="file" id="dokumen" name="dokumen" accept=".pdf,.jpg,.jpeg,.png">
                        <span class="sp-hint">Format PDF, JPG, JPEG, atau PNG. Ukuran maksimal 5 MB.</span>
                        @error('dokumen')
                            <span class="sp-error">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
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
        <h2>Riwayat Izin Pulang</h2>

        <div class="sp-table-wrap">
            <table class="sp-table sp-table--pulang">
                <thead>
                    <tr>
                        <th>Tanggal Ajuan</th>
                        <th>Jenis</th>
                        <th>Periode</th>
                        <th>Durasi</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                        <th>Catatan Musyrifah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatPulang as $izin)
                        <tr>
                            <td data-label="Tanggal Ajuan">{{ $izin->created_at?->locale('id')->translatedFormat('d M Y') }}</td>
                            <td data-label="Jenis">{{ $izin->labelJenis() }}</td>
                            <td data-label="Periode">{{ $izin->periodeTeks() }}</td>
                            <td data-label="Durasi">{{ $izin->durasi_hari ? $izin->durasi_hari.' hari' : '-' }}</td>
                            <td data-label="Tujuan">{{ $izin->tujuanTeks() }}</td>
                            <td data-label="Status">
                                <span class="sp-badge sp-badge--{{ $izin->kelasStatus() }}">
                                    {{ $izin->labelStatus() }}
                                </span>
                            </td>
                            <td data-label="Catatan Musyrifah">{{ $izin->catatan_musyrifah ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr class="sp-empty-row">
                            <td colspan="7" class="sp-empty">Belum ada pengajuan izin pulang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const mulai = document.getElementById('tanggal_mulai');
        const kembali = document.getElementById('tanggal_selesai');
        const hint = document.getElementById('hintDurasi');
        const maksHari = @json($darurat ? null : \App\Models\SuratPerizinan::MAKS_DURASI_PULANG_HARI);

        function perbaruiDurasi() {
            if (!mulai || !kembali) {
                return;
            }

            kembali.min = mulai.value || '{{ now()->toDateString() }}';

            if (!mulai.value || !kembali.value) {
                hint.textContent = maksHari === null
                    ? 'Durasi disesuaikan dengan keadaan mendesak.'
                    : 'Durasi maksimal ' + maksHari + ' hari.';
                return;
            }

            const awal = new Date(mulai.value + 'T00:00:00');
            const akhir = new Date(kembali.value + 'T00:00:00');
            const hari = Math.round((akhir - awal) / 86400000) + 1;

            if (hari < 1) {
                hint.textContent = 'Tanggal kembali tidak boleh sebelum tanggal mulai.';
            } else if (maksHari !== null && hari > maksHari) {
                hint.textContent = 'Durasi ' + hari + ' hari melebihi batas ' + maksHari + ' hari.';
            } else {
                hint.textContent = 'Durasi pengajuan: ' + hari + ' hari.';
            }
        }

        if (mulai && kembali) {
            mulai.addEventListener('change', perbaruiDurasi);
            kembali.addEventListener('change', perbaruiDurasi);
            perbaruiDurasi();
        }
    </script>
@endpush
