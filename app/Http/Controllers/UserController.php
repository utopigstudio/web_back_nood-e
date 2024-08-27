<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\UserInviteNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class UserController extends Controller 
{
    public function index()
    {
        $users = User::all()->sortBy('name');
        return response()->json($users);
    }
    
    public function store(UserRequest $request)
    {
        $user = User::create($request->validated());

        $url = URL::signedRoute('invitation', $user);

        $user->notify(new UserInviteNotification($url));
        return response()->json(['message' => 'Invitation sent successfully'], 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        if ($request->hasFile('image')) {
           if ($user->image) {
               $user->deleteImage($user->image, 'local');
           }

           $data['image'] = User::store64Image($request->input('image'), 'users/images');
        }

        $user->update($request->toArray());
        return response()->json(['message' => 'Changes saved successfully'], 200);
    }

    public function destroy(User $user)
    {
        if ($user->image) {
            $user->deleteImage($user->image, 'local');
        }

        $user->delete();
        return response()->json(['message' => 'entity deleted successfully'], 204);
    }
   
}
