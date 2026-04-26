@extends('layouts.admin')
@section('title', 'Manage Users')

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
    <table class="table table-hover mobile-table">
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
                    <td data-label="Name"><strong>{{ $user->full_name }}</strong></td>
                    <td data-label="Student ID"><small class="text-muted">{{ $user->student_id_number }}</small></td>
                    <td data-label="Email">{{ $user->school_email }}</td>
                    <td data-label="Total Bookings">{{ $user->bookings_count }}</td>
                    <td data-label="Joined">{{ $user->created_at->format('M d, Y') }}</td>
                    <td data-label="Actions" class="actions-cell">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i> View Bookings
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4 empty-cell">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $users->links() }}</div>
@endsection