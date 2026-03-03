<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Customer\CustomerAuthController;
use App\Http\Controllers\Api\Customer\CustomerHotelController;
use App\Http\Controllers\Api\Customer\CustomerBookingController;

use App\Http\Controllers\Api\HotelAdmin\HotelAdminAuthController;
use App\Http\Controllers\Api\HotelAdmin\HotelAdminHotelController;
use App\Http\Controllers\Api\HotelAdmin\HotelAdminRoomTypeController;
use App\Http\Controllers\Api\HotelAdmin\HotelAdminPriceController;

use App\Http\Controllers\Api\SuperAdmin\SuperAdminHotelController;

// API Routes
Route::prefix('v1')->group(function () {

    // Customer Authentication Routes
    Route::post('/customer/register', [CustomerAuthController::class, 'register']);
    Route::post('/customer/login', [CustomerAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/customer/me', [CustomerAuthController::class, 'me']);
    });

    // Public Routes for Customers
    Route::get('/hotels', [CustomerHotelController::class, 'index']);
    Route::get('/hotels/{id}', [CustomerHotelController::class, 'show']);
    // Route to check room availability for a specific hotel and room type
    Route::get('/hotels/{id}/roomtype/{room_type_id}/availability', [CustomerHotelController::class, 'availability']);

    // Protected Routes for Customers (e.g., booking)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/booking/create', [CustomerBookingController::class, 'store']);
        Route::get('/bookings', [CustomerBookingController::class, 'index']);
        Route::get('/bookings/{id}', [CustomerBookingController::class, 'show']);
        Route::put('/booking/cancel/{id}', [CustomerBookingController::class, 'cancel']);
    });



    // Hotel Admin Authentication Routes
    Route::post('/hotel-admin/register', [HotelAdminAuthController::class, 'register']);
    Route::post('/hotel-admin/login', [HotelAdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/hotel-admin/logout', [HotelAdminAuthController::class, 'logout']);
        Route::get('/hotel-admin/me', [HotelAdminAuthController::class, 'me']);
    });

    // Hotel Management Routes (Protected by Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        // Hotel Management Routes
        Route::post('/hotel/create', [HotelAdminHotelController::class, 'create']);
        Route::put('/hotel/update/{id}', [HotelAdminHotelController::class, 'update']);
        Route::delete('/hotel/delete/{id}', [HotelAdminHotelController::class, 'delete']);

        // Room Type Management Routes
        Route::post('/roomtype/create', [HotelAdminRoomTypeController::class, 'create']);
        Route::put('/roomtype/update/{id}', [HotelAdminRoomTypeController::class, 'update']);
        Route::delete('/roomtype/delete/{id}', [HotelAdminRoomTypeController::class, 'delete']);

        // Price Management Routes
        Route::post('/price/create', [HotelAdminPriceController::class, 'create']);
    });

    // Super Admin Routes (Protected by Sanctum and SuperAdmin Middleware)
    Route::middleware('auth:sanctum')->group(function () {
        // Super Admin Hotel Approval Routes
        Route::put('/hotel/approve/{id}', [SuperAdminHotelController::class, 'approve']);
        Route::put('/hotel/reject/{id}', [SuperAdminHotelController::class, 'reject']);
    });

});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
