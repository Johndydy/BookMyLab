<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Laboratory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
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

        // Weekly Schedule Data
        $laboratories = Laboratory::orderBy('name')->get();
        $selectedLabId = $request->get('lab_id', $laboratories->first()->laboratory_id ?? null);

        // Week navigation: default to current week's Monday
        $weekOffset = (int) $request->get('week_offset', 0);
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Fetch approved bookings for the selected lab within this week
        $weeklyBookings = [];
        $calendarBookings = [];
        if ($selectedLabId) {
            $calendarBookings = Booking::with(['user', 'laboratory', 'equipment'])
                ->where('laboratory_id', $selectedLabId)
                ->where('status', 'approved')
                ->where(function ($q) use ($weekStart, $weekEnd) {
                    $q->whereBetween('start_time', [$weekStart, $weekEnd])
                      ->orWhereBetween('end_time', [$weekStart, $weekEnd])
                      ->orWhere(function ($q2) use ($weekStart, $weekEnd) {
                          $q2->where('start_time', '<=', $weekStart)
                             ->where('end_time', '>=', $weekEnd);
                      });
                })
                ->get();

            // Build a lookup: day_of_week => hour => booking info
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            foreach ($days as $day) {
                $weeklyBookings[$day] = [];
            }

            foreach ($calendarBookings as $booking) {
                $bookingStart = $booking->start_time;
                $bookingEnd = $booking->end_time;

                // Iterate through each day of the week (Mon-Sat only)
                for ($date = $weekStart->copy(); $date->lte($weekEnd); $date->addDay()) {
                    $dayName = $date->format('l');
                    if ($dayName === 'Sunday') continue; // Skip Sunday

                    // Saturday: 7 AM to 11 AM (last slot ends at 12 PM)
                    // Weekdays: 7 AM to 5 PM (last slot ends at 6 PM)
                    $maxHour = ($dayName === 'Saturday') ? 11 : 17;

                    for ($hour = 7; $hour <= $maxHour; $hour++) {
                        $slotStart = $date->copy()->setTime($hour, 0, 0);
                        $slotEnd = $date->copy()->setTime($hour + 1, 0, 0);

                        // Check if booking overlaps with this slot
                        if ($bookingStart < $slotEnd && $bookingEnd > $slotStart) {
                            $weeklyBookings[$dayName][$hour] = [
                                'user' => $booking->user->full_name,
                                'purpose' => $booking->purpose,
                                'start' => $bookingStart->format('g:i A'),
                                'end' => $bookingEnd->format('g:i A'),
                                'booking_id' => $booking->booking_id,
                            ];
                        }
                    }
                }
            }
        }

        return view('admin.dashboard', compact(
            'pendingBookingsCount',
            'totalLaboratories',
            'totalEquipment',
            'totalUsers',
            'recentBookings',
            'laboratories',
            'selectedLabId',
            'weeklyBookings',
            'calendarBookings',
            'weekStart',
            'weekEnd',
            'weekOffset'
        ));
    }
}