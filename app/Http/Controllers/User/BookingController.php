<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\BookingRequest;
use App\Models\Booking;
use App\Models\Laboratory;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        $user = auth()->user();
        $bookings = Booking::where('user_id', $user->user_id)
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('user.bookings.index', compact('bookings'));
    }

    public function create()
    {
        if (!auth()->user()->hasCompletedProfile()) {
            return redirect()->route('user.profile.edit')
                ->with('error', 'Please complete all required fields in your profile before booking a laboratory.');
        }

        $laboratories = Laboratory::available()->get();
        return view('user.bookings.create', compact('laboratories'));
    }

    public function store(BookingRequest $request)
    {
        if (!auth()->user()->hasCompletedProfile()) {
            return redirect()->route('user.profile.edit')
                ->with('error', 'Please complete all required fields in your profile before booking a laboratory.');
        }

        try {
            $this->bookingService->create(
                $request->validated(),
                auth()->user()->user_id
            );

            return redirect()->route('user.bookings.index')
                ->with('success', 'Booking submitted successfully! Waiting for admin approval.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        try {
            $this->bookingService->cancel($booking);
            return redirect()->route('user.bookings.index')
                ->with('success', 'Booking cancelled successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel booking.');
        }
    }
}
