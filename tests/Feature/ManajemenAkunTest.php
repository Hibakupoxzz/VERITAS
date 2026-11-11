<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Manajemen Akun + dua hak akses tambahan.
 *
 * Aturan yang dikunci di sini:
 *  - Manajemen Akun hanya untuk Super Admin.
 *  - Pemegang "akses penuh" (Waka Kesiswaan) boleh semua, kecuali akun.
 *  - "pelanggaran_writer" (Waka Humas) boleh menambah pelanggaran.
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

    public function test_akses_penuh_ditolak_di_manajemen_akun_tapi_boleh_backup(): void
    {
        $hanifi = $this->pimpinan(User::ACCESS_FULL, 'Hanifan');

        $this->actingAs($hanifi)
            ->get(route('account.index'))
            ->assertRedirect();

        $this->actingAs($hanifi)
            ->get(route('backup.index'))
            ->assertOk();

        $this->actingAs($hanifi)
            ->get(route('pelanggaran.create'))
            ->assertOk();
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
        $this->actingAs($admin)->get(route('maintenance.index'))->assertOk();

        // Aksi bersihkan harus berjalan dan mengembalikan pesan sukses.
        $this->actingAs($admin)
            ->post(route('maintenance.clean'))
            ->assertRedirect()
            ->assertSessionHas('success');

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
