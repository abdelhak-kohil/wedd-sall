<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Palais El Bahia');
    }

    public function test_owner_can_login_and_creates_audit_log(): void
    {
        $user = User::where('email', 'owner@palaiselbahia.dz')->first();

        $response = $this->post('/login', [
            'email' => 'owner@palaiselbahia.dz',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'LOGIN',
        ]);
    }

    public function test_language_switch_updates_session(): void
    {
        $response = $this->get('/lang/ar');
        $response->assertSessionHas('locale', 'ar');

        $responseFr = $this->get('/lang/fr');
        $responseFr->assertSessionHas('locale', 'fr');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'owner@palaiselbahia.dz')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Kamel Benali');
        $response->assertSee('OWNER');
    }
}
