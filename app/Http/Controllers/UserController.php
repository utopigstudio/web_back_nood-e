<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\EntityInviteNotification;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    public function index()
    {
        $entities = User::where('role_id', 2)->get();
        return response()->json($entities);
    }
    
    public function store(UserRequest $request)
    {
        $user = User::create($request->validated() + ['role_id' => 2], ['password' => bcrypt('password')]);

        $url = URL::signedRoute('invitation', $user);

        $user->notify(new EntityInviteNotification($url));
        return response()->json(['message' => 'Invitation sent successfully'], 201);
    }

    public function show(User $entity)
    {
        return response()->json($entity);
    }

    public function update(UserRequest $request, User $entity)
    {
        $entity->update($request->validated());
        return response()->json(['message' => 'Changes saved successfully'], 200);
    }

    public function destroy(User $entity)
    {
        $entity->delete();
        return response()->json(['message' => 'entity deleted successfully'], 204);
    }

    public function invitation(User $user)
    {
        if (!request()->hasValidSignature() || $user->passord != 'password') {
            abort(401, 'Unauthorized');
        }

        auth()->login($user);
        return json_encode($user);
    }
}
