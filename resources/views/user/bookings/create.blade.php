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
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time_display" value="{{ old('start_time') && strtotime(old('start_time')) ? (new \DateTime(old('start_time'), new \DateTimeZone('UTC')))->format('Y-m-d\TH:i') : '' }}" required>
                                <input type="hidden" id="start_time_hidden" name="start_time">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="end_time" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time_display" value="{{ old('end_time') && strtotime(old('end_time')) ? (new \DateTime(old('end_time'), new \DateTimeZone('UTC')))->format('Y-m-d\TH:i') : '' }}" required>
                                <input type="hidden" id="end_time_hidden" name="end_time">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Equipment (Optional)</label>
                        <div id="equipment-selection" style="min-height: 30px; overflow: visible;">
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
    // Convert datetime-local format to Y-m-d H:i format before submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const startTimeDisplay = document.getElementById('start_time');
        const endTimeDisplay = document.getElementById('end_time');
        const startTimeHidden = document.getElementById('start_time_hidden');
        const endTimeHidden = document.getElementById('end_time_hidden');

        if (startTimeDisplay.value) {
            // Convert from YYYY-MM-DDTHH:mm to YYYY-MM-DD HH:mm
            startTimeHidden.value = startTimeDisplay.value.replace('T', ' ');
        }

        if (endTimeDisplay.value) {
            // Convert from YYYY-MM-DDTHH:mm to YYYY-MM-DD HH:mm
            endTimeHidden.value = endTimeDisplay.value.replace('T', ' ');
        }
    });

    document.getElementById('laboratory_id').addEventListener('change', function() {
        const labId = this.value;
        const equipmentDiv = document.getElementById('equipment-selection');

        if (!labId) {
            equipmentDiv.innerHTML = '<p class="text-muted small">Select laboratory first to see available equipment.</p>';
            return;
        }

        // Fetch equipment for the selected laboratory
        fetch(`/bookings/laboratory/${labId}/equipment`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load equipment');
                return response.json();
            })
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    equipmentDiv.innerHTML = '<p class="text-muted small">No equipment available for this laboratory.</p>';
                } else {
                    const fragment = document.createDocumentFragment();
                    data.forEach((item) => {
                        // Validate equipment data
                        if (!item.equipment_id || !item.name || item.quantity === undefined) return;
                        
                        const div = document.createElement('div');
                        div.className = 'form-check mb-2';
                        
                        const checkbox = document.createElement('input');
                        checkbox.className = 'form-check-input';
                        checkbox.type = 'checkbox';
                        checkbox.name = 'equipment_ids[]';
                        checkbox.value = String(item.equipment_id);
                        checkbox.id = `eq_${item.equipment_id}`;
                        
                        const label = document.createElement('label');
                        label.className = 'form-check-label';
                        label.htmlFor = `eq_${item.equipment_id}`;
                        label.textContent = `${item.name} (Available: ${item.quantity})`;
                        
                        const quantityInput = document.createElement('input');
                        quantityInput.className = 'form-control form-control-sm mt-1';
                        quantityInput.type = 'number';
                        quantityInput.name = 'equipment_quantities[]';
                        quantityInput.value = '1';
                        quantityInput.min = '1';
                        quantityInput.max = String(item.quantity);
                        quantityInput.placeholder = 'Qty';
                        
                        div.appendChild(checkbox);
                        div.appendChild(label);
                        div.appendChild(quantityInput);
                        fragment.appendChild(div);
                    });
                    equipmentDiv.innerHTML = '';
                    equipmentDiv.appendChild(fragment);
                }
            })
            .catch(error => {
                console.error('Error loading equipment:', error);
                equipmentDiv.innerHTML = '<p class="text-danger small">Error loading equipment. Please try again.</p>';
            });
    });
</script>
@section('styles')
<style>
    .card {
        display: flex;
        flex-direction: column;
        height: auto !important;
    }
    
    .card-body {
        flex: 1;
        overflow: visible !important;
    }
    
    #equipment-selection {
        padding: 8px 0;
        min-height: 30px;
    }
    
    .form-check {
        padding: 8px 0;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 10px;
        align-items: center;
    }
    
    .form-check-input {
        margin-top: 0;
    }
    
    .form-check-label {
        margin-bottom: 0;
    }
    
    .form-check .form-control-sm {
        width: 80px;
        margin-top: 0 !important;
    }
</style>
@endsection
