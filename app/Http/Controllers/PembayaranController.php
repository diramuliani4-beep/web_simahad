<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    /**
     * Display the payment (Pembayaran) page for Mahasiswa.
     */
    public function index(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Cari atau buat tagihan aktif untuk mahasiswa ini
        $pembayaran = Pembayaran::firstOrCreate(
            [
                'user_id' => $user->id,
                'periode' => 'Januari 2027',
            ],
            [
                'kode_transaksi' => 'TRX-'.date('Ymd').'-'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                'nama_tagihan' => 'Pembayaran Asrama',
                'nominal' => 2500000.00,
                'status' => session('pembayaran_status', 'belum_lunas'),
                'keterangan' => 'Tagihan Asrama Periode Januari 2027',
            ]
        );

        // Jika session diset lunas (misal dari tes fitur atau aksi langsung) sinkronkan ke database
        if (session('pembayaran_status') === 'lunas' && $pembayaran->status !== 'lunas') {
            $pembayaran->update([
                'status' => 'lunas',
                'metode_pembayaran' => $pembayaran->metode_pembayaran ?? 'Virtual Account BSI',
                'tanggal_bayar' => $pembayaran->tanggal_bayar ?? now(),
                'keterangan' => $pembayaran->keterangan ?? 'Pembayaran Asrama Periode Januari 2027 via Virtual Account BSI',
            ]);
        }

        $status = ($pembayaran->status === 'lunas' || session('pembayaran_status') === 'lunas')
            ? 'lunas'
            : $pembayaran->status;

        $tagihan = [
            'id' => $pembayaran->id,
            'nama' => $pembayaran->nama_tagihan ?? 'Pembayaran Asrama',
            'periode' => $pembayaran->periode ?? 'Januari 2027',
            'nominal' => '2.500.00,00',
            'nominal_rupiah' => 'Rp 2.500.000',
            'status' => $status,
            'metode' => $pembayaran->metode_pembayaran,
            'tanggal_bayar' => $pembayaran->tanggal_bayar,
        ];

        // Riwayat pembayaran
        $riwayat = [
            [
                'periode' => 'Agustus-Desember',
                'nominal' => '2.500.000,00',
                'status' => 'Lunas',
            ],
        ];

        // Tambahkan riwayat pendaftaran jika ada
        $pendaftaran = Pendaftaran::where('user_id', $user->id)->first();
        if ($pendaftaran && $pendaftaran->nominal_pembayaran) {
            $riwayat[] = [
                'periode' => 'Awal Pendaftaran',
                'nominal' => number_format((float) $pendaftaran->nominal_pembayaran, 0, ',', '.').',00',
                'status' => $pendaftaran->status === 'disetujui' ? 'Lunas' : 'Menunggu Konfirmasi',
            ];
        }

        return view('Mahasiswa.pembayaran', [
            'tagihan' => $tagihan,
            'riwayat' => $riwayat,
            'status' => $status,
            'user' => $user,
            'pembayaran' => $pembayaran,
        ]);
    }

    /**
     * Process or simulate bill payment.
     */
    public function bayar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'metode' => ['nullable', 'string', 'max:100'],
        ]);

        $user = Auth::user();
        $metode = $validated['metode'] ?? 'Virtual Account BSI';

        $pembayaran = Pembayaran::firstOrCreate(
            [
                'user_id' => $user->id,
                'periode' => 'Januari 2027',
            ],
            [
                'kode_transaksi' => 'TRX-'.date('Ymd').'-'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                'nama_tagihan' => 'Pembayaran Asrama',
                'nominal' => 2500000.00,
            ]
        );

        $pembayaran->update([
            'status' => 'lunas',
            'metode_pembayaran' => $metode,
            'tanggal_bayar' => now(),
            'keterangan' => "Pembayaran Asrama Periode {$pembayaran->periode} sebesar Rp 2.500.000 via {$metode}",
        ]);

        // Catat aktivitas ke log aktivitas sistem
        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role ?? 'mahasiswa',
            'activity' => 'Pembayaran Tagihan',
            'description' => "Mahasiswa {$user->name} ({$user->nim}) berhasil melakukan pembayaran {$pembayaran->nama_tagihan} periode {$pembayaran->periode} sebesar Rp 2.500.000 via {$metode}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        session(['pembayaran_status' => 'lunas']);

        return redirect()->route('pembayaran')
            ->with('status', 'Pembayaran Asrama Periode Januari 2027 sebesar Rp 2.500.000 via '.$metode.' berhasil diproses!');
    }

    /**
     * Reset payment simulation state to unpaid.
     */
    public function reset(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $pembayaran = Pembayaran::where('user_id', $user->id)
                ->where('periode', 'Januari 2027')
                ->first();

            if ($pembayaran) {
                $pembayaran->update([
                    'status' => 'belum_lunas',
                    'metode_pembayaran' => null,
                    'tanggal_bayar' => null,
                    'keterangan' => 'Tagihan Asrama Periode Januari 2027',
                ]);
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role ?? 'mahasiswa',
                'activity' => 'Reset Pembayaran',
                'description' => "Mahasiswa {$user->name} ({$user->nim}) me-reset status pembayaran tagihan Asrama ke Belum Lunas.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        session()->forget('pembayaran_status');

        return redirect()->route('pembayaran')
            ->with('status', 'Status tagihan berhasil di-reset kembali ke Belum Lunas.');
    }
}
