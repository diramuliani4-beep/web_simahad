<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Melengkapi kolom pengajuan kamar pada basis data yang dibuat sebelum
     * migration create_pengajuan_kamars_table diperbaiki.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pengajuan_kamars') || Schema::hasColumn('pengajuan_kamars', 'user_id')) {
            return;
        }

        Schema::table('pengajuan_kamars', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('kamar_id')->nullable()->after('pendaftaran_id')->constrained()->nullOnDelete();
            $table->string('status', 20)->default('menunggu')->after('kamar_id');
            $table->unique('user_id');
            $table->index(['kamar_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('pengajuan_kamars', 'user_id')) {
            return;
        }

        Schema::table('pengajuan_kamars', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropIndex(['kamar_id', 'status']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('pendaftaran_id');
            $table->dropConstrainedForeignId('kamar_id');
            $table->dropColumn('status');
        });
    }
};
