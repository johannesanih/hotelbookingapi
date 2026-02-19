<?php

namespace App\Http\Controllers\Api\HotelAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Hotel;


class HotelAdminHotelController extends Controller
{
    public function create(Request $request)
    {
        $this->authorize('create', Hotel::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
        ]);

        $hotel = Hotel::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'],
            'status' => 'pending',
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Hotel created successfully',
            'hotel' => $hotel,
        ], 201);
    }
}
