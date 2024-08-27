<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Models\User;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    /** @var JWTGuard */
    private $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = $this->auth->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function setPassword(SetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->save();
        
        $token = $this->auth->fromUser($user); 
        return $this->respondWithToken($token);

    }   

    public function me()
    {
        return response()->json($this->auth->user());
    }

    public function logout()
    {
        $this->auth->invalidate($this->auth->getToken());
        $this->auth->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->auth->refresh());
    }

    public function acceptInvitation(User $user)
    {
        $user = User::findOrFail($user->id);

        if (!request()->hasValidSignature()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $token = $this->auth->fromUser($user);
        
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->auth->factory()->getTTL() * 60
        ]);
    }
}