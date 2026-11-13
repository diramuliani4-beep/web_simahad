@extends('layouts.pimpinan')

@section('title', $register->judul)

@section('content')
    <style>
        .lreg {
            font-family: 'Poppins', sans-serif;
            color: #29252a;
            padding: 8px 0 28px;
        }

        .lreg-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 20px;
        }

        .lreg-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            color: #6B0D1B;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .lreg-back:hover {
            text-decoration: underline;
        }

        .lreg-head h1 {
            margin: 0 0 6px;
            color: #6B0D1B;
            font-size: 25px;
            font-weight: 700;
        }

        .lreg-head p {
            margin: 0;
            color: #81777d;
            font-size: 13px;
            line-height: 1.65;
        }

        .lreg-aksi {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
        }

        .lreg-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 15px;
            border: 1px solid #6B0D1B;
            border-radius: 9px;
            background: #6B0D1B;
            color: #fff;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .lreg-btn:hover {
            background: #560A16;
            border-color: #560A16;
        }

        .lreg-btn.lreg-btn-outline {
            background: #fff;
            color: #6B0D1B;
        }

        .lreg-btn.lreg-btn-outline:hover {
            background: #F8EDEF;
        }

        .lreg-kartu {
            padding: 20px;
            margin-bottom: 18px;
            border: 1px solid #eee7e9;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(50, 20, 30, .04);
        }

        .lreg-kartu h2 {
            margin: 0 0 14px;
            color: #30272b;
            font-size: 15.5px;
            font-weight: 700;
        }

        .lreg-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px 22px;
            margin: 0;
        }

        .lreg-meta div {
            min-width: 0;
        }

        .lreg-meta dt {
            margin: 0 0 3px;
            color: #8b7f84;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .lreg-meta dd {
            margin: 0;
            color: #352c30;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .lreg-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .lreg-badge.draft {
            background: #f1ecee;
            color: #6d6166;
        }

        .lreg-badge.diterbitkan {
            background: #eaf5e6;
            color: #4e8c35;
        }

        .lreg-badge.diarsipkan {
            background: #fdf3e2;
            color: #b07a1e;
        }

        .lreg-tabel-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .lreg-tabel {
            width: 100%;
            min-width: 620px;
            border-collapse: collapse;
        }

        .lreg-tabel th {
            padding: 11px 10px;
            border-bottom: 1px solid #eee1e4;
            background: #f8f0f2;
            color: #6B0D1B;
            text-align: left;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        .lreg-tabel td {
            padding: 11px 10px;
            border-bottom: 1px solid #f1ebed;
            color: #51474c;
            font-size: 12px;
            vertical-align: top;
        }

        .lreg-tabel tbody tr:hover {
            background: #fffafb;
        }

        .lreg-kosong {
            padding: 26px !important;
            color: #91858a !important;
            text-align: center;
        }

        .lreg-ubah {
            display: block;
            font-size: 11px;
            color: #6f636a;
        }

        .lreg-ubah b {
            color: #6B0D1B;
            font-weight: 600;
        }

        @media (max-width: 700px) {
            .lreg-head h1 {
                font-size: 21px;
            }

            .lreg-aksi {
                width: 100%;
            }

            .lreg-aksi .lreg-btn {
                flex: 1;
                justify-content: center;
            }
        }

        @media print {
            .topbar,
            .sidebar,
            .sidebar-backdrop,
            .lreg-head .lreg-aksi,
            .lreg-back,
            .lreg-no-print {
                display: none !important;
            }

            .main-content {
                margin: 0 !important;
                padding: 0 !important;
            }

            .lreg,
            .lreg-kartu {
                padding: 0 !important;
                margin: 0 0 12px !important;
                border: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>

    <div class="lreg">
        <div class="lreg-head">
            <div>
                <a href="{{ route('pimpinan.laporan-mahad') }}" class="lreg-back">← Kembali ke daftar laporan</a>
                <h1>{{ $register->judul }}</h1>
                <p>
                    {{ $register->labelPeriode() }} · {{ $register->labelUnit() }} ·
                    {{ $register->labelSumber() }}
                </p>
            </div>

            <div class="lreg-aksi">
                <a href="{{ route('pimpinan.laporan.cetak', $register) }}" class="lreg-btn">
                    Cetak Laporan
                </a>

                @if (! empty($periodik))
                    <a href="{{ route('pimpinan.laporan.pdf', $register) }}" class="lreg-btn lreg-btn-outline">
                        Unduh PDF
                    </a>

                    <a href="{{ route('pimpinan.laporan.excel', $register) }}" class="lreg-btn lreg-btn-outline">
                        Unduh Excel
                    </a>
                @endif

                @if ($unduhUrl)
                    <a href="{{ $unduhUrl }}" class="lreg-btn lreg-btn-outline">Unduh CSV</a>
                @endif

                <a href="{{ route('pimpinan.laporan-mahad') }}" class="lreg-btn lreg-btn-outline">
                    Daftar Laporan
                </a>
            </div>
        </div>

        <div class="lreg-kartu lreg-no-print">
            <h2>Identitas Laporan</h2>

            <dl class="lreg-meta">
                <div>
                    <dt>Jenis Laporan</dt>
                    <dd>{{ $register->judul }} <span style="color:#8b7f84;font-weight:500;">({{ $register->jenis }})</span></dd>
                </div>

                <div>
                    <dt>Periode Laporan</dt>
                    <dd>{{ $register->labelPeriode() }}</dd>
                </div>

                <div>
                    <dt>Lantai / Unit Sumber</dt>
                    <dd>{{ $register->labelUnit() }}</dd>
                </div>

                <div>
                    <dt>Nama Pembuat</dt>
                    <dd>{{ $register->identitasPembuat() }}</dd>
                </div>

                <div>
                    <dt>Role Pembuat</dt>
                    <dd>
                        @switch(strtolower((string) $register->pembuat_role))
                            @case('musyrifah')
                                Musyrifah
                                @break
                            @case('admin')
                                Admin Sistem
                                @break
                            @case('pimpinan')
                                Pimpinan
                                @break
                            @case('sistem')
                                Sistem (rekap otomatis)
                                @break
                            @default
                                {{ ucfirst((string) $register->pembuat_role ?: '-') }}
                        @endswitch
                    </dd>
                </div>

                <div>
                    <dt>Status Laporan</dt>
                    <dd>
                        <span class="lreg-badge {{ $register->kelasStatus() }}">{{ $register->labelStatus() }}</span>
                        <span style="color:#8b7f84;font-weight:500;"> · versi {{ (int) $register->versi }}</span>
                    </dd>
                </div>

                <div>
                    <dt>Dibuat</dt>
                    <dd>{{ $register->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                </div>

                <div>
                    <dt>Diterbitkan</dt>
                    <dd>
                        {{ $register->diterbitkan_pada?->format('d/m/Y H:i') ?? 'Belum diterbitkan' }}
                        @if ($register->penerbit)
                            <span style="color:#8b7f84;font-weight:500;"> · {{ $register->penerbit->name }}</span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt>Terakhir Diperbarui</dt>
                    <dd>{{ $register->updated_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                </div>

                <div>
                    <dt>Sumber Data</dt>
                    <dd>{{ $register->labelSumber() }}</dd>
                </div>
            </dl>

            @if (filled($register->catatan))
                <p style="margin: 14px 0 0; color: #6f636a; font-size: 12.5px; line-height: 1.6;">
                    Catatan: {{ $register->catatan }}
                </p>
            @endif
        </div>

        <div class="lreg-kartu lreg-no-print">
            <h2>Riwayat Aktivitas Laporan</h2>

            <div class="lreg-tabel-wrap">
                <table class="lreg-tabel">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Aksi</th>
                            <th>Versi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($riwayat as $jejak)
                            <tr>
                                <td>{{ $jejak->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>{{ $jejak->pengguna_nama ?: 'Sistem' }}</td>
                                <td>{{ ucfirst((string) $jejak->pengguna_role ?: '-') }}</td>
                                <td><span class="lreg-badge {{ $jejak->aksi === 'diterbitkan' ? 'diterbitkan' : ($jejak->aksi === 'diarsipkan' ? 'diarsipkan' : 'draft') }}">{{ $jejak->labelAksi() }}</span></td>
                                <td>{{ (int) $jejak->versi }}</td>
                                <td>
                                    {{ $jejak->keterangan ?: '-' }}

                                    @if (! empty($jejak->data_sebelum) || ! empty($jejak->data_sesudah))
                                        @php $dataSebelum = $jejak->data_sebelum ?? []; @endphp

                                        <span class="lreg-ubah">
                                            @foreach ($jejak->data_sesudah ?? [] as $kunci => $nilai)
                                                <b>{{ $kunci }}</b>:
                                                {{ $dataSebelum[$kunci] ?? '-' }} → {{ is_scalar($nilai) ? $nilai : json_encode($nilai) }}
                                                <br>
                                            @endforeach
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="lreg-kosong">
                                    Belum ada aktivitas tercatat untuk laporan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if (! empty($periodik))
            <div class="lreg-kartu">
                <h2>Dokumen Laporan Periodik</h2>
                <p style="margin: 0 0 16px; color: #81777d; font-size: 12.5px; line-height: 1.6;">
                    Dokumen berikut adalah potret data saat laporan dibuat. Isinya tidak berubah
                    walaupun data operasional setelahnya diperbarui.
                </p>

                @include('partials.laporan-periodik', [
                    'laporan' => $register->konten,
                    'meta' => $metaCetak,
                ])
            </div>
        @elseif ($rekap)
            @include('partials.laporan-mahad', [
                'laporan' => $rekap,
                'sembunyikanFilter' => true,
                'metaCetak' => $metaCetak,
            ])
        @else
            <div class="lreg-kartu lreg-no-print">
                <h2>Pratinjau Laporan</h2>
                <p style="margin:0;color:#81777d;font-size:12.5px;">
                    Jenis laporan ini belum memiliki pratinjau otomatis.
                </p>
            </div>
        @endif
    </div>

    @if ($cetak)
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
@endsection
