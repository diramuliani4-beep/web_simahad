<?php

namespace Database\Factories;

use App\Models\Kamar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kamar>
 */
class KamarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(101, 320);
        $floor = (int) substr((string) $number, 0, 1);

        return [
            'nama_kamar' => 'Kamar '.$number,
            'nomor_kamar' => (string) $number,
            'gedung' => 'Gedung A',
            'lantai' => $floor,
            'kapasitas' => 4,
            'terisi' => fake()->numberBetween(0, 4),
            'status' => 'Tersedia',
            'fasilitas' => 'Tempat tidur, lemari pakaian, meja belajar, kamar mandi dalam',
        ];
    }
}
