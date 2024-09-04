<?php

namespace Tests\Feature\Discussions;

use App\Models\Discussion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\TestCase;

class DiscussionCrudAuthTest extends TestCase
{
    use RefreshDatabase, Authentication;

    private function createDiscussion(User $user): Discussion
    {
        return Discussion::create([
            'title' => 'Discussion title',
            'description' => 'Discussion description',
            'author_id' => $user->id,
        ]);
    }

    public function test_not_auth_user_cannot_get_all_discussions(): void
    {
        $this->authenticated('invalid-token')
            ->get('/api/v1/discussions')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_auth_user_can_get_all_discussions(): void
    {
        $this->createDiscussion($this->user);

        $this->authenticated()
            ->get("/api/v1/discussions")
            ->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'description',
                    'author_id',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function test_auth_user_can_get_discussion_by_id(): void
    {        
        $discussion = $this->createDiscussion($this->user);

        $this->authenticated()
            ->get("/api/v1/discussions/{$discussion->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $discussion->id,
                'title' => 'Discussion title',
                'description' => 'Discussion description',
                'author_id' => $this->user->id,
            ]);
    }

    public function test_auth_user_can_create_discussion_only_required_fields(): void
    {
        $this->authenticated()
            ->post('/api/v1/discussions', [
                'title' => 'Discussion title',
                'author_id' => $this->user->id
            ])
            ->assertCreated(201)
            ->assertJsonFragment([
                'title' => 'Discussion title',
                'author_id' => $this->user->id
            ]);
    }

    public function test_auth_user_can_update_discussion_only_required_fields(): void
    {
        $discussion = $this->createDiscussion($this->user);

        $this->authenticated()
            ->put("/api/v1/discussions/{$discussion->id}", [
                'title' => 'Discussion title updated',
                'author_id' => $this->user->id
            ])
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Discussion title updated',
                'author_id' => $this->user->id
            ]);
    }

    public function test_auth_user_can_delete_discussion(): void
    {
        $discussion = $this->createDiscussion($this->user);

        $this->authenticated()
            ->delete("/api/v1/discussions/{$discussion->id}")
            ->assertStatus(200)
            ->assertJson(
                ['message' => 'Discussion deleted successfully']
            );
    }
}
