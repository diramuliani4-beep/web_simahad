<?php

namespace App\Services;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Backup basis data SIMAHAD tanpa ketergantungan biner eksternal.
 *
 * Berkas dump disimpan pada disk "local" (storage/app/private/backups) agar
 * tidak dapat diakses langsung dari web dan hanya dapat diunduh Admin melalui
 * halaman Pengaturan Sistem.
 */
class BackupDatabaseService
{
    /** Disk penyimpanan berkas backup. */
    public const DISK = 'local';

    /** Folder relatif di dalam disk. */
    public const FOLDER = 'backups';

    /** Jumlah baris per perintah INSERT agar berkas tetap wajar. */
    private const BARIS_PER_INSERT = 100;

    /**
     * Membuat berkas backup baru lalu memangkas berkas lama sesuai retensi.
     *
     * @return array{nama: string, path: string, ukuran: int, dibaca: string, tabel: int, waktu: int}
     */
    public function buat(int $retensi): array
    {
        $nama = 'backup-'.now()->format('Ymd-His').'.sql';
        $tabel = 0;

        [$isi, $tabel] = $this->dump();

        $disk = Storage::disk(self::DISK);

        if (! $disk->put(self::FOLDER.'/'.$nama, $isi)) {
            throw new RuntimeException('Berkas backup gagal ditulis ke penyimpanan.');
        }

        $this->pangkas($retensi);

        return $this->info(self::FOLDER.'/'.$nama) + ['tabel' => $tabel];
    }

    /**
     * Daftar berkas backup terbaru lebih dahulu.
     *
     * @return Collection<int, array{nama: string, path: string, ukuran: int, dibaca: string, waktu: int}>
     */
    public function daftar(): Collection
    {
        $disk = Storage::disk(self::DISK);

        return collect($disk->files(self::FOLDER))
            ->filter(fn (string $path): bool => str_ends_with(strtolower($path), '.sql'))
            ->map(fn (string $path): array => $this->info($path))
            // Nama berkas memuat timestamp sehingga urutan nama = urutan waktu.
            ->sortByDesc('nama')
            ->values();
    }

    /** Menghapus satu berkas backup berdasarkan nama berkas yang tervalidasi. */
    public function hapus(string $nama): bool
    {
        return Storage::disk(self::DISK)->delete($this->pathAman($nama));
    }

    /** Path relatif berkas backup untuk diunduh, atau null bila tidak ada. */
    public function pathUnduh(string $nama): ?string
    {
        $path = $this->pathAman($nama);
        $disk = Storage::disk(self::DISK);

        return $disk->exists($path) ? $path : null;
    }

    /** Total ukuran seluruh berkas backup dalam byte. */
    public function totalUkuran(): int
    {
        return (int) $this->daftar()->sum('ukuran');
    }

