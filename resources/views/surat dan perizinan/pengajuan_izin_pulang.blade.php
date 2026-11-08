@extends('layouts.surat-perizinan')

@section('title', 'Izin Pulang Mingguan')
@section('judul', 'Izin Pulang Mingguan')
@section('keterangan', 'Ajukan izin pulang dengan mencantumkan tanggal mulai, tanggal kembali, alamat tujuan, dan alasan. Maksimal 2 kali izin pulang per bulan dengan durasi maksimal 14 hari.')

@section('konten')
    <div class="sp-kuota">
        <div>
            <span class="sp-kuota__judul">
                <i class="fa-solid fa-house"></i> Monitoring Kuota {{ $kuotaPulang['bulan'] }}
            </span>
            <p>Maksimal {{ $kuotaPulang['maksimal'] }} kali per bulan. Pengajuan yang menunggu tetap menahan satu slot kuota.</p>
        </div>
        <div class="sp-kuota__angka">
            <span>{{ $kuotaPulang['terpakai'] }} / {{ $kuotaPulang['maksimal'] }}</span>
            <small>Sisa {{ $kuotaPulang['sisa'] }} slot</small>
        </div>
    </div>

    <div class="sp-card">
        <h2>Formulir Pengajuan Izin Pulang</h2>

        @if ($kuotaPulang['sisa'] <= 0)
            <div class="sp-alert sp-alert--gagal" style="margin-bottom:0">
                Kuota izin pulang bulan ini sudah habis. Pengajuan baru dapat dilakukan pada bulan berikutnya
                atau setelah pengajuan yang menunggu ditolak/dibatalkan.
            </div>
        @else
            <form method="POST" action="{{ route('surat.perizinan.mhs.pengajuan.store') }}">
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
                        <span class="sp-hint" id="hintDurasi">Durasi maksimal 14 hari.</span>
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
                        <label for="alasan">Alasan Izin <span>*</span></label>
                        <textarea
                            id="alasan"
                            name="alasan"
                            maxlength="2000"
                            placeholder="Tuliskan alasan izin pulang secara jelas (minimal 10 karakter)."
                            required
                        >{{ old('alasan') }}</textarea>
                        @error('alasan')
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
        @endif
    </div>

    <div class="sp-card">
        <h2>Riwayat Izin Pulang</h2>

        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Tanggal Ajuan</th>
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
                            <td>{{ $izin->created_at?->locale('id')->translatedFormat('d M Y') }}</td>
                            <td>{{ $izin->periodeTeks() }}</td>
                            <td>{{ $izin->durasi_hari ? $izin->durasi_hari.' hari' : '-' }}</td>
                            <td>{{ $izin->tujuanTeks() }}</td>
                            <td>
                                <span class="sp-badge sp-badge--{{ $izin->kelasStatus() }}">
                                    {{ $izin->labelStatus() }}
                                </span>
                            </td>
                            <td>{{ $izin->catatan_musyrifah ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="sp-empty">Belum ada pengajuan izin pulang.</td>
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
        const maksHari = {{ \App\Models\SuratPerizinan::MAKS_DURASI_PULANG_HARI }};

        function perbaruiDurasi() {
            if (!mulai || !kembali) {
                return;
            }

            kembali.min = mulai.value || '{{ now()->toDateString() }}';

            if (!mulai.value || !kembali.value) {
                hint.textContent = 'Durasi maksimal ' + maksHari + ' hari.';
                return;
            }

            const awal = new Date(mulai.value + 'T00:00:00');
            const akhir = new Date(kembali.value + 'T00:00:00');
            const hari = Math.round((akhir - awal) / 86400000) + 1;

            if (hari < 1) {
                hint.textContent = 'Tanggal kembali tidak boleh sebelum tanggal mulai.';
            } else if (hari > maksHari) {
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
