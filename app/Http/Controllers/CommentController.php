<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all();
        return response()->json($comments, 200);
    }

    public function store(CommentRequest $request)
    {
        $comment = Comment::create($request->all());
        return response()->json($comment, 201);
    }

    public function show(string $id)
    {
        $comment = Comment::find($id);
        return response()->json($comment, 200);
    }

    public function update(CommentRequest $request, string $id)
    {
        $comment = Comment::find($id);
        $comment->update($request->all());
        return response()->json($comment, 200);
    }

    public function destroy(string $id)
    {
        $comment = Comment::find($id);
        $comment->delete();
        return response()->json('Comment deleted successfully', 204);
    }
}
