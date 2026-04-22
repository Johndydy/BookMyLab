@extends('layouts.admin')
@section('title', 'Manage Equipment')
@section('content')
<div class="row mb-4">
    <div class="col-md-8"><h2>Manage Equipment</h2></div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.equipment.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Equipment
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.equipment.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control"
                    placeholder="Search by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-5">
                <select name="laboratory_id" class="form-select">
                    <option value="">All Laboratories</option>
                    @foreach($laboratories as $lab)
                        <option value="{{ $lab->laboratory_id }}"
                            {{ request('laboratory_id') == $lab->laboratory_id ? 'selected' : '' }}>
                            {{ $lab->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Laboratory</th>
                <th>Quantity</th>
                <th>Condition</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipment as $item)
                <tr>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td>{{ $item->laboratory->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        @if($item->condition === 'good')
                            <span class="badge bg-success">Good</span>
                        @elseif($item->condition === 'damaged')
                            <span class="badge bg-danger">Damaged</span>
                        @else
                            <span class="badge bg-warning text-dark">Under Repair</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.equipment.edit', $item) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.equipment.destroy', $item) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this equipment?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No equipment found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $equipment->links() }}</div>
@endsection