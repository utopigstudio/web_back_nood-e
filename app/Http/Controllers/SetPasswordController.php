<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetPasswordRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class SetPasswordController extends Controller
{

    public function setPassword(SetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = bcrypt($request->password->validated());
        $this->respondWithToken(JWTAuth::fromUser($user));
        return response()->json([
            'status' => 'Password set successfully'
        ], 200);
    }
}
