<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\RoomType;
use App\Models\Price;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceRoom;
use App\Models\Rating;

class Hotel extends Model
{
    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function serviceRooms()
    {
        return $this->hasMany(ServiceRoom::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
