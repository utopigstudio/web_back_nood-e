<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentCrudAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser (): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    private function createComment(): Comment
    {
        return Comment::create([
            'description' => 'Comment description',
            'user_id' => 1,
            'topic_id' => 1
        ]);
    }

    private function createTopic(): Topic
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
        return Discussion::create([
            'title' => 'Discussion title',
            'description' => 'Discussion description',
            'user_id' => 1,
        ]);
    }

    public function test_route_auth_comments_retrieves_ok_status(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $discussion = $this->createDiscussion($user);
        $topic = $this->createTopic($discussion, $user);

        $response = $this->get("api/v1/discussions/{$discussion->id}/{$topic->id}");

        $response->assertStatus(200);
    }   

    public function test_get_comments_in_topic(): void
    {
        $this->withoutExceptionHandling();
    
        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);
    
        $discussion = $this->createDiscussion();
        $topic = $this->createTopic();

        Comment::create([
            'description' => 'Comment description',
            'user_id' => 1,
            'topic_id' => 1
        ]);


        $response = $this->get("/api/v1/discussions/{$discussion->id}/{$topic->id}");
    
        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                'comments' => [
                    '*' => [
                        'description',
                        'user_id',
                        'topic_id',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'topic' => [
                    'title',
                    'description',
                    'discussion_id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_create_comment_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $discussion = $this->createDiscussion($user);
        $topic = $this->createTopic($discussion, $user);;

        $data = [
            'description' => 'Comment description',
            'user_id' => $user->id,
            'topic_id' => $topic->id
        ];

        $response = $this->post("/api/v1/discussions/{$discussion->id}/{$topic->id}", $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'description' => 'Comment description',
                'user_id' => $user->id,
                'topic_id' => $topic->id
            ]);
    }

    public function test_update_comment_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);
        
        $discussion = $this->createDiscussion($user);
        $topic = $this->createTopic($discussion, $user);;

        $comment = $this->createComment($user, $topic);

        $data = [
            'description' => 'Update comment description',
            'user_id' => $user->id,
            'topic_id' => $topic->id
        ];

        $response = $this->put("/api/v1/discussions/{$discussion->id}/{$topic->id}/{$comment->id}", $data);
        

        $response->assertStatus(200)
        ->assertJsonFragment([
            'description' => 'Update comment description',
            'user_id' => $user->id,
            'topic_id' => $topic->id
        ]);
    }

    public function test_delete_comment(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $discussion = $this->createDiscussion();
        $topic = $this->createTopic();;

        $comment = $this->createComment();

        $response = $this->delete("/api/v1/discussions/{$discussion->id}/{$topic->id}/{$comment->id}");

        $response->assertNoContent();
    }
}
