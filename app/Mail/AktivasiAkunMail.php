<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email tautan aktivasi akun SIMAHAD.
 *
 * Email hanya memuat tautan aktivasi; kata sandi tidak pernah dikirim
 * dan tidak pernah diketahui oleh Admin Sistem.
 */
class AktivasiAkunMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $pengguna,
        public string $tautan,
        public int $berlakuJam,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aktivasi Akun SIMAHAD - '.$this->pengguna->labelPeran(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.aktivasi-akun',
        );
    }
}
