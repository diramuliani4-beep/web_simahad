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
        if (! Schema::hasTable('surat_perizinans')) {
            return;
        }

        Schema::table('surat_perizinans', function (Blueprint $table) {
            if (! Schema::hasColumn('surat_perizinans', 'musyrifah_id')) {
                $table->foreignId('musyrifah_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('surat_perizinans', 'transportasi')) {
                $table->string('transportasi')->nullable()->after('lokasi');
            }

            if (! Schema::hasColumn('surat_perizinans', 'keterangan')) {
                $table->string('keterangan')->nullable()->after('alasan');
            }

            if (! Schema::hasColumn('surat_perizinans', 'waktu_keluar')) {
                $table->timestamp('waktu_keluar')->nullable()->after('tanggal_selesai');
            }

            if (! Schema::hasColumn('surat_perizinans', 'waktu_kembali')) {
                $table->timestamp('waktu_kembali')->nullable()->after('waktu_keluar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('surat_perizinans')) {
            return;
        }

        if (Schema::hasColumn('surat_perizinans', 'musyrifah_id')) {
            Schema::table('surat_perizinans', function (Blueprint $table) {
                $table->dropConstrainedForeignId('musyrifah_id');
            });
        }

        Schema::table('surat_perizinans', function (Blueprint $table) {
            foreach (['transportasi', 'keterangan', 'waktu_keluar', 'waktu_kembali'] as $column) {
                if (Schema::hasColumn('surat_perizinans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
