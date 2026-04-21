<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Console\Command;

class ManageRoles extends Command
{
    protected $signature = 'roles:manage {action} {--role=} {--permission=} {--user=} {--email=}';
    protected $description = 'Manage roles and permissions - actions: create-role, create-permission, assign-role, remove-role, give-permission, revoke-permission, list-roles, list-permissions';

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'create-role':
                $this->createRole();
                break;

            case 'create-permission':
                $this->createPermission();
                break;

            case 'assign-role':
                $this->assignRole();
                break;

            case 'remove-role':
                $this->removeRole();
                break;

            case 'give-permission':
                $this->givePermission();
                break;

            case 'revoke-permission':
                $this->revokePermission();
                break;

            case 'list-roles':
                $this->listRoles();
                break;

            case 'list-permissions':
                $this->listPermissions();
                break;

            case 'list-user-permissions':
                $this->listUserPermissions();
                break;

            default:
                $this->error("Unknown action: {$action}");
        }
    }

    protected function createRole()
    {
        $name = $this->option('role') ?? $this->ask('Role name');
        $description = $this->ask('Role description (optional)', null);

        Role::create([
            'name' => $name,
            'description' => $description,
        ]);

        $this->info("Role '{$name}' created successfully.");
    }

    protected function createPermission()
    {
        $name = $this->option('permission') ?? $this->ask('Permission name');
        $description = $this->ask('Permission description (optional)', null);

        Permission::create([
            'name' => $name,
            'description' => $description,
        ]);

        $this->info("Permission '{$name}' created successfully.");
    }

    protected function assignRole()
    {
        $email = $this->option('email') ?? $this->ask('User email');
        $roleName = $this->option('role') ?? $this->ask('Role name');

        $user = User::where('school_email', $email)->first();
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return;
        }

        $user->assignRole($role);
        $this->info("Role '{$roleName}' assigned to user '{$email}'.");
    }

    protected function removeRole()
    {
        $email = $this->option('email') ?? $this->ask('User email');
        $roleName = $this->option('role') ?? $this->ask('Role name');

        $user = User::where('school_email', $email)->first();
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return;
        }

        $user->removeRole($role);
        $this->info("Role '{$roleName}' removed from user '{$email}'.");
    }

    protected function givePermission()
    {
        $roleName = $this->option('role') ?? $this->ask('Role name');
        $permissionName = $this->option('permission') ?? $this->ask('Permission name');

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return;
        }

        $permission = Permission::where('name', $permissionName)->first();
        if (!$permission) {
            $this->error("Permission '{$permissionName}' not found.");
            return;
        }

        $role->givePermission($permission);
        $this->info("Permission '{$permissionName}' given to role '{$roleName}'.");
    }

    protected function revokePermission()
    {
        $roleName = $this->option('role') ?? $this->ask('Role name');
        $permissionName = $this->option('permission') ?? $this->ask('Permission name');

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return;
        }

        $permission = Permission::where('name', $permissionName)->first();
        if (!$permission) {
            $this->error("Permission '{$permissionName}' not found.");
            return;
        }

        $role->revokePermission($permission);
        $this->info("Permission '{$permissionName}' revoked from role '{$roleName}'.");
    }

    protected function listRoles()
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            $this->info('No roles found.');
            return;
        }

        $this->table(['ID', 'Name', 'Description', 'Permissions'], 
            $roles->map(function ($role) {
                return [
                    $role->role_id,
                    $role->name,
                    $role->description,
                    $role->permissions->count(),
                ];
            })->toArray()
        );
    }

    protected function listPermissions()
    {
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            $this->info('No permissions found.');
            return;
        }

        $this->table(['ID', 'Name', 'Description'], 
            $permissions->map(function ($permission) {
                return [
                    $permission->permission_id,
                    $permission->name,
                    $permission->description,
                ];
            })->toArray()
        );
    }

    protected function listUserPermissions()
    {
        $email = $this->option('email') ?? $this->ask('User email');
        
        $user = User::where('school_email', $email)->first();
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        $permissions = $user->getAllPermissions();

        if ($permissions->isEmpty()) {
            $this->info("User '{$email}' has no permissions.");
            return;
        }

        $this->table(['Permission Name', 'Description'], 
            $permissions->map(function ($permission) {
                return [
                    $permission->name,
                    $permission->description,
                ];
            })->toArray()
        );
    }
}
