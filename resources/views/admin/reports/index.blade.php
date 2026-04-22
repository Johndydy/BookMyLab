@extends('layouts.admin')
@section('title', 'Reports')
@section('content')
<h2 class="mb-4"><i class="bi bi-graph-up"></i> Reports & Analytics</h2>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">Most Booked Laboratory</div>
            <div class="card-body">
                @if($mostBookedLab)
                    <h5>{{ $mostBookedLab->name }}</h5>
                    <p class="text-muted mb-0">{{ $mostBookedLab->bookings_count }} total bookings</p>
                @else
                    <p class="text-muted mb-0">No booking data available yet.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card h-100">
            <div class="card-header">Approval Rate</div>
            <div class="card-body">
                <h5>{{ $approvalRate }}% Approved</h5>
                <p class="text-muted mb-0">
                    Approved: <strong>{{ $approvedCount }}</strong> &nbsp;|&nbsp;
                    Rejected: <strong>{{ $rejectedCount }}</strong> &nbsp;|&nbsp;
                    Total: <strong>{{ $totalApprovals }}</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Busiest Time Slots</div>
    <div class="card-body">
        @if($busiestSlots->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Hour</th><th>Approved Bookings</th></tr>
                    </thead>
                    <tbody>
                        @foreach($busiestSlots as $slot)
                            <tr>
                                <td>{{ str_pad($slot->hour, 2, '0', STR_PAD_LEFT) }}:00 — {{ str_pad($slot->hour + 1, 2, '0', STR_PAD_LEFT) }}:00</td>
                                <td>{{ $slot->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No data available yet.</p>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Most Requested Equipment</div>
    <div class="card-body">
        @if($mostRequestedEquipment->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Equipment</th><th>Laboratory</th><th>Times Requested</th></tr>
                    </thead>
                    <tbody>
                        @foreach($mostRequestedEquipment as $eq)
                            <tr>
                                <td>{{ $eq->name }}</td>
                                <td>{{ $eq->laboratory->name }}</td>
                                <td>{{ $eq->booking_equipment_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No data available yet.</p>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">Top Bookers</div>
    <div class="card-body">
        @if($topBookers->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Name</th><th>Email</th><th>Total Bookings</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topBookers as $booker)
                            <tr>
                                <td>{{ $booker->full_name }}</td>
                                <td>{{ $booker->school_email }}</td>
                                <td>{{ $booker->booking_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No data available yet.</p>
        @endif
    </div>
</div>
@endsection