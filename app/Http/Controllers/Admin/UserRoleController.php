<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage-users');
    }

    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('admin.user-roles.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('roles');
        $availableRoles = Role::whereNotIn('role_id', $user->roles->pluck('role_id'))->get();
        return view('admin.user-roles.show', compact('user', 'availableRoles'));
    }

    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,role_id',
        ]);

        try {
            $role = Role::find($validated['role_id']);

            if ($user->hasRole($role->name)) {
                return back()->with('info', "User already has the '{$role->name}' role.");
            }

            $user->assignRole($role);
            return back()->with('success', "Role '{$role->name}' assigned to {$user->full_name}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign role: ' . $e->getMessage());
        }
    }

    public function removeRole(User $user, Role $role)
    {
        try {
            if (!$user->hasRole($role->name)) {
                return back()->with('info', "User does not have the '{$role->name}' role.");
            }

            $user->removeRole($role);
            return back()->with('success', "Role '{$role->name}' removed from {$user->full_name}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove role: ' . $e->getMessage());
        }
    }
}