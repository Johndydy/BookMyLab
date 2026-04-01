<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index(Request $request)
    {
        $query = Booking::with('user', 'laboratory');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function approve(Request $request, Booking $booking)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $this->approvalService->approve(
                $booking,
                auth()->user()->user_id,
                $request->remarks
            );

            return redirect()->back()
                ->with('success', 'Booking approved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve booking.');
        }
    }

    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $this->approvalService->reject(
                $booking,
                auth()->user()->user_id,
                $request->remarks
            );

            return redirect()->back()
                ->with('success', 'Booking rejected successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject booking.');
        }
    }
}
