<?php

namespace Database\Factories;

use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengumpulanTugas>
 */
class PengumpulanTugasFactory extends Factory
{
    protected $model = PengumpulanTugas::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'tugas_id' => Tugas::factory(),
            'user_id' => User::factory(),
            'file_path' => 'tugas/pengumpulan/contoh.pdf',
            'file_nama' => 'contoh.pdf',
            'dikumpulkan_pada' => now(),
            'keterangan' => null,
            'nilai' => null,
            'komentar_guru' => null,
        ];
    }

    public function dinilai(int $nilai, ?string $komentar = null): static
    {
        return $this->state(fn () => [
            'nilai' => $nilai,
            'komentar_guru' => $komentar,
            'dinilai_pada' => now(),
        ]);
    }

    public function nilaiDipublikasikan(int $nilai, ?string $komentar = null): static
    {
        return $this->state(fn () => [
            'nilai' => $nilai,
            'komentar_guru' => $komentar,
            'dinilai_pada' => now(),
            'nilai_dipublikasikan_pada' => now(),
        ]);
    }
}
