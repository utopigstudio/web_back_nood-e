<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function setPassword(SetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->save();
        
        $token = auth()->fromUser($user); 
        return $this->respondWithToken($token);

    }   

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->invalidate(auth()->getToken());
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}