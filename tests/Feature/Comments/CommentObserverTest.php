<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentObserverTest extends TestCase
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
        $topic = Topic::create([
            'title' => 'Topic title',
            'description' => 'Topic description',
            'discussion_id' => $discussion->id,
            'user_id' => 1
        ]);

        return $topic;
    }

    public function test_comment_creation_increments_comments_count()
    {
        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $discussion = $this->createDiscussion();
        $topic = $this->createTopic($discussion);

        $this->assertEquals(0, $topic->comments_counter);

        Comment::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ]);

        $topic->refresh();
        $this->assertEquals(1, $topic->comments_counter);
    }

    public function test_comment_creation_decrements_comments_count()
    {
        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $discussion = $this->createDiscussion();
        $topic = $this->createTopic($discussion);

        $comment = Comment::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ]);

        $topic->refresh();
        $this->assertEquals(1, $topic->comments_counter);

        $comment->delete();


        $topic->refresh();
        $this->assertEquals(null, $topic->comments_counter);
    }
}
