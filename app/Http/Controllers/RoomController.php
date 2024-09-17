<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailableRoomRequest;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $dateStart = $request->get('start') ? new Carbon($request->get('start')) : now()->startOfMonth();
        $dateEnd = $request->get('end') ? new Carbon($request->get('end')) : now()->addMonth()->startOfMonth();

        //if dateEnd > dateStart + 1 month, set dateEnd to dateStart + 1 month
        if ($dateEnd->diffInMonths($dateStart, true) > 1) {
            $dateEnd = $dateStart->copy()->addMonth();
        }

        $rooms = Room::query();

        // by default, show only available rooms
        if (!isset($request->show_unavailable) && $request->show_unavailable == 0) {
            $rooms = $rooms->isAvailable();
        }

        $rooms = $rooms->with(['events' => function($query) use ($dateStart, $dateEnd) {
            $query->where('start', '>=', $dateStart)
                ->where('end', '<', $dateEnd);
        }])->get();

        return response()->json($rooms, 200);
    }

    public function showFree(AvailableRoomRequest $request)
    {
        $data = $request->validated();
        $dateStart = new Carbon($data['start']);
        $dateEnd = new Carbon($data['end']);

        $rooms = Room::isAvailable()->whereDoesntHave('events', function($query) use ($dateStart, $dateEnd) {
            $query->where('start', '<', $dateEnd)
                ->where('end', '>', $dateStart);
        })->get();

        return response()->json($rooms, 200);
    }

    public function store(RoomRequest $request)
    {
        Gate::authorize('create', Room::class);

        $data = $request->validated();
        $room = Room::create($data);
        return response()->json($room, 201);
    }

    public function show(Request $request, Room $room)
    {
        $dateStart = $request->get('start') ? new Carbon($request->get('start')) : now()->startOfMonth();
        $dateEnd = $request->get('end') ? new Carbon($request->get('end')) : now()->addMonth()->startOfMonth();

        //if dateEnd > dateStart + 1 month, set dateEnd to dateStart + 1 month
        if ($dateEnd->diffInMonths($dateStart, true) > 1) {
            $dateEnd = $dateStart->copy()->addMonth();
        }

        $room->load(['events' => function($query) use ($dateStart, $dateEnd) {
            $query->where('start', '>=', $dateStart)
                ->where('end', '<', $dateEnd);
        }]);

        return response()->json($room, 200);
    }

    public function update(RoomRequest $request, Room $room)
    {
        Gate::authorize('update', $room);

        $data = $request->validated();
        $room->update($data);
        return response()->json($room, 200);
    }

    public function destroy(Room $room)
    {
        Gate::authorize('delete', $room);
        // TODO: validate if the room can be deleted

        // TODO: move to observer (deleted event)
        if ($room->image) {
            $room->deleteImage($room->image, 'local');
        }

        $room->delete();
        return response()->json(['message' => 'Room deleted successfully'], 200);
    }
}
