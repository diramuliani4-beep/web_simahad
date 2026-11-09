<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Konfigurasi umum aplikasi: identitas sistem, preferensi notifikasi,
     * dan durasi sesi login. Hanya ada satu baris pengaturan aktif.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengaturan_sistems')) {
            return;
        }

        Schema::create('pengaturan_sistems', function (Blueprint $table) {
            $table->id();

            $table->string('nama_sistem', 60)->default('SIMAHAD');
            $table->string('nama_institusi', 150)
                ->default('Ma’had Al-Jami’ah Tuanku Imam Bonjol');

            // Preferensi notifikasi per kategori kegiatan.
            $table->boolean('notifikasi_pengumuman')->default(true);
            $table->boolean('notifikasi_pembelajaran')->default(true);
            $table->boolean('notifikasi_pembayaran')->default(true);

            // Batas waktu sesi login dalam menit.
            $table->unsignedSmallInteger('durasi_sesi')->default(120);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sistems');
    }
};
