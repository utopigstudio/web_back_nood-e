<?php

namespace App\Http\Controllers;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\UserInviteNotification;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller 
{
    public function index()
    {
        $users = User::where('role_id', 2)->get();
        return response()->json($users);
    }
    
    public function store(UserRequest $request)
    {
        $user = User::create($request->validated() + ['role_id' => 0], ['password' =>'password']);

        $url = URL::signedRoute('invitation', $user);

        $user->notify(new UserInviteNotification($url));
        return response()->json(['message' => 'Invitation sent successfully'], 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validated());
        return response()->json(['message' => 'Changes saved successfully'], 200);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'entity deleted successfully'], 204);
    }

    public function invitation(User $user)
    {
        if (!request()->hasValidSignature() || $user->passord != 'password') {
            abort(401, 'Unauthorized');
        }

        $token = JWTAuth::fromUser($user); 
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 9847547847943
        ]);
    }

    
}
