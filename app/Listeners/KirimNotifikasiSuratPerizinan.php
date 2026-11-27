<?php

namespace App\Listeners;

use App\Events\SuratPerizinanDicatat;
use App\Services\SuratPerizinanNotificationService;
use Illuminate\Support\Facades\Log;
use Throwable;

class KirimNotifikasiSuratPerizinan
{
    public function __construct(
        private readonly SuratPerizinanNotificationService $notifikasi,
    ) {}

    public function handle(SuratPerizinanDicatat $event): void
    {
        try {
            $this->notifikasi->kirim($event);
        } catch (Throwable $e) {
            report($e);

            Log::error('Notifikasi Surat dan Perizinan gagal diproses.', [
                'surat_perizinan_id' => $event->pengajuan->getKey(),
                'surat_perizinan_riwayat_id' => $event->riwayat->getKey(),
                'exception' => $e::class,
            ]);
        }
    }
}
