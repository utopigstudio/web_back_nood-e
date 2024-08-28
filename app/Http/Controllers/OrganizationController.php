<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Request;

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

    public function store(OrganizationRequest $request, Organization $organization)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $organization->uploadImage($request->input('image'), 'organizations/images');
        }

        $organization = Organization::create($data);
        return response()->json($organization, 201);
    }

    public function show(string $id)
    {
        $organization = Organization::with('user')->where('id', $id)->first();
        return response()->json($organization, 200);
    }

    public function update(Request $request, string $id)
    {
        $organization = Organization::find($id);

        if ($request->hasFile('image')) {
            if ($organization->image) {
                $organization->deleteImage($organization->image);
            }

            $data['image'] = Organization::store64Image($request->input('image'), 'organizations/images');
        }

        $organization->update($request->all());
        return response()->json($organization, 200);
    }

    public function destroy(string $id)
    {
        $organization = Organization::find($id);

        if ($organization->image) {
            $organization->deleteImage($organization->image);
        }
        
        $organization->delete();
        return response()->json('Organization deleted successfully', 204);
    }
}
