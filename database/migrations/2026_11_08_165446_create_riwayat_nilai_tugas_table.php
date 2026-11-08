<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat perubahan nilai tugas. Setiap penyimpanan nilai baru
     * mencatat nilai lama agar penilaian dapat ditelusuri.
     */
    public function up(): void
    {
        if (Schema::hasTable('riwayat_nilai_tugas')) {
            return;
        }

        Schema::create('riwayat_nilai_tugas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengumpulan_tugas_id')
                ->constrained('pengumpulan_tugas')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('nilai_lama')->nullable();
            $table->unsignedTinyInteger('nilai_baru')->nullable();
            $table->text('komentar_lama')->nullable();
            $table->text('komentar_baru')->nullable();

            $table->foreignId('diubah_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('nama_pengubah')->nullable();

            $table->timestamps();

            $table->index(['pengumpulan_tugas_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_nilai_tugas');
    }
};
