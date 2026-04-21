<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Laboratory;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $mostBookedLab = Laboratory::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->first();

        $busiestSlots = DB::table('bookings')
            ->selectRaw('HOUR(start_time) as hour, COUNT(*) as count')
            ->where('status', 'approved')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $mostRequestedEquipment = Equipment::withCount('bookingEquipment')
            ->orderBy('booking_equipment_count', 'desc')
            ->limit(5)
            ->get();

        // Fixed: use first_name + last_name instead of name
        $topBookers = DB::table('users')
            ->join('bookings', 'users.user_id', '=', 'bookings.user_id')
            ->selectRaw('users.user_id, CONCAT(users.first_name, " ", users.last_name) as full_name, users.school_email, COUNT(*) as booking_count')
            ->groupBy('users.user_id', 'users.first_name', 'users.last_name', 'users.school_email')
            ->orderBy('booking_count', 'desc')
            ->limit(10)
            ->get();

        $totalApprovals  = Approval::count();
        $approvedCount   = Approval::where('decision', 'approved')->count();
        $rejectedCount   = Approval::where('decision', 'rejected')->count();
        $approvalRate    = $totalApprovals > 0 ? round(($approvedCount / $totalApprovals) * 100, 2) : 0;

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