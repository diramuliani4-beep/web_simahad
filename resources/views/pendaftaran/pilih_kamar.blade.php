
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Kamar - SIMAHAD</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #080808;
            color: #222;
            min-height: 100vh;
            padding: 40px 24px;
        }

        .container {
            width: 100%;
            max-width: 1260px;
            margin: auto;
            background: #fff;
            border: 1px solid #7a1024;
            border-radius: 11px;
            padding: 48px 38px;
            min-height: 650px;
        }

        .back {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            color: #7a1024;
            text-decoration: none;
            font-size: 35px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .back span {
            font-size: 35px;
        }

        .description {
            color: #777;
            font-size: 19px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .notice {
            background: #f8ecef;
            border-radius: 14px;
            padding: 24px 20px;
            margin-bottom: 28px;
        }

        .notice-title {
            color: #7a1024;
            font-weight: bold;
            font-size: 21px;
            margin-bottom: 12px;
        }

        .notice p {
            color: #444;
            font-size: 17px;
            line-height: 1.5;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 15px;
        }

        .alert.success {
            background: #e5f7eb;
            color: #18733c;
        }

        .alert.error {
            background: #fbe7e7;
            color: #a51d2d;
        }

        .section-title {
            font-size: 23px;
            margin-bottom: 24px;
        }

        .room-card {
            border: 1px solid #333;
            border-radius: 11px;
            padding: 17px 24px;
            margin-bottom: 12px;
        }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .room-header h3 {
            font-size: 25px;
        }

        .badge {
            padding: 7px 13px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
        }

        .badge.available {
            background: #d9f4e3;
            color: #168348;
        }

        .badge.full {
            background: #eee;
            color: #555;
        }

        .room-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 18px;
            color: #444;
        }

        .room-info svg {
            width: 23px;
            height: 23px;
            color: #7a1024;
            flex-shrink: 0;
        }

        .room-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-top: 28px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 49px;
            padding: 12px 25px;
            border: none;
            border-radius: 11px;
            background: #7a1024;
            color: white;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            flex: 1;
            transition: .2s;
        }

        .btn:hover {
            background: #5e0b1b;
        }

        .btn.disabled {
            background: #eee;
            color: #888;
            cursor: not-allowed;
        }

        .btn.outline {
            background: #fff;
            color: #7a1024;
            border: 1px solid #7a1024;
        }

        .btn.outline:hover {
            background: #f8ecef;
        }

        .empty {
            padding: 40px 20px;
            border: 1px dashed #bbb;
            border-radius: 10px;
            text-align: center;
            color: #777;
            line-height: 1.7;
        }

        .detail-box {
            background: #fff9fa;
            border: 1px solid #e6cbd1;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 25px;
        }

        .detail-box h3 {
            color: #7a1024;
            font-size: 22px;
            margin-bottom: 18px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #eadde0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row span:first-child {
            color: #777;
        }

        .detail-row span:last-child {
            text-align: right;
            font-weight: 600;
        }

        .pending-box {
            background: #fff6df;
            color: #76500b;
            padding: 17px;
            border-radius: 10px;
            margin-bottom: 22px;
            line-height: 1.6;
        }

        @media(max-width: 650px) {
            body {
                padding: 15px;
            }

            .container {
                padding: 25px 17px;
            }

            .back {
                font-size: 27px;
            }

            .description {
                font-size: 15px;
            }

            .room-card {
                padding: 16px;
            }

            .room-header h3 {
                font-size: 20px;
            }

            .room-info {
                font-size: 15px;
            }

            .room-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                flex: auto;
                font-size: 16px;
            }
        }
    </style>
</head>

<body class="sb-mandiri">
    {{-- Sidebar bersama seluruh role SIMAHAD. --}}
    <x-panel.sidebar ragam="mandiri" tombol-buka />

