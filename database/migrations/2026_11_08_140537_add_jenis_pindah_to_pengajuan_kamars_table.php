
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan data pendukung pengajuan pindah kamar.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('pengajuan_kamars', 'jenis')) {
            Schema::table('pengajuan_kamars', function (Blueprint $table) {
                $table->string('jenis', 20)->default('penempatan')->after('status');
            });
        }

        if (! Schema::hasColumn('pengajuan_kamars', 'kamar_asal_id')) {
            Schema::table('pengajuan_kamars', function (Blueprint $table) {
                $table->foreignId('kamar_asal_id')->nullable()->after('kamar_id');
            });
        }

        if (! Schema::hasColumn('pengajuan_kamars', 'alasan')) {
            Schema::table('pengajuan_kamars', function (Blueprint $table) {
                $table->text('alasan')->nullable()->after('kamar_asal_id');
            });
        }

        if (! Schema::hasColumn('pengajuan_kamars', 'catatan')) {
            Schema::table('pengajuan_kamars', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('alasan');
            });
        }

        if (! Schema::hasColumn('pengajuan_kamars', 'diverifikasi_oleh')) {
            Schema::table('pengajuan_kamars', function (Blueprint $table) {
                $table->foreignId('diverifikasi_oleh')->nullable()->after('catatan');
            });
        }

        // Menambahkan index jika belum tersedia.
        $indexes = collect(Schema::getIndexes('pengajuan_kamars'))
            ->pluck('name')
            ->toArray();

        if (! in_array('pengajuan_kamars_user_id_jenis_status_index', $indexes)) {
            Schema::table('pengajuan_kamars', function (Blueprint $table) {
                $table->index(['user_id', 'jenis', 'status']);
            });
        }

        // Melepas unique user_id jika masih tersedia.
        $uniqueIndexes = collect(Schema::getIndexes('pengajuan_kamars'))
            ->where('unique', true)
            ->pluck('name')
            ->toArray();

        foreach ($uniqueIndexes as $index) {
            $indexColumns = collect(Schema::getIndexes('pengajuan_kamars'))
                ->firstWhere('name', $index)['columns'] ?? [];

            if ($indexColumns === ['user_id']) {
                Schema::table('pengajuan_kamars', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index);
                });
                break;
            }
        }
    }

    public function down(): void
    {
        // Tidak menghapus kolom secara otomatis agar data tetap aman.
    }
};
