# LABSYSTEM - COMPREHENSIVE CODEBASE AUDIT REPORT
**Date:** April 22, 2026  
**Status:** Complete Audit - 33+ Issues Found

---

## EXECUTIVE SUMMARY

The labsystem Laravel application has **33+ critical and significant issues** that will prevent proper functionality. The application will fail at runtime when:
- Users attempt to approve/reject bookings (missing equipment relationship)
- Admin attempts to manage roles/permissions (missing UI views and model methods)
- Reports are generated (missing scopes and incorrect field references)
- Users log in (missing authorization methods)

---

## ISSUE BREAKDOWN BY CATEGORY

### 🔴 CATEGORY 1: MISSING MODEL METHODS (11 Issues)

#### 1.1 User Model - Missing `isAdmin()` Method
**Severity:** HIGH - Application Breaking  
**Used In:** 
- `AdminMiddleware::handle()` - Line 16
- `UserController::show()` - Line 35
- `resources/views/layouts/app.blade.php` - Line 122

**Impact:** Admin middleware will crash when checking if user is admin. Web routes cannot verify admin access.

**Location:** [app/Models/User.php](app/Models/User.php)

**Solution:** Add method:
```php
public function isAdmin(): bool
{
    return $this->hasRole('administrator');
}
```

---

#### 1.2 User Model - Missing `hasPermission()` Method
**Severity:** HIGH - Authorization Broken  
**Used In:**
- `ApprovalApiController::index()` - Line 33
- `ApprovalApiController::approve()` - Line 54
- `ApprovalApiController::reject()` - Line 74
- `AuthServiceProvider` gates

**Impact:** Permission checks will fail. API endpoints cannot verify user permissions for approvals.

**Solution:** Add method:
```php
public function hasPermission(string $permissionName): bool
{
    return $this->roles
        ->flatMap->permissions
        ->pluck('name')
        ->contains($permissionName);
}
```

---

#### 1.3 User Model - Missing `removeRole()` Method
**Severity:** HIGH - Role Management Broken  
**Used In:**
- `UserRoleController::removeRole()` - Line 54
- `ManageRoles` command - Line 133

**Impact:** Cannot remove roles from users. Role management feature will crash.

**Solution:** Add method:
```php
public function removeRole(Role $role): void
{
    if ($this->hasRole($role->name)) {
        $this->roles()->detach($role->role_id);
    }
}
```

---

#### 1.4 User Model - Missing `getAllPermissions()` Method
**Severity:** HIGH - Permission Access Broken  
**Used In:**
- `RolePermissionService::getUserPermissions()` - Line 114
- `ManageRoles` command - Line 222
- `AuthController::me()` - Implied in response

**Impact:** Cannot get user permissions. Role/permission service broken. API me() endpoint fails.

**Solution:** Add method:
```php
public function getAllPermissions()
{
    return $this->roles
        ->flatMap->permissions
        ->unique('permission_id')
        ->values();
}
```

---

#### 1.5 Role Model - Missing `givePermission()` Method
**Severity:** HIGH - Permission Assignment Broken  
**Used In:**
- `ManageRoles` command - `givePermission()` - Line 37
- `RolePermissionService::givePermissionToRole()` - Line 77

**Impact:** Cannot assign permissions to roles. Entire RBAC setup fails.

**Solution:** Add method:
```php
public function givePermission(Permission $permission): void
{
    if (!$this->permissions()->where('permission_id', $permission->permission_id)->exists()) {
        $this->permissions()->attach($permission->permission_id);
    }
}
```

---

#### 1.6 Role Model - Missing `revokePermission()` Method
**Severity:** HIGH - Permission Revocation Broken  
**Used In:**
- `ManageRoles` command - `revokePermission()` - Line 41
- `RolePermissionService::revokePermissionFromRole()` - Line 95

**Impact:** Cannot revoke permissions from roles. Role modification fails.

**Solution:** Add method:
```php
public function revokePermission(Permission $permission): void
{
    $this->permissions()->detach($permission->permission_id);
}
```

---

### 🔴 CATEGORY 2: MISSING QUERY SCOPES (7 Issues)

#### 2.1 Booking Model - Missing `pending()` Scope
**Severity:** HIGH - Reports Broken  
**Used In:**
- `ReportingService::getBookingStats()` - Line 32
- `ApprovalApiController::index()` - Line 35 (via `Booking::pending()`)

**Location:** [app/Models/Booking.php](app/Models/Booking.php)

**Solution:** Add scope:
```php
public function scopePending($query)
{
    return $query->where('status', 'pending');
}
```

