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
        // Create permissions (skip if already exist)
        $permissions = [
            // Booking permissions
            ['name' => 'create-booking', 'description' => 'Can create new bookings'],
            ['name' => 'view-booking', 'description' => 'Can view bookings'],
            ['name' => 'cancel-booking', 'description' => 'Can cancel bookings'],
            
            // Approval permissions
            ['name' => 'approve-booking', 'description' => 'Can approve bookings'],
            ['name' => 'reject-booking', 'description' => 'Can reject bookings'],
            
            // Lab management permissions
            ['name' => 'manage-laboratory', 'description' => 'Can manage laboratories'],
            ['name' => 'view-laboratory', 'description' => 'Can view laboratories'],
            
            // Equipment management permissions
            ['name' => 'manage-equipment', 'description' => 'Can manage equipment'],
            ['name' => 'view-equipment', 'description' => 'Can view equipment'],
            
            // Department management permissions
            ['name' => 'manage-department', 'description' => 'Can manage departments'],
            
            // User management permissions
            ['name' => 'manage-users', 'description' => 'Can manage users'],
            ['name' => 'view-users', 'description' => 'Can view users'],
            
            // Maintenance permissions
            ['name' => 'manage-maintenance', 'description' => 'Can manage maintenance logs'],
            ['name' => 'view-maintenance', 'description' => 'Can view maintenance logs'],
            
            // Equipment log permissions
            ['name' => 'manage-equipment-logs', 'description' => 'Can manage equipment logs'],
            ['name' => 'view-equipment-logs', 'description' => 'Can view equipment logs'],
            
            // Report permissions
            ['name' => 'view-reports', 'description' => 'Can view system reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create roles (skip if already exist)
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

        // Assign permissions to admin role
        $adminPermissions = Permission::pluck('permission_id')->toArray();
        if ($adminRole->permissions()->count() === 0) {
            $adminRole->permissions()->attach($adminPermissions);
        }

        // Create admin user (skip if already exist)
        $admin = User::firstOrCreate(
            ['school_email' => 'admin@school.edu'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );
        if (!$admin->hasRole('administrator')) {
            $admin->assignRole($adminRole);
        }

        // Create test users (skip if already exist)
        $john = User::firstOrCreate(
            ['school_email' => 'john@school.edu'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ]
        );
        if (!$john->hasRole('student')) {
            $john->assignRole($studentRole);
        }

        $jane = User::firstOrCreate(
            ['school_email' => 'jane@school.edu'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password123'),
                'role' => 'user'
            ]
        );
        if (!$jane->hasRole('student')) {
            $jane->assignRole($studentRole);
        }

        // Create department (skip if already exist)
        $dept = Department::firstOrCreate(
            ['name' => 'Physics Department'],
            ['building' => 'Science Building A']
        );

        // Create laboratories (skip if already exist)
        $lab1 = Laboratory::firstOrCreate(
            ['department_id' => $dept->department_id, 'name' => 'Physics Lab 101'],
            [
                'location' => 'Room 101',
                'capacity' => 30,
                'status' => 'available'
            ]
        );

        $lab2 = Laboratory::firstOrCreate(
            ['department_id' => $dept->department_id, 'name' => 'Physics Lab 102'],
            [
                'location' => 'Room 102',
                'capacity' => 25,
                'status' => 'available'
            ]
        );

        // Create equipment (skip if already exist)
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
