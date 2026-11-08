<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tugas pembelajaran yang dibuat Guru untuk satu mata pelajaran
     * dan satu kelompok belajar.
     */
    public function up(): void
    {
        if (Schema::hasTable('tugas')) {
            return;
        }

        Schema::create('tugas', function (Blueprint $table) {
            $table->id();

            // Guru pembuat tugas.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nama_guru')->nullable();
            $table->foreignId('mata_pelajaran_id')
                ->nullable()
                ->constrained('mata_pelajarans')
                ->nullOnDelete();
            $table->string('nama_mata_pelajaran')->nullable();

            // Kelompok belajar tujuan tugas (mis. "Kelompok 1").
            $table->string('kelompok', 60);

            $table->string('judul');
            $table->longText('deskripsi');

            $table->string('lampiran_path')->nullable();
            $table->string('lampiran_nama')->nullable();

            $table->date('tanggal_mulai')->nullable();
            $table->dateTime('batas_pengumpulan');

            // draft | dipublikasikan
            $table->string('status', 20)->default('draft');
            $table->timestamp('dipublikasikan_pada')->nullable();

            $table->timestamps();

            $table->index(['kelompok', 'mata_pelajaran_id']);
            $table->index(['status', 'batas_pengumpulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
