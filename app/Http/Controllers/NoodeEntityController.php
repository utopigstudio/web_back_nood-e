<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use App\Models\User;
use App\Notifications\EntityInviteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class NoodeEntityController extends Controller
{
    public function index()
    {
        $entities = User::where('role_id', 2)->get();
        return response()->json($entities);
    }
    
    public function store(StoreEntityRequest $request)
    {
        $user = User::create($request->validated() + ['role_id' => 2], ['password' => bcrypt('password')]);

        $url = URL::signedRoute('invitation', $user);

        $user->notify(new EntityInviteNotification($url));
        return response()->json(['message' => 'Entity invited successfully'], 201);
    }

    public function show(User $entity)
    {
        return response()->json($entity);
    }

    public function update(UpdateEntityRequest $request, User $entity)
    {
        $entity->update($request->validated());
        return response()->json(['message' => 'entity updated successfully'], 200);
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
