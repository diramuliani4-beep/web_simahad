<?php

namespace Database\Factories;

use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tugas>
 */
class TugasFactory extends Factory
{
    protected $model = Tugas::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_guru' => null,
            'mata_pelajaran_id' => null,
            'nama_mata_pelajaran' => 'Bahasa Arab Dasar',
            'kelompok' => 'Kelompok 1',
            'judul' => 'Tugas '.fake()->words(3, true),
            'deskripsi' => fake()->paragraph(),
            'tanggal_mulai' => now()->toDateString(),
            'batas_pengumpulan' => now()->addDays(7)->setTime(23, 59),
            'status' => Tugas::STATUS_DRAFT,
            'dipublikasikan_pada' => null,
        ];
    }

    public function dipublikasikan(): static
    {
        return $this->state(fn () => [
            'status' => Tugas::STATUS_DIPUBLIKASIKAN,
            'dipublikasikan_pada' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => Tugas::STATUS_DRAFT,
            'dipublikasikan_pada' => null,
        ]);
    }

    public function untuk(string $kelompok, ?string $namaMataPelajaran = null): static
    {
        return $this->state(fn () => [
            'kelompok' => $kelompok,
            'nama_mata_pelajaran' => $namaMataPelajaran ?? 'Bahasa Arab Dasar',
        ]);
    }
}
