@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    @include('partials.dashboard-gaya')

    @php
        $peran = $ringkasan['perRole'];
        $konfigurasi = $ringkasan['konfigurasi'];
    @endphp

    <div class="dash">

        <header class="dash-kepala">
            <div class="dash-sapaan">
                <h1>Assalamu’alaikum, {{ auth()->user()->name ?? 'Admin' }}</h1>
                <p>Ringkasan pengelolaan akun, aktivitas, dan konfigurasi teknis sistem.</p>
                <span class="dash-peran">Admin Sistem</span>
            </div>

            <span class="dash-tanggal">
                <x-panel.ikon nama="jadwal" />
                {{ now()->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </header>

        {{-- Ringkasan angka --}}
        <div class="dash-grid">
            <x-dash.statistik
                label="Total Akun"
                :nilai="$ringkasan['totalAkun']"
                ikon="pengguna"
                ket="Seluruh pengguna sistem"
                :tautan="route('admin.manajemen-pengguna')" />

            <x-dash.statistik
                label="Akun Aktif"
                :nilai="$ringkasan['akunAktif']"
                ikon="mahasiswa"
                nada="sukses"
                ket="Sudah dapat masuk sistem"
                :tautan="route('admin.manajemen-pengguna', ['status' => 'aktif'])" />

            <x-dash.statistik
                label="Menunggu Aktivasi"
                :nilai="$ringkasan['akunMenunggu']"
                ikon="pendaftaran"
                :nada="$ringkasan['akunMenunggu'] > 0 ? 'emas' : null"
                ket="Belum membuat kata sandi"
                :tautan="route('admin.manajemen-pengguna', ['status' => 'menunggu'])" />

            <x-dash.statistik
                label="Akun Nonaktif"
                :nilai="$ringkasan['akunNonaktif']"
                ikon="keluar"
                :nada="$ringkasan['akunNonaktif'] > 0 ? 'bahaya' : null"
                ket="Akses dihentikan sementara"
                :tautan="route('admin.manajemen-pengguna', ['status' => 'nonaktif'])" />
        </div>

        {{-- Aksi cepat --}}
        <div class="dash-aksi">
            <a href="{{ route('admin.manajemen-pengguna.create') }}">
                <x-panel.ikon nama="pengguna" />
                <span>Buat Akun Baru</span>
            </a>

            <a href="{{ route('admin.manajemen-pengguna') }}">
                <x-panel.ikon nama="pegawai" />
                <span>Manajemen Pengguna</span>
                @if ($ringkasan['akunMenunggu'] > 0)
                    <span class="dash-aksi-badge">{{ $ringkasan['akunMenunggu'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.data-pegawai.index') }}">
                <x-panel.ikon nama="pegawai" />
                <span>Data Pegawai</span>
            </a>

            <a href="{{ route('admin.data-mahasiswa.index') }}">
                <x-panel.ikon nama="mahasiswa" />
                <span>Data Mahasiswa</span>
            </a>

            <a href="{{ route('admin.log-aktivitas') }}">
                <x-panel.ikon nama="log" />
                <span>Log Aktivitas</span>
            </a>

            <a href="{{ route('admin.pengaturan-sistem') }}">
                <x-panel.ikon nama="sistem" />
                <span>Pengaturan Sistem</span>
            </a>
        </div>

        <div class="dash-kolom">

            {{-- Pengguna per role --}}
            <x-dash.panel judul="Jumlah Pengguna per Role" ikon="pengguna">
                <table class="dash-tabel">
                    <thead>
                        <tr>
                            <th scope="col">Role</th>
                            <th scope="col">Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mahasiswa</td>
                            <td>{{ $peran['mahasiswa'] }}</td>
                        </tr>
                        <tr>
                            <td>Guru</td>
                            <td>{{ $peran['guru'] }}</td>
                        </tr>
                        <tr>
                            <td>Musyrifah</td>
                            <td>{{ $peran['musyrifah'] }}</td>
                        </tr>
                        <tr>
                            <td>Pimpinan</td>
                            <td>{{ $peran['pimpinan'] }}</td>
                        </tr>
                        <tr>
                            <td>Admin Sistem</td>
                            <td>{{ $peran['admin'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </x-dash.panel>

            {{-- Status konfigurasi --}}
            <x-dash.panel judul="Status Konfigurasi" ikon="sistem"
                :tautan="route('admin.pengaturan-sistem')" label-tautan="Ubah">
                <table class="dash-tabel">
                    <tbody>
                        <tr>
                            <td>Nama sistem</td>
                            <td>{{ $konfigurasi['nama_sistem'] }}</td>
                        </tr>
                        <tr>
                            <td>Institusi</td>
                            <td>{{ $konfigurasi['nama_institusi'] }}</td>
                        </tr>
                        <tr>
                            <td>Durasi sesi</td>
                            <td>{{ $konfigurasi['durasi_sesi'] ? $konfigurasi['durasi_sesi'].' menit' : 'Bawaan' }}</td>
                        </tr>
                        <tr>
                            <td>Pengiriman email</td>
                            <td>{{ $konfigurasi['mail'] }}</td>
                        </tr>
                        <tr>
                            <td>Login Google</td>
                            <td>
                                <span class="dash-pil" data-nada="{{ $konfigurasi['google_login'] ? 'sukses' : 'bahaya' }}">
                                    {{ $konfigurasi['google_login'] ? 'Aktif' : 'Belum diatur' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Mode debug</td>
                            <td>
                                <span class="dash-pil" data-nada="{{ $konfigurasi['debug'] ? 'bahaya' : 'sukses' }}">
                                    {{ $konfigurasi['debug'] ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                @if ($konfigurasi['notifikasi'])
                    <p class="dash-catatan">
                        Notifikasi:
                        pengumuman {{ $konfigurasi['notifikasi']['pengumuman'] ? 'aktif' : 'nonaktif' }},
                        pembelajaran {{ $konfigurasi['notifikasi']['pembelajaran'] ? 'aktif' : 'nonaktif' }},
                        pembayaran {{ $konfigurasi['notifikasi']['pembayaran'] ? 'aktif' : 'nonaktif' }}.
                    </p>
                @endif
            </x-dash.panel>

            {{-- Akun terbaru --}}
            <x-dash.panel judul="Akun Terbaru" ikon="pegawai"
                :tautan="route('admin.manajemen-pengguna')" label-tautan="Kelola" rapat>
                @forelse ($ringkasan['akunTerbaru'] as $akun)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="pengguna" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $akun->name }}</span>
                            <span class="dash-baris-ket">{{ $akun->email }} · {{ ucfirst((string) $akun->role) }}</span>
                        </span>
                        <span class="dash-baris-akhir">
                            <span class="dash-pil"
                                data-nada="{{ in_array(strtolower((string) $akun->status), ['aktif'], true) ? 'sukses' : (in_array(strtolower((string) $akun->status), ['nonaktif'], true) ? 'bahaya' : 'emas') }}">
                                {{ ucfirst((string) $akun->status) }}
                            </span>
                        </span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada akun yang dibuat." />
                @endforelse
            </x-dash.panel>

            {{-- Aktivitas login --}}
            <x-dash.panel judul="Aktivitas Login Terakhir" ikon="log"
                :tautan="route('admin.log-aktivitas')" label-tautan="Log" rapat>
                @forelse ($ringkasan['loginTerbaru'] as $akun)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="profil" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $akun->name }}</span>
                            <span class="dash-baris-ket">{{ ucfirst((string) $akun->role) }}</span>
                        </span>
                        <span class="dash-baris-akhir">
                            {{ $akun->last_login_at?->locale('id')->diffForHumans() ?? '-' }}
                        </span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada catatan login."
                        petunjuk="Waktu login tercatat otomatis saat pengguna masuk." />
                @endforelse
            </x-dash.panel>

            {{-- Riwayat perubahan akun --}}
            <x-dash.panel judul="Riwayat Perubahan Akun" ikon="pendaftaran"
                :tautan="route('admin.log-aktivitas')" label-tautan="Lihat Log" rapat>
                @forelse ($ringkasan['riwayatAkun'] as $log)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="pendaftaran" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $log->description }}</span>
                            <span class="dash-baris-ket">{{ $log->user_name ?: 'Sistem' }}</span>
                        </span>
                        <span class="dash-baris-akhir">{{ $log->created_at?->locale('id')->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada riwayat perubahan akun." />
                @endforelse
            </x-dash.panel>

            {{-- Log sistem --}}
            <x-dash.panel judul="Log Sistem Terbaru" ikon="log"
                :tautan="route('admin.log-aktivitas')" label-tautan="Semua Log" rapat>
                @forelse ($ringkasan['logSistem'] as $log)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="log" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $log->description }}</span>
                            <span class="dash-baris-ket">
                                {{ $log->activity }} · {{ $log->user_name ?: 'Sistem' }}
                            </span>
                        </span>
                        <span class="dash-baris-akhir">{{ $log->created_at?->locale('id')->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada aktivitas sistem yang tercatat." />
                @endforelse
            </x-dash.panel>

        </div>
    </div>
@endsection
