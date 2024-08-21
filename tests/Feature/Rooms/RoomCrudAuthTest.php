<?php

namespace Tests\Feature\Rooms;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoomCrudAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthUser (): array
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    private function createRoom (): Room
    {
        return Room::create([
            'name' => 'Room name',
            'description' => 'Room description',
            'image' => 'image.jpg',
            'is_available' => true,
        ]);

    }

    public function test_users_can_create_rooms()
    {
        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $data = [
            'name' => 'Room name',
            'description' => 'Room description',
            'image' => 'image.jpg',
            'is_available' => true,
        ];
        
        $response = $this->post('/api/v1/rooms', $data);
 

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
        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->createRoom();

        $response = $this->put('/api/v1/rooms/1', [
            'name' => 'Update room name',
            'description' => 'Update room description',
            'image' => 'image.jpg',
            'is_available' => true,
            ]);

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Update room name',
                    'description' => 'Update room description',
                    'image' => 'image.jpg',
                    'is_available' => true,
                ]);
    }

    public function test_users_can_delete_rooms()
    {
        $authData = $this->createAuthUser();
        $user = $authData['user'];
        $token = $user['token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ]);

        $this->createRoom();

        $response = $this->actingAs($user)->delete('/api/v1/rooms/1');

        $response->assertStatus(204);
    }
}