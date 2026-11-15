<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Manajemen Akun — khusus Super Admin.
 *
 * Sampai sekarang akun hanya bisa dibuat lewat seeder. Halaman ini
 * memberi Super Admin cara mengelola akun dari aplikasi: menambah,
 * mengubah, mengganti password, mengubah role, dan menghapus.
 *
 * Password TIDAK pernah ditampilkan; mengganti password dilakukan
 * dengan mengisi "Password baru" di form edit (dibiarkan kosong
 * berarti tidak diubah).
 */
class AccountController extends Controller
{
    /**
     * Pilihan role aplikasi untuk form.
     *
     * @return array<string, string>
     */
    public static function roleOptions(): array
    {
        return [
            'admin' => 'Super Admin',
            'pimpinan' => 'Pimpinan Sekolah',
            'pds' => 'Guru PDS',
            'bk' => 'Guru BK',
            'walas' => 'Wali Kelas',
            'guru' => 'Guru',
        ];
    }

    /**
     * Pilihan hak akses tambahan untuk form.
     *
     * @return array<string, string>
     */
    public static function accessOptions(): array
    {
        return [
            '' => 'Normal (mengikuti role)',
            User::ACCESS_FULL => 'Akses Penuh (semua fitur, setara Super Admin)',
            User::ACCESS_PELANGGARAN_WRITER => 'Boleh Tambah Pelanggaran',
            User::ACCESS_VERIFIKATOR => 'Boleh Tambah & Verifikasi Pelanggaran (seperti PDS)',
        ];
    }

    public function index()
    {
        // Akun Super Admin lama (duospirits@gmail.com) sengaja tidak
        // ditampilkan di daftar; lihat User::HIDDEN_ACCOUNT_EMAIL.
        $users = User::query()
            ->visibleInAccountList()
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => $users->count(),
            'admin' => $users->where('role', 'admin')->count(),
            'pimpinan' => $users->where('role', 'pimpinan')->count(),
            'pds' => $users->where('role', 'pds')->count(),
            'bk' => $users->where('role', 'bk')->count(),
            'walas' => $users->where('role', 'walas')->count(),
            'guru' => $users->where('role', 'guru')->count(),
        ];

        return view('account.index', [
            'users' => $users,
            'stats' => $stats,
            'roleOptions' => self::roleOptions(),
        ]);
    }

    public function create()
    {
        return view('account.create', [
            'roleOptions' => self::roleOptions(),
            'accessOptions' => self::accessOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['access_level'] = ($data['access_level'] ?? null) ?: null;
        $data['kelas'] = $data['kelas'] ?? null;
        $data['jabatan'] = $data['jabatan'] ?? null;
        $data['nig'] = $data['nig'] ?? null;
        $data['email_verified_at'] = now();

        User::create($data);

        return redirect()
            ->route('account.index')
            ->with('success', "Akun {$data['name']} berhasil ditambahkan.");
    }

    public function edit(User $account)
    {
        $this->ensureAccountIsVisible($account);

        return view('account.edit', [
            'account' => $account,
            'roleOptions' => self::roleOptions(),
            'accessOptions' => self::accessOptions(),
        ]);
    }

    public function update(Request $request, User $account)
    {
        $this->ensureAccountIsVisible($account);

        $data = $this->validated($request, $account);

        $data['access_level'] = ($data['access_level'] ?? null) ?: null;
        $data['kelas'] = $data['kelas'] ?? null;
        $data['jabatan'] = $data['jabatan'] ?? null;
        $data['nig'] = $data['nig'] ?? null;

        /*
         * Cegah admin mengunci dirinya sendiri keluar:
         * role sendiri tidak boleh diturunkan dari admin.
         */
        if ($this->isSelf($account) && $account->isAdmin() && $data['role'] !== 'admin') {
            return back()
                ->withInput()
                ->withErrors(['role' => 'Anda tidak dapat menurunkan role akun Anda sendiri.']);
        }

        // Password hanya diperbarui bila diisi.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $account->update($data);

        return redirect()
            ->route('account.index')
            ->with('success', "Akun {$account->name} berhasil diperbarui.");
    }

    /**
     * Form khusus mengganti password seorang akun.
     *
     * Super Admin dapat mengganti password SEMUA akun di bawahnya.
     * Dipisah dari form edit supaya jelas dan tidak tercampur dengan
     * perubahan data profil.
     */
    public function editPassword(User $account)
    {
        $this->ensureAccountIsVisible($account);

        return view('account.password', [
            'account' => $account,
        ]);
    }

    public function updatePassword(Request $request, User $account)
    {
        $this->ensureAccountIsVisible($account);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $account->update(['password' => $data['password']]);

        return redirect()
            ->route('account.index')
            ->with('success', "Password akun {$account->name} berhasil diubah.");
    }

    public function destroy(User $account)
    {
        $this->ensureAccountIsVisible($account);

        if ($this->isSelf($account)) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Jangan sampai tidak ada Super Admin yang tersisa.
        if ($account->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Akun Super Admin terakhir tidak dapat dihapus.');
        }

        $nama = $account->name;
        $account->delete();

        return redirect()
            ->route('account.index')
            ->with('success', "Akun {$nama} berhasil dihapus.");
    }

    /**
     * Akun tersembunyi tidak boleh diakses lewat URL langsung.
     *
     * Tanpa ini, akun yang sudah disembunyikan dari daftar masih bisa
     * dibuka dengan menebak ID-nya.
     */
    private function ensureAccountIsVisible(User $account): void
    {
        abort_if($account->isHiddenAccount(), 404);
    }

    /**
     * Aturan validasi bersama store/update.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $account = null): array
    {
        $userId = $account?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'nig' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'nig')->ignore($userId),
            ],
            'role' => ['required', Rule::in(array_keys(self::roleOptions()))],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'access_level' => ['nullable', Rule::in(array_keys(self::accessOptions()))],
        ];

        // Password wajib saat membuat akun, opsional saat mengubah.
        $rules['password'] = $account
            ? ['nullable', 'string', 'min:6']
            : ['required', 'string', 'min:6'];

        return $request->validate($rules);
    }

    private function isSelf(User $account): bool
    {
        return auth()->id() === $account->id;
    }
}
