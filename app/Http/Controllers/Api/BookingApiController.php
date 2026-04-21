<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Laboratory;
use App\Models\Equipment;
use App\Services\BookingService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingApiController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * GET /api/bookings - List user's bookings
     */
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with('laboratory', 'equipment')
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        return response()->json($bookings);
    }

    /**
     * POST /api/bookings - Create new booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'laboratory_id' => 'required|exists:laboratories,laboratory_id',
            'start_time' => 'required|date_format:Y-m-d H:i:s|after:now',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'purpose' => 'required|string|max:1000',
            'equipment_ids' => 'array',
            'equipment_ids.*' => 'exists:equipment,equipment_id',
            'equipment_quantities' => 'array',
        ]);

        try {
            $booking = $this->bookingService->create($validated, $request->user()->user_id);
            return response()->json($booking->load('laboratory', 'equipment'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/bookings/{booking} - Get booking details
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return response()->json($booking->load('laboratory', 'equipment', 'approval'));
    }

    /**
     * DELETE /api/bookings/{booking} - Cancel booking
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        try {
            $this->bookingService->cancel($booking);
            return response()->json(['message' => 'Booking cancelled successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/laboratories - List available laboratories
     */
    public function laboratoryList()
    {
        $labs = Laboratory::available()
            ->with('equipment', 'department')
            ->get();

        return response()->json($labs);
    }

    /**
     * GET /api/laboratories/{laboratory}/equipment - Get equipment in lab
     */
    public function laboratoryEquipment(Laboratory $laboratory)
    {
        $equipment = $laboratory->equipment()->get();
        return response()->json($equipment);
    }

    /**
     * GET /api/laboratories/{laboratory}/availability - Check availability
     */
    public function checkAvailability(Request $request, Laboratory $laboratory)
    {
        $validated = $request->validate([
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
        ]);

        $hasConflict = $this->bookingService->hasConflict(
            $laboratory->laboratory_id,
            $validated['start_time'],
            $validated['end_time']
        );

        return response()->json([
            'laboratory_id' => $laboratory->laboratory_id,
            'available' => !$hasConflict,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);
    }
}
