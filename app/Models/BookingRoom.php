<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\RoomType;

class BookingRoom extends Model
{
    protected $fillable = [
        'booking_id',
        'hotel_id',
        'room_type_id',
        'quantity',
        'amount',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
