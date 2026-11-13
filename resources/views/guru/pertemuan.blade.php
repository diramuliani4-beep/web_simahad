@extends('layouts.guru')

@section('title', 'Pertemuan '.$pertemuan->pertemuan_ke)

@push('styles')
<style>
    .pt-page, .pt-page * { box-sizing: border-box; }
    .pt-page { width: 100%; min-width: 0; color: #292929; }
    .pt-header { padding-bottom: 18px; margin-bottom: 20px; border-bottom: 1px solid #e7cfd5; }
    .pt-breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; font-size: 12px; color: #8d8587; }
    .pt-breadcrumb a { color: #8d8587; text-decoration: none; }
    .pt-breadcrumb a:hover { color: #800b22; }
    .pt-header h1 { margin: 0 0 6px; font-size: 23px; font-weight: 700; }
    .pt-header p { margin: 0; font-size: 13px; line-height: 1.7; color: #777; }

    .pt-alert { margin-bottom: 16px; padding: 12px 15px; border: 1px solid #bfebcb; border-radius: 9px; background: #e8f7ed; color: #196a38; font-size: 12px; line-height: 1.6; }
    .pt-alert.error { border-color: #f0c5c5; background: #fff0f0; color: #ae3030; }
    .pt-alert.info { border-color: #cfe3f5; background: #eef6fd; color: #205a86; }
    .pt-alert ul { margin: 6px 0 0; padding-left: 18px; }

    .pt-card { margin-bottom: 18px; overflow: hidden; background: #fff; border: 1px solid #efe2df; border-radius: 12px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .pt-card-top { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid #f0e8e7; }
    .pt-card-top h2 { margin: 0; font-size: 15px; font-weight: 700; }
    .pt-card-top p { margin: 3px 0 0; font-size: 11px; color: #8a8284; }
    .pt-card-body { padding: 18px 20px; }

    .pt-tags { display: flex; flex-wrap: wrap; gap: 7px; }
    .pt-tag { padding: 4px 10px; border-radius: 999px; background: #f7f0f1; color: #6e5a5f; font-size: 10.5px; font-weight: 600; }
    .pt-tag.utama { background: #fbe9ed; color: #800b22; }
    .pt-tag.selesai { background: #def8e8; color: #17763e; }
    .pt-tag.berlangsung { background: #fdf3dc; color: #93650d; }

    .pt-ringkas { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 14px; }
    .pt-stat { padding: 11px 13px; border: 1px solid #f0e6e5; border-radius: 10px; background: #fdfbfb; }
    .pt-stat span { display: block; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #8a8284; }
    .pt-stat strong { display: block; margin-top: 4px; font-size: 18px; }

    .pt-kolom { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr); gap: 18px; align-items: start; }

    .pt-table-wrap { width: 100%; overflow-x: auto; }
    .pt-table { width: 100%; min-width: 560px; border-collapse: collapse; font-size: 12px; }
    .pt-table th { padding: 9px 10px; background: #f7f0f1; color: #6e5a5f; font-size: 10px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .pt-table td { padding: 9px 10px; border-bottom: 1px solid #f4eceb; color: #4f4749; vertical-align: middle; }
    .pt-table tr:last-child td { border-bottom: 0; }
    .pt-nama { font-weight: 600; color: #332c2e; }
    .pt-nim { display: block; font-size: 10.5px; color: #9a9294; }

    .pt-input, .pt-select, .pt-textarea { width: 100%; padding: 8px 10px; border: 1px solid #e7dbd9; border-radius: 8px; background: #fff; font: inherit; font-size: 12.5px; color: #333; }
    .pt-input:focus, .pt-select:focus, .pt-textarea:focus { border-color: #800b22; outline: 0; box-shadow: 0 0 0 3px rgba(128, 11, 34, .07); }
    .pt-textarea { min-height: 92px; resize: vertical; line-height: 1.6; }
    .pt-field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
    .pt-field label { font-size: 10.5px; font-weight: 700; color: #6e5a5f; letter-spacing: .04em; text-transform: uppercase; }
    .pt-field small { font-size: 10.5px; color: #9a9294; }

    .pt-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 36px; padding: 9px 14px; border: 1px solid #800b22; border-radius: 8px; background: #800b22; color: #fff; font-family: inherit; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .pt-btn:hover { background: #65091b; }
    .pt-btn.ghost { background: #fff; color: #800b22; border-color: #e2c3ca; }
    .pt-btn.ghost:hover { background: #fdf6f7; }
    .pt-btn.small { min-height: 30px; padding: 6px 10px; font-size: 11px; }
    .pt-aksi { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }

    .pt-tugas { display: flex; flex-direction: column; gap: 10px; }
    .pt-tugas-item { padding: 12px 13px; border: 1px solid #f0e6e5; border-radius: 10px; background: #fdfbfb; }
    .pt-tugas-item h4 { margin: 0 0 4px; font-size: 13px; font-weight: 700; }
    .pt-tugas-meta { font-size: 11px; color: #8a8284; line-height: 1.6; }
    .pt-tugas-aksi { display: flex; flex-wrap: wrap; gap: 7px; align-items: center; margin-top: 8px; }

    .pt-materi { font-size: 12.5px; line-height: 1.7; color: #4f4749; white-space: pre-line; }
    .pt-kosong { padding: 16px; border: 1px dashed #e3cfd4; border-radius: 10px; background: #fdfafa; text-align: center; color: #8a8284; font-size: 12px; line-height: 1.7; }

    .pt-riwayat { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 9px; }
    .pt-riwayat li { padding-left: 12px; border-left: 2px solid #f0dfe3; font-size: 12px; line-height: 1.6; color: #4f4749; }
    .pt-riwayat time { display: block; margin-top: 2px; font-size: 10.5px; color: #9a9294; }

    @media (max-width: 1000px) {
        .pt-kolom { grid-template-columns: 1fr; }
        .pt-ringkas { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 640px) {
        .pt-ringkas { grid-template-columns: 1fr; }
        .pt-btn { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="pt-page">

    <header class="pt-header">
        <nav class="pt-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('guru.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('guru.pembelajaran', [
                'mata_pelajaran' => $pertemuan->mata_pelajaran_id,
                'kelompok' => $pertemuan->kelompok,
                'periode' => $pertemuan->periode,
            ]) }}">Pembelajaran</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">Pertemuan {{ $pertemuan->pertemuan_ke }}</span>
        </nav>

        <h1>Pertemuan {{ $pertemuan->pertemuan_ke }} &middot; {{ $pertemuan->mataPelajaran?->nama }}</h1>
        <p>
            {{ $pertemuan->kelompok }} &middot; {{ $pertemuan->periode }}.
            Simpan absensi, materi, dan tugas. Pertemuan yang sudah selesai tetap dapat dibuka
            kembali untuk melihat detail dan riwayat koreksi.
        </p>

        <div class="pt-tags" style="margin-top: 12px;">
            <span class="pt-tag utama">{{ $pertemuan->kelompok }}</span>
            <span class="pt-tag">{{ $pertemuan->periode }}</span>
            <span class="pt-tag">{{ $pertemuan->tanggalTeks() }}</span>
            @if ($pertemuan->jadwal)
                <span class="pt-tag">{{ $pertemuan->jadwal->hari }}, {{ $pertemuan->jadwal->jamTeks() }}</span>
                <span class="pt-tag">{{ $pertemuan->jadwal->ruangan ?: 'Ruang Kelas' }}</span>
            @endif
            <span class="pt-tag {{ $pertemuan->status }}">{{ $pertemuan->labelStatus() }}</span>
        </div>
    </header>

    @if (session('success'))
        <div class="pt-alert" role="status">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="pt-alert info" role="status">{{ session('info') }}</div>
    @endif

    @if (session('error'))
        <div class="pt-alert error" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="pt-alert error" role="alert">
            <strong>Perubahan belum tersimpan.</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="pt-card" aria-label="Ringkasan pertemuan">
        <div class="pt-card-body">
            <div class="pt-ringkas">
                <div class="pt-stat">
                    <span>Hadir</span>
                    <strong>{{ $ringkasanAbsensi['hadir'] }}</strong>
                </div>
                <div class="pt-stat">
                    <span>Izin</span>
                    <strong>{{ $ringkasanAbsensi['izin'] }}</strong>
                </div>
                <div class="pt-stat">
                    <span>Sakit</span>
                    <strong>{{ $ringkasanAbsensi['sakit'] }}</strong>
                </div>
                <div class="pt-stat">
                    <span>Alpa</span>
                    <strong>{{ $ringkasanAbsensi['alpa'] }}</strong>
                </div>
            </div>

            <div class="pt-aksi">
                @if ($sudahSelesai)
                    <span class="pt-tag selesai">Pertemuan sudah diselesaikan {{ $pertemuan->selesai_pada?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
                @else
                    <form method="POST" action="{{ route('guru.pembelajaran.selesai', $pertemuan) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="pt-btn">Selesaikan Pertemuan {{ $pertemuan->pertemuan_ke }}</button>
                    </form>
                    <span class="pt-tag">Absensi minimal satu mahasiswa wajib tersimpan sebelum pertemuan diselesaikan.</span>
                @endif
            </div>
        </div>
    </section>

    <div class="pt-kolom">

        {{-- ABSENSI --}}
        <section class="pt-card" aria-label="Absensi pertemuan">
            <div class="pt-card-top">
                <div>
                    <h2>Absensi Pertemuan {{ $pertemuan->pertemuan_ke }}</h2>
                    <p>Simpan status Hadir, Izin, Sakit, atau Alpa untuk mahasiswa {{ $pertemuan->kelompok }}.</p>
                </div>
                <span class="pt-tag">
                    {{ $ringkasanAbsensi['total'] }} dari {{ $mahasiswa->count() }} mahasiswa terisi
                </span>
            </div>
            <div class="pt-card-body">
                @if ($mahasiswa->isEmpty())
                    <div class="pt-kosong">
                        <strong>Belum ada mahasiswa pada kelompok ini.</strong>
                        Keanggotaan kelompok ditetapkan Musyrifah melalui Manajemen Pembelajaran.
                    </div>
                @else
                    <form method="POST" action="{{ route('guru.pembelajaran.absensi', $pertemuan) }}">
                        @csrf
                        @method('PUT')

                        <div class="pt-table-wrap">
                            <table class="pt-table">
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mahasiswa as $siswa)
                                        @php($baris = $absensi->get($siswa->id))
                                        <tr>
                                            <td>
                                                <span class="pt-nama">{{ $siswa->name }}</span>
                                                <span class="pt-nim">{{ $siswa->nim ?: 'NIM belum diisi' }}</span>
                                            </td>
                                            <td style="min-width: 130px;">
                                                <select name="absensi[{{ $siswa->id }}][status]" class="pt-select"
                                                    aria-label="Status absensi {{ $siswa->name }}">
                                                    @foreach ($statusAbsensi as $nilai => $label)
                                                        <option value="{{ $nilai }}" @selected(old('absensi.'.$siswa->id.'.status', $baris?->status ?? 'hadir') === $nilai)>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="min-width: 160px;">
                                                <input type="text" class="pt-input" maxlength="300"
                                                    name="absensi[{{ $siswa->id }}][catatan]"
                                                    value="{{ old('absensi.'.$siswa->id.'.catatan', $baris?->catatan) }}"
                                                    placeholder="Opsional">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="pt-aksi">
                            <button type="submit" class="pt-btn">
                                {{ $ringkasanAbsensi['total'] > 0 ? 'Simpan Koreksi Absensi' : 'Simpan Absensi' }}
                            </button>
                            @if ($ringkasanAbsensi['total'] > 0)
                                <span class="pt-tag">Koreksi akan tercatat pada riwayat perubahan.</span>
                            @endif
                        </div>
                    </form>
                @endif
            </div>
        </section>

        <div>
            {{-- MATERI --}}
            <section class="pt-card" aria-label="Materi pertemuan">
                <div class="pt-card-top">
                    <div>
                        <h2>Materi</h2>
                        <p>Ringkasan materi dan/atau berkas bahan ajar.</p>
                    </div>
                </div>
                <div class="pt-card-body">
                    @if ($pertemuan->punyaMateri())
                        <div class="pt-materi">{{ $pertemuan->materi ?: 'Materi disimpan sebagai berkas.' }}</div>

                        @if ($pertemuan->punyaBerkasMateri())
                            <div class="pt-aksi">
                                <a class="pt-btn ghost small" href="{{ route('guru.pembelajaran.materi.unduh', $pertemuan) }}">
                                    Unduh {{ $pertemuan->materi_nama ?: 'berkas materi' }}
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="pt-kosong">Materi pertemuan ini belum diunggah.</div>
                    @endif

                    <form method="POST" action="{{ route('guru.pembelajaran.materi', $pertemuan) }}"
                        enctype="multipart/form-data" style="margin-top: 16px;">
                        @csrf
                        <div class="pt-field">
                            <label for="materi">Ringkasan Materi</label>
                            <textarea id="materi" name="materi" class="pt-textarea" maxlength="5000"
                                placeholder="Tuliskan pokok bahasan dan catatan materi pertemuan ini.">{{ old('materi', $pertemuan->materi) }}</textarea>
                        </div>
                        <div class="pt-field">
                            <label for="berkas">Berkas Materi</label>
                            <input type="file" id="berkas" name="berkas" class="pt-input"
                                accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
                            <small>PDF, DOC, DOCX, PPT, PPTX, JPG, JPEG, atau PNG. Maksimal 10 MB.</small>
                        </div>
                        <button type="submit" class="pt-btn">{{ $pertemuan->punyaMateri() ? 'Perbarui Materi' : 'Simpan Materi' }}</button>
                    </form>
                </div>
            </section>

            {{-- TUGAS --}}
            <section class="pt-card" aria-label="Tugas pertemuan">
                <div class="pt-card-top">
                    <div>
                        <h2>Tugas</h2>
                        <p>Tugas yang diberikan pada pertemuan ini.</p>
                    </div>
                    <span class="pt-tag">{{ $tugas->count() }} tugas</span>
                </div>
                <div class="pt-card-body">
                    @if ($tugas->isEmpty())
                        <div class="pt-kosong">Belum ada tugas pada pertemuan ini.</div>
                    @else
                        <div class="pt-tugas">
                            @foreach ($tugas as $item)
                                <div class="pt-tugas-item">
                                    <h4>{{ $item->judul }}</h4>
                                    <div class="pt-tugas-meta">
                                        {{ $item->labelStatus() }} &middot; batas {{ $item->batasTeks() }}<br>
                                        {{ $item->pengumpulan_count }} pengumpulan &middot;
                                        {{ $item->pengumpulan_dinilai_count }} sudah dinilai
                                    </div>
                                    <div class="pt-tugas-aksi">
                                        <a class="pt-btn ghost small" href="{{ route('guru.pengumpulan.index', $item) }}">
                                            Periksa &amp; Beri Nilai
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <details style="margin-top: 16px;">
                        <summary style="cursor: pointer; font-size: 12px; font-weight: 700; color: #800b22;">
                            Tambah Tugas pada Pertemuan Ini
                        </summary>

                        <form method="POST" action="{{ route('guru.pembelajaran.tugas', $pertemuan) }}"
                            enctype="multipart/form-data" style="margin-top: 14px;">
                            @csrf
                            <div class="pt-field">
                                <label for="judul">Judul Tugas</label>
                                <input type="text" id="judul" name="judul" class="pt-input" maxlength="180"
                                    value="{{ old('judul') }}" required>
                            </div>
                            <div class="pt-field">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" class="pt-textarea" minlength="10" maxlength="20000"
                                    required placeholder="Jelaskan instruksi tugas untuk mahasiswa.">{{ old('deskripsi') }}</textarea>
                            </div>
                            <div class="pt-field">
                                <label for="batas_pengumpulan">Batas Pengumpulan</label>
                                <input type="datetime-local" id="batas_pengumpulan" name="batas_pengumpulan" class="pt-input"
                                    value="{{ old('batas_pengumpulan', now()->addDays(3)->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="pt-field">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="pt-select">
                                    @foreach ($statusTugasPilihan as $nilai => $label)
                                        <option value="{{ $nilai }}" @selected(old('status') === $nilai)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small>Tugas dipublikasikan akan mengirim notifikasi ke mahasiswa kelompok.</small>
                            </div>
                            <div class="pt-field">
                                <label for="lampiran">Lampiran (opsional)</label>
                                <input type="file" id="lampiran" name="lampiran" class="pt-input"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small>PDF, JPG, JPEG, atau PNG. Maksimal 5 MB.</small>
                            </div>
                            <button type="submit" class="pt-btn">Simpan Tugas</button>
                        </form>
                    </details>
                </div>
            </section>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <section class="pt-card" aria-label="Riwayat pertemuan">
        <div class="pt-card-top">
            <div>
                <h2>Riwayat Pertemuan</h2>
                <p>Catatan mulai pertemuan, absensi, koreksi, materi, tugas, dan penyelesaian.</p>
            </div>
            <span class="pt-tag">{{ $riwayat->count() }} catatan terakhir</span>
        </div>
        <div class="pt-card-body">
            @if ($riwayat->isEmpty())
                <div class="pt-kosong">Belum ada catatan perubahan pada pertemuan ini.</div>
            @else
                <ul class="pt-riwayat">
                    @foreach ($riwayat as $catatan)
                        <li>
                            <strong>{{ $catatan->user_name ?: 'Sistem' }}</strong>
                            &middot; {{ $catatan->description ?: $catatan->activity }}
                            <time>{{ $catatan->created_at?->locale('id')->translatedFormat('d F Y, H:i') }} WIB</time>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</div>
@endsection
