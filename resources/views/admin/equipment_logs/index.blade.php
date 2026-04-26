@extends('layouts.admin')
@section('title', 'Equipment Logs')

@section('styles')
<style>
    @media (max-width: 767.98px) {
        .mobile-table, .mobile-table tbody, .mobile-table tr, .mobile-table td {
            display: block;
            width: 100%;
        }
        .mobile-table thead {
            display: none;
        }
        .mobile-table tr {
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: var(--border-radius, 8px);
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .mobile-table td {
            text-align: right;
            padding-left: 45%;
            position: relative;
            border-bottom: 1px solid #f0f0f0;
            border-top: none !important;
            word-wrap: break-word;
            min-height: 3rem;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
        .mobile-table td:last-child {
            border-bottom: 0;
        }
        .mobile-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            top: 0.75rem;
            width: 45%;
            padding-left: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark-blue, #1a2e4a);
        }
        .mobile-table td.actions-cell {
            padding-left: 15px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        .mobile-table td.actions-cell::before {
            position: static;
            width: auto;
            padding-left: 0;
            margin-right: auto;
        }
        .mobile-table td.empty-cell {
            padding-left: 15px;
            text-align: center;
        }
        .mobile-table td.empty-cell::before {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Equipment Borrowing & Return Logs</h2>
        <p class="text-muted">Track all equipment borrowed and returned per booking.</p>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover mobile-table">
        <thead>
            <tr>
                <th>Equipment</th>
                <th>User</th>
                <th>Laboratory</th>
                <th>Qty Borrowed</th>
                <th>Borrowed At</th>
                <th>Returned At</th>
                <th>Condition</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td data-label="Equipment"><strong>{{ $log->equipment->name }}</strong></td>
                    <td data-label="User">
                        {{ $log->booking->user->full_name }}<br>
                        <small class="text-muted">Booking #{{ $log->booking->booking_id }}</small>
                    </td>
                    <td data-label="Laboratory">{{ $log->booking->laboratory->name }}</td>
                    <td data-label="Qty Borrowed">{{ $log->quantity_borrowed }}</td>
                    <td data-label="Borrowed At">{{ $log->borrowed_at->format('M d, Y g:i A') }}</td>
                    <td data-label="Returned At">
                        @if($log->returned_at)
                            {{ $log->returned_at->format('M d, Y g:i A') }}
                        @else
                            <span class="badge bg-warning text-dark">Not Returned</span>
                        @endif
                    </td>
                    <td data-label="Condition">
                        @if($log->condition_after === 'good')
                            <span class="badge bg-success">Good</span>
                        @elseif($log->condition_after === 'damaged')
                            <span class="badge bg-danger">Damaged</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td data-label="Actions" class="actions-cell">
                        @if(!$log->returned_at)
                            <button class="btn btn-sm btn-primary"
                                data-bs-toggle="modal" data-bs-target="#returnModal{{ $log->equipmentlog_id }}">
                                Mark Returned
                            </button>

                            <div class="modal fade" id="returnModal{{ $log->equipmentlog_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Mark Equipment as Returned</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.equipment_logs.update', $log) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <p>Marking <strong>{{ $log->equipment->name }}</strong> as returned
                                                   from <strong>{{ $log->booking->user->full_name }}</strong>.</p>
                                                <div class="mb-3">
                                                    <label class="form-label">Condition After Return <span class="text-danger">*</span></label>
                                                    <select class="form-select" name="condition_after" required>
                                                        <option value="">-- Select Condition --</option>
                                                        <option value="good">Good</option>
                                                        <option value="damaged">Damaged</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Mark as Returned</button>
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
                    <td colspan="8" class="text-center text-muted py-4 empty-cell">No equipment logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $logs->links() }}</div>
@endsection