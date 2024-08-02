<?php

namespace Tests\Feature\Events;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventCrudTest extends TestCase

{
    use RefreshDatabase;

    private function createEvent(): void
    {
        Event::create([
            'title' => 'Event title',
            'description' => 'Event description',
            'date' => '2024-09-01',
            'start' => '2024-09-01 12:00:00',
            'end' => '2024-09-01 14:00:00',
            'room_id' => 'Room 1',
            'price' => 100,
            'image' => 'image.jpg'
        ]);
    }


    public function test_get_all_events(): void
    {
        $this->withoutExceptionHandling();

        $events = Event::factory(3)->create();
        $this->createEvent();
        $response = $this->get('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonIsArray()
            ->assertJsonCount(4)
            ->assertJsonStructure([
                '*' => [
                    'title',
                    'description',
                    'date',
                    'start',
                    'end',
                    'room_id',
                    'price',
                    'image'
                ]
            ])
            ->assertJsonFragment([
                'title' => 'Event title',
                'description' => 'Event description',
                'date' => '2024-09-01',
                'start' => '2024-09-01 12:00:00',
                'end' => '2024-09-01 14:00:00',
                'room_id' => 'Room 1',
                'price' => 100,
                'image' => 'image.jpg'
            ]);
    }

    public function test_get_event_by_id(): void
    {
        $this->withoutExceptionHandling();

        $this->createEvent();

        $response = $this->get('/api/v1/events/1')->assertJson([
            'title' => 'Event title',
            'description' => 'Event description',
            'date' => '2024-09-01',
            'start' => '2024-09-01 12:00:00',
            'end' => '2024-09-01 14:00:00',
            'room_id' => 'Room 1',
            'price' => 100,
            'image' => 'image.jpg'
        ]);

        $response->assertStatus(200);
    }

    public function test_create_event_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/v1/events', [
            'title' => 'Event title',
            'date' => '2024-09-01',
            'start' => '2024-09-01 12:00:00',
            'end' => '2024-09-01 14:00:00',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Event title',
                'date' => '2024-09-01',
                'start' => '2024-09-01 12:00:00',
                'end' => '2024-09-01 14:00:00',
        ])->assertCreated();
    }

    public function test_update_event_only_required_fields(): void
    {
        $this->withoutExceptionHandling();

        $this->createEvent();

        $response = $this->put('/api/v1/events/1', [
            'title' => 'Updated event title',
            'date' => '2024-09-01',
            'start' => '2024-09-01 12:00:00',
            'end' => '2024-09-01 14:00:00',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated event title',
                'date' => '2024-09-01',
                'start' => '2024-09-01 12:00:00',
                'end' => '2024-09-01 14:00:00',
            ]);
    }

    public function test_delete_event(): void
    {
        $this->withoutExceptionHandling();

        $this->createEvent();

        $response = $this->delete('/api/v1/events/1');

        $response->assertStatus(204)
            ->assertNoContent();
    }
}
