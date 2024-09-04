<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return response()->json($events, 200);
    }

    public function store(EventRequest $request): JsonResponse
    {
        $event = Event::create($request->validated());
        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        return response()->json($event);
    }

    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        $event->update($request->toArray());
        return response()->json($event);
    }
    
    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}
