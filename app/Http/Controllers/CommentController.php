<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Topic;

class CommentController extends Controller
{
    public function index(Discussion $discussion,Topic $topic)
    {
        $comments = Comment::where('topic_id', $topic->id)->get();

        return response()->json($comments, 200);
    }

    public function store(CommentRequest $request, Discussion $discussion, Topic $topic, Comment $comment)
    {
        $validateData = $request->validated();
        $validateData['topic_id'] = $topic->id;

        $comment = Comment::create($validateData);

        return response()->json($comment, 201);
    }

    public function show(Discussion $discussion, Topic $topic, Comment $comment)
    {
        $topic = Topic::find($topic->id)->with('comments')->get();

        if ($comment->topic_id !== $topic->id) {
            return response()->json(['error' => 'Comment not found in this topic'], 404);
        }
        return response()->json($comment, 200);
    }

    public function update(CommentRequest $request, Discussion $discussion, Topic $topic, Comment $comment)
    {
        if ($comment->topic_id !== $topic->id) {
            return response()->json(['error' => 'Comment does not belong to this topic'], 403);
        }
        $comment->update($request->validated());
        return response()->json($comment, 200);
    }

    public function destroy(Discussion $discussion, Topic $topic, Comment $comment)
    {
        if ($comment->topic_id !== $topic->id) {
            return response()->json(['error' => 'Comment does not belong to this topic'], 403);
        }

        if ($topic->discussion_id !== $discussion->id) {
            return response()->json(['error' => 'Topic does not belong to this discussion'], 403);
        }

        $comment->delete();
        return response()->json('Comment deleted successfully', 204);
    }
}
