<?php

namespace Tests\Feature\Topics;

use App\Models\Discussion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TopicCrudAuthTest extends TestCase
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

    private function createTopic(Discussion $discussion): Topic
    {
        return Topic::create([
            'title' => 'Topic title',
            'description' => 'Topic description',
            'discussion_id' => 1,
            'user_id' => 1
        ]);
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

    public function test_route_auth_topics_retrieves_ok_status(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $discussion = $this->createDiscussion($user);

        $response = $this->get("/api/v1/discussions/{$discussion->id}");

        $response->assertStatus(200);
    }

    public function test_get_all_topics_from_discussion_as_json(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $discussion = $this->createDiscussion($user);

        $this->createTopic($discussion);

        $response = $this->get("/api/v1/discussions/{$discussion->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Topic title',
                'description' => 'Topic description',
                'discussion_id' => 1,
                'user_id' => 1
            ]);
    }

    public function test_get_single_topic_as_json(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $discussion = $this->createDiscussion();

        $topic = $this->createTopic($discussion);

        $response = $this->get("/api/v1/discussions/{$discussion->id}/{$topic->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'topic' => [
                    'title',
                    'description',
                    'discussion_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ],
                'comments' => [
                    '*' => [
                        'description',
                        'user_id',
                        'topic_id',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_auth_user_can_create_topic_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $discussion = $this->createDiscussion($user);

        $data = [
            'title' => 'Topic title',
            'description' => 'Topic description',
            'discussion_id' => $discussion->id,
            'user_id' => $user->id
        ];

        $response = $this->post("/api/v1/discussions/{$discussion->id}", $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Topic title',
                'description' => 'Topic description',
                'discussion_id' => $discussion->id,
                'user_id' => $user->id
            ]);
    }

    public function test_auth_user_can_update_topic_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $discussion = $this->createDiscussion();
        $topic = $this->createTopic($discussion);

        $data = [
            'title' => 'Updated topic title',
            'description' => 'Updated topic description',
            'discussion_id' => $discussion->id,
            'user_id' => $user->id
        ];

        $response = $this->put("/api/v1/discussions/{$discussion->id}/{$topic->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated topic title',
                'description' => 'Updated topic description',
                'discussion_id' => $discussion->id,
                'user_id' => $user->id
            ])
            ->assertJsonStructure([
                'title',
                'description',
                'discussion_id',
                'user_id',
                'created_at',
                'updated_at'
            ]);
    }

    public function test_auth_user_can_delete_topic(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $discussion = $this->createDiscussion();

        $topic = $this->createTopic($discussion);

        $response = $this->delete("/api/v1/discussions/{$discussion->id}/{$topic->id}");

        $response->assertStatus(204)
            ->assertNoContent();
    }



    
}
