<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\UserInviteNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_verified_by_accepting_invitation(): void
    {
        $user = User::create([
            'name' => 'John',
            'email' => 'johndoe@mail.com'
        ]);

    $verificationUrl = URL::signedRoute('invitation-accepted', $user);

    $response = $this->actingAs($user)->get($verificationUrl);


    $response->assertStatus(200);
    $this->assertTrue($user->fresh()->hasVerifiedEmail());
    $response->assertJsonFragment(['message' => 'Email verified successfully']);
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::signedRoute('invitation-accepted', $user);

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
