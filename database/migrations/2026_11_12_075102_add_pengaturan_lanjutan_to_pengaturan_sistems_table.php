<?php

use App\Models\PengaturanSistem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah konfigurasi lanjutan pada baris pengaturan sistem: logo aplikasi,
 * pengaturan SMTP yang dapat diubah Admin, dan retensi berkas backup.
 *
 * Migrasi ini hanya menambah kolom. Setiap kolom diperiksa lebih dahulu agar
 * aman dijalankan pada basis data yang skemanya sudah berkembang.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pengaturan_sistems')) {
            return;
        }

        Schema::table('pengaturan_sistems', function (Blueprint $table) {
            if (! Schema::hasColumn('pengaturan_sistems', 'logo_path')) {
                $table->string('logo_path', 255)->nullable()->after('nama_institusi');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'mail_mode')) {
                $table->string('mail_mode', 20)->default('lokal')->after('durasi_sesi');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'mail_host')) {
                $table->string('mail_host', 255)->nullable()->after('mail_mode');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'mail_port')) {
                $table->unsignedSmallInteger('mail_port')->nullable()->after('mail_host');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'mail_scheme')) {
                $table->string('mail_scheme', 20)->nullable()->after('mail_port');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'mail_username')) {
                $table->string('mail_username', 255)->nullable()->after('mail_scheme');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'mail_password')) {
                $table->text('mail_password')->nullable()->after('mail_username');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'mail_from_address')) {
                $table->string('mail_from_address', 255)->nullable()->after('mail_password');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'mail_from_name')) {
                $table->string('mail_from_name', 255)->nullable()->after('mail_from_address');
            }

            if (! Schema::hasColumn('pengaturan_sistems', 'backup_retensi')) {
                $table->unsignedTinyInteger('backup_retensi')->default(5)->after('mail_from_name');
            }
        });

        // Cache pengaturan lama belum memuat kolom baru, sehingga dibuang agar
        // nilai default kolom langsung terbaca oleh aplikasi.
        Cache::forget(PengaturanSistem::KUNCI_CACHE);
    }

    public function down(): void
    {
        // Penghapusan kolom tidak dilakukan: kebijakan proyek hanya mengizinkan
        // perubahan skema yang bersifat menambah.
    }
};
