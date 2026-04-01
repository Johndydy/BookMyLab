<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LaboratoryRequest;
use App\Models\Department;
use App\Models\Laboratory;
use App\Services\LaboratoryService;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    protected $laboratoryService;

    public function __construct(LaboratoryService $laboratoryService)
    {
        $this->laboratoryService = $laboratoryService;
    }

    public function index(Request $request)
    {
        $query = Laboratory::with('department');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $laboratories = $query->paginate(10);
        return view('admin.laboratories.index', compact('laboratories'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.laboratories.create', compact('departments'));
    }

    public function store(LaboratoryRequest $request)
    {
        try {
            Laboratory::create($request->validated());
            return redirect()->route('admin.laboratories.index')
                ->with('success', 'Laboratory created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create laboratory.');
        }
    }

    public function edit(Laboratory $laboratory)
    {
        $departments = Department::all();
        return view('admin.laboratories.edit', compact('laboratory', 'departments'));
    }

    public function update(LaboratoryRequest $request, Laboratory $laboratory)
    {
        try {
            $laboratory->update($request->validated());
            return redirect()->route('admin.laboratories.index')
                ->with('success', 'Laboratory updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update laboratory.');
        }
    }

    public function destroy(Laboratory $laboratory)
    {
        try {
            $laboratory->delete();
            return redirect()->route('admin.laboratories.index')
                ->with('success', 'Laboratory deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete laboratory.');
        }
    }
}
