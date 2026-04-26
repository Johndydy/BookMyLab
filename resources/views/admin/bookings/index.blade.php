@extends('layouts.admin')
@section('title', 'Manage Bookings')
@section('content')
<div class="row mb-4">
    <div class="col-md-7">
        <h2>Manage Bookings</h2>
    </div>
    <div class="col-md-5">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
                <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Rejected</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>
</div>

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
                            <button class="btn btn-sm btn-success mb-1"
                                data-bs-toggle="modal" data-bs-target="#approveModal{{ $booking->booking_id }}">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $booking->booking_id }}">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>

                            {{-- Approve Modal --}}
                            <div class="modal fade" id="approveModal{{ $booking->booking_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approve Booking</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p class="text-muted">
                                                    Approving booking for <strong>{{ $booking->user->full_name }}</strong>
                                                    at <strong>{{ $booking->laboratory->name }}</strong>
                                                    on {{ $booking->start_time->format('M d, Y g:i A') }}.
                                                </p>
                                                <div class="mb-3">
                                                    <label class="form-label">Remarks <span class="text-muted">(Optional)</span></label>
                                                    <textarea class="form-control" name="remarks" rows="3" placeholder="Add a note..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">Approve</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Reject Modal --}}
                            <div class="modal fade" id="rejectModal{{ $booking->booking_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Booking</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p class="text-muted">
                                                    Rejecting booking for <strong>{{ $booking->user->full_name }}</strong>
                                                    at <strong>{{ $booking->laboratory->name }}</strong>.
                                                </p>
                                                <div class="mb-3">
                                                    <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="remarks" rows="3"
                                                        placeholder="Explain why this booking is being rejected..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
<div class="d-flex justify-content-center mt-3">{{ $bookings->links() }}</div>
@endsection