<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PengajuanKamar extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';
    public const JENIS_PENEMPATAN = 'penempatan';
    public const JENIS_PINDAH = 'pindah';

    protected $table = 'pengajuan_kamars';

    protected $fillable = [
        'user_id',
        'pendaftaran_id',
        'kamar_id',
        'kamar_asal_id',
        'jenis',
        'alasan',
        'catatan',
        'diverifikasi_oleh',
        'status',
        'persetujuan_asal_oleh',
        'persetujuan_asal_pada',
    ];

    protected function casts(): array
    {
        return [
            'persetujuan_asal_pada' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Kamar tujuan (atau pilihan penempatan pertama). */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function kamarAsal(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_asal_id');
    }

    /** Verifikator akhir: Musyrifah tujuan untuk perpindahan lintas wilayah. */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /** Persetujuan tahap pertama dari Musyrifah kamar asal. */
    public function verifikatorAsal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'persetujuan_asal_oleh');
    }

    public function labelJenis(): string
    {
        return $this->jenis === self::JENIS_PINDAH ? 'Pindah Kamar' : 'Penempatan Baru';
    }

    public function menungguPersetujuanTujuan(): bool
    {
        return $this->jenis === self::JENIS_PINDAH
            && $this->status === self::STATUS_MENUNGGU
            && $this->persetujuan_asal_oleh !== null;
    }

    public function labelStatus(): string
    {
        if ($this->menungguPersetujuanTujuan()) {
            return 'Menunggu Persetujuan Musyrifah Tujuan';
        }

        return match ($this->status) {
            self::STATUS_MENUNGGU => 'Menunggu Verifikasi',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            default => ucfirst((string) $this->status),
        };
    }

    public function masihMenunggu(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    /**
     * Pengajuan 'menunggu' tetap mencakup tahap pertama dan kedua, agar
     * reservasi slot, daftar pengajuan aktif, dan aplikasi mahasiswa konsisten.
     */
    public static function slotTersedia(Kamar $kamar, ?int $kecualiUserId = null): int
    {
        $penghuni = $kamar->jumlahPenghuni();

        $menunggu = static::query()
            ->where('kamar_id', $kamar->id)
            ->where('status', self::STATUS_MENUNGGU)
            ->when(
                $kecualiUserId !== null,
                fn ($query) => $query->where('user_id', '!=', $kecualiUserId)
            )
            ->count();

        return max(0, (int) $kamar->kapasitas - $penghuni - $menunggu);
    }

    /** Pendaftaran penempatan pertama: perilaku lama dipertahankan. */
    public static function ajukan(User $user, Kamar $kamar): self
    {
        return DB::transaction(function () use ($user, $kamar): self {
            $kamar = Kamar::query()->lockForUpdate()->findOrFail($kamar->id);

            $pengajuanLama = static::query()
                ->where('user_id', $user->id)
                ->where('jenis', self::JENIS_PENEMPATAN)
                ->lockForUpdate()
                ->first();

            if ($pengajuanLama?->status === self::STATUS_DISETUJUI) {
                throw new \RuntimeException('Pengajuan kamar kamu sudah disetujui.');
            }
            if (! $kamar->bisaDiajukan()) {
                throw new \RuntimeException('Kamar '.$kamar->nomor_kamar.' sedang tidak dapat diajukan.');
            }
            if (static::slotTersedia($kamar, $user->id) <= 0) {
                throw new \RuntimeException('Maaf, Kamar '.$kamar->nomor_kamar.' sudah penuh atau sedang dalam proses pengajuan.');
            }

            $pendaftaran = Pendaftaran::query()
                ->where('user_id', $user->id)->latest('id')->first();

            return static::query()->updateOrCreate(
                ['user_id' => $user->id, 'jenis' => self::JENIS_PENEMPATAN],
                [
                    'pendaftaran_id' => $pendaftaran?->id,
                    'kamar_id' => $kamar->id,
                    'kamar_asal_id' => null,
                    'alasan' => null,
                    'catatan' => null,
                    'diverifikasi_oleh' => null,
                    'persetujuan_asal_oleh' => null,
                    'persetujuan_asal_pada' => null,
                    'status' => self::STATUS_MENUNGGU,
                ]
            );
        });
    }

    /**
     * Pindah kamar tidak langsung mengubah users.kamar_id.
     * Pengajuan dengan status menunggu, termasuk setelah persetujuan asal,
     * mencegah permohonan pindah ganda.
     */
    public static function ajukanPindah(User $user, Kamar $kamarTujuan, string $alasan): self
    {
        return DB::transaction(function () use ($user, $kamarTujuan, $alasan): self {
            $santri = User::query()->lockForUpdate()->findOrFail($user->id);
            if (! $santri->kamar_id) {
                throw new \RuntimeException('Kamu belum memiliki penempatan kamar yang dapat dipindahkan.');
            }

            $tujuan = Kamar::query()->lockForUpdate()->findOrFail($kamarTujuan->id);
            if ((int) $santri->kamar_id === (int) $tujuan->id) {
                throw new \RuntimeException('Kamar tujuan sama dengan kamar yang kamu tempati saat ini.');
            }

            $sudahMengajukan = static::query()
                ->where('user_id', $santri->id)
                ->where('jenis', self::JENIS_PINDAH)
                ->where('status', self::STATUS_MENUNGGU)
                ->lockForUpdate()->exists();

            if ($sudahMengajukan) {
                throw new \RuntimeException('Kamu masih memiliki pengajuan pindah kamar yang menunggu verifikasi Musyrifah.');
            }
            if (! $tujuan->bisaDiajukan()) {
                throw new \RuntimeException('Kamar '.$tujuan->nomor_kamar.' sedang tidak dapat diajukan.');
            }
            if (static::slotTersedia($tujuan, $santri->id) <= 0) {
                throw new \RuntimeException('Maaf, Kamar '.$tujuan->nomor_kamar.' sudah penuh atau sedang dalam proses pengajuan.');
            }

            $pendaftaran = Pendaftaran::query()
                ->where('user_id', $santri->id)->latest('id')->first();

            return static::query()->create([
                'user_id' => $santri->id,
                'pendaftaran_id' => $pendaftaran?->id,
                'kamar_id' => $tujuan->id,
                'kamar_asal_id' => $santri->kamar_id,
                'jenis' => self::JENIS_PINDAH,
                'alasan' => $alasan,
                'catatan' => null,
                'diverifikasi_oleh' => null,
                'persetujuan_asal_oleh' => null,
                'persetujuan_asal_pada' => null,
                'status' => self::STATUS_MENUNGGU,
            ]);
        });
    }
}
