<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser (): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        
        $this->withoutExceptionHandling();
        User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response = $this->post('/api/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $this->withoutExceptionHandling();

        User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response = $this->post('/api/v1/auth/login', [
            'email' => 'test@test.com',
            'password' => '123456678',
        ]);
        $this->assertGuest();

        $response->assertStatus(401);
    }

    public function test_users_can_logout(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $response = $this->post('/api/v1/logout');

        $response->assertStatus(200)
        ->assertJsonFragment([
            'message' => 'Successfully logged out'
        ]);
    }
}
