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

    public function store(DiscussionRequest $request)
    {
        $discussion = Discussion::create($request->validated());
        return response()->json($discussion, 201);
    }

    public function show($id)
    {
        $discussion = Discussion::with('topics')->findOrFail($id);
        return response()->json($discussion, 200);
    }

    public function update(DiscussionRequest $request, $id)
    {
        $discussion = Discussion::find($id);
        $discussion->update($request->validated());
        return response()->json($discussion, 200);
    }

    public function destroy($id)
    {
        $discussion = Discussion::find($id);
        $discussion->delete();
        return response()->json('Discussion deleted successfully', 204);
    }
}
