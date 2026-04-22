@extends('layouts.admin')
@section('title', 'Edit Department')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.departments.index') }}" class="btn btn-sm btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <h2>Edit Department</h2>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $department->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="building" class="form-label">Building <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('building') is-invalid @enderror"
                               id="building" name="building" value="{{ old('building', $department->building) }}" required>
                        @error('building')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Department</button>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection