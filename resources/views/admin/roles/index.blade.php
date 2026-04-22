@extends('layouts.admin')
@section('title', 'Manage Roles')
@section('content')
<div class="row mb-4">
    <div class="col-md-10">
        <h2><i class="bi bi-shield-check"></i> Manage Roles</h2>
        <p class="text-muted">Define and manage user roles in the system</p>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary w-100">
            <i class="bi bi-plus-circle"></i> New Role
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
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td><strong>{{ $role->name }}</strong></td>
                        <td>{{ $role->description ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ $role->permissions->count() }} permissions
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $role->users->count() }} users
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $role->created_at->format('M d, Y') }}</small></td>
                        <td>
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($role->name !== 'administrator')
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" 
                                        onclick="return confirm('Delete this role?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No roles found. <a href="{{ route('admin.roles.create') }}">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
