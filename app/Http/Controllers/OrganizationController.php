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
        $organizations = Organization::all();
        return response()->json($organizations, 200);
    }

    public function store(OrganizationRequest $request)
    {
        $organization = Organization::create($request->validated());
        return response()->json($organization, 201);
    }

    public function show(string $id)
    {
        $organization = Organization::find($id);
        return response()->json($organization, 200);
    }

    public function update(OrganizationRequest $request, string $id)
    {
        $organization = Organization::find($id);
        $organization->update($request->validated());
    }

    public function destroy(string $id)
    {
        $organization = Organization::find($id);
        $organization->delete();
        return response()->json('Organization deleted successfully', 204);
    }
}
