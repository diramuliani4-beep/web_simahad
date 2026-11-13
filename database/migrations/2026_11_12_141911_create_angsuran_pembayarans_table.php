<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal angsuran per periode pembayaran. Maksimal tiga angsuran per
     * tahun; angsuran kedua dan ketiga diatur Musyrifah (nominal, jatuh
     * tempo, dan jadwal pengingat).
     */
    public function up(): void
    {
        if (Schema::hasTable('angsuran_pembayarans')) {
            return;
        }

        Schema::create('angsuran_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 60);
            $table->unsignedTinyInteger('angsuran_ke');
            $table->string('nama_tahap', 80);
            $table->decimal('nominal', 15, 2);
            $table->date('jatuh_tempo')->nullable();
            $table->unsignedSmallInteger('reminder_hari')->default(3);
            $table->boolean('is_aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['periode', 'angsuran_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angsuran_pembayarans');
    }
};
