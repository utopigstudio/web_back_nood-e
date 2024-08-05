<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;

class SetPasswordController extends Controller
{

    public function setPassword(SetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            'status' => 'Password set successfully'
        ], 200);
    }
    public function setPassword(SetPasswordRequest $request)
    {
        auth('api')->user()->update([
            'password' => bcrypt($request->password)
        ]);
        return response()->json([
            'status' => 'Password set successfully'
        ], 200);
    }
}
