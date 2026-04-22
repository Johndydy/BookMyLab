@extends('layouts.admin')
@section('title', 'Manage Permissions')
@section('content')
<div class="row mb-4">
    <div class="col-md-10">
        <h2><i class="bi bi-key"></i> Manage Permissions</h2>
        <p class="text-muted">Define system-wide permissions</p>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary w-100">
            <i class="bi bi-plus-circle"></i> New Permission
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Permission Name</th>
                    <th>Description</th>
                    <th>Roles Using This</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                    <tr>
                        <td><strong>{{ $permission->name }}</strong></td>
                        <td>{{ $permission->description ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ $permission->roles->count() }} roles
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $permission->created_at->format('M d, Y') }}</small></td>
                        <td>
                            <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" 
                                    onclick="return confirm('Delete this permission?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No permissions found. <a href="{{ route('admin.permissions.create') }}">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