<div class="container">

    <a href="{{ route('pendaftaran.konfirmasi') }}" class="back">
        <span>←</span>
        Pilih Kamar
    </a>

    <p class="description">
        Pilih kamar yang tersedia sesuai kebutuhanmu.
        Penempatan kamar menunggu persetujuan pengelola Ma’had.
    </p>

    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="notice">
        <div class="notice-title">
            ⓘ Sebelum memilih
        </div>

        <p>
            Kamar yang kamu pilih belum menjadi penempatan tetap
            sampai disetujui pengelola.
        </p>
    </div>

    @if(isset($pengajuanSaya) && $pengajuanSaya
        && $pengajuanSaya->status === 'menunggu')

        <div class="pending-box">
            <strong>Pengajuan kamar sedang diproses.</strong><br>
            Kamu sudah mengajukan kamar.
            Silakan menunggu persetujuan pengelola Ma’had.
        </div>

    @endif

    @if(isset($kamarDetail))

        <div class="detail-box">

            <h3>Detail Kamar {{ $kamarDetail->nomor_kamar }}</h3>

            @if(!empty($kamarDetail->foto))
                <div style="margin-bottom: 20px; border-radius: 10px; overflow: hidden; max-height: 280px; background: #eee;">
                    <img src="{{ asset('storage/' . $kamarDetail->foto) }}" alt="Foto Kamar {{ $kamarDetail->nomor_kamar }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            @endif

            <div class="detail-row">
                <span>Nama kamar</span>
                <span>{{ $kamarDetail->nama_kamar }}</span>
            </div>

            <div class="detail-row">
                <span>Nomor kamar</span>
                <span>{{ $kamarDetail->nomor_kamar }}</span>
            </div>

            <div class="detail-row">
                <span>Gedung</span>
                <span>{{ $kamarDetail->gedung ?: '-' }}</span>
            </div>

            <div class="detail-row">
                <span>Lantai</span>
                <span>{{ $kamarDetail->lantai ?: '-' }}</span>
            </div>

            <div class="detail-row">
                <span>Kapasitas</span>
                <span>{{ $kamarDetail->kapasitas }} orang</span>
            </div>

            <div class="detail-row">
                <span>Tempat tersedia</span>
                <span>{{ $kamarDetail->tempat_tersedia }} orang</span>
            </div>

            <div class="detail-row">
                <span>Fasilitas</span>
                <span>{{ $kamarDetail->fasilitas ?: 'Belum tersedia' }}</span>
            </div>

            <div class="room-actions">

                <a href="{{ route('pendaftaran.pilihKamarMhs') }}"
                   class="btn outline">
                    Kembali
                </a>

                @if(!$kamarDetail->penuh &&
                    !($pengajuanSaya &&
                    $pengajuanSaya->status === 'menunggu'))

                    <form method="POST"
                          action="{{ route('pendaftaran.simpanPilihanKamarMhs') }}"
                          style="flex:1;">

                        @csrf

                        <input type="hidden"
                               name="kamar_id"
                               value="{{ $kamarDetail->id }}">

                        <button type="submit" class="btn"
                                style="width:100%;">
                            Pilih Kamar
                        </button>

                    </form>

                @else

                    <button class="btn disabled" disabled>
                        Tidak Tersedia
                    </button>

                @endif

            </div>

        </div>

    @endif

    <h2 class="section-title">Daftar Kamar</h2>

    @forelse($kamars as $kamar)

        <div class="room-card">

            @if(!empty($kamar->foto))
                <div style="margin: -17px -24px 16px -24px; height: 160px; overflow: hidden; border-radius: 10px 10px 0 0; background: #eed8dc;">
                    <img src="{{ asset('storage/' . $kamar->foto) }}" alt="Foto Kamar {{ $kamar->nomor_kamar }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            @endif

            <div class="room-header">

                <h3>Kamar {{ $kamar->nomor_kamar }}</h3>

                @if($kamar->penuh)
                    <span class="badge full">Penuh</span>
                @else
                    <span class="badge available">Tersedia</span>
                @endif

            </div>

            <div class="room-info">

                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path d="M3 21V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v16"/>
                    <path d="M3 10h18M8 10v11M16 10v11"/>
                </svg>

                <span>
                    Gedung dan lantai:
                    {{ $kamar->gedung ?: '-' }},
                    lantai {{ $kamar->lantai ?: '-' }}
                </span>

            </div>

            <div class="room-info">

                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="8" r="3"/>
                    <path d="M3 20v-2a6 6 0 0 1 12 0v2"/>
                    <path d="M16 5a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v2"/>
                </svg>

                <span>
                    Tempat tersedia: {{ $kamar->tempat_tersedia }}
                </span>

            </div>

            <div class="room-actions">

                <a href="{{ route(
                    'pendaftaran.detailKamarMhs',
                    $kamar->id
                ) }}" class="btn outline">
                    Cek Kamar
                </a>

                @if(!$kamar->penuh &&
                    !($pengajuanSaya &&
                    $pengajuanSaya->status === 'menunggu'))

                    <a href="{{ route(
                        'pendaftaran.detailKamarMhs',
                        $kamar->id
                    ) }}" class="btn">
                        Pilih Kamar
                    </a>

                @else

                    <button class="btn disabled" disabled>
                        Penuh
                    </button>

                @endif

            </div>

        </div>

    @empty

        <div class="empty">
            <strong>Belum ada data kamar.</strong>
            <br>
            Silakan menunggu pengelola Ma’had menambahkan data kamar.
        </div>

    @endforelse

</div>

</body>
</html>