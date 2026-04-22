@extends('layouts.admin')
@section('title', 'Manage User Roles')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.user-roles.index') }}" class="btn btn-sm btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <h2><i class="bi bi-person-badge"></i> Manage Roles for {{ $user->full_name }}</h2>
    <p class="text-muted">Email: {{ $user->school_email }}</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Available Roles</h5>
            </div>
            <div class="card-body">
                @forelse($roles as $role)
                    <div class="card mb-3">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="card-title mb-1">{{ $role->name }}</h6>
                                    <small class="text-muted d-block">{{ $role->description }}</small>
                                    <small class="badge bg-secondary">
                                        {{ $role->permissions->count() }} permissions
                                    </small>
                                </div>
                                <div class="col-md-4 text-end">
                                    @if($user->roles->contains('role_id', $role->role_id))
                                        <form action="{{ route('admin.user-roles.remove', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="role_id" value="{{ $role->role_id }}">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Remove {{ $role->name }} role?')">
                                                <i class="bi bi-x-circle"></i> Remove
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.user-roles.assign', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="role_id" value="{{ $role->role_id }}">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-plus-circle"></i> Assign
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No roles available.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Current Roles ({{ $user->roles->count() }})</h5>
            </div>
            <div class="card-body">
                @forelse($user->roles as $role)
                    <div class="alert alert-info mb-2" role="alert">
                        <div class="row align-items-center">
                            <div class="col">
                                <strong>{{ $role->name }}</strong>
                                <small class="d-block text-muted">{{ $role->description }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">This user has no roles assigned.</p>
                @endforelse
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light">
                <h5 class="mb-0">User Permissions</h5>
            </div>
            <div class="card-body p-0">
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($user->getAllPermissions() as $permission)
                        <div class="p-2 border-bottom">
                            <small><strong>{{ $permission->name }}</strong></small>
                        </div>
                    @empty
                        <p class="text-muted p-2 mb-0">No permissions.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
