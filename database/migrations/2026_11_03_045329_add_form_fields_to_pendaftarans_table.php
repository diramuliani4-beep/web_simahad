<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {

            // Data tambahan mahasiswa
            $table->string('no_wa', 20)->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('nama_ortu')->nullable();
            $table->string('no_hp_ortu', 20)->nullable();

            // Kategori pendaftaran
            $table->enum('kategori', [
                'umum',
                'kip',
            ])->nullable();

            // Data akademik tambahan
            $table->string('tahun_angkatan', 4)->nullable();

            // Berkas pendaftaran
            $table->string('bukti_penerimaan')->nullable();
            $table->text('alasan_daftar')->nullable();

            // Pembayaran
            $table->decimal('nominal_pembayaran', 15, 2)
                ->nullable();

            $table->string('bukti_pembayaran')->nullable();

            // Konfirmasi data
            $table->boolean('konfirmasi_data')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {

            $table->dropColumn([
                'no_wa',
                'asal_sekolah',
                'nama_ortu',
                'no_hp_ortu',
                'kategori',
                'tahun_angkatan',
                'bukti_penerimaan',
                'alasan_daftar',
                'nominal_pembayaran',
                'bukti_pembayaran',
                'konfirmasi_data',
            ]);
        });
    }
};