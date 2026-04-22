@extends('layouts.admin')
@section('title', 'Edit Equipment')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.equipment.index') }}" class="btn btn-sm btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <h2>Edit Equipment</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.equipment.update', $equipment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="laboratory_id" class="form-label">Laboratory <span class="text-danger">*</span></label>
                        <select class="form-select @error('laboratory_id') is-invalid @enderror"
                                id="laboratory_id" name="laboratory_id" required>
                            <option value="">-- Select Laboratory --</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->laboratory_id }}"
                                    {{ old('laboratory_id', $equipment->laboratory_id) == $lab->laboratory_id ? 'selected' : '' }}>
                                    {{ $lab->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('laboratory_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Equipment Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $equipment->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                               id="quantity" name="quantity" value="{{ old('quantity', $equipment->quantity) }}" min="1" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="condition" class="form-label">Condition <span class="text-danger">*</span></label>
                        <select class="form-select @error('condition') is-invalid @enderror"
                                id="condition" name="condition" required>
                            <option value="good"        {{ old('condition', $equipment->condition) === 'good'         ? 'selected' : '' }}>Good</option>
                            <option value="damaged"     {{ old('condition', $equipment->condition) === 'damaged'      ? 'selected' : '' }}>Damaged</option>
                            <option value="under repair" {{ old('condition', $equipment->condition) === 'under repair' ? 'selected' : '' }}>Under Repair</option>
                        </select>
                        @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update Equipment</button>
                    <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection