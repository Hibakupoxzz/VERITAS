<?php

namespace Tests\Feature;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Manajemen Akun + dua hak akses tambahan.
 *
 * Aturan yang dikunci di sini:
 *  - Manajemen Akun untuk Super Admin dan pemegang "akses penuh".
 *  - Pemegang "akses penuh" (Waka Kesiswaan) boleh semua, setara admin.
 *  - "pelanggaran_writer" hanya boleh menambah pelanggaran.
 *  - "verifikator" (Waka Humas) setara Guru PDS untuk pelanggaran,
 *    termasuk edit dan hapus.
 */
class ManajemenAkunTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@veritas.test',
            'password' => Hash::make('zidan123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    private function pimpinan(string $level, string $nama = 'Pimpinan'): User
    {
        return User::create([
            'name' => $nama,
            'email' => strtolower(str_replace(' ', '', $nama)).'@veritas.test',
            'password' => Hash::make('pimpinan123'),
            'role' => 'guru',
            'jabatan' => 'Waka Urs. Kesiswaan',
            'access_level' => $level,
            'email_verified_at' => now(),
        ]);
    }

    public function test_super_admin_bisa_membuka_manajemen_akun(): void
    {
        $this->actingAs($this->admin())
            ->get(route('account.index'))
            ->assertOk();
    }

    public function test_akun_admin_duospirits_tidak_tampil_di_manajemen_akun(): void
    {
        $admin = $this->admin();

        $hidden = User::create([
            'name' => 'Admin Lama',
            'email' => 'duospirits@gmail.com',
            'password' => Hash::make('zidan123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('account.index'))
            ->assertOk();

        $response->assertDontSee('duospirits@gmail.com');
        $response->assertSee($admin->email);

        // Akses langsung lewat URL juga harus ditolak.
        $this->actingAs($admin)
            ->get(route('account.edit', $hidden->id))
            ->assertNotFound();

        $this->actingAs($admin)
            ->get(route('account.password.edit', $hidden->id))
            ->assertNotFound();

        $this->actingAs($admin)
            ->delete(route('account.destroy', $hidden->id))
            ->assertNotFound();
    }

    public function test_guru_biasa_tidak_bisa_membuka_manajemen_akun(): void
    {
        $guru = User::create([
            'name' => 'Guru Biasa',
            'email' => 'guru@veritas.test',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($guru)
            ->get(route('account.index'))
            ->assertRedirect();
    }

    public function test_akses_penuh_boleh_semua_termasuk_manajemen_akun(): void
    {
        $hanifi = $this->pimpinan(User::ACCESS_FULL, 'Hanifan');

        $this->actingAs($hanifi)
            ->get(route('account.index'))
            ->assertOk();

        $this->actingAs($hanifi)
            ->get(route('account.create'))
            ->assertOk();

        $this->actingAs($hanifi)
            ->get(route('backup.index'))
            ->assertOk();

        $this->actingAs($hanifi)
            ->get(route('pelanggaran.create'))
            ->assertOk();
    }

    public function test_menu_super_admin_muncul_untuk_akses_penuh(): void
    {
        $hanifi = $this->pimpinan(User::ACCESS_FULL, 'Hanifan');

        $response = $this->actingAs($hanifi)
            ->get(route('backup.index'))
            ->assertOk();

        // Menu yang sama dengan Super Admin harus terlihat, bukan menu guru.
        foreach ([
            'Manajemen Akun',
            'Data Siswa',
            'Tambah Siswa',
            'Data Prestasi',
            'Tambah Prestasi',
            'Pending Laporan',
            'Tambah Pelanggaran',
        ] as $menu) {
            $response->assertSee($menu);
        }
    }

    public function test_pelanggaran_writer_boleh_menambah_pelanggaran(): void
    {
        $septia = $this->pimpinan(User::ACCESS_PELANGGARAN_WRITER, 'Septia');

        $this->assertTrue($septia->canDirectAddPelanggaran());
        $this->assertFalse($septia->hasFullAccess());
        $this->assertFalse($septia->canVerify());

        $this->actingAs($septia)
            ->get(route('pelanggaran.create'))
            ->assertOk();
    }

    public function test_verifikator_boleh_memverifikasi_layaknya_pds(): void
    {
        $septia = $this->pimpinan(User::ACCESS_VERIFIKATOR, 'Septia');

        $this->assertTrue($septia->canVerify());
        $this->assertTrue($septia->canDirectAddPelanggaran());
        $this->assertFalse($septia->canManagePrestasi());
        $this->assertFalse($septia->hasFullAccess());

        $this->actingAs($septia)
            ->get(route('pelanggaran.pending'))
            ->assertOk();

        $this->actingAs($septia)
            ->get(route('pelanggaran.create'))
            ->assertOk();
    }

    public function test_verifikator_bisa_edit_dan_hapus_pelanggaran_layaknya_pds(): void
    {
        $septia = $this->pimpinan(User::ACCESS_VERIFIKATOR, 'Septia');

        $siswa = Siswa::create([
            'nama' => 'Siswa Uji',
            'nisn' => '12345678',
            'kelas' => 'X RPL 1',
            'jk' => 'L',
        ]);

        $pelanggaran = Pelanggaran::create([
            'siswa_id' => $siswa->id,
            'pelapor_id' => $septia->id,
            'tanggal' => today(),
            'jenis_pelanggaran' => 'Terlambat',
            'kategori' => 'Ringan',
            'poin' => 5,
            'status' => 'diverifikasi',
        ]);

        $this->actingAs($septia)
            ->get(route('pelanggaran.index'))
            ->assertOk()
            ->assertSee(route('pelanggaran.destroy', $pelanggaran->id));

        $this->actingAs($septia)
            ->get(route('pelanggaran.edit', $pelanggaran->id))
            ->assertOk();

        $this->actingAs($septia)
            ->put(route('pelanggaran.update', $pelanggaran->id), [
                'siswa_id' => $siswa->id,
                'tanggal' => today()->toDateString(),
                'jenis_pelanggaran' => 'Terlambat (dikoreksi)',
                'keterangan' => 'Sudah dikoreksi Waka Humas',
            ])
            ->assertRedirect(route('pelanggaran.index'));

        $this->assertDatabaseHas('pelanggarans', [
            'id' => $pelanggaran->id,
            'jenis_pelanggaran' => 'Terlambat (dikoreksi)',
        ]);

        $this->actingAs($septia)
            ->delete(route('pelanggaran.destroy', $pelanggaran->id))
            ->assertRedirect(route('pelanggaran.index'));

        $this->assertDatabaseMissing('pelanggarans', ['id' => $pelanggaran->id]);
    }

    public function test_pelanggaran_writer_tetap_tidak_bisa_menghapus_pelanggaran(): void
    {
        $writer = $this->pimpinan(User::ACCESS_PELANGGARAN_WRITER, 'Penulis');

        $this->actingAs($writer)
            ->get(route('pelanggaran.create'))
            ->assertOk();

        $this->actingAs($writer)
            ->delete(route('pelanggaran.destroy', 999))
            ->assertRedirect(route('lapor.index'));
    }

    public function test_admin_bisa_menambah_dan_menghapus_akun(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('account.store'), [
                'name' => 'Akun Baru',
                'email' => 'baru@veritas.test',
                'nig' => '0999999999',
                'password' => 'rahasia123',
                'role' => 'guru',
            ])
            ->assertRedirect(route('account.index'));

        $this->assertDatabaseHas('users', ['email' => 'baru@veritas.test']);

        $target = User::where('email', 'baru@veritas.test')->first();

        $this->actingAs($admin)
            ->delete(route('account.destroy', $target->id))
            ->assertRedirect(route('account.index'));

        $this->assertDatabaseMissing('users', ['email' => 'baru@veritas.test']);
    }

    public function test_admin_tidak_bisa_menghapus_akun_sendiri(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('account.destroy', $admin->id));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_semua_halaman_baru_render_tanpa_error(): void
    {
        $admin = $this->admin();
        $target = $this->pimpinan(User::ACCESS_FULL, 'Hanifan');

        $this->actingAs($admin)->get(route('account.create'))->assertOk();
        $this->actingAs($admin)->get(route('account.edit', $target->id))->assertOk();
        $this->actingAs($admin)->get(route('account.password.edit', $target->id))->assertOk();
        $this->actingAs($admin)->get(route('backup.index'))->assertOk();

        // Export backup tahunan menghasilkan file Excel.
        $this->actingAs($admin)
            ->get(route('backup.export', ['tahun' => 2026]))
            ->assertOk()
            ->assertDownload('backup-veritas-2026.xlsx');
    }

    public function test_super_admin_bisa_mengganti_password_user_lain(): void
    {
        $admin = $this->admin();
        $target = $this->pimpinan(User::ACCESS_VERIFIKATOR, 'Septia');

        $this->actingAs($admin)
            ->put(route('account.password.update', $target->id), [
                'password' => 'gantipassword123',
                'password_confirmation' => 'gantipassword123',
            ])
            ->assertRedirect(route('account.index'));

        $this->assertTrue(Hash::check('gantipassword123', $target->fresh()->password));
    }

    public function test_password_baru_harus_dikonfirmasi(): void
    {
        $admin = $this->admin();
        $target = $this->pimpinan(User::ACCESS_VERIFIKATOR, 'Septia');

        $this->actingAs($admin)
            ->put(route('account.password.update', $target->id), [
                'password' => 'gantipassword123',
                'password_confirmation' => 'beda123456',
            ])
            ->assertSessionHasErrors('password');

        $this->assertFalse(Hash::check('gantipassword123', $target->fresh()->password));
    }
}
