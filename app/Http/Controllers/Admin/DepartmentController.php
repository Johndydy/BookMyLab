<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::withCount('laboratories');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $departments = $query->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(DepartmentRequest $request)
    {
        try {
            Department::create($request->validated());
            return redirect()->route('admin.departments.index')
                ->with('success', 'Department created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create department.');
        }
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        try {
            $department->update($request->validated());
            return redirect()->route('admin.departments.index')
                ->with('success', 'Department updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update department.');
        }
    }

    public function destroy(Department $department)
    {
        try {
            $department->delete();
            return redirect()->route('admin.departments.index')
                ->with('success', 'Department deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete department.');
        }
    }
}
