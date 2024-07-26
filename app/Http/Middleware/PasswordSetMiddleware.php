<?php

namespace App\Http\Middleware;

use App\Http\Requests\SetPasswordRequest;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordSetMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->password == 'password' && !$request->password && !$request->is('api/v1/users/setPassword/{user}')) {
            return response()->json(['message' => 'Password is required'], 400);
        } else {
            return $this->setPassword($request, auth()->user());
        }
        return $next($request);
    }

    public function setPassword(SetPasswordRequest $request, User $user): Response
    {
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message' => 'Password set successfully'], 200);
    }
}
