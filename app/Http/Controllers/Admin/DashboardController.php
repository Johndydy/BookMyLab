<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingBookingsCount = Booking::where('status', 'pending')->count();
        $totalLaboratories = Laboratory::count();
        $totalEquipment = Equipment::count();
        $totalUsers = User::where('role', 'user')->count();

        $recentBookings = Booking::where('status', 'pending')
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
