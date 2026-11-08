<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Notifikasi terintegrasi untuk Guru dan Mahasiswa. Kolom "kunci"
     * dipakai sebagai penanda unik agar pengingat yang dibangkitkan
     * berulang tidak membuat notifikasi ganda.
     */
    public function up(): void
    {
        if (Schema::hasTable('notifikasis')) {
            return;
        }

        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // tugas_baru | tenggat_tugas | pengumpulan_baru | nilai_dipublikasikan
            // pengumuman_baru | jadwal_berubah | pembayaran
            $table->string('tipe', 40);
            $table->string('judul');
            $table->text('pesan')->nullable();
            $table->string('url')->nullable();
            $table->string('kunci', 160)->nullable();
            $table->timestamp('dibaca_pada')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'kunci']);
            $table->index(['user_id', 'dibaca_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
