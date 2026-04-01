<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $approvedBookings = Booking::where('user_id', $user->user_id)
            ->where('status', 'approved')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get();

        $pendingBookings = Booking::where('user_id', $user->user_id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.dashboard', compact('approvedBookings', 'pendingBookings'));
    }
}
