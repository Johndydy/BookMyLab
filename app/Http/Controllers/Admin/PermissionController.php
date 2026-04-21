<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-department'); // Reuse for admin management
    }

    public function index()
    {
        $permissions = Permission::with('roles')->paginate(20);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $permission = Permission::create($validated);
            return redirect()->route('admin.permissions.show', $permission)
                ->with('success', "Permission '{$permission->name}' created successfully.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create permission: ' . $e->getMessage());
        }
    }

    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('admin.permissions.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->permission_id . ',permission_id|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $permission->update($validated);
            return redirect()->route('admin.permissions.show', $permission)
                ->with('success', "Permission '{$permission->name}' updated successfully.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update permission: ' . $e->getMessage());
        }
    }

    public function destroy(Permission $permission)
    {
        try {
            if ($permission->roles()->count() > 0) {
                return back()->with('error', 'Cannot delete permission assigned to roles.');
            }

            $permission->delete();
            return redirect()->route('admin.permissions.index')
                ->with('success', "Permission '{$permission->name}' deleted successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete permission: ' . $e->getMessage());
        }
    }
}
