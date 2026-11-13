@props([
    'label' => '',
    'nilai' => 0,
    'ikon' => 'dashboard',
    'nada' => null,
    'ket' => null,
    'tautan' => null,
])

{{-- Kartu statistik bersama agar ukuran, ikon, dan tipografi dashboard seragam. --}}
@if ($tautan)
    <a href="{{ $tautan }}" class="dash-kartu" @if ($nada) data-nada="{{ $nada }}" @endif>
@else
    <article class="dash-kartu" @if ($nada) data-nada="{{ $nada }}" @endif>
@endif

    <span class="dash-kartu-kepala">
        <span class="dash-kartu-ikon">
            <x-panel.ikon :nama="$ikon" />
        </span>

        @if ($tautan)
            <span class="dash-kartu-panah" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 17 17 7" />
                    <path d="M8 7h9v9" />
                </svg>
            </span>
        @endif
    </span>

    <span class="dash-kartu-nilai">{{ $nilai }}</span>
    <span class="dash-kartu-label">{{ $label }}</span>

    @if ($ket)
        <span class="dash-kartu-ket">{{ $ket }}</span>
    @endif

@if ($tautan)
    </a>
@else
    </article>
@endif
