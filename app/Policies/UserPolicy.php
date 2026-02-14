<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function update(User $user, User $model)
    {
        return $user->role === 'super_admin';
    }

    public function delete(User $user, User $model)
    {
        return $user->role === 'super_admin';
    }
}

