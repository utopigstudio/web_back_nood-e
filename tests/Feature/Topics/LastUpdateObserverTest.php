<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LastUpdateObserverTest extends TestCase
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
        return Discussion::create([
            'title' => 'Discussion title',
            'description' => 'Discussion description',
            'user_id' => 1,
        ]);
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

    public function test_last_update_is_updated_when_comment_is_created()
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $discussion = $this->createDiscussion();

        $topic = $this->createTopic($discussion);

        $this->assertNull($topic->last_update);
        $this->assertCount(0, $topic->comments);

        Comment::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ]);
        
        $topic->refresh();

        $this->assertNotNull($topic->last_update);
        $this->assertEquals(now(), $topic->last_update);
        $this->assertCount(1, $topic->comments);

    }
}
