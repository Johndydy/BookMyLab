@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-title">
    <i class="bi bi-speedometer2"></i> Dashboard
</div>
<p class="page-subtitle">System overview and recent activities</p>

<div class="row mb-5">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card">
            <i class="bi bi-clock-history" style="font-size: 2rem; opacity: 0.7;"></i>
            <div class="stat-number">{{ $pendingBookingsCount }}</div>
            <div class="stat-label">Pending Bookings</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card">
            <i class="bi bi-building" style="font-size: 2rem; opacity: 0.7;"></i>
            <div class="stat-number">{{ $totalLaboratories }}</div>
            <div class="stat-label">Laboratories</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card">
            <i class="bi bi-tools" style="font-size: 2rem; opacity: 0.7;"></i>
            <div class="stat-number">{{ $totalEquipment }}</div>
            <div class="stat-label">Equipment Items</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card">
            <i class="bi bi-people" style="font-size: 2rem; opacity: 0.7;"></i>
            <div class="stat-number">{{ $totalUsers }}</div>
            <div class="stat-label">Registered Users</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3 class="mb-4">
            <i class="bi bi-calendar-event"></i> Recent Pending Bookings
        </h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Laboratory</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                        <tr>
                            <td>
                                <i class="bi bi-person"></i>
                                <strong>{{ $booking->user->name }}</strong>
                            </td>
                            <td>{{ $booking->laboratory->name }}</td>
                            <td>{{ $booking->start_time->format('M d, Y') }}</td>
                            <td>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.bookings.index') }}?status=pending" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-right"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                                <p class="mt-2">No pending bookings - all caught up!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
