@extends('layouts.admin')

@section('title', 'Manage Laboratories')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Manage Laboratories</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.laboratories.create') }}" class="btn btn-primary">+ Add Laboratory</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.laboratories.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
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
    <table class="table table-hover">
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
                    <td>{{ $lab->name }}</td>
                    <td>{{ $lab->department->name }}</td>
                    <td>{{ $lab->location }}</td>
                    <td>{{ $lab->capacity }} people</td>
                    <td>
                        @if($lab->status === 'available')
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-warning">Maintenance</span>
                        @endif
                    </td>
                    <td>{{ $lab->equipment->count() }} items</td>
                    <td>
                        <a href="{{ route('admin.laboratories.edit', $lab) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.laboratories.destroy', $lab) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No laboratories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $laboratories->links() }}
</div>
@endsection
