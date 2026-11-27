<?php

namespace App\Events;

use App\Models\SuratPerizinan;
use App\Models\SuratPerizinanRiwayat;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuratPerizinanDicatat implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public const PENGAJUAN_BARU = 'pengajuan_baru';

    public const SURAT_DIUPLOAD = 'surat_diupload';

    public const DISETUJUI = 'disetujui';

    public const DITOLAK = 'ditolak';

    public const PERLU_PERBAIKAN = 'perlu_perbaikan';

    public const SELESAI = 'selesai';

    public const DIBATALKAN = 'dibatalkan';

    public const WAKTU_KELUAR = 'waktu_keluar';

    /**
     * @param  string|null  $peristiwa  Explicit event type for transitions that share a status.
     */
    public function __construct(
        public SuratPerizinan $pengajuan,
        public SuratPerizinanRiwayat $riwayat,
        public ?string $peristiwa = null,
    ) {}
}
