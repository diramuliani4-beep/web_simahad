@extends('layouts.panel')

@section('title', 'Dokumentasi Pertemuan '.$pertemuan->pertemuan_ke)

@push('styles')
<style>
    .dk-page { color: #1f2430; min-width: 0; }
    .dk-back { display: inline-block; margin-bottom: 18px; color: #6b0d1b; font-size: 13px; font-weight: 600; }
    .dk-header { padding-bottom: 20px; margin-bottom: 22px; border-bottom: 1px solid #ecd9dd; }
    .dk-header h1 { margin: 0 0 10px; font-size: 24px; }
    .dk-header p { margin: 4px 0; color: #4f5767; font-size: 13px; line-height: 1.8; }
    .dk-label { display: inline-block; margin-top: 12px; padding: 5px 12px; border-radius: 20px; background: #f8eff2; color: #6b0d1b; font-size: 12px; font-weight: 600; }
</style>
@endpush

@section('konten')
<div class="dk-page">
    <a class="dk-back" href="{{ $kembali }}">&larr; Kembali ke pembelajaran</a>
    <header class="dk-header">
        <h1>Dokumentasi Pertemuan {{ $pertemuan->pertemuan_ke }}</h1>
        <p><strong>{{ $pertemuan->mataPelajaran?->nama }}</strong> · {{ $pertemuan->kelompok }}</p>
        <p>{{ $pertemuan->periode }} · {{ $pertemuan->tanggalTeks() }}</p>
        <span class="dk-label">{{ $dokumentasi->count() }} foto · Hanya lihat</span>
    </header>
    @include('partials.dokumentasi-pertemuan', ['bolehKelola' => false])
</div>
@endsection
