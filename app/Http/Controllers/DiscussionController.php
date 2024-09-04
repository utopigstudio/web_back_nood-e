<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscussionRequest;
use App\Models\Discussion;

class DiscussionController extends Controller
{
    public function index()
    {
        $discussions = Discussion::all();
        return response()->json($discussions, 200);
    }

    public function store(DiscussionRequest $request, Discussion $discussion)
    {
        $data = $request->validated();
        $discussion = Discussion::create($data); 
        return response()->json($discussion, 201);
    }

    public function show(Discussion $discussion)
    {
        $discussion->load('topics');

        return response()->json($discussion, 200);
    }

    public function update(DiscussionRequest $request, Discussion $discussion)
    {
        $data = $request->validated();
        $discussion->update($data);
        return response()->json($discussion, 200);
    }

    public function destroy(Discussion $discussion)
    {
        $discussion->delete();
        return response()->json(['message' => 'Discussion deleted successfully'], 200);
    }
}
