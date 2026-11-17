
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom Jabatan dan Kelompok yang Diajar
     * ke tabel pegawais yang sudah ada.
     */
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('jabatan', 150)->nullable();

            $table->string('kelompok_yang_diajar', 255)->nullable();
        });
    }

    /**
     * Mengembalikan struktur tabel jika migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn([
                'jabatan',
                'kelompok_yang_diajar',
            ]);
        });
    }
};
