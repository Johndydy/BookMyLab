@extends('layouts.admin')
@section('title', 'View Role')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Roles
    </a>
    <h2><i class="bi bi-shield-check"></i> {{ $role->name }}</h2>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Role Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted">Role Name</label>
                        <p class="mb-0"><strong>{{ $role->name }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted">Created</label>
                        <p class="mb-0">{{ $role->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Description</label>
                    <p class="mb-0">{{ $role->description ?? 'No description' }}</p>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @if($role->name !== 'administrator')
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Delete this role?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Permissions ({{ $role->permissions->count() }})</h5>
            </div>
            <div class="card-body">
                @forelse($role->permissions as $permission)
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="mb-1">{{ $permission->name }}</h6>
                        <small class="text-muted">{{ $permission->description }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No permissions assigned to this role.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Users with this Role ({{ $role->users->count() }})</h5>
            </div>
            <div class="card-body">
                @forelse($role->users as $user)
                    <div class="mb-2 pb-2 border-bottom">
                        <strong>{{ $user->full_name }}</strong>
                        <small class="d-block text-muted">{{ $user->school_email }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No users have this role.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
