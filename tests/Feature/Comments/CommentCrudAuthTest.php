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

    private function createComment($user, $topic): void
    {
        Comment::create([
            'description' => 'Comment description',
            'user_id' => $user->id,
            'topic_id' => $topic->id
        ]);
    }

    private function createTopic($user): Topic
    {
        $discussion = $this->createDiscussion($user);
        return Topic::create([
            'title' => 'Topic title',
            'description' => 'Topic description',
            'user_id' => $user->id,
            'discussion_id' => $discussion->id,
        ]);
    }

    private function createDiscussion($user): Discussion
    {
        return Discussion::create([
            'title' => 'Discussion title',
            'description' => 'Discussion description',
            'user_id' => $user->id,
        ]);
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

        $topic = $this->createTopic($user);

        $this->createComment($user, $topic);

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
        
        $topic = $this->createtopic($user);

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
        $topic = $this->createtopic($user);

        $this->createComment($user, $topic);

        $response = $this->delete('/api/v1/comments/1');

        $response->assertNoContent();
    }
}
