<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan seluruh pengajuan surat & perizinan mahasiswa
     * (izin kegiatan, izin pulang mingguan, dan surat pulang semester).
     */
    public function up(): void
    {
        if (Schema::hasTable('surat_perizinans')) {
            return;
        }

        Schema::create('surat_perizinans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // kegiatan_fakultas | kegiatan_kampus | pulang_mingguan | surat_pulang_semester
            $table->string('jenis', 30);
            // menunggu | disetujui | ditolak | perlu_perbaikan | selesai | dibatalkan
            $table->string('status', 30)->default('menunggu');

            // Detail izin kegiatan.
            $table->string('nama_kegiatan')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('lokasi')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();

            // Periode izin: tanggal berangkat/awal kegiatan sampai tanggal kembali/selesai.
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->unsignedSmallInteger('durasi_hari')->nullable();

            $table->text('alasan')->nullable();

            // Dokumen pendukung (surat resmi fakultas/prodi/penyelenggara).
            $table->string('dokumen_path')->nullable();
            $table->string('dokumen_nama')->nullable();

            // Surat pulang semester: nomor surat dan berkas bertanda tangan orang tua.
            $table->string('nomor_surat', 60)->nullable();
            $table->string('surat_tertanda_path')->nullable();
            $table->string('surat_tertanda_nama')->nullable();
            $table->timestamp('surat_diunggah_pada')->nullable();

            // Keputusan Musyrifah.
            $table->foreignId('keputusan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('keputusan_nama')->nullable();
            $table->timestamp('keputusan_pada')->nullable();
            $table->text('catatan_musyrifah')->nullable();

            // Pembatalan oleh mahasiswa mengembalikan slot kuota izin pulang.
            $table->timestamp('dibatalkan_pada')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'jenis', 'status']);
            $table->index(['jenis', 'tanggal_mulai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_perizinans');
    }
};
