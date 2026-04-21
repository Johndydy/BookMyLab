<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-users');
    }

    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|unique:roles|max:255',
            'description'     => 'nullable|string|max:1000',
            'permissions'     => 'array',
            'permissions.*'   => 'exists:permissions,permission_id',
        ]);

        try {
            $role = Role::create([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['permissions'])) {
                $role->permissions()->attach($validated['permissions']);
            }

            return redirect()->route('admin.roles.show', $role)
                ->with('success', "Role '{$role->name}' created successfully.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create role.');
        }
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'          => 'required|string|unique:roles,name,' . $role->role_id . ',role_id|max:255',
            'description'   => 'nullable|string|max:1000',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,permission_id',
        ]);

        try {
            $role->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);

            return redirect()->route('admin.roles.show', $role)
                ->with('success', "Role '{$role->name}' updated successfully.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update role.');
        }
    }

    public function destroy(Role $role)
    {
        try {
            if ($role->users()->count() > 0) {
                return back()->with('error', 'Cannot delete a role that has assigned users.');
            }

            $role->permissions()->detach();
            $role->delete();

            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$role->name}' deleted successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role.');
        }
    }

    public function attachPermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,permission_id',
        ]);

        $permission = Permission::find($validated['permission_id']);

        if ($role->permissions()->where('permission_id', $permission->permission_id)->exists()) {
            return back()->with('info', 'Role already has this permission.');
        }

        $role->permissions()->attach($permission->permission_id);
        return back()->with('success', "Permission '{$permission->name}' attached.");
    }

    public function detachPermission(Role $role, Permission $permission)
    {
        $role->permissions()->detach($permission->permission_id);
        return back()->with('success', "Permission '{$permission->name}' removed.");
    }
}