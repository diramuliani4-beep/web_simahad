@extends('layouts.musyrifah')

@section('title', $laporan['judul'] ?? 'Laporan Periodik')

@section('content')
    <style>
        .lp-page {
            width: 100%;
            max-width: 1080px;
            margin: 0 auto;
            padding-bottom: 24px;
            color: #322a2d;
            font-size: 13px;
        }

        .lp-page *,
        .lp-page *::before,
        .lp-page *::after {
            box-sizing: border-box;
        }

        .lp-back {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 8px;
            color: #6B0D1B;
            font-size: 11.5px;
            font-weight: 600;
            text-decoration: none;
        }

        .lp-back:hover {
            text-decoration: underline;
        }

        .lp-kepala {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px 14px;
            margin-bottom: 12px;
        }

        .lp-kepala h1 {
            margin: 0;
            color: #211a1c;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -.2px;
        }

        .lp-kepala p {
            max-width: 620px;
            margin: 4px 0 0;
            color: #8b7e84;
            font-size: 12px;
            line-height: 1.55;
        }

        .lp-tombol {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
        }

        .lp-tombol form {
            display: inline-flex;
        }

        .lp-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 34px;
            padding: 7px 13px;
            border: 1px solid #6B0D1B;
            border-radius: 9px;
            background: #6B0D1B;
            color: #fff;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.1;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
            transition: background .16s, border-color .16s, color .16s;
        }

        .lp-btn:hover {
            background: #52070f;
            border-color: #52070f;
        }

        .lp-btn.lp-btn-outline {
            border-color: #e6d7dc;
            background: #fff;
            color: #6B0D1B;
        }

        .lp-btn.lp-btn-outline:hover {
            border-color: #C9A96A;
            background: #F8EDEF;
            color: #6B0D1B;
        }

        .lp-kartu {
            margin-bottom: 12px;
            padding: 13px 15px 15px;
            border: 1px solid #efe5e7;
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(59, 7, 15, .035);
        }

        .lp-kartu.lp-dokumen-kartu {
            padding: 16px 14px;
        }

        .lp-kartu > h2 {
            position: relative;
            margin: 0 0 9px;
            padding-left: 9px;
            color: #6B0D1B;
            font-size: 13.5px;
            font-weight: 700;
            line-height: 1.3;
        }

        .lp-kartu > h2::before {
            content: "";
            position: absolute;
            top: 2px;
            bottom: 2px;
            left: 0;
            width: 3px;
            border-radius: 2px;
            background: #C9A96A;
        }

        .lp-dua {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 12px;
        }

        .lp-dua .lp-kartu {
            margin-bottom: 0;
        }

        .lp-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 9px 16px;
            margin: 0;
        }

        .lp-meta dt {
            margin: 0 0 2px;
            color: #8b7f84;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .lp-meta dd {
            margin: 0;
            color: #352c30;
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .lp-badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 700;
        }

        .lp-badge.draft {
            background: #f1ecee;
            color: #6d6166;
        }

        .lp-badge.diterbitkan {
            background: #eaf5e6;
            color: #4e8c35;
        }

        .lp-badge.diarsipkan {
            background: #fdf3e2;
            color: #b07a1e;
        }

        .lp-alert {
            margin-bottom: 12px;
            padding: 9px 12px;
            border: 1px solid #ead8dd;
            border-left: 3px solid #6B0D1B;
            border-radius: 9px;
            background: #fff;
            color: #6B0D1B;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.55;
        }

        .lp-alert.lp-alert-info {
            border-left-color: #C9A96A;
            color: #8c621a;
            font-weight: 500;
        }

        .lp-tabel {
            width: 100%;
            min-width: 520px;
            border-collapse: collapse;
        }

        .lp-tabel th {
            padding: 7px 9px;
            border-bottom: 1px solid #eee1e4;
            background: #FBF6EC;
            color: #6B0D1B;
            text-align: left;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .lp-tabel td {
            padding: 7px 9px;
            border-bottom: 1px solid #f4eef0;
            color: #51474c;
            font-size: 11.5px;
            vertical-align: top;
        }

        .lp-tabel tbody tr:last-child td {
            border-bottom: 0;
        }

        .lp-tabel-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #f1e9eb;
            border-radius: 9px;
        }

        .lp-form-baris {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 9px 10px;
            margin-bottom: 10px;
        }

        .lp-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .lp-field label {
            color: #7d7075;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.4;
        }

        .lp-field textarea {
            width: 100%;
            min-height: 64px;
            padding: 7px 9px;
            border: 1px solid #e6dde0;
            border-radius: 8px;
            background: #fff;
            color: #3f353a;
            font-family: inherit;
            font-size: 12.5px;
            line-height: 1.5;
            outline: none;
            resize: vertical;
            transition: border-color .16s, box-shadow .16s;
        }

        .lp-field textarea:focus {
            border-color: #C9A96A;
            box-shadow: 0 0 0 3px rgba(201, 169, 106, .16);
        }

        @media (max-width: 1080px) {
            .lp-dua {
                grid-template-columns: minmax(0, 1fr);
            }

            .lp-dua .lp-kartu {
                margin-bottom: 12px;
            }
        }

        @media (max-width: 700px) {
            .lp-kepala h1 {
                font-size: 18px;
            }

            .lp-kartu {
                padding: 11px;
            }

            .lp-kartu.lp-dokumen-kartu {
                padding: 10px 8px;
            }

            .lp-tombol .lp-btn {
                flex: 1;
            }
        }
    </style>

    <div class="lp-page">
        <a href="{{ $backUrl }}" class="lp-back">← Kembali</a>

        <div class="lp-kepala">
            <div>
                <h1>{{ $laporan['judul'] ?? 'Laporan Periodik' }}</h1>
                <p>
                    Periode {{ $laporan['periode_label'] ?? '-' }} ·
                    Cakupan {{ $laporan['cakupan'] ?? '-' }}
                </p>
            </div>

            @if ($register)
                <div class="lp-tombol">
                    <a href="{{ route('musyrifah.laporan.periodik.cetak', $register) }}" class="lp-btn" target="_blank" rel="noopener">
                        Cetak
                    </a>
                    <a href="{{ route('musyrifah.laporan.periodik.pdf', $register) }}" class="lp-btn lp-btn-outline">
                        Unduh PDF
                    </a>
                    <a href="{{ route('musyrifah.laporan.periodik.excel', $register) }}" class="lp-btn lp-btn-outline">
                        Unduh Excel
                    </a>
                </div>
            @endif
        </div>

        @if (session('status'))
            <div class="lp-alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="lp-alert">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($pratinjau)
            <div class="lp-alert lp-alert-info">
                Ini pratinjau sebelum laporan disimpan. Setelah disimpan, isi laporan menjadi
                dokumen tetap dan tidak berubah walaupun data operasional diperbarui.
            </div>

            <div class="lp-kartu">
                <h2>Simpan Laporan</h2>

                <form method="POST" action="{{ route('musyrifah.laporan.periodik.simpan') }}">
                    @csrf

                    <input type="hidden" name="jenis" value="{{ $jenis }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    @if ($jenis === 'bulanan')
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                    @endif

                    <input type="hidden" name="evaluasi" value="{{ $evaluasi }}">
                    <input type="hidden" name="kendala" value="{{ $kendala }}">
                    <input type="hidden" name="catatan" value="{{ $catatan }}">

                    <div class="lp-tombol">
                        <button type="submit" class="lp-btn">Simpan sebagai Draft</button>
                        <a href="{{ $backUrl }}" class="lp-btn lp-btn-outline">Ubah Periode</a>
                    </div>
                </form>
            </div>
        @else
            <div class="lp-dua">
                <div class="lp-kartu">
                    <h2>Identitas Laporan</h2>

                    <dl class="lp-meta">
                        <div>
                            <dt>Jenis Laporan</dt>
                            <dd>{{ $laporan['jenis_label'] ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt>Periode</dt>
                            <dd>{{ $register->labelPeriode() }}</dd>
                        </div>
                        <div>
                            <dt>Status</dt>
                            <dd>
                                <span class="lp-badge {{ $register->kelasStatus() }}">{{ $register->labelStatus() }}</span>
                                <span style="color:#8b7f84;font-weight:500;"> · versi {{ (int) $register->versi }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt>Pembuat</dt>
                            <dd>{{ $register->identitasPembuat() }}</dd>
                        </div>
                        <div>
                            <dt>Dibuat</dt>
                            <dd>{{ $register->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt>Diterbitkan</dt>
                            <dd>{{ $register->diterbitkan_pada?->format('d/m/Y H:i') ?? 'Belum diterbitkan' }}</dd>
                        </div>
                    </dl>

                    @if (filled($register->catatan))
                        <p style="margin: 10px 0 0; color: #6f636a; font-size: 11.5px; line-height: 1.55;">
                            Catatan register: {{ $register->catatan }}
                        </p>
                    @endif
                </div>

                <div class="lp-kartu">
                    <h2>Status dan Pembaruan Laporan</h2>

                    <div class="lp-tombol" style="margin-bottom: 10px;">
                        @if ($register->status !== \App\Models\LaporanMahad::STATUS_DITERBITKAN)
                            <form method="POST" action="{{ route('musyrifah.laporan.terbitkan', $register) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="lp-btn">Terbitkan Laporan</button>
                            </form>
                        @endif

                        @if ($register->status !== \App\Models\LaporanMahad::STATUS_DIARSIPKAN)
                            <form method="POST" action="{{ route('musyrifah.laporan.arsipkan', $register) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="lp-btn lp-btn-outline">Arsipkan Laporan</button>
                            </form>
                        @endif
                    </div>

                    @if ($register->status === \App\Models\LaporanMahad::STATUS_DIARSIPKAN)
                        <p style="margin: 0; color: #8c621a; font-size: 11.5px; line-height: 1.55;">
                            Laporan yang sudah diarsipkan tidak dapat dibangkitkan ulang, sehingga isinya tetap
                            sebagai dokumen arsip.
                        </p>
                    @else
                        <p style="margin: 0 0 10px; color: #81777d; font-size: 11.5px; line-height: 1.55;">
                            Isi laporan tidak berubah otomatis ketika data operasional diperbarui. Gunakan
                            tombol di bawah hanya bila Anda memang ingin mengambil data terbaru untuk periode
                            yang sama; versi laporan akan bertambah dan riwayatnya tercatat.
                        </p>

                        <form method="POST" action="{{ route('musyrifah.laporan.periodik.perbarui', $register) }}">
                            @csrf
                            @method('PATCH')

                            <div class="lp-form-baris">
                                <div class="lp-field">
                                    <label for="perbarui-evaluasi">Catatan evaluasi</label>
                                    <textarea
                                        name="evaluasi"
                                        id="perbarui-evaluasi"
                                        maxlength="2000"
                                    >{{ $evaluasi }}</textarea>
                                </div>

                                <div class="lp-field">
                                    <label for="perbarui-kendala">Kendala selama periode</label>
                                    <textarea
                                        name="kendala"
                                        id="perbarui-kendala"
                                        maxlength="2000"
                                    >{{ $kendala }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="lp-btn lp-btn-outline">
                                Bangkitkan Ulang Isi Laporan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        <div class="lp-kartu lp-dokumen-kartu">
            @include('partials.laporan-periodik', [
                'laporan' => $laporan,
                'meta' => $register
                    ? [
                        'Status' => $register->labelStatus(),
                        'Pembuat' => $register->identitasPembuat(),
                        'Versi' => 'Versi '.(int) $register->versi,
                    ]
                    : ['Pembuat' => auth()->user()?->name ?? '-'],
            ])
        </div>

        @if (! $pratinjau && $riwayat->isNotEmpty())
            <div class="lp-kartu">
                <h2>Riwayat Aktivitas Laporan</h2>

                <div class="lp-tabel-wrap">
                    <table class="lp-tabel">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Versi</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($riwayat as $jejak)
                                <tr>
                                    <td>{{ $jejak->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ $jejak->pengguna_nama ?: 'Sistem' }}</td>
                                    <td>{{ $jejak->labelAksi() }}</td>
                                    <td>{{ (int) $jejak->versi }}</td>
                                    <td>{{ $jejak->keterangan ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
