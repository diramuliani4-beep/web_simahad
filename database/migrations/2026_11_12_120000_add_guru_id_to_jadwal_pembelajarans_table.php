<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tautan penugasan jadwal ke akun Guru.
 *
 * Musyrifah menetapkan Guru pengampu melalui Manajemen Pembelajaran. Sebelumnya
 * hanya nama guru yang disimpan sebagai teks, sehingga penugasan tidak dapat
 * dipastikan milik satu akun Guru. Kolom guru_id melengkapi jadwal yang sudah
 * ada tanpa mengubah data lama: baris lama tetap memakai pencocokan nama.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jadwal_pembelajarans')
            || Schema::hasColumn('jadwal_pembelajarans', 'guru_id')) {
            return;
        }

        Schema::table('jadwal_pembelajarans', function (Blueprint $table) {
            $table->foreignId('guru_id')
                ->nullable()
                ->after('guru_pengampu')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['guru_id', 'periode'], 'jadwal_guru_periode_idx');
        });
    }

    public function down(): void
    {
        // Penghapusan kolom tidak dilakukan: kebijakan proyek hanya
        // mengizinkan perubahan skema yang bersifat menambah.
    }
};
