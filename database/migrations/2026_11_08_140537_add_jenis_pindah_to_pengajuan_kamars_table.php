<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan data pendukung pengajuan pindah kamar pada tabel pengajuan
     * yang sudah ada (tanpa membuat tabel baru).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('pengajuan_kamars', 'jenis')) {
            Schema::table('pengajuan_kamars', function (Blueprint $table) {
                $table->string('jenis', 20)->default('penempatan')->after('status');
                $table->foreignId('kamar_asal_id')->nullable()->after('kamar_id');
                $table->text('alasan')->nullable()->after('kamar_asal_id');
                $table->text('catatan')->nullable()->after('alasan');
                $table->foreignId('diverifikasi_oleh')->nullable()->after('catatan');
                $table->index(['user_id', 'jenis', 'status']);
            });
        }

        // Satu mahasiswa kini boleh memiliki riwayat pengajuan lebih dari satu
        // (penempatan awal dan pindah kamar), sehingga unique(user_id) dilepas.
        try {
            Schema::table('pengajuan_kamars', function (Blueprint $table) {
                $table->dropUnique(['user_id']);
            });
        } catch (Throwable) {
            // Indeks unik memang sudah tidak ada pada basis data ini.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_kamars', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'jenis', 'status']);
            $table->dropColumn(['jenis', 'kamar_asal_id', 'alasan', 'catatan', 'diverifikasi_oleh']);
        });
    }
};
