<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penempatan_kelompok_belajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('kelompok_belajar_id')
                ->constrained('kelompok_belajars')
                ->cascadeOnDelete();
            $table->string('status')->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamp('ditugaskan_pada')->nullable();
            $table->timestamp('dicabut_pada')->nullable();
            $table->timestamps();

            $table->index(['kelompok_belajar_id', 'status'], 'penempatan_kelompok_status_idx');
        });

        $this->pindahkanPenempatanLegacy();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Global placements cannot be safely converted back to subject-scoped
        // memberships, so the table and its data are intentionally retained.
    }

    /**
     * Copy the latest active placement without changing legacy memberships.
     */
    private function pindahkanPenempatanLegacy(): void
    {
        DB::transaction(function (): void {
            DB::table('kelompok_belajar_anggotas as anggota')
                ->select('anggota.*')
                ->whereRaw('LOWER(anggota.status) = ?', ['aktif'])
                ->whereExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('pendaftarans as pendaftaran')
                        ->whereColumn('pendaftaran.user_id', 'anggota.user_id')
                        ->where('pendaftaran.status', 'disetujui');
                })
                ->whereExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('users')
                        ->whereColumn('users.id', 'anggota.user_id')
                        ->whereRaw('LOWER(users.role) = ?', ['mahasiswa']);
                })
                ->whereNotExists(function ($query): void {
                    $query->selectRaw('1')
                        ->from('kelompok_belajar_anggotas as lebih_baru')
                        ->whereColumn('lebih_baru.user_id', 'anggota.user_id')
                        ->whereRaw('LOWER(lebih_baru.status) = ?', ['aktif'])
                        ->where(function ($newer): void {
                            $newer->whereColumn('lebih_baru.updated_at', '>', 'anggota.updated_at')
                                ->orWhere(function ($same): void {
                                    $same->whereColumn('lebih_baru.updated_at', 'anggota.updated_at')
                                        ->whereColumn('lebih_baru.id', '>', 'anggota.id');
                                });
                        });
                })
                ->chunkById(500, function ($rows): void {
                    foreach ($rows as $row) {
                        $ditugaskan = $row->updated_at ?: $row->created_at ?: now();
                        $keterangan = trim((string) $row->keterangan);
                        $catatanMigrasi = 'Migrasi dari keanggotaan per mata pelajaran #'.$row->id;

                        DB::table('penempatan_kelompok_belajars')->insertOrIgnore([
                            'user_id' => $row->user_id,
                            'kelompok_belajar_id' => $row->kelompok_belajar_id,
                            'status' => 'aktif',
                            'keterangan' => $keterangan !== ''
                                ? $keterangan.' · '.$catatanMigrasi
                                : $catatanMigrasi,
                            'ditugaskan_pada' => $ditugaskan,
                            'dicabut_pada' => null,
                            'created_at' => $ditugaskan,
                            'updated_at' => now(),
                        ]);
                    }
                }, 'anggota.id', 'id');
        });
    }
};
