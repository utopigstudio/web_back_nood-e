<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Organizations = Organization::all();
        return response()->json($Organizations, 200);
    }

    public function store(OrganizationRequest $request)
    {
        $Organization = Organization::create($request->all());
        return response()->json($Organization, 201);
    }

    public function show(string $id)
    {
        $Organization = Organization::find($id);
        return response()->json($Organization, 200);
    }

    public function update(OrganizationRequest $request, string $id)
    {
        $Organization = Organization::find($id);
        $Organization->update($request->all());
    }

    public function destroy(string $id)
    {
        $Organization = Organization::find($id);
        $Organization->delete();
        return response()->json('Organization deleted successfully', 204);
    }
}
