<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HotelPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['hotel_admin', 'super_admin']);
    }

    public function view(User $user, Hotel $hotel)
    {
        if ($user->role === 'super_admin') return true;

        return $user->role === 'hotel_admin'
            && $hotel->user_id === $user->id;
    }

    public function create(User $user)
    {
        return $user->role === 'hotel_admin';
    }

    public function update(User $user, Hotel $hotel)
    {
        return $user->role === 'hotel_admin'
            && $hotel->user_id === $user->id;
    }

    public function delete(User $user, Hotel $hotel)
    {
        return $user->role === 'super_admin';
    }

    public function approve(User $user)
    {
        return $user->role === 'super_admin';
    }

    public function reject(User $user)
    {
        return $user->role === 'super_admin';
    }
}

