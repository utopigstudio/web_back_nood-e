<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends Policy
{
    public function massInvite(User $authUser)
    {
        return $authUser->role->name === 'admin';
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user)
    {
        return $authUser->role->name === 'admin';
    }

}
