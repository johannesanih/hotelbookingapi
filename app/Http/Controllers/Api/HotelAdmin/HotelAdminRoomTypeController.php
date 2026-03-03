<?php

namespace App\Http\Controllers\Api\HotelAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RoomType;
use App\Models\Hotel;

class HotelAdminRoomTypeController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hotel_id' => 'required|exists:hotels,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $hotel = Hotel::findOrFail($validated['hotel_id']);

        // 🔥 Authorize using the hotel model
        $this->authorize('create', $hotel);

        $roomType = RoomType::create([
            'name' => $validated['name'],
            'hotel_id' => $hotel->id,
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            'message' => 'Room type created successfully',
            'room_type' => $roomType,
        ], 201);
    }
}
