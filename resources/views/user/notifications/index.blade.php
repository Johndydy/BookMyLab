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
                <button type="submit" class="btn btn-sm btn-secondary">Mark All as Read</button>
            </form>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @forelse($notifications as $notification)
            <div class="card mb-3 {{ $notification->is_read ? '' : 'border-primary' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="card-text">{{ $notification->message }}</p>
                            <small class="text-muted">
                                Booking #{{ $notification->booking->booking_id }} - 
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div>
                            @if(!$notification->is_read)
                                <span class="badge badge-primary bg-primary">New</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">No Notifications</h4>
                <p>You don't have any notifications yet. Once your bookings are reviewed, you'll receive notifications here.</p>
            </div>
        @endforelse

        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
