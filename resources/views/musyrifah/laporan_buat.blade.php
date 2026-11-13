@extends('layouts.musyrifah')

@section('title', 'Buat Laporan')

@section('content')
    <style>
        .lb-page {
            width: 100%;
            max-width: 920px;
            margin: 0 auto;
            padding-bottom: 24px;
            color: #322a2d;
            font-size: 13px;
        }

        .lb-page *,
        .lb-page *::before,
        .lb-page *::after {
            box-sizing: border-box;
        }

        .lb-back {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 8px;
            color: #6B0D1B;
            font-size: 11.5px;
            font-weight: 600;
            text-decoration: none;
        }

        .lb-back:hover {
            text-decoration: underline;
        }

        .lb-head {
            margin-bottom: 10px;
        }

        .lb-page h1 {
            margin: 0;
            color: #211a1c;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -.2px;
        }

        .lb-sub {
            max-width: 640px;
            margin: 4px 0 0;
            color: #8b7e84;
            font-size: 12px;
            line-height: 1.55;
        }

        .lb-kartu {
            padding: 13px 15px 15px;
            margin-bottom: 12px;
            border: 1px solid #efe5e7;
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(59, 7, 15, .035);
        }

        .lb-kartu h2 {
            position: relative;
            margin: 0 0 9px;
            padding-left: 9px;
            color: #6B0D1B;
            font-size: 13.5px;
            font-weight: 700;
            line-height: 1.3;
        }

        .lb-kartu h2::before {
            content: "";
            position: absolute;
            top: 2px;
            bottom: 2px;
            left: 0;
            width: 3px;
            border-radius: 2px;
            background: #C9A96A;
        }

        .lb-jenis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 9px;
            margin-bottom: 12px;
        }

        .lb-jenis-item {
            position: relative;
            display: block;
            padding: 10px 12px 11px;
            border: 1px solid #ece2e4;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: border-color .16s, background .16s, box-shadow .16s;
        }

        .lb-jenis-item:hover {
            border-color: #dfc9cf;
        }

        .lb-jenis-item input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .lb-jenis-item strong {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
            color: #3f353a;
            font-size: 12.5px;
            font-weight: 700;
            line-height: 1.35;
        }

        .lb-jenis-item strong::before {
            content: "";
            flex: 0 0 8px;
            width: 8px;
            height: 8px;
            border: 1.5px solid #c9b6bc;
            border-radius: 50%;
            background: #fff;
        }

        .lb-jenis-item span {
            display: block;
            padding-left: 14px;
            color: #8b7e84;
            font-size: 11.5px;
            line-height: 1.45;
        }

        .lb-jenis-item:has(input:checked) {
            border-color: #6B0D1B;
            background: #FCF5F6;
            box-shadow: inset 0 0 0 1px #6B0D1B;
        }

        .lb-jenis-item:has(input:checked) strong {
            color: #6B0D1B;
        }

        .lb-jenis-item:has(input:checked) strong::before {
            border-color: #6B0D1B;
            background: #6B0D1B;
            box-shadow: inset 0 0 0 2px #fff;
        }

        .lb-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 9px 10px;
        }

        .lb-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .lb-field label {
            color: #7d7075;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.4;
        }

        .lb-field select,
        .lb-field input,
        .lb-field textarea {
            width: 100%;
            height: 34px;
            padding: 0 9px;
            border: 1px solid #e6dde0;
            border-radius: 8px;
            background: #fff;
            color: #3f353a;
            font-family: inherit;
            font-size: 12.5px;
            outline: none;
            transition: border-color .16s, box-shadow .16s;
        }

        .lb-field textarea {
            height: auto;
            min-height: 64px;
            padding: 7px 9px;
            line-height: 1.5;
            resize: vertical;
        }

        .lb-field select:focus,
        .lb-field input:focus,
        .lb-field textarea:focus {
            border-color: #C9A96A;
            box-shadow: 0 0 0 3px rgba(201, 169, 106, .16);
        }

        .lb-catatan-kecil {
            margin: 8px 0 0;
            color: #8b7e84;
            font-size: 11px;
            line-height: 1.55;
        }

        .lb-catatan-kecil strong {
            color: #6B0D1B;
        }

        .lb-aksi {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
            padding-top: 11px;
            border-top: 1px dashed #f0e7e9;
        }

        .lb-btn {
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

        .lb-btn:hover {
            background: #52070f;
            border-color: #52070f;
        }

        .lb-btn.lb-btn-outline {
            background: #fff;
            border-color: #e6d7dc;
            color: #6B0D1B;
        }

        .lb-btn.lb-btn-outline:hover {
            border-color: #C9A96A;
            background: #F8EDEF;
            color: #6B0D1B;
        }

        .lb-alert {
            padding: 9px 12px;
            margin-bottom: 12px;
            border: 1px solid #ead8dd;
            border-left: 3px solid #6B0D1B;
            border-radius: 9px;
            background: #fff;
            color: #6B0D1B;
            font-size: 12px;
            font-weight: 600;
        }

        .lb-alert.lb-alert-error {
            border-left-color: #c0392b;
            color: #a4342a;
        }

        .lb-alert ul {
            margin: 5px 0 0;
            padding-left: 16px;
            font-weight: 500;
        }

        .lb-riwayat {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f4eef0;
        }

        .lb-riwayat:last-child {
            border-bottom: 0;
        }

        .lb-riwayat strong {
            display: block;
            color: #6B0D1B;
            font-size: 12px;
            line-height: 1.35;
        }

        .lb-riwayat span {
            display: block;
            margin-top: 1px;
            color: #8b7e84;
            font-size: 10.5px;
            line-height: 1.45;
        }

        .lb-kosong {
            padding: 14px;
            border: 1px dashed #e6d7dc;
            border-radius: 9px;
            background: #fdfbfb;
            color: #91858a;
            text-align: center;
            font-size: 11.5px;
            line-height: 1.55;
        }

        [hidden] {
            display: none !important;
        }

        @media (max-width: 700px) {
            .lb-page h1 {
                font-size: 18px;
            }

            .lb-kartu {
                padding: 11px;
            }

            .lb-aksi .lb-btn {
                flex: 1;
            }
        }
    </style>

    <div class="lb-page">
        <a href="{{ route('musyrifah.laporan') }}" class="lb-back">&lsaquo; Kembali ke daftar laporan</a>

        <div class="lb-head">
            <h1>Buat Laporan</h1>
            <p class="lb-sub">
                Pilih Laporan Bulanan atau Laporan Tahunan, lalu tentukan periode datanya.
                Sistem menyusun laporan dari data operasional Ma’had sesuai periode yang dipilih
                dan menyimpannya sebagai dokumen tetap agar isi tidak berubah saat data diperbarui.
            </p>
        </div>

        @if ($errors->any())
            <div class="lb-alert lb-alert-error">
                Periksa kembali periode laporan:
                <ul>
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            id="form-laporan-periodik"
            method="POST"
            action="{{ route('musyrifah.laporan.periodik.simpan') }}"
            class="lb-kartu"
        >
            @csrf

            <h2>1. Jenis Laporan</h2>

            <div class="lb-jenis">
                @foreach ($opsiJenis as $opsi)
                    <label class="lb-jenis-item" for="jenis-{{ $opsi['value'] }}">
                        <input
                            type="radio"
                            name="jenis"
                            id="jenis-{{ $opsi['value'] }}"
                            value="{{ $opsi['value'] }}"
                            data-jenis="{{ $opsi['value'] }}"
                            @checked(old('jenis', $jenis) === $opsi['value'])
                        >

                        <strong>{{ $opsi['label'] }}</strong>

                        <span>
                            @if ($opsi['value'] === 'bulanan')
                                Pilih bulan dan tahun laporan. Data yang diambil hanya data
                                pada bulan tersebut.
                            @else
                                Pilih tahun laporan. Data satu tahun digabungkan dan dihitung
                                satu kali untuk setiap catatan.
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>

            <h2>2. Periode Laporan</h2>

            <div class="lb-grid">
                <div class="lb-field">
                    <label for="tahun">Tahun laporan</label>

                    <select name="tahun" id="tahun" required>
                        @foreach ($opsiTahun as $opsi)
                            <option value="{{ $opsi['value'] }}" @selected((string) old('tahun', $tahun) === (string) $opsi['value'])>
                                {{ $opsi['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lb-field" id="field-bulan" data-bulan>
                    <label for="bulan">Bulan laporan (Laporan Bulanan)</label>

                    <select name="bulan" id="bulan">
                        @foreach ($opsiBulan as $opsi)
                            <option value="{{ $opsi['value'] }}" @selected((string) old('bulan', $bulan) === (string) $opsi['value'])>
                                {{ $opsi['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p class="lb-catatan-kecil">
                Periode terpilih: <strong id="label-periode">{{ $labelPeriode }}</strong>.
                Laporan bulanan dan tahunan disimpan terpisah berdasarkan jenis dan periodenya.
            </p>

            <h2 style="margin-top: 14px;">3. Catatan Evaluasi dan Kendala</h2>

            <div class="lb-grid">
                <div class="lb-field">
                    <label for="evaluasi">Catatan evaluasi</label>
                    <textarea
                        name="evaluasi"
                        id="evaluasi"
                        maxlength="2000"
                        placeholder="Misalnya: kedisiplinan kamar membaik, pembelajaran berjalan sesuai jadwal."
                    >{{ old('evaluasi') }}</textarea>
                </div>

                <div class="lb-field">
                    <label for="kendala">Kendala selama periode</label>
                    <textarea
                        name="kendala"
                        id="kendala"
                        maxlength="2000"
                        placeholder="Misalnya: beberapa mahasiswa belum melunasi tagihan bulan ini."
                    >{{ old('kendala') }}</textarea>
                </div>
            </div>

            <div class="lb-field" style="margin-top: 10px;">
                <label for="catatan">Catatan ringkas untuk register (opsional)</label>
                <input
                    type="text"
                    name="catatan"
                    id="catatan"
                    maxlength="500"
                    value="{{ old('catatan') }}"
                    placeholder="Misalnya: laporan bulanan disusun bersama pengurus kamar"
                >
            </div>

            <div class="lb-aksi">
                <button
                    type="submit"
                    class="lb-btn lb-btn-outline"
                    formaction="{{ route('musyrifah.laporan.pratinjau') }}"
                >
                    Pratinjau Laporan
                </button>

                <button type="submit" class="lb-btn">
                    Simpan sebagai Draft
                </button>
            </div>
        </form>

        <div class="lb-kartu">
            <h2>Riwayat Laporan Periodik Terakhir</h2>

            @forelse ($riwayatPeriodik as $item)
                <div class="lb-riwayat">
                    <div>
                        <strong>{{ $item->judul }}</strong>
                        <span>
                            {{ $item->labelPeriode() }} · {{ $item->identitasPembuat() }} ·
                            {{ $item->labelStatus() }} · v{{ (int) $item->versi }}
                        </span>
                    </div>

                    <a href="{{ route('musyrifah.laporan.periodik', $item) }}" class="lb-btn lb-btn-outline">
                        Buka Laporan
                    </a>
                </div>
            @empty
                <div class="lb-kosong">
                    Belum ada laporan bulanan atau tahunan yang tersimpan.
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('form-laporan-periodik');

            if (! form) {
                return;
            }

            var fieldBulan = form.querySelector('[data-bulan]');
            var selectBulan = document.getElementById('bulan');
            var labelPeriode = document.getElementById('label-periode');
            var selectTahun = document.getElementById('tahun');
            var namaBulan = {
                @foreach ($opsiBulan as $opsi)
                    '{{ $opsi['value'] }}': '{{ $opsi['label'] }}',
                @endforeach
            };

            function jenisAktif() {
                var dipilih = form.querySelector('input[name="jenis"]:checked');

                return dipilih ? dipilih.value : 'bulanan';
            }

            function perbarui() {
                var tahunan = jenisAktif() === 'tahunan';

                fieldBulan.hidden = tahunan;

                if (selectBulan) {
                    selectBulan.disabled = tahunan;
                }

                var tahun = selectTahun ? selectTahun.value : '';
                var bulan = selectBulan ? selectBulan.value : '';

                labelPeriode.textContent = tahunan
                    ? 'Tahun ' + tahun
                    : (namaBulan[bulan] || bulan) + ' ' + tahun;
            }

            form.addEventListener('change', function (event) {
                if (event.target.name === 'jenis' || event.target.name === 'tahun' || event.target.name === 'bulan') {
                    perbarui();
                }
            });

            perbarui();
        })();
    </script>
@endpush
