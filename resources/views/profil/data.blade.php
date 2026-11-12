@extends('layouts.panel')

@section('title', 'Data Saya')

@section('konten')
    @include('profil.partials.gaya')

    @php
        $statusAkun = strtolower(trim((string) $user->status));
        $kelasStatus = match ($statusAkun) {
            'aktif' => 'pr-badge--aktif',
            'menunggu aktivasi', 'menunggu', 'pending' => 'pr-badge--menunggu',
            default => 'pr-badge--nonaktif',
        };

        // Form dibuka otomatis saat validasi gagal agar pesan galat terlihat.
        $modeUbah = $errors->any();
        $jenisKelamin = old('jenis_kelamin', $pegawai?->jenis_kelamin);
        $labelJenisKelamin = match ($pegawai?->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    @endphp

    <div class="pr-page">
        @if (session('success'))
            <div class="pr-alert pr-alert--sukses" role="status">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="pr-alert pr-alert--gagal" role="alert">
                <strong>Periksa kembali data yang dimasukkan.</strong>
                <ul>
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="pr-hero">
            <div class="pr-hero-teks">
                <span class="pr-hero-eyebrow">Data Saya · {{ $labelPeran }}</span>
                <h1>{{ $user->name }}</h1>
                <p class="pr-hero-jabatan">Jabatan: {{ $jabatan }}</p>
                <p>Informasi pribadi lengkap yang tercatat pada sistem SIMAHAD.</p>

                <div class="pr-hero-aksi">
                    <a href="{{ route('profil.pengguna') }}" class="pr-tombol pr-tombol--emas">
                        <x-panel.ikon nama="profil" />
                        <span>Lihat Profil</span>
                    </a>

                    <a href="{{ route('pengaturan-akun.index') }}" class="pr-tombol">
                        <x-panel.ikon nama="pengaturan" />
                        <span>Pengaturan Akun</span>
                    </a>
                </div>
            </div>
        </section>

        <div class="pr-grid">
            <section class="pr-card" id="dataPribadi">
                <div class="pr-toolbar">
                    <div>
                        <h2>Data Pribadi</h2>
                        <p class="pr-sub" style="margin: 6px 0 0;">
                            Nama dan biodata pribadi berikut dapat Anda perbarui sendiri.
                        </p>
                    </div>

                    <button type="button"
                        class="pr-btn pr-btn--utama pr-ubah-aksi"
                        data-pr-ubah
                        @if ($modeUbah) hidden @endif>
                        Edit
                    </button>
                </div>

                <div class="pr-baca" data-pr-baca @if ($modeUbah) hidden @endif>
                    <table class="pr-tabel">
                        <tbody>
                            <tr>
                                <th scope="row">Nama Lengkap</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Jabatan</th>
                                <td>{{ $jabatan }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Email</th>
                                <td>{{ $user->email ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Nomor WhatsApp</th>
                                <td>{{ $user->no_hp ?: 'Belum diisi' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Jenis Kelamin</th>
                                <td>{{ $labelJenisKelamin }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Pendidikan Terakhir</th>
                                <td>{{ $pegawai?->pendidikan_terakhir ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Alamat</th>
                                <td>{{ $pegawai?->alamat ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    @if (! $pegawai)
                        <p class="pr-catatan">
                            Akun ini belum tertaut dengan Data Pegawai sehingga biodata pribadi
                            belum tersedia. Penautan dilakukan Admin melalui menu Manajemen Pengguna.
                        </p>
                    @endif

                    <p class="pr-catatan">
                        Email tidak diubah dari halaman ini. Perubahan email dilakukan melalui
                        <a href="{{ route('pengaturan-akun.index') }}">Pengaturan Akun</a> dengan verifikasi kata sandi.
                    </p>
                </div>

                <form class="pr-form pr-ubah" action="{{ route('data.pengguna.update') }}" method="POST"
                    data-pr-ubah-form @if (! $modeUbah) hidden @endif>
                    @csrf
                    @method('PUT')

                    <div class="pr-field">
                        <label for="dataNama">Nama Lengkap</label>
                        <input type="text" id="dataNama" name="name" value="{{ old('name', $user->name) }}"
                            maxlength="255" required autocomplete="name">
                    </div>

                    <div class="pr-field">
                        <label for="dataNoHp">Nomor WhatsApp</label>
                        <input type="text" id="dataNoHp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                            maxlength="20" inputmode="tel" placeholder="Contoh: 081234567890" autocomplete="tel">
                    </div>

                    <div class="pr-field">
                        <label for="dataJenisKelamin">Jenis Kelamin</label>
                        <select id="dataJenisKelamin" name="jenis_kelamin" @disabled(! $pegawai)>
                            <option value="">Belum diisi</option>
                            <option value="L" @selected($jenisKelamin === 'L')>Laki-laki</option>
                            <option value="P" @selected($jenisKelamin === 'P')>Perempuan</option>
                        </select>
                    </div>

                    <div class="pr-field">
                        <label for="dataPendidikan">Pendidikan Terakhir</label>
                        <input type="text" id="dataPendidikan" name="pendidikan_terakhir"
                            value="{{ old('pendidikan_terakhir', $pegawai?->pendidikan_terakhir) }}"
                            maxlength="100" placeholder="Contoh: S1 / S2" @disabled(! $pegawai)>
                    </div>

                    <div class="pr-field">
                        <label for="dataAlamat">Alamat</label>
                        <textarea id="dataAlamat" name="alamat" maxlength="255"
                            placeholder="Alamat tempat tinggal saat ini" @disabled(! $pegawai)>{{ old('alamat', $pegawai?->alamat) }}</textarea>
                    </div>

                    <div class="pr-aksi">
                        <button type="button" class="pr-btn pr-btn--garis" data-pr-batal>Batal</button>
                        <button type="submit" class="pr-btn pr-btn--utama">Simpan</button>
                    </div>

                    @if (! $pegawai)
                        <p class="pr-catatan">
                            Biodata kepegawaian baru dapat diisi setelah akun Anda tertaut dengan Data Pegawai.
                        </p>
                    @endif
                </form>
            </section>

            <div class="pr-kolom">
                <section class="pr-card">
                    <h2>Data Akun</h2>
                    <p class="pr-sub">Data akun yang dipakai untuk masuk ke SIMAHAD.</p>

                    <table class="pr-tabel">
                        <tbody>
                            <tr>
                                <th scope="row">Peran</th>
                                <td>{{ $labelPeran }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Status Akun</th>
                                <td>
                                    <span class="pr-badge {{ $kelasStatus }}">
                                        {{ $user->status ? ucfirst(strtolower($user->status)) : '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Email</th>
                                <td>{{ $user->email ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Email Terverifikasi</th>
                                <td>{{ $user->email_verified_at ? 'Ya' : 'Belum' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Kata Sandi</th>
                                <td>{{ $user->sudahPunyaKataSandi() ? 'Sudah dibuat sendiri' : 'Belum dibuat' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Terdaftar Sejak</th>
                                <td>{{ $user->created_at?->translatedFormat('d F Y') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Login Terakhir</th>
                                <td>
                                    {{ $user->last_login_at
                                        ? $user->last_login_at->translatedFormat('d F Y, H:i').' WIB'
                                        : 'Belum tercatat' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <section class="pr-card">
                    <h2>Ringkasan Kontribusi</h2>
                    <p class="pr-sub">Data yang tercatat atas nama akun ini.</p>

                    <dl class="pr-daftar">
                        @if (! is_null($jumlahTugas))
                            <div>
                                <dt>Tugas Dibuat</dt>
                                <dd>{{ $jumlahTugas }} tugas</dd>
                            </div>
                        @endif

                        @if (! is_null($jumlahPengumuman))
                            <div>
                                <dt>Pengumuman Ditulis</dt>
                                <dd>{{ $jumlahPengumuman }} pengumuman</dd>
                            </div>
                        @endif

                        <div>
                            <dt>Jabatan</dt>
                            <dd>{{ $jabatan }}</dd>
                        </div>
                        <div>
                            <dt>Status Kepegawaian</dt>
                            <dd>{{ $pegawai ? ($pegawai->status_pegawai ? 'Aktif' : 'Tidak Aktif') : '-' }}</dd>
                        </div>
                    </dl>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bagian = document.getElementById('dataPribadi');

            if (!bagian) {
                return;
            }

            const modeBaca = bagian.querySelector('[data-pr-baca]');
            const modeUbah = bagian.querySelector('[data-pr-ubah-form]');
            const tombolEdit = bagian.querySelector('[data-pr-ubah]');
            const tombolBatal = bagian.querySelector('[data-pr-batal]');

            function tampilkanUbah(aktif) {
                modeBaca.hidden = aktif;
                modeUbah.hidden = !aktif;
                tombolEdit.hidden = aktif;

                if (aktif) {
                    const pertama = modeUbah.querySelector('input, select, textarea');

                    if (pertama) {
                        pertama.focus();
                    }
                }
            }

            tombolEdit.addEventListener('click', function () {
                tampilkanUbah(true);
            });

            tombolBatal.addEventListener('click', function () {
                // Batal hanya menutup form tanpa menyimpan perubahan.
                modeUbah.reset();
                tampilkanUbah(false);
            });
        });
    </script>
@endsection
