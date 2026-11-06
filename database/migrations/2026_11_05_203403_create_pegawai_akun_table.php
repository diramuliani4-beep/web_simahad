
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_akun', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')
                ->unique()
                ->constrained('pegawais')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        // Salin hubungan akun-pegawai yang sudah ada.
        // Data lama di pegawais dan users tidak dihapus.
        DB::table('pegawais')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(100, function ($pegawais) {
                foreach ($pegawais as $pegawai) {
                    DB::table('pegawai_akun')->insertOrIgnore([
                        'pegawai_id' => $pegawai->id,
                        'user_id' => $pegawai->user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_akun');
    }
};
