@extends('layouts.panel')

@section('title', 'Pengaturan Akun')

@section('konten')
    @include('partials.pengaturan-akun', [
        'user' => $user,
        'pegawai' => $pegawai,
        'labelPeran' => $labelPeran,
        'daftarAkses' => $daftarAkses,
        'tautanAkses' => $tautanAkses,
        'aksiProfil' => $aksiProfil ?? null,
        'aksiFoto' => $aksiFoto,
        'aksiHapusFoto' => $aksiHapusFoto,
        'aksiPassword' => $aksiPassword,
        'aksiEmail' => $aksiEmail,
    ])
@endsection
