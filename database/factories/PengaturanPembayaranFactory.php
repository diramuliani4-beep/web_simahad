<?php

namespace Database\Factories;

use App\Models\PengaturanPembayaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengaturanPembayaran>
 */
class PengaturanPembayaranFactory extends Factory
{
    protected $model = PengaturanPembayaran::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'periode' => 'Januari 2027',
            'nama_tahap' => 'Tahap 1',
            'nominal' => 2500000,
            // Satu nomor Virtual Account BSI untuk seluruh mahasiswa.
            'va_nomor' => '9900112233445566',
            'va_bank' => 'BSI',
            'periode_dibuka_pada' => now()->subDays(2)->toDateString(),
            'jatuh_tempo' => now()->addDays(14)->toDateString(),
            'is_aktif' => true,
        ];
    }

    public function belumDibuka(): static
    {
        return $this->state(fn () => [
            'periode_dibuka_pada' => now()->addDays(10)->toDateString(),
            'jatuh_tempo' => now()->addDays(40)->toDateString(),
        ]);
    }

    public function mendekatiJatuhTempo(): static
    {
        return $this->state(fn () => [
            'periode_dibuka_pada' => now()->subDays(20)->toDateString(),
            'jatuh_tempo' => now()->addDays(3)->toDateString(),
        ]);
    }

    public function terlewat(): static
    {
        return $this->state(fn () => [
            'periode_dibuka_pada' => now()->subDays(40)->toDateString(),
            'jatuh_tempo' => now()->subDays(5)->toDateString(),
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['is_aktif' => false]);
    }
}
