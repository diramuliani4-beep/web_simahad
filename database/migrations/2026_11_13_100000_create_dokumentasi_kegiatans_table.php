<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dokumentasi kegiatan pembelajaran yang diunggah Guru.
 *
 * Foto disimpan pada disk lokal dan disajikan lewat route tersendiri
 * (lihat route dokumentasi.foto) agar tetap terlindungi autentikasi.
 * Musyrifah dan Pimpinan hanya dapat melihat, tanpa akses mengubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('dokumentasi_kegiatans')) {
            Schema::create('dokumentasi_kegiatans', function (Blueprint $table) {
                $table->id();

                $table->foreignId('guru_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->string('judul', 180);
                $table->string('kelompok', 60)->nullable();
                $table->date('tanggal');
                $table->text('keterangan')->nullable();
                $table->string('foto_path')->nullable();
                $table->string('foto_nama')->nullable();

                $table->timestamps();

                $table->index(['guru_id', 'tanggal'], 'dokumentasi_guru_tanggal_idx');
            });
        }
    }

    public function down(): void
    {
        // Penghapusan tabel tidak dilakukan: kebijakan proyek hanya
        // mengizinkan perubahan skema yang bersifat menambah.
    }
};
