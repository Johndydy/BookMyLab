<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Laboratory;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingBookingsCount = Booking::where('status', 'pending')->count();
        $totalLaboratories    = Laboratory::count();
        $totalEquipment       = Equipment::count();

        // Count users who have the student role via user_roles table
        $totalUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'student');
        })->count();

        $recentBookings = Booking::with('user', 'laboratory')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'pendingBookingsCount',
            'totalLaboratories',
            'totalEquipment',
            'totalUsers',
            'recentBookings'
        ));
    }
}