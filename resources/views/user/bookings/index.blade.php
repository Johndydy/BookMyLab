@extends('layouts.app')
@section('title', 'My Bookings')
@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-7 col-md-6">
        <h2 class="page-title mb-0">My Bookings</h2>
        <p class="text-muted mb-0 d-none d-md-block">Manage your laboratory bookings</p>
    </div>
    <div class="col-5 col-md-6 text-end">
        <a href="{{ route('user.bookings.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">New Booking</span><span class="d-sm-none">New</span>
        </a>
    </div>
</div>

{{-- Desktop Table View --}}
<div class="d-none d-lg-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Laboratory</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $booking->laboratory->name }}</div>
                            <small class="text-muted">{{ $booking->laboratory->location }}</small>
                        </td>
                        <td>{{ $booking->start_time->format('M d, Y') }}</td>
                        <td>{{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</td>
                        <td><span title="{{ $booking->purpose }}">{{ Str::limit($booking->purpose, 40) }}</span></td>
                        <td>
                            @if($booking->status === 'pending')
                                <span class="badge status-badge status-pending">Pending</span>
                            @elseif($booking->status === 'approved')
                                <span class="badge status-badge status-approved">Approved</span>
                            @elseif($booking->status === 'rejected')
                                <span class="badge status-badge status-rejected">Rejected</span>
                            @else
                                <span class="badge status-badge status-cancelled">Cancelled</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($booking->status === 'pending')
                                <form action="{{ route('user.bookings.destroy', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0"
                                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-3">No bookings found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Mobile Card View --}}
<div class="d-lg-none">
    @forelse($bookings as $booking)
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-0" style="color: var(--dark-blue);">{{ $booking->laboratory->name }}</h5>
                        <small class="text-muted">{{ $booking->laboratory->location }}</small>
                    </div>
                    @if($booking->status === 'pending')
                        <span class="badge status-badge status-pending">Pending</span>
                    @elseif($booking->status === 'approved')
                        <span class="badge status-badge status-approved">Approved</span>
                    @elseif($booking->status === 'rejected')
                        <span class="badge status-badge status-rejected">Rejected</span>
                    @else
                        <span class="badge status-badge status-cancelled">Cancelled</span>
                    @endif
                </div>
                
                <hr class="my-2 opacity-10">
                
                <div class="row g-0 mb-2">
                    <div class="col-6">
                        <small class="text-muted d-block">Date</small>
                        <span class="small fw-500"><i class="bi bi-calendar3 me-1"></i> {{ $booking->start_time->format('M d, Y') }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Time</small>
                        <span class="small fw-500"><i class="bi bi-clock me-1"></i> {{ $booking->start_time->format('g:i A') }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Purpose</small>
                    <p class="small mb-0 text-dark">{{ $booking->purpose }}</p>
                </div>
                
                @if($booking->status === 'pending')
                    <form action="{{ route('user.bookings.destroy', $booking) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-2"
                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                            <i class="bi bi-x-circle"></i> Cancel Booking
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-5 bg-white rounded-3 shadow-sm">
            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3; color: var(--dark-blue);"></i>
            <p class="mt-3 text-muted">No bookings yet.</p>
            <a href="{{ route('user.bookings.create') }}" class="btn btn-primary px-4">Create One Now</a>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $bookings->links() }}
</div>
@endsection