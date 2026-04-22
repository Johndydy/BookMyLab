@extends('layouts.app')
@section('title', 'Book a Laboratory')

@section('styles')
<style>
    .form-check {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 10px;
        align-items: center;
        padding: 8px 0;
    }
    .form-check-input { margin-top: 0; }
    .form-check-label { margin-bottom: 0; }
    .form-check .form-control-sm { width: 80px; margin-top: 0 !important; }
</style>
@endsection

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
            <div class="card-header">New Booking Request</div>
            <div class="card-body">
                <form action="{{ route('user.bookings.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <div class="mb-3">
                        <label for="laboratory_id" class="form-label">Laboratory <span class="text-danger">*</span></label>
                        <select class="form-select @error('laboratory_id') is-invalid @enderror"
                                id="laboratory_id" name="laboratory_id" required>
                            <option value="">-- Select a Laboratory --</option>
                            @foreach($laboratories as $lab)
                                <option value="{{ $lab->laboratory_id }}"
                                    {{ old('laboratory_id') == $lab->laboratory_id ? 'selected' : '' }}>
                                    {{ $lab->name }} ({{ $lab->department->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('laboratory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror"
                                  id="purpose" name="purpose" rows="3" required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time_display" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local"
                                   class="form-control @error('start_time') is-invalid @enderror"
                                   id="start_time_display" required>
                            <input type="hidden" id="start_time" name="start_time">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time_display" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local"
                                   class="form-control @error('end_time') is-invalid @enderror"
                                   id="end_time_display" required>
                            <input type="hidden" id="end_time" name="end_time">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Equipment <span class="text-muted">(Optional)</span></label>
                        <div id="equipment-selection">
                            <p class="text-muted small">Select a laboratory first to see available equipment.</p>
                        </div>
                        @error('equipment_quantities')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Submit Booking Request
                    </button>
                    <a href="{{ route('user.bookings.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Important Notes</div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li class="mb-2">Select an available laboratory from the dropdown.</li>
                    <li class="mb-2">Choose a date and time in the future.</li>
                    <li class="mb-2">Clearly specify the purpose of your booking.</li>
                    <li class="mb-2">Optionally select equipment you'll need.</li>
                    <li class="mb-2">Your booking will be reviewed by an administrator.</li>
                    <li class="mb-2">You will be notified once it is approved or rejected.</li>
                    <li>You can only cancel <strong>pending</strong> bookings.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('bookingForm').addEventListener('submit', function () {
        const start = document.getElementById('start_time_display').value;
        const end = document.getElementById('end_time_display').value;
        if (start) document.getElementById('start_time').value = start.replace('T', ' ') + ':00';
        if (end) document.getElementById('end_time').value = end.replace('T', ' ') + ':00';
    });

    document.getElementById('laboratory_id').addEventListener('change', function () {
        const labId = this.value;
        const div = document.getElementById('equipment-selection');

        if (!labId) {
            div.innerHTML = '<p class="text-muted small">Select a laboratory first to see available equipment.</p>';
            return;
        }

        div.innerHTML = '<p class="text-muted small">Loading equipment...</p>';

        fetch(`/bookings/laboratory/${labId}/equipment`)
            .then(res => { if (!res.ok) throw new Error('Failed'); return res.json(); })
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    div.innerHTML = '<p class="text-muted small">No equipment available for this laboratory.</p>';
                    return;
                }
                const fragment = document.createDocumentFragment();
                data.forEach(item => {
                    if (!item.equipment_id || !item.name) return;
                    const wrapper = document.createElement('div');
                    wrapper.className = 'form-check mb-2';

                    const cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.className = 'form-check-input';
                    cb.name = 'equipment_ids[]';
                    cb.value = item.equipment_id;
                    cb.id = `eq_${item.equipment_id}`;

                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = `eq_${item.equipment_id}`;
                    label.textContent = `${item.name} (Available: ${item.quantity})`;

                    const qty = document.createElement('input');
                    qty.type = 'number';
                    qty.className = 'form-control form-control-sm';
                    qty.name = 'equipment_quantities[]';
                    qty.value = 1;
                    qty.min = 1;
                    qty.max = item.quantity;
                    qty.placeholder = 'Qty';

                    wrapper.appendChild(cb);
                    wrapper.appendChild(label);
                    wrapper.appendChild(qty);
                    fragment.appendChild(wrapper);
                });
                div.innerHTML = '';
                div.appendChild(fragment);
            })
            .catch(() => {
                div.innerHTML = '<p class="text-danger small">Error loading equipment. Please try again.</p>';
            });
    });
</script>
@endsection