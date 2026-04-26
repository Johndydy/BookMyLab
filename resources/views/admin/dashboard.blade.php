@extends('layouts.admin')
@section('title', 'Dashboard')

@section('styles')
    <style>
        /* ── Weekly Schedule Grid ── */
        .schedule-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.1);
        }

        .schedule-card:hover {
            transform: none;
        }

        /* disable default card hover lift */

        .schedule-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 16px 20px;
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            color: var(--white);
        }

        .schedule-toolbar h5 {
            color: var(--white);
            margin: 0;
            font-weight: 700;
        }

        .schedule-toolbar .btn-week {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--white);
            border-radius: 6px;
            padding: 6px 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .schedule-toolbar .btn-week:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .schedule-toolbar .week-label {
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .schedule-toolbar select {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--white);
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }

        .schedule-toolbar select option {
            color: #333;
            background: #fff;
        }

        .schedule-legend {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 10px 20px;
            background: #f0f4f8;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.8rem;
            color: #4a5568;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 3px;
            display: inline-block;
        }

        .legend-booked {
            background: linear-gradient(135deg, #a8d5a2 0%, #7bc67e 100%);
        }

        .legend-available {
            background: #ffffff;
            border: 1px solid #d1d5db;
        }

        .schedule-grid-wrapper {
            overflow-x: auto;
        }

        .schedule-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            table-layout: fixed;
            min-width: 820px;
        }

        .schedule-grid thead th {
            background: #1e3554;
            color: #fff;
            text-align: center;
            padding: 12px 6px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: 1px solid #16293f;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .schedule-grid thead th:first-child {
            width: 110px;
        }

        .schedule-grid tbody tr {
            border-bottom: 1px solid #e8ecf1;
        }

        .schedule-grid tbody td {
            padding: 0;
            height: 52px;
            border: 1px solid #e8ecf1;
            vertical-align: middle;
            text-align: center;
            position: relative;
            transition: background 0.15s ease;
        }

        .schedule-grid tbody td:first-child {
            background: #f7f9fc;
            font-weight: 700;
            color: var(--dark-blue);
            font-size: 0.78rem;
            padding: 6px 8px;
            text-align: right;
            border-right: 2px solid #d1d9e6;
            white-space: nowrap;
        }

        .time-main {
            display: block;
            font-size: 0.85rem;
            line-height: 1.2;
        }

        .time-range {
            display: block;
            font-size: 0.65rem;
            color: #8896a8;
            font-weight: 500;
            margin-top: 1px;
        }

        /* Available cell */
        .slot-available {
            background: #ffffff;
            cursor: default;
        }

        .slot-available:hover {
            background: #f0f7ff;
        }

        /* Booked cell */
        .slot-booked {
            background: linear-gradient(135deg, #d4edda 0%, #b7e4c7 100%);
            cursor: pointer;
            position: relative;
        }

        .slot-booked:hover {
            background: linear-gradient(135deg, #c3e6cb 0%, #a3d9b1 100%);
        }

        .slot-booked-inner {
            padding: 4px 6px;
            line-height: 1.25;
        }

        .slot-booked-user {
            font-weight: 700;
            color: #1a5928;
            font-size: 0.75rem;
            display: block;
        }

        .slot-booked-purpose {
            color: #2d6e3a;
            font-size: 0.68rem;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .slot-booked-time {
            color: #3c8a4f;
            font-size: 0.62rem;
            display: block;
            margin-top: 1px;
        }

        /* Tooltip on hover */
        .slot-tooltip {
            display: none;
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
            background: var(--dark-blue);
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.78rem;
            white-space: nowrap;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            pointer-events: none;
            text-align: left;
            min-width: 180px;
        }

        .slot-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: var(--dark-blue);
        }

        .slot-booked:hover .slot-tooltip {
            display: block;
        }

        /* Closed/unavailable cell */
        .slot-closed {
            background: #e9ecef;
            cursor: not-allowed;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(0, 0, 0, 0.03) 5px, rgba(0, 0, 0, 0.03) 10px);
        }

        @media (max-width: 768px) {
            .schedule-toolbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px 15px;
            }

            .schedule-toolbar h5 {
                font-size: 1.1rem;
            }

            .schedule-toolbar .week-label {
                font-size: 0.85rem;
                width: 100%;
                text-align: center;
                order: 3;
                margin-top: 5px;
            }

            .schedule-grid {
                min-width: 550px;
                font-size: 0.75rem;
            }

            .schedule-grid thead th {
                padding: 8px 2px;
                font-size: 0.7rem;
                letter-spacing: 0.3px;
            }

            .schedule-grid tbody td {
                height: 42px;
            }

            .schedule-grid thead th:first-child {
                width: 65px;
            }

            .slot-booked-inner {
                padding: 2px 4px;
            }

            .slot-booked-user {
                font-size: 0.68rem;
            }

            .slot-booked-purpose {
                display: none;
            }

            .schedule-legend {
                overflow-x: auto;
                white-space: nowrap;
                font-size: 0.75rem;
                padding: 10px 15px;
            }

            .stat-card {
                padding: 10px 4px;
                margin-bottom: 0;
            }

            .stat-card i {
                font-size: 1.1rem !important;
            }

            .stat-card .stat-number {
                font-size: 1.3rem !important;
                margin: 3px 0;
            }

            .stat-card .stat-label {
                font-size: 0.55rem !important;
                text-transform: uppercase;
                letter-spacing: 0.2px;
            }

            /* Maximize space for schedule card */
            .schedule-card {
                border-radius: 0;
                border-left: none;
                border-right: none;
                margin-left: -15px;
                margin-right: -15px;
            }
        }
    </style>
