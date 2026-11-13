<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\PengajuanKeterlambatanPembayaran;
use App\Models\User;
use App\Services\PembayaranAngsuranService;
use App\Services\SuratKeterlambatanPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function __construct(private readonly PembayaranAngsuranService $layanan) {}

    /**
     * Halaman pembayaran mahasiswa: ringkasan tagihan, tiga angsuran,
     * rekening resmi BSI, riwayat, dan pengajuan keterlambatan.
     */
    public function index(Request $request): View
    {
        $mahasiswa = $this->mahasiswa($request);

        return view('Mahasiswa.pembayaran', [
            'user' => $mahasiswa,
            'ringkasan' => $this->layanan->ringkasan($mahasiswa),
        ]);
    }

    /**
     * Halaman detail lama diarahkan ke halaman pembayaran yang baru agar
     * seluruh informasi berada pada satu tempat.
     */
    public function detail(): RedirectResponse
    {
        return redirect()->route('pembayaran');
    }

    /**
     * Unggah bukti transfer untuk satu angsuran.
     */
    public function unggahBukti(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $mahasiswa = $this->mahasiswa($request);

        abort_unless($pembayaran->user_id === $mahasiswa->id, 403);

        $validated = $request->validate([
            'bukti' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'bukti.required' => 'Berkas bukti transfer wajib dipilih.',
            'bukti.mimes' => 'Bukti transfer harus berformat PDF, JPG, JPEG, atau PNG.',
            'bukti.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ]);

        try {
            $this->layanan->unggahBukti($mahasiswa, $pembayaran, $validated['bukti']);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        $this->catatAktivitas(
            $mahasiswa,
            'Unggah Bukti Pembayaran',
            'Mahasiswa '.$mahasiswa->name.' ('.$mahasiswa->nim.') mengunggah bukti transfer '.
                $pembayaran->labelTahap().' sebesar '.$pembayaran->nominal_rupiah.'.',
            $request
        );

        return redirect()->route('pembayaran')->with('success', 'Bukti transfer '.$pembayaran->labelTahap().
            ' berhasil diunggah. Status tagihan menunggu pemeriksaan Musyrifah.');
    }

    /**
     * Ajukan keterlambatan pembayaran beserta alasan dan rencana tanggal bayar.
     */
    public function ajukanKeterlambatan(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $mahasiswa = $this->mahasiswa($request);

        abort_unless($pembayaran->user_id === $mahasiswa->id, 403);

        $validated = $request->validate([
            'alasan' => ['required', 'string', 'min:10', 'max:1000'],
            'tanggal_rencana_bayar' => ['required', 'date', 'after:today', 'before:+6 months'],
        ], [
            'alasan.required' => 'Alasan keterlambatan wajib diisi.',
            'alasan.min' => 'Alasan keterlambatan minimal 10 karakter.',
            'tanggal_rencana_bayar.required' => 'Tanggal rencana pembayaran wajib diisi.',
            'tanggal_rencana_bayar.after' => 'Tanggal rencana pembayaran harus setelah hari ini.',
            'tanggal_rencana_bayar.before' => 'Tanggal rencana pembayaran maksimal 6 bulan ke depan.',
        ]);

        try {
            $pengajuan = $this->layanan->ajukanKeterlambatan(
                $mahasiswa,
                $pembayaran,
                $validated['alasan'],
                $validated['tanggal_rencana_bayar']
            );
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        $this->catatAktivitas(
            $mahasiswa,
            'Pengajuan Keterlambatan Pembayaran',
            'Mahasiswa '.$mahasiswa->name.' ('.$mahasiswa->nim.') mengajukan keterlambatan '.
                $pembayaran->labelTahap().' dengan nomor surat '.$pengajuan->nomor_surat.'.',
            $request
        );

        return redirect()->route('pembayaran')->with('success', 'Pengajuan keterlambatan terkirim dengan nomor surat '.
            $pengajuan->nomor_surat.'. Surat PDF dapat diperiksa pada riwayat pengajuan.');
    }

    /**
     * Unduh surat pengajuan keterlambatan dalam format PDF.
     */
    public function suratKeterlambatan(Request $request, PengajuanKeterlambatanPembayaran $pengajuan): Response
    {
        $pengguna = $request->user();
        $role = strtolower(trim((string) $pengguna?->role));

        abort_unless(
            $pengguna && ($pengajuan->user_id === $pengguna->id || in_array($role, ['musyrifah', 'pengelola', 'admin'], true)),
            403
        );

        $pdf = app(SuratKeterlambatanPdfService::class)->render($pengajuan);
        $namaBerkas = 'surat-keterlambatan-'.str_replace('/', '-', $pengajuan->nomor_surat).'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$namaBerkas.'"',
        ]);
    }

    private function mahasiswa(Request $request): User
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));

        abort_unless($user && $role === 'mahasiswa', 403);

        return $user;
    }

    private function catatAktivitas(User $mahasiswa, string $aktivitas, string $deskripsi, Request $request): void
    {
        ActivityLog::create([
            'user_id' => $mahasiswa->id,
            'user_name' => $mahasiswa->name,
            'user_email' => $mahasiswa->email,
            'user_role' => 'mahasiswa',
            'activity' => $aktivitas,
            'description' => $deskripsi,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
