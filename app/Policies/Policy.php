<?php

namespace App\Policies;

use App\Models\User;

abstract class Policy
{
    public function before(User $user)
    {
        if ($user->role->name === 'superadmin') {
            return true;
        }
    }
}