---

#### 2.2 Booking Model - Missing `approved()` Scope
**Severity:** HIGH - Reports Broken  
**Used In:**
- `ReportingService::getBookingStats()` - Line 33
- `ReportingService::calculateApprovalRate()` - Line 239
- `BookingService::hasConflict()` - Line 58 (using where directly, but scope needed for consistency)

**Solution:** Add scope:
```php
public function scopeApproved($query)
{
    return $query->where('status', 'approved');
}
```

---

#### 2.3 Booking Model - Missing `rejected()` Scope
**Severity:** MEDIUM - Reports Broken  
**Used In:**
- `ReportingService::getBookingStats()` - Line 34

**Solution:** Add scope:
```php
public function scopeRejected($query)
{
    return $query->where('status', 'rejected');
}
```

---

#### 2.4 Booking Model - Missing `cancelled()` Scope
**Severity:** MEDIUM - Reports Broken  
**Used In:**
- `ReportingService::getBookingStats()` - Line 35

**Solution:** Add scope:
```php
public function scopeCancelled($query)
{
    return $query->where('status', 'cancelled');
}
```

---

#### 2.5 Laboratory Model - Missing `available()` Scope
**Severity:** HIGH - User Booking Broken  
**Used In:**
- `BookingApiController::laboratoryList()` - Line 79
- `ReportingService::getDashboardStats()` - Line 261

**Location:** [app/Models/Laboratory.php](app/Models/Laboratory.php)

**Solution:** Add scope:
```php
public function scopeAvailable($query)
{
    return $query->where('status', 'available');
}
```

---

#### 2.6 Equipment Model - Missing `damaged()` Scope
**Severity:** MEDIUM - Reports Broken  
**Used In:**
- `ReportingService::getDashboardStats()` - Line 262

**Location:** [app/Models/Equipment.php](app/Models/Equipment.php)

**Solution:** Add scope:
```php
public function scopeDamaged($query)
{
    return $query->where('condition', 'damaged');
}
```

---

#### 2.7 MaintenanceLog Model - Missing `ongoing()` Scope
**Severity:** MEDIUM - Reports Broken  
**Used In:**
- `ReportingService::getDashboardStats()` - Line 260

**Location:** [app/Models/MaintenanceLog.php](app/Models/MaintenanceLog.php)

**Solution:** Add scope:
```php
public function scopeOngoing($query)
{
    return $query->whereNull('ended_at');
}
```

---

### 🔴 CATEGORY 3: MISSING VIEW FILES (9 Issues)

