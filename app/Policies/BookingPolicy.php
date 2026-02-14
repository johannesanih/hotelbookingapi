<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    public function view(User $user, Booking $booking)
    {
        if ($user->role === 'super_admin') return true;

        if ($user->role === 'hotel_admin') {
            return $booking->hotel->user_id === $user->id;
        }

        return $booking->user_id === $user->id; // customer
    }

    public function updateStatus(User $user, Booking $booking)
    {
        return $user->role === 'hotel_admin'
            && $booking->hotel->user_id === $user->id;
    }
}

