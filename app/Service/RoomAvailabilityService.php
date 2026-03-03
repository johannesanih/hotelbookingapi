<?php

namespace App\Service;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\BookingRoom;

class RoomAvailabilityService
{
    public function checkAvailability($hotelId, $roomTypeId, $checkInDate, $checkOutDate, $requestedQuantity)
    {
        // Validate hotel and room type existence
        $hotel = Hotel::findOrFail($hotelId);

        // Validate room type belongs to the hotel
        $roomType = RoomType::where('hotel_id', $hotelId)->findOrFail($roomTypeId);

        // Get total rooms of this type in the hotel
        $totalRooms = $roomType->quantity;

        // Step 1: Check for completely vacant room type (no bookings at all)
        $totalBookedRooms = BookingRoom::where('hotel_id', $hotelId)
            ->where('room_type_id', $roomTypeId)
            ->sum('quantity');

        $vacantRooms = $totalRooms - $totalBookedRooms;

        if ($vacantRooms > 0) {
            return response()->json([
                'message'           => "There are {$vacantRooms} rooms available of this type. You requested {$requestedQuantity}.",
                'available'         => $vacantRooms >= $requestedQuantity,
                'available_rooms'   => $vacantRooms,
                'requested_rooms'   => $requestedQuantity,
            ], 201);
        }

        // Step 2: All rooms have bookings — check how many overlap with requested dates
        // A booking OVERLAPS if:  booked_check_in < requested_check_out
        //                     AND booked_check_out > requested_check_in
        $overlappingRooms = BookingRoom::where('hotel_id', $hotelId)
            ->where('room_type_id', $roomTypeId)
            ->whereHas('booking', function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('check_in_date', '<', $checkOutDate)
                      ->where('check_out_date', '>', $checkInDate);
            })
            ->sum('quantity');

        $availableRooms = $totalRooms - $overlappingRooms;

        if ($availableRooms > 0) {
            return response()->json([
                'message'           => "There are {$availableRooms} rooms available of this type for the selected dates. You requested {$requestedQuantity}.",
                'available'         => $availableRooms >= $requestedQuantity,
                'available_rooms'   => $availableRooms,
                'requested_rooms'   => $requestedQuantity,
            ], 201);
        }

        // Step 3: No rooms available at all
        return response()->json([
            'message'           => 'No rooms available for the selected dates.',
            'available'         => false,
            'available_rooms'   => 0,
            'requested_rooms'   => $requestedQuantity,
        ], 201);
    }

}
