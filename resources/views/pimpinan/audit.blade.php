@extends('layouts.pimpinan')

@section('title', 'Audit Trail SIMAHAD')

@section('content')
    <style>
        .aud { font-family: 'Poppins', sans-serif; color: #29252a; padding: 8px 0 28px; }
        .aud-head { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 18px; }
        .aud-head h1 { margin: 0 0 6px; color: #6B0D1B; font-size: 25px; font-weight: 700; }
        .aud-head p { margin: 0; color: #81777d; font-size: 13px; line-height: 1.65; }
        .aud-ringkas { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 18px; }
        .aud-kartu-angka { padding: 14px 16px; border: 1px solid #eee7e9; border-radius: 13px; background: #fff; box-shadow: 0 4px 15px rgba(50,20,30,.04); }
        .aud-kartu-angka span { display: block; color: #81777d; font-size: 11.5px; font-weight: 600; }
        .aud-kartu-angka strong { display: block; margin-top: 4px; color: #6B0D1B; font-size: 21px; font-weight: 700; }
        .aud-tab { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
        .aud-tab a { padding: 9px 15px; border: 1px solid #ecd9dd; border-radius: 999px; background: #fff; color: #6B0D1B; font-size: 12.5px; font-weight: 600; text-decoration: none; }
        .aud-tab a.active { background: #F8EDEF; border-color: #C9A96A; font-weight: 700; }
        .aud-filter { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px; padding: 16px; margin-bottom: 18px; border: 1px solid #eee7e9; border-radius: 14px; background: #fff; }
        .aud-field { display: flex; flex-direction: column; gap: 6px; min-width: 165px; }
        .aud-field.cari { flex: 1; min-width: 220px; }
        .aud-field label { color: #81777d; font-size: 11.5px; font-weight: 600; }
        .aud-field select, .aud-field input { width: 100%; box-sizing: border-box; padding: 9px 11px; border: 1px solid #e8dfe2; border-radius: 9px; background: #fff; color: #30272b; font-family: inherit; font-size: 12.5px; outline-color: #6B0D1B; }
        .aud-btn { display: inline-flex; align-items: center; justify-content: center; padding: 10px 16px; border: 1px solid #6B0D1B; border-radius: 9px; background: #6B0D1B; color: #fff; font-family: inherit; font-size: 12.5px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .aud-btn:hover { background: #560A16; border-color: #560A16; }
        .aud-kartu { padding: 18px; border: 1px solid #eee7e9; border-radius: 15px; background: #fff; box-shadow: 0 4px 15px rgba(50,20,30,.04); }
        .aud-wrap { width: 100%; overflow-x: auto; }
        .aud-tabel { width: 100%; min-width: 780px; border-collapse: collapse; }
        .aud-tabel th { padding: 11px 10px; border-bottom: 1px solid #eee1e4; background: #f8f0f2; color: #6B0D1B; text-align: left; font-size: 11.5px; font-weight: 700; white-space: nowrap; }
        .aud-tabel td { padding: 11px 10px; border-bottom: 1px solid #f1ebed; color: #51474c; font-size: 12px; vertical-align: top; }
        .aud-tabel tbody tr:hover { background: #fffafb; }
        .aud-kosong { padding: 26px !important; color: #91858a !important; text-align: center; }
        .aud-badge { display: inline-block; padding: 3px 9px; border-radius: 999px; background: #F8EDEF; color: #6B0D1B; font-size: 10.5px; font-weight: 700; }
        .aud-diff { display: block; margin-top: 4px; font-size: 11px; color: #6f636a; }
        .aud-diff b { color: #6B0D1B; }
        .aud-paginasi { margin-top: 14px; }
        @media (max-width: 700px) {
            .aud-head h1 { font-size: 21px; }
            .aud-field, .aud-field.cari { min-width: 100%; }
            .aud-filter .aud-btn { width: 100%; }
        }
    </style>

    <div class="aud">
        <div class="aud-head">
            <div>
                <h1>Audit Trail SIMAHAD</h1>
                <p>
                    Riwayat aktivitas penting yang tercatat otomatis setelah perubahan tersimpan.
                    Riwayat bersifat terlindungi: tidak dapat diubah atau dihapus.
                </p>
            </div>
        </div>

        <div class="aud-ringkas">
            <div class="aud-kartu-angka">
                <span>Total Aktivitas</span>
                <strong>{{ $ringkasan['total'] }}</strong>
            </div>
            <div class="aud-kartu-angka">
                <span>Aktivitas Hari Ini</span>
                <strong>{{ $ringkasan['hari_ini'] }}</strong>
            </div>
            <div class="aud-kartu-angka">
                <span>Audit Laporan</span>
                <strong>{{ $ringkasan['audit_laporan'] }}</strong>
            </div>
        </div>

        <div class="aud-tab">
            <a href="{{ route('pimpinan.audit', ['tab' => 'aktivitas']) }}"
                class="{{ $tab === 'aktivitas' ? 'active' : '' }}">Aktivitas Sistem</a>
            <a href="{{ route('pimpinan.audit', ['tab' => 'laporan']) }}"
                class="{{ $tab === 'laporan' ? 'active' : '' }}">Audit Laporan</a>
        </div>

        <form action="{{ route('pimpinan.audit') }}" method="GET" class="aud-filter">
            <input type="hidden" name="tab" value="{{ $tab }}">

            @if ($tab === 'aktivitas')
                <div class="aud-field">
                    <label for="aud-modul">Modul</label>
                    <select name="modul" id="aud-modul">
                        <option value="">Semua modul</option>
                        @foreach ($daftarModul as $kunci => $label)
                            <option value="{{ $kunci }}" @selected($filter['modul'] === $kunci)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="aud-field">
                    <label for="aud-modul">Jenis Laporan</label>
                    <select name="modul" id="aud-modul">
                        <option value="">Semua jenis</option>
                        @foreach (\App\Services\LaporanRegisterService::JUDUL as $kunci => $label)
                            <option value="{{ $kunci }}" @selected($filter['modul'] === $kunci)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="aud-field">
                <label for="aud-role">Role Pengguna</label>
                <select name="role" id="aud-role">
                    <option value="">Semua role</option>
                    @foreach (['pimpinan', 'musyrifah', 'guru', 'admin', 'mahasiswa', 'sistem'] as $peran)
                        <option value="{{ $peran }}" @selected($filter['role'] === $peran)>{{ ucfirst($peran) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="aud-field">
                <label for="aud-periode">Periode</label>
                <select name="periode" id="aud-periode">
                    <option value="">Semua waktu</option>
                    <option value="hari_ini" @selected($filter['periode'] === 'hari_ini')>Hari ini</option>
                    <option value="7_hari" @selected($filter['periode'] === '7_hari')>7 hari terakhir</option>
                    <option value="30_hari" @selected($filter['periode'] === '30_hari')>30 hari terakhir</option>
                </select>
            </div>

            <div class="aud-field cari">
                <label for="aud-cari">Pencarian</label>
                <input type="text" name="cari" id="aud-cari" value="{{ $filter['cari'] }}"
                    placeholder="Cari objek, keterangan, atau nama pengguna...">
            </div>

            <button type="submit" class="aud-btn">Terapkan</button>
        </form>

        <div class="aud-kartu">
            @if ($tab === 'aktivitas')
                <div class="aud-wrap">
                    <table class="aud-tabel">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Role</th>
                                <th>Modul</th>
                                <th>Aksi</th>
                                <th>Objek</th>
                                <th>Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($aktivitas as $log)
                                <tr>
                                    <td>{{ $log->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ $log->user_name ?: 'Sistem' }}</td>
                                    <td>{{ ucfirst((string) $log->user_role ?: '-') }}</td>
                                    <td>{{ $daftarModul[$log->modul] ?? ($log->modul ?: '-') }}</td>
                                    <td><span class="aud-badge">{{ $log->aksi ?: ($log->activity ?: '-') }}</span></td>
                                    <td>
                                        {{ $log->objek ?: '-' }}
                                        @if ($log->objek_id)
                                            <span style="color:#91858a;">#{{ $log->objek_id }}</span>
                                        @endif
                                        @if (filled($log->description) && $log->description !== $log->objek)
                                            <br><span style="color:#8b7f84;font-size:11px;">{{ $log->description }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $sesudahLog = $log->data_sesudah ?? []; $sebelumLog = $log->data_sebelum ?? []; @endphp
                                        @if (! empty($sesudahLog))
                                            <span class="aud-diff">
                                                @foreach ($sesudahLog as $kunci => $nilai)
                                                    <b>{{ $kunci }}</b>: {{ $sebelumLog[$kunci] ?? '-' }} →
                                                    {{ is_scalar($nilai) || $nilai === null ? $nilai : json_encode($nilai) }}<br>
                                                @endforeach
                                            </span>
                                        @else
                                            <span style="color:#b0a4a9;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="aud-kosong">
                                        Belum ada aktivitas yang sesuai dengan filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="aud-paginasi">{{ $aktivitas->links() }}</div>
            @else
                <div class="aud-wrap">
                    <table class="aud-tabel">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Role</th>
                                <th>Laporan</th>
                                <th>Aksi</th>
                                <th>Versi</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $jejak)
                                <tr>
                                    <td>{{ $jejak->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ $jejak->pengguna_nama ?: 'Sistem' }}</td>
                                    <td>{{ ucfirst((string) $jejak->pengguna_role ?: '-') }}</td>
                                    <td>
                                        @if ($jejak->laporan)
                                            <a href="{{ route('pimpinan.laporan.detail', $jejak->laporan) }}"
                                                style="color:#6B0D1B;font-weight:600;text-decoration:none;">
                                                {{ $jejak->laporan->judul }}
                                            </a>
                                        @else
                                            {{ \App\Services\LaporanRegisterService::JUDUL[$jejak->jenis_laporan] ?? '-' }}
                                        @endif
                                        <br>
                                        <span style="color:#8b7f84;font-size:11px;">
                                            {{ $jejak->periode }}
                                            @if ($jejak->lantai)
                                                · Lantai {{ $jejak->lantai }}
                                            @endif
                                        </span>
                                    </td>
                                    <td><span class="aud-badge">{{ $jejak->labelAksi() }}</span></td>
                                    <td>{{ (int) $jejak->versi }}</td>
                                    <td>{{ $jejak->keterangan ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="aud-kosong">
                                        Belum ada audit laporan yang sesuai dengan filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="aud-paginasi">{{ $laporan->links() }}</div>
            @endif
        </div>
    </div>
@endsection
