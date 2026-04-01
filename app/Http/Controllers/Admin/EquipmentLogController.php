<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentLog;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = EquipmentLog::with('booking', 'equipment', 'booking.user', 'booking.laboratory')
            ->orderBy('borrowed_at', 'desc')
            ->paginate(15);

        return view('admin.equipment_logs.index', compact('logs'));
    }

    public function update(Request $request, EquipmentLog $log)
    {
        $request->validate([
            'condition_after' => 'required|in:good,damaged',
        ]);

        try {
            DB::transaction(function () use ($log, $request) {
                $log->update([
                    'returned_at'    => now(),
                    'condition_after' => $request->condition_after,
                ]);

                // If equipment was damaged, update equipment condition
                if ($request->condition_after === 'damaged') {
                    $log->equipment->update(['condition' => 'damaged']);
                }
            });

            return redirect()->back()
                ->with('success', 'Equipment returned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process equipment return: ' . $e->getMessage());
        }
    }
}
