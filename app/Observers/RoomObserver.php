<?php

namespace App\Observers;

use App\Models\Room;

class RoomObserver
{
    public function deleting(Room $room)
    {
        if ($room->events()->where('start', '>=', now())->exists()) {
            throw new \Exception('Cannot delete room with future events');
        }
    }
}
