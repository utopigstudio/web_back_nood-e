<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\Discussion;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function store(TopicRequest $request, Discussion $discussion, Topic $topic)
    {
        $validateData = $request->validated();
        $validateData['discussion_id'] = $discussion->id;

        $topic = Topic::create($validateData);

        return response()->json($topic, 201);
    }

    public function show(Discussion $discussion, Topic $topic)
    {
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic not found in this discussion'], 404);
        }

        $topic->load('comments');

        return response()->json($topic, 200);
    }

    public function update(Request $request, Discussion $discussion, Topic $topic)
    {
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic does not belong to this discussion'], 403);
        }
        $topic->update($request->toArray());
        return response()->json($topic, 200);
    }

    public function destroy(Discussion $discussion, Topic $topic)
    {
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic does not belong to this discussion'], 403);
        }

        $topic->delete();
        return response()->json(['message' => 'Topic deleted successfully'], 200);
    }
}
