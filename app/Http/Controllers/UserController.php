<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller 
{
    public function index()
    {
        $users = User::with('organization', 'roles')->orderBy('name')->get()->values();
        return response()->json($users);
    }
    
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);

        $user->sendInviteNotification();

        return response()->json([
            'user' => $user,
            'message' => 'Invitation sent successfully'
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);
        return response()->json($user, 200);
    }

    public function destroy(User $user)
    {
        // TODO: users are soft deleted

        return response()->json(['message' => 'Method not implemented'], 501);
    }
}
