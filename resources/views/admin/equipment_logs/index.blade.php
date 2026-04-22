@extends('layouts.admin')
@section('title', 'Equipment Logs')
@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Equipment Borrowing & Return Logs</h2>
        <p class="text-muted">Track all equipment borrowed and returned per booking.</p>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
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
                    <td><strong>{{ $log->equipment->name }}</strong></td>
                    <td>
                        {{ $log->booking->user->full_name }}<br>
                        <small class="text-muted">Booking #{{ $log->booking->booking_id }}</small>
                    </td>
                    <td>{{ $log->booking->laboratory->name }}</td>
                    <td>{{ $log->quantity_borrowed }}</td>
                    <td>{{ $log->borrowed_at->format('M d, Y H:i') }}</td>
                    <td>
                        @if($log->returned_at)
                            {{ $log->returned_at->format('M d, Y H:i') }}
                        @else
                            <span class="badge bg-warning text-dark">Not Returned</span>
                        @endif
                    </td>
                    <td>
                        @if($log->condition_after === 'good')
                            <span class="badge bg-success">Good</span>
                        @elseif($log->condition_after === 'damaged')
                            <span class="badge bg-danger">Damaged</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
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
                    <td colspan="8" class="text-center text-muted py-4">No equipment logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $logs->links() }}</div>
@endsection