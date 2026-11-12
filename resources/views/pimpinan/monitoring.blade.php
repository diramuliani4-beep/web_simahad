@extends('layouts.pimpinan')

@section('title', $laporan['judul'])

@section('content')
    <style>
        .mon-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0 0 18px;
        }

        .mon-nav a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 13px;
            border: 1px solid #ecd9dd;
            border-radius: 999px;
            background: #fff;
            color: #6B0D1B;
            font-family: 'Poppins', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: background .16s ease, border-color .16s ease;
        }

        .mon-nav a:hover,
        .mon-nav a.active {
            background: #F8EDEF;
            border-color: #C9A96A;
            color: #6B0D1B;
            font-weight: 700;
        }

        .mon-lantai {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .mon-lantai-kartu {
            padding: 14px 16px;
            border: 1px solid #eee7e9;
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(50, 20, 30, .04);
            font-family: 'Poppins', sans-serif;
        }

        .mon-lantai-kartu span {
            display: block;
            color: #81777d;
            font-size: 11.5px;
            font-weight: 600;
        }

        .mon-lantai-kartu strong {
            display: block;
            margin: 4px 0 6px;
            color: #6B0D1B;
            font-size: 19px;
            font-weight: 700;
        }

        .mon-lantai-kartu small {
            color: #81777d;
            font-size: 11.5px;
        }

        .mon-lantai-bilah {
            height: 6px;
            margin-top: 8px;
            border-radius: 999px;
            background: #f4e7ea;
            overflow: hidden;
        }

        .mon-lantai-bilah i {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #C9A96A;
        }
    </style>

    <nav class="mon-nav" aria-label="Kategori monitoring">
        @foreach (\App\Services\PimpinanMonitoringService::daftarKategori() as $kunci => $meta)
            <a href="{{ route('pimpinan.monitoring.'.$kunci) }}"
                class="{{ $kunci === $kategori ? 'active' : '' }}"
                @if ($kunci === $kategori) aria-current="page" @endif>
                <x-panel.ikon :nama="$meta['ikon']" style="width:15px;height:15px;" />
                {{ $meta['judul'] }}
            </a>
        @endforeach
    </nav>

    @if (! empty($laporan['per_lantai']))
        <div class="mon-lantai">
            @foreach ($laporan['per_lantai'] as $lantai)
                @php
                    $persen = $lantai['kapasitas'] > 0
                        ? round(($lantai['terisi'] / $lantai['kapasitas']) * 100, 1)
                        : 0;
                @endphp

                <div class="mon-lantai-kartu">
                    <span>Lantai {{ $lantai['lantai'] }}</span>
                    <strong>{{ $persen }}% terisi</strong>
                    <small>
                        {{ $lantai['jumlah'] }} kamar ·
                        {{ $lantai['terisi'] }}/{{ $lantai['kapasitas'] }} tempat ·
                        sisa {{ $lantai['sisa'] }}
                    </small>

                    <div class="mon-lantai-bilah" role="progressbar"
                        aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $persen }}">
                        <i style="width: {{ min(100, max(0, (float) $persen)) }}%;"></i>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @include('partials.laporan-mahad', [
        'resetUrl' => route('pimpinan.monitoring.'.$kategori),
    ])
@endsection
