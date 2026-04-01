@extends('layouts.admin')

@section('title', 'Maintenance Logs')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Laboratory Maintenance Logs</h2>
    </div>
    <div class="col-md-4 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMaintenanceModal">
            + Start Maintenance
        </button>
    </div>
</div>

<!-- New Maintenance Modal -->
<div class="modal fade" id="newMaintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start Laboratory Maintenance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.maintenance_logs.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="laboratory_id" class="form-label">Laboratory <span class="text-danger">*</span></label>
                        <select class="form-select @error('laboratory_id') is-invalid @enderror" id="laboratory_id" name="laboratory_id" required>
                            <option value="">-- Select a Laboratory --</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->laboratory_id }}">
                                    {{ $lab->name }} ({{ $lab->department->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('laboratory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="reason" class="form-label">Reason for Maintenance <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="4" required placeholder="Describe the maintenance work..."></textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Start Maintenance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Laboratory</th>
                <th>Reason</th>
                <th>Started By</th>
                <th>Started At</th>
                <th>Ended At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->laboratory->name }}</td>
                    <td>{{ Str::limit($log->reason, 40) }}</td>
                    <td>{{ $log->admin->name }}</td>
                    <td>{{ $log->started_at->format('M d, Y H:i') }}</td>
                    <td>
                        @if($log->ended_at)
                            {{ $log->ended_at->format('M d, Y H:i') }}
                        @else
                            <span class="badge badge-danger">Ongoing</span>
                        @endif
                    </td>
                    <td>
                        @if(!$log->ended_at)
                            <span class="badge badge-warning">In Progress</span>
                        @else
                            <span class="badge badge-success">Completed</span>
                        @endif
                    </td>
                    <td>
                        @if(!$log->ended_at)
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#endMaintenanceModal{{ $log->log_id }}">
                                Mark as Ended
                            </button>

                            <!-- End Maintenance Modal -->
                            <div class="modal fade" id="endMaintenanceModal{{ $log->log_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">End Maintenance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.maintenance_logs.update', $log) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <p>Are you sure you want to mark maintenance as ended? The laboratory will be set to available for booking.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">End Maintenance</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No maintenance logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $logs->links() }}
</div>
@endsection
