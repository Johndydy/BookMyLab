<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EquipmentRequest;
use App\Models\Equipment;
use App\Models\Laboratory;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('laboratory');

        if ($request->filled('laboratory_id')) {
            $query->where('laboratory_id', $request->laboratory_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $equipment = $query->paginate(10);
        $laboratories = Laboratory::all();
        return view('admin.equipment.index', compact('equipment', 'laboratories'));
    }

    public function create()
    {
        $laboratories = Laboratory::all();
        return view('admin.equipment.create', compact('laboratories'));
    }

    public function store(EquipmentRequest $request)
    {
        try {
            Equipment::create($request->validated());
            return redirect()->route('admin.equipment.index')
                ->with('success', 'Equipment created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create equipment.');
        }
    }

    public function edit(Equipment $equipment)
    {
        $laboratories = Laboratory::all();
        return view('admin.equipment.edit', compact('equipment', 'laboratories'));
    }

    public function update(EquipmentRequest $request, Equipment $equipment)
    {
        try {
            $equipment->update($request->validated());
            return redirect()->route('admin.equipment.index')
                ->with('success', 'Equipment updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update equipment.');
        }
    }

    public function destroy(Equipment $equipment)
    {
        try {
            $equipment->delete();
            return redirect()->route('admin.equipment.index')
                ->with('success', 'Equipment deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete equipment.');
        }
    }
}
