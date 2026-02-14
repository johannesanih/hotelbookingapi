<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Hotel;
use App\Models\Service;
use App\Models\RoomType;

class ServiceRoom extends Model
{
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
