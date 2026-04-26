@extends('layouts.admin')
@section('title', 'Manage Departments')

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
    <div class="col-md-8"><h2>Manage Departments</h2></div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Department
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.departments.index') }}" method="GET" class="row g-3">
            <div class="col-md-9">
                <input type="text" name="search" class="form-control"
                    placeholder="Search by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
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
                <th>Building</th>
                <th>Laboratories</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departments as $dept)
                <tr>
                    <td data-label="Name"><strong>{{ $dept->name }}</strong></td>
                    <td data-label="Building">{{ $dept->building }}</td>
                    <td data-label="Laboratories">{{ $dept->laboratories_count }}</td>
                    <td data-label="Actions" class="actions-cell">
                        <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this department?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4 empty-cell">No departments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $departments->links() }}</div>
@endsection