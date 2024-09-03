<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Models\Room;


class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('events')->get();
        return response()->json($rooms, 200);
    }

    public function store(RoomRequest $request)
    {
        $data = $request->validated();
        $room = Room::create($data);
        return response()->json($room, 201);
    }

    public function show(Room $room)
    {
        $room->load('events');
        return response()->json($room, 200);
    }

    public function update(RoomRequest $request, Room $room)
    {
        $data = $request->validated();
        $room->update($data);
        return response()->json($room, 200);
    }

    public function destroy(Room $room)
    {
        // TODO: validate if the user can delete the room
        // TODO: validate if the room can be deleted

        // TODO: move to observer (deleted event)
        if ($room->image) {
            $room->deleteImage($room->image, 'local');
        }

        $room->delete();
        return response()->json(['message' => 'Room deleted successfully'], 200);
    }
}
