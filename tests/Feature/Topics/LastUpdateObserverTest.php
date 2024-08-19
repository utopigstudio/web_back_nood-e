<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LastUpdateObserverTest extends TestCase
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

    public function test_last_update_is_updated_when_comment_is_created()
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();

        $discussion = $this->createDiscussion();

        $topic = $this->createTopic($discussion);

        $this->assertNull($topic->last_update);

        $comment = Comment::factory()->create([
            'topic_id' => $topic->id,
            'user_id' => $user->id,
        ]);
        
        $topic->refresh();

        $this->assertNotNull($topic->last_update);
        $this->assertEquals(now(), $topic->last_update);

    }
}
