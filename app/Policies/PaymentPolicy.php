<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * super_admin sees all, hotel_admin sees payments for their hotel,
     * customer sees only their own payments.
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->role === 'super_admin') return true;

        if ($user->role === 'hotel_admin') {
            return $payment->hotel->user_id === $user->id;
        }

        return $payment->user_id === $user->id; // customer
    }

    /**
     * Only customers can initialize a payment (it's tied to their booking).
     */
    public function initialize(User $user, Payment $payment): bool
    {
        return $user->role === 'customer'
            && $payment->user_id === $user->id;
    }
}
