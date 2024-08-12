<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function store(TopicRequest $request)
    {
        $topic = Topic::create($request->validated());
        return response()->json($topic, 201);
    }

    public function show($id)
    {
        $topic = Topic::with('comments')->findOrFail($id);
        return response()->json($topic, 200);
    }

    public function update(TopicRequest $request, $id)
    {
        $topic = Topic::find($id);
        $topic->update($request->validated());
        return response()->json($topic, 200);
    }

    public function destroy($id)
    {
        Topic::destroy($id);
        return response()->json(null, 204);
    }
}
