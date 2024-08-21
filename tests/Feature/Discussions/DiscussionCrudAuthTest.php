<?php

namespace Tests\Feature\Discussions;

use App\Models\Discussion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DiscussionCrudAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser (): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    private function createDiscussion(): Discussion
    {
        $discussion = Discussion::create([
            'title' => 'Discussion title',
            'description' => 'Discussion description',
            'user_id' => 1,
        ]);

        return $discussion;
    }

    private function createTopic(Discussion $discussion): Topic
    {
        return Topic::create([
            'title' => 'Topic title',
            'description' => 'Topic description',
            'discussion_id' => 1,
            'user_id' => 1
        ]);
    }

    public function test_route_auth_retrieves_ok_status(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response = $this->get('/api/v1/discussions');

        $response->assertStatus(200);
    }

    public function test_get_all_discussions_as_json(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->createDiscussion();

        $response = $this->get("/api/v1/discussions");

        $response->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'description',
                    'user_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function test_get_single_discussion_as_json(): void
    {
        $this->withoutExceptionHandling();
        
        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);
        
        $discussion = $this->createDiscussion($user);

        $this->createTopic($discussion);

        $response = $this->get("/api/v1/discussions/{$discussion->id}");
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $discussion->id,
                'title' => 'Discussion title',
                'description' => 'Discussion description',
                'user_id' => $user->id,
            ]);
    }

    public function test_create_discussion(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response = $this->post('/api/v1/discussions', [
            'title' => 'Discussion title',
            'description' => 'Discussion description',
            'user_id' => 1
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'user_id',
                'created_at',
                'updated_at',
            ])
            ->assertJsonFragment([
                'title' => 'Discussion title',
                'description' => 'Discussion description',
                'user_id' => 1
            ]);
    }

    public function test_update_discussion(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $discussion = $this->createDiscussion();
        $response = $this->put("/api/v1/discussions/{$discussion->id}", [
            'title' => 'Discussion title updated',
            'description' => 'Discussion description updated',
            'user_id' => 1
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'user_id',
                'created_at',
                'updated_at',
            ])
            ->assertJsonFragment([
                'title' => 'Discussion title updated',
                'description' => 'Discussion description updated',
                'user_id' => 1
            ]);
    }

    public function test_delete_discussion(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $discussion = $this->createDiscussion();

        $response = $this->delete("/api/v1/discussions/{$discussion->id}");

        $response->assertStatus(204);
    }
}
