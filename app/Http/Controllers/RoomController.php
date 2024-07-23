<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;
use App\Models\Room;


class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return response()->json($rooms, 200);
    }

    public function store(RoomRequest $request)
    {
        $room = Room::create($request->validated());
        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::find($id);
        return response()->json($room, 200);
    }

    public function update(RoomRequest $request, $id)
    {
        $room = Room::find($id);
        $room->update($request->validated());
        return response()->json($room, 200);
    }

    public function destroy($id)
    {
        Room::destroy($id);
        return response()->json(null, 204);
    }
}
