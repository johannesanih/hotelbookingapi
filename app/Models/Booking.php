<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Hotel;
use App\Models\BookingRoom;
use App\Models\Payment;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'hotel_id',
        'checkin',
        'checkout',
        'amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