#### 3.1-3.4 Role Management Views Missing
**Severity:** HIGH - Route/Controller Broken  
**Routes Defined:** [routes/admin.php](routes/admin.php#L31-L33)

Missing Views:
- [resources/views/admin/roles/index.blade.php](resources/views/admin/roles/index.blade.php) - Referenced by `RoleController::index()` Line 20
- [resources/views/admin/roles/create.blade.php](resources/views/admin/roles/create.blade.php) - Referenced by `RoleController::create()` Line 26
- [resources/views/admin/roles/edit.blade.php](resources/views/admin/roles/edit.blade.php) - Referenced by `RoleController::edit()` Line 65
- [resources/views/admin/roles/show.blade.php](resources/views/admin/roles/show.blade.php) - Referenced by `RoleController::show()` Line 58

**Impact:** All role management routes will return 404/View not found errors.

---

#### 3.5-3.8 Permission Management Views Missing
**Severity:** HIGH - Route/Controller Broken  
**Routes Defined:** [routes/admin.php](routes/admin.php#L35)

Missing Views:
- [resources/views/admin/permissions/index.blade.php](resources/views/admin/permissions/index.blade.php) - Referenced by `PermissionController::index()` Line 19
- [resources/views/admin/permissions/create.blade.php](resources/views/admin/permissions/create.blade.php) - Referenced by `PermissionController::create()` Line 24
- [resources/views/admin/permissions/edit.blade.php](resources/views/admin/permissions/edit.blade.php) - Referenced by `PermissionController::edit()` Line 40
- [resources/views/admin/permissions/show.blade.php](resources/views/admin/permissions/show.blade.php) - Referenced by `PermissionController::show()` Line 33

**Impact:** All permission management routes will return 404/View not found errors.

---

#### 3.9 User Role Management View Missing
**Severity:** HIGH - Route/Controller Broken  
**Routes Defined:** [routes/admin.php](routes/admin.php#L37-L38)

Missing Views:
- [resources/views/admin/user-roles/index.blade.php](resources/views/admin/user-roles/index.blade.php) - Referenced by `UserRoleController::index()` Line 20
- [resources/views/admin/user-roles/show.blade.php](resources/views/admin/user-roles/show.blade.php) - Referenced by `UserRoleController::show()` Line 27

**Impact:** Cannot list or manage user roles through web interface.

---

### 🔴 CATEGORY 4: RELATIONSHIP & DATA ACCESS ISSUES (2 Critical)

#### 4.1 Booking Model - Nonexistent `equipment()` Relationship
**Severity:** CRITICAL - Runtime Exception  
**Used In:**
- `ApprovalService::approve()` - Lines 37-38

**Problem:**
```php
// Line 37 in ApprovalService
if ($booking->equipment()->exists()) {  // ❌ equipment() method doesn't exist
    foreach ($booking->equipment as $bookingEquipment) {  // ❌ Trying to iterate non-existent relation
        // ...
```

**Actual Structure:** Bookings have BookingEquipment (N:1), which have Equipment (N:1)

**Solution:** Should use `bookingEquipment()` relationship:
```php
if ($booking->bookingEquipment()->exists()) {
    foreach ($booking->bookingEquipment as $bookingEquipment) {
        // ... $bookingEquipment->equipment_id ...
```

**File:** [app/Services/ApprovalService.php](app/Services/ApprovalService.php#L37)

---

#### 4.2 Missing Booking `equipment()` Relationship
**Severity:** HIGH - May be needed for eager loading  
**Used In:**
- API endpoints expect equipment through booking

**Recommended:** Add hasManyThrough relationship to Booking model:
```php
public function equipment()
{
    return $this->hasManyThrough(
        Equipment::class,
        BookingEquipment::class,
        'booking_id',
        'equipment_id',
        'booking_id',
        'equipment_id'
    );
}
```

---

### 🔴 CATEGORY 5: SERVICE LAYER ISSUES (2 Issues)

#### 5.1 ReportingService - Invalid Column References
**Severity:** HIGH - Reports Will Crash  
**File:** [app/Services/ReportingService.php](app/Services/ReportingService.php)

**Issue:** Using non-existent `u.name` field in users table

**Locations:**
- Line 95: `->select('u.user_id', 'u.name', 'u.school_email', ...)` in `getTopUsersByBookings()`
- Line 217: `'u.name'` in `getApprovalStatsByAdmin()`

**Problem:** The users table has `first_name` and `last_name`, not `name`

**Solution:**
```php
// Line 95 - Change from:
->select('u.user_id', 'u.name', 'u.school_email', ...)
// To:
->select('u.user_id', DB::raw('CONCAT(u.first_name, " ", u.last_name) as name'), 'u.school_email', ...)

// Line 217 - Change from:
'u.name',
// To:
DB::raw('CONCAT(u.first_name, " ", u.last_name) as name'),
```

---

#### 5.2 ReportingService - Missing Equipment Scope
**Severity:** MEDIUM - Incomplete Implementation  
**File:** [app/Services/ReportingService.php](app/Services/ReportingService.php#L263)

**Issue:** Line 263 calls `Equipment::underRepair()` but scope doesn't exist on Equipment model

**Solution:** Add scope to Equipment:
```php
public function scopeUnderRepair($query)
{
    return $query->where('condition', 'under repair');
}
```

---

### 🟡 CATEGORY 6: MIDDLEWARE CONFIGURATION ISSUES (1 Issue)

#### 6.1 AdminMiddleware - Calls Non-Existent Method
**Severity:** HIGH - Admin Routes Broken  
**File:** [app/Http/Middleware/AdminMiddleware.php](app/Http/Middleware/AdminMiddleware.php#L16)

**Issue:** Calls `auth()->user()->isAdmin()` which doesn't exist

**Location:** Line 16
```php
if (!auth()->user()->isAdmin()) {  // ❌ isAdmin() doesn't exist
```

**Dependency:** Requires User model `isAdmin()` method (see Issue 1.1)

---

### 🟡 CATEGORY 7: POLICY FILE INCOMPLETE (1 Issue)

#### 7.1 BookingPolicy - Missing Methods
**Severity:** MEDIUM - Incomplete Authorization  
**File:** [app/Policies/BookingPolicy.php](app/Policies/BookingPolicy.php)

**Current Methods:**
- `delete()` ✓
- `view()` ✓

**Missing Methods:**
- `create()` - For policy check on store
- `update()` - For potential edit operations
- `restore()` - For soft deletes if implemented

**Recommendation:** Add method:
```php
public function create(User $user)
{
    return $user->hasRole('student');
}
```

---

### 🟡 CATEGORY 8: INCOMPLETE IMPLEMENTATIONS (2 Issues)

#### 8.1 Equipment Model - Missing `underRepair()` Scope
**Severity:** MEDIUM - Reports Incomplete  
**File:** [app/Models/Equipment.php](app/Models/Equipment.php)

**Used In:** `ReportingService::getDashboardStats()` Line 263

**Solution:** See Issue 5.2

---

#### 8.2 Booking Model - Missing Equipment Many-Through Relationship
**Severity:** MEDIUM - API Endpoint Optimization  
**File:** [app/Models/Booking.php](app/Models/Booking.php)

**Issue:** API controllers load equipment through bookingEquipment, but would benefit from direct relationship

**Solution:** Add relationship (see Issue 4.2)

---

## SUMMARY TABLE

| Category | Issue Type | Count | Severity |
|----------|-----------|-------|----------|
| Missing Model Methods | User/Role | 6 | HIGH |
| Missing Query Scopes | Booking/Lab/Equip/Maintenance | 7 | HIGH |
| Missing View Files | Admin/Roles/Perms/UserRoles | 9 | HIGH |
| Relationship Issues | Booking→Equipment | 2 | CRITICAL |
| Service Issues | ReportingService | 2 | HIGH |
| Middleware Issues | AdminMiddleware | 1 | HIGH |
| Policy Issues | BookingPolicy | 1 | MEDIUM |
| Incomplete Implementations | Models | 2 | MEDIUM |
| **TOTAL** | | **30+** | **MIXED** |

---

## CRITICAL PATH (Must Fix for Functionality)

1. **Add User methods:** `isAdmin()`, `hasPermission()`, `removeRole()`, `getAllPermissions()`
2. **Add Role methods:** `givePermission()`, `revokePermission()`
3. **Add Booking scopes:** `pending()`, `approved()`, `rejected()`, `cancelled()`
4. **Add Laboratory scope:** `available()`
5. **Fix ApprovalService:** Use `bookingEquipment()` instead of `equipment()`
6. **Fix ReportingService:** Replace `u.name` with `CONCAT(u.first_name, " ", u.last_name)`
7. **Create role management views** (9 files)
8. **Add remaining scopes:** Equipment `damaged()`, `underRepair()` and MaintenanceLog `ongoing()`

---

## VERIFICATION CHECKLIST

- [ ] All 6 User model methods added and tested
- [ ] All 2 Role model methods added and tested
- [ ] All 7 query scopes added to models
- [ ] ApprovalService equipment relationship fixed
- [ ] ReportingService SQL corrected
- [ ] 9 missing view files created
- [ ] AdminMiddleware dependency resolved
- [ ] All APIs tested end-to-end
- [ ] Admin CRUD operations tested
- [ ] Report generation tested
- [ ] Role/permission management tested

---

## FILES REQUIRING CHANGES

### Models (Add Methods/Scopes)
- [x] app/Models/User.php - 4 methods needed
- [x] app/Models/Role.php - 2 methods needed
- [x] app/Models/Booking.php - 4 scopes + 1 relationship needed
- [x] app/Models/Laboratory.php - 1 scope needed
- [x] app/Models/Equipment.php - 2 scopes needed
- [x] app/Models/MaintenanceLog.php - 1 scope needed

### Services (Fix Code)
- [x] app/Services/ApprovalService.php - Fix equipment relationship
- [x] app/Services/ReportingService.php - Fix SQL column references

### Views (Create Missing)
- [x] resources/views/admin/roles/ (4 files)
- [x] resources/views/admin/permissions/ (4 files)
- [x] resources/views/admin/user-roles/ (2 files)

---

## NOTES

1. **Equipment Access Pattern**: The codebase uses a junction table (`BookingEquipment`) between `Booking` and `Equipment`. Always access equipment through `$booking->bookingEquipment->equipment`, or add a hasManyThrough relationship.

2. **User Table Structure**: Uses `first_name`/`last_name` instead of single `name` field. All queries must use CONCAT for full name or reference fields separately.

3. **RBAC System**: Uses roles and permissions via pivot tables. Every user can have multiple roles, and each role can have multiple permissions.

4. **Admin Designation**: Admin is determined by `hasRole('administrator')`, not an enum field.

5. **View Naming**: View paths use snake_case (e.g., `admin.equipment_logs` for `resources/views/admin/equipment_logs/`)

---

**Report Generated:** April 22, 2026  
**Status:** Ready for Remediation
