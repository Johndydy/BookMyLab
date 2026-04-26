@extends('layouts.admin')
@section('title', 'Manage Users')
@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Manage Users</h2>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
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
                <th>Name</th>
                <th>Student ID</th>
                <th>Email</th>
                <th>Total Bookings</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->full_name }}</strong></td>
                    <td><small class="text-muted">{{ $user->student_id_number }}</small></td>
                    <td>{{ $user->school_email }}</td>
                    <td>{{ $user->bookings_count }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i> View Bookings
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $users->links() }}</div>
@endsection