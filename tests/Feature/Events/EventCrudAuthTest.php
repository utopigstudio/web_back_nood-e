<?php

namespace Tests\Feature\Events;

use App\Models\Event;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventCrudAuthTest extends TestCase

{
    use RefreshDatabase;

    private function createEvent(): void
    {
        Event::create([
            'title' => 'Event title',
            'description' => 'Event description',
            'date' => '30-09-2024',
            'start' => '12:00',
            'end' => '14:00',
            'room_id' => 'Room 1',
            'meet_link' => 'https://meet.google.com/abc-def-ghi',
        ]);
    }

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


    public function test_get_all_events_as_json(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);
        $this->createEvent();
        $response = $this->get('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => [
                    'title',
                    'description',
                    'date',
                    'start',
                    'end',
                    'room_id',
                    'meet_link'
                ]
            ])
            ->assertJsonFragment([
                'title' => 'Event title',
                'description' => 'Event description',
                'date' => '30-09-2024',
                'start' => '12:00',
                'end' => '14:00',
                'room_id' => 'Room 1',
                'meet_link' => 'https://meet.google.com/abc-def-ghi'
            ]);
    }

    public function test_get_event_by_id(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);
        $this->createEvent();

        $response = $this->get('/api/v1/events/1')->assertJson([
            'title' => 'Event title',
            'description' => 'Event description',
            'date' => '30-09-2024',
            'start' => '12:00',
            'end' => '14:00',
            'room_id' => 'Room 1',
            'meet_link' => 'https://meet.google.com/abc-def-ghi'
        ]);

        $response->assertStatus(200);
    }

    public function test_create_event_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $response = $this->post('/api/v1/events', [
            'title' => 'Event title',
            'date' => '30-09-2024',
            'start' => '12:00',
            'end' => '14:00',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Event title',
                'date' => '30-09-2024',
                'start' => '12:00',
                'end' => '14:00',
        ])->assertCreated();
    }

    public function test_update_event_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $this->createEvent();

        $response = $this->put('/api/v1/events/1', [
            'title' => 'Updated event title',
            'date' => '30-09-2024',
            'start' => '12:00',
            'end' => '14:00',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated event title',
                'date' => '30-09-2024',
                'start' => '12:00',
                'end' => '14:00',
            ]);
    }

    public function test_delete_event(): void
    {
        $this->withoutExceptionHandling();

        $user = $this->createAuthUser();
        $this-> actingAs($user);

        $this->createEvent();

        $response = $this->delete('/api/v1/events/1');

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