@endsection

@section('content')
    <h2 class="page-title"><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <p class="page-subtitle">System overview and recent activity</p>

    <div class="row mb-4 g-1 g-md-3">
        <div class="col-3 col-lg-3">
            <div class="stat-card">
                <i class="bi bi-clock-history" style="opacity: 0.7;"></i>
                <div class="stat-number">{{ $pendingBookingsCount }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-3 col-lg-3">
            <div class="stat-card">
                <i class="bi bi-building" style="opacity: 0.7;"></i>
                <div class="stat-number">{{ $totalLaboratories }}</div>
                <div class="stat-label">Labs</div>
            </div>
        </div>
        <div class="col-3 col-lg-3">
            <div class="stat-card">
                <i class="bi bi-tools" style="opacity: 0.7;"></i>
                <div class="stat-number">{{ $totalEquipment }}</div>
                <div class="stat-label">Equip</div>
            </div>
        </div>
        <div class="col-3 col-lg-3">
            <div class="stat-card">
                <i class="bi bi-people" style="opacity: 0.7;"></i>
                <div class="stat-number">{{ $totalUsers }}</div>
                <div class="stat-label">Users</div>
            </div>
        </div>
    </div>

    {{-- ── Weekly Lab Booking Schedule ── --}}
    @php
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $hours = range(7, 17); // 7 AM to 5 PM (each slot = 1 hour, last ends at 6 PM)
    @endphp

    <div class="card schedule-card mb-4">
        <div class="schedule-toolbar">
            <h5><i class="bi bi-calendar-week me-2"></i>Weekly Lab Schedule</h5>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Lab Selector --}}
                <select id="labSelector" onchange="navigateSchedule()">
                    @foreach($laboratories as $lab)
                        <option value="{{ $lab->laboratory_id }}" {{ $selectedLabId == $lab->laboratory_id ? 'selected' : '' }}>
                            {{ $lab->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Week Navigation --}}
                <a href="{{ route('admin.dashboard', ['week_offset' => $weekOffset - 1, 'lab_id' => $selectedLabId]) }}"
                    class="btn-week"><i class="bi bi-chevron-left"></i></a>
                <span class="week-label">
                    {{ $weekStart->format('M d') }} – {{ $weekEnd->format('M d, Y') }}
                </span>
                <a href="{{ route('admin.dashboard', ['week_offset' => $weekOffset + 1, 'lab_id' => $selectedLabId]) }}"
                    class="btn-week"><i class="bi bi-chevron-right"></i></a>

                @if($weekOffset != 0)
                    <a href="{{ route('admin.dashboard', ['week_offset' => 0, 'lab_id' => $selectedLabId]) }}"
                        class="btn-week">Today</a>
                @endif
            </div>
        </div>

        <div class="schedule-legend">
            <div class="legend-item"><span class="legend-dot legend-booked"></span> Booked</div>
            <div class="legend-item"><span class="legend-dot legend-available"></span> Available</div>
            <div class="legend-item"><span class="legend-dot" style="background:#e9ecef;border:1px solid #ccc;"></span>
                Closed</div>
            <div class="legend-item" style="margin-left:auto; font-style:italic; color:#6c757d;"><i
                    class="bi bi-info-circle me-1"></i>Saturday: 7 AM – 12 PM only | Sunday: Closed</div>
        </div>

        <div class="schedule-grid-wrapper">
            <table class="schedule-grid">
                <thead>
                    <tr>
                        <th>Time</th>
                        @foreach($days as $day)
                            <th>
                                <span class="d-none d-md-inline">{{ $day }}</span>
                                <span class="d-md-none">{{ substr($day, 0, 3) }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($hours as $hour)
                        @php
                            $startTime = \Carbon\Carbon::createFromTime($hour, 0)->format('g:i A');
                            $endTime = \Carbon\Carbon::createFromTime($hour + 1, 0)->format('g:i A');
                        @endphp
                        <tr>
                            <td>
                                <span class="time-main">{{ $startTime }}</span>
                                <span class="time-range">{{ $endTime }}</span>
                            </td>
                            @foreach($days as $day)
                                @if($day === 'Saturday' && $hour >= 12)
                                    {{-- Saturday afternoon: closed --}}
                                    <td class="slot-closed"></td>
                                @elseif(isset($weeklyBookings[$day][$hour]))
                                    @php $slot = $weeklyBookings[$day][$hour]; @endphp
                                    <td class="slot-booked" data-bs-toggle="modal"
                                        data-bs-target="#bookingDetailsModal{{ $slot['booking_id'] }}">
                                        <div class="slot-booked-inner">
                                            <span class="slot-booked-user">{{ Str::limit($slot['user'], 18) }}</span>
                                            <span class="slot-booked-purpose">{{ Str::limit($slot['purpose'], 20) }}</span>
                                        </div>
                                        <div class="slot-tooltip">
                                            <strong><i class="bi bi-person-fill me-1"></i>{{ $slot['user'] }}</strong><br>
                                            <span><i class="bi bi-clock me-1"></i>{{ $slot['start'] }} – {{ $slot['end'] }}</span><br>
                                            <span><i class="bi bi-card-text me-1"></i>{{ Str::limit($slot['purpose'], 40) }}</span>
                                        </div>
                                    </td>
                                @else
                                    <td class="slot-available"></td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Render Booking Details Modals --}}
    @if(isset($calendarBookings))
        @foreach($calendarBookings as $booking)
            <div class="modal fade" id="bookingDetailsModal{{ $booking->booking_id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Booking Information</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>User:</strong> {{ $booking->user->full_name }} ({{ $booking->user->school_email }})</p>
                            <p><strong>Laboratory:</strong> {{ optional($booking->laboratory)->name }}</p>
                            <p><strong>Date & Time:</strong> {{ $booking->start_time->format('M d, Y g:i A') }} -
                                {{ $booking->end_time->format('g:i A') }}
                            </p>
                            <p><strong>Purpose:</strong> {{ $booking->purpose }}</p>
                            <p><strong>Capacity:</strong> {{ $booking->number_of_persons }} persons</p>
                            @if($booking->equipment->count() > 0)
                                <p><strong>Equipment:</strong></p>
                                <ul>
                                    @foreach($booking->equipment as $eq)
                                        <li>{{ $eq->name }} (Qty: {{ $eq->pivot->quantity_requested }})</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- ── Recent Pending Bookings ── --}}
    <h4 class="mb-3 px-3 px-md-0"><i class="bi bi-calendar-event"></i> Recent Pending Bookings</h4>

    {{-- Desktop Table View --}}
    <div class="d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
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
                                <strong>{{ $booking->user->full_name }}</strong><br>
                                <small class="text-muted">{{ $booking->user->school_email }}</small>
                            </td>
                            <td>{{ $booking->laboratory->name }}</td>
                            <td>{{ $booking->start_time->format('M d, Y') }}</td>
                            <td>{{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</td>
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
                                <p class="mt-2">No pending bookings — all caught up!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Card View --}}
    <div class="d-md-none px-2">
        @forelse($recentBookings as $booking)
            <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $booking->user->full_name }}</h6>
                            <small class="text-muted">{{ $booking->user->school_email }}</small>
                        </div>
                        <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Pending</span>
                    </div>

                    <div class="mb-3" style="font-size: 0.85rem;">
                        <div class="mb-1">
                            <i class="bi bi-building text-primary me-2"></i>{{ $booking->laboratory->name }}
                        </div>
                        <div class="mb-1">
                            <i class="bi bi-calendar3 text-primary me-2"></i>{{ $booking->start_time->format('M d, Y') }}
                        </div>
                        <div>
                            <i class="bi bi-clock text-primary me-2"></i>{{ $booking->start_time->format('g:i A') }} -
                            {{ $booking->end_time->format('g:i A') }}
                        </div>
                    </div>

                    <a href="{{ route('admin.bookings.index') }}?status=pending" class="btn btn-primary btn-sm w-100 py-2">
                        <i class="bi bi-arrow-right-circle me-1"></i> Review Booking
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4 bg-white rounded shadow-sm">
                <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                <p class="mt-2">No pending bookings.</p>
            </div>
        @endforelse
    </div>
@endsection

@section('scripts')
    <script>
        function navigateSchedule() {
            const labId = document.getElementById('labSelector').value;
            const url = new URL(window.location.href);
            url.searchParams.set('lab_id', labId);
            // Reset to current week offset when switching labs
            if (!url.searchParams.has('week_offset')) {
                url.searchParams.set('week_offset', '0');
            }
            window.location.href = url.toString();
        }
    </script>
@endsection