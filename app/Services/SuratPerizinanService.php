<?php

namespace App\Services;

use App\Events\SuratPerizinanDicatat;
use App\Models\SuratPerizinan;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SuratPerizinanService
{
    /**
     * Menyimpan pengajuan sekaligus menetapkan Musyrifah penanggung jawab.
     *
     * @param  array<string, mixed>  $atribut
     */
    public function buatPengajuan(User $mahasiswa, array $atribut, string $catatanRiwayat): SuratPerizinan
    {
        return DB::transaction(function () use ($mahasiswa, $atribut, $catatanRiwayat): SuratPerizinan {
            $jenis = (string) ($atribut['jenis'] ?? '');

            if (! in_array($jenis, SuratPerizinan::jenisPengajuanAktif(), true)) {
                throw ValidationException::withMessages(['jenis' => 'Jenis pengajuan tidak dikenali.']);
            }

            $mahasiswa = User::query()->lockForUpdate()->findOrFail($mahasiswa->id);
            $musyrifah = $this->musyrifahPenanggungJawab($mahasiswa);

            if ($musyrifah === null) {
                throw ValidationException::withMessages([
                    'tanggal_mulai' => 'Pengajuan belum dapat diteruskan karena Musyrifah penanggung jawab lantai kamar Anda belum ditetapkan. Hubungi Admin Ma’had.',
                ]);
            }

            if ($jenis === SuratPerizinan::JENIS_PULANG) {
                $this->pastikanAturanPulang($mahasiswa, Carbon::parse((string) $atribut['tanggal_mulai']));
            }

            $pengajuan = $mahasiswa->suratPerizinans()->create(array_merge($atribut, [
                'user_id' => $mahasiswa->id,
                'status' => SuratPerizinan::STATUS_MENUNGGU,
                'musyrifah_id' => $musyrifah->id,
            ]));

            $pengajuan->catatRiwayat(
                SuratPerizinan::STATUS_MENUNGGU,
                $catatanRiwayat,
                $mahasiswa,
                SuratPerizinanDicatat::PENGAJUAN_BARU,
            );

            return $pengajuan;
        });
    }

    /**
     * Memeriksa ulang kuota dan jarak pada saat pengajuan maupun persetujuan.
     */
    public function pastikanAturanPulang(
        User $mahasiswa,
        CarbonInterface $tanggalMulai,
        ?int $pengajuanId = null,
    ): void {
        $mulai = Carbon::instance($tanggalMulai)->startOfDay();
        $awalBulan = $mulai->copy()->startOfMonth()->toDateString();
        $akhirBulan = $mulai->copy()->endOfMonth()->toDateString();

        $terpakai = SuratPerizinan::query()
            ->where('user_id', $mahasiswa->id)
            ->where('jenis', SuratPerizinan::JENIS_PULANG)
            ->whereIn('status', SuratPerizinan::statusMenggunakanKuota())
            ->when($pengajuanId !== null, fn ($query) => $query->whereKeyNot($pengajuanId))
            ->whereBetween('tanggal_mulai', [$awalBulan, $akhirBulan])
            ->count();

        if ($terpakai >= SuratPerizinan::KUOTA_PULANG_PER_BULAN) {
            throw ValidationException::withMessages([
                'tanggal_mulai' => 'Kuota izin pulang bulan '.$mulai->locale('id')->translatedFormat('F Y')
                    .' sudah habis. Izin darurat tetap dapat diajukan untuk keadaan mendesak.',
            ]);
        }

        $terlaluDekat = SuratPerizinan::query()
            ->where('user_id', $mahasiswa->id)
            ->where('jenis', SuratPerizinan::JENIS_PULANG)
            ->whereIn('status', SuratPerizinan::statusMenggunakanKuota())
            ->when($pengajuanId !== null, fn ($query) => $query->whereKeyNot($pengajuanId))
            ->whereBetween('tanggal_mulai', [
                $mulai->copy()->subDays(SuratPerizinan::JARAK_PULANG_HARI - 1)->toDateString(),
                $mulai->copy()->addDays(SuratPerizinan::JARAK_PULANG_HARI - 1)->toDateString(),
            ])
            ->exists();

        if ($terlaluDekat) {
            throw ValidationException::withMessages([
                'tanggal_mulai' => 'Jarak minimum antarizin pulang mingguan adalah '
                    .SuratPerizinan::JARAK_PULANG_HARI.' hari. Pilih tanggal lain atau ajukan izin darurat bila mendesak.',
            ]);
        }
    }

    /**
     * Menentukan Musyrifah aktif berdasarkan gedung dan lantai kamar mahasiswa.
     */
    public function musyrifahPenanggungJawab(User $mahasiswa): ?User
    {
        $kamar = $mahasiswa->kamar;

        if ($kamar === null || trim((string) $kamar->gedung) === '' || (int) $kamar->lantai < 1) {
            return null;
        }

        return User::query()
            ->whereRaw('LOWER(TRIM(role)) = ?', ['musyrifah'])
            ->whereRaw('LOWER(TRIM(status)) = ?', ['aktif'])
            ->whereRaw('LOWER(TRIM(gedung_penempatan)) = ?', [mb_strtolower(trim((string) $kamar->gedung))])
            ->where('lantai_penempatan', (int) $kamar->lantai)
            ->orderBy('id')
            ->first();
    }
}
