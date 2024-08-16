<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;

class TopicController extends Controller
{
    public function index(Discussion $discussion)
    {
        $topics = Topic::where('discussion_id', $discussion->id)->get();

        return response()->json($topics, 200);
    }

    public function store(TopicRequest $request, Discussion $discussion, Topic $topic)
    {
        $validateData = $request->validated();
        $validateData['discussion_id'] = $discussion->id;

        $topic = Topic::create($validateData);

        return response()->json($topic, 201);
    }

    public function show(Discussion $discussion, Topic $topic, Comment $comment)
    {
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic not found in this discussion'], 404);
        }
        
        $comments = Comment::where('topic_id', $topic->id)->get();

        return response()->json(['topic' => $topic, 'comments' => $comments], 200);
    }

    public function update(TopicRequest $request, Discussion $discussion, Topic $topic)
    {
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic does not belong to this discussion'], 403);
        }
        $topic->update($request->validated());
        return response()->json($topic, 200);
    }

    public function destroy(Discussion $discussion, Topic $topic)
    {
        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic does not belong to this discussion'], 403);
        }
        return response()->json('Topic deleted successfully', 204);
    }
}
