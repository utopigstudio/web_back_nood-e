<?php

namespace Tests\Feature\Organizations;

use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\TestCase;

class OrganizationCrudAuthTest extends TestCase
{
    use RefreshDatabase, Authentication;

    private function createOrganization($user): Organization
    {
        return Organization::create([
            'name' => 'Organization name',
            'description' => 'Organization description',
            'owner_id' => $user->id
        ]);
    }

    public function test_not_auth_user_cannot_get_all_organizations(): void
    {
        $this->authenticated('invalid-token')
            ->get('/api/v1/organizations')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_auth_user_can_get_all_organizations(): void
    {
        $this->createOrganization($this->user);

        $this->authenticated()
            ->get('/api/v1/organizations')
            ->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'description',
                    'image',
                    'owner_id',
                    'updated_at',
                    'created_at',
            ]]);
    }

    public function test_auth_user_can_get_organization_by_id(): void
    {
        $organization = $this->createOrganization($this->user);

        $this->authenticated()
            ->get('/api/v1/organizations/'.$organization->id)
            ->assertJson([
                'name' => 'Organization name',
                'description' => 'Organization description',
                'owner_id' => $this->user->id
            ])
            ->assertStatus(200);
    }

    public function test_auth_user_can_create_organization_only_required_fields(): void
    {
        $data = [
            'name' => 'Organization name', 
            'owner_id' => $this->user->id
        ];

        $this->authenticated()
            ->post('/api/v1/organizations', $data)
            ->assertCreated(201)
            ->assertJsonFragment([
                'name' => 'Organization name', 
                'owner_id' => $this->user->id
        ]);
    }

    public function test_auth_user_can_update_organization_only_required_fields(): void
    {
        $organization = $this->createOrganization($this->user);

        $data = [
            'name' => 'Updated organization name', 
            'owner_id' => $this->user->id
        ];

        $this->authenticated()
            ->put('/api/v1/organizations/'.$organization->id, $data)
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated organization name', 
                'owner_id' => $this->user->id
            ]);
    }

    public function test_auth_user_can_delete_organization(): void
    {
        $organization = $this->createOrganization($this->user);

        $this->authenticated()
            ->delete('/api/v1/organizations/'.$organization->id)
            ->assertStatus(200)
            ->assertJson(
                ['message' => 'Organization deleted successfully']
            );
    }
}
