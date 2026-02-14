<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\PlanPrice;

class PercentagePerBooking extends Model
{
    public function planPrice()
    {
        return $this->belongsTo(PlanPrice::class);
    }
}
