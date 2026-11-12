@extends('layouts.panel')

@section('title', 'Profil '.$labelPeran)

@section('konten')
    @include('profil.partials.gaya')

    {{-- Halaman Lihat Profil hanya menampilkan identitas singkat pemilik akun.
         Perubahan data pribadi tetap dilakukan di Data Saya dan perubahan
         kata sandi/email di Pengaturan Akun agar ketiga halaman tidak tumpang tindih. --}}
    <div class="pr-page">
        @if (session('success'))
            <div class="pr-alert pr-alert--sukses" role="status">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="pr-alert pr-alert--gagal" role="alert">
                <strong>Foto profil gagal diperbarui.</strong>
                <ul>
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="pr-profil" aria-labelledby="judulProfil">
            <div class="pr-profil-foto">
                <x-foto-profil
                    :pengguna="$user"
                    :aksi-simpan="'profil.pengguna.foto.update'"
                    :aksi-hapus="'profil.pengguna.foto.destroy'"
                    :ukuran="148"
                />
            </div>

            <span class="pr-profil-eyebrow">Profil {{ $labelPeran }} SIMAHAD</span>

            <h1 id="judulProfil">{{ $user->name }}</h1>

            <p class="pr-profil-jabatan">Jabatan: {{ $jabatan }}</p>

            <h2 class="pr-profil-judul">Informasi Pribadi</h2>

            <dl class="pr-identitas">
                <div>
                    <dt>
                        <span class="pr-identitas-ikon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21v-1.5A6.5 6.5 0 0 1 10.5 13h3A6.5 6.5 0 0 1 20 19.5V21" />
                            </svg>
                        </span>
                        Nama Lengkap
                    </dt>
                    <dd>{{ $user->name }}</dd>
                </div>

                <div>
                    <dt>
                        <span class="pr-identitas-ikon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="16" rx="2" />
                                <circle cx="9" cy="10" r="2.2" />
                                <path d="M6 16c0-1.9 6-1.9 6 0M15 9h3M15 13h3" />
                            </svg>
                        </span>
                        Jabatan / Peran
                    </dt>
                    <dd>{{ $jabatan }}</dd>
                </div>

                <div>
                    <dt>
                        <span class="pr-identitas-ikon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                                <path d="m3 7 9 6 9-6" />
                            </svg>
                        </span>
                        Email
                    </dt>
                    <dd>{{ $user->email ?: 'Email belum tersedia' }}</dd>
                </div>

                <div>
                    <dt>
                        <span class="pr-identitas-ikon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6.6 3h3l1.5 4-2 1.4a12 12 0 0 0 5.5 5.5l1.4-2 4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.6 5.2 2 2 0 0 1 6.6 3z" />
                            </svg>
                        </span>
                        Nomor Telepon
                    </dt>
                    <dd>{{ $user->no_hp ?: 'Nomor telepon belum diisi' }}</dd>
                </div>
            </dl>

            <div class="pr-profil-aksi">
                <a href="{{ route('data.pengguna') }}" class="pr-btn pr-btn--utama">
                    <x-panel.ikon nama="data" />
                    <span>Data Saya</span>
                </a>

                <a href="{{ route('pengaturan-akun.index') }}" class="pr-btn pr-btn--garis">
                    <x-panel.ikon nama="pengaturan" />
                    <span>Pengaturan Akun</span>
                </a>
            </div>

            <p class="pr-profil-catatan">
                Data pribadi diubah melalui <a href="{{ route('data.pengguna') }}">Data Saya</a>,
                sedangkan kata sandi dan email diubah melalui
                <a href="{{ route('pengaturan-akun.index') }}">Pengaturan Akun</a>.
            </p>
        </section>
    </div>
@endsection
