<?php

namespace App\Http\Controllers;

use App\Models\SuratPerizinan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PengajuanIzinPulangController extends Controller
{
    /**
     * Menampilkan formulir izin pulang mingguan beserta monitoring kuota.
     */
    public function index(Request $request): View
    {
        $user = $this->penggunaMahasiswa($request);

        return view('surat dan perizinan.pengajuan_izin_pulang', [
            'user' => $user,
            'kuotaPulang' => SuratPerizinan::kuotaPulang($user),
            'riwayatPulang' => $user->suratPerizinans()
                ->where('jenis', SuratPerizinan::JENIS_PULANG)
                ->latest('id')
                ->get(),
        ]);
    }

    /**
     * Memvalidasi kuota, durasi, dan tanggal sebelum menyimpan izin pulang.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->penggunaMahasiswa($request);

        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'lokasi' => ['required', 'string', 'max:255'],
            'alasan' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'tanggal_mulai.required' => 'Tanggal mulai izin wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai izin tidak boleh tanggal yang sudah lewat.',
            'tanggal_selesai.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal mulai.',
            'lokasi.required' => 'Alamat atau tujuan pulang wajib diisi.',
            'lokasi.max' => 'Alamat atau tujuan pulang maksimal 255 karakter.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'alasan.min' => 'Alasan izin minimal 10 karakter agar dapat diperiksa Musyrifah.',
            'alasan.max' => 'Alasan izin maksimal 2000 karakter.',
        ]);

        $mulai = Carbon::parse($validated['tanggal_mulai'])->startOfDay();
        $kembali = Carbon::parse($validated['tanggal_selesai'])->startOfDay();
        $durasi = (int) $mulai->diffInDays($kembali) + 1;

        if ($durasi > SuratPerizinan::MAKS_DURASI_PULANG_HARI) {
            throw ValidationException::withMessages([
                'tanggal_selesai' => 'Durasi izin pulang maksimal '.SuratPerizinan::MAKS_DURASI_PULANG_HARI.' hari.',
            ]);
        }

        // Kunci baris pengguna agar dua pengajuan bersamaan tidak lolos kuota.
        $pengajuan = DB::transaction(function () use ($user, $validated, $mulai, $kembali, $durasi): SuratPerizinan {
            $terkunci = User::query()->lockForUpdate()->findOrFail($user->id);

            $kuota = SuratPerizinan::kuotaPulang($terkunci, $mulai);

            if ($kuota['sisa'] <= 0) {
                throw ValidationException::withMessages([
                    'tanggal_mulai' => 'Kuota izin pulang bulan '.$kuota['bulan'].' sudah habis (maksimal '
                        .SuratPerizinan::KUOTA_PULANG_PER_BULAN.' kali per bulan).',
                ]);
            }

            $pengajuan = $terkunci->suratPerizinans()->create([
                'jenis' => SuratPerizinan::JENIS_PULANG,
                'status' => SuratPerizinan::STATUS_MENUNGGU,
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $kembali->toDateString(),
                'durasi_hari' => $durasi,
                'lokasi' => trim($validated['lokasi']),
                'alasan' => trim($validated['alasan']),
            ]);

            $pengajuan->catatRiwayat(
                SuratPerizinan::STATUS_MENUNGGU,
                'Pengajuan izin pulang dikirim mahasiswa ('.$durasi.' hari).',
                $terkunci
            );

            return $pengajuan;
        });

        return redirect()
            ->route('surat.perizinan.mhs')
            ->with('success', 'Pengajuan izin pulang berhasil dikirim. Menunggu persetujuan Musyrifah.');
    }

    /**
     * Memastikan hanya akun mahasiswa yang dapat mengajukan izin pulang.
     */
    private function penggunaMahasiswa(Request $request): User
    {
        $user = $request->user();

        abort_unless(
            $user && strtolower(trim((string) $user->role)) === 'mahasiswa',
            403,
            'Halaman ini hanya dapat diakses oleh mahasiswa.'
        );

        return $user;
    }
}
