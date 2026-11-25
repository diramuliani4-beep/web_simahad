<?php

namespace App\Mail;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email pemberitahuan pengumuman Ma'had yang baru dipublikasikan.
 *
 * Email memuat judul, isi, dan tanggal publikasi pengumuman sesuai
 * data yang tersimpan, sehingga penerima tidak perlu membuka SIMAHAD
 * lebih dahulu untuk mengetahui isi pengumuman.
 */
class PengumumanDipublikasikanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pengumuman $pengumuman,
        public User $penerima,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengumuman Ma\'had: '.$this->pengumuman->judul,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pengumuman-dipublikasikan',
        );
    }
}
