<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Laboratory;
use App\Models\MaintenanceLog;
use App\Models\EquipmentLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * Get booking statistics
     */
    public function getBookingStats($dateFrom = null, $dateTo = null)
    {
        $query = Booking::query();

        if ($dateFrom) {
            $query->where('start_time', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('end_time', '<=', $dateTo);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->clone()->pending()->count(),
            'approved' => $query->clone()->approved()->count(),
            'rejected' => $query->clone()->rejected()->count(),
            'cancelled' => $query->clone()->cancelled()->count(),
            'approval_rate' => $this->calculateApprovalRate($dateFrom, $dateTo),
        ];
    }

    /**
     * Get equipment condition report
     */
    public function getEquipmentConditionReport()
    {
        return [
            'good' => Equipment::where('condition', 'good')->count(),
            'damaged' => Equipment::where('condition', 'damaged')->count(),
            'under_repair' => Equipment::where('condition', 'under repair')->count(),
        ];
    }

    /**
     * Get laboratory utilization report
     */
    public function getLaboratoryUtilizationReport($dateFrom = null, $dateTo = null)
    {
        $query = Booking::with('laboratory')
            ->where('status', 'approved');

        if ($dateFrom) {
            $query->where('start_time', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('end_time', '<=', $dateTo);
        }

        $bookings = $query->get();

        $utilization = [];
        foreach (Laboratory::all() as $lab) {
            $labBookings = $bookings->where('laboratory_id', $lab->laboratory_id);
            $totalHours = $labBookings->sum(function ($booking) {
                return $booking->start_time->diffInHours($booking->end_time);
            });

            $utilization[] = [
                'laboratory_id' => $lab->laboratory_id,
                'name' => $lab->name,
                'total_bookings' => $labBookings->count(),
                'total_hours' => $totalHours,
                'utilization_percentage' => $this->calculateUtilization($totalHours),
            ];
        }

        return collect($utilization)->sortByDesc('total_bookings')->values();
    }

    /**
     * Get top users by bookings
     */
    public function getTopUsersByBookings($limit = 10)
    {
        return DB::table('users as u')
            ->leftJoin('bookings as b', 'u.user_id', '=', 'b.user_id')
            ->select('u.user_id', 'u.name', 'u.school_email', DB::raw('COUNT(b.booking_id) as total_bookings'))
            ->groupBy('u.user_id', 'u.name', 'u.school_email')
            ->orderByDesc('total_bookings')
            ->limit($limit)
            ->get();
    }

    /**
     * Get maintenance impact report
     */
    public function getMaintenanceImpactReport()
    {
        $logs = MaintenanceLog::with('laboratory')
            ->orderBy('started_at', 'desc')
            ->get();

        return $logs->map(function ($log) {
            $duration = $log->ended_at ? $log->started_at->diffInHours($log->ended_at) : null;
            $cancelledBookings = Booking::where('laboratory_id', $log->laboratory_id)
                ->where('status', 'rejected')
                ->whereBetween('start_time', [$log->started_at, $log->ended_at ?? now()])
                ->count();

            return [
                'lab_name' => $log->laboratory->name,
                'reason' => $log->reason,
                'started_at' => $log->started_at,
                'ended_at' => $log->ended_at,
                'duration_hours' => $duration,
                'cancelled_bookings' => $cancelledBookings,
                'status' => $log->ended_at ? 'completed' : 'ongoing',
            ];
        });
    }

    /**
     * Get equipment usage report
     */
    public function getEquipmentUsageReport($dateFrom = null, $dateTo = null)
    {
        $query = EquipmentLog::with('equipment', 'booking');

        if ($dateFrom) {
            $query->where('borrowed_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('borrowed_at', '<=', $dateTo);
        }

        $logs = $query->get();

        $usage = [];
        foreach (Equipment::all() as $equipment) {
            $equipmentLogs = $logs->where('equipment_id', $equipment->equipment_id);
            $damageCount = $equipmentLogs->where('condition_after', 'damaged')->count();

            $usage[] = [
                'equipment_id' => $equipment->equipment_id,
                'name' => $equipment->name,
                'laboratory' => $equipment->laboratory->name,
                'times_borrowed' => $equipmentLogs->count(),
                'damage_incidents' => $damageCount,
                'current_condition' => $equipment->condition,
            ];
        }

        return collect($usage)->sortByDesc('times_borrowed')->values();
    }

    /**
     * Get peak booking hours
     */
    public function getPeakBookingHours($dateFrom = null, $dateTo = null)
    {
        $query = Booking::where('status', 'approved');

        if ($dateFrom) {
            $query->where('start_time', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('end_time', '<=', $dateTo);
        }

        $bookings = $query->get();

        $hourlyStats = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $count = $bookings->filter(function ($booking) use ($hour) {
                $start = $booking->start_time->hour;
                $end = $booking->end_time->hour;
                return $hour >= $start && $hour < $end;
            })->count();

            $hourlyStats[] = [
                'hour' => sprintf('%02d:00', $hour),
                'bookings' => $count,
            ];
        }

        return $hourlyStats;
    }

    /**
     * Get approval statistics by admin
     */
    public function getApprovalStatsByAdmin()
    {
        return DB::table('approvals as a')
            ->join('users as u', 'a.admin_id', '=', 'u.user_id')
            ->select(
                'u.user_id',
                'u.name',
                DB::raw('COUNT(a.approval_id) as total_decisions'),
                DB::raw("SUM(CASE WHEN a.decision = 'approved' THEN 1 ELSE 0 END) as approved"),
                DB::raw("SUM(CASE WHEN a.decision = 'rejected' THEN 1 ELSE 0 END) as rejected")
            )
            ->groupBy('u.user_id', 'u.name')
            ->orderByDesc('total_decisions')
            ->get();
    }

    /**
     * Calculate approval rate
     */
    private function calculateApprovalRate($dateFrom = null, $dateTo = null)
    {
        $query = Booking::query();

        if ($dateFrom) {
            $query->where('start_time', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('end_time', '<=', $dateTo);
        }

        $total = $query->count();
        if ($total === 0) {
            return 0;
        }

        $approved = $query->clone()->approved()->count();
        return round(($approved / $total) * 100, 2);
    }

    /**
     * Calculate laboratory utilization percentage
     */
    private function calculateUtilization($totalHours)
    {
        // Assuming labs are available 8 hours per day
        $daysInPeriod = 30; // Default to 30 days
        $totalAvailableHours = $daysInPeriod * 8;
        $percentage = ($totalHours / $totalAvailableHours) * 100;
        return round(min($percentage, 100), 2);
    }

    /**
     * Generate dashboard statistics
     */
    public function getDashboardStats()
    {
        return [
            'pending_bookings' => Booking::pending()->count(),
            'approved_bookings' => Booking::approved()->count(),
            'total_laboratories' => Laboratory::count(),
            'available_laboratories' => Laboratory::available()->count(),
            'maintenance_in_progress' => MaintenanceLog::ongoing()->count(),
            'total_equipment' => Equipment::count(),
            'damaged_equipment' => Equipment::damaged()->count(),
            'equipment_under_repair' => Equipment::underRepair()->count(),
        ];
    }
}
