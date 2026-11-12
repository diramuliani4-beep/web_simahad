@extends('layouts.musyrifah')

@section('title', 'Dashboard Musyrifah')

@section('content')
    @include('partials.dashboard-gaya')

    @php
        $kamar = $ringkasan['kamar'];
        $pembayaran = $ringkasan['pembayaran'];
        $pendaftaran = $ringkasan['pendaftaran'];
        $pengajuan = $ringkasan['pengajuanKamar'];
        $perizinan = $ringkasan['suratPerizinan'];
        $pembelajaran = $ringkasan['pembelajaran'];
        $dokumentasi = $ringkasan['dokumentasi'];
    @endphp

    <div class="dash">

        <header class="dash-kepala">
            <div class="dash-sapaan">
                <h1>Assalamu’alaikum, {{ auth()->user()->name ?? 'Musyrifah' }}</h1>
                <p>Ringkasan pekerjaan operasional Ma’had yang perlu ditindaklanjuti.</p>
                <span class="dash-peran">Musyrifah</span>
            </div>

            <span class="dash-tanggal">
                <x-panel.ikon nama="jadwal" />
                {{ now()->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </header>

        {{-- Ringkasan angka --}}
        <div class="dash-grid">
            <x-dash.statistik
                label="Total Mahasiswa"
                :nilai="$ringkasan['totalMahasiswa']"
                ikon="mahasiswa"
                ket="Akun mahasiswa terdaftar" />

            <x-dash.statistik
                label="Pendaftaran Menunggu"
                :nilai="$pendaftaran['menunggu']"
                ikon="pendaftaran"
                :nada="$pendaftaran['menunggu'] > 0 ? 'bahaya' : null"
                :ket="$pendaftaran['disetujui'].' pendaftaran disetujui'"
                :tautan="route('musyrifah.pendaftaran')" />

            <x-dash.statistik
                label="Keterisian Kamar"
                :nilai="$kamar['terisi'].' / '.$kamar['kapasitas']"
                ikon="kamar"
                :ket="$kamar['persentase'].'% terisi · sisa '.$kamar['sisa'].' tempat'"
                :tautan="route('musyrifah.data_kamar')" />

            <x-dash.statistik
                label="Pengajuan Kamar"
                :nilai="$pengajuan['menunggu']"
                ikon="penghuni"
                :nada="$pengajuan['menunggu'] > 0 ? 'emas' : null"
                ket="Menunggu verifikasi"
                :tautan="route('musyrifah.data_kamar')" />

            <x-dash.statistik
                label="Perizinan Menunggu"
                :nilai="$perizinan['menunggu']"
                ikon="surat"
                :nada="$perizinan['perluPerbaikan'] > 0 ? 'bahaya' : null"
                :ket="$perizinan['perluPerbaikan'].' perlu perbaikan'"
                :tautan="route('musyrifah.surat_perizinan')" />

            <x-dash.statistik
                label="Surat Semester"
                :nilai="$perizinan['semesterMenunggu']"
                ikon="laporan"
                ket="Menunggu verifikasi surat"
                :tautan="route('musyrifah.surat_perizinan')" />

            <x-dash.statistik
                label="Absensi Belum Diisi"
                :nilai="$pembelajaran['absensiBelumDiisi']"
                ikon="absensi"
                :nada="$pembelajaran['absensiBelumDiisi'] > 0 ? 'bahaya' : 'sukses'"
                :ket="$pembelajaran['jadwalHariIni'].' jadwal hari '.$pembelajaran['hariIni']"
                :tautan="route('musyrifah.manajemen_pembelajaran')" />
        </div>

        {{-- Aksi cepat --}}
        <div class="dash-aksi">
            <a href="{{ route('musyrifah.pendaftaran') }}">
                <x-panel.ikon nama="pendaftaran" />
                <span>Verifikasi Pendaftaran</span>
                @if ($pendaftaran['menunggu'] > 0)
                    <span class="dash-aksi-badge">{{ $pendaftaran['menunggu'] }}</span>
                @endif
            </a>

            <a href="{{ route('musyrifah.data_kamar') }}">
                <x-panel.ikon nama="kamar" />
                <span>Data Kamar</span>
                @if ($pengajuan['menunggu'] > 0)
                    <span class="dash-aksi-badge">{{ $pengajuan['menunggu'] }}</span>
                @endif
            </a>

            <a href="{{ route('musyrifah.pembayaran') }}">
                <x-panel.ikon nama="pembayaran" />
                <span>Verifikasi Pembayaran</span>
            </a>

            <a href="{{ route('musyrifah.surat_perizinan') }}">
                <x-panel.ikon nama="surat" />
                <span>Surat &amp; Perizinan</span>
            </a>

            <a href="{{ route('musyrifah.manajemen_pembelajaran') }}">
                <x-panel.ikon nama="pembelajaran" />
                <span>Manajemen Pembelajaran</span>
            </a>

            <a href="{{ route('musyrifah.pengumuman') }}">
                <x-panel.ikon nama="pengumuman" />
                <span>Pengumuman</span>
            </a>
        </div>

        <div class="dash-kolom">

            {{-- Pendaftaran --}}
            <x-dash.panel judul="Pendaftaran Menunggu Verifikasi" ikon="pendaftaran"
                :tautan="route('musyrifah.pendaftaran')" label-tautan="Kelola" rapat>
                @forelse ($pendaftaran['terbaru'] as $item)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="mahasiswa" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $item->nama_lengkap ?: 'Pendaftar' }}</span>
                            <span class="dash-baris-ket">
                                {{ $item->nim ? 'NIM '.$item->nim.' · ' : '' }}
                                {{ $item->email_aktif ?: 'Email belum diisi' }} · {{ ucfirst((string) $item->status) }}
                            </span>
                        </span>
                        <span class="dash-baris-akhir">{{ $item->created_at?->locale('id')->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Tidak ada pendaftaran yang menunggu verifikasi."
                        petunjuk="Pendaftaran baru akan tampil di sini." />
                @endforelse
            </x-dash.panel>

            {{-- Kamar --}}
            <x-dash.panel judul="Kapasitas & Keterisian Kamar" ikon="kamar"
                :tautan="route('musyrifah.data_kamar')" label-tautan="Data Kamar">
                <div class="dash-angka-besar">{{ $kamar['persentase'] }}%</div>
                <p class="dash-catatan">
                    {{ $kamar['terisi'] }} dari {{ $kamar['kapasitas'] }} tempat terisi
                    · {{ $kamar['jumlah'] }} kamar
                </p>

                <div class="dash-bilah" role="progressbar" aria-label="Keterisian kamar"
                    aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $kamar['persentase'] }}">
                    <div class="dash-bilah-isi" style="width: {{ min(100, max(0, (float) $kamar['persentase'])) }}%;"></div>
                </div>

                <div class="dash-lantai" style="margin-top: 10px;">
                    @forelse ($kamar['per_lantai'] as $lantai)
                        <div class="dash-lantai-item">
                            <div class="dash-lantai-kepala">
                                <span>Lantai {{ $lantai['lantai'] }}</span>
                                <span>{{ $lantai['terisi'] }} / {{ $lantai['kapasitas'] }}</span>
                            </div>

                            <div class="dash-bilah">
                                <div class="dash-bilah-isi"
                                    style="width: {{ $lantai['kapasitas'] > 0 ? min(100, round(($lantai['terisi'] / $lantai['kapasitas']) * 100, 1)) : 0 }}%;">
                                </div>
                            </div>

                            <p class="dash-catatan">{{ $lantai['jumlah'] }} kamar · sisa {{ $lantai['sisa'] }} tempat</p>
                        </div>
                    @empty
                        <x-dash.kosong pesan="Data kamar belum tersedia."
                            petunjuk="Tambahkan kamar pada menu Data Kamar." />
                    @endforelse
                </div>
            </x-dash.panel>

            {{-- Pembayaran --}}
            <x-dash.panel judul="Pembayaran Periode Aktif" ikon="pembayaran"
                :tautan="route('musyrifah.pembayaran')" label-tautan="Verifikasi">
                @if ($pembayaran['periode'])
                    <div class="dash-angka-besar">{{ $pembayaran['persentase'] }}%</div>
                    <p class="dash-catatan">
                        {{ $pembayaran['tahap'] ?: 'Tahap Pembayaran' }} ·
                        {{ $pembayaran['periode'] }} · {{ $pembayaran['nominalRupiah'] }}
                    </p>

                    <div class="dash-bilah" role="progressbar" aria-label="Mahasiswa sudah lunas"
                        aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $pembayaran['persentase'] }}">
                        <div class="dash-bilah-isi" style="width: {{ min(100, max(0, (float) $pembayaran['persentase'])) }}%;"></div>
                    </div>

                    <table class="dash-tabel" style="margin-top: 10px;">
                        <tbody>
                            <tr>
                                <td>Sudah lunas</td>
                                <td>{{ $pembayaran['lunas'] }} mahasiswa</td>
                            </tr>
                            <tr>
                                <td>Menunggu konfirmasi</td>
                                <td>{{ $pembayaran['menunggu'] }} mahasiswa</td>
                            </tr>
                            <tr>
                                <td>Belum membayar</td>
                                <td>{{ $pembayaran['belum'] }} mahasiswa</td>
                            </tr>
                        </tbody>
                    </table>

                    @if ($pembayaran['pengingat'])
                        <div class="dash-peringatan" style="margin-top: 10px;">
                            <x-panel.ikon nama="pembayaran" />
                            <span>
                                Jatuh tempo {{ $pembayaran['jatuhTempo'] }} —
                                sisa {{ $pembayaran['sisaHari'] }} hari. Segera ingatkan mahasiswa.
                            </span>
                        </div>
                    @elseif ($pembayaran['jatuhTempo'])
                        <p class="dash-catatan">Jatuh tempo {{ $pembayaran['jatuhTempo'] }}</p>
                    @endif
                @else
                    <x-dash.kosong pesan="Belum ada periode pembayaran yang aktif."
                        petunjuk="Aktifkan periode pada menu Pembayaran agar tagihan mahasiswa muncul." />
                @endif
            </x-dash.panel>

            {{-- Pengajuan kamar --}}
            <x-dash.panel judul="Pengajuan Kamar Menunggu" ikon="penghuni"
                :tautan="route('musyrifah.data_kamar')" label-tautan="Tinjau" rapat>
                @forelse ($pengajuan['terbaru'] as $item)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="kamar" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $item->user?->name ?? 'Mahasiswa' }}</span>
                            <span class="dash-baris-ket">
                                {{ $item->labelJenis() }} · {{ $item->kamar?->nama_kamar ?: 'Kamar belum dipilih' }}
                            </span>
                        </span>
                        <span class="dash-baris-akhir">{{ $item->created_at?->locale('id')->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Tidak ada pengajuan kamar yang menunggu."
                        petunjuk="Pengajuan penempatan dan pindah kamar akan tampil di sini." />
                @endforelse
            </x-dash.panel>

            {{-- Perizinan --}}
            <x-dash.panel judul="Surat & Perizinan Menunggu" ikon="surat"
                :tautan="route('musyrifah.surat_perizinan')" label-tautan="Verifikasi" rapat>
                @forelse ($perizinan['terbaru'] as $item)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="surat" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $item->user?->name ?? 'Mahasiswa' }}</span>
                            <span class="dash-baris-ket">{{ $item->labelJenis() }}</span>
                        </span>
                        <span class="dash-baris-akhir">
                            <span class="dash-pil"
                                data-nada="{{ $item->status === \App\Models\SuratPerizinan::STATUS_PERLU_PERBAIKAN ? 'bahaya' : 'emas' }}">
                                {{ $item->labelStatus() }}
                            </span>
                        </span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Tidak ada surat yang menunggu verifikasi."
                        petunjuk="Pengajuan izin mahasiswa akan tampil di sini." />
                @endforelse
            </x-dash.panel>

            {{-- Jadwal hari ini --}}
            <x-dash.panel judul="Jadwal Pembelajaran Hari Ini" ikon="jadwal"
                :tautan="route('musyrifah.manajemen_pembelajaran')" label-tautan="Kelola">
                <p class="dash-catatan" style="margin-top: 0;">
                    {{ $pembelajaran['hariIni'] }} · {{ $pembelajaran['jadwalHariIni'] }} jadwal
                    · {{ $pembelajaran['kelompok'] }} kelompok belajar
                </p>

                @forelse ($pembelajaran['jadwal'] as $jadwal)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="jadwal" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $jadwal->namaMataPelajaran() }}</span>
                            <span class="dash-baris-ket">
                                {{ $jadwal->kelompok ?: '-' }} · {{ $jadwal->guru_pengampu ?: 'Guru belum ditetapkan' }}
                            </span>
                        </span>
                        <span class="dash-baris-akhir">{{ $jadwal->jamTeks() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Tidak ada jadwal pembelajaran hari ini."
                        petunjuk="Jadwal dapat ditambahkan pada menu Manajemen Pembelajaran." />
                @endforelse
            </x-dash.panel>

            {{-- Pengumuman --}}
            <x-dash.panel judul="Pengumuman Terbaru" ikon="pengumuman"
                :tautan="route('musyrifah.pengumuman')" label-tautan="Kelola" rapat>
                @forelse ($ringkasan['pengumuman'] as $pengumuman)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="pengumuman" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $pengumuman->judul }}</span>
                            <span class="dash-baris-ket">{{ $pengumuman->kategori ?: 'Informasi Umum' }}</span>
                        </span>
                        <span class="dash-baris-akhir">{{ $pengumuman->tanggalTeks() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada pengumuman yang dipublikasikan."
                        petunjuk="Publikasikan pengumuman agar tampil untuk seluruh pengguna." />
                @endforelse
            </x-dash.panel>

            {{-- Dokumentasi kegiatan --}}
            <x-dash.panel judul="Dokumentasi Kegiatan" ikon="kegiatan"
                :tautan="route('musyrifah.pengumuman')" label-tautan="Lihat" rapat>
                <p class="dash-catatan" style="margin: 0 0 4px;">
                    {{ $dokumentasi['jumlah'] }} dokumentasi kegiatan terpublikasi
                </p>

                @forelse ($dokumentasi['terbaru'] as $item)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="kegiatan" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $item->judul }}</span>
                            <span class="dash-baris-ket">{{ $item->ringkasan(70) }}</span>
                        </span>
                        <span class="dash-baris-akhir">{{ $item->tanggalTeks() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada dokumentasi kegiatan."
                        petunjuk="Unggah dokumentasi melalui menu Pengumuman dengan kategori Kegiatan Ma’had." />
                @endforelse
            </x-dash.panel>

            {{-- Aktivitas --}}
            <x-dash.panel judul="Aktivitas Terbaru" ikon="log"
                :tautan="route('musyrifah.pengumuman')" label-tautan="Pengumuman" rapat>
                @forelse ($ringkasan['aktivitas'] as $aktivitas)
                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="log" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">{{ $aktivitas->description }}</span>
                            <span class="dash-baris-ket">{{ $aktivitas->user_name ?: 'Pengguna' }}</span>
                        </span>
                        <span class="dash-baris-akhir">{{ $aktivitas->created_at?->locale('id')->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Belum ada aktivitas yang tercatat."
                        petunjuk="Aktivitas pengguna akan tercatat otomatis oleh sistem." />
                @endforelse
            </x-dash.panel>

        </div>
    </div>
@endsection
