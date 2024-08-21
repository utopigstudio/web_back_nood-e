<?php

namespace Tests\Feature\Organizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrganizationCrudAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createOrganization($user): Organization
    {
        return Organization::create([
            'name' => 'Organization name', 
            'description' => 'Organization description',                                 
            'image' => 'image.jpg',
            'user_id' => $user->id
        ]);
        
    }

    private function createAuthUser (): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }


    public function test_auth_user_can_get_all_organizations(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->createOrganization($user);
        $response = $this->get('/api/v1/organizations');

        $response->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'name', 
                    'description',            
                    'image',
                    'user_id',
                    'updated_at',
                    'created_at',
                    ]]);
    }

    public function test_auth_user_can_get_organization_by_id(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->createOrganization($user);

        $response = $this->get('/api/v1/organizations/1')
            ->assertJson([
                'name' => 'Organization name', 
                'description' => 'Organization description',
                'image' => 'image.jpg',
                'user_id' => $user->id
                ])
            ->assertJsonStructure([
                    'name', 
                    'description',             
                    'image',
                    'user_id',])
            ->assertStatus(200);
    }

    public function test_auth_user_can_create_organization_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $data = [
            'name' => 'Organization name', 
            'description' => 'Organization description',
            'user_id' => $user->id
        ];

        $response = $this->post('/api/v1/organizations', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Organization name', 
                'description' => 'Organization description',
                'user_id' => $user->id
        ])->assertCreated();
    }

    public function test_auth_user_can_update_organization_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);;

        $this->createOrganization($user);

        $response = $this->put('/api/v1/organizations/1', [
            'name' => 'Updated organization name', 
            'description' => 'Updated organization description',
            'image' => 'image.jpg',
            'user_id' => $user->id
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated organization name', 
                'description' => 'Updated organization description',
                'image' => 'image.jpg',
                'user_id' => $user->id
            ]);
    }

    public function test_auth_user_can_delete_organization(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->createOrganization($user);

        $response = $this->delete('/api/v1/organizations/1');

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
