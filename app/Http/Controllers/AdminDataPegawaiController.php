<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminDataPegawaiController extends Controller
{
    private function pastikanAdmin(Request $request): void
    {
        abort_unless(
            strtolower((string) $request->user()?->role) === 'admin',
            403,
            'Hanya Admin yang dapat mengelola Data Pegawai.'
        );
    }

    private function aturanBiodata(bool $create): array
    {
        $aturan = [
            'nama_pegawai' => ['required', 'string', 'min:2', 'max:150', 'not_regex:/^\s*$/u'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:3000'],
            'status_pegawai' => ['required', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if ($create) {
            $aturan['email'] = ['required', 'email', 'max:255', Rule::unique('pegawais', 'email'), Rule::unique('users', 'email')];
        }

        return $aturan;
    }

    /** Semua pegawai ditampilkan, baik yang sudah maupun yang belum mempunyai akun. */
    public function index(Request $request): View
    {
        $this->pastikanAdmin($request);

        $rawQ = $request->query('q', '');
        $pencarian = is_string($rawQ) ? mb_substr(trim($rawQ), 0, 150) : '';
        $rawStatus = $request->query('status', 'semua');
        $statusFilter = in_array($rawStatus, ['semua', 'aktif', 'nonaktif'], true)
            ? $rawStatus : 'semua';

        $pegawai = Pegawai::query()
            ->with('user')
            ->when($pencarian !== '', function ($query) use ($pencarian) {
                $query->where(function ($sub) use ($pencarian) {
                    $sub->where('nama_pegawai', 'like', "%{$pencarian}%")
                        ->orWhere('email', 'like', "%{$pencarian}%")
                        ->orWhere('no_hp', 'like', "%{$pencarian}%")
                        ->orWhereHas('user', function ($userQuery) use ($pencarian) {
                            $userQuery->where('no_hp', 'like', "%{$pencarian}%")
                                ->orWhere('email', 'like', "%{$pencarian}%");
                        });
                });
            })
            ->when($statusFilter !== 'semua', function ($query) use ($statusFilter) {
                $query->where('status_pegawai', $statusFilter === 'aktif');
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('Admin.data_pegawai', compact('pegawai', 'pencarian', 'statusFilter'));
    }

    /** Buat biodata mandiri; tidak perlu akun, user_id, atau role. */
    public function create(Request $request): View
    {
        $this->pastikanAdmin($request);

        return view('Admin.createdatapegawai');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->pastikanAdmin($request);
        // Standarkan email untuk meminimalkan data akun yang tidak konsisten.
        if (is_string($request->input('email'))) {
            $request->merge(['email' => mb_strtolower(trim($request->input('email')))]);
        }
        $data = $request->validate($this->aturanBiodata(true));

        $fotoBaru = null;
        try {
            if ($request->hasFile('foto')) {
                $fotoBaru = $request->file('foto')->store('pegawai/foto', 'public');
                if (!$fotoBaru) {
                    throw new \RuntimeException('Unggah foto pegawai gagal.');
                }
            }

            // Sama sekali tidak membuat/mencari/menghubungkan users berdasarkan email.
            Pegawai::create([
                'nama_pegawai' => trim($data['nama_pegawai']),
                'email' => trim($data['email']),
                'no_hp' => $data['no_hp'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'pendidikan_terakhir' => $data['pendidikan_terakhir'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'status_pegawai' => (bool) $data['status_pegawai'],
                'foto' => $fotoBaru,
            ]);
        } catch (Throwable $e) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }
            throw $e;
        }

        return redirect()->route('admin.data-pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan tanpa membuat akun pengguna.');
    }

    public function edit(Request $request, Pegawai $pegawai): View
    {
        $this->pastikanAdmin($request);
        $pegawai->load('user');

        return view('Admin.editdatapegawai', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai): RedirectResponse
    {
        $this->pastikanAdmin($request);
        // Email hanya dapat diedit sebelum pegawai ditautkan ke users.
        // Jangan percaya sekadar readonly/disabled di halaman browser.
        if (!$pegawai->akunLink()->exists()) {
            if (is_string($request->input('email'))) {
                $request->merge(['email' => mb_strtolower(trim($request->input('email')))]);
            }

            $aturanEmail = [
                'email' => [
                    'required', 'email', 'max:255',
                    Rule::unique('pegawais', 'email')->ignore($pegawai->getKey()),
                    Rule::unique('users', 'email'),
                ],
            ];
        } else {
            // Menolak percobaan mengirim email dari form yang sudah terkunci.
            $aturanEmail = ['email' => ['prohibited']];
        }
        $data = $request->validate(array_merge($this->aturanBiodata(false), $aturanEmail), [
            'email.prohibited' => 'Email sudah terhubung dengan akun pengguna. Untuk mengubah email, silakan hubungi Admin.',
        ]);

        $fotoBaru = null;
        $fotoLama = $pegawai->foto;

        try {
            if ($request->hasFile('foto')) {
                $fotoBaru = $request->file('foto')->store('pegawai/foto', 'public');
                if (!$fotoBaru) {
                    throw new \RuntimeException('Unggah foto pegawai gagal.');
                }
            }

            DB::transaction(function () use ($pegawai, $data, $fotoBaru): void {
                $dataPegawai = Pegawai::query()->lockForUpdate()->findOrFail($pegawai->getKey());
                $namaBaru = trim($data['nama_pegawai']);

                // Periksa ulang sesudah record dikunci: mencegah email diubah
                // ketika akun ditautkan bersamaan dengan proses edit.
                $userIdTerhubung = DB::table('pegawai_akun')
                    ->where('pegawai_id', $dataPegawai->getKey())
                    ->value('user_id');

                if ($userIdTerhubung !== null && array_key_exists('email', $data)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'email' => 'Email sudah terhubung dengan akun pengguna. Untuk mengubah email, silakan hubungi Admin.',
                    ]);
                }

                // Pegawai mandiri: hanya memperbarui pegawais.
                // Pegawai lama yang sudah ditautkan: nama users tetap konsisten.
                if ($userIdTerhubung !== null) {
                    $akun = User::query()->lockForUpdate()->findOrFail($userIdTerhubung);
                    if ($akun->name !== $namaBaru || $akun->no_hp !== ($data['no_hp'] ?? null)) {
                        $akun->name = $namaBaru;
                        $akun->no_hp = $data['no_hp'] ?? null;
                        $akun->save();
                    }
                }

                $dataPegawai->nama_pegawai = $namaBaru;

                if ($userIdTerhubung === null) {
                    // Pegawai belum memiliki akun: boleh mengubah email biodata.
                    $dataPegawai->email = $data['email'];
                } else {
                    // Akun pengguna adalah sumber utama alamat email login.
                    // Tidak pernah menulis users.email dari halaman Data Pegawai.
                    $dataPegawai->email = $akun->email ?? $dataPegawai->email;
                }

                $dataPegawai->no_hp = $data['no_hp'] ?? null;
                $dataPegawai->jenis_kelamin = $data['jenis_kelamin'] ?? null;
                $dataPegawai->pendidikan_terakhir = $data['pendidikan_terakhir'] ?? null;
                $dataPegawai->alamat = $data['alamat'] ?? null;
                $dataPegawai->status_pegawai = (bool) $data['status_pegawai'];

                if ($fotoBaru !== null) {
                    $dataPegawai->foto = $fotoBaru;
                }

                $dataPegawai->save();
            }, 3);
        } catch (Throwable $e) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }
            throw $e;
        }

        if ($fotoBaru && $fotoLama && $fotoBaru !== $fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()->route('admin.data-pegawai.index')
            ->with('success', 'Biodata pegawai berhasil diperbarui.');
    }

    /**
     * Hapus hanya baris pegawais dan foto miliknya; akun users tidak disentuh.
     * Harus dipanggil memakai DELETE + CSRF, bukan GET.
     */
    public function destroy(Request $request, Pegawai $pegawai): RedirectResponse
    {
        $this->pastikanAdmin($request);

        $namaPegawai = (string) $pegawai->nama_pegawai;
        $fotoLama = $pegawai->foto;

        try {
            DB::transaction(function () use ($pegawai): void {
                // Kunci baris agar penghapusan tidak bentrok dengan perubahan biodata.
                $dataPegawai = Pegawai::query()
                    ->lockForUpdate()
                    ->findOrFail($pegawai->getKey());

                // Hapus hanya biodata. Jangan panggil $dataPegawai->user->delete().
                $dataPegawai->delete();
            }, 3);
        } catch (\Illuminate\Database\QueryException $exception) {
            // Jika sudah direferensikan tabel lain dengan RESTRICT, jangan paksa hapus.
            if (in_array((string) $exception->getCode(), ['23000', '23503', '19'], true)) {
                return redirect()->route('admin.data-pegawai.index')
                    ->with('error', 'Data pegawai tidak bisa dihapus karena masih digunakan oleh data lain.');
            }

            throw $exception;
        }

        // Hapus file hanya setelah penghapusan database berhasil.
        if (is_string($fotoLama)
            && preg_match('~\Apegawai/foto/[A-Za-z0-9._-]+\.(?:jpe?g|png|webp)\z~i', $fotoLama) === 1
            && !Pegawai::query()->where('foto', $fotoLama)->exists()) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()->route('admin.data-pegawai.index')
            ->with('success', 'Data pegawai "' . $namaPegawai . '" berhasil dihapus. Akun pengguna tetap aman.');
    }

    /** Foto ditampilkan lewat Laravel agar tetap bekerja meski tanpa storage:link di Windows. */
    public function foto(Request $request, Pegawai $pegawai): StreamedResponse
    {
        $this->pastikanAdmin($request);

        // Storage::disk() dikenali oleh editor sebagai interface Filesystem.
        // Padahal disk Laravel memiliki method response() di FilesystemAdapter.
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $path = $pegawai->foto;
        abort_unless(
            is_string($path)
                && preg_match('~\Apegawai/foto/[A-Za-z0-9._-]+\.(?:jpe?g|png|webp)\z~i', $path) === 1
                && $disk->exists($path),
            404,
            'Foto pegawai tidak ditemukan.'
        );

        return $disk->response(
            $path,
            basename($path),
            ['X-Content-Type-Options' => 'nosniff'],
            'inline'
        );
    }
}
