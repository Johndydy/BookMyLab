@extends('layouts.app')

@section('title', 'Book a Laboratory')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2>Book a Laboratory</h2>
        <p class="text-muted">Select a laboratory and choose your desired time slot.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">New Booking</div>
            <div class="card-body">
                <form action="{{ route('user.bookings.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="laboratory_id" class="form-label">Laboratory <span class="text-danger">*</span></label>
                        <select class="form-select @error('laboratory_id') is-invalid @enderror" id="laboratory_id" name="laboratory_id" required>
                            <option value="">-- Select a Laboratory --</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->laboratory_id }}" {{ old('laboratory_id') == $lab->laboratory_id ? 'selected' : '' }}>
                                    {{ $lab->name }} ({{ $lab->department->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('laboratory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" id="purpose" name="purpose" rows="3" required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="start_time" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="end_time" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Equipment (Optional)</label>
                        <div id="equipment-selection">
                            <p class="text-muted small">Select laboratory first to see available equipment.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Booking Request</button>
                    <a href="{{ route('user.bookings.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Important Notes</div>
            <div class="card-body">
                <ul class="small">
                    <li>Select an available laboratory from the dropdown.</li>
                    <li>Choose a date and time in the future.</li>
                    <li>Clearly specify the purpose of your booking.</li>
                    <li>Optionally select equipment you'll need.</li>
                    <li>Your booking will be reviewed by administrators.</li>
                    <li>You will be notified once it is approved or rejected.</li>
                    <li>You can only cancel pending bookings.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.getElementById('laboratory_id').addEventListener('change', function() {
        const labId = this.value;
        const equipmentDiv = document.getElementById('equipment-selection');

        if (!labId) {
            equipmentDiv.innerHTML = '<p class="text-muted small">Select laboratory first to see available equipment.</p>';
            return;
        }

        // Fetch equipment for the selected laboratory
        fetch(`/bookings/laboratory/${labId}/equipment`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    equipmentDiv.innerHTML = '<p class="text-muted small">No equipment available for this laboratory.</p>';
                } else {
                    let html = '';
                    data.forEach((item, index) => {
                        html += `
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="equipment_ids[]" value="${item.equipment_id}" id="eq_${item.equipment_id}">
                                <label class="form-check-label" for="eq_${item.equipment_id}">
                                    ${item.name} (Available: ${item.quantity})
                                </label>
                                <input type="number" class="form-control form-control-sm mt-1" name="equipment_quantities[]" value="1" min="1" max="${item.quantity}" placeholder="Qty">
                            </div>
                        `;
                    });
                    equipmentDiv.innerHTML = html;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                equipmentDiv.innerHTML = '<p class="text-danger small">Error loading equipment.</p>';
            });
    });
</script>
@endsection
