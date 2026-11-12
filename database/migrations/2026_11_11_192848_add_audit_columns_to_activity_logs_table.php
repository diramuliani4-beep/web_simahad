<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom tambahan untuk audit trail aktivitas penting.
     *
     * Kolom lama (activity, description) tetap dipertahankan agar halaman
     * log aktivitas yang sudah berjalan tidak rusak. Kolom baru hanya
     * menambah konteks: modul, jenis aksi, objek terdampak, serta nilai
     * sebelum dan sesudah perubahan.
     */
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_logs', 'modul')) {
                $table->string('modul', 40)->nullable()->after('user_role');
            }

            if (! Schema::hasColumn('activity_logs', 'aksi')) {
                $table->string('aksi', 40)->nullable()->after('modul');
            }

            if (! Schema::hasColumn('activity_logs', 'objek')) {
                $table->string('objek')->nullable()->after('aksi');
            }

            if (! Schema::hasColumn('activity_logs', 'objek_id')) {
                $table->unsignedBigInteger('objek_id')->nullable()->after('objek');
            }

            if (! Schema::hasColumn('activity_logs', 'data_sebelum')) {
                $table->json('data_sebelum')->nullable()->after('objek_id');
            }

            if (! Schema::hasColumn('activity_logs', 'data_sesudah')) {
                $table->json('data_sesudah')->nullable()->after('data_sebelum');
            }
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('modul');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['modul']);

            $table->dropColumn([
                'modul',
                'aksi',
                'objek',
                'objek_id',
                'data_sebelum',
                'data_sesudah',
            ]);
        });
    }
};
