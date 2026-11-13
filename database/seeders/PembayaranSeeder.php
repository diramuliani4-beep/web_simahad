<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan akun mahasiswa utama (User 1) memiliki tagihan
        $userMahasiswa = User::where('email', 'mahasiswa@asrama.ac.id')->first();
        if ($userMahasiswa) {
            $pembayaranUtama = Pembayaran::updateOrCreate(
                [
                    'user_id' => $userMahasiswa->id,
                    'periode' => 'Januari 2027',
                    'angsuran_ke' => 1,
                ],
                [
                    'kode_transaksi' => 'TRX-'.date('Ymd').'-0001',
                    'nama_tagihan' => 'Tahap 1',
                    'angsuran_ke' => 1,
                    'nominal' => 2500000.00,
                    'status' => 'belum_lunas',
                    'metode_pembayaran' => null,
                    'tanggal_bayar' => null,
                    'keterangan' => 'Tagihan Asrama Periode Januari 2027',
                ]
            );

            // Log aktivitas awal
            ActivityLog::firstOrCreate(
                [
                    'user_id' => $userMahasiswa->id,
                    'activity' => 'Tagihan Diterbitkan',
                ],
                [
                    'user_name' => $userMahasiswa->name,
                    'user_email' => $userMahasiswa->email,
                    'user_role' => 'mahasiswa',
                    'description' => "Tagihan Pembayaran Asrama Periode Januari 2027 sebesar Rp 2.500.000 diterbitkan untuk {$userMahasiswa->name} ({$userMahasiswa->nim}).",
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ]
            );
        }

        // 2. Data mahasiswa lainnya dari desain halaman musyrifah
        $dataMahasiswa = [
            [
                'name' => 'Ahmad',
                'nim' => '2317001',
                'email' => 'ahmad@asrama.ac.id',
                'tagihan' => 1000000,
                'status' => 'menunggu',
                'metode' => 'Transfer BSI',
                'tanggal_bayar' => now()->subHours(5),
            ],
            [
                'name' => 'Budi',
                'nim' => '2317002',
                'email' => 'budi@asrama.ac.id',
                'tagihan' => 1000000,
                'status' => 'menunggu',
                'metode' => 'Transfer BSI',
                'tanggal_bayar' => now()->subHours(8),
            ],
            [
                'name' => 'Cinta',
                'nim' => '2317003',
                'email' => 'cinta@asrama.ac.id',
                'tagihan' => 1000000,
                'status' => 'lunas',
                'metode' => 'BRIVA',
                'tanggal_bayar' => now()->subDays(1),
            ],
            [
                'name' => 'Dinda',
                'nim' => '2317004',
                'email' => 'dinda@asrama.ac.id',
                'tagihan' => 1000000,
                'status' => 'lunas',
                'metode' => 'Transfer BSI',
                'tanggal_bayar' => now()->subDays(2),
            ],
            [
                'name' => 'Gibran',
                'nim' => '2317005',
                'email' => 'gibran@asrama.ac.id',
                'tagihan' => 1000000,
                'status' => 'lunas',
                'metode' => 'Transfer BSI',
                'tanggal_bayar' => now()->subDays(3),
            ],
            [
                'name' => 'Hakim',
                'nim' => '2317006',
                'email' => 'hakim@asrama.ac.id',
                'tagihan' => 1500000,
                'status' => 'menunggu',
                'metode' => 'Transfer BSI',
                'tanggal_bayar' => now()->subHours(2),
            ],
            [
                'name' => 'Indah',
                'nim' => '2317007',
                'email' => 'indah@asrama.ac.id',
                'tagihan' => 1500000,
                'status' => 'lunas',
                'metode' => 'BRIVA',
                'tanggal_bayar' => now()->subDays(1),
            ],
        ];

        foreach ($dataMahasiswa as $index => $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'nim' => $item['nim'],
                    'password' => Hash::make('password123'),
                    'role' => 'mahasiswa',
                    'status' => 'aktif',
                ]
            );

            Pembayaran::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'periode' => 'Januari 2027',
                    'angsuran_ke' => 1,
                ],
                [
                    'kode_transaksi' => 'TRX-'.date('Ymd').'-'.str_pad((string) ($index + 2), 4, '0', STR_PAD_LEFT),
                    'nama_tagihan' => 'Tahap 1',
                    'angsuran_ke' => 1,
                    'nominal' => $item['tagihan'],
                    'status' => $item['status'],
                    'metode_pembayaran' => $item['metode'],
                    'tanggal_bayar' => $item['tanggal_bayar'],
                    'keterangan' => 'Pembayaran Asrama via '.$item['metode'],
                ]
            );

            // Buat activity log contoh untuk mahasiswa
            ActivityLog::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'activity' => $item['status'] === 'lunas' ? 'Pembayaran Tagihan' : 'Konfirmasi Pembayaran',
                ],
                [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_role' => 'mahasiswa',
                    'description' => "Mahasiswa {$user->name} ({$user->nim}) melakukan pembayaran tagihan Asrama sebesar Rp ".number_format($item['tagihan'], 0, ',', '.')." via {$item['metode']}.",
                    'created_at' => $item['tanggal_bayar'],
                    'updated_at' => $item['tanggal_bayar'],
                ]
            );
        }

        // Pastikan juga user "nana" yang sudah ada memiliki tagihan
        $userNana = User::where('email', 'nana@gmail.com')->first();
        if ($userNana) {
            Pembayaran::updateOrCreate(
                [
                    'user_id' => $userNana->id,
                    'periode' => 'Januari 2027',
                    'angsuran_ke' => 1,
                ],
                [
                    'kode_transaksi' => 'TRX-'.date('Ymd').'-0099',
                    'nama_tagihan' => 'Tahap 1',
                    'angsuran_ke' => 1,
                    'nominal' => 2500000.00,
                    'status' => 'belum_lunas',
                    'metode_pembayaran' => null,
                    'tanggal_bayar' => null,
                    'keterangan' => 'Tagihan Asrama Periode Januari 2027',
                ]
            );
        }
    }
}
