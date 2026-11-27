<?php

namespace App\Mail;

use App\Models\SuratPerizinan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuratPerizinanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SuratPerizinan $pengajuan,
        public User $penerima,
        public string $judul,
        public string $pesan,
        public string $url,
        public string $labelTombol,
        public ?string $catatan = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[SIMAHAD] '.$this->judul.' - '.$this->pengajuan->labelJenis(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.surat-perizinan',
        );
    }
}
