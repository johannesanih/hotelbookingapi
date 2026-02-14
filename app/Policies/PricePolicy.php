<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\Price;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PricePolicy
{
    public function create(User $user, RoomType $roomType)
    {
        return $user->role === 'hotel_admin'
            && $roomType->hotel->user_id === $user->id;
    }

    public function update(User $user, Price $price)
    {
        return $user->role === 'hotel_admin'
            && $price->hotel->user_id === $user->id;
    }

    public function delete(User $user, Price $price)
    {
        return $this->update($user, $price);
    }
}

