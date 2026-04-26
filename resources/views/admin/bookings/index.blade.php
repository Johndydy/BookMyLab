@extends('layouts.admin')
@section('title', 'Manage Bookings')
@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-12 col-md-7 mb-3 mb-md-0">
        <h2>Manage Bookings</h2>
    </div>
    <div class="col-12 col-md-5">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
                <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Rejected</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-primary px-4">Filter</button>
        </form>
    </div>
</div>

{{-- Desktop Table View --}}
<div class="d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Laboratory</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <strong>{{ $booking->user->full_name }}</strong><br>
                            <small class="text-muted">{{ $booking->user->school_email }}</small>
                        </td>
                        <td>{{ $booking->laboratory->name }}</td>
                        <td>{{ $booking->start_time->format('M d, Y') }}</td>
                        <td>{{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</td>
                        <td>{{ Str::limit($booking->purpose, 30) }}</td>
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
                        <td>
                            @if($booking->status === 'pending')
                                <button class="btn btn-sm btn-approve mb-1"
                                    data-bs-toggle="modal" data-bs-target="#approveModal{{ $booking->booking_id }}">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-reject"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $booking->booking_id }}">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No bookings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Mobile Card View --}}
<div class="d-md-none">
    @forelse($bookings as $booking)
        <div class="card mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">{{ $booking->user->full_name }}</h5>
                        <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $booking->user->school_email }}</div>
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

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="small text-muted mb-1">Laboratory</div>
                        <div class="fw-semibold"><i class="bi bi-building me-1 text-primary"></i>{{ $booking->laboratory->name }}</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted mb-1">Date</div>
                        <div class="fw-semibold"><i class="bi bi-calendar3 me-1 text-primary"></i>{{ $booking->start_time->format('M d, Y') }}</div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="small text-muted mb-1">Time Slot</div>
                        <div class="fw-semibold"><i class="bi bi-clock me-1 text-primary"></i>{{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="small text-muted mb-1">Purpose</div>
                        <div class="text-dark bg-light p-2 rounded" style="font-size: 0.9rem;">{{ $booking->purpose }}</div>
                    </div>
                </div>

                @if($booking->status === 'pending')
                    <div class="d-grid gap-2">
                        <button class="btn btn-lg btn-approve py-3"
                            data-bs-toggle="modal" data-bs-target="#approveModal{{ $booking->booking_id }}">
                            <i class="bi bi-check-lg me-2"></i> Approve Booking
                        </button>
                        <button class="btn btn-lg btn-reject py-3"
                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $booking->booking_id }}">
                            <i class="bi bi-x-lg me-2"></i> Reject Booking
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <p class="mt-3 text-muted">No bookings found for the selected status.</p>
        </div>
    @endforelse
</div>

{{-- Modals remain global --}}
@foreach($bookings as $booking)
    @if($booking->status === 'pending')
        {{-- Approve Modal --}}
        <div class="modal fade" id="approveModal{{ $booking->booking_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Approve Booking</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="text-muted">You are about to approve the booking for <strong>{{ $booking->user->full_name }}</strong>. This will notify the user.</p>
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Remarks <span class="text-muted">(Optional)</span></label>
                                <textarea class="form-control" name="remarks" rows="3" placeholder="Add any special instructions or remarks..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-approve px-4">Confirm Approval</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectModal{{ $booking->booking_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">Reject Booking</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="text-muted">Please provide a reason for rejecting the booking for <strong>{{ $booking->user->full_name }}</strong>.</p>
                            <div class="mb-0">
                                <label class="form-label fw-semibold text-danger">Reason for Rejection *</label>
                                <textarea class="form-control border-danger" name="remarks" rows="3"
                                    placeholder="Explain why this booking is being rejected..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-reject px-4">Confirm Rejection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<div class="d-flex justify-content-center mt-3">{{ $bookings->links() }}</div>
@endsection