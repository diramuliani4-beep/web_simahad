<?php

namespace Database\Factories;

use App\Models\SuratPerizinan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SuratPerizinan>
 */
class SuratPerizinanFactory extends Factory
{
    protected $model = SuratPerizinan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mulai = now()->addDays(3)->startOfDay();

        return [
            'user_id' => User::factory(),
            'jenis' => SuratPerizinan::JENIS_PULANG,
            'status' => SuratPerizinan::STATUS_MENUNGGU,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $mulai->copy()->addDays(2)->toDateString(),
            'durasi_hari' => 3,
            'lokasi' => fake()->city(),
            'alasan' => fake()->sentence(8),
        ];
    }

    public function pulang(): static
    {
        return $this->state(fn (): array => [
            'jenis' => SuratPerizinan::JENIS_PULANG,
        ]);
    }

    public function kegiatanFakultas(): static
    {
        return $this->state(fn (): array => [
            'jenis' => SuratPerizinan::JENIS_KEGIATAN_FAKULTAS,
            'nama_kegiatan' => 'Seminar Nasional '.fake()->words(2, true),
            'penyelenggara' => 'Fakultas '.fake()->words(2, true),
            'lokasi' => fake()->city(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '15:00',
        ]);
    }

    public function kegiatanKampus(): static
    {
        return $this->state(fn (): array => [
            'jenis' => SuratPerizinan::JENIS_KEGIATAN_KAMPUS,
            'nama_kegiatan' => 'Kegiatan Kampus '.fake()->words(2, true),
            'penyelenggara' => 'UIN Imam Bonjol',
            'lokasi' => fake()->city(),
            'waktu_mulai' => '07:30',
            'waktu_selesai' => '12:00',
        ]);
    }

    public function semester(): static
    {
        return $this->state(fn (): array => [
            'jenis' => SuratPerizinan::JENIS_SEMESTER,
            'nomor_surat' => 'SPM/'.now()->format('Y').'/'.fake()->numerify('###'),
        ]);
    }

    public function disetujui(): static
    {
        return $this->state(fn (): array => [
            'status' => SuratPerizinan::STATUS_DISETUJUI,
            'keputusan_pada' => now(),
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn (): array => [
            'status' => SuratPerizinan::STATUS_DITOLAK,
            'keputusan_pada' => now(),
        ]);
    }
}
