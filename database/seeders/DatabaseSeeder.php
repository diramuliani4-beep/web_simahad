<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(KamarSeeder::class);

        $kamar = Kamar::where('nomor_kamar', '102')->first();

        User::updateOrCreate(
            ['email' => 'mahasiswa@asrama.ac.id'],
            [
                'name' => 'Ahmad Mahasiswa',
                'nim' => '240101',
                'kamar_id' => $kamar?->id,
                'password' => Hash::make('password123'),
            ]
        );
    }
}
