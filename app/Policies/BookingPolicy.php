<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Only customers can create bookings.
     */
    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }

    /**
     * super_admin sees all, hotel_admin sees their hotel's bookings, customer sees their own.
     */
    public function view(User $user, Booking $booking): bool
    {
        if ($user->role === 'super_admin') return true;

        if ($user->role === 'hotel_admin') {
            return $booking->hotel->user_id === $user->id;
        }

        return $booking->user_id === $user->id; // customer
    }

    /**
     * Only hotel_admin of the booking's hotel can update booking status.
     */
    public function updateStatus(User $user, Booking $booking): bool
    {
        return $user->role === 'hotel_admin'
            && $booking->hotel->user_id === $user->id;
    }

    /**
     * super_admin can cancel anything, customer can only cancel their own booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->role === 'super_admin') return true;

        return $user->role === 'customer'
            && $booking->user_id === $user->id;
    }
}
