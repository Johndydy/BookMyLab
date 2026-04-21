<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ApprovalApiController;
use Illuminate\Support\Facades\Route;

// Public routes — no token needed
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected routes — requires valid Sanctum token
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Student routes — requires student role
    Route::middleware('role:student')->group(function () {
        Route::get('/bookings',                                       [BookingApiController::class, 'index'])->name('api.bookings.index');
        Route::post('/bookings',                                      [BookingApiController::class, 'store'])->name('api.bookings.store');
        Route::get('/bookings/{booking}',                             [BookingApiController::class, 'show'])->name('api.bookings.show');
        Route::delete('/bookings/{booking}',                          [BookingApiController::class, 'destroy'])->name('api.bookings.destroy');

        Route::get('/laboratories',                                   [BookingApiController::class, 'laboratoryList'])->name('api.laboratories.list');
        Route::get('/laboratories/{laboratory}/equipment',            [BookingApiController::class, 'laboratoryEquipment'])->name('api.laboratories.equipment');
        Route::post('/laboratories/{laboratory}/check-availability',  [BookingApiController::class, 'checkAvailability'])->name('api.laboratories.check-availability');
    });

    // Admin routes — requires administrator role
    Route::middleware('role:administrator')->prefix('admin')->group(function () {
        // Static routes first — always before routes with {parameters}
        Route::get('/approvals',                      [ApprovalApiController::class, 'index'])->name('api.approvals.index');
        Route::get('/approvals/history',              [ApprovalApiController::class, 'history'])->name('api.approvals.history');

        // Dynamic routes after
        Route::post('/approvals/{booking}/approve',   [ApprovalApiController::class, 'approve'])->name('api.approvals.approve');
        Route::post('/approvals/{booking}/reject',    [ApprovalApiController::class, 'reject'])->name('api.approvals.reject');
    });
});