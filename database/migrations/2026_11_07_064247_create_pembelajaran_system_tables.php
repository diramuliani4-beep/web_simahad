<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->string('kategori')->nullable();
            $table->unsignedInteger('target_pertemuan')->default(16);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('kelompok_belajars', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelompok');
            $table->unsignedInteger('nomor_kelompok');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('kelompok_pengajars', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengajar');
            $table->string('program');
            $table->string('kelompok_teks');
            $table->string('jumlah_tm')->default('32 TM (2 kelas)');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('jadwal_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajarans')->nullOnDelete();
            $table->string('nama_mata_pelajaran');
            $table->string('kelompok');
            $table->string('guru_pengampu');
            $table->string('periode')->default('Semester Ganjil 2026/2027');
            $table->string('hari');
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->string('ruangan')->default('Ruang Kelas');
            $table->timestamps();
        });

        Schema::create('absensi_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal_pembelajarans')->nullOnDelete();
            $table->string('kelompok')->nullable();
            $table->unsignedSmallInteger('pertemuan_ke');
            $table->date('tanggal');
            $table->string('status')->default('hadir'); // hadir, izin, sakit, alpa
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'mata_pelajaran_id', 'pertemuan_ke'], 'absensi_user_mapel_ptm_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_pembelajarans');
        Schema::dropIfExists('jadwal_pembelajarans');
        Schema::dropIfExists('kelompok_pengajars');
        Schema::dropIfExists('kelompok_belajars');
        Schema::dropIfExists('mata_pelajarans');
    }
};
