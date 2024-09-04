<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\TestCase;

class UserCrudAuthTest extends TestCase
{
    use RefreshDatabase, Authentication;

    private function createUser(): User
    {
        return User::create([
            'name' => 'User name',
            'surname' => 'User surname',
            'description' => 'User description',
            'email' => 'test@test.com',
        ]);
    }

    public function test_not_auth_user_cannot_get_all_users(): void
    {
        $this->authenticated('invalid-token')
            ->get('/api/v1/users')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_auth_user_can_get_all_users(): void
    {
        $this->createUser();

        $this->authenticated()
            ->get('/api/v1/users')
            ->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'surname',
                    'description',
                    'image',
                    'organization_id',
                    'updated_at',
                    'created_at',
            ]]);
    }

    public function test_auth_user_can_get_user_by_id(): void
    {
        $user = $this->createUser();

        $this->authenticated()
            ->get('/api/v1/users/'.$user->id)
            ->assertJson([
                'name' => 'User name',
                'surname' => 'User surname',
                'description' => 'User description',
                'email' => 'test@test.com',
            ])
            ->assertStatus(200);
    }

    public function test_auth_user_can_create_user_only_required_fields(): void
    {
        $data = [
            'name' => 'User name', 
            'email' => 'test@test.com',
        ];

        $this->authenticated()
            ->post('/api/v1/users', $data)
            ->assertCreated(201)
            ->assertJson([
                'message' => 'Invitation sent successfully',
                'user' => [
                    'name' => 'User name', 
                    'email' => 'test@test.com',
                ],
        ]);
    }

    public function test_auth_user_can_update_user_only_required_fields(): void
    {
        $user = $this->createUser();

        $data = [
            'name' => 'Updated user name', 
            'email' => 'updated@test.com',
        ];

        $this->authenticated()
            ->put('/api/v1/users/'.$user->id, $data)
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated user name', 
                'email' => 'updated@test.com',
            ]);
    }

    public function test_auth_user_can_delete_user(): void
    {
        $user = $this->createUser();

        $this->authenticated()
            ->delete('/api/v1/users/'.$user->id)
            ->assertStatus(200)
            ->assertJson(
                ['message' => 'User deactivated successfully']
            );
    }
}
