<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscussionRequest;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;

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

    public function show(Discussion $discussion, Topic $topic)
    {
        $discussion = Discussion::with('topics')->findOrFail($discussion->id);

        $topics = Topic::where('discussion_id', $discussion->id)->get();
        return response()->json(['discussion' => $discussion, 'topics' => $topics], 200);
    }

    public function update(DiscussionRequest $request, Discussion $discussion)
    {
        $discussion = Discussion::find($discussion->id);
        $discussion->update($request->validated());
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
