<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pengumpulan tugas oleh mahasiswa. Satu mahasiswa hanya memiliki
     * satu baris pengumpulan per tugas sehingga tidak terjadi duplikasi
     * saat mengunggah ulang.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengumpulan_tugas')) {
            return;
        }

        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tugas_id')
                ->constrained('tugas')
                ->cascadeOnDelete();

            // Mahasiswa pengumpul tugas.
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('file_path');
            $table->string('file_nama');
            $table->dateTime('dikumpulkan_pada');
            $table->text('keterangan')->nullable();

            // Nilai hanya diisi Guru dan baru terlihat mahasiswa setelah dipublikasikan.
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->text('komentar_guru')->nullable();
            $table->foreignId('dinilai_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('dinilai_pada')->nullable();
            $table->timestamp('nilai_dipublikasikan_pada')->nullable();

            $table->timestamps();

            $table->unique(['tugas_id', 'user_id']);
            $table->index(['user_id', 'nilai_dipublikasikan_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
