# LABSYSTEM - Role-Based Access Control (RBAC) Implementation

## Overview

The LABSYSTEM has been enhanced with a complete Role-Based Access Control (RBAC) system that provides granular permission management. This system replaces the simple enum-based role system while maintaining backward compatibility.

---

## Database Schema

### New Tables Added

#### 1. **roles** Table
```sql
CREATE TABLE roles (
    role_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 2. **permissions** Table
```sql
CREATE TABLE permissions (
    permission_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 3. **user_roles** Table (Many-to-Many)
```sql
CREATE TABLE user_roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
);
```

#### 4. **role_permissions** Table (Many-to-Many)
```sql
CREATE TABLE role_permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    role_id BIGINT NOT NULL,
    permission_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE
);
```

---

## Models

### Role Model
```php
// Create role with permissions
$role = Role::create([
    'name' => 'editor',
    'description' => 'Can edit content'
]);

// Check if role has permission
$hasPermission = $role->hasPermission('create-booking');

// Give permission to role
$role->givePermission($permission);

// Get all permissions
$permissions = $role->permissions;

// Get all users with this role
$users = $role->users;
```

### Permission Model
```php
// Create permission
$permission = Permission::create([
    'name' => 'approve-booking',
    'description' => 'Can approve bookings'
]);

// Get all roles with this permission
$roles = $permission->roles;
```

### User Model (Enhanced)
```php
// Get all roles
$roles = $user->roles;

// Check if user has role
$hasRole = $user->hasRole('administrator');

// Check if user has permission
$hasPermission = $user->hasPermission('approve-booking');

// Assign role to user
$user->assignRole($role);

// Remove role from user
$user->removeRole($role);

// Get all permissions across all roles
$permissions = $user->getAllPermissions();

// Backward compatible - old enum still works
$isAdmin = $user->isAdmin(); // Checks role === 'admin'
```

---

## Implemented Permissions

### Booking Permissions
- `create-booking` - Can create new bookings
- `view-booking` - Can view bookings
- `cancel-booking` - Can cancel bookings
- `approve-booking` - Can approve bookings
- `reject-booking` - Can reject bookings

### Laboratory Management
- `manage-laboratory` - Can manage laboratories (CRUD)
- `view-laboratory` - Can view laboratories

### Equipment Management
- `manage-equipment` - Can manage equipment (CRUD)
- `view-equipment` - Can view equipment

### Department Management
- `manage-department` - Can manage departments

### User Management
- `manage-users` - Can manage users
- `view-users` - Can view users

### Maintenance
- `manage-maintenance` - Can manage maintenance logs
- `view-maintenance` - Can view maintenance logs

### Equipment Logs
- `manage-equipment-logs` - Can manage equipment logs
- `view-equipment-logs` - Can view equipment logs

### Reports
- `view-reports` - Can view system reports

---

## Default Roles & Permissions

### Student Role
**Permissions:**
- create-booking
- view-booking
- cancel-booking
- view-laboratory
- view-equipment

### Administrator Role
**Permissions:**
- All permissions

---

## Usage Examples

### 1. Check User Permission in Controller
```php
public function approve(Booking $booking)
{
    if (!auth()->user()->hasPermission('approve-booking')) {
        abort(403, 'Unauthorized');
    }
    
    // Approval logic
}
```

### 2. Using Middleware
```php
// In routes/web.php
Route::middleware('permission:approve-booking')->group(function () {
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve']);
});

// Or check role
Route::middleware('role:administrator')->group(function () {
    Route::prefix('admin')->group(function () {
        // Admin routes
    });
});
```

### 3. Using Gates
```php
// In controllers or views
if (Gate::allows('approve-bookings')) {
    // Show approve button
}

// In controller
$this->authorize('approve-bookings');

// In Blade template
@can('approve-bookings')
    <button>Approve Booking</button>
@endcan
```

### 4. Using RolePermissionService
```php
use App\Services\RolePermissionService;

public function __construct(protected RolePermissionService $rolePermissionService)
{
}

public function grantPermission(User $user)
{
    // Assign role to user
    $this->rolePermissionService->assignRoleToUser($user, 'administrator');
    
    // Check if user has permission
    $canApprove = $this->rolePermissionService->userHasPermission($user, 'approve-booking');
    
    // Get all user permissions
    $permissions = $this->rolePermissionService->getUserPermissions($user);
}
```

---

## Console Commands

### Manage Roles and Permissions

#### Create Role
```bash
php artisan roles:manage create-role --role="editor" 
# Or interactive
php artisan roles:manage create-role
```

#### Create Permission
```bash
php artisan roles:manage create-permission --permission="edit-content"
```

#### Assign Role to User
```bash
php artisan roles:manage assign-role --email="john@school.edu" --role="administrator"
```

#### Remove Role from User
```bash
php artisan roles:manage remove-role --email="john@school.edu" --role="student"
```

#### Give Permission to Role
```bash
php artisan roles:manage give-permission --role="editor" --permission="edit-content"
```

#### Revoke Permission from Role
```bash
php artisan roles:manage revoke-permission --role="editor" --permission="edit-content"
```

#### List All Roles
```bash
php artisan roles:manage list-roles
```

#### List All Permissions
```bash
php artisan roles:manage list-permissions
```

#### List User Permissions
```bash
php artisan roles:manage list-user-permissions --email="john@school.edu"
```

---

## Middleware

### CheckRole Middleware
Checks if authenticated user has a specific role.

```php
Route::middleware('role:administrator')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});
```

### CheckPermission Middleware
Checks if authenticated user has a specific permission.

```php
Route::middleware('permission:approve-booking')->group(function () {
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve']);
});
```

---

## Authorization Gates

The following gates are registered in `AuthServiceProvider`:

- `check-permission` - Dynamic permission gate
- `check-role` - Dynamic role gate
- `approve-bookings` - Can approve bookings
- `manage-labs` - Can manage laboratories
- `manage-equipment` - Can manage equipment
- `view-reports` - Can view reports

### Using Gates
```php
// In controller
Gate::authorize('approve-bookings');

// In Blade
@can('approve-bookings')
    <!-- Show content -->
@endcan
```

---

## Migration & Deployment

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will create:
- roles table
- permissions table
- user_roles table
- role_permissions table

### Step 2: Seed Initial Data
```bash
php artisan db:seed
```

This will create:
- 17 default permissions
- 2 default roles (student, administrator)
- 3 test users with assigned roles
- Sample departments, labs, and equipment

---

## Backward Compatibility

The system maintains backward compatibility with the existing enum-based role system:

```php
// Old way (still works)
if ($user->role === 'admin') {
    // ...
}

// Old way (still works)
$isAdmin = $user->isAdmin();

// New way
if ($user->hasRole('administrator')) {
    // ...
}

// New way
if ($user->hasPermission('approve-booking')) {
    // ...
}
```

---

## Best Practices

### 1. Use Permissions, Not Roles
Prefer checking permissions over roles:
```php
// Good
if ($user->hasPermission('approve-booking')) {
    // ...
}

// Less flexible
if ($user->hasRole('administrator')) {
    // ...
}
```

### 2. Define Permissions First
Create permissions before assigning them to roles.

### 3. Use Middleware for Routes
Protect routes using middleware for consistent authorization:
```php
Route::middleware('permission:approve-booking')->post('/bookings/{booking}/approve', [BookingController::class, 'approve']);
```

### 4. Use Gates in Views
Check permissions before displaying UI elements:
```php
@can('manage-equipment')
    <button class="btn btn-primary">Manage Equipment</button>
@endcan
```

### 5. Use Services for Complex Logic
Delegate role/permission management to `RolePermissionService`.

---

## Summary

The RBAC system provides:

✅ **Fine-grained permission control**  
✅ **Role-based grouping of permissions**  
✅ **User-role assignment flexibility**  
✅ **Middleware-based route protection**  
✅ **Gate-based authorization checks**  
✅ **Console commands for management**  
✅ **Full backward compatibility**  
✅ **Scalable permission architecture**

This implementation follows Laravel best practices and provides a solid foundation for managing complex authorization requirements as the system grows.
