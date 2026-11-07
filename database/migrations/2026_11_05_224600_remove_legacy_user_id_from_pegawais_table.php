<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pegawais') || ! Schema::hasTable('pegawai_akun')) {
            throw new RuntimeException('Tabel pegawais dan pegawai_akun harus ada terlebih dahulu.');
        }

        // Idempotent: migration terdahulu mungkin telah memindahkan kolom ini.
        if (! Schema::hasColumn('pegawais', 'user_id')) {
            return;
        }

        // Amankan seluruh hubungan lama sebelum menghapus kolom. Jangan
        // mencocokkan akun berdasarkan nama/email karena berisiko salah orang.
        DB::table('pegawais')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(100, function ($records): void {
                foreach ($records as $record) {
                    $byPegawai = DB::table('pegawai_akun')
                        ->where('pegawai_id', $record->id)->first();
                    $byUser = DB::table('pegawai_akun')
                        ->where('user_id', $record->user_id)->first();

                    if ($byPegawai || $byUser) {
                        if (! $byPegawai || ! $byUser
                            || (int) $byPegawai->user_id !== (int) $record->user_id
                            || (int) $byUser->pegawai_id !== (int) $record->id) {
                            throw new RuntimeException(
                                'Relasi lama bertentangan untuk pegawai ID '.$record->id
                                .'. Periksa isi pegawai_akun, jangan hapus kolom sebelum konflik ditangani.'
                            );
                        }

                        continue;
                    }

                    DB::table('pegawai_akun')->insert([
                        'pegawai_id' => $record->id,
                        'user_id' => $record->user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        // Ambil nama FK dari MySQL, termasuk jika namanya bukan konvensi Laravel.
        if (DB::getDriverName() === 'mysql') {
            $foreignKeys = DB::select(<<<'SQL'
                SELECT DISTINCT CONSTRAINT_NAME AS nama_fk
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'pegawais'
                  AND COLUMN_NAME = 'user_id'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            SQL);

            foreach ($foreignKeys as $fk) {
                Schema::table('pegawais', function (Blueprint $table) use ($fk): void {
                    $table->dropForeign((string) $fk->nama_fk);
                });
            }
        }

        Schema::table('pegawais', function (Blueprint $table): void {
            if (DB::getDriverName() === 'sqlite') {
                $table->dropForeign(['user_id']);
                $table->dropUnique(['user_id']);
            }
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pegawais') || Schema::hasColumn('pegawais', 'user_id')) {
            return;
        }

        // Nullable saat rollback agar pegawai mandiri tidak kehilangan biodata.
        Schema::table('pegawais', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->unique()
                ->constrained('users')->restrictOnDelete();
        });

        DB::table('pegawai_akun')->orderBy('id')->chunkById(100, function ($links): void {
            foreach ($links as $link) {
                DB::table('pegawais')->where('id', $link->pegawai_id)
                    ->update(['user_id' => $link->user_id]);
            }
        });
    }
};
