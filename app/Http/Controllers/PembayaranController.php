<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    /**
     * Display the payment (Pembayaran) page.
     */
    public function index(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $status = session('pembayaran_status', 'belum_lunas');

        $tagihan = [
            'nama' => 'Pembayaran Asrama',
            'periode' => 'Januari 2027',
            'nominal' => '2.500.00,00',
            'nominal_rupiah' => 'Rp 2.500.000',
            'status' => $status,
        ];

        $riwayat = [
            [
                'periode' => 'Agustus-Desember',
                'nominal' => '2.500.000,00',
                'status' => 'Lunas',
            ],
        ];

        return view('pembayaran', [
            'tagihan' => $tagihan,
            'riwayat' => $riwayat,
            'status' => $status,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Process or simulate bill payment.
     */
    public function bayar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'metode' => ['nullable', 'string', 'max:50'],
        ]);

        session(['pembayaran_status' => 'lunas']);

        $metode = $validated['metode'] ?? 'Virtual Account BSI';

        return redirect()->route('pembayaran')
            ->with('status', 'Pembayaran Asrama Periode Januari 2027 sebesar Rp 2.500.000 via '.$metode.' berhasil diproses!');
    }

    /**
     * Reset payment simulation state to unpaid.
     */
    public function reset(): RedirectResponse
    {
        session()->forget('pembayaran_status');

        return redirect()->route('pembayaran')
            ->with('status', 'Status tagihan berhasil di-reset kembali ke Belum Lunas.');
    }
}
