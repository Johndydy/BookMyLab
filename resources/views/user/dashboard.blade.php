@extends('layouts.app')
@section('title', 'Dashboard')
@section('styles')
<style>
    .booking-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        background: #fff;
    }
    .booking-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    .booking-card .card-header {
        background: linear-gradient(135deg, #1a2e4a 0%, #2d4a73 100%);
        color: white;
        padding: 16px 20px;
        border: none;
    }
    .booking-card .card-header h5 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
    }
    .booking-card .card-body {
        padding: 20px;
    }
    .detail-item {
        margin-bottom: 15px;
    }
    .detail-label {
        display: block;
        color: #8e9aaf;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .detail-value {
        display: block;
        color: #2d3748;
        font-weight: 600;
        font-size: 1rem;
    }
    .booking-card .card-footer {
        padding: 12px 20px;
        border-top: 1px solid #edf2f7;
        background: #fcfdfe;
    }
    .status-badge-custom {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-approved {
        background: #e6fffa;
        color: #2c7a7b;
    }
    .badge-pending {
        background: #fffaf0;
        color: #b7791f;
    }
</style>
@endsection
@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="page-title"><i class="bi bi-house-fill"></i> Welcome, {{ auth()->user()->full_name }}!</h2>
        <p class="page-subtitle">Here's a quick overview of your laboratory bookings.</p>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-6 mb-3">
        <a href="{{ route('user.bookings.create') }}" class="btn btn-primary btn-lg w-100 py-3">
            <i class="bi bi-plus-circle"></i> Book a Laboratory
        </a>
    </div>
    <div class="col-md-6 mb-3">
        <a href="{{ route('user.bookings.index') }}" class="btn btn-outline-primary btn-lg w-100 py-3">
            <i class="bi bi-calendar-check"></i> View All Bookings
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4 class="mb-3"><i class="bi bi-calendar-check"></i> Upcoming Approved Bookings</h4>
        @if($approvedBookings->count() > 0)
            <div class="row">
                @foreach($approvedBookings as $booking)
                    <div class="col-md-6 mb-4">
                        <div class="card booking-card h-100">
                            <div class="card-header">
                                <h5><i class="bi bi-building me-2"></i>{{ $booking->laboratory->name }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <span class="detail-label">Department</span>
                                    <span class="detail-value">{{ $booking->laboratory->department->name }}</span>
                                </div>
                                <div class="row g-0">
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Date</span>
                                            <span class="detail-value"><i class="bi bi-calendar3 me-1"></i> {{ $booking->start_time->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Time</span>
                                            <span class="detail-value"><i class="bi bi-clock me-1"></i> {{ $booking->start_time->format('g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-item mb-0">
                                    <span class="detail-label">Purpose</span>
                                    <span class="detail-value" style="font-weight: 500;">{{ Str::limit($booking->purpose, 80) }}</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="status-badge-custom badge-approved">
                                    <i class="bi bi-check-circle-fill"></i> Approved
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No upcoming approved bookings.
                <a href="{{ route('user.bookings.create') }}">Book a lab now!</a>
            </div>
        @endif
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <h4 class="mb-3"><i class="bi bi-clock-history"></i> Pending Bookings</h4>
        @if($pendingBookings->count() > 0)
            <div class="row">
                @foreach($pendingBookings as $booking)
                    <div class="col-md-6 mb-4">
                        <div class="card booking-card h-100">
                            <div class="card-header">
                                <h5><i class="bi bi-building me-2"></i>{{ $booking->laboratory->name }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <span class="detail-label">Department</span>
                                    <span class="detail-value">{{ $booking->laboratory->department->name }}</span>
                                </div>
                                <div class="row g-0">
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Date</span>
                                            <span class="detail-value"><i class="bi bi-calendar3 me-1"></i> {{ $booking->start_time->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Time</span>
                                            <span class="detail-value"><i class="bi bi-clock me-1"></i> {{ $booking->start_time->format('g:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-item mb-0">
                                    <span class="detail-label">Purpose</span>
                                    <span class="detail-value" style="font-weight: 500;">{{ Str::limit($booking->purpose, 80) }}</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="status-badge-custom badge-pending">
                                    <i class="bi bi-hourglass-split"></i> Pending Review
                                </div>
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