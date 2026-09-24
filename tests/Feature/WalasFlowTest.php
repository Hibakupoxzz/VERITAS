<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalasFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_walas_is_redirected_to_lapor_index_on_login()
    {
        $walas = User::factory()->create(['role' => 'walas']);
        
        $response = $this->post('/login', [
            'email' => $walas->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/lapor');
    }

    public function test_walas_cannot_access_dashboard()
    {
        $walas = User::factory()->create(['role' => 'walas']);

        $response = $this->actingAs($walas)->get('/dashboard');

        $response->assertRedirect('/lapor');
    }

    public function test_walas_cannot_access_siswa()
    {
        $walas = User::factory()->create(['role' => 'walas']);

        $response = $this->actingAs($walas)->get('/siswa');

        $response->assertRedirect('/lapor');
    }

    public function test_admin_can_access_dashboard_and_siswa()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/siswa');
        $response->assertStatus(200);
    }
}
