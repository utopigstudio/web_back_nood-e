<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return response()->json($events, 200);
    }

    public function store(EventRequest $request): JsonResponse
    {
        $data = $request->validated();
        $event = Event::create($data);
        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        return response()->json($event);
    }

    public function update(EventRequest $request, Event $event)
    {
        $data = $request->validated();
        $event->update($data);
        return response()->json($event);
    }
    
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}
