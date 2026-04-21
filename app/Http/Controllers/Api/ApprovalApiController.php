<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Approval;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApprovalApiController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * GET /api/approvals - List pending bookings for admin
     */
    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('approve-booking')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $bookings = Booking::pending()
            ->with('user', 'laboratory', 'equipment')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return response()->json($bookings);
    }

    /**
     * POST /api/approvals/{booking}/approve - Approve booking
     */
    public function approve(Request $request, Booking $booking)
    {
        if (!$request->user()->hasPermission('approve-booking')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $this->approvalService->approve(
                $booking,
                $request->user()->user_id,
                $validated['remarks'] ?? null
            );

            return response()->json([
                'message' => 'Booking approved successfully',
                'booking' => $booking->fresh()->load('approval')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /api/approvals/{booking}/reject - Reject booking
     */
    public function reject(Request $request, Booking $booking)
    {
        if (!$request->user()->hasPermission('reject-booking')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        try {
            $this->approvalService->reject(
                $booking,
                $request->user()->user_id,
                $validated['remarks']
            );

            return response()->json([
                'message' => 'Booking rejected successfully',
                'booking' => $booking->fresh()->load('approval')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/approvals - Get approval history
     */
    public function history(Request $request)
    {
        $approvals = Approval::with('booking', 'admin')
            ->orderBy('decided_at', 'desc')
            ->paginate(15);

        return response()->json($approvals);
    }
}
