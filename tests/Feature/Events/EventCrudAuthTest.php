<?php

namespace Tests\Feature\Events;

use App\Models\Event;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventCrudAuthTest extends TestCase

{
    use RefreshDatabase;

    private function createEvent($room, $user): Event
    {
        return Event::create([
            'title' => 'Event title',
            'description' => 'Event description',
            'start' => '2024-09-13 12:00:00',
            'end' => '2024-09-13 14:00:00',
            'meet_link' => 'https://meet.google.com/abc-def-ghi',
            'room_id' => $room->id,
            'user_id' => $user->id,
        ]);
    }

    private function createAuthUser (): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    private function createRoom(): Room
    {
        return Room::create([
            'name' => 'Room 1',
            'image' => 'room1.jpg', 
            'description' => 'Room 1 description',
            'is_available' => true
        ]);

    }


    public function test_get_all_events_as_json(): void
    {
        $this->withoutExceptionHandling();

        $room = $this->createRoom();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);;

        $this->createEvent($room, $user);
        $response = $this->get('/api/v1/events');

        $response->assertStatus(200)
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
                    'user_id'
                ]
            ])
            ->assertJsonFragment([
                'title' => 'Event title',
                'description' => 'Event description',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
                'meet_link' => 'https://meet.google.com/abc-def-ghi',
                'room_id' => $room->id,
                'user_id' => $user->id
            ]);
    }

    public function test_get_event_by_id(): void
    {
        $this->withoutExceptionHandling();

        $room = $this->createRoom();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $this->createEvent($user, $room);

        $response = $this->get('/api/v1/events/1')->assertJson([
            'title' => 'Event title',
            'description' => 'Event description',
            'start' => '2024-09-13 12:00:00',
            'end' => '2024-09-13 14:00:00',
            'meet_link' => 'https://meet.google.com/abc-def-ghi',
            'room_id' => $room->id,
            'user_id' => $user->id
        ]);

        $response->assertStatus(200);
    }

    public function test_create_event_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $response = $this->post('/api/v1/events', [
            'title' => 'Event title',
            'start' => '2024-09-13 12:00:00',
            'end' => '2024-09-13 14:00:00',
            'user_id' => $user->id
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
        ])->assertCreated();
    }

    public function test_update_event_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $room = $this->createRoom();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $this->createEvent($user, $room);

        $response = $this->put('/api/v1/events/1', [
            'title' => 'Updated event title',
            'start' => '2024-09-13 12:00:00',
            'end' => '2024-09-13 14:00:00',
            'user_id' => $user->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated event title',
                'start' => '2024-09-13 12:00:00',
                'end' => '2024-09-13 14:00:00',
            ]);
    }

    public function test_delete_event(): void
    {
        $this->withoutExceptionHandling();

        $room = $this->createRoom();

        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $authData['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->actingAs($user);

        $this->createEvent($user, $room);

        $response = $this->delete('/api/v1/events/1');

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
