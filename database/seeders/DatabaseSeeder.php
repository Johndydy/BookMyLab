<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Laboratory;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            ['name' => 'create-booking',        'description' => 'Can create new bookings'],
            ['name' => 'view-booking',           'description' => 'Can view bookings'],
            ['name' => 'cancel-booking',         'description' => 'Can cancel bookings'],
            ['name' => 'approve-booking',        'description' => 'Can approve bookings'],
            ['name' => 'reject-booking',         'description' => 'Can reject bookings'],
            ['name' => 'manage-laboratory',      'description' => 'Can manage laboratories'],
            ['name' => 'view-laboratory',        'description' => 'Can view laboratories'],
            ['name' => 'manage-equipment',       'description' => 'Can manage equipment'],
            ['name' => 'view-equipment',         'description' => 'Can view equipment'],
            ['name' => 'manage-department',      'description' => 'Can manage departments'],
            ['name' => 'manage-users',           'description' => 'Can manage users'],
            ['name' => 'view-users',             'description' => 'Can view users'],
            ['name' => 'manage-maintenance',     'description' => 'Can manage maintenance logs'],
            ['name' => 'view-maintenance',       'description' => 'Can view maintenance logs'],
            ['name' => 'manage-equipment-logs',  'description' => 'Can manage equipment logs'],
            ['name' => 'view-equipment-logs',    'description' => 'Can view equipment logs'],
            ['name' => 'view-reports',           'description' => 'Can view system reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create roles
        $studentRole = Role::firstOrCreate(
            ['name' => 'student'],
            ['description' => 'Regular student user']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'administrator'],
            ['description' => 'System administrator']
        );

        // Assign permissions to student role
        $studentPermissions = Permission::whereIn('name', [
            'create-booking',
            'view-booking',
            'cancel-booking',
            'view-laboratory',
            'view-equipment',
        ])->pluck('permission_id')->toArray();

        if ($studentRole->permissions()->count() === 0) {
            $studentRole->permissions()->attach($studentPermissions);
        }

        // Assign all permissions to admin role
        $adminPermissions = Permission::pluck('permission_id')->toArray();
        if ($adminRole->permissions()->count() === 0) {
            $adminRole->permissions()->attach($adminPermissions);
        }

        // Create admin user
        // FIXED: uses first_name/last_name and school_id_number instead of name/role
        $admin = User::updateOrCreate(
            ['school_email' => 'admin@school.edu'],
            [
                'first_name'        => 'Admin',
                'last_name'         => 'User',
                'username'          => 'admin',
                'student_id_number' => 'ADMIN-001',
                'password'          => Hash::make('password'),
            ]
        );
        if (!$admin->hasRole('administrator')) {
            $admin->assignRole($adminRole);
        }



        // Create department
        $dept = Department::firstOrCreate(
            ['name' => 'Physics Department'],
            ['building' => 'Science Building A']
        );

        // Create laboratories
        $lab1 = Laboratory::firstOrCreate(
            ['department_id' => $dept->department_id, 'name' => 'Physics Lab 101'],
            [
                'location' => 'Room 101',
                'capacity' => 30,
                'status'   => 'available',
            ]
        );

        $lab2 = Laboratory::firstOrCreate(
            ['department_id' => $dept->department_id, 'name' => 'Physics Lab 102'],
            [
                'location' => 'Room 102',
                'capacity' => 25,
                'status'   => 'available',
            ]
        );

        // Create equipment
        Equipment::firstOrCreate(
            ['laboratory_id' => $lab1->laboratory_id, 'name' => 'Oscilloscope'],
            ['quantity' => 5, 'condition' => 'good']
        );

        Equipment::firstOrCreate(
            ['laboratory_id' => $lab1->laboratory_id, 'name' => 'Multimeter'],
            ['quantity' => 10, 'condition' => 'good']
        );

        Equipment::firstOrCreate(
            ['laboratory_id' => $lab2->laboratory_id, 'name' => 'Power Supply'],
            ['quantity' => 8, 'condition' => 'good']
        );
    }
}