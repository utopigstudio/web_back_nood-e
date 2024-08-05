<?php

namespace Tests\Feature\Organizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrganizationsCrudTest extends TestCase
{

    use RefreshDatabase;

    private function createOrganization(): void
    {
        Organization::create([
            'name' => 'Organization name', 
            'description' => 'Organization description',
            'team' => json_encode([
                ['name' => 'User1'],
                ['name' => 'User2'],
                ['name' => 'User3']
            ]),                                     
            'image' => 'image.jpg',
            'role_id' => 1
        ]);
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

        $organizations = Organization::factory(3)->create();
        $this->createOrganization();
        $response = $this->get('/api/v1/organizations');

        // dd($response->getContent());

        $response->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(4)
            ->assertJsonStructure([
                '*' => [
                    'name', 
                    'description',
                    'team' => json_encode([
                        'team' => [
                        ['name' => 'User1'],
                        ['name' => 'User2'],
                        ['name' => 'User3']
                        ]
                    ]),                  
                    'image',
                    'user_id'
                    ]])
            ->assertJsonFragment([
                'name' => 'Organization name',
                'description' => 'Organization description',
                'team' => [
                    'name' => 'User1',
                    'name' => 'User2',
                    'name' => 'User3'
                    ],
                'image' => 'image.jpg',
                'user_id' => 1
            ]);
    }

    public function test_auth_user_can_get_organization_by_id(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $this->createOrganization();

        $response = $this->get('/api/v1/organizations/1')->assertJson([
            'name' => 'Organization name', 
            'description' => 'Organization description',
            'team' => 'Organization team',
            'image' => 'image.jpg',
            'user_id' => 1
        ]);

        $response->assertStatus(200);
    }

    public function test_auth_user_can_create_organization_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $response = $this->post('/api/v1/organizations', [
            'name' => 'Organization name', 
            'description' => 'Organization description',
            'team' => 'Organization team',
            'image' => 'image.jpg',
            'user_id' => 1
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Organization name', 
                'description' => 'Organization description',
                'team' => 'Organization team',
                'image' => 'image.jpg',
                'user_id' => 1
        ])->assertCreated();
    }

    public function test_auth_user_can_update_organization_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $this->createOrganization();

        $response = $this->put('/api/v1/organizations/1', [
            'name' => 'Updated Organization name', 
            'description' => 'Organization description',
            'team' => 'Organization team',
            'image' => 'image.jpg',
            'user_id' => 1
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Organization name', 
                'description' => 'Organization description',
                'team' => 'Organization team',
                'image' => 'image.jpg',
                'user_id' => 1
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
