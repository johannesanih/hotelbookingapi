<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Hotel;

class SuperAdminHotelController extends Controller
{
    public function approve(Request $request, $id)
    {
        $this->authorize('approve', Hotel::class);

        $hotel = Hotel::findOrFail($id);
        $hotel->status = 'approved';
        $hotel->save();
        return response()->json(['message' => 'Hotel approved successfully']);
    }

    public function reject(Request $request, $id)
    {
        $this->authorize('reject', Hotel::class);

        $hotel = Hotel::findOrFail($id);
        $hotel->status = 'rejected';
        $hotel->save();
        return response()->json(['message' => 'Hotel rejected successfully']);
    }
}
