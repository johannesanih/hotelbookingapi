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
        $this->authorize('create', RoomType::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hotel_id' => 'required|exists:hotels,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $hotel = Hotel::where('id', $validated['hotel_id'])
            ->where('user_id', $request->user()->id)
            ->where('status', 'approved')
            ->firstOrFail();

        if(! $hotel) {
            return response()->json([
                'message' => 'Hotel not found or not approved',
            ], 404);
        }

        $roomType = RoomType::create([
            'name' => $validated['name'],
            'hotel_id' => $validated['hotel_id'],
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            'message' => 'Room type created successfully',
            'room_type' => $roomType,
        ], 201);
    }
}
