<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;
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

        if ($request->hasFile('image')) {
            $data['image'] = Room::store64Image($request->input('image'), 'rooms/images');
        }

        $room = Room::create($data);
        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::find($id)->with('events')->first();
        return response()->json($room, 200);
    }

    public function update(Request $request, $id)
    {
        $room = Room::find($id);

        if ($request->hasFile('image')) {
           if ($room->image) {
               $room->deleteImage($room->image, 'local');
           }

           $data['image'] = Room::store64Image($request->input('image'), 'rooms/images');
        }

        $room->update($request->toArray());
        return response()->json($room, 200);
    }

    public function destroy(Room $room)
    {
        Room::destroy($room);

        if ($room->image) {
            $room->deleteImage($room->image, 'local');
        }

        $room->delete();
        return response()->json('Room deleted successfully', 204);
    }
}
