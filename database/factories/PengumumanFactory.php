<?php

namespace Database\Factories;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengumuman>
 */
class PengumumanFactory extends Factory
{
    protected $model = Pengumuman::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul' => 'Pengumuman '.fake()->words(4, true),
            'kategori' => fake()->randomElement(Pengumuman::kategoriBaku()),
            'isi' => fake()->paragraphs(2, true),
            'tanggal_publikasi' => now()->startOfDay()->toDateString(),
            'status' => Pengumuman::STATUS_DRAFT,
        ];
    }

    /** Pengumuman yang sudah terbit pada tanggal tertentu. */
    public function dipublikasikan(): static
    {
        return $this->state(fn (): array => [
            'status' => Pengumuman::STATUS_DIPUBLIKASIKAN,
            'dipublikasikan_pada' => now(),
        ]);
    }

    /** Pengumuman yang masih berupa draft. */
    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => Pengumuman::STATUS_DRAFT,
            'dipublikasikan_pada' => null,
        ]);
    }

    public function kategori(string $kategori): static
    {
        return $this->state(fn (): array => [
            'kategori' => $kategori,
        ]);
    }
}
