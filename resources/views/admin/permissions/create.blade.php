@extends('layouts.admin')
@section('title', 'Add Permission')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-sm btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Permissions
    </a>
    <h2><i class="bi bi-key-fill"></i> Add New Permission</h2>
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
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="e.g., view_bookings, approve_bookings" required>
                        <small class="form-text text-muted">Use snake_case format</small>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3"
                                  placeholder="What does this permission allow?">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Permission
                        </button>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
