<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser (): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    public function test_email_invitation(): void
    {
        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response = $this->post('/api/v1/users', [
            'name' => 'Laurita',
            'email' => 'laurita@test.com'
        ]);

        $response->assertStatus(201);
    }
}
