<?php

namespace Tests\Support;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait Authentication
{
    protected User $user;

    protected string $token;

    public function setupAuthentication()
    {
        $this->afterApplicationCreated(function () {
            $this->user = User::factory()->create();
            $this->token = JWTAuth::fromUser($this->user);
        });
    }

    public function authenticated(string $token = null)
    {
        return $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . ($token ?? $this->token),
                'Accept' => 'application/json'
            ]
        );
    }
}