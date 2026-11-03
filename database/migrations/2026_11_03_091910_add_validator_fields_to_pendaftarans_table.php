
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {

            // Akun Musyrifah yang melakukan validasi.
            $table->foreignId('validator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Nama ustazah ketika melakukan validasi.
            $table->string('validator_nama')->nullable();

            // Tanggal dan waktu validasi.
            $table->timestamp('divalidasi_pada')->nullable();

            // Catatan persetujuan atau penolakan.
            $table->text('catatan_validasi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {

            $table->dropForeign(['validator_id']);

            $table->dropColumn([
                'validator_id',
                'validator_nama',
                'divalidasi_pada',
                'catatan_validasi',
            ]);
        });
    }
};
