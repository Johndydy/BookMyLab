@extends('layouts.admin')

@section('title', 'Manage Departments')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>Manage Departments</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">+ Add Department</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.departments.index') }}" method="GET" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
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
                <th>Building</th>
                <th>Laboratories</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departments as $dept)
                <tr>
                    <td>{{ $dept->name }}</td>
                    <td>{{ $dept->building }}</td>
                    <td>{{ $dept->laboratories_count }}</td>
                    <td>
                        <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No departments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $departments->links() }}
</div>
@endsection
