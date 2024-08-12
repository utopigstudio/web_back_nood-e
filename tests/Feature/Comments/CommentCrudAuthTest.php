<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentCrudAuthTest extends TestCase
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

    private function createComment(): void
    {
        Comment::create([
            'description' => 'Comment description',
            'user_id' => 1,
            'topic_id' => 1
        ]);
    }

    private function createTopic(): Topic
    {
        $discussion = $this->createDiscussion();
        $topic = Topic::create([
            'title' => 'Topic title',
            'description' => 'Topic description',
            'user_id' => 1,
            'discussion_id' => $discussion->id,
        ]);

        return $topic;
    }

    private function createDiscussion(): Discussion
    {
        $discussion = Discussion::create([
            'title' => 'Discussion title',
            'description' => 'Discussion description',
            'user_id' => 1
        ]);

        return $discussion;
    }

    public function test_route_auth_commets_retrieves_ok_status(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $response = $this->get('/api/v1/comments');

        $response->assertStatus(200);
    }   

    public function test_get_all_comments_as_json(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $comment = $this->createComment();

        $response = $this->get('/api/v1/comments');

        $response->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'description',
                    'user_id',
                    'topic_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_update_comment_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $topic = $this->createtopic();

        $response = $this->post('/api/v1/comments', [
            'description' => 'Comment description',
            'user_id' => $user->id,
            'topic_id' => $topic->id
        ]);

        $response->assertCreated()
        ->assertJsonFragment([
            'description' => 'Comment description',
            'user_id' => $user->id,
            'topic_id' => $topic->id
        ]);
    }

    public function test_delete_comment(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this->actingAs($user);

        $this->createComment();

        $response = $this->delete('/api/v1/comments/1');

        $response->assertNoContent();
    }
}
