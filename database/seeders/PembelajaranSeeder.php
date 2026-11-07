<?php

namespace Database\Seeders;

use App\Models\AbsensiPembelajaran;
use App\Models\JadwalPembelajaran;
use App\Models\KelompokBelajar;
use App\Models\KelompokPengajar;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class PembelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data 5 Mata Pelajaran
        $mapels = [
            [
                'nama' => 'Pembelajaran Kitab Kuning',
                'kode' => 'PKK-01',
                'kategori' => 'Keislaman & Turots',
                'target_pertemuan' => 16,
                'deskripsi' => 'Kajian mendalam teks turots, fiqih, dan gramatika bahasa arab kitab kuning.',
            ],
            [
                'nama' => 'Penguatan Keterampilan Khusus Wawasan Interdisipliner dan Moderasi Beragama',
                'kode' => 'PKK-WIMB',
                'kategori' => 'Wawasan Kebangsaan & Moderasi',
                'target_pertemuan' => 16,
                'deskripsi' => 'Penguatan moderasi beragama, toleransi, dan integrasi keilmuan interdisipliner.',
            ],
            [
                'nama' => 'Penguatan Keterampilan Khusus',
                'kode' => 'PKK-02',
                'kategori' => 'Keterampilan & Kepemimpinan',
                'target_pertemuan' => 16,
                'deskripsi' => 'Pengembangan soft skill, kepemimpinan asrama, dan manajemen diri santri mahad.',
            ],
            [
                'nama' => 'Penguatan Bahasa Asing',
                'kode' => 'PBA-01',
                'kategori' => 'Bahasa & Komunikasi',
                'target_pertemuan' => 16,
                'deskripsi' => 'Peningkatan kemahiran muhadatsah bahasa Arab dan bahasa Inggris percakapan aktif.',
            ],
            [
                'nama' => 'Penguatan Keterampilan Khusus (Karakter & Dakwah)',
                'kode' => 'PKK-03',
                'kategori' => 'Karakter & Pengabdian',
                'target_pertemuan' => 16,
                'deskripsi' => 'Pembinaan akhlak karimah, retorika dakwah, dan keterampilan sosial kemasyarakatan.',
            ],
        ];

        $mapelModels = [];
        foreach ($mapels as $m) {
            $mapelModels[$m['nama']] = MataPelajaran::updateOrCreate(
                ['nama' => $m['nama']],
                $m
            );
        }

        // 2. Data 6 Kelompok Belajar
        for ($i = 1; $i <= 6; $i++) {
            KelompokBelajar::updateOrCreate(
                ['nomor_kelompok' => $i],
                [
                    'nama_kelompok' => 'Kelompok ' . $i,
                    'keterangan' => 'Kelompok Belajar ' . $i . ' Mahad Al-Jamiah',
                ]
            );
        }

        // 3. Data 8 Guru/Pengajar Pengampu Kelompok Belajar
        $pengajars = [
            [
                'nama_pengajar' => 'Dr. Zulkifli, M.A.',
                'program' => 'Pembelajaran Kitab Kuning',
                'kelompok_teks' => '4 dan 5',
                'jumlah_tm' => '32 TM (2 kelas)',
                'keterangan' => 'Pengampu Kitab Fathul Qorib & Ta\'lim Muta\'allim',
            ],
            [
                'nama_pengajar' => 'Ilya Husna, M.A.',
                'program' => 'Pembelajaran Kitab Kuning',
                'kelompok_teks' => '1 dan 2',
                'jumlah_tm' => '32 TM (2 kelas)',
                'keterangan' => 'Pengampu Kitab Bidayatul Hidayah',
            ],
            [
                'nama_pengajar' => 'Latania Fizikri, M.Ag.',
                'program' => 'Penguatan Keterampilan Khusus Wawasan Interdisipliner dan Moderasi Beragama',
                'kelompok_teks' => '6 dan 3',
                'jumlah_tm' => '32 TM (2 kelas)',
                'keterangan' => 'Fokus kajian Moderasi Beragama & Wasathiyah Islam',
            ],
            [
                'nama_pengajar' => 'Melia Kantosa, M.H.',
                'program' => 'Penguatan Keterampilan Khusus',
                'kelompok_teks' => '4 dan 5',
                'jumlah_tm' => '32 TM (2 kelas)',
                'keterangan' => 'Fokus kajian Kepemimpinan & Hukum Islam Terapan',
            ],
            [
                'nama_pengajar' => 'Kenny Andika, M.Hum.',
                'program' => 'Penguatan Bahasa Asing',
                'kelompok_teks' => '1 dan 2',
                'jumlah_tm' => '32 TM (2 kelas)',
                'keterangan' => 'Fokus Bahasa Inggris Aktif & Academic Speaking',
            ],
            [
                'nama_pengajar' => 'Husnul Khatimah, M.Hum.',
                'program' => 'Penguatan Bahasa Asing',
                'kelompok_teks' => '5 dan 5',
                'jumlah_tm' => '32 TM (2 kelas)',
                'keterangan' => 'Fokus Bahasa Arab Muhadatsah & Qowaid',
            ],
            [
                'nama_pengajar' => 'Monalisa, S.Psi.',
                'program' => 'Penguatan Keterampilan Khusus',
                'kelompok_teks' => '2 dan 3',
                'jumlah_tm' => '32 TM (2 kelas)',
                'keterangan' => 'Fokus Konseling, Pengembangan Kepribadian & Self-Management',
            ],
            [
                'nama_pengajar' => 'Khaira Ummah, S.Pd.',
                'program' => 'Penguatan Bahasa Asing',
                'kelompok_teks' => '5 dan 6',
                'jumlah_tm' => '32 TM (2 kelas)',
                'keterangan' => 'Fokus English Vocabulary & Daily Conversation',
            ],
        ];

        foreach ($pengajars as $p) {
            KelompokPengajar::updateOrCreate(
                [
                    'nama_pengajar' => $p['nama_pengajar'],
                    'program' => $p['program'],
                ],
                $p
            );
        }

        // 4. Data Jadwal Pembelajaran
        $jadwals = [
            [
                'nama_mata_pelajaran' => 'Pembelajaran Kitab Kuning',
                'kelompok' => 'Kelompok 1',
                'guru_pengampu' => 'Ilya Husna, M.A.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Ruang Teori 1',
            ],
            [
                'nama_mata_pelajaran' => 'Pembelajaran Kitab Kuning',
                'kelompok' => 'Kelompok 2',
                'guru_pengampu' => 'Ilya Husna, M.A.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Senin',
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:30',
                'ruangan' => 'Ruang Teori 2',
            ],
            [
                'nama_mata_pelajaran' => 'Pembelajaran Kitab Kuning',
                'kelompok' => 'Kelompok 4',
                'guru_pengampu' => 'Dr. Zulkifli, M.A.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Selasa',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Ruang Teori 3',
            ],
            [
                'nama_mata_pelajaran' => 'Pembelajaran Kitab Kuning',
                'kelompok' => 'Kelompok 5',
                'guru_pengampu' => 'Dr. Zulkifli, M.A.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Selasa',
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:30',
                'ruangan' => 'Ruang Teori 4',
            ],
            [
                'nama_mata_pelajaran' => 'Penguatan Bahasa Asing',
                'kelompok' => 'Kelompok 1',
                'guru_pengampu' => 'Kenny Andika, M.Hum.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Rabu',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Laboratorium Bahasa 1',
            ],
            [
                'nama_mata_pelajaran' => 'Penguatan Bahasa Asing',
                'kelompok' => 'Kelompok 2',
                'guru_pengampu' => 'Kenny Andika, M.Hum.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Rabu',
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:30',
                'ruangan' => 'Laboratorium Bahasa 1',
            ],
            [
                'nama_mata_pelajaran' => 'Penguatan Keterampilan Khusus Wawasan Interdisipliner dan Moderasi Beragama',
                'kelompok' => 'Kelompok 3',
                'guru_pengampu' => 'Latania Fizikri, M.Ag.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Kamis',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Aula Ma\'had Lt. 1',
            ],
            [
                'nama_mata_pelajaran' => 'Penguatan Keterampilan Khusus Wawasan Interdisipliner dan Moderasi Beragama',
                'kelompok' => 'Kelompok 6',
                'guru_pengampu' => 'Latania Fizikri, M.Ag.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Kamis',
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:30',
                'ruangan' => 'Aula Ma\'had Lt. 2',
            ],
            [
                'nama_mata_pelajaran' => 'Penguatan Keterampilan Khusus',
                'kelompok' => 'Kelompok 4',
                'guru_pengampu' => 'Melia Kantosa, M.H.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Jumat',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Ruang Diskusi 1',
            ],
            [
                'nama_mata_pelajaran' => 'Penguatan Keterampilan Khusus',
                'kelompok' => 'Kelompok 2',
                'guru_pengampu' => 'Monalisa, S.Psi.',
                'periode' => 'Semester Ganjil 2026/2027',
                'hari' => 'Sabtu',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'ruangan' => 'Ruang Konseling/Pelatihan',
            ],
        ];

        foreach ($jadwals as $j) {
            $mapelRef = $mapelModels[$j['nama_mata_pelajaran']] ?? MataPelajaran::first();
            JadwalPembelajaran::updateOrCreate(
                [
                    'nama_mata_pelajaran' => $j['nama_mata_pelajaran'],
                    'kelompok' => $j['kelompok'],
                    'hari' => $j['hari'],
                    'jam_mulai' => $j['jam_mulai'],
                ],
                array_merge($j, ['mata_pelajaran_id' => $mapelRef?->id])
            );
        }

        // 5. Data Absensi 16 Pertemuan untuk Mahasiswa
        // Mahasiswa 1 (Lulus: hadir 16/16 di seluruh mata pelajaran)
        $userMahasiswa1 = User::where('email', 'mahasiswa@asrama.ac.id')->first();
        // Mahasiswa 2 (Belum Lulus: hadir < 16 pertemuan)
        $userMahasiswa2 = User::where('email', 'nana@gmail.com')->first();
        // Mahasiswa lainnya dari seeder
        $userAhmad = User::where('email', 'ahmad@asrama.ac.id')->first();

        $allMapels = MataPelajaran::all();

        // Seed untuk User 1 (Memenuhi Syarat: 16/16 hadir di semua mapel)
        if ($userMahasiswa1) {
            foreach ($allMapels as $mp) {
                for ($ptm = 1; $ptm <= 16; $ptm++) {
                    AbsensiPembelajaran::updateOrCreate(
                        [
                            'user_id' => $userMahasiswa1->id,
                            'mata_pelajaran_id' => $mp->id,
                            'pertemuan_ke' => $ptm,
                        ],
                        [
                            'kelompok' => 'Kelompok 1',
                            'tanggal' => now()->subWeeks(16 - $ptm)->startOfWeek()->toDateString(),
                            'status' => 'hadir',
                            'catatan' => 'Hadir tepat waktu mengikuti pembelajaran tatap muka.',
                        ]
                    );
                }
            }
        }

        // Seed untuk Ahmad (Memenuhi Syarat: 16/16 hadir di semua mapel)
        if ($userAhmad) {
            foreach ($allMapels as $mp) {
                for ($ptm = 1; $ptm <= 16; $ptm++) {
                    AbsensiPembelajaran::updateOrCreate(
                        [
                            'user_id' => $userAhmad->id,
                            'mata_pelajaran_id' => $mp->id,
                            'pertemuan_ke' => $ptm,
                        ],
                        [
                            'kelompok' => 'Kelompok 1',
                            'tanggal' => now()->subWeeks(16 - $ptm)->startOfWeek()->toDateString(),
                            'status' => 'hadir',
                            'catatan' => 'Hadir dan aktif dalam diskusi.',
                        ]
                    );
                }
            }
        }

        // Seed untuk Nana (Belum Memenuhi Syarat: ada izin dan alpa sehingga tidak mencapai 16 hadir)
        if ($userMahasiswa2) {
            foreach ($allMapels as $idx => $mp) {
                for ($ptm = 1; $ptm <= 16; $ptm++) {
                    $st = 'hadir';
                    $catatan = 'Hadir';
                    if ($ptm === 14) {
                        $st = 'izin';
                        $catatan = 'Izin surat sakit';
                    } elseif ($ptm === 16 && $idx === 0) {
                        $st = 'alpa';
                        $catatan = 'Tanpa keterangan';
                    }

                    AbsensiPembelajaran::updateOrCreate(
                        [
                            'user_id' => $userMahasiswa2->id,
                            'mata_pelajaran_id' => $mp->id,
                            'pertemuan_ke' => $ptm,
                        ],
                        [
                            'kelompok' => 'Kelompok 2',
                            'tanggal' => now()->subWeeks(16 - $ptm)->startOfWeek()->toDateString(),
                            'status' => $st,
                            'catatan' => $catatan,
                        ]
                    );
                }
            }
        }
    }
}

