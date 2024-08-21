<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscussionRequest;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index()
    {
        $discussions = Discussion::all();
        return response()->json($discussions, 200);
    }

    public function store(DiscussionRequest $request, Discussion $discussion)
    {
        $discussion = Discussion::create($request->validated()); 
        return response()->json($discussion, 201);
    }

    public function show(Discussion $discussion)
    {
        $discussion = Discussion::with('topics')->findOrFail($discussion->id);

        return response()->json($discussion, 200);
    }

    public function update(Request $request, Discussion $discussion)
    {
        $discussion->update($request->toArray());
        return response()->json($discussion, 200);
    }

    public function destroy(Discussion $discussion)
    {
        if (!$discussion) {
            return response()->json(['error' => 'Discussion not found'], 404);
        }

        $discussion->delete();
        return response()->json('Discussion deleted successfully', 204);
    }
}
