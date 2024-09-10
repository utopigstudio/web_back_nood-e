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

        $members = $this->getMembersFromData($data);

        $discussion = Discussion::create($data);

        $discussion = $this->attachMembers($discussion, $members);

        return response()->json($discussion, 201);
    }

    public function show(Discussion $discussion)
    {
        $discussion->load('topics', 'members');

        return response()->json($discussion, 200);
    }

    public function update(DiscussionRequest $request, Discussion $discussion)
    {
        $data = $request->validated();

        $members = $this->getMembersFromData($data);

        $discussion->update($data);

        $discussion = $this->attachMembers($discussion, $members);

        return response()->json($discussion, 200);
    }

    public function destroy(Discussion $discussion)
    {
        $discussion->delete();
        return response()->json(['message' => 'Discussion deleted successfully'], 200);
    }

    private function getMembersFromData(array &$data): array
    {
        $members = $data['members'] ?? [];
        unset($data['members']);

        return $members;
    }

    private function attachMembers(Discussion $discussion, array $members): Discussion
    {
        if (!$members) {
            return $discussion;
        }

        $discussion->members()->attach($members);
        $discussion->load('members');

        return $discussion;
    }
}
