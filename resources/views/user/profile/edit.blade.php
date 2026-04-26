@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-gear"></i> Account Settings
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('user.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name', $user->first_name) }}" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name', $user->last_name) }}" required>
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="username"
                                        class="form-control @error('username') is-invalid @enderror"
                                        value="{{ old('username', $user->username) }}" required>
                                </div>
                                @error('username') <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}
                                </div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">School Email</label>
                                <input type="text" class="form-control" value="{{ $user->school_email }}" disabled>
                                <div class="form-text">Email cannot be changed.</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">School ID Number</label>
                                <input type="text" name="school_id_number"
                                    class="form-control @error('school_id_number') is-invalid @enderror"
                                    value="{{ old('school_id_number', $user->school_id_number) }}" required>
                                @error('school_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    value="{{ old('phone_number', $user->phone_number) }}"
                                    maxlength="11"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Academic Information</h5>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Department</label>
                                <input type="text" name="department_name"
                                    class="form-control @error('department_name') is-invalid @enderror"
                                    value="{{ old('department_name', $user->department_name) }}">
                                @error('department_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Course</label>
                                <input type="text" name="course" class="form-control @error('course') is-invalid @enderror"
                                    value="{{ old('course', $user->course) }}">
                                @error('course') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year Level</label>
                                <select name="year_level" class="form-select @error('year_level') is-invalid @enderror">
                                    <option value="">Select Year</option>
                                    <option value="1st Year" {{ old('year_level', $user->year_level) == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2nd Year" {{ old('year_level', $user->year_level) == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3rd Year" {{ old('year_level', $user->year_level) == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4th Year" {{ old('year_level', $user->year_level) == '4th Year' ? 'selected' : '' }}>4th Year</option>
                                    <option value="5th Year" {{ old('year_level', $user->year_level) == '5th Year' ? 'selected' : '' }}>5th Year</option>
                                </select>
                                @error('year_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Bio <span class="text-muted small">(Optional)</span></label>
                            <textarea name="bio" class="form-control @error('bio') is-invalid @enderror"
                                rows="3">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-light me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection