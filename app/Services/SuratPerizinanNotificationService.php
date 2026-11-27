<?php

namespace App\Services;

use App\Events\SuratPerizinanDicatat;
use App\Mail\SuratPerizinanMail;
use App\Models\Notifikasi;
use App\Models\SuratPerizinan;
use App\Models\SuratPerizinanRiwayat;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SuratPerizinanNotificationService
{
    /**
     * Send in-app and email notifications for one committed history event.
     */
    public function kirim(SuratPerizinanDicatat $event): void
    {
        $peristiwa = $this->peristiwa($event);

        if ($peristiwa === null) {
            return;
        }

        $pengajuan = $event->pengajuan->loadMissing('user.kamar', 'musyrifah');

        foreach ($this->penerima($pengajuan, $peristiwa) as $penerima) {
            $isi = $this->isi($event, $penerima, $peristiwa);

            $this->kirimNotifikasiAplikasi($event, $penerima, $isi);
            $this->kirimEmail($event, $penerima, $peristiwa, $isi);
        }
    }

    private function peristiwa(SuratPerizinanDicatat $event): ?string
    {
        $peristiwa = $event->peristiwa ?? $this->peristiwaDariRiwayat($event->riwayat);

        return match ($peristiwa) {
            SuratPerizinanDicatat::PENGAJUAN_BARU,
            SuratPerizinanDicatat::SURAT_DIUPLOAD,
            SuratPerizinanDicatat::DISETUJUI,
            SuratPerizinanDicatat::DITOLAK,
            SuratPerizinanDicatat::PERLU_PERBAIKAN,
            SuratPerizinanDicatat::SELESAI,
            SuratPerizinanDicatat::DIBATALKAN,
            SuratPerizinanDicatat::WAKTU_KELUAR => $peristiwa,
            default => null,
        };
    }

    private function peristiwaDariRiwayat(SuratPerizinanRiwayat $riwayat): ?string
    {
        $peran = strtolower(trim((string) ($riwayat->pengubah?->role ?? '')));

        if ($riwayat->status === SuratPerizinan::STATUS_MENUNGGU && $peran === 'mahasiswa') {
            return SuratPerizinanDicatat::PENGAJUAN_BARU;
        }

        return match ($riwayat->status) {
            SuratPerizinan::STATUS_DISETUJUI => SuratPerizinanDicatat::DISETUJUI,
            SuratPerizinan::STATUS_DITOLAK => SuratPerizinanDicatat::DITOLAK,
            SuratPerizinan::STATUS_PERLU_PERBAIKAN => SuratPerizinanDicatat::PERLU_PERBAIKAN,
            SuratPerizinan::STATUS_SELESAI => SuratPerizinanDicatat::SELESAI,
            SuratPerizinan::STATUS_DIBATALKAN => SuratPerizinanDicatat::DIBATALKAN,
            default => null,
        };
    }

    /**
     * @return Collection<int, User>
     */
    private function penerima(SuratPerizinan $pengajuan, string $peristiwa): Collection
    {
        $hanyaMahasiswa = in_array($peristiwa, [
            SuratPerizinanDicatat::DISETUJUI,
            SuratPerizinanDicatat::DITOLAK,
            SuratPerizinanDicatat::PERLU_PERBAIKAN,
            SuratPerizinanDicatat::SELESAI,
            SuratPerizinanDicatat::WAKTU_KELUAR,
        ], true);

        $penerima = collect([$pengajuan->user]);

        if (! $hanyaMahasiswa) {
            $penerima->push($this->musyrifahPenerima($pengajuan));
        }

        return $penerima
            ->filter(fn ($user): bool => $user instanceof User && $user->getKey() !== null)
            ->unique('id')
            ->values();
    }

    private function musyrifahPenerima(SuratPerizinan $pengajuan): ?User
    {
        $kamar = $pengajuan->user?->kamar;
        $gedung = trim((string) $kamar?->gedung);
        $lantai = (int) $kamar?->lantai;

        if ($gedung === '' || $lantai < 1) {
            return null;
        }

        return User::query()
            ->whereRaw('LOWER(TRIM(role)) = ?', ['musyrifah'])
            ->whereRaw('LOWER(TRIM(status)) = ?', ['aktif'])
            ->whereRaw('LOWER(TRIM(gedung_penempatan)) = ?', [mb_strtolower($gedung)])
            ->where('lantai_penempatan', $lantai)
            ->when(
                $pengajuan->musyrifah_id !== null,
                fn ($query) => $query->whereKey($pengajuan->musyrifah_id),
            )
            ->orderBy('id')
            ->first();
    }

    /**
     * @return array{judul: string, pesan: string, url: string, label_tombol: string, catatan: string|null}
     */
    private function isi(SuratPerizinanDicatat $event, User $penerima, string $peristiwa): array
    {
        $pengajuan = $event->pengajuan;
        $untukMahasiswa = strtolower(trim((string) $penerima->role)) === 'mahasiswa';
        $namaMahasiswa = $pengajuan->user?->name ?? 'Mahasiswa';
        $nimMahasiswa = $pengajuan->user?->nim ?? '-';
        $jenis = $pengajuan->labelJenis();
        $status = $pengajuan->labelStatus();
        $catatan = $event->riwayat->catatan;

        [$judul, $pesan] = match ($peristiwa) {
            SuratPerizinanDicatat::PENGAJUAN_BARU => $untukMahasiswa
                ? ['Pengajuan Baru Diterima', 'Pengajuan '.$jenis.' Anda telah diterima dan menunggu pemeriksaan Musyrifah.']
                : ['Pengajuan Baru Menunggu Pemeriksaan', $namaMahasiswa.' (NIM '.$nimMahasiswa.') mengajukan '.$jenis.' dan menunggu pemeriksaan Anda.'],
            SuratPerizinanDicatat::SURAT_DIUPLOAD => $untukMahasiswa
                ? ['Surat Bertanda Tangan Diunggah', 'Surat '.$jenis.' yang telah ditandatangani telah diunggah dan menunggu pemeriksaan Musyrifah.']
                : ['Surat Bertanda Tangan Diunggah Ulang', $namaMahasiswa.' mengunggah ulang surat bertanda tangan untuk '.$jenis.'.'],
            SuratPerizinanDicatat::DISETUJUI => ['Pengajuan Disetujui', 'Pengajuan '.$jenis.' Anda telah disetujui oleh Musyrifah.'],
            SuratPerizinanDicatat::DITOLAK => ['Pengajuan Ditolak', 'Pengajuan '.$jenis.' Anda ditolak. Alasan penolakan dicantumkan pada catatan di bawah.'],
            SuratPerizinanDicatat::PERLU_PERBAIKAN => ['Pengajuan Perlu Diperbaiki', 'Pengajuan '.$jenis.' Anda perlu diperbaiki. Catatan Musyrifah dicantumkan di bawah.'],
            SuratPerizinanDicatat::SELESAI => ['Pengajuan Selesai', 'Status '.$jenis.' Anda telah berubah menjadi '.$status.'.'],
            SuratPerizinanDicatat::DIBATALKAN => $untukMahasiswa
                ? ['Pengajuan Dibatalkan', 'Pengajuan '.$jenis.' Anda telah dibatalkan.']
                : ['Pengajuan Dibatalkan Mahasiswa', $namaMahasiswa.' membatalkan pengajuan '.$jenis.'.'],
            SuratPerizinanDicatat::WAKTU_KELUAR => ['Waktu Keluar Dicatat', 'Waktu keluar Ma’had untuk '.$jenis.' Anda telah dicatat oleh Musyrifah.'],
            default => ['Status Pengajuan Diperbarui', 'Status '.$jenis.' Anda telah berubah menjadi '.$status.'.'],
        };

        return [
            'judul' => $judul,
            'pesan' => $pesan,
            'url' => $untukMahasiswa
                ? route('surat.perizinan.mhs').'#riwayat-pengajuan'
                : route('musyrifah.surat_perizinan.show', $pengajuan),
            'label_tombol' => $untukMahasiswa ? 'Lihat Riwayat Pengajuan' : 'Lihat Detail Pengajuan',
            'catatan' => $catatan,
        ];
    }

    /**
     * @param  array{judul: string, pesan: string, url: string, label_tombol: string, catatan: string|null}  $isi
     */
    private function kirimNotifikasiAplikasi(SuratPerizinanDicatat $event, User $penerima, array $isi): void
    {
        try {
            Notifikasi::kirim(
                $penerima,
                Notifikasi::TIPE_SURAT_PERIZINAN,
                $isi['judul'],
                $isi['pesan'],
                $isi['url'],
                'surat-perizinan:'.$event->riwayat->getKey().':'.$penerima->getKey()
            );
        } catch (Throwable $e) {
            report($e);

            Log::error('Notifikasi dalam aplikasi Surat dan Perizinan gagal dibuat.', [
                'surat_perizinan_id' => $event->pengajuan->getKey(),
                'surat_perizinan_riwayat_id' => $event->riwayat->getKey(),
                'user_id' => $penerima->getKey(),
                'exception' => $e::class,
            ]);
        }
    }

    /**
     * @param  array{judul: string, pesan: string, url: string, label_tombol: string, catatan: string|null}  $isi
     */
    private function kirimEmail(SuratPerizinanDicatat $event, User $penerima, string $peristiwa, array $isi): void
    {
        $email = trim((string) $penerima->email);

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            $logId = DB::table('surat_perizinan_email_logs')->insertGetId([
                'surat_perizinan_id' => $event->pengajuan->getKey(),
                'surat_perizinan_riwayat_id' => $event->riwayat->getKey(),
                'user_id' => $penerima->getKey(),
                'peristiwa' => $peristiwa,
                'email' => $email,
                'status' => 'memproses',
                'terkirim_pada' => null,
                'gagal_pada' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            return;
        } catch (Throwable $e) {
            report($e);

            Log::error('Log pengiriman email Surat dan Perizinan gagal dibuat.', [
                'surat_perizinan_id' => $event->pengajuan->getKey(),
                'surat_perizinan_riwayat_id' => $event->riwayat->getKey(),
                'user_id' => $penerima->getKey(),
                'exception' => $e::class,
            ]);

            return;
        }

        try {
            Mail::to($email)->send(new SuratPerizinanMail(
                $event->pengajuan,
                $penerima,
                $isi['judul'],
                $isi['pesan'],
                $isi['url'],
                $isi['label_tombol'],
                $isi['catatan'],
            ));
        } catch (Throwable $e) {
            DB::table('surat_perizinan_email_logs')
                ->where('id', $logId)
                ->update(['status' => 'gagal', 'gagal_pada' => now(), 'updated_at' => now()]);
            report($e);

            Log::error('Pengiriman email Surat dan Perizinan gagal.', [
                'surat_perizinan_id' => $event->pengajuan->getKey(),
                'surat_perizinan_riwayat_id' => $event->riwayat->getKey(),
                'user_id' => $penerima->getKey(),
                'mailer' => (string) config('mail.default'),
                'exception' => $e::class,
            ]);

            return;
        }

        try {
            DB::table('surat_perizinan_email_logs')
                ->where('id', $logId)
                ->update(['status' => 'terkirim', 'terkirim_pada' => now(), 'updated_at' => now()]);
        } catch (Throwable $e) {
            report($e);

            Log::error('Tanggal pengiriman email Surat dan Perizinan gagal disimpan.', [
                'surat_perizinan_id' => $event->pengajuan->getKey(),
                'surat_perizinan_riwayat_id' => $event->riwayat->getKey(),
                'user_id' => $penerima->getKey(),
                'exception' => $e::class,
            ]);
        }
    }
}
