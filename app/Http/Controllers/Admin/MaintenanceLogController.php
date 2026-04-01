<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laboratory;
use App\Models\MaintenanceLog;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = MaintenanceLog::with('laboratory', 'admin')
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        $laboratories = Laboratory::all();

        return view('admin.maintenance_logs.index', compact('logs', 'laboratories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'laboratory_id' => 'required|exists:laboratories,laboratory_id',
            'reason'        => 'required|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $lab = Laboratory::findOrFail($request->laboratory_id);

                // Update lab status to maintenance
                $lab->update(['status' => 'maintenance']);

                // Create maintenance log
                MaintenanceLog::create([
                    'laboratory_id' => $lab->laboratory_id,
                    'admin_id'      => auth()->user()->user_id,
                    'reason'        => $request->reason,
                    'started_at'    => now(),
                    'ended_at'      => null,
                ]);

                // Auto-reject all pending bookings for this lab
                Booking::where('laboratory_id', $lab->laboratory_id)
                    ->where('status', 'pending')
                    ->update(['status' => 'rejected']);
            });

            return redirect()->route('admin.maintenance_logs.index')
                ->with('success', 'Maintenance mode activated. All pending bookings for this lab have been auto-rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate maintenance mode: ' . $e->getMessage());
        }
    }

    public function update(Request $request, MaintenanceLog $log)
    {
        try {
            DB::transaction(function () use ($log) {
                $log->update(['ended_at' => now()]);

                // Update lab status back to available
                $log->laboratory->update(['status' => 'available']);
            });

            return redirect()->route('admin.maintenance_logs.index')
                ->with('success', 'Maintenance ended. Laboratory is now available for booking.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to end maintenance: ' . $e->getMessage());
        }
    }
}
