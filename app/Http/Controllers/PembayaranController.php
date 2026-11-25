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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'bayar_semua' => ['sometimes', 'boolean'],
        ], [
            'bukti.required' => 'Berkas bukti transfer wajib dipilih.',
            'bukti.mimes' => 'Bukti transfer harus berformat PDF, JPG, JPEG, atau PNG.',
            'bukti.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ]);

        try {
            $pembayaran = $this->layanan->unggahBukti(
                $mahasiswa,
                $pembayaran,
                $validated['bukti'],
                (bool) ($validated['bayar_semua'] ?? false)
            );
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        $grup = Pembayaran::query()
            ->where('user_id', $mahasiswa->id)
            ->where('bukti_grup', $pembayaran->bukti_grup)
            ->get();
        $totalGrup = (float) $grup->sum(fn (Pembayaran $item): float => (float) $item->nominal);
        $keteranganGrup = $grup->count() > 1
            ? ' untuk '.$grup->count().' tagihan total Rp '.number_format($totalGrup, 0, ',', '.')
            : ' sebesar '.$pembayaran->nominal_rupiah;

        $this->catatAktivitas(
            $mahasiswa,
            'Unggah Bukti Pembayaran',
            'Mahasiswa '.$mahasiswa->name.' ('.$mahasiswa->nim.') mengunggah bukti transfer '.
                $pembayaran->labelTahap().$keteranganGrup.'.',
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

        $bolehAkses = $pengguna && (
            $pengajuan->user_id === $pengguna->id
            || in_array($role, ['pengelola', 'admin'], true)
            || ($role === 'musyrifah'
                && $pengajuan->pembayaran
                && $this->layanan->bolehAksesPembayaran($pengguna, $pengajuan->pembayaran))
        );

        abort_unless($bolehAkses, 403);

        $pdf = app(SuratKeterlambatanPdfService::class)->render($pengajuan);
        $namaBerkas = 'surat-keterlambatan-'.str_replace('/', '-', $pengajuan->nomor_surat).'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$namaBerkas.'"',
        ]);
    }

    /**
     * Tampilkan bukti melalui controller agar akses lintas lantai ditolak.
     * Bukti lama pada disk public tetap didukung tanpa dipindahkan.
     */
    public function buktiPembayaran(Request $request, Pembayaran $pembayaran): StreamedResponse
    {
        $pengguna = $request->user();
        $role = strtolower(trim((string) $pengguna?->role));
        $bolehAkses = $pengguna && (
            $pembayaran->user_id === $pengguna->id
            || in_array($role, ['pengelola', 'admin'], true)
            || ($role === 'musyrifah' && $this->layanan->bolehAksesPembayaran($pengguna, $pembayaran))
        );

        abort_unless($bolehAkses, 403);
        abort_if(blank($pembayaran->bukti_pembayaran), 404);

        $disk = Storage::disk('local')->exists($pembayaran->bukti_pembayaran) ? 'local' : 'public';
        abort_unless(Storage::disk($disk)->exists($pembayaran->bukti_pembayaran), 404);

        return Storage::disk($disk)->response(
            $pembayaran->bukti_pembayaran,
            $pembayaran->namaBerkasBukti(),
            ['Content-Type' => 'application/octet-stream'],
            'inline'
        );
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
