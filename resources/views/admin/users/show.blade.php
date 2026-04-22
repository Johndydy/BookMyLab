@extends('layouts.admin')
@section('title', $user->full_name . ' — Bookings')
@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
        <h2>{{ $user->full_name }}'s Bookings</h2>
        <p class="text-muted">
            {{ $user->school_email }} &middot; ID: {{ $user->school_id_number }}
        </p>
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
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">This user has no bookings.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $bookings->links() }}</div>
@endsection