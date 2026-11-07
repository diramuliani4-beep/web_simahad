<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('pendaftarans', function (Blueprint $table) {
        // Data Diri
        $table->string('email_aktif', 255)->nullable();
        $table->string('ijazah_sma', 255)->nullable();
        $table->string('ktp', 255)->nullable();
        $table->string('kk', 255)->nullable();
    });
}

public function down(): void
{
    Schema::table('pendaftarans', function (Blueprint $table) {
        $table->dropColumn([
            'email_aktif',
            'ijazah_sma',
            'ktp',
            'kk',
        ]);
    });
}
};
