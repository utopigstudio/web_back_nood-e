<?php

namespace Tests\Feature\Events;

use App\Models\Event;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Support\Authentication;
use Tests\TestCase;

class EventCrudAuthTest extends TestCase
{
    use RefreshDatabase, Authentication;

    private function createEvent($room, $user): Event
    {
        return Event::create([
            'title' => 'Event title',
            'description' => 'Event description',
            'start' => '2024-09-13 12:00:00',
            'end' => '2024-09-13 14:00:00',
            'meet_link' => 'https://meet.google.com/abc-def-ghi',
            'room_id' => $room->id,
            'author_id' => $user->id,
        ]);
    }

    private function createRoom(): Room
    {
        return Room::create([
            'name' => 'Room 1',
            'description' => 'Room 1 description',
            'is_available' => true
        ]);
    }

    public function test_not_auth_user_cannot_get_all_events(): void
    {
        $this->authenticated('invalid-token')
            ->get('/api/v1/events')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_auth_user_can_get_all_events(): void
    {
        $room = $this->createRoom();
        $this->createEvent($room, $this->user);

        $this->authenticated()
            ->get('/api/v1/events')
            ->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'title',
                    'description',
                    'start',
                    'end',
                    'meet_link',
                    'room_id',
                    'author_id'
                ]
            ]);
    }

    public function test_auth_user_can_get_event_by_id(): void
    {
        $room = $this->createRoom();
        $event = $this->createEvent($room, $this->user);

        $this->authenticated()
            ->get("/api/v1/events/{$event->id}")
            ->assertStatus(200)
            ->assertJson([
                'title' => 'Event title',
                'description' => 'Event description',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
                'meet_link' => 'https://meet.google.com/abc-def-ghi',
                'room_id' => $room->id,
                'author_id' => $this->user->id
            ]);
    }

    public function test_auth_user_can_create_event_only_required_fields(): void
    {
        $this->authenticated()
            ->post('/api/v1/events', [
                'title' => 'Event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
            ])
            ->assertCreated()
            ->assertJson([
                'title' => 'Event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
                'author_id' => $this->user->id
            ]);
    }

    public function test_update_event_only_required_fields(): void
    {
        $room = $this->createRoom();
        $event = $this->createEvent($room, $this->user);

        $this->authenticated()
            ->put("/api/v1/events/{$event->id}", [
                'title' => 'Updated event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
            ])->assertStatus(200)
            ->assertJson([
                'title' => 'Updated event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
                'author_id' => $this->user->id
            ]);
    }

    public function test_auth_user_cannot_update_event_not_authored(): void
    {
        $room = $this->createRoom();
        
        $user = User::factory()->create();
        $event = $this->createEvent($room, $user);

        $this->authenticated()
            ->put("/api/v1/events/{$event->id}", [
                'title' => 'Updated event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
            ])
            ->assertStatus(403);
    }

    public function test_auth_admin_can_update_event_not_authored(): void
    {
        $room = $this->createRoom();
        
        $user = User::factory()->create();
        $event = $this->createEvent($room, $user);

        $this->userRoleAdmin()
            ->authenticated()
            ->put("/api/v1/events/{$event->id}", [
                'title' => 'Updated event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
            ])
            ->assertStatus(200)
            ->assertJson([
                'title' => 'Updated event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
                'author_id' => $user->id
            ]);
    }


    public function test_auth_user_can_create_event_with_members(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->authenticated()
            ->post('/api/v1/events', [
                'title' => 'Event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
                'members' => [$user1->id, $user2->id]
            ])
            ->assertCreated(201)
            ->assertJson([
                'title' => 'Event title',
                'author_id' => $this->user->id,
                'members' => [
                    ['id' => $user1->id, 'name' => $user1->name],
                    ['id' => $user2->id, 'name' => $user2->name]
                ]
            ]);
    }

    public function test_auth_user_can_update_event_with_members(): void
    {
        $room = $this->createRoom();
        $event = $this->createEvent($room, $this->user);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->authenticated()
            ->put("/api/v1/events/{$event->id}", [
                'title' => 'Event title updated',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
                'members' => [$user1->id, $user2->id]
            ])
            ->assertStatus(200)
            ->assertJson([
                'title' => 'Event title updated',
                'author_id' => $this->user->id,
                'members' => [
                    ['id' => $user1->id, 'name' => $user1->name],
                    ['id' => $user2->id, 'name' => $user2->name]
                ]
            ]);

        $this->authenticated()
            ->put("/api/v1/events/{$event->id}", [
                'title' => 'Event title updated',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
                'members' => [$user1->id]
            ])
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('members', 1)
                ->missing($user2->name)
                ->etc()
            );
    }

    public function test_auth_user_can_delete_event(): void
    {
        $room = $this->createRoom();
        $event = $this->createEvent($room, $this->user);

        $this->authenticated()
            ->delete("/api/v1/events/{$event->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Event deleted successfully']);
    }

    public function test_auth_user_cannot_delete_event_not_authored(): void
    {
        $room = $this->createRoom();
        $user = User::factory()->create();
        $event = $this->createEvent($room, $user);

        $this->authenticated()
            ->delete("/api/v1/events/{$event->id}")
            ->assertStatus(403);
    }

    public function test_auth_admin_can_delete_event_not_authored(): void
    {
        $room = $this->createRoom();
        $user = User::factory()->create();
        $event = $this->createEvent($room, $user);

        $this->userRoleAdmin()
            ->authenticated()
            ->delete("/api/v1/events/{$event->id}")
            ->assertStatus(200)
            ->assertJson(['message' => 'Event deleted successfully']);
    }
}
