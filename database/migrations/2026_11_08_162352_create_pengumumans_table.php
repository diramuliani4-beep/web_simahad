<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan pengumuman Ma'had yang dikelola Musyrifah dan
     * ditampilkan kepada mahasiswa ketika sudah dipublikasikan.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengumumans')) {
            return;
        }

        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();

            // Penulis pengumuman (akun Musyrifah). Pengumuman tetap tersimpan
            // walaupun akun penulisnya dihapus.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('penulis_nama')->nullable();

            $table->string('judul');
            // Akademik | Kegiatan Ma'had | Pembayaran | Peraturan | Informasi Umum | kategori kustom
            $table->string('kategori', 60);
            $table->longText('isi');

            // Berkas pendukung opsional.
            $table->string('gambar_path')->nullable();
            $table->string('gambar_nama')->nullable();
            $table->string('dokumen_path')->nullable();
            $table->string('dokumen_nama')->nullable();

            $table->date('tanggal_publikasi');
            // draft | dipublikasikan
            $table->string('status', 20)->default('draft');
            $table->timestamp('dipublikasikan_pada')->nullable();

            $table->timestamps();

            $table->index(['status', 'tanggal_publikasi']);
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};
