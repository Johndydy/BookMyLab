@extends('layouts.admin')
@section('title', 'View Permission')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-sm btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Permissions
    </a>
    <h2><i class="bi bi-key"></i> {{ $permission->name }}</h2>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Permission Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted">Permission Name</label>
                        <p class="mb-0"><strong>{{ $permission->name }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Created</label>
                        <p class="mb-0">{{ $permission->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Description</label>
                    <p class="mb-0">{{ $permission->description ?? 'No description' }}</p>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" 
                            onclick="return confirm('Delete this permission?')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Roles Using This Permission ({{ $permission->roles->count() }})</h5>
            </div>
            <div class="card-body">
                @forelse($permission->roles as $role)
                    <div class="mb-2 pb-2 border-bottom">
                        <a href="{{ route('admin.roles.show', $role) }}" class="text-decoration-none">
                            <strong>{{ $role->name }}</strong>
                        </a>
                        <small class="d-block text-muted">{{ $role->description }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No roles have this permission.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
