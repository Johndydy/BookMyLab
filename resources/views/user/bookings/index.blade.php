@extends('layouts.app')
@section('title', 'My Bookings')
@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>My Bookings</h2>
        <p class="text-muted">Manage your laboratory bookings</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('user.bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Booking
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
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
                    <td><strong>{{ $booking->laboratory->name }}</strong></td>
                    <td>{{ $booking->start_time->format('M d, Y') }}</td>
                    <td>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</td>
                    <td>{{ Str::limit($booking->purpose, 40) }}</td>
                    <td>
                        @if($booking->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($booking->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($booking->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-secondary">Cancelled</span>
                        @endif
                    </td>
                    <td>
                        @if($booking->status === 'pending')
                            <form action="{{ route('user.bookings.destroy', $booking) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                    <i class="bi bi-trash"></i> Cancel
                                </button>
                            </form>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2">No bookings yet. <a href="{{ route('user.bookings.create') }}">Create one now</a></p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $bookings->links() }}</div>
@endsection