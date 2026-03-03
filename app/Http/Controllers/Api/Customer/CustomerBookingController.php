<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Payment;
use App\Models\Price;
use App\Models\RoomType;
use App\Service\RoomAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerBookingController extends Controller
{
    public function __construct(protected RoomAvailabilityService $availabilityService) {}

    /**
     * Get all bookings for the authenticated customer.
     */
    public function index(Request $request)
    {
        $bookings = Booking::with('bookingRooms.roomType')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'bookings' => $bookings,
        ], 200);
    }

    /**
     * Get a single booking for the authenticated customer.
     */
    public function show($bookingId)
    {
        $booking = Booking::with('bookingRooms.roomType')->findOrFail($bookingId);

        $this->authorize('view', $booking);

        return response()->json([
            'booking' => $booking,
        ], 200);
    }

    /**
     * Create a new booking with multiple room types for the authenticated customer.
     *
     * Request body example:
     * {
     *   "hotel_id": 1,
     *   "check_in_date": "2025-06-01",
     *   "check_out_date": "2025-06-05",
     *   "rooms": [
     *     { "room_type_id": 1, "quantity": 2 },
     *     { "room_type_id": 3, "quantity": 1 }
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', Booking::class);

        $request->validate([
            'hotel_id'               => 'required|exists:hotels,id',
            'check_in_date'          => 'required|date|after_or_equal:today',
            'check_out_date'         => 'required|date|after:check_in_date',
            'rooms'                  => 'required|array|min:1',
            'rooms.*.room_type_id'   => 'required|exists:room_types,id',
            'rooms.*.quantity'       => 'required|integer|min:1',
        ]);

        $nights = (int) now()->parse($request->check_in_date)
            ->diffInDays($request->check_out_date);

        // Step 1: Check availability for all room types and calculate amounts
        $checkedRooms = [];

        foreach ($request->rooms as $index => $room) {
            $availability = $this->availabilityService->checkAvailability(
                $request->hotel_id,
                $room['room_type_id'],
                $request->check_in_date,
                $request->check_out_date,
                $room['quantity'],
            );

            $availabilityData = $availability->getData(assoc: true);

            if (!$availabilityData['available']) {
                return response()->json([
                    'message'         => "Room type ID {$room['room_type_id']}: {$availabilityData['message']}",
                    'available_rooms' => $availabilityData['available_rooms'],
                    'room_type_id'    => $room['room_type_id'],
                ], 422);
            }

            // Fetch the price active on check-in date for this room type
            $price = Price::where('room_type_id', $room['room_type_id'])
                ->where('hotel_id', $request->hotel_id)
                ->activeOn($request->check_in_date)
                ->first();

            if (!$price) {
                return response()->json([
                    'message'      => "Room type ID {$room['room_type_id']} has no price set for the selected dates. Please contact the hotel.",
                    'room_type_id' => $room['room_type_id'],
                ], 422);
            }

            // total_amount accessor applies VAT on top of base amount
            // then multiply by quantity and number of nights
            $roomAmount = $price->total_amount * $room['quantity'] * $nights;

            $checkedRooms[] = [
                'room_type_id' => $room['room_type_id'],
                'quantity'     => $room['quantity'],
                'amount'       => $roomAmount,
            ];
        }

        // Step 2: Calculate total booking amount
        $totalAmount = collect($checkedRooms)->sum('amount');

        // Step 3: Create booking, booking rooms, and payment in a single transaction
        $booking = DB::transaction(function () use ($request, $checkedRooms, $totalAmount) {
            $booking = Booking::create([
                'user_id'        => $request->user()->id,
                'hotel_id'       => $request->hotel_id,
                'checkin'        => $request->check_in_date,
                'checkout'       => $request->check_out_date,
                'status'         => 'pending',
                'amount'         => $totalAmount,
            ]);

            foreach ($checkedRooms as $room) {
                BookingRoom::create([
                    'booking_id'   => $booking->id,
                    'hotel_id'     => $request->hotel_id,
                    'room_type_id' => $room['room_type_id'],
                    'quantity'     => $room['quantity'],
                    'amount'       => $room['amount'],
                ]);
            }

            Payment::create([
                'user_id'    => $request->user()->id,
                'hotel_id'   => $request->hotel_id,
                'booking_id' => $booking->id,
                'amount'     => $totalAmount,
                'reference'  => 'PAY-' . strtoupper(Str::random(12)),
                'status'     => 'pending',
            ]);

            return $booking;
        });

        return response()->json([
            'message' => 'Booking created successfully.',
            'booking' => $booking->load('bookingRooms.roomType', 'payment'),
        ], 201);
    }

    /**
     * Cancel a booking for the authenticated customer.
     */
    public function cancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $this->authorize('cancel', $booking);

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'This booking has already been cancelled.',
            ], 422);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);
            $booking->payment()->update(['status' => 'cancelled']);
        });

        return response()->json([
            'message' => 'Booking cancelled successfully.',
            'booking' => $booking->fresh('payment'),
        ], 200);
    }
}
