<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Hotel;
use App\Models\ServiceRoom;

class Service extends Model
{
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function serviceRooms()
    {
        return $this->hasMany(ServiceRoom::class);
    }
}
