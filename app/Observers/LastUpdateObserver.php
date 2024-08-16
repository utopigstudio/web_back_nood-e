<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\Topic;

class LastUpdateObserver
{
    public function created(Comment $comment): void
    {
        $topic = Topic::find($comment->topic_id);

        if ($topic) {
            $topic->update([
                'last_update' => now(),
            ]);
        }
    }

    public function updated(Comment $comment): void
    {
        //
    }


    public function deleted(Comment $comment): void
    {
        //
    }

    public function restored(Comment $comment): void
    {
        //
    }

    public function forceDeleted(Comment $comment): void
    {
        //
    }
}
