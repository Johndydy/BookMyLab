<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Exception;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    /**
     * Create a new role with permissions
     */
    public function createRole(string $name, string $description = null, array $permissionNames = []): Role
    {
        try {
            return DB::transaction(function () use ($name, $description, $permissionNames) {
                $role = Role::create([
                    'name' => $name,
                    'description' => $description,
                ]);

                if (!empty($permissionNames)) {
                    $permissions = Permission::whereIn('name', $permissionNames)->get();
                    $role->permissions()->attach($permissions);
                }

                return $role;
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a new permission
     */
    public function createPermission(string $name, string $description = null): Permission
    {
        try {
            return Permission::create([
                'name' => $name,
                'description' => $description,
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser($user, $roleNameOrId): void
    {
        try {
            $role = is_numeric($roleNameOrId)
                ? Role::find($roleNameOrId)
                : Role::where('name', $roleNameOrId)->firstOrFail();

            $user->assignRole($role);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Revoke role from user
     */
    public function revokeRoleFromUser($user, $roleNameOrId): void
    {
        try {
            $role = is_numeric($roleNameOrId)
                ? Role::find($roleNameOrId)
                : Role::where('name', $roleNameOrId)->firstOrFail();

            $user->removeRole($role);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Give permission to role
     */
    public function givePermissionToRole(Role $role, $permissionNameOrId): void
    {
        try {
            $permission = is_numeric($permissionNameOrId)
                ? Permission::find($permissionNameOrId)
                : Permission::where('name', $permissionNameOrId)->firstOrFail();

            $role->givePermission($permission);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Revoke permission from role
     */
    public function revokePermissionFromRole(Role $role, $permissionNameOrId): void
    {
        try {
            $permission = is_numeric($permissionNameOrId)
                ? Permission::find($permissionNameOrId)
                : Permission::where('name', $permissionNameOrId)->firstOrFail();

            $role->revokePermission($permission);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all permissions for a user
     */
    public function getUserPermissions($user)
    {
        return $user->getAllPermissions();
    }

    /**
     * Check if user has permission
     */
    public function userHasPermission($user, string $permissionName): bool
    {
        return $user->hasPermission($permissionName);
    }

    /**
     * Check if user has role
     */
    public function userHasRole($user, string $roleName): bool
    {
        return $user->hasRole($roleName);
    }
}
