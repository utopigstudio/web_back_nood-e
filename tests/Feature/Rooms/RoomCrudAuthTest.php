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
        $user = $this->createAuthUser();
        $this->actingAs($user);

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
        $user = $this->createAuthUser();
        $this->actingAs($user);

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
        $user = $this->createAuthUser();
        $this->actingAs($user);

        $this->createRoom();

        $response = $this->actingAs($user)->delete('/api/v1/rooms/1');

        $response->assertStatus(204);
    }
}