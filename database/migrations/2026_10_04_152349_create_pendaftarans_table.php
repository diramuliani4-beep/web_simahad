<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();

            // Relasi dengan user yang melakukan pendaftaran
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // =========================
            // DATA PRIBADI
            // =========================

            $table->string('nama_lengkap');
            $table->string('nim')->unique();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', [
                'Laki-laki',
                'Perempuan',
            ]);
            $table->string('no_hp');

            // =========================
            // DATA AKADEMIK
            // =========================

            $table->string('program_studi');
            $table->string('fakultas');
            $table->unsignedTinyInteger('semester');

            // =========================
            // STATUS PENDAFTARAN
            // =========================

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
            ])->default('menunggu');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
