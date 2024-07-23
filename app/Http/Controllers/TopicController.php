<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::all();
        return response()->json($topics, 200);
    }

    public function store(Request $request)
    {
        $topic = Topic::create($request->validated());
        return response()->json($topic, 201);
    }

    public function show($id)
    {
        $topic = Topic::find($id);
        return response()->json($topic, 200);
    }

    public function update(Request $request, $id)
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
