@extends('layouts.guru')

@section('title', 'Penilaian Guru')

@push('styles')
<style>
    .pn-page, .pn-page * { box-sizing: border-box; }
    .pn-page { width: 100%; min-width: 0; color: #292929; }
    .pn-header { padding-bottom: 18px; margin-bottom: 20px; border-bottom: 1px solid #e7cfd5; }
    .pn-breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; font-size: 12px; color: #8d8587; }
    .pn-breadcrumb a { color: #8d8587; text-decoration: none; }
    .pn-breadcrumb a:hover { color: #800b22; }
    .pn-header h1 { margin: 0 0 6px; font-size: 23px; font-weight: 700; }
    .pn-header p { margin: 0; font-size: 13px; line-height: 1.7; color: #777; }

    .pn-ringkas { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 10px; margin-bottom: 18px; }
    .pn-stat { padding: 12px 14px; background: #fff; border: 1px solid #efe2df; border-radius: 11px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .pn-stat span { display: block; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #8a8284; }
    .pn-stat strong { display: block; margin-top: 5px; font-size: 19px; }
    .pn-stat small { display: block; margin-top: 3px; font-size: 10.5px; color: #9a9294; }
    .pn-stat.utama strong { color: #800b22; }

    .pn-card { margin-bottom: 18px; overflow: hidden; background: #fff; border: 1px solid #efe2df; border-radius: 12px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .pn-card-top { display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f0e8e7; }
    .pn-card-top h2 { margin: 0; font-size: 15px; font-weight: 700; }
    .pn-card-top p { margin: 3px 0 0; font-size: 11px; color: #8a8284; }
    .pn-card-body { padding: 18px 20px; }

    .pn-filter { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)) auto; gap: 12px; align-items: end; }
    .pn-field { display: flex; flex-direction: column; min-width: 0; }
    .pn-field label { margin-bottom: 5px; font-size: 10.5px; font-weight: 700; color: #6e5a5f; letter-spacing: .04em; text-transform: uppercase; }
    .pn-select { width: 100%; min-height: 38px; padding: 8px 11px; border: 1px solid #e7dbd9; border-radius: 8px; background: #fff; font: inherit; font-size: 12.5px; color: #333; }
    .pn-select:focus { border-color: #800b22; outline: 0; box-shadow: 0 0 0 3px rgba(128, 11, 34, .07); }

    .pn-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 38px; padding: 9px 15px; border: 1px solid #800b22; border-radius: 8px; background: #800b22; color: #fff; font-family: inherit; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .pn-btn:hover { background: #65091b; }
    .pn-btn.ghost { background: #fff; color: #800b22; border-color: #e2c3ca; }
    .pn-btn.ghost:hover { background: #fdf6f7; }
    .pn-btn.small { min-height: 30px; padding: 6px 10px; font-size: 11px; }

    .pn-table-wrap { width: 100%; overflow-x: auto; }
    .pn-table { width: 100%; min-width: 720px; border-collapse: collapse; font-size: 12px; }
    .pn-table th { padding: 9px 11px; background: #f7f0f1; color: #6e5a5f; font-size: 10px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .pn-table td { padding: 10px 11px; border-bottom: 1px solid #f4eceb; color: #4f4749; vertical-align: top; line-height: 1.6; }
    .pn-table tr:last-child td { border-bottom: 0; }
    .pn-judul { font-weight: 600; color: #332c2e; }
    .pn-ket { display: block; font-size: 10.5px; color: #9a9294; }

    .pn-pil { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 700; }
    .pn-pil.sukses { background: #def8e8; color: #17763e; }
    .pn-pil.emas { background: #fdf3dc; color: #93650d; }
    .pn-pil.netral { background: #f1eff0; color: #77706f; }

    .pn-kosong { padding: 30px 18px; border: 1px dashed #e3cfd4; border-radius: 10px; background: #fdfafa; text-align: center; color: #8a8284; font-size: 12px; line-height: 1.7; }
    .pn-kosong strong { display: block; margin-bottom: 4px; color: #4a4244; font-size: 13px; }

    @media (max-width: 1080px) {
        .pn-ringkas { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 720px) {
        .pn-ringkas { grid-template-columns: 1fr; }
        .pn-filter { grid-template-columns: 1fr; }
        .pn-btn { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="pn-page">

    <header class="pn-header">
        <nav class="pn-breadcrumb" aria-label="Navigasi halaman">
            <a href="{{ route('guru.dashboard') }}">Dashboard</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">Penilaian</span>
        </nav>

        <h1>Penilaian</h1>
        <p>
            Kelola nilai mahasiswa pada tugas yang Anda berikan. Penilaian disimpan pada
            data pengumpulan tugas sehingga langsung terbaca pada monitoring Musyrifah.
        </p>
    </header>

    <div class="pn-ringkas">
        <div class="pn-stat utama">
            <span>Tugas</span>
            <strong>{{ $ringkasan['tugas'] }}</strong>
            <small>Pada penugasan Anda</small>
        </div>
        <div class="pn-stat">
            <span>Pengumpulan</span>
            <strong>{{ $ringkasan['pengumpulan'] }}</strong>
            <small>Berkas mahasiswa masuk</small>
        </div>
        <div class="pn-stat">
            <span>Sudah Dinilai</span>
            <strong>{{ $ringkasan['dinilai'] }}</strong>
            <small>Nilai tersimpan</small>
        </div>
        <div class="pn-stat">
            <span>Belum Dinilai</span>
            <strong>{{ $ringkasan['belum_dinilai'] }}</strong>
            <small>Perlu diperiksa</small>
        </div>
        <div class="pn-stat">
            <span>Rata-rata Nilai</span>
            <strong>{{ $ringkasan['rata_rata'] }}</strong>
            <small>Dari seluruh tugas</small>
        </div>
    </div>

    <section class="pn-card">
        <div class="pn-card-top">
            <div>
                <h2>Filter Penilaian</h2>
                <p>Hanya mata pelajaran dan kelompok yang ditugaskan Musyrifah kepada Anda.</p>
            </div>
        </div>
        <div class="pn-card-body">
            <form method="GET" action="{{ route('guru.penilaian') }}" class="pn-filter">
                <div class="pn-field">
                    <label for="mata_pelajaran">Mata Pelajaran</label>
                    <select id="mata_pelajaran" name="mata_pelajaran" class="pn-select">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach ($pilihanMapel as $idMapel => $namaMapel)
                            <option value="{{ $idMapel }}" @selected((string) ($filter['mata_pelajaran'] ?? '') === (string) $idMapel)>
                                {{ $namaMapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pn-field">
                    <label for="kelompok">Kelompok</label>
                    <select id="kelompok" name="kelompok" class="pn-select">
                        <option value="">Semua Kelompok</option>
                        @foreach ($pilihanKelompok as $kelompok)
                            <option value="{{ $kelompok }}" @selected(($filter['kelompok'] ?? '') === $kelompok)>
                                {{ $kelompok }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="pn-btn">Tampilkan</button>
            </form>
        </div>
    </section>

    <section class="pn-card">
        <div class="pn-card-top">
            <div>
                <h2>Daftar Tugas &amp; Nilai</h2>
                <p>Total {{ $daftarTugas->count() }} tugas sesuai filter.</p>
            </div>
        </div>

        @if ($daftarTugas->isEmpty())
            <div class="pn-card-body">
                <div class="pn-kosong">
                    <strong>Belum ada tugas pada penugasan ini.</strong>
                    Tambahkan tugas dari menu Pembelajaran pada pertemuan yang sedang berjalan.
                </div>
            </div>
        @else
            <div class="pn-table-wrap">
                <table class="pn-table">
                    <thead>
                        <tr>
                            <th>Judul Tugas</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelompok</th>
                            <th>Batas Pengumpulan</th>
                            <th>Status</th>
                            <th>Pengumpulan</th>
                            <th>Dinilai</th>
                            <th>Rata-rata</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarTugas as $tugas)
                            <tr>
                                <td>
                                    <span class="pn-judul">{{ $tugas->judul }}</span>
                                    <span class="pn-ket">
                                        {{ $tugas->pertemuan_id ? 'Tugas pertemuan' : 'Tugas luar pertemuan' }}
                                    </span>
                                </td>
                                <td>{{ $tugas->namaMataPelajaran() }}</td>
                                <td>{{ $tugas->kelompok ?: '-' }}</td>
                                <td>{{ $tugas->batasTeks() }}</td>
                                <td>
                                    <span class="pn-pil {{ $tugas->isDipublikasikan() ? 'sukses' : 'emas' }}">
                                        {{ $tugas->labelStatus() }}
                                    </span>
                                </td>
                                <td>{{ $tugas->pengumpulan_count }}</td>
                                <td>{{ $tugas->pengumpulan_dinilai_count }}</td>
                                <td>{{ $tugas->pengumpulan_avg_nilai !== null ? round((float) $tugas->pengumpulan_avg_nilai, 1) : '-' }}</td>
                                <td>
                                    <a class="pn-btn ghost small" href="{{ route('guru.pengumpulan.index', $tugas) }}">
                                        Beri Nilai
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="pn-card">
        <div class="pn-card-top">
            <div>
                <h2>Rekap Nilai Mahasiswa</h2>
                <p>
                    @if ($kelompokRekap)
                        Kelompok {{ $kelompokRekap }} &middot; {{ $rekapMahasiswa->count() }} mahasiswa
                    @else
                        Belum ada kelompok pada penugasan Anda.
                    @endif
                </p>
            </div>
        </div>

        @if ($rekapMahasiswa->isEmpty())
            <div class="pn-card-body">
                <div class="pn-kosong">
                    <strong>Belum ada mahasiswa untuk direkap.</strong>
                    Keanggotaan kelompok ditetapkan Musyrifah melalui Manajemen Pembelajaran.
                </div>
            </div>
        @else
            <div class="pn-table-wrap">
                <table class="pn-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Dikumpulkan</th>
                            <th>Dinilai</th>
                            <th>Rata-rata Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekapMahasiswa as $baris)
                            <tr>
                                <td>
                                    <span class="pn-judul">{{ $baris['mahasiswa']->name }}</span>
                                    <span class="pn-ket">{{ $baris['mahasiswa']->nim ?: 'NIM belum diisi' }}</span>
                                </td>
                                <td>{{ $baris['dikumpulkan'] }} dari {{ $baris['target_tugas'] }} tugas</td>
                                <td>{{ $baris['dinilai'] }}</td>
                                <td>
                                    @if ($baris['rata_rata'] !== null)
                                        <span class="pn-pil sukses">{{ $baris['rata_rata'] }}</span>
                                    @else
                                        <span class="pn-pil netral">Belum dinilai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
