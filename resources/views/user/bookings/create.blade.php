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
    .time-warning {
        display: none; align-items: center; gap: 6px;
        background: #fff3cd; border: 1px solid #ffc107; color: #856404;
        padding: 8px 12px; border-radius: 6px; font-size: 0.82rem;
        margin-top: 8px;
    }
    .time-warning.show { display: flex; }
    .time-warning i { font-size: 1rem; }
    .hours-info {
        background: #e8f4fd; border: 1px solid #bee5eb; color: #0c5460;
        padding: 10px 14px; border-radius: 6px; font-size: 0.85rem;
        margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
    }
    .hours-info i { font-size: 1.1rem; }
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
                <div class="hours-info">
                    <i class="bi bi-clock"></i>
                    <span><strong>Operating Hours:</strong> Mon–Fri: <strong>7:00 AM – 6:00 PM</strong> | Saturday: <strong>7:00 AM – 12:00 PM</strong> | Sunday: <strong>Closed</strong></span>
                </div>

                <form action="{{ route('user.bookings.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <div class="mb-3">
                        <label for="laboratory_id" class="form-label">Laboratory <span class="text-danger">*</span></label>
                        <select class="form-select @error('laboratory_id') is-invalid @enderror"
                                id="laboratory_id" name="laboratory_id" required>
                            <option value="">-- Select a Laboratory --</option>
                            @foreach($laboratories as $lab)
                                    <option value="{{ $lab->laboratory_id }}" data-capacity="{{ $lab->capacity }}"
                                        {{ old('laboratory_id') == $lab->laboratory_id ? 'selected' : '' }}>
                                        {{ $lab->name }} ({{ $lab->department->name }}) - Max: {{ $lab->capacity }}
                                    </option>
                            @endforeach
                        </select>
                        @error('laboratory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                        <select class="form-select @error('purpose') is-invalid @enderror"
                                id="purpose" name="purpose" required>
                            <option value="">-- Select Purpose --</option>
                            <option value="Study" {{ old('purpose') == 'Study' ? 'selected' : '' }}>Study</option>
                            <option value="Presentation" {{ old('purpose') == 'Presentation' ? 'selected' : '' }}>Presentation</option>
                            <option value="Defense" {{ old('purpose') == 'Defense' ? 'selected' : '' }}>Defense</option>
                            <option value="Research" {{ old('purpose') == 'Research' ? 'selected' : '' }}>Research</option>
                        </select>
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
                            <div class="time-warning" id="start_time_warning">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span id="start_time_warning_text"></span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Duration <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="duration_hours" min="1" max="11" value="1" required>
                                <span class="input-group-text">Hours</span>
                            </div>
                            <input type="hidden" id="end_time" name="end_time">
                            <small class="text-muted mt-1 d-block" id="calculated_end_time_display"></small>
                            @error('end_time')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="time-warning" id="end_time_warning">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span id="end_time_warning_text"></span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="number_of_persons" class="form-label">Capacity <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('number_of_persons') is-invalid @enderror" id="number_of_persons" name="number_of_persons" min="1" value="{{ old('number_of_persons', 1) }}" required>
                                <span class="input-group-text">Persons</span>
                            </div>
                            <small id="capacity_help" class="form-text text-muted"></small>
                            @error('number_of_persons')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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

                    <button type="submit" class="btn btn-primary" id="submitBtn">
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
                    <li class="mb-2"><strong>You can only book from the present time forward.</strong> Past dates and times are not selectable.</li>
                    <li class="mb-2"><strong>Mon–Fri:</strong> 7:00 AM – 6:00 PM</li>
                    <li class="mb-2"><strong>Saturday:</strong> 7:00 AM – 12:00 PM only</li>
                    <li class="mb-2"><strong>Sunday:</strong> Closed – no bookings allowed</li>
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
    // ── Set minimum date/time to RIGHT NOW ──
    function setMinDateTime() {
        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const hh = String(now.getHours()).padStart(2, '0');
        const min = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('start_time_display').min = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
    }
    setMinDateTime();
    // Refresh the min every minute so it stays current
    setInterval(setMinDateTime, 60000);

    // ── Validate time is within operating hours ──
    function validateStartTime() {
        const input = document.getElementById('start_time_display');
        const warning = document.getElementById('start_time_warning');
        const warningText = document.getElementById('start_time_warning_text');

        if (!input.value) {
            warning.classList.remove('show');
            return true;
        }

        const date = new Date(input.value);
        const hour = date.getHours();
        const dayOfWeek = date.getDay(); // 0=Sunday, 6=Saturday

        // Block Sunday
        if (dayOfWeek === 0) {
            warningText.textContent = 'Bookings are not available on Sundays. Please select another day.';
            warning.classList.add('show');
            return false;
        }

        // Saturday: 7 AM – 12 PM only
        if (dayOfWeek === 6) {
            if (hour < 7 || hour >= 12) {
                warningText.textContent = 'Saturday bookings are only available from 7:00 AM to 12:00 PM.';
                warning.classList.add('show');
                return false;
            }
        } else {
            // Weekdays: 7 AM – 6 PM
            if (hour < 7 || hour >= 18) {
                warningText.textContent = 'Start time must be between 7:00 AM and 6:00 PM. Please select a valid time.';
                warning.classList.add('show');
                return false;
            }
        }

        // Check if it's in the past
        if (date <= new Date()) {
            warningText.textContent = 'You cannot book a time in the past. Please select a future time.';
            warning.classList.add('show');
            return false;
        }

        warning.classList.remove('show');
        return true;
    }

    // ── Calculate end time and validate ──
    function calculateEndTime() {
        const startInput = document.getElementById('start_time_display').value;
        const hours = parseInt(document.getElementById('duration_hours').value) || 0;
        const endWarning = document.getElementById('end_time_warning');
        const endWarningText = document.getElementById('end_time_warning_text');

        if (startInput && hours > 0) {
            const startDate = new Date(startInput);
            const endDate = new Date(startDate);
            endDate.setHours(endDate.getHours() + hours);

            const endHour = endDate.getHours();
            const endDay = endDate.getDay(); // 0=Sunday, 6=Saturday

            // Validate end time based on day
            if (endDay === 0) {
                endWarningText.textContent = 'Booking cannot extend into Sunday. Please reduce the duration.';
                endWarning.classList.add('show');
            } else if (endDay === 6 && (endHour > 12 || (endHour === 12 && endDate.getMinutes() > 0))) {
                endWarningText.textContent = 'Saturday bookings must end by 12:00 PM. Please reduce the duration.';
                endWarning.classList.add('show');
            } else if (endDay !== 6 && (endHour > 18 || (endHour === 18 && endDate.getMinutes() > 0) || endHour < 7)) {
                endWarningText.textContent = 'End time exceeds 6:00 PM. Please reduce the duration.';
                endWarning.classList.add('show');
            } else {
                endWarning.classList.remove('show');
            }

            const yyyy = endDate.getFullYear();
            const mm = String(endDate.getMonth() + 1).padStart(2, '0');
            const dd = String(endDate.getDate()).padStart(2, '0');
            const hh = String(endDate.getHours()).padStart(2, '0');
            const min = String(endDate.getMinutes()).padStart(2, '0');

            document.getElementById('end_time').value = `${yyyy}-${mm}-${dd} ${hh}:${min}`;
            document.getElementById('calculated_end_time_display').textContent =
                `Ends on: ${endDate.toLocaleString([], {dateStyle: 'medium', timeStyle: 'short'})}`;
        } else {
            document.getElementById('end_time').value = '';
            document.getElementById('calculated_end_time_display').textContent = '';
            endWarning.classList.remove('show');
        }
    }

    document.getElementById('start_time_display').addEventListener('change', function() {
        validateStartTime();
        calculateEndTime();
    });
    document.getElementById('duration_hours').addEventListener('input', calculateEndTime);

    // ── Form submission: final validation ──
    document.getElementById('bookingForm').addEventListener('submit', function (e) {
        calculateEndTime();

        const start = document.getElementById('start_time_display').value;
        if (start) {
            document.getElementById('start_time').value = start.replace('T', ' ');
        }

        // Block submission if time is outside operating hours
        if (!validateStartTime()) {
            e.preventDefault();
            return false;
        }

        const endVal = document.getElementById('end_time').value;
        if (endVal) {
            const endParts = endVal.split(' ');
            const timeParts = endParts[1].split(':');
            const endHour = parseInt(timeParts[0]);
            if (endHour > 18 || (endHour === 18 && parseInt(timeParts[1]) > 0) || endHour < 7) {
                e.preventDefault();
                alert('Your booking end time is outside operating hours (7:00 AM – 6:00 PM). Please adjust the duration.');
                return false;
            }
        }
    });

    // ── Equipment loader ──
    document.getElementById('laboratory_id').addEventListener('change', function () {
        const labId = this.value;
        const div = document.getElementById('equipment-selection');
        const capacityInput = document.getElementById('number_of_persons');
        const capacityHelp = document.getElementById('capacity_help');

        if (this.options[this.selectedIndex] && this.options[this.selectedIndex].dataset.capacity) {
            const maxCap = this.options[this.selectedIndex].dataset.capacity;
            capacityInput.max = maxCap;
            capacityHelp.textContent = `Maximum capacity: ${maxCap} persons`;
        } else {
            capacityInput.removeAttribute('max');
            capacityHelp.textContent = '';
        }

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
                    qty.disabled = true; // Disabled by default so it doesn't submit

                    cb.addEventListener('change', function() {
                        qty.disabled = !this.checked;
                    });

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