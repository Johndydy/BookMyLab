@extends('layouts.admin')

@section('title', $user->name . ' Bookings')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>{{ $user->name }}'s Bookings</h2>
        <p class="text-muted">Email: {{ $user->school_email }}</p>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Laboratory</th>
                <th>Date</th>
                <th>Time</th>
                <th>Purpose</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->laboratory->name }}</td>
                    <td>{{ $booking->start_time->format('M d, Y') }}</td>
                    <td>{{ $booking->start_time->format('H:i') }} - {{ $booking->end_time->format('H:i') }}</td>
                    <td>{{ Str::limit($booking->purpose, 30) }}</td>
                    <td>
                        @if($booking->status === 'pending')
                            <span class="badge badge-pending">Pending</span>
                        @elseif($booking->status === 'approved')
                            <span class="badge badge-approved">Approved</span>
                        @elseif($booking->status === 'rejected')
                            <span class="badge badge-rejected">Rejected</span>
                        @else
                            <span class="badge badge-cancelled">Cancelled</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No bookings found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $bookings->links() }}
</div>

<a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">Back to Users</a>
@endsection
