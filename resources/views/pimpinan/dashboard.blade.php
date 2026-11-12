@extends('layouts.pimpinan')

@section('title', 'Dashboard Pimpinan')

@section('content')
    @include('partials.dashboard-gaya')

    @php
        $pendaftaran = $ringkasan['pendaftaran'];
        $kamar = $ringkasan['kamar'];
        $pembayaran = $ringkasan['pembayaran'];
        $pembelajaran = $ringkasan['pembelajaran'];
        $absensi = $ringkasan['absensi'];
        $perizinan = $ringkasan['perizinan'];
        $grafik = collect($pendaftaran['per_bulan']);
        $nilaiGrafikMaks = max(1, (int) $grafik->max('jumlah'));
    @endphp

    <div class="dash">

        <header class="dash-kepala">
            <div class="dash-sapaan">
                <h1>Assalamu’alaikum, {{ auth()->user()->name ?? 'Pimpinan' }}</h1>
                <p>Ringkasan monitoring dan evaluasi penyelenggaraan Ma’had.</p>
                <span class="dash-peran">Pimpinan</span>
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
                ket="Akun mahasiswa terdaftar"
                :tautan="route('pimpinan.monitoring.mahasiswa')" />

            <x-dash.statistik
                label="Keterisian Kamar"
                :nilai="$kamar['persentase'].'%'"
                ikon="kamar"
                :ket="$kamar['terisi'].' dari '.$kamar['kapasitas'].' tempat terisi'"
                :tautan="route('pimpinan.monitoring.kamar')" />

            <x-dash.statistik
                label="Total Pendaftaran"
                :nilai="$pendaftaran['total']"
                ikon="pendaftaran"
                :ket="$pendaftaran['menunggu'].' menunggu · '.$pendaftaran['disetujui'].' disetujui'"
                :tautan="route('pimpinan.laporan-pendaftaran')" />

            <x-dash.statistik
                label="Kelompok Belajar"
                :nilai="$pembelajaran['kelompok']"
                ikon="pembelajaran"
                :ket="$pembelajaran['mataPelajaran'].' mata pelajaran · '.$pembelajaran['jadwal'].' jadwal'"
                :tautan="route('pimpinan.monitoring.pembelajaran')" />

            <x-dash.statistik
                label="Kehadiran Pembelajaran"
                :nilai="$absensi['persentase'].'%'"
                ikon="absensi"
                :nada="$absensi['persentase'] >= 80 ? 'sukses' : ($absensi['total'] > 0 ? 'bahaya' : null)"
                :ket="$absensi['hadir'].' hadir dari '.$absensi['total'].' catatan absensi'"
                :tautan="route('pimpinan.monitoring.pembelajaran')" />

            <x-dash.statistik
                label="Penyelesaian Tugas"
                :nilai="$pembelajaran['penyelesaian'].'%'"
                ikon="tugas"
                :ket="$pembelajaran['tugasDinilai'].' dari '.$pembelajaran['tugas'].' tugas sudah dinilai'"
                :tautan="route('pimpinan.monitoring.pembelajaran')" />

            <x-dash.statistik
                label="Perizinan Diproses"
                :nilai="$perizinan['total']"
                ikon="surat"
                :nada="$perizinan['menunggu'] > 0 ? 'emas' : null"
                :ket="$perizinan['menunggu'].' menunggu · '.$perizinan['selesai'].' selesai'"
                :tautan="route('pimpinan.monitoring.perizinan')" />
        </div>

        <div class="dash-kolom">

            {{-- Grafik pendaftaran --}}
            <x-dash.panel judul="Perkembangan Pendaftaran (6 Bulan)" ikon="laporan">
                @if ($grafik->sum('jumlah') > 0)
                    <div class="dash-grafik" role="img"
                        aria-label="Grafik pendaftaran enam bulan terakhir">
                        @foreach ($grafik as $titik)
                            <div class="dash-grafik-kolom">
                                <span class="dash-grafik-nilai">{{ $titik['jumlah'] }}</span>
                                <div class="dash-grafik-batang"
                                    style="height: {{ max(4, (int) round(($titik['jumlah'] / $nilaiGrafikMaks) * 82)) }}%;">
                                </div>
                                <span class="dash-grafik-label">{{ $titik['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-dash.kosong pesan="Belum ada data pendaftaran dalam enam bulan terakhir."
                        petunjuk="Grafik akan muncul setelah pendaftaran tercatat." />
                @endif
            </x-dash.panel>

            {{-- Pembayaran --}}
            <x-dash.panel judul="Ringkasan Pembayaran" ikon="pembayaran"
                :tautan="route('pimpinan.monitoring.pembayaran')" label-tautan="Monitoring">
                @if ($pembayaran['periode'])
                    <div class="dash-angka-besar">{{ $pembayaran['persentase'] }}%</div>
                    <p class="dash-catatan">
                        {{ $pembayaran['tahap'] ?: 'Tahap Pembayaran' }} ·
                        {{ $pembayaran['periode'] }} · {{ $pembayaran['nominalRupiah'] }}
                    </p>

                    <div class="dash-bilah" role="progressbar" aria-label="Persentase pembayaran lunas"
                        aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $pembayaran['persentase'] }}">
                        <div class="dash-bilah-isi"
                            style="width: {{ min(100, max(0, (float) $pembayaran['persentase'])) }}%;"></div>
                    </div>

                    <table class="dash-tabel" style="margin-top: 10px;">
                        <tbody>
                            <tr>
                                <td>Lunas (terkonfirmasi Rektorat)</td>
                                <td>{{ $pembayaran['lunas'] }}</td>
                            </tr>
                            <tr>
                                <td>Menunggu konfirmasi</td>
                                <td>{{ $pembayaran['menunggu'] }}</td>
                            </tr>
                            <tr>
                                <td>Belum membayar</td>
                                <td>{{ $pembayaran['belum'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <x-dash.kosong pesan="Belum ada periode pembayaran aktif."
                        petunjuk="Ringkasan akan tampil setelah periode pembayaran dibuka." />
                @endif
            </x-dash.panel>

            {{-- Laporan gabungan tiga lantai --}}
            <x-dash.panel judul="Laporan Gabungan per Lantai" ikon="penghuni"
                :tautan="route('pimpinan.monitoring.kamar')" label-tautan="Monitoring">
                @forelse ($kamar['per_lantai'] as $lantai)
                    @php
                        $persenLantai = $lantai['kapasitas'] > 0
                            ? round(($lantai['terisi'] / $lantai['kapasitas']) * 100, 1)
                            : 0;
                    @endphp

                    <div class="dash-baris">
                        <span class="dash-baris-ikon"><x-panel.ikon nama="kamar" /></span>
                        <span class="dash-baris-isi">
                            <span class="dash-baris-utama">Lantai {{ $lantai['lantai'] }}</span>
                            <div class="dash-bilah" style="margin-top: 5px;">
                                <div class="dash-bilah-isi" style="width: {{ min(100, max(0, $persenLantai)) }}%;"></div>
                            </div>
                            <span class="dash-baris-ket">
                                {{ $lantai['jumlah'] }} kamar · {{ $persenLantai }}% terisi · sisa {{ $lantai['sisa'] }} tempat
                            </span>
                        </span>
                        <span class="dash-baris-akhir">{{ $lantai['terisi'] }} / {{ $lantai['kapasitas'] }}</span>
                    </div>
                @empty
                    <x-dash.kosong pesan="Data kamar belum tersedia."
                        petunjuk="Laporan per lantai muncul setelah data kamar diisi Musyrifah." />
                @endforelse
            </x-dash.panel>

            {{-- Perizinan --}}
            <x-dash.panel judul="Rekap Perizinan" ikon="surat"
                :tautan="route('pimpinan.monitoring.perizinan')" label-tautan="Monitoring">
                <table class="dash-tabel">
                    <tbody>
                        <tr>
                            <td>Total pengajuan</td>
                            <td>{{ $perizinan['total'] }}</td>
                        </tr>
                        <tr>
                            <td>Menunggu keputusan</td>
                            <td>{{ $perizinan['menunggu'] }}</td>
                        </tr>
                        <tr>
                            <td>Disetujui</td>
                            <td>{{ $perizinan['disetujui'] }}</td>
                        </tr>
                        <tr>
                            <td>Selesai</td>
                            <td>{{ $perizinan['selesai'] }}</td>
                        </tr>
                    </tbody>
                </table>

                <p class="dash-catatan">
                    Pimpinan berfokus pada monitoring dan evaluasi; keputusan operasional tetap dijalankan Musyrifah.
                </p>
            </x-dash.panel>

            {{-- Aktivitas --}}
            <x-dash.panel judul="Aktivitas Terbaru" ikon="log" rapat>
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
                        petunjuk="Aktivitas sistem akan tampil di sini." />
                @endforelse
            </x-dash.panel>

            {{-- Aksi monitoring dan laporan --}}
            <x-dash.panel judul="Akses Monitoring & Laporan" ikon="laporan">
                <div class="dash-aksi">
                    <a href="{{ route('pimpinan.monitoring.mahasiswa') }}">
                        <x-panel.ikon nama="mahasiswa" />
                        <span>Monitoring Mahasiswa</span>
                    </a>

                    <a href="{{ route('pimpinan.monitoring.kamar') }}">
                        <x-panel.ikon nama="kamar" />
                        <span>Monitoring Kamar</span>
                    </a>

                    <a href="{{ route('pimpinan.monitoring.pembayaran') }}">
                        <x-panel.ikon nama="pembayaran" />
                        <span>Monitoring Pembayaran</span>
                    </a>

                    <a href="{{ route('pimpinan.monitoring.pembelajaran') }}">
                        <x-panel.ikon nama="pembelajaran" />
                        <span>Monitoring Pembelajaran</span>
                    </a>

                    <a href="{{ route('pimpinan.monitoring.perizinan') }}">
                        <x-panel.ikon nama="surat" />
                        <span>Monitoring Perizinan</span>
                    </a>

                    <a href="{{ route('pimpinan.laporan-mahad') }}">
                        <x-panel.ikon nama="laporan" />
                        <span>Register Laporan</span>
                    </a>

                    <a href="{{ route('pimpinan.audit') }}">
                        <x-panel.ikon nama="log" />
                        <span>Audit Trail</span>
                    </a>
                </div>
            </x-dash.panel>

        </div>
    </div>
@endsection
