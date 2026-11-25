<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('angsuran_pembayarans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('jatuh_tempo');
            $table->dateTime('dibuka_pada')->nullable()->after('tanggal_mulai');
            $table->dateTime('ditutup_pada')->nullable()->after('dibuka_pada');
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->string('bukti_grup')->nullable()->index()->after('bukti_pembayaran');
        });

        $this->isiKolomBaru();
    }

    /**
     * Rollback sengaja tidak menghapus kolom atau data pembayaran.
     */
    public function down(): void
    {
        // Data pembayaran, bukti transfer, dan riwayat harus tetap tersimpan.
    }

    private function isiKolomBaru(): void
    {
        DB::table('angsuran_pembayarans')->orderBy('id')->chunkById(200, function ($jadwalList): void {
            foreach ($jadwalList as $jadwal) {
                $pengaturan = DB::table('pengaturan_pembayarans')->where('periode', $jadwal->periode)->first();
                $mulaiPeriode = $pengaturan?->periode_dibuka_pada
                    ? Carbon::parse($pengaturan->periode_dibuka_pada)->startOfDay()
                    : now()->startOfDay();
                $jatuhTempo = $jadwal->jatuh_tempo
                    ? Carbon::parse($jadwal->jatuh_tempo)->startOfDay()
                    : $mulaiPeriode->copy();

                // Jangan menggeser tanggal mulai melewati jatuh tempo existing,
                // termasuk jadwal lama yang tanggalnya tidak berurutan.
                $tanggalMulai = $mulaiPeriode->copy();
                if ((int) $jadwal->angsuran_ke > 1) {
                    $tanggalMulai->addDays(match ((int) $jadwal->angsuran_ke) {
                        2 => 30,
                        default => 90,
                    });
                }
                if ($tanggalMulai->greaterThan($jatuhTempo)) {
                    $tanggalMulai = $jatuhTempo->copy();
                }

                $dibukaPada = null;
                $periodeTerbuka = $pengaturan
                    && (bool) $pengaturan->is_aktif
                    && $mulaiPeriode->lessThanOrEqualTo(now()->startOfDay());

                if ($periodeTerbuka && (int) $jadwal->angsuran_ke === 1) {
                    $dibukaPada = now();
                } elseif ($periodeTerbuka) {
                    $adaPembayaran = DB::table('pembayarans')
                        ->where('periode', $jadwal->periode)
                        ->where('angsuran_ke', $jadwal->angsuran_ke)
                        ->where(function ($query): void {
                            $query->whereNotNull('bukti_pembayaran')
                                ->orWhere('bukti_pembayaran', '<>', '')
                                ->orWhereNotIn('status', ['belum_lunas']);
                        })
                        ->exists();

                    if ($adaPembayaran) {
                        $dibukaPada = DB::table('pembayarans')
                            ->where('periode', $jadwal->periode)
                            ->where('angsuran_ke', $jadwal->angsuran_ke)
                            ->where(function ($query): void {
                                $query->whereNotNull('tanggal_upload_bukti')
                                    ->orWhereNotNull('tanggal_bayar')
                                    ->orWhereNotNull('tanggal_verifikasi');
                            })
                            ->min('tanggal_upload_bukti') ?: now();
                    }
                }

                DB::table('angsuran_pembayarans')->where('id', $jadwal->id)->update([
                    'tanggal_mulai' => $tanggalMulai->toDateString(),
                    'dibuka_pada' => $dibukaPada,
                    'ditutup_pada' => null,
                ]);
            }
        });
    }
};
