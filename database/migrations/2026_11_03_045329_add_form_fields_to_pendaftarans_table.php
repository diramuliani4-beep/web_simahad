
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom formulir pendaftaran mahasiswa.
     */
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {

            // ========================================
            // 1. RELASI AKUN MAHASISWA
            // ========================================

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            // ========================================
            // 2. DATA DIRI MAHASISWA
            // ========================================

            $table->string('nama_lengkap')->nullable();

            $table->enum('jenis_kelamin', [
                'Laki-laki',
                'Perempuan',
            ])->nullable();

            $table->string('no_wa', 20)->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('nama_ortu')->nullable();
            $table->string('no_hp_ortu', 20)->nullable();

            // Kategori pendaftaran: umum atau KIP.
            $table->enum('kategori', [
                'umum',
                'kip',
            ])->nullable();

            // ========================================
            // 3. DATA AKADEMIK MAHASISWA
            // ========================================

            $table->string('tahun_angkatan', 4)->nullable();
            $table->string('fakultas')->nullable();
            $table->string('program_studi')->nullable();

            // Menyimpan lokasi file bukti penerimaan.
            $table->string('bukti_penerimaan')->nullable();

            // ========================================
            // 4. PERSYARATAN DAN PEMBAYARAN
            // ========================================

            $table->text('alasan_daftar')->nullable();

            $table->decimal('nominal_pembayaran', 15, 2)
                ->nullable();

            // Menyimpan lokasi file bukti pembayaran.
            $table->string('bukti_pembayaran')->nullable();

            $table->boolean('konfirmasi_data')
                ->default(false);

            // ========================================
            // 5. STATUS PENDAFTARAN
            // ========================================

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
            ])->default('menunggu');
        });
    }

    /**
     * Membatalkan kolom yang ditambahkan migration ini.
     */
    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {

            // Hapus foreign key sebelum kolom user_id.
            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'user_id',
                'nama_lengkap',
                'jenis_kelamin',
                'no_wa',
                'asal_sekolah',
                'nama_ortu',
                'no_hp_ortu',
                'kategori',
                'tahun_angkatan',
                'fakultas',
                'program_studi',
                'bukti_penerimaan',
                'alasan_daftar',
                'nominal_pembayaran',
                'bukti_pembayaran',
                'konfirmasi_data',
                'status',
            ]);
        });
    }
};
