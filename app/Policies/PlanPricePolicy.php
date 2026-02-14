<?php

namespace App\Policies;

use App\Models\PlanPrice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanPricePolicy
{
    public function before(User $user)
    {
        if ($user->role === 'super_admin') return true;
    }

    public function viewAny(User $user) { return false; }
    public function view(User $user) { return false; }
    public function create(User $user) { return false; }
    public function update(User $user) { return false; }
    public function delete(User $user) { return false; }
}

