@extends('layouts.guru')

@section('title', 'Pembelajaran Guru')

@push('styles')
<style>
    .pl-page, .pl-page * { box-sizing: border-box; }
    .pl-page { width: 100%; min-width: 0; color: #292929; }
    .pl-header { padding-bottom: 20px; margin-bottom: 22px; border-bottom: 1px solid #e7cfd5; }
    .pl-breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; font-size: 12px; color: #8d8587; }
    .pl-breadcrumb a { color: #8d8587; text-decoration: none; }
    .pl-breadcrumb a:hover { color: #800b22; }
    .pl-header h1 { margin: 0 0 6px; font-size: 24px; font-weight: 700; }
    .pl-header p { margin: 0; font-size: 13px; line-height: 1.7; color: #777; }

    .pl-alert { margin-bottom: 16px; padding: 12px 15px; border: 1px solid #bfebcb; border-radius: 9px; background: #e8f7ed; color: #196a38; font-size: 12px; line-height: 1.6; }
    .pl-alert.error { border-color: #f0c5c5; background: #fff0f0; color: #ae3030; }
    .pl-alert.info { border-color: #cfe3f5; background: #eef6fd; color: #205a86; }

    .pl-card { margin-bottom: 18px; overflow: hidden; background: #fff; border: 1px solid #efe2df; border-radius: 12px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .pl-card-top { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; padding: 16px 20px; border-bottom: 1px solid #f0e8e7; }
    .pl-card-top h2 { margin: 0; font-size: 15px; font-weight: 700; }
    .pl-card-top p { margin: 3px 0 0; font-size: 11px; color: #8a8284; }
    .pl-card-body { padding: 18px 20px; }

    .pl-filter { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)) auto; gap: 12px; align-items: end; }
    .pl-field { display: flex; flex-direction: column; min-width: 0; }
    .pl-field label { margin-bottom: 5px; font-size: 10.5px; font-weight: 700; color: #6e5a5f; letter-spacing: .04em; text-transform: uppercase; }
    .pl-input, .pl-select { width: 100%; min-height: 38px; padding: 8px 11px; border: 1px solid #e7dbd9; border-radius: 8px; background: #fff; font: inherit; font-size: 12.5px; color: #333; }
    .pl-input:focus, .pl-select:focus { border-color: #800b22; outline: 0; box-shadow: 0 0 0 3px rgba(128, 11, 34, .07); }

    .pl-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 38px; padding: 9px 15px; border: 1px solid #800b22; border-radius: 8px; background: #800b22; color: #fff; font-family: inherit; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .pl-btn:hover { background: #65091b; }
    .pl-btn.ghost { background: #fff; color: #800b22; border-color: #e2c3ca; }
    .pl-btn.small { min-height: 31px; padding: 6px 11px; font-size: 11px; }

    .pl-ringkas { display: flex; align-items: flex-start; flex-wrap: wrap; gap: 14px; justify-content: space-between; }
    .pl-ringkas h3 { margin: 0 0 6px; font-size: 17px; font-weight: 700; }
    .pl-tags { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 8px; }
    .pl-tag { padding: 4px 10px; border-radius: 999px; background: #f7f0f1; color: #6e5a5f; font-size: 10.5px; font-weight: 600; }
    .pl-tag.utama { background: #fbe9ed; color: #800b22; }

    .pl-progress { min-width: 220px; flex: 1 1 240px; }
    .pl-progress-head { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11px; font-weight: 700; color: #6e5a5f; }
    .pl-track { width: 100%; height: 8px; overflow: hidden; border-radius: 999px; background: #f1e7e8; }
    .pl-fill { height: 100%; border-radius: 999px; background: #800b22; transition: width .3s; }

    .pl-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-top: 16px; }
    .pl-tile { position: relative; display: flex; flex-direction: column; gap: 6px; padding: 12px; border: 1px solid #eee2e1; border-radius: 10px; background: #fdfbfb; }
    .pl-tile.selesai { border-color: #cbe7d4; background: #f4fbf6; }
    .pl-tile.berlangsung { border-color: #f2d9a8; background: #fdf8ec; }
    .pl-tile-nomor { font-size: 11px; font-weight: 700; color: #8a8284; letter-spacing: .04em; text-transform: uppercase; }
    .pl-tile-status { display: inline-flex; align-self: flex-start; padding: 3px 8px; border-radius: 999px; font-size: 9.5px; font-weight: 700; }
    .pl-tile-status.selesai { background: #def8e8; color: #17763e; }
    .pl-tile-status.berlangsung { background: #fdf3dc; color: #93650d; }
    .pl-tile-status.terjadwal { background: #f1eff0; color: #77706f; }
    .pl-tile-tanggal { font-size: 11px; color: #6b6164; }
    .pl-tile-meta { font-size: 10.5px; color: #9a9294; line-height: 1.5; }
    .pl-tile a.pl-btn { margin-top: auto; }

    .pl-table-wrap { width: 100%; overflow-x: auto; }
    .pl-table { width: 100%; min-width: 620px; border-collapse: collapse; font-size: 12px; }
    .pl-table th { padding: 9px 11px; background: #f7f0f1; color: #6e5a5f; font-size: 10px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .pl-table td { padding: 10px 11px; border-bottom: 1px solid #f4eceb; color: #4f4749; vertical-align: top; line-height: 1.6; }
    .pl-table tr:last-child td { border-bottom: 0; }
    .pl-table .aktif { background: #fdf6f7; }

    .pl-empty { padding: 30px 18px; border: 1px dashed #e3cfd4; border-radius: 10px; background: #fdfafa; text-align: center; color: #8a8284; font-size: 12px; line-height: 1.7; }
    .pl-empty strong { display: block; margin-bottom: 4px; color: #4a4244; font-size: 13px; }

    @media (max-width: 980px) {
        .pl-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 760px) {
        .pl-filter { grid-template-columns: 1fr; }
        .pl-grid { grid-template-columns: 1fr; }
        .pl-btn { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="pl-page">

    <header class="pl-header">
        <nav class="pl-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('guru.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">Pembelajaran</span>
        </nav>
        <h1>Pembelajaran</h1>
        <p>
            Pilih mata pelajaran yang Anda ampu, jalankan pertemuan 1&ndash;{{ $targetPertemuan }},
            isi absensi, unggah materi dan tugas, lalu selesaikan pertemuan.
        </p>
    </header>

    @if (session('success'))
        <div class="pl-alert" role="status">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="pl-alert info" role="status">{{ session('info') }}</div>
    @endif

    @if (session('error'))
        <div class="pl-alert error" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="pl-alert error" role="alert">
            <strong>Perubahan belum tersimpan.</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($penugasan->isEmpty())
        <div class="pl-card">
            <div class="pl-card-body">
                <div class="pl-empty">
                    <strong>Belum ada penugasan.</strong>
                    Mata pelajaran, kelompok, dan jadwal ditetapkan oleh Musyrifah melalui
                    Manajemen Pembelajaran. Pastikan akun Guru Anda dipilih sebagai Guru Pengampu
                    pada jadwal tersebut, lalu muat ulang halaman ini. Hubungi Musyrifah bila
                    jadwal mengajar Anda belum tercantum.
                </div>
            </div>
        </div>
    @else
        <section class="pl-card" aria-label="Pilih penugasan">
            <div class="pl-card-top">
                <div>
                    <h2>Pilih Mata Pelajaran</h2>
                    <p>Daftar ini hanya memuat penugasan yang diberikan Musyrifah kepada Anda.</p>
                </div>
            </div>
            <div class="pl-card-body">
                <form method="GET" action="{{ route('guru.pembelajaran') }}" class="pl-filter">
                    <div class="pl-field">
                        <label for="mata_pelajaran">Mata Pelajaran</label>
                        <select id="mata_pelajaran" name="mata_pelajaran" class="pl-select">
                            @foreach ($pilihanMapel as $idMapel => $namaMapel)
                                <option value="{{ $idMapel }}" @selected((int) ($filter['mata_pelajaran'] ?? $jadwal?->mata_pelajaran_id) === (int) $idMapel)>
                                    {{ $namaMapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pl-field">
                        <label for="kelompok">Kelompok</label>
                        <select id="kelompok" name="kelompok" class="pl-select">
                            @foreach ($pilihanKelompok as $kelompok)
                                <option value="{{ $kelompok }}" @selected(($filter['kelompok'] ?? $jadwal?->kelompok) === $kelompok)>
                                    {{ $kelompok }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pl-field">
                        <label for="periode">Periode</label>
                        <select id="periode" name="periode" class="pl-select">
                            @foreach ($pilihanPeriode as $periode)
                                <option value="{{ $periode }}" @selected(($filter['periode'] ?? $periodeAktif) === $periode)>
                                    {{ $periode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="pl-btn">Tampilkan</button>
                </form>
            </div>
        </section>

        @if ($jadwal && $progres)
            <section class="pl-card" aria-label="Progres pertemuan">
                <div class="pl-card-body">
                    <div class="pl-ringkas">
                        <div>
                            <h3>{{ $jadwal->namaMataPelajaran() }}</h3>
                            <div class="pl-tags">
                                <span class="pl-tag utama">{{ $jadwal->kelompok }}</span>
                                <span class="pl-tag">{{ $periodeAktif }}</span>
                                <span class="pl-tag">{{ $jadwal->hari }}, {{ $jadwal->jamTeks() }}</span>
                                <span class="pl-tag">{{ $jadwal->ruangan }}</span>
                            </div>
                        </div>

                        <div class="pl-progress">
                            <div class="pl-progress-head">
                                <span>Progres pertemuan</span>
                                <span>{{ $progres['selesai'] }}/{{ $targetPertemuan }} selesai</span>
                            </div>
                            <div class="pl-track" role="img" aria-label="Progres {{ $progres['persentase'] }} persen">
                                <div class="pl-fill" style="width: {{ min(100, $progres['persentase']) }}%"></div>
                            </div>
                        </div>

                        <div>
                            @if ($progres['berlangsung'] !== null)
                                @php($pertemuanBerlangsung = $progres['daftar'][$progres['berlangsung'] - 1]['pertemuan'])
                                <a class="pl-btn" href="{{ $pertemuanBerlangsung ? route('guru.pembelajaran.pertemuan', $pertemuanBerlangsung) : '#' }}">
                                    Lanjutkan Pertemuan {{ $progres['berlangsung'] }}
                                </a>
                            @elseif ($progres['berikutnya'] !== null)
                                <form method="POST" action="{{ route('guru.pembelajaran.mulai') }}">
                                    @csrf
                                    <input type="hidden" name="mata_pelajaran_id" value="{{ $jadwal->mata_pelajaran_id }}">
                                    <input type="hidden" name="kelompok" value="{{ $jadwal->kelompok }}">
                                    <input type="hidden" name="periode" value="{{ $periodeAktif }}">
                                    <button type="submit" class="pl-btn">Mulai Pertemuan {{ $progres['berikutnya'] }}</button>
                                </form>
                            @else
                                <span class="pl-tag utama">16 pertemuan selesai</span>
                            @endif
                        </div>
                    </div>

                    <div class="pl-grid">
                        @foreach ($progres['daftar'] as $butir)
                            <div class="pl-tile {{ $butir['status'] ?? '' }}">
                                <span class="pl-tile-nomor">Pertemuan {{ $butir['nomor'] }}</span>
                                <span class="pl-tile-status {{ strtolower($butir['label_status']) }}">{{ $butir['label_status'] }}</span>
                                <span class="pl-tile-tanggal">{{ $butir['tanggal'] ?? 'Belum dimulai' }}</span>
                                <span class="pl-tile-meta">
                                    Materi: {{ $butir['punya_materi'] ? 'tersedia' : 'belum' }}
                                    &middot; Tugas: {{ $butir['jumlah_tugas'] }}
                                </span>
                                @if ($butir['pertemuan'])
                                    <a class="pl-btn ghost small" href="{{ route('guru.pembelajaran.pertemuan', $butir['pertemuan']) }}">
                                        Buka Pertemuan
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="pl-card" aria-label="Semua penugasan">
                <div class="pl-card-top">
                    <div>
                        <h2>Penugasan Saya</h2>
                        <p>Klik salah satu baris untuk berpindah mata pelajaran, kelompok, atau periode.</p>
                    </div>
                </div>
                <div class="pl-table-wrap">
                    <table class="pl-table">
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Kelompok</th>
                                <th>Periode</th>
                                <th>Jadwal</th>
                                <th>Progres</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ringkasanPenugasan as $baris)
                                <tr class="{{ $baris['aktif'] ? 'aktif' : '' }}">
                                    <td>{{ $baris['jadwal']->namaMataPelajaran() }}</td>
                                    <td>{{ $baris['jadwal']->kelompok }}</td>
                                    <td>{{ $baris['periode'] }}</td>
                                    <td>{{ $baris['jadwal']->hari }}, {{ $baris['jadwal']->jamTeks() }}</td>
                                    <td>{{ $baris['selesai'] }}/{{ $targetPertemuan }} pertemuan</td>
                                    <td>
                                        <a class="pl-btn ghost small" href="{{ route('guru.pembelajaran', [
                                            'mata_pelajaran' => $baris['jadwal']->mata_pelajaran_id,
                                            'kelompok' => $baris['jadwal']->kelompok,
                                            'periode' => $baris['periode'],
                                        ]) }}">Pilih</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    @endif
</div>
@endsection
