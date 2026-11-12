<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar laporan Ma'had (register laporan).
     *
     * Laporan tidak menyimpan salinan angka karena isinya selalu dibangkitkan
     * dari data operasional terkini melalui LaporanMahadService. Tabel ini
     * menyimpan identitas, periode, sumber (lantai/unit), status, dan versi
     * agar Pimpinan dapat menelusuri siapa yang membuat dan menerbitkannya.
     */
    public function up(): void
    {
        if (Schema::hasTable('laporan_mahads')) {
            return;
        }

        Schema::create('laporan_mahads', function (Blueprint $table) {
            $table->id();

            // mahasiswa | kamar | pembayaran | pembelajaran | perizinan | kehadiran | pendaftaran
            $table->string('jenis', 40);
            $table->string('judul');
            $table->string('periode', 7);
            $table->unsignedTinyInteger('lantai')->nullable();
            $table->string('unit', 80)->nullable();

            // sistem | musyrifah
            $table->string('sumber', 20)->default('sistem');

            // draft | diterbitkan | diarsipkan
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('versi')->default(1);

            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pembuat_nama')->nullable();
            $table->string('pembuat_role', 30)->nullable();

            $table->timestamp('diterbitkan_pada')->nullable();
            $table->foreignId('diterbitkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diarsipkan_pada')->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index(['jenis', 'periode']);
            $table->index(['lantai', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_mahads');
    }
};
