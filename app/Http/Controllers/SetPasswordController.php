<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;

class SetPasswordController extends Controller
{
    public function setPassword(SetPasswordRequest $request)
    {
        auth()->user()->update([
            'password' => bcrypt($request->password)
        ]);
        return response()->json([
            'status' => 'Password set successfully'
        ], 200);
    }
}
