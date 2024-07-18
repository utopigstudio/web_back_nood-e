<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function store(Request $request)
    {
        $user = User::create($request->all());
        return response()->json($user, 201);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json($user, 200);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->update($request->all());
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json('User deleted successfully', 204);
    }
}
