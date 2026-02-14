<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Customer\CustomerAuthController;
use App\Http\Controllers\Api\HotelAdmin\HotelAdminAuthController;

// API Routes
Route::prefix('v1')->group(function () {

    // Customer Authentication Routes
    Route::post('/customer/register', [CustomerAuthController::class, 'register']);
    Route::post('/customer/login', [CustomerAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/customer/me', [CustomerAuthController::class, 'me']);
    });

    // Hotel Admin Authentication Routes
    Route::post('/hotel-admin/register', [HotelAdminAuthController::class, 'register']);
    Route::post('/hotel-admin/login', [HotelAdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/hotel-admin/logout', [HotelAdminAuthController::class, 'logout']);
        Route::get('/hotel-admin/me', [HotelAdminAuthController::class, 'me']);
    });
    
});

//----------

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
