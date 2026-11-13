@extends(request()->is('musyrifah/*') ? 'layouts.musyrifah' : 'layouts.guru')

@section('title', 'Rekap Absensi Pembelajaran')

@push('styles')
<style>
    .ab-page, .ab-page * { box-sizing: border-box; }
    .ab-page { width: 100%; min-width: 0; color: #292929; }
    .ab-header { padding-bottom: 18px; margin-bottom: 20px; border-bottom: 1px solid #e7cfd5; }
    .ab-breadcrumb { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; font-size: 12px; color: #8d8587; }
    .ab-breadcrumb a { color: #8d8587; text-decoration: none; }
    .ab-breadcrumb a:hover { color: #800b22; }
    .ab-header h1 { margin: 0 0 6px; font-size: 23px; font-weight: 700; }
    .ab-header p { margin: 0; font-size: 13px; line-height: 1.7; color: #777; }

    .ab-ringkas { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 10px; margin-bottom: 18px; }
    .ab-stat { padding: 12px 14px; background: #fff; border: 1px solid #efe2df; border-radius: 11px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .ab-stat span { display: block; font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #8a8284; }
    .ab-stat strong { display: block; margin-top: 5px; font-size: 19px; }
    .ab-stat small { display: block; margin-top: 3px; font-size: 10.5px; color: #9a9294; }
    .ab-stat.utama strong { color: #800b22; }

    .ab-card { overflow: hidden; background: #fff; border: 1px solid #efe2df; border-radius: 12px; box-shadow: 0 2px 8px rgba(50, 20, 20, .025); }
    .ab-card-top { display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f0e8e7; }
    .ab-card-top h2 { margin: 0; font-size: 15px; font-weight: 700; }
    .ab-card-top p { margin: 3px 0 0; font-size: 11px; color: #8a8284; }
    .ab-card-body { padding: 18px 20px; }

    .ab-filter { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)) auto; gap: 12px; align-items: end; }
    .ab-field { display: flex; flex-direction: column; min-width: 0; }
    .ab-field label { margin-bottom: 5px; font-size: 10.5px; font-weight: 700; color: #6e5a5f; letter-spacing: .04em; text-transform: uppercase; }
    .ab-select { width: 100%; min-height: 38px; padding: 8px 11px; border: 1px solid #e7dbd9; border-radius: 8px; background: #fff; font: inherit; font-size: 12.5px; color: #333; }
    .ab-select:focus { border-color: #800b22; outline: 0; box-shadow: 0 0 0 3px rgba(128, 11, 34, .07); }

    .ab-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 38px; padding: 9px 15px; border: 1px solid #800b22; border-radius: 8px; background: #800b22; color: #fff; font-family: inherit; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; }
    .ab-btn:hover { background: #65091b; }
    .ab-btn.ghost { background: #fff; color: #800b22; border-color: #e2c3ca; }
    .ab-btn.ghost:hover { background: #fdf6f7; }
    .ab-btn.small { min-height: 30px; padding: 6px 10px; font-size: 11px; }

    .ab-table-wrap { width: 100%; overflow-x: auto; }
    .ab-table { width: 100%; min-width: 900px; border-collapse: collapse; font-size: 12px; }
    .ab-table th { padding: 9px 11px; background: #f7f0f1; color: #6e5a5f; font-size: 10px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .ab-table td { padding: 10px 11px; border-bottom: 1px solid #f4eceb; color: #4f4749; vertical-align: top; line-height: 1.6; }
    .ab-table tr:last-child td { border-bottom: 0; }
    .ab-mapel { font-weight: 600; color: #332c2e; }
    .ab-ket { display: block; font-size: 10.5px; color: #9a9294; }
    .ab-angka { font-weight: 600; color: #332c2e; }

    .ab-pil { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 700; }
    .ab-pil.selesai { background: #def8e8; color: #17763e; }
    .ab-pil.berlangsung { background: #fdf3dc; color: #93650d; }
    .ab-pil.terjadwal { background: #f1eff0; color: #77706f; }

    .ab-kosong { padding: 30px 18px; border: 1px dashed #e3cfd4; border-radius: 10px; background: #fdfafa; text-align: center; color: #8a8284; font-size: 12px; line-height: 1.7; }
    .ab-kosong strong { display: block; margin-bottom: 4px; color: #4a4244; font-size: 13px; }

    @media (max-width: 1080px) {
        .ab-ringkas { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ab-filter { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 640px) {
        .ab-ringkas { grid-template-columns: 1fr; }
        .ab-filter { grid-template-columns: 1fr; }
        .ab-btn { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="ab-page">

    <header class="ab-header">
        <nav class="ab-breadcrumb" aria-label="Navigasi halaman">
            @if ($dariMusyrifah)
                <a href="{{ route('musyrifah.manajemen_pembelajaran') }}">Manajemen Pembelajaran</a>
            @else
                <a href="{{ route('guru.dashboard') }}">Dashboard</a>
            @endif
            <span aria-hidden="true">/</span>
            <span aria-current="page">Absensi</span>
        </nav>

        <h1>Rekap Absensi</h1>
        <p>
            Rekap absensi per mata pelajaran, kelompok, periode, dan pertemuan 1&ndash;{{ $targetPertemuan }}.
            Data ini berasal dari absensi yang disimpan Guru pada menu Pembelajaran.
        </p>
    </header>

    <div class="ab-ringkas">
        <div class="ab-stat utama">
            <span>Pertemuan Tercatat</span>
            <strong>{{ $ringkasan['pertemuan'] }}</strong>
            <small>{{ $ringkasan['selesai'] }} selesai &middot; {{ $ringkasan['berlangsung'] }} berlangsung</small>
        </div>
        <div class="ab-stat">
            <span>Total Absensi</span>
            <strong>{{ $ringkasan['absensi'] }}</strong>
            <small>Baris kehadiran tersimpan</small>
        </div>
        <div class="ab-stat">
            <span>Hadir</span>
            <strong>{{ $ringkasan['hadir'] }}</strong>
            <small>Dari seluruh absensi</small>
        </div>
        <div class="ab-stat">
            <span>Persentase Hadir</span>
            <strong>{{ $ringkasan['persentase'] }}%</strong>
            <small>Rata-rata kehadiran</small>
        </div>
        <div class="ab-stat">
            <span>Periode Aktif</span>
            <strong>{{ count($pilihanPeriode) }}</strong>
            <small>Periode pada cakupan data</small>
        </div>
    </div>

    <section class="ab-card">
        <div class="ab-card-top">
            <div>
                <h2>Filter Rekap</h2>
                <p>Pilih mata pelajaran, kelompok, periode, atau pertemuan tertentu.</p>
            </div>
        </div>
        <div class="ab-card-body">
            <form method="GET" action="{{ url()->current() }}" class="ab-filter">
                <div class="ab-field">
                    <label for="mata_pelajaran">Mata Pelajaran</label>
                    <select id="mata_pelajaran" name="mata_pelajaran" class="ab-select">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach ($pilihanMapel as $idMapel => $namaMapel)
                            <option value="{{ $idMapel }}" @selected((string) ($filter['mata_pelajaran'] ?? '') === (string) $idMapel)>
                                {{ $namaMapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="ab-field">
                    <label for="kelompok">Kelompok</label>
                    <select id="kelompok" name="kelompok" class="ab-select">
                        <option value="">Semua Kelompok</option>
                        @foreach ($pilihanKelompok as $kelompok)
                            <option value="{{ $kelompok }}" @selected(($filter['kelompok'] ?? '') === $kelompok)>
                                {{ $kelompok }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="ab-field">
                    <label for="periode">Periode</label>
                    <select id="periode" name="periode" class="ab-select">
                        <option value="">Semua Periode</option>
                        @foreach ($pilihanPeriode as $periode)
                            <option value="{{ $periode }}" @selected(($filter['periode'] ?? '') === $periode)>
                                {{ $periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="ab-field">
                    <label for="pertemuan">Pertemuan</label>
                    <select id="pertemuan" name="pertemuan" class="ab-select">
                        <option value="">Semua Pertemuan</option>
                        @for ($nomor = 1; $nomor <= $targetPertemuan; $nomor++)
                            <option value="{{ $nomor }}" @selected((string) ($filter['pertemuan'] ?? '') === (string) $nomor)>
                                Pertemuan {{ $nomor }}
                            </option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="ab-btn">Tampilkan</button>
            </form>
        </div>
    </section>

    <section class="ab-card" style="margin-top: 18px;">
        <div class="ab-card-top">
            <div>
                <h2>Daftar Absensi</h2>
                <p>Total {{ $rekap->count() }} pertemuan sesuai filter.</p>
            </div>
            @unless ($dariMusyrifah)
                <a class="ab-btn ghost small" href="{{ route('guru.pembelajaran') }}">Buka Pembelajaran</a>
            @endunless
        </div>

        @if ($rekap->isEmpty())
            <div class="ab-card-body">
                <div class="ab-kosong">
                    <strong>Belum ada absensi tersimpan.</strong>
                    Absensi akan tampil setelah Guru menjalankan pertemuan pada menu Pembelajaran.
                </div>
            </div>
        @else
            <div class="ab-table-wrap">
                <table class="ab-table">
                    <thead>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <th>Kelompok</th>
                            <th>Periode</th>
                            <th>Pertemuan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpa</th>
                            <th>Tugas</th>
                            @unless ($dariMusyrifah)
                                <th>Aksi</th>
                            @endunless
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap as $baris)
                            <tr>
                                <td>
                                    <span class="ab-mapel">{{ $baris->mataPelajaran?->nama ?? 'Mata Pelajaran' }}</span>
                                    <span class="ab-ket">{{ $baris->mataPelajaran?->kode ?: '-' }}</span>
                                </td>
                                <td>{{ $baris->kelompok }}</td>
                                <td>{{ $baris->periode }}</td>
                                <td>Pertemuan {{ $baris->pertemuan_ke }}</td>
                                <td>{{ $baris->tanggalTeks() }}</td>
                                <td>
                                    <span class="ab-pil {{ $baris->status }}">{{ $baris->labelStatus() }}</span>
                                </td>
                                <td><span class="ab-angka">{{ $baris->hadir_count }}</span></td>
                                <td>{{ $baris->izin_count }}</td>
                                <td>{{ $baris->sakit_count }}</td>
                                <td>{{ $baris->alpa_count }}</td>
                                <td>{{ $baris->tugas_count }}</td>
                                @unless ($dariMusyrifah)
                                    <td>
                                        <a class="ab-btn ghost small" href="{{ route('guru.pembelajaran.pertemuan', $baris) }}">
                                            Buka
                                        </a>
                                    </td>
                                @endunless
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
