<?php

use App\Models\PengaturanSistem;
use App\Services\PembayaranAngsuranService;
use App\Support\KonfigurasiEmail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('email:uji {tujuan : Alamat email penerima uji coba}', function (string $tujuan) {
    if (! filter_var($tujuan, FILTER_VALIDATE_EMAIL)) {
        $this->error('Alamat email tujuan tidak valid: '.$tujuan);

        return 1;
    }

    // Pengaturan email dari halaman Admin dipakai juga oleh perintah konsol.
    try {
        KonfigurasiEmail::terapkan(PengaturanSistem::aktif());
    } catch (Throwable) {
        // Tabel pengaturan belum tersedia: pakai konfigurasi .env.
    }

    $masalah = KonfigurasiEmail::masalah();

    if ($masalah !== null) {
        $this->error($masalah);

        return 1;
    }

    try {
        Mail::raw(
            "Ini email uji pengiriman SIMAHAD.\n\nBila pesan ini diterima, konfigurasi pengiriman email sudah benar.",
            fn ($pesan) => $pesan->to($tujuan)->subject('Uji Pengiriman Email SIMAHAD'),
        );
    } catch (Throwable $e) {
        report($e);
        $this->error('Pengiriman gagal ('.$e::class.'). Rincian sudah dicatat pada log aplikasi.');

        return 1;
    }

    $this->info('Email uji terkirim ke '.$tujuan.' melalui mailer "'.KonfigurasiEmail::mailer().'".');
    $this->line('Pengirim: '.config('mail.from.address').'.');

    if (KonfigurasiEmail::smtpLokal()) {
        $this->line('Buka dashboard Mailpit untuk melihat pesan: http://127.0.0.1:8025');
    }

    return 0;
})->purpose('Mengirim email uji untuk memverifikasi konfigurasi SMTP (Mailpit lokal)');

Artisan::command('pembayaran:pengingat', function (PembayaranAngsuranService $layanan) {
    $hasil = $layanan->kirimPengingatJatuhTempo();

    $this->info('Pengingat pembayaran dikirim untuk '.$hasil['dikirim'].' dari '.$hasil['diperiksa'].' tagihan yang diperiksa.');

    return 0;
})->purpose('Mengirim pengingat pembayaran sebelum jatuh tempo');

// Pengingat dijalankan setiap pagi agar mahasiswa punya waktu membayar.
Schedule::command('pembayaran:pengingat')->dailyAt('07:30');
