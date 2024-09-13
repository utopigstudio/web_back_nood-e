<?php

namespace App\Policies;

use App\Models\Discussion;
use App\Models\User;
use Illuminate\Auth\Access\Response;
class DiscussionPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, Discussion $discussion)
    {
        if (
                $discussion->is_public ||
                $discussion->author_id === $user->id ||
                $discussion->members->contains($user)
        ) {
            return Response::allow();
        } else {
            return Response::denyAsNotFound();
        }
    }

    public function update(User $user, Discussion $discussion)
    {
        return $discussion->author_id === $user->id;
    }

    public function delete(User $user, Discussion $discussion)
    {
        return $discussion->author_id === $user->id;
    }
}
