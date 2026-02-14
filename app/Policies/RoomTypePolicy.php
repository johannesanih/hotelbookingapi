<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomTypePolicy
{
    public function create(User $user, Hotel $hotel)
    {
        return $user->role === 'hotel_admin'
            && $hotel->user_id === $user->id
            && $hotel->status === 'approved';
    }

    public function update(User $user, RoomType $roomType)
    {
        return $user->role === 'hotel_admin'
            && $roomType->hotel->user_id === $user->id;
    }

    public function delete(User $user, RoomType $roomType)
    {
        return $this->update($user, $roomType);
    }
}

