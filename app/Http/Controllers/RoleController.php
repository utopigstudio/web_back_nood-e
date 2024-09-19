<?php

namespace App\Http\Controllers;

use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        return response()->json($roles, 200);
    }

    public function show($id)
    {
        $role = Role::find($id);

        return response()->json($role, 200);
    }
}
