@extends('layouts.admin')
@section('title', 'Add Role')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Roles
    </a>
    <h2><i class="bi bi-shield-plus"></i> Add New Role</h2>
</div>

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
            <div class="card-body">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="e.g., manager, supervisor" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="Role description and responsibilities">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="permissions" class="form-label">Assign Permissions</label>
                        <div class="card border-light">
                            <div class="card-body p-2">
                                @forelse($permissions as $permission)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="perm_{{ $permission->permission_id }}" 
                                               name="permissions[]" 
                                               value="{{ $permission->permission_id }}"
                                               {{ old('permissions') && in_array($permission->permission_id, old('permissions')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $permission->permission_id }}">
                                            <strong>{{ $permission->name }}</strong>
                                            <small class="text-muted d-block">{{ $permission->description }}</small>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No permissions available.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Role
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
