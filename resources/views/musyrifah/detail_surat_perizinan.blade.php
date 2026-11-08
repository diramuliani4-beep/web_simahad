@extends('layouts.musyrifah')

@php
    use App\Models\SuratPerizinan;

    $mahasiswa = $pengajuan->user;
    $bolehDiputus = ! $pengajuan->suratSemester() && $pengajuan->masihMenunggu();
    $bolehDiverifikasi = $pengajuan->suratSemester()
        && $pengajuan->punyaSuratTertanda()
        && ! in_array($pengajuan->status, [SuratPerizinan::STATUS_SELESAI, SuratPerizinan::STATUS_DIBATALKAN], true);
@endphp

@section('title', 'Detail Pengajuan Perizinan')

@section('content')

<style>
    .rincian {
        width: 100%;
        color: #080808;
    }

    .rincian__kepala {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin: 0 20px 22px;
    }

    .rincian__kepala h1 {
        font-size: 28px;
        line-height: 1.35;
        font-weight: 700;
    }

    .rincian__kepala p {
        margin-top: 5px;
        font-size: 14px;
        color: #555;
    }

    .rincian__kepala a {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 16px;
        border: 1px solid #800d22;
        border-radius: 9px;
        background: #fff;
        color: #800d22;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .rincian__kepala a:hover {
        background: #800d22;
        color: #fff;
    }

    .rincian-alert {
        margin: 0 20px 18px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
    }

    .rincian-alert--sukses {
        background: #eaf6ee;
        border: 1px solid #2f7d4f;
        color: #1c5334;
    }

    .rincian-alert--gagal {
        background: #fdeff1;
        border: 1px solid #800d22;
        color: #800d22;
    }

    .rincian-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        margin: 0 20px 20px;
    }

    .rincian-kartu {
        padding: 18px;
        border: 1px solid #d9c2c8;
        border-radius: 14px;
        background: #fff;
    }

    .rincian-kartu--penuh {
        margin: 0 20px 20px;
    }

    .rincian-kartu h2 {
        margin-bottom: 12px;
        font-size: 17px;
        font-weight: 700;
        color: #800d22;
    }

    .rincian-tabel {
        width: 100%;
        border-collapse: collapse;
    }

    .rincian-tabel th,
    .rincian-tabel td {
        padding: 7px 0;
        font-size: 13.5px;
        text-align: left;
        vertical-align: top;
    }

    .rincian-tabel th {
        width: 42%;
        font-weight: 600;
        color: #4b5563;
    }

    .rincian-tabel td {
        color: #111;
    }

    .rincian-badge {
        display: inline-block;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .rincian-badge--menunggu { background: #fdf1d8; color: #8a5a00; border: 1px solid #e0b658; }
    .rincian-badge--disetujui { background: #e7f6ec; color: #1e6b3a; border: 1px solid #7fbf95; }
    .rincian-badge--ditolak { background: #fdeaea; color: #a01c2c; border: 1px solid #e2969f; }
    .rincian-badge--perbaikan { background: #fdf1d8; color: #8a4b00; border: 1px solid #e0a558; }
    .rincian-badge--selesai { background: #e8eefb; color: #22457f; border: 1px solid #93a9d6; }
    .rincian-badge--dibatalkan { background: #eef0f3; color: #4b5563; border: 1px solid #b9c0ca; }

    .rincian-berkas {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 6px;
    }

    .rincian-berkas a {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 15px;
        border: 1px solid #800d22;
        border-radius: 9px;
        color: #800d22;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .rincian-berkas a:hover {
        background: #800d22;
        color: #fff;
    }

    .rincian-berkas a.rincian-berkas--emas {
        border-color: #C9A96A;
        background: #fdf6e6;
        color: #6b4a12;
    }

    .rincian-kuota {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .rincian-kuota span {
        padding: 8px 14px;
        border: 1px solid #C9A96A;
        border-radius: 10px;
        background: #fdf6e6;
        font-size: 13px;
        font-weight: 600;
        color: #5c4310;
    }

    .rincian-keputusan {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .rincian-opsi {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .rincian-opsi label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border: 1px solid #cdb4ba;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
    }

    .rincian-opsi label:hover {
        border-color: #800d22;
        background: #fdf6f7;
    }

    .rincian-keputusan textarea {
        width: 100%;
        min-height: 92px;
        padding: 11px 13px;
        border: 1px solid #cdb4ba;
        border-radius: 10px;
        font-family: inherit;
        font-size: 13.5px;
        resize: vertical;
    }

    .rincian-keputusan textarea:focus {
        outline: none;
        border-color: #800d22;
        box-shadow: 0 0 0 3px rgba(128, 13, 34, .12);
    }

    .rincian-keputusan label.judul {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .rincian-btn {
        align-self: flex-start;
        padding: 10px 20px;
        border: 1px solid #800d22;
        border-radius: 9px;
        background: #800d22;
        color: #fff;
        font-family: inherit;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
    }

    .rincian-btn:hover {
        background: #590018;
    }

    .rincian-catatan {
        margin-top: 10px;
        padding: 11px 14px;
        border: 1px dashed #C9A96A;
        border-radius: 10px;
        background: #fffdf7;
        font-size: 13px;
        color: #4b5563;
        line-height: 1.6;
    }

    .rincian-riwayat {
        display: flex;
        flex-direction: column;
        gap: 0;
        margin-top: 4px;
    }

    .rincian-riwayat__baris {
        position: relative;
        padding: 0 0 16px 22px;
        border-left: 2px solid #eddde1;
    }

    .rincian-riwayat__baris:last-child {
        padding-bottom: 0;
        border-left-color: transparent;
    }

    .rincian-riwayat__baris::before {
        content: "";
        position: absolute;
        top: 4px;
        left: -7px;
        width: 12px;
        height: 12px;
        border: 2px solid #800d22;
        border-radius: 50%;
        background: #fff;
    }

    .rincian-riwayat__baris strong {
        font-size: 13.5px;
    }

    .rincian-riwayat__baris small {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        color: #6b7280;
    }

    .rincian-riwayat__baris p {
        margin-top: 5px;
        font-size: 13px;
        color: #374151;
        line-height: 1.6;
    }

    .rincian-kosong {
        font-size: 13.5px;
        color: #6b7280;
        line-height: 1.6;
    }

    @media (max-width: 900px) {
        .rincian-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .rincian__kepala,
        .rincian-grid,
        .rincian-kartu--penuh,
        .rincian-alert {
            margin-left: 0;
            margin-right: 0;
        }

        .rincian__kepala h1 {
            font-size: 23px;
        }
    }
</style>

<div class="rincian">

    <section class="rincian__kepala">
        <div>
            <h1>Detail Pengajuan Perizinan</h1>
            <p>{{ $pengajuan->labelJenis() }} &middot; diajukan {{ $pengajuan->created_at?->locale('id')->translatedFormat('d F Y H:i') }} WIB</p>
        </div>
        <a href="{{ route('musyrifah.surat_perizinan') }}">Kembali ke Daftar</a>
    </section>

    @if (session('success'))
        <div class="rincian-alert rincian-alert--sukses">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="rincian-alert rincian-alert--gagal">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="rincian-alert rincian-alert--gagal">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rincian-grid">
        <section class="rincian-kartu">
            <h2>Identitas Mahasiswa</h2>
            <table class="rincian-tabel">
                <tr>
                    <th>Nama</th>
                    <td>{{ $mahasiswa?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>NIM</th>
                    <td>{{ $mahasiswa?->nim ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Kamar Ma'had</th>
                    <td>{{ $mahasiswa?->kamar?->nama_kamar ?: '-' }}</td>
                </tr>
                <tr>
                    <th>No. HP</th>
                    <td>{{ $mahasiswa?->no_hp ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $mahasiswa?->email ?: '-' }}</td>
                </tr>
            </table>
        </section>

        <section class="rincian-kartu">
            <h2>Ringkasan Pengajuan</h2>
            <table class="rincian-tabel">
                <tr>
                    <th>Jenis Izin</th>
                    <td>{{ $pengajuan->labelJenis() }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="rincian-badge rincian-badge--{{ $pengajuan->kelasStatus() }}">
                            {{ $pengajuan->labelStatus() }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Pengajuan</th>
                    <td>{{ $pengajuan->created_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }} WIB</td>
                </tr>
                <tr>
                    <th>Periode Izin</th>
                    <td>{{ $pengajuan->periodeTeks() }}</td>
                </tr>
                @if ($pengajuan->durasi_hari)
                    <tr>
                        <th>Durasi</th>
                        <td>{{ $pengajuan->durasi_hari }} hari</td>
                    </tr>
                @endif
            </table>

            @if ($pengajuan->izinPulang() && $kuotaPulang)
                <div class="rincian-kuota">
                    <span>Kuota {{ $kuotaPulang['bulan'] }}: {{ $kuotaPulang['terpakai'] }} / {{ $kuotaPulang['maksimal'] }}</span>
                    <span>Sisa {{ $kuotaPulang['sisa'] }} slot</span>
                </div>
            @endif
        </section>
    </div>

    <section class="rincian-kartu rincian-kartu--penuh">
        <h2>Rincian Izin</h2>
        <table class="rincian-tabel">
            @if ($pengajuan->izinKegiatan())
                <tr>
                    <th>Nama Kegiatan</th>
                    <td>{{ $pengajuan->nama_kegiatan ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Penyelenggara</th>
                    <td>{{ $pengajuan->penyelenggara ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Lokasi Kegiatan</th>
                    <td>{{ $pengajuan->lokasi ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Kegiatan</th>
                    <td>{{ $pengajuan->periodeTeks() }}</td>
                </tr>
                <tr>
                    <th>Waktu Kegiatan</th>
                    <td>{{ $pengajuan->waktuTeks() }}</td>
                </tr>
            @elseif ($pengajuan->izinPulang())
                <tr>
                    <th>Tanggal Mulai</th>
                    <td>{{ $pengajuan->tanggal_mulai?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Kembali</th>
                    <td>{{ $pengajuan->tanggal_selesai?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Alamat / Tujuan Pulang</th>
                    <td>{{ $pengajuan->lokasi ?: '-' }}</td>
                </tr>
            @else
                <tr>
                    <th>Nomor Surat</th>
                    <td>{{ $pengajuan->nomor_surat ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Surat Bertanda Tangan</th>
                    <td>
                        @if ($pengajuan->punyaSuratTertanda())
                            Sudah diunggah
                            <div style="font-size:12.5px;color:#6b7280;">
                                {{ $pengajuan->surat_diunggah_pada?->locale('id')->translatedFormat('d F Y H:i') }} WIB
                            </div>
                        @else
                            Belum diunggah mahasiswa
                        @endif
                    </td>
                </tr>
            @endif
            <tr>
                <th>Alasan / Keterangan</th>
                <td>{{ $pengajuan->alasan ?: '-' }}</td>
            </tr>
        </table>

        <h2 style="margin-top:18px;">Dokumen</h2>

        @if ($pengajuan->punyaDokumen())
            <div class="rincian-berkas">
                <a href="{{ route('musyrifah.surat_perizinan.dokumen', [$pengajuan, 'pendukung']) }}" target="_blank" rel="noopener">
                    Lihat Surat Pendukung
                </a>
                @if ($pengajuan->dokumen_nama)
                    <span style="align-self:center;font-size:12.5px;color:#6b7280;">{{ $pengajuan->dokumen_nama }}</span>
                @endif
            </div>
        @else
            <p class="rincian-kosong">Tidak ada dokumen pendukung pada pengajuan ini.</p>
        @endif

        @if ($pengajuan->suratSemester())
            @if ($pengajuan->punyaSuratTertanda())
                <div class="rincian-berkas">
                    <a class="rincian-berkas--emas" href="{{ route('musyrifah.surat_perizinan.dokumen', [$pengajuan, 'tertanda']) }}" target="_blank" rel="noopener">
                        Lihat Surat Bertanda Tangan
                    </a>
                    @if ($pengajuan->surat_tertanda_nama)
                        <span style="align-self:center;font-size:12.5px;color:#6b7280;">{{ $pengajuan->surat_tertanda_nama }}</span>
                    @endif
                </div>
            @else
                <p class="rincian-kosong" style="margin-top:10px;">
                    Surat bertanda tangan orang tua belum diunggah mahasiswa.
                </p>
            @endif
        @endif
    </section>

    <div class="rincian-grid">
        <section class="rincian-kartu">
            <h2>Keputusan Musyrifah</h2>

            @if ($pengajuan->keputusan_nama)
                <table class="rincian-tabel">
                    <tr>
                        <th>Diputuskan Oleh</th>
                        <td>{{ $pengajuan->keputusan_nama }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Keputusan</th>
                        <td>{{ $pengajuan->keputusan_pada?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }} WIB</td>
                    </tr>
                </table>
                @if ($pengajuan->catatan_musyrifah)
                    <div class="rincian-catatan">{{ $pengajuan->catatan_musyrifah }}</div>
                @endif
            @else
                <p class="rincian-kosong">Pengajuan ini belum memiliki keputusan.</p>
            @endif
        </section>

        <section class="rincian-kartu">
            <h2>Riwayat Perubahan Status</h2>

            <div class="rincian-riwayat">
                @forelse ($pengajuan->riwayats as $riwayat)
                    <div class="rincian-riwayat__baris">
                        <strong>{{ $riwayat->labelStatus() }}</strong>
                        <small>
                            {{ $riwayat->created_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }} WIB
                            @if ($riwayat->nama_pengubah)
                                &middot; oleh {{ $riwayat->nama_pengubah }}
                            @endif
                        </small>
                        @if ($riwayat->catatan)
                            <p>{{ $riwayat->catatan }}</p>
                        @endif
                    </div>
                @empty
                    <p class="rincian-kosong">Belum ada riwayat perubahan status.</p>
                @endforelse
            </div>
        </section>
    </div>

    @if ($bolehDiputus)
        <section class="rincian-kartu rincian-kartu--penuh">
            <h2>Ambil Keputusan</h2>

            <form method="POST" action="{{ route('musyrifah.surat_perizinan.keputusan', $pengajuan) }}" class="rincian-keputusan">
                @csrf

                <div class="rincian-opsi">
                    <label>
                        <input type="radio" name="keputusan" value="{{ SuratPerizinan::STATUS_DISETUJUI }}" required
                            @checked(old('keputusan') === SuratPerizinan::STATUS_DISETUJUI)>
                        Setujui
                    </label>
                    <label>
                        <input type="radio" name="keputusan" value="{{ SuratPerizinan::STATUS_DITOLAK }}" required
                            @checked(old('keputusan') === SuratPerizinan::STATUS_DITOLAK)>
                        Tolak
                    </label>
                </div>

                <label class="judul" for="catatanKeputusan">Catatan Keputusan (wajib saat menolak)</label>
                <textarea
                    id="catatanKeputusan"
                    name="catatan_musyrifah"
                    maxlength="2000"
                    placeholder="Tuliskan pertimbangan atau alasan keputusan."
                >{{ old('catatan_musyrifah') }}</textarea>

                <button type="submit" class="rincian-btn">Simpan Keputusan</button>
            </form>
        </section>
    @endif

    @if ($pengajuan->suratSemester())
        <section class="rincian-kartu rincian-kartu--penuh">
            <h2>Verifikasi Surat Bertanda Tangan</h2>

            @if ($bolehDiverifikasi)
                <form method="POST" action="{{ route('musyrifah.surat_perizinan.verifikasi', $pengajuan) }}" class="rincian-keputusan">
                    @csrf

                    <div class="rincian-opsi">
                        <label>
                            <input type="radio" name="status" value="{{ SuratPerizinan::STATUS_SELESAI }}" required
                                @checked(old('status') === SuratPerizinan::STATUS_SELESAI)>
                            Sudah Diverifikasi
                        </label>
                        <label>
                            <input type="radio" name="status" value="{{ SuratPerizinan::STATUS_PERLU_PERBAIKAN }}" required
                                @checked(old('status') === SuratPerizinan::STATUS_PERLU_PERBAIKAN)>
                            Perlu Perbaikan
                        </label>
                    </div>

                    <label class="judul" for="catatanVerifikasi">Catatan Verifikasi (wajib saat perlu perbaikan)</label>
                    <textarea
                        id="catatanVerifikasi"
                        name="catatan_musyrifah"
                        maxlength="2000"
                        placeholder="Contoh: tanda tangan orang tua belum terbaca, mohon unggah ulang."
                    >{{ old('catatan_musyrifah') }}</textarea>

                    <button type="submit" class="rincian-btn">Simpan Verifikasi</button>
                </form>
            @elseif ($pengajuan->status === SuratPerizinan::STATUS_SELESAI)
                <p class="rincian-kosong">Surat ini sudah diverifikasi final.</p>
            @elseif ($pengajuan->status === SuratPerizinan::STATUS_DIBATALKAN)
                <p class="rincian-kosong">Surat ini dibatalkan oleh mahasiswa.</p>
            @else
                <p class="rincian-kosong">Menunggu mahasiswa mengunggah surat bertanda tangan orang tua.</p>
            @endif
        </section>
    @endif

</div>

<script>
    (function () {
        var formKeputusan = document.querySelector('form[action*="keputusan"]');

        if (formKeputusan) {
            var catatanKeputusan = formKeputusan.querySelector('#catatanKeputusan');
            var opsiKeputusan = formKeputusan.querySelectorAll('input[name="keputusan"]');

            function sinkronKeputusan() {
                var terpilih = formKeputusan.querySelector('input[name="keputusan"]:checked');
                catatanKeputusan.required = terpilih !== null && terpilih.value === 'ditolak';
            }

            opsiKeputusan.forEach(function (opsi) {
                opsi.addEventListener('change', sinkronKeputusan);
            });

            sinkronKeputusan();
        }

        var formVerifikasi = document.querySelector('form[action*="verifikasi-surat"]');

        if (formVerifikasi) {
            var catatanVerifikasi = formVerifikasi.querySelector('#catatanVerifikasi');
            var opsiVerifikasi = formVerifikasi.querySelectorAll('input[name="status"]');

            function sinkronVerifikasi() {
                var terpilih = formVerifikasi.querySelector('input[name="status"]:checked');
                catatanVerifikasi.required = terpilih !== null && terpilih.value === 'perlu_perbaikan';
            }

            opsiVerifikasi.forEach(function (opsi) {
                opsi.addEventListener('change', sinkronVerifikasi);
            });

            sinkronVerifikasi();
        }
    })();
</script>

@endsection
