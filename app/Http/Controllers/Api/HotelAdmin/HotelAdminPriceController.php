<?php

namespace App\Http\Controllers\Api\HotelAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\Price;

class HotelAdminPriceController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'hotel_id'     => 'required|exists:hotels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'amount'       => 'required|numeric|min:0',
            'vat'          => 'sometimes|numeric|min:0|max:100',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
        ]);

        $hotel = Hotel::findOrFail($validated['hotel_id']);

        $roomType = RoomType::where('id', $validated['room_type_id'])
            ->where('hotel_id', $validated['hotel_id'])
            ->firstOrFail();

        $this->authorize('create', [Price::class, $hotel]);

        // Check for overlapping price ranges for the same room type
        $overlap = Price::where('room_type_id', $roomType->id)
            ->where('hotel_id', $validated['hotel_id'])
            ->where('start_date', '<=', $validated['end_date'])
            ->where('end_date', '>=', $validated['start_date'])
            ->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'A price already exists for this room type within the given date range.',
            ], 422);
        }

        $price = Price::create([
            'hotel_id'     => $validated['hotel_id'],
            'room_type_id' => $validated['room_type_id'],
            'amount'       => $validated['amount'],
            'vat'          => $validated['vat'] ?? 0,
            'start_date'   => $validated['start_date'],
            'end_date'     => $validated['end_date'],
        ]);

        return response()->json([
            'message' => 'Price created successfully.',
            'price'   => $price,
        ], 201);
    }
}
