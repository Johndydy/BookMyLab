<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'school_email' => 'admin@school.edu',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        // Create test users
        User::create([
            'name' => 'John Doe',
            'school_email' => 'john@school.edu',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        User::create([
            'name' => 'Jane Smith',
            'school_email' => 'jane@school.edu',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        // Create department
        $dept = Department::create([
            'name' => 'Physics Department',
            'building' => 'Science Building A'
        ]);

        // Create laboratories
        $lab1 = Laboratory::create([
            'department_id' => $dept->department_id,
            'name' => 'Physics Lab 101',
            'location' => 'Room 101',
            'capacity' => 30,
            'status' => 'available'
        ]);

        $lab2 = Laboratory::create([
            'department_id' => $dept->department_id,
            'name' => 'Physics Lab 102',
            'location' => 'Room 102',
            'capacity' => 25,
            'status' => 'available'
        ]);

        // Create equipment
        Equipment::create([
            'laboratory_id' => $lab1->laboratory_id,
            'name' => 'Oscilloscope',
            'quantity' => 5,
            'condition' => 'good'
        ]);

        Equipment::create([
            'laboratory_id' => $lab1->laboratory_id,
            'name' => 'Multimeter',
            'quantity' => 10,
            'condition' => 'good'
        ]);

        Equipment::create([
            'laboratory_id' => $lab2->laboratory_id,
            'name' => 'Power Supply',
            'quantity' => 8,
            'condition' => 'good'
        ]);
    }
}
