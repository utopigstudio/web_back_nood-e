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
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = Organization::store64Image($request->input('image'), 'organizations/images');
        }

        $organization = Organization::create($data);
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

        $data = $request->validated();

        if ($request->hasFile('image')) {
           if ($organization->image) {
               $organization->deleteImage($organization->image, 'local');
           }

           $data['image'] = Organization::store64Image($request->input('image'), 'organizations/images');
        }

        $organization->update($request->validated());
        return response()->json($organization, 200);
    }

    public function destroy(string $id)
    {
        $organization = Organization::find($id);

        if ($organization->image) {
            $organization->deleteImage($organization->image, 'local');
        }
        
        $organization->delete();
        return response()->json('Organization deleted successfully', 204);
    }
}
