<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Hotel;
use App\Models\RoomType;


class Price extends Model
{
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
