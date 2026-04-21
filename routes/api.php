<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ApprovalApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public endpoint - get current user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles', 'bookings');
});

// Protected API routes (require Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Booking endpoints
    Route::get('/bookings', [BookingApiController::class, 'index'])->name('api.bookings.index');
    Route::post('/bookings', [BookingApiController::class, 'store'])->name('api.bookings.store');
    Route::get('/bookings/{booking}', [BookingApiController::class, 'show'])->name('api.bookings.show');
    Route::delete('/bookings/{booking}', [BookingApiController::class, 'destroy'])->name('api.bookings.destroy');

    // Laboratory endpoints
    Route::get('/laboratories', [BookingApiController::class, 'laboratoryList'])->name('api.laboratories.list');
    Route::get('/laboratories/{laboratory}/equipment', [BookingApiController::class, 'laboratoryEquipment'])->name('api.laboratories.equipment');
    Route::post('/laboratories/{laboratory}/check-availability', [BookingApiController::class, 'checkAvailability'])->name('api.laboratories.check-availability');

    // Approval endpoints (admin only)
    Route::get('/approvals', [ApprovalApiController::class, 'index'])->name('api.approvals.index');
    Route::post('/approvals/{booking}/approve', [ApprovalApiController::class, 'approve'])->name('api.approvals.approve');
    Route::post('/approvals/{booking}/reject', [ApprovalApiController::class, 'reject'])->name('api.approvals.reject');
    Route::get('/approvals-history', [ApprovalApiController::class, 'history'])->name('api.approvals.history');
});

