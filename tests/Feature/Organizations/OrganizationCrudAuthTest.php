<?php

namespace Tests\Feature\Organizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrganizationCrudAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createOrganization()
    {
        $organization = Organization::create([
            'name' => 'Organization name', 
            'description' => 'Organization description',                                 
            'image' => 'image.jpg'
        ]);
        return $organization;
    }

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


    public function test_auth_user_can_get_all_organizations(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $this->createOrganization();
        $response = $this->get('/api/v1/organizations');

        // dd($response->getContent());

        $response->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'name', 
                    'description',            
                    'image',
                    'updated_at',
                    'created_at',
                    ]]);
    }

    public function test_auth_user_can_get_organization_by_id(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $this->createOrganization();

        $response = $this->get('/api/v1/organizations/1')
            ->assertJson([
                'name' => 'Organization name', 
                'description' => 'Organization description',
                'image' => 'image.jpg'
                ])
            ->assertJsonStructure([
                    'name', 
                    'description',             
                    'image'])
            ->assertStatus(200);
    }

    public function test_auth_user_can_create_organization_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $data = [
            'name' => 'Organization name', 
            'description' => 'Organization description'
        ];

        $response = $this->post('/api/v1/organizations', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Organization name', 
                'description' => 'Organization description'
        ])->assertCreated();
    }

    public function test_auth_user_can_update_organization_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $this->createOrganization();

        $response = $this->put('/api/v1/organizations/1', [
            'name' => 'Updated organization name', 
            'description' => 'Updated organization description',
            'image' => 'image.jpg'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated organization name', 
                'description' => 'Updated organization description',
                'image' => 'image.jpg',
            ]);
    }

    public function test_auth_user_can_delete_organization(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $this->createOrganization();

        $response = $this->delete('/api/v1/organizations/1');

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