    /**
     * Membuat dump SQL sesuai driver basis data yang sedang dipakai.
     *
     * @return array{0: string, 1: int}
     */
    private function dump(): array
    {
        $koneksi = DB::connection();

        return match ($koneksi->getDriverName()) {
            'mysql', 'mariadb' => $this->dumpMysql($koneksi),
            'sqlite' => $this->dumpSqlite($koneksi),
            default => throw new RuntimeException(
                'Backup basis data belum mendukung driver "'.$koneksi->getDriverName().'".'
            ),
        };
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function dumpMysql(Connection $koneksi): array
    {
        $pdo = $koneksi->getPdo();

        $tabel = array_map(
            static fn (object|array $baris): string => (string) array_values((array) $baris)[0],
            $koneksi->select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")
        );

        $sql = "-- Backup basis data SIMAHAD\n-- Dibuat: ".now()->toDateTimeString()."\n"
            ."-- Driver: mysql\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tabel as $nama) {
            $definisi = (array) $koneksi->selectOne('SHOW CREATE TABLE `'.$nama.'`');
            $sql .= 'DROP TABLE IF EXISTS `'.$nama."`;\n".array_values($definisi)[1].";\n\n";
            $sql .= $this->insertMysql($koneksi, $pdo, $nama);
        }

        return [$sql."SET FOREIGN_KEY_CHECKS=1;\n", count($tabel)];
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function dumpSqlite(Connection $koneksi): array
    {
        $pdo = $koneksi->getPdo();

        $tabel = $koneksi->select(
            "SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        );

        $sql = "-- Backup basis data SIMAHAD\n-- Dibuat: ".now()->toDateTimeString()."\n"
            ."-- Driver: sqlite\nBEGIN TRANSACTION;\n\n";

        foreach ($tabel as $baris) {
            $nama = (string) $baris->name;
            $sql .= 'DROP TABLE IF EXISTS "'.$nama.'";'."\n".$baris->sql.";\n\n";
            $sql .= $this->insertSqlite($koneksi, $pdo, $nama);
        }

        return [$sql."COMMIT;\n", count($tabel)];
    }

    private function insertMysql(Connection $koneksi, \PDO $pdo, string $tabel): string
    {
        $baris = $koneksi->select('SELECT * FROM `'.$tabel.'`');

        if ($baris === []) {
            return '';
        }

        $kolom = array_keys((array) $baris[0]);
        $daftarKolom = '('.implode(', ', array_map(static fn (string $k): string => '`'.$k.'`', $kolom)).')';
        $sql = '';

        foreach (array_chunk($baris, self::BARIS_PER_INSERT) as $potongan) {
            $nilai = array_map(
                static function (object|array $record) use ($pdo): string {
                    $isi = array_map(
                        static fn (mixed $value): string => $value === null ? 'NULL' : $pdo->quote((string) $value),
                        (array) $record
                    );

                    return '('.implode(', ', $isi).')';
                },
                $potongan
            );

            $sql .= 'INSERT INTO `'.$tabel.'` '.$daftarKolom." VALUES\n".implode(",\n", $nilai).";\n\n";
        }

        return $sql;
    }

    private function insertSqlite(Connection $koneksi, \PDO $pdo, string $tabel): string
    {
        $baris = $koneksi->select('SELECT * FROM "'.$tabel.'"');

        if ($baris === []) {
            return '';
        }

        $kolom = array_keys((array) $baris[0]);
        $daftarKolom = '('.implode(', ', array_map(static fn (string $k): string => '"'.$k.'"', $kolom)).')';
        $sql = '';

        foreach (array_chunk($baris, self::BARIS_PER_INSERT) as $potongan) {
            $nilai = array_map(
                static function (object|array $record) use ($pdo): string {
                    $isi = array_map(
                        static fn (mixed $value): string => $value === null ? 'NULL' : $pdo->quote((string) $value),
                        (array) $record
                    );

                    return '('.implode(', ', $isi).')';
                },
                $potongan
            );

            $sql .= 'INSERT INTO "'.$tabel.'" '.$daftarKolom." VALUES\n".implode(",\n", $nilai).";\n\n";
        }

        return $sql;
    }

    /** Menyisakan hanya sejumlah berkas backup terbaru. */
    private function pangkas(int $retensi): void
    {
        $retensi = max(1, $retensi);
        $disk = Storage::disk(self::DISK);

        $berkas = collect($disk->files(self::FOLDER))
            ->filter(fn (string $path): bool => str_ends_with(strtolower($path), '.sql'))
            ->sortByDesc(fn (string $path): string => basename($path))
            ->values();

        $berkas->slice($retensi)->each(fn (string $path) => $disk->delete($path));
    }

    /**
     * @return array{nama: string, path: string, ukuran: int, dibaca: string, waktu: int}
     */
    private function info(string $path): array
    {
        $disk = Storage::disk(self::DISK);
        $ukuran = (int) $disk->size($path);
        $waktu = (int) $disk->lastModified($path);

        return [
            'nama' => basename($path),
            'path' => $path,
            'ukuran' => $ukuran,
            'dibaca' => $ukuran >= 1048576
                ? number_format($ukuran / 1048576, 1, ',', '.').' MB'
                : number_format(max(1, $ukuran / 1024), 1, ',', '.').' KB',
            'waktu' => $waktu,
        ];
    }

    /** Membatasi nama berkas agar tidak dapat keluar dari folder backup. */
    private function pathAman(string $nama): string
    {
        if (preg_match('/^backup-\d{8}-\d{6}\.sql$/', $nama) !== 1) {
            throw new RuntimeException('Nama berkas backup tidak valid.');
        }

        return self::FOLDER.'/'.$nama;
    }
}
