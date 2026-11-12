@extends('layouts.pimpinan')

@section('title', 'Laporan Ma’had')

@section('content')
<style>
    .laporan-page {
        font-family: 'Poppins', sans-serif;
        color: #29252a;
        padding: 8px 0 28px;
    }

    .laporan-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 26px;
    }

    .laporan-heading h1 {
        margin: 0 0 8px;
        font-size: 27px;
        font-weight: 700;
        color: #6B0D1B;
    }

    .laporan-heading p {
        margin: 0;
        color: #777078;
        font-size: 14px;
        line-height: 1.7;
    }

    .laporan-date {
        padding: 10px 14px;
        border: 1px solid #eadde0;
        border-radius: 10px;
        background: #fff;
        color: #6B0D1B;
        font-size: 13px;
        font-weight: 600;
    }

    .laporan-info {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 18px;
        margin-bottom: 26px;
        border: 1px solid #f0dfe3;
        border-left: 4px solid #6B0D1B;
        border-radius: 12px;
        background: #fff9fa;
    }

    .laporan-info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #F8EDEF;
        color: #6B0D1B;
    }

    .laporan-info h3 {
        margin: 0 0 5px;
        font-size: 15px;
        font-weight: 700;
    }

    .laporan-info p {
        margin: 0;
        color: #716970;
        font-size: 13px;
        line-height: 1.7;
    }

    .laporan-section-title {
        margin: 0 0 16px;
        font-size: 18px;
        font-weight: 700;
    }

    .laporan-section-title.geser {
        margin-top: 32px;
    }

    .laporan-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .laporan-card {
        display: flex;
        flex-direction: column;
        min-height: 220px;
        padding: 22px;
        border: 1px solid #eee7e9;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(50, 20, 30, .04);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .laporan-card:hover {
        transform: translateY(-4px);
        border-color: #d9b5bd;
        box-shadow: 0 10px 25px rgba(107, 13, 27, .09);
    }

    .laporan-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .laporan-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #F8EDEF;
        color: #6B0D1B;
    }

    .laporan-icon .ikon-menu,
    .laporan-info-icon svg {
        width: 23px;
        height: 23px;
    }

    .laporan-number {
        color: #c7b7bb;
        font-size: 13px;
        font-weight: 700;
    }

    .laporan-card h3 {
        margin: 0 0 8px;
        color: #30272b;
        font-size: 16px;
        font-weight: 700;
    }

    .laporan-card p {
        flex: 1;
        margin: 0 0 20px;
        color: #81777d;
        font-size: 13px;
        line-height: 1.7;
    }

    .laporan-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        color: #6B0D1B;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .laporan-link:hover {
        color: #a31c36;
    }

    .laporan-link svg {
        width: 16px;
        height: 16px;
        transition: transform .2s ease;
    }

    .laporan-link:hover svg {
        transform: translateX(4px);
    }

    /* Register laporan */
    .reg-filter {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 12px;
        padding: 16px;
        margin-bottom: 16px;
        border: 1px solid #eee7e9;
        border-radius: 14px;
        background: #fff;
    }

    .reg-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 155px;
    }

    .reg-field.cari {
        flex: 1;
        min-width: 210px;
    }

    .reg-field label {
        color: #81777d;
        font-size: 11.5px;
        font-weight: 600;
    }

    .reg-field select,
    .reg-field input {
        width: 100%;
        box-sizing: border-box;
        padding: 9px 11px;
        border: 1px solid #e8dfe2;
        border-radius: 9px;
        background: #fff;
        color: #30272b;
        font-family: inherit;
        font-size: 12.5px;
        outline-color: #6B0D1B;
    }

    .reg-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
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

    .reg-btn:hover {
        background: #560A16;
        border-color: #560A16;
    }

    .reg-btn.garis {
        background: #fff;
        color: #6B0D1B;
        border-color: #ecd9dd;
    }

    .reg-btn.garis:hover {
        background: #F8EDEF;
        border-color: #C9A96A;
        color: #6B0D1B;
    }

    .reg-kartu {
        padding: 18px;
        border: 1px solid #eee7e9;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(50, 20, 30, .04);
    }

    .reg-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .reg-tabel {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .reg-tabel th {
        padding: 11px 10px;
        border-bottom: 1px solid #eee1e4;
        background: #F8EDEF;
        color: #6B0D1B;
        text-align: left;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .reg-tabel td {
        padding: 12px 10px;
        border-bottom: 1px solid #f1ebed;
        color: #51474c;
        font-size: 12px;
        vertical-align: top;
    }

    .reg-tabel tbody tr:hover {
        background: #fffafb;
    }

    .reg-kosong {
        padding: 26px !important;
        color: #91858a !important;
        text-align: center;
    }

    .reg-judul {
        display: block;
        color: #6B0D1B;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
    }

    .reg-judul:hover {
        text-decoration: underline;
    }

    .reg-sub {
        display: block;
        margin-top: 3px;
        color: #91858a;
        font-size: 11px;
    }

    .reg-badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .reg-badge.draft {
        background: #f4f1ed;
        color: #7a6a54;
    }

    .reg-badge.diterbitkan {
        background: #edf5ee;
        color: #2f6b3a;
    }

    .reg-badge.diarsipkan {
        background: #F8EDEF;
        color: #6B0D1B;
    }

    .reg-aksi {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .reg-aksi a {
        color: #6B0D1B;
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
        border-bottom: 1px solid #C9A96A;
        padding-bottom: 1px;
    }

    .reg-paginasi {
        margin-top: 14px;
    }

    @media (max-width: 1000px) {
        .laporan-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .laporan-heading h1 {
            font-size: 23px;
        }

        .laporan-grid {
            grid-template-columns: 1fr;
        }

        .laporan-card {
            min-height: 190px;
        }

        .laporan-date {
            width: 100%;
            box-sizing: border-box;
        }

        .reg-field,
        .reg-field.cari {
            min-width: 100%;
        }

        .reg-filter .reg-btn {
            width: 100%;
        }
    }
</style>

<div class="laporan-page">
    <div class="laporan-heading">
        <div>
            <h1>Laporan Ma’had</h1>
            <p>
                Register laporan gabungan seluruh lantai. Setiap laporan mencantumkan
                sumber, pembuat, periode, dan statusnya.
            </p>
        </div>

        <div class="laporan-date">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 style="vertical-align: -3px; margin-right: 5px;">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 11h18"/>
            </svg>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    <div class="laporan-info">
        <div class="laporan-info-icon">
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        </div>
        <div>
            <h3>Mode Monitoring</h3>
            <p>
                Pimpinan hanya dapat melihat, memfilter, mencetak, dan mengunduh laporan.
                Perubahan data operasional maupun isi laporan dilakukan oleh Musyrifah
                sesuai lantai atau unitnya.
            </p>
        </div>
    </div>

    <h2 class="laporan-section-title">Kategori Laporan</h2>

    <div class="laporan-grid">
        @foreach ($laporan as $item)
            <article class="laporan-card">
                <div class="laporan-card-top">
                    <div class="laporan-icon">
                        <x-panel.ikon :nama="$item['ikon']" />
                    </div>

                    <span class="laporan-number">
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <h3>{{ $item['judul'] }}</h3>
                <p>{{ $item['deskripsi'] }}</p>
                <a href="{{ $item['url'] }}" class="laporan-link">
                    Lihat laporan
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </article>
        @endforeach
    </div>

    <h2 class="laporan-section-title geser">Register Laporan</h2>

    <form action="{{ route('pimpinan.laporan-mahad') }}" method="GET" class="reg-filter">
        <div class="reg-field">
            <label for="reg-jenis">Jenis laporan</label>
            <select name="jenis" id="reg-jenis">
                <option value="">Semua jenis</option>
                @foreach ($opsiJenis as $opsi)
                    <option value="{{ $opsi['value'] }}" @selected($filter['jenis'] === $opsi['value'])>
                        {{ $opsi['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="reg-field">
            <label for="reg-periode">Periode</label>
            <select name="periode" id="reg-periode">
                <option value="">Semua periode</option>
                @foreach ($opsiPeriode as $opsi)
                    <option value="{{ $opsi['value'] }}" @selected($filter['periode'] === $opsi['value'])>
                        {{ $opsi['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="reg-field">
            <label for="reg-lantai">Lantai / unit</label>
            <select name="lantai" id="reg-lantai">
                <option value="">Semua lantai</option>
                @foreach ($opsiLantai as $opsi)
                    <option value="{{ $opsi['value'] }}" @selected($filter['lantai'] === (string) $opsi['value'])>
                        {{ $opsi['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="reg-field">
            <label for="reg-sumber">Sumber</label>
            <select name="sumber" id="reg-sumber">
                <option value="">Semua sumber</option>
                @foreach ($opsiSumber as $opsi)
                    <option value="{{ $opsi['value'] }}" @selected($filter['sumber'] === $opsi['value'])>
                        {{ $opsi['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="reg-field">
            <label for="reg-status">Status</label>
            <select name="status" id="reg-status">
                <option value="">Semua status</option>
                @foreach ($opsiStatus as $opsi)
                    <option value="{{ $opsi['value'] }}" @selected($filter['status'] === $opsi['value'])>
                        {{ $opsi['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="reg-field cari">
            <label for="reg-cari">Pencarian</label>
            <input type="text" name="cari" id="reg-cari" value="{{ $filter['cari'] }}"
                placeholder="Cari judul, pembuat, atau unit...">
        </div>

        <button type="submit" class="reg-btn">Terapkan</button>
        <a href="{{ route('pimpinan.laporan-mahad') }}" class="reg-btn garis">Reset</a>
    </form>

    <div class="reg-kartu">
        <div class="reg-wrap">
            <table class="reg-tabel">
                <thead>
                    <tr>
                        <th>Laporan</th>
                        <th>Periode</th>
                        <th>Lantai / Unit</th>
                        <th>Pembuat</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Terakhir Diperbarui</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftar as $item)
                        <tr>
                            <td>
                                <a href="{{ route('pimpinan.laporan.detail', $item) }}" class="reg-judul">
                                    {{ $item->judul }}
                                </a>
                                <span class="reg-sub">
                                    {{ \App\Services\LaporanRegisterService::JUDUL[$item->jenis] ?? $item->jenis }}
                                </span>
                            </td>
                            <td>{{ $item->labelPeriode() }}</td>
                            <td>{{ $item->labelUnit() }}</td>
                            <td>
                                {{ $item->identitasPembuat() }}
                                <span class="reg-sub">{{ $item->labelSumber() }}</span>
                            </td>
                            <td>
                                <span class="reg-badge {{ $item->kelasStatus() }}">{{ $item->labelStatus() }}</span>
                                <span class="reg-sub">Versi {{ (int) $item->versi }}</span>
                            </td>
                            <td>{{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>{{ $item->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>
                                <div class="reg-aksi">
                                    <a href="{{ route('pimpinan.laporan.detail', $item) }}">Detail</a>
                                    <a href="{{ route('pimpinan.laporan.cetak', $item) }}" target="_blank">Cetak</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="reg-kosong">
                                Belum ada laporan yang sesuai dengan filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="reg-paginasi">{{ $daftar->links() }}</div>
    </div>
</div>
@endsection
