<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\User;
use Database\Seeders\PembayaranSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MusyrifahPembayaranController extends Controller
{
    /**
     * Menampilkan halaman pembayaran mahasiswa untuk Musyrifah.
     */
    public function index(): View|RedirectResponse
    {
        $this->pastikanAdaDataPembayaran();

        $daftarPembayaran = $this->ambilDataPembayaran();
        $statistik = $this->hitungStatistik();

        return view('musyrifah.pembayaran', array_merge([
            'daftarPembayaran' => $daftarPembayaran,
        ], $statistik));
    }

    /**
     * Endpoint API JSON untuk sinkronisasi otomatis / real-time tanpa refresh halaman.
     */
    public function apiData(): JsonResponse
    {
        $this->pastikanAdaDataPembayaran();

        $daftarPembayaran = $this->ambilDataPembayaran();
        $statistik = $this->hitungStatistik();

        return response()->json([
            'success' => true,
            'data' => $daftarPembayaran,
            'summary' => $statistik,
            'last_sync' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Mengambil detail pembayaran dan riwayat aktivitas mahasiswa terkait pembayaran.
     */
    public function detail(int $id): JsonResponse
    {
        $pembayaran = Pembayaran::with('user')->findOrFail($id);
        $user = $pembayaran->user;

        // Ambil seluruh log aktivitas mahasiswa terkait pembayaran
        $logs = ActivityLog::where('user_id', $pembayaran->user_id)
            ->where(function ($query) {
                $query->where('activity', 'like', '%pembayaran%')
                    ->orWhere('activity', 'like', '%tagihan%')
                    ->orWhere('description', 'like', '%pembayaran%')
                    ->orWhere('description', 'like', '%tagihan%');
            })
            ->latest('created_at')
            ->take(20)
            ->get();

        // Jika tidak ada filter khusus, ambil log aktivitas umum mahasiswa
        if ($logs->isEmpty()) {
            $logs = ActivityLog::where('user_id', $pembayaran->user_id)
                ->latest('created_at')
                ->take(10)
                ->get();
        }

        $formattedLogs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'activity' => $log->activity,
                'description' => $log->description,
                'time_formatted' => $log->created_at ? $log->created_at->format('d M Y, H:i') : '-',
                'time_diff' => $log->created_at ? $log->created_at->diffForHumans() : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'pembayaran' => [
                'id' => $pembayaran->id,
                'kode_transaksi' => $pembayaran->kode_transaksi ?? ('TRX-'.$pembayaran->id),
                'nama_tagihan' => $pembayaran->nama_tagihan,
                'periode' => $pembayaran->periode,
                'nominal' => (float) $pembayaran->nominal,
                'nominal_rupiah' => 'Rp '.number_format((float) $pembayaran->nominal, 0, ',', '.'),
                'status' => $pembayaran->status,
                'status_label' => $pembayaran->status_label,
                'metode_pembayaran' => $pembayaran->metode_pembayaran ?? 'Belum ada',
                'tanggal_bayar' => $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d M Y, H:i').' WIB' : 'Belum dibayar',
                'keterangan' => $pembayaran->keterangan ?? '-',
                'updated_at' => $pembayaran->updated_at ? $pembayaran->updated_at->format('d M Y, H:i') : '-',
            ],
            'mahasiswa' => [
                'id' => $user?->id,
                'name' => $user?->name ?? 'Mahasiswa',
                'nim' => $user?->nim ?? '-',
                'email' => $user?->email ?? '-',
                'no_hp' => $user?->no_hp ?? '-',
                'status' => $user?->status ?? 'aktif',
            ],
            'riwayat_aktivitas' => $formattedLogs,
        ]);
    }

    /**
     * Memperbarui status pembayaran mahasiswa oleh Musyrifah.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:lunas,menunggu,belum_lunas'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $pembayaran = Pembayaran::with('user')->findOrFail($id);
        $statusLama = $pembayaran->status_label;
        $statusDipilih = $validated['status'];

        $updateData = ['status' => $statusDipilih];
        if ($statusDipilih === 'lunas' && ! $pembayaran->tanggal_bayar) {
            $updateData['tanggal_bayar'] = now();
            $updateData['metode_pembayaran'] = $pembayaran->metode_pembayaran ?? 'Dikonfirmasi Musyrifah';
        } elseif ($statusDipilih === 'belum_lunas') {
            $updateData['tanggal_bayar'] = null;
        }

        $pembayaran->update($updateData);

        // Catat ke log aktivitas
        $petugas = Auth::user();
        $mahasiswa = $pembayaran->user;
        $statusLabelBaru = $pembayaran->fresh()->status_label;

        ActivityLog::create([
            'user_id' => $mahasiswa?->id,
            'user_name' => $mahasiswa?->name ?? 'Mahasiswa',
            'user_email' => $mahasiswa?->email,
            'user_role' => 'mahasiswa',
            'activity' => 'Verifikasi Pembayaran',
            'description' => 'Musyrifah '.($petugas?->name ?? 'Musyrifah')." memperbarui status pembayaran {$pembayaran->nama_tagihan} ({$mahasiswa?->name} - {$mahasiswa?->nim}) dari {$statusLama} menjadi {$statusLabelBaru}.".($validated['catatan'] ? " Catatan: {$validated['catatan']}" : ''),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Status pembayaran {$mahasiswa?->name} berhasil diperbarui menjadi {$statusLabelBaru}.",
            'pembayaran' => $pembayaran->fresh(),
        ]);
    }

    /**
     * Memastikan data pembayaran di database terisi.
     */
    protected function pastikanAdaDataPembayaran(): void
    {
        if (Pembayaran::count() === 0) {
            (new PembayaranSeeder)->run();
        }

        // Pastikan setiap user dengan role mahasiswa yang belum punya pembayaran dibuatkan
        $mahasiswas = User::where('role', 'mahasiswa')
            ->whereDoesntHave('pembayarans')
            ->get();

        foreach ($mahasiswas as $mhs) {
            Pembayaran::create([
                'user_id' => $mhs->id,
                'kode_transaksi' => 'TRX-'.date('Ymd').'-'.str_pad((string) $mhs->id, 4, '0', STR_PAD_LEFT),
                'nama_tagihan' => 'Pembayaran Asrama',
                'periode' => 'Januari 2027',
                'nominal' => 2500000.00,
                'status' => 'belum_lunas',
                'keterangan' => 'Tagihan Asrama Periode Januari 2027',
            ]);
        }
    }

    /**
     * Mengambil daftar pembayaran yang rapi untuk ditampilkan.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function ambilDataPembayaran(): array
    {
        $list = Pembayaran::with('user')
            ->orderByRaw("CASE 
                WHEN status IN ('menunggu', 'menunggu_konfirmasi') THEN 1 
                WHEN status = 'lunas' THEN 2 
                ELSE 3 END")
            ->latest('updated_at')
            ->get();

        return $list->map(function ($p, $index) {
            $user = $p->user;

            return [
                'id' => $p->id,
                'no' => $index + 1,
                'nama' => $user?->name ?? 'Mahasiswa',
                'nim' => $user?->nim ?? '-',
                'email' => $user?->email ?? '-',
                'tagihan' => (float) $p->nominal,
                'tagihan_rupiah' => 'Rp '.number_format((float) $p->nominal, 0, ',', '.'),
                'periode' => $p->periode,
                'status' => $p->status_label,
                'raw_status' => strtolower((string) $p->status),
                'metode' => $p->metode_pembayaran ?? '—',
                'tanggal_bayar' => $p->tanggal_bayar ? $p->tanggal_bayar->format('d/m/Y H:i') : '—',
                'updated_at' => $p->updated_at ? $p->updated_at->format('d/m/Y H:i') : '—',
            ];
        })->toArray();
    }

    /**
     * Menghitung statistik ringkasan pembayaran.
     *
     * @return array<string, mixed>
     */
    protected function hitungStatistik(): array
    {
        $totalMahasiswa = Pembayaran::count();
        $totalLunas = Pembayaran::whereIn('status', ['lunas', 'paid', 'terkonfirmasi'])->count();
        $totalMenunggu = Pembayaran::whereIn('status', ['menunggu', 'menunggu_konfirmasi', 'pending'])->count();
        $totalBelumLunas = Pembayaran::whereNotIn('status', ['lunas', 'paid', 'terkonfirmasi', 'menunggu', 'menunggu_konfirmasi', 'pending'])->count();
        $totalNominalTerkumpul = Pembayaran::whereIn('status', ['lunas', 'paid', 'terkonfirmasi'])->sum('nominal');

        return [
            'totalMahasiswa' => $totalMahasiswa,
            'totalLunas' => $totalLunas,
            'totalMenunggu' => $totalMenunggu,
            'totalBelumLunas' => $totalBelumLunas,
            'totalNominalTerkumpul' => (float) $totalNominalTerkumpul,
            'totalNominalTerkumpulRupiah' => 'Rp '.number_format((float) $totalNominalTerkumpul, 0, ',', '.'),
        ];
    }
}
