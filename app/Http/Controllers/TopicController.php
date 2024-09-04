<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\Discussion;
use App\Models\Topic;

class TopicController extends Controller
{
    public function store(TopicRequest $request, Discussion $discussion, Topic $topic)
    {
        $data = $request->validated();
        $data['discussion_id'] = $discussion->id;

        $topic = Topic::create($data);

        return response()->json($topic, 201);
    }

    public function show(Discussion $discussion, Topic $topic)
    {
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic not found in this discussion'], 404);
        }

        $topic->load(['author', 'comments.author']);

        return response()->json($topic, 200);
    }

    public function update(TopicRequest $request, Discussion $discussion, Topic $topic)
    {
        $data = $request->validated();
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic not found in this discussion'], 404);
        }
        $topic->update($data);
        return response()->json($topic, 200);
    }

    public function destroy(Discussion $discussion, Topic $topic)
    {
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic not found in this discussion'], 404);
        }

        $topic->delete();
        return response()->json(['message' => 'Topic deleted successfully'], 200);
    }
}
