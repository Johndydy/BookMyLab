<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MaintenanceLogController;
use App\Http\Controllers\Admin\EquipmentLogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/bookings', BookingController::class)->only(['index']);
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');

    Route::resource('/laboratories', LaboratoryController::class);
    Route::resource('/equipment', EquipmentController::class);
    Route::resource('/departments', DepartmentController::class);

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Maintenance Logs Routes
    Route::get('/maintenance-logs', [MaintenanceLogController::class, 'index'])->name('maintenance_logs.index');
    Route::post('/maintenance-logs', [MaintenanceLogController::class, 'store'])->name('maintenance_logs.store');
    Route::put('/maintenance-logs/{log}/end', [MaintenanceLogController::class, 'update'])->name('maintenance_logs.update');

    // Equipment Logs Routes
    Route::get('/equipment-logs', [EquipmentLogController::class, 'index'])->name('equipment_logs.index');
    Route::put('/equipment-logs/{log}/return', [EquipmentLogController::class, 'update'])->name('equipment_logs.update');

    // RBAC Management Routes
    Route::resource('/roles', RoleController::class);
    Route::post('/roles/{role}/attach-permission', [RoleController::class, 'attachPermission'])->name('roles.attach-permission');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'detachPermission'])->name('roles.detach-permission');

    Route::resource('/permissions', PermissionController::class);

    Route::get('/user-roles', [UserRoleController::class, 'index'])->name('user-roles.index');
    Route::get('/user-roles/{user}', [UserRoleController::class, 'show'])->name('user-roles.show');
    Route::post('/user-roles/{user}/assign', [UserRoleController::class, 'assignRole'])->name('user-roles.assign');
    Route::delete('/user-roles/{user}/roles/{role}', [UserRoleController::class, 'removeRole'])->name('user-roles.remove');
});
