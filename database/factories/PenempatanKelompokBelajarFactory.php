<?php

namespace Database\Factories;

use App\Models\KelompokBelajar;
use App\Models\PenempatanKelompokBelajar;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PenempatanKelompokBelajar>
 */
class PenempatanKelompokBelajarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nomor = fake()->unique()->numberBetween(1, 999);

        return [
            'user_id' => User::factory()->state([
                'role' => 'mahasiswa',
                'nim' => (string) fake()->unique()->numerify('23########'),
            ]),
            'kelompok_belajar_id' => KelompokBelajar::create([
                'nama_kelompok' => 'Kelompok '.$nomor,
                'nomor_kelompok' => $nomor,
            ]),
            'status' => 'aktif',
            'keterangan' => 'Penempatan kelompok belajar',
            'ditugaskan_pada' => now(),
            'dicabut_pada' => null,
        ];
    }
}
