@extends('layouts.admin')
@section('title', 'Manage Laboratories')

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
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2>Manage Laboratories</h2>
    </div>
    <div class="col-md-6 text-md-end">
        <a href="{{ route('admin.laboratories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Laboratory
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.laboratories.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                    placeholder="Search by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="available"  {{ request('status') === 'available'  ? 'selected' : '' }}>Available</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover mobile-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Location</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Equipment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laboratories as $lab)
                <tr>
                    <td data-label="Name"><strong>{{ $lab->name }}</strong></td>
                    <td data-label="Department">{{ $lab->department->name }}</td>
                    <td data-label="Location">{{ $lab->location }}</td>
                    <td data-label="Capacity">{{ $lab->capacity }} people</td>
                    <td data-label="Status">
                        @if($lab->status === 'available')
                            <span class="badge status-badge status-approved">Available</span>
                        @else
                            <span class="badge status-badge status-pending">Maintenance</span>
                        @endif
                    </td>
                    <td data-label="Equipment">{{ $lab->equipment->count() }} items</td>
                    <td data-label="Actions" class="actions-cell">
                        <a href="{{ route('admin.laboratories.edit', $lab) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.laboratories.destroy', $lab) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this laboratory? This cannot be undone.')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4 empty-cell">No laboratories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $laboratories->links() }}</div>
@endsection