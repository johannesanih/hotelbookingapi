<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Hotel;
use App\Models\Price;
use App\Models\BookingRoom;
use App\Models\ServiceRoom;

class RoomType extends Model
{
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function serviceRooms()
    {
        return $this->hasMany(ServiceRoom::class);
    }
}
