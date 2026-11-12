<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Perekam audit trail aktivitas penting SIMAHAD.
 *
 * Pemanggilan dilakukan setelah tindakan benar-benar tersimpan ke database,
 * bukan saat tombol diklik. Nilai rahasia (kata sandi, token, remember token)
 * tidak pernah direkam.
 */
class AuditTrailService
{
    /** Kolom yang tidak boleh masuk audit trail. */
    private const KOLOM_RAHASIA = [
        'password',
        'password_confirmation',
        'token',
        'token_aktivasi',
        'remember_token',
        'current_password',
        'new_password',
        'api_token',
    ];

    /**
     * Mencatat satu aktivitas penting.
     *
     * @param  array<string, mixed>  $sebelum
     * @param  array<string, mixed>  $sesudah
     */
    public function catat(
        string $modul,
        string $aksi,
        string $objek,
        ?int $objekId = null,
        ?string $deskripsi = null,
        ?User $pelaku = null,
        array $sebelum = [],
        array $sesudah = [],
    ): ?ActivityLog {
        $pelaku ??= Auth::user();

        [$sebelumBersih, $sesudahBersih] = $this->saring($sebelum, $sesudah);

        try {
            return ActivityLog::create([
                'user_id' => $pelaku?->getKey(),
                'user_name' => $pelaku?->name ?? 'Sistem',
                'user_email' => $pelaku?->email,
                'user_role' => $pelaku?->role ? strtolower((string) $pelaku->role) : 'sistem',
                'modul' => $modul,
                'aksi' => $aksi,
                'objek' => $objek,
                'objek_id' => $objekId,
                'data_sebelum' => $sebelumBersih ?: null,
                'data_sesudah' => $sesudahBersih ?: null,
                'activity' => trim($modul.' · '.$aksi),
                'description' => $deskripsi,
                'ip_address' => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Audit tidak boleh menggagalkan tindakan utama pengguna.
            report($e);

            return null;
        }
    }

    /**
     * Mencatat perubahan atribut model dengan nilai sebelum dan sesudah.
     *
     * @param  array<string, mixed>  $sebelum
     */
    public function catatPerubahan(
        Model $model,
        array $sebelum,
        string $modul,
        string $aksi,
        string $label,
        ?User $pelaku = null,
    ): ?ActivityLog {
        $sesudah = $model->getAttributes();

        return $this->catat(
            $modul,
            $aksi,
            $label,
            (int) $model->getKey(),
            $label.' · '.$aksi,
            $pelaku,
            $sebelum,
            $sesudah,
        );
    }

    /**
     * Menyaring kolom rahasia dan hanya menyisakan nilai yang berubah.
     *
     * @param  array<string, mixed>  $sebelum
     * @param  array<string, mixed>  $sesudah
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    private function saring(array $sebelum, array $sesudah): array
    {
        $sebelum = $this->buangRahasia($sebelum);
        $sesudah = $this->buangRahasia($sesudah);

        if ($sebelum === [] || $sesudah === []) {
            return [$sebelum, $sesudah];
        }

        $kunci = array_unique(array_merge(array_keys($sebelum), array_keys($sesudah)));
        $sebelumUbah = [];
        $sesudahUbah = [];

        foreach ($kunci as $nama) {
            $lama = $sebelum[$nama] ?? null;
            $baru = $sesudah[$nama] ?? null;

            if ($this->sama($lama, $baru)) {
                continue;
            }

            if (array_key_exists($nama, $sebelum)) {
                $sebelumUbah[$nama] = $this->ringkas($lama);
            }

            if (array_key_exists($nama, $sesudah)) {
                $sesudahUbah[$nama] = $this->ringkas($baru);
            }
        }

        return [$sebelumUbah, $sesudahUbah];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buangRahasia(array $data): array
    {
        foreach (self::KOLOM_RAHASIA as $kolom) {
            unset($data[$kolom]);
        }

        return $data;
    }

    private function sama(mixed $lama, mixed $baru): bool
    {
        if (is_array($lama) || is_array($baru)) {
            return $lama == $baru;
        }

        return (string) $lama === (string) $baru;
    }

    private function ringkas(mixed $nilai): mixed
    {
        if ($nilai instanceof \DateTimeInterface) {
            return $nilai->format('Y-m-d H:i:s');
        }

        if (is_bool($nilai) || is_int($nilai) || is_float($nilai) || $nilai === null) {
            return $nilai;
        }

        if (is_array($nilai)) {
            return $nilai;
        }

        return mb_substr((string) $nilai, 0, 500);
    }
}
