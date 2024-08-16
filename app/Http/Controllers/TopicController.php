<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function store(TopicRequest $request)
    {
        $topic = Topic::create($request->validated());
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

    public function destroy($id)
    {
        Topic::destroy($id);
        return response()->json(null, 204);
    }
}
