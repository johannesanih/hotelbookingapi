<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\PercentagePerBooking;

class PlanPrice extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function percentages()
    {
        return $this->hasMany(PercentagePerBooking::class);
    }
}
