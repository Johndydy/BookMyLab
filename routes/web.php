<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout')->middleware('auth');

// Google Auth Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/auth/google/complete-setup', [AuthController::class, 'completeGoogleSetup'])->name('auth.google.complete-setup')->middleware('auth');

// User Routes
Route::middleware(['auth', 'user'])->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::resource('/bookings', BookingController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/bookings/laboratory/{laboratory}/equipment', function ($laboratory) {
        // Ensure laboratory exists and is accessible
        \App\Models\Laboratory::findOrFail($laboratory);
        return \App\Models\Equipment::where('laboratory_id', $laboratory)
            ->select('equipment_id', 'name', 'quantity')
            ->get();
    });

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read', [NotificationController::class, 'markAllRead'])->name('notifications.read');
});

