<?php

namespace Tests\Feature\Rooms;

use App\Models\Room;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoomCrudAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser (): Authenticatable
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@mail.com',
            'role_id' => 1,
            'password' => bcrypt('password123')
        ]);

        $token = JWTAuth::fromUser($user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        return $user;
    }

    private function createRoom (): Room
    {
        $room = Room::create([
            'name' => 'Room name',
            'is_available' => 'available',
        ]);

        return $room;
    }

    public function test_users_can_create_rooms()
    {
        $user = $this->createAuthUser();

        $this->actingAs($user);
        
        $this->createRoom();

        $response = $this->post('/api/v1/rooms', [
            'name' => 'Room name',
            'description' => 'Room description',
            'image' => 'image.jpg',
            'is_available' => 'available',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'name',
                'description',
                'image',
                'is_available',
            ]);

    }

    public function test_users_can_update_rooms()
    {
        $user = $this->createAuthUser();
        $room = Room::factory()->create();

        $this->actingAs($user);

        $response = $this->put('/api/v1/rooms/1', [
            'name' => 'Room name',
            'description' => 'Room description',
            'image' => 'image.jpg',
            'is_available' => 'available',
            ]);

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Room name',
                    'description' => 'Room description',
                    'image' => 'image.jpg',
                    'is_available' => 'available',
                ]);
    }

    public function test_users_can_delete_rooms()
    {
        $user = $this->createAuthUser();
        $room = $this->createRoom();

        $response = $this->actingAs($user)->delete('/api/v1/rooms/1');

        $response->assertStatus(204);
    }
}