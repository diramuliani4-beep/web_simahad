<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_kamars', function (Blueprint $table) {
            $table->foreignId('persetujuan_asal_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('persetujuan_asal_pada')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_kamars', function (Blueprint $table) {
            $table->dropConstrainedForeignId('persetujuan_asal_oleh');
            $table->dropColumn('persetujuan_asal_pada');
        });
    }
};