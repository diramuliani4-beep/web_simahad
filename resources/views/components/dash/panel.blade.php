@props([
    'judul' => '',
    'ikon' => 'dashboard',
    'tautan' => null,
    'labelTautan' => 'Lihat semua',
    'rapat' => false,
])

{{-- Panel isi dashboard: satu gaya untuk seluruh role. --}}
<section {{ $attributes->merge(['class' => 'dash-panel']) }}>
    <header class="dash-panel-kepala">
        <h2 class="dash-panel-judul">
            <x-panel.ikon :nama="$ikon" />
            <span>{{ $judul }}</span>
        </h2>

        @if ($tautan)
            <a href="{{ $tautan }}" class="dash-panel-tautan">{{ $labelTautan }}</a>
        @elseif (isset($aksi))
            {{ $aksi }}
        @endif
    </header>

    <div class="dash-panel-isi {{ $rapat ? 'dash-panel-isi--rapat' : '' }}">
        {{ $slot }}
    </div>
</section>
