<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser (): Authenticatable
    {
        return $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@mail.com',
            'password' => bcrypt('password123')
        ]);

        $token = JWTAuth::fromUser($user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        
        $this->withoutExceptionHandling();
        $user = User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response = $this->post('/api/v1/auth/login', [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertContent($response->getContent());
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $this->withoutExceptionHandling();

        $user = User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response = $this->post('/api/v1/auth/login', [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => '123456678',
        ]);
        $this->assertGuest();

        $response->assertStatus(401);
    }

    public function test_users_can_logout(): void
    {
        $this->withoutExceptionHandling();

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@mail.com',
            'password' => bcrypt('password123'),
            'role_id'  => 0
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response = $this->post('/api/v1/logout');

        $response->assertStatus(200)
        ->assertJsonFragment([
            'message' => 'Successfully logged out'
        ]);
    }
}
