@extends('layouts.admin')
@section('title', 'Manage User Roles')
@section('content')
<div class="mb-4">
    <h2><i class="bi bi-person-badge"></i> Manage User Roles</h2>
    <p class="text-muted">Assign and manage roles for users</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.user-roles.index') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control"
                    placeholder="Search by name, email, or student ID..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>User Name</th>
                <th>School Email</th>
                <th>Student ID</th>
                <th>Current Roles</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->full_name }}</strong></td>
                    <td>{{ $user->school_email }}</td>
                    <td><small class="text-muted">{{ $user->student_id_number }}</small></td>
                    <td>
                        @forelse($user->roles as $role)
                            <span class="badge bg-info">{{ $role->name }}</span>
                        @empty
                            <span class="text-muted">No roles</span>
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('admin.user-roles.show', $user) }}" class="btn btn-sm btn-primary" title="Manage Roles">
                            <i class="bi bi-gear"></i> Manage
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        No users found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(method_exists($users, 'links'))
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
@endif
@endsection
