@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-5">
    <div class="col-md-12">
        <div class="page-title">
            <i class="bi bi-house-fill"></i> Welcome, {{ auth()->user()->name }}!
        </div>
        <p class="page-subtitle">Here's a quick overview of your laboratory bookings.</p>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-6 mb-3 mb-md-0">
        <a href="{{ route('user.bookings.create') }}" class="btn btn-primary btn-lg w-100 py-3">
            <i class="bi bi-plus-circle"></i> Book a Laboratory
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('user.bookings.index') }}" class="btn btn-outline-primary btn-lg w-100 py-3">
            <i class="bi bi-calendar-check"></i> View All Bookings
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3 class="mb-4">
            <i class="bi bi-calendar-check"></i> Upcoming Approved Bookings
        </h3>
        @if($approvedBookings->count() > 0)
            <div class="row">
                @foreach($approvedBookings as $booking)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-building"></i> {{ $booking->laboratory->name }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">Department</small>
                                    <div>{{ $booking->laboratory->department->name }}</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Location</small>
                                    <div>{{ $booking->laboratory->location }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Date</small>
                                        <div>{{ $booking->start_time->format('M d, Y') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Time</small>
                                        <div>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Purpose</small>
                                    <div>{{ Str::limit($booking->purpose, 60) }}</div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Approved
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No upcoming approved bookings. <a href="{{ route('user.bookings.create') }}">Book a lab now!</a>
            </div>
        @endif
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-12">
        <h3 class="mb-4">
            <i class="bi bi-clock-history"></i> Pending Bookings
        </h3>
        @if($pendingBookings->count() > 0)
            <div class="row">
                @foreach($pendingBookings as $booking)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-building"></i> {{ $booking->laboratory->name }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">Department</small>
                                    <div>{{ $booking->laboratory->department->name }}</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Location</small>
                                    <div>{{ $booking->laboratory->location }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Date</small>
                                        <div>{{ $booking->start_time->format('M d, Y') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Time</small>
                                        <div>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Purpose</small>
                                    <div>{{ Str::limit($booking->purpose, 60) }}</div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split"></i> Pending Review
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No pending bookings.
            </div>
        @endif
    </div>
</div>
@endsection
