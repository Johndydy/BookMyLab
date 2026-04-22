@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Notifications</h2>
    </div>
    <div class="col-md-4 text-end">
        @if($notifications->count() > 0)
            <form action="{{ route('user.notifications.read') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-secondary">
                    <i class="bi bi-check2-all"></i> Mark All as Read
                </button>
            </form>
        @endif
    </div>
</div>

@forelse($notifications as $notification)
    <div class="card mb-3 {{ $notification->is_read ? '' : 'border-primary' }}">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="mb-1">{{ $notification->message }}</p>
                    <small class="text-muted">
                        Booking #{{ $notification->booking_id }} &mdash;
                        {{ $notification->created_at->diffForHumans() }}
                    </small>
                </div>
                @if(!$notification->is_read)
                    <span class="badge bg-primary">New</span>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info">
        <i class="bi bi-bell-slash"></i> You have no notifications yet.
        Once your bookings are reviewed, you'll be notified here.
    </div>
@endforelse

<div class="d-flex justify-content-center mt-3">{{ $notifications->links() }}</div>
@endsection