<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pertemuan pembelajaran 1-16 untuk setiap mata pelajaran, kelompok, dan periode.
 *
 * Skema ini melanjutkan sistem pembelajaran yang sudah ada: absensi tetap
 * disimpan pada absensi_pembelajarans dan tugas tetap pada tabel tugas.
 * Migrasi hanya menambah tabel/kolom baru agar data lama tidak berubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pertemuan_pembelajarans')) {
            Schema::create('pertemuan_pembelajarans', function (Blueprint $table) {
                $table->id();

                $table->foreignId('jadwal_id')
                    ->nullable()
                    ->constrained('jadwal_pembelajarans')
                    ->nullOnDelete();

                $table->foreignId('mata_pelajaran_id')
                    ->constrained('mata_pelajarans')
                    ->cascadeOnDelete();

                $table->foreignId('guru_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->string('kelompok', 60);
                $table->string('periode', 100);

                // Nomor pertemuan 1-16, terpisah untuk setiap mata pelajaran,
                // kelompok, dan periode.
                $table->unsignedSmallInteger('pertemuan_ke');

                $table->date('tanggal');
                $table->text('materi')->nullable();
                $table->string('materi_path')->nullable();
                $table->string('materi_nama')->nullable();

                // berlangsung | selesai
                $table->string('status', 20)->default('berlangsung');
                $table->timestamp('dimulai_pada')->nullable();
                $table->timestamp('selesai_pada')->nullable();

                $table->timestamps();

                $table->unique(
                    ['mata_pelajaran_id', 'kelompok', 'periode', 'pertemuan_ke'],
                    'pertemuan_pembelajaran_unik'
                );
                $table->index(['guru_id', 'status']);
            });
        }

        if (Schema::hasTable('absensi_pembelajarans')
            && ! Schema::hasColumn('absensi_pembelajarans', 'pertemuan_id')) {
            Schema::table('absensi_pembelajarans', function (Blueprint $table) {
                $table->unsignedBigInteger('pertemuan_id')->nullable()->after('jadwal_id');
                $table->index('pertemuan_id', 'absensi_pertemuan_idx');
            });

            // Satu baris absensi untuk setiap mahasiswa pada satu pertemuan.
            Schema::table('absensi_pembelajarans', function (Blueprint $table) {
                $table->unique(['pertemuan_id', 'user_id'], 'absensi_pertemuan_user_unik');
            });
        }

        if (Schema::hasTable('tugas') && ! Schema::hasColumn('tugas', 'pertemuan_id')) {
            Schema::table('tugas', function (Blueprint $table) {
                $table->unsignedBigInteger('pertemuan_id')->nullable()->after('kelompok');
                $table->index('pertemuan_id', 'tugas_pertemuan_idx');
            });
        }
    }

    public function down(): void
    {
        // Penghapusan tabel/kolom tidak dilakukan: kebijakan proyek hanya
        // mengizinkan perubahan skema yang bersifat menambah.
    }
};
