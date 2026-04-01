<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Most booked laboratory
        $mostBookedLab = Laboratory::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->first();

        // Busiest time slots (bookings by hour)
        $busiestSlots = DB::table('bookings')
            ->selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
            ->where('status', 'approved')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Equipment most requested
        $mostRequestedEquipment = Equipment::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        // Top bookers
        $topBookers = DB::table('users')
            ->join('bookings', 'users.user_id', '=', 'bookings.user_id')
            ->selectRaw('users.user_id, users.name, users.school_email, COUNT(*) as booking_count')
            ->groupBy('users.user_id', 'users.name', 'users.school_email')
            ->orderBy('booking_count', 'desc')
            ->limit(10)
            ->get();

        // Approval rate
        $totalApprovals = Approval::count();
        $approvedCount = Approval::where('decision', 'approved')->count();
        $rejectedCount = Approval::where('decision', 'rejected')->count();

        $approvalRate = $totalApprovals > 0 ? round(($approvedCount / $totalApprovals) * 100, 2) : 0;

        return view('admin.reports.index', compact(
            'mostBookedLab',
            'busiestSlots',
            'mostRequestedEquipment',
            'topBookers',
            'totalApprovals',
            'approvedCount',
            'rejectedCount',
            'approvalRate'
        ));
    }
}
