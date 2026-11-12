@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
    @include('partials.dashboard-gaya')

    <div class="dash">

        <header class="dash-kepala">
            <div class="dash-sapaan">
                <h1>Assalamu’alaikum, {{ $guru?->name ?? 'Ustazah' }}</h1>
                <p>Ringkasan penugasan, absensi, dan tugas yang menjadi tanggung jawab Anda.</p>
                <span class="dash-peran">Guru</span>
            </div>

            <span class="dash-tanggal">
                <x-panel.ikon nama="jadwal" />
                {{ now()->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </header>

        {{-- Ringkasan angka --}}
        <div class="dash-grid">
            <x-dash.statistik
                label="Mata Pelajaran"
                :nilai="$ringkasan['jumlahMapel']"
                ikon="pembelajaran"
                :ket="$ringkasan['jumlahKelompok'].' kelompok diajar'"
                :tautan="route('guru.jadwal-saya')" />

            <x-dash.statistik
                label="Jadwal Mengajar"
                :nilai="$ringkasan['jumlahJadwal']"
                ikon="jadwal"
                :ket="$ringkasan['jadwalHariIni']->count().' jadwal hari ini'"
                :tautan="route('guru.jadwal-saya')" />

            <x-dash.statistik
                label="Absensi Hari Ini"
                :nilai="$ringkasan['absensiHariIni']"
                ikon="absensi"
                :nada="$ringkasan['absensiBelumDiisi'] > 0 ? 'bahaya' : 'sukses'"
                :ket="$ringkasan['absensiBelumDiisi'].' jadwal belum diisi'"
                :tautan="route('guru.absensi')" />

            <x-dash.statistik
                label="Tugas Aktif"
                :nilai="$ringkasan['tugasAktif']"
                ikon="tugas"
                :ket="$ringkasan['tugasDraft'].' tugas masih draft'"
                :tautan="route('guru.tugas.index')" />

            <x-dash.statistik
                label="Pengumpulan Masuk"
                :nilai="$ringkasan['jumlahPengumpulan']"
                ikon="pendaftaran"
                ket="Dari seluruh tugas Anda"
                :tautan="route('guru.tugas.index')" />

            <x-dash.statistik
                label="Belum Dinilai"
                :nilai="$ringkasan['belumDinilai']"
                ikon="laporan"
                :nada="$ringkasan['belumDinilai'] > 0 ? 'emas' : 'sukses'"
                ket="Pengumpulan menunggu penilaian"
                :tautan="route('guru.tugas.index')" />
        </div>

        {{-- Aksi cepat --}}
        <div class="dash-aksi">
            <a href="{{ route('guru.tugas.create') }}">
                <x-panel.ikon nama="tugas" />
                <span>Buat Tugas</span>
            </a>

            <a href="{{ route('guru.tugas.index') }}">
                <x-panel.ikon nama="pendaftaran" />
                <span>Periksa Pengumpulan</span>
                @if ($ringkasan['belumDinilai'] > 0)
                    <span class="dash-aksi-badge">{{ $ringkasan['belumDinilai'] }}</span>
                @endif
            </a>

            <a href="{{ route('guru.absensi') }}">
                <x-panel.ikon nama="absensi" />
                <span>Isi Absensi</span>
                @if ($ringkasan['absensiBelumDiisi'] > 0)
                    <span class="dash-aksi-badge">{{ $ringkasan['absensiBelumDiisi'] }}</span>
                @endif
            </a>

            <a href="{{ route('guru.jadwal-saya') }}">
                <x-panel.ikon nama="jadwal" />
                <span>Lihat Jadwal</span>
            </a>

            <a href="{{ route('guru.kegiatan-dokumentasi') }}">
                <x-panel.ikon nama="kegiatan" />
                <span>Kegiatan &amp; Dokumentasi</span>
            </a>
        </div>

        <div class="dash-kolom">

            {{-- Jadwal hari ini --}}
            <x-dash.panel judul="Jadwal Mengajar Hari Ini" ikon="jadwal"
                :tautan="route('guru.jadwal-saya')" label-tautan="Jadwal Saya" rapat>
                @forelse ($ringkasan['jadwalHariIni'] as $jadwal)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="jadwal" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $jadwal->namaMataPelajaran() }}</span>
                            <span class="dash-baris-ket">
                                {{ $jadwal->kelompok ?: '-' }} · {{ $jadwal->ruangan ?: 'Ruang Kelas' }}
                            </span>
                        </span>
                        <span class="dash-baris-akhir">{{ $jadwal->jamTeks() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Tidak ada jadwal mengajar hari ini."
                        petunjuk="Jadwal ditetapkan oleh Musyrifah pada menu Manajemen Pembelajaran." />
                @endforelse
            </x-dash.panel>

            {{-- Kelompok & mapel --}}
            <x-dash.panel judul="Kelompok & Mata Pelajaran" ikon="pembelajaran">
                <p class="dash-catatan" style="margin-top: 0;">Kelompok yang Anda ajar</p>

                @forelse ($ringkasan['kelompok'] as $kelompok)
                    <span class="dash-pil" style="margin: 0 4px 6px 0;">{{ $kelompok }}</span>
                @empty
                    <x-dash.kosong pesan="Belum ada kelompok yang ditugaskan."
                        petunjuk="Hubungi Musyrifah bila penugasan belum tersedia." />
                @endforelse

                <p class="dash-catatan" style="margin-top: 8px;">Mata pelajaran</p>

                @forelse ($ringkasan['mataPelajaran'] as $mapel)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="pembelajaran" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $mapel }}</span>
                        </span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada mata pelajaran yang ditugaskan." />
                @endforelse
            </x-dash.panel>

            {{-- Tugas terbaru --}}
            <x-dash.panel judul="Tugas Terbaru" ikon="tugas"
                :tautan="route('guru.tugas.index')" label-tautan="Semua Tugas" rapat>
                @forelse ($ringkasan['tugasDaftar'] as $tugas)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="tugas" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $tugas->judul }}</span>
                            <span class="dash-baris-ket">
                                {{ $tugas->kelompok ?: '-' }} · {{ $tugas->pengumpulan_count }} pengumpulan
                                @if ($tugas->batas_pengumpulan)
                                    · batas {{ $tugas->batas_pengumpulan->locale('id')->translatedFormat('d M Y H:i') }}
                                @endif
                            </span>
                        </span>
                        <span class="dash-baris-akhir">
                            <span class="dash-pil"
                                data-nada="{{ $tugas->status === \App\Models\Tugas::STATUS_DIPUBLIKASIKAN ? 'sukses' : 'emas' }}">
                                {{ $tugas->status === \App\Models\Tugas::STATUS_DIPUBLIKASIKAN ? 'Aktif' : 'Draft' }}
                            </span>
                        </span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada tugas yang dibuat."
                        petunjuk="Gunakan tombol Buat Tugas untuk memulai." />
                @endforelse
            </x-dash.panel>

            {{-- Aktivitas --}}
            <x-dash.panel judul="Aktivitas Terbaru" ikon="log" rapat>
                @forelse ($ringkasan['aktivitas'] as $aktivitas)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="log" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $aktivitas['judul'] }}</span>
                            <span class="dash-baris-ket">{{ $aktivitas['keterangan'] }}</span>
                        </span>
                        <span class="dash-baris-akhir">{{ $aktivitas['waktu']?->locale('id')->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada aktivitas terbaru."
                        petunjuk="Aktivitas tugas dan pengumpulan akan tampil di sini." />
                @endforelse
            </x-dash.panel>

            {{-- Pengumuman --}}
            <x-dash.panel judul="Pengumuman Ma’had" ikon="pengumuman" rapat>
                @forelse ($ringkasan['pengumuman'] as $pengumuman)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="pengumuman" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $pengumuman->judul }}</span>
                            <span class="dash-baris-ket">{{ $pengumuman->ringkasan(80) }}</span>
                        </span>
                        <span class="dash-baris-akhir">{{ $pengumuman->tanggalTeks() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada pengumuman yang dipublikasikan." />
                @endforelse
            </x-dash.panel>

        </div>
    </div>
@endsection
