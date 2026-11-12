@extends('layouts.panel')

{{--
    Layout Guru kini memakai kerangka panel SIMAHAD yang sama dengan role lain.
    Isi halaman Guru tetap memakai @section('content') seperti sebelumnya,
    sehingga seluruh halaman Guru tidak perlu diubah.
--}}
@section('konten')
    @yield('content')
@endsection
