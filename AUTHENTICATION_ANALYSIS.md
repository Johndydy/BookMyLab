# Laboratory Booking System - Complete Authentication & Architecture Analysis

**Analysis Date**: April 22, 2026  
**Framework**: Laravel 8 with Sanctum  
**User Issue**: Getting identical login response output regardless of context  

---

## EXECUTIVE SUMMARY

The laboratory booking system has a **critical authentication design flaw**: both the web and API login endpoints return **identical JSON responses**, making it impossible to differentiate between user types, authentication methods, or access permissions from the response alone.

### Key Problems:
1. ✗ **Duplicate Login Endpoints** - Both return the same JSON response
2. ✗ **No Response Differentiation** - Can't tell if user is student/admin from response
3. ✗ **Inconsistent Flow** - Web login returns JSON instead of redirecting
4. ✗ **Single Active Token** - All old tokens revoked on new login
5. ✗ **Role Information Incomplete** - Response doesn't include permission details

---

## 1. AUTHENTICATION/LOGIN FLOW ANALYSIS

### 1.1 Current Login Endpoints

#### A. Web Route Login
**File**: [routes/web.php](routes/web.php#L9)
```
POST /login → AuthController::login()
```
- Uses session-based authentication
- Returns JSON response (should redirect to dashboard)
- Rate limited to 5 attempts/60 seconds

#### B. API Route Login  
**File**: [routes/api.php](routes/api.php#L5)
```
POST /api/login → AuthController::login()
```
- Uses Sanctum token-based authentication
- Returns JSON response (correct for API)
- **Same controller method as web route**

### 1.2 Login Controller Method
**File**: [app/Http/Controllers/Auth/AuthController.php](app/Http/Controllers/Auth/AuthController.php#L65-L113)

```php
public function login(Request $request)
{
    // Validation
    $request->validate([
        'school_email' => 'required|email',
        'password'     => 'required|string',
    ]);

    // Rate limiting (5 attempts per 60 seconds)
    $key = 'login-attempt:' . $request->ip();
    if (RateLimiter::tooManyAttempts($key, 5)) {
        return response()->json([
            'message' => "Too many login attempts. Try again in {$seconds} seconds.",
        ], 429);
    }

    // Find user
    $user = User::where('school_email', $request->school_email)->first();

    // Validate password
    if (!$user || !Hash::check($request->password, $user->password)) {
        RateLimiter::hit($key, 60);
        return response()->json([
            'message' => 'Invalid credentials.',
        ], 401);
    }

    // Clear rate limiter
    RateLimiter::clear($key);

    // *** CRITICAL: Revoke ALL old tokens ***
    $user->tokens()->delete();

    // Create new token
    $token = $user->createToken('auth_token')->plainTextToken;

    // *** ISSUE: Same response for both web and API ***
    return response()->json([
        'message' => 'Logged in successfully.',
        'token'   => $token,
        'user'    => [
            'user_id'          => $user->user_id,
            'full_name'        => $user->full_name,
            'school_email'     => $user->school_email,
            'school_id_number' => $user->school_id_number,
            'roles'            => $user->roles->pluck('name'),
        ],
    ]);
}
```

### 1.3 Login Response Structure

**Current Response** (IDENTICAL for all users):
```json
{
  "message": "Logged in successfully.",
  "token": "1|3aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890",
  "user": {
    "user_id": 1,
    "full_name": "Admin User",
    "school_email": "admin@school.edu",
    "school_id_number": "ADMIN-001",
    "roles": ["administrator"]
  }
}
```

**Problems with this response**:
- No HTTP status code explicit (defaults to 200)
- Includes token in every response (should only be in API context)
- Roles array doesn't include permissions
- No redirect URL for web clients
- No indication of available endpoints
- No access level categorization

---

## 2. USER MODEL & ROLES ANALYSIS

### 2.1 User Model Structure
**File**: [app/Models/User.php](app/Models/User.php)

**Primary Key**: `user_id` (custom, not `id`)

**Fillable Fields**:
```php
protected $fillable = [
    'first_name',
    'last_name',
    'school_email',
    'school_id_number',
    'password',
];
```

**Hidden Fields**:
```php
protected $hidden = ['password'];
```

### 2.2 Role System
**File**: [app/Models/Role.php](app/Models/Role.php)

**Relationships**:
```
User → Roles (Many-to-Many via user_roles)
Role → Permissions (Many-to-Many via role_permissions)
```

**Role Helper Methods** (User.php):
```php
public function hasRole(string $roleName): bool
public function assignRole(Role $role): void
public function removeRole(Role $role): void
public function isAdmin(): bool  // Checks for 'administrator' role
public function hasPermission(string $permissionName): bool
public function getAllPermissions()
```

### 2.3 Available Roles & Permissions

**Roles**:
- `student` - Regular student user
- `administrator` - System administrator

**Permissions** (17 total):
| Permission | Description | Assigned To |
|---|---|---|
| create-booking | Create new bookings | student, admin |
| view-booking | View bookings | student, admin |
| cancel-booking | Cancel bookings | student, admin |
| approve-booking | Approve bookings | admin only |
| reject-booking | Reject bookings | admin only |
| manage-laboratory | Manage labs | admin only |
| view-laboratory | View labs | student, admin |
| manage-equipment | Manage equipment | admin only |
| view-equipment | View equipment | student, admin |
| manage-department | Manage departments | admin only |
| manage-users | Manage users | admin only |
| view-users | View users | admin only |
| manage-maintenance | Manage maintenance logs | admin only |
| view-maintenance | View maintenance logs | admin only |
| manage-equipment-logs | Manage equipment logs | admin only |
| view-equipment-logs | View equipment logs | admin only |
| view-reports | View system reports | admin only |

### 2.4 Test Users (From DatabaseSeeder)
**File**: [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php#L60-L102)

| Email | Password | Role | Full Name | ID |
|---|---|---|---|---|
| admin@school.edu | password123 | administrator | Admin User | ADMIN-001 |
| john@school.edu | password123 | student | John Doe | STU-001 |
| jane@school.edu | password123 | student | Jane Smith | STU-002 |

---

## 3. API RESPONSES & TOKEN GENERATION ANALYSIS

### 3.1 Token Generation Method
**Uses**: Laravel Sanctum

**When Generated**:
```php
$token = $user->createToken('auth_token')->plainTextToken;
```

**Token Format**: `{id}|{hash}` (e.g., `1|3aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890`)

**Token Storage**:
- Stored in `personal_access_tokens` table
- Hash stored in database
- Plain text returned to client
- Client must save for future API requests

### 3.2 Token Usage (Protected Routes)

**API Routes Requiring Token** (routes/api.php):
```
All routes in auth:sanctum middleware group require Bearer token
```

**Authorization Header Format**:
```
Authorization: Bearer 1|3aBcDeFgHiJkLmNoPqRsTuVwXyZ1234567890
```

### 3.3 CRITICAL ISSUE: Token Revocation Logic

**Current Behavior**:
```php
$user->tokens()->delete();  // Deletes ALL tokens for user
$token = $user->createToken('auth_token')->plainTextToken;
```

**Problems**:
- ✗ Only one active token per user allowed
- ✗ Cannot support multi-device sessions
- ✗ Logging in on one device logs out all other devices
- ✗ No refresh token mechanism

**Should Be**:
- Keep existing tokens active
- Issue new token without deleting old ones
- OR implement refresh token strategy
- OR set token expiration time

### 3.4 Login Response for Different Scenarios

**Scenario 1: Student Login**
```json
{
  "message": "Logged in successfully.",
  "token": "1|...",
  "user": {
    "user_id": 2,
    "full_name": "John Doe",
    "school_email": "john@school.edu",
    "school_id_number": "STU-001",
    "roles": ["student"]
  }
}
```
**Result**: Response is identical to admin login except roles array

**Scenario 2: Admin Login**
```json
{
  "message": "Logged in successfully.",
  "token": "1|...",
  "user": {
    "user_id": 1,
    "full_name": "Admin User",
    "school_email": "admin@school.edu",
    "school_id_number": "ADMIN-001",
    "roles": ["administrator"]
  }
}
```
**Result**: Only difference is roles array - no other differentiation

**Scenario 3: Rate Limited Login**
```json
{
  "message": "Too many login attempts. Try again in 45 seconds."
}
```
**HTTP Status**: 429 Too Many Requests

**Scenario 4: Invalid Credentials**
```json
{
  "message": "Invalid credentials."
}
```
**HTTP Status**: 401 Unauthorized

---

## 4. DATABASE SEEDERS ANALYSIS

### 4.1 Permission Seeding
**File**: [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php#L15-L43)

Creates 17 permissions using `firstOrCreate()`:
```php
Permission::firstOrCreate(['name' => 'create-booking'], [
    'description' => 'Can create new bookings'
]);
```

**Idempotency**: Won't create duplicates if run multiple times

### 4.2 Role Seeding
**File**: [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php#L45-L71)

**Student Role** - 5 permissions:
- create-booking
- view-booking
- cancel-booking
- view-laboratory
- view-equipment

**Administrator Role** - 17 permissions (ALL):
- All permissions listed above

### 4.3 User Seeding
**File**: [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php#L73-L102)

Creates 3 users:
```php
$admin = User::firstOrCreate(['school_email' => 'admin@school.edu'], [
    'first_name'       => 'Admin',
    'last_name'        => 'User',
    'school_id_number' => 'ADMIN-001',
    'password'         => Hash::make('password123'),
]);
if (!$admin->hasRole('administrator')) {
    $admin->assignRole($adminRole);
}
```

**Key Features**:
- ✓ Uses `firstOrCreate()` (idempotent)
- ✓ Properly hashes passwords
- ✓ Assigns roles after creation
- ✓ Checks if role already assigned (prevents duplicates)

### 4.4 Test Data Creation

**Departments**:
- Physics Department (Science Building A)

**Laboratories**:
- Physics Lab 101 (Room 101, 30 capacity)
- Physics Lab 102 (Room 102, 25 capacity)

**Equipment**:
- Oscilloscope (Lab 101, qty: 5)
- Multimeter (Lab 101, qty: 10)

---

## 5. KEY CONTROLLERS ANALYSIS

### 5.1 Authentication Controller
**File**: [app/Http/Controllers/Auth/AuthController.php](app/Http/Controllers/Auth/AuthController.php)

**Methods**:
```php
public function showLogin()         // GET /login
public function showRegister()      // GET /register
public function register()          // POST /register
public function login()             // POST /login & POST /api/login
public function logout()            // POST /logout & POST /api/logout
public function me()               // GET /api/me (current user)
```

**Issues**:
- Login method is NOT differentiated for web vs API
- Returns JSON for both contexts
- No redirect handling for web route

### 5.2 User Dashboard Controller
**File**: [app/Http/Controllers/User/DashboardController.php](app/Http/Controllers/User/DashboardController.php)

```php
public function index()
{
    $user = auth()->user();
    
    // Get approved bookings
    $approvedBookings = Booking::where('user_id', $user->user_id)
        ->where('status', 'approved')
        ->where('start_time', '>=', now())
        ->orderBy('start_time')
        ->get();
    
    // Get pending bookings
    $pendingBookings = Booking::where('user_id', $user->user_id)
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('user.dashboard', compact('approvedBookings', 'pendingBookings'));
}
```

**Works for**: Session-authenticated users only

### 5.3 Admin User Controller
**File**: [app/Http/Controllers/Admin/UserController.php](app/Http/Controllers/Admin/UserController.php)

**Features**:
- ✓ Filters to show only students (excludes admins)
- ✓ Search functionality (name, email, ID)
- ✓ Pagination (10 per page)
- ✓ Shows booking count per user
- ✓ Can view individual user details and their bookings

**Authorization**: Requires `admin` middleware (checks `isAdmin()`)

### 5.4 API Controllers

#### BookingApiController
**File**: [app/Http/Controllers/Api/BookingApiController.php](app/Http/Controllers/Api/BookingApiController.php)

**Routes Protected By**: `role:student` middleware

**Endpoints**:
- `GET /api/bookings` - List user's bookings
- `POST /api/bookings` - Create booking
- `GET /api/bookings/{booking}` - Get booking details
- `DELETE /api/bookings/{booking}` - Cancel booking
- `GET /api/laboratories` - List available labs
- `GET /api/laboratories/{lab}/equipment` - Get lab equipment
- `POST /api/laboratories/{lab}/check-availability` - Check availability

#### ApprovalApiController
**File**: [app/Http/Controllers/Api/ApprovalApiController.php](app/Http/Controllers/Api/ApprovalApiController.php)

**Routes Protected By**: `role:administrator` middleware

**Endpoints**:
- `GET /api/admin/approvals` - List pending bookings
- `GET /api/admin/approvals/history` - Get approval history
- `POST /api/admin/approvals/{booking}/approve` - Approve booking
- `POST /api/admin/approvals/{booking}/reject` - Reject booking

---

## 6. ROUTES ANALYSIS

### 6.1 Web Routes
**File**: [routes/web.php](routes/web.php)

```
GET  /                          → Redirect to /login
GET  /login                     → Show login form
POST /login                     → Login (returns JSON - ISSUE!)
GET  /register                  → Show register form
POST /register                  → Register (returns JSON)
POST /logout                    → Logout (requires auth)

GET  /dashboard                 → Show user dashboard
GET  /bookings                  → List bookings
POST /bookings                  → Create booking
DELETE /bookings/{booking}      → Cancel booking
GET  /notifications             → List notifications
POST /notifications/read        → Mark all as read
```

**Authentication**: Uses `auth` middleware (session-based)

### 6.2 API Routes
**File**: [routes/api.php](routes/api.php)

**Public Routes**:
```
POST /api/login                 → Login
POST /api/register              → Register
```

**Protected Routes** (require `auth:sanctum`):
```
POST /api/logout                → Logout
GET  /api/me                    → Get current user

[Student Routes]
GET  /api/bookings              → List bookings
POST /api/bookings              → Create booking
GET  /api/bookings/{booking}    → Get booking
DELETE /api/bookings/{booking}  → Cancel booking
GET  /api/laboratories          → List labs
GET  /api/laboratories/{id}/equipment → Get equipment
POST /api/laboratories/{id}/check-availability → Check availability

[Admin Routes] (prefix: /api/admin)
GET  /admin/approvals           → List pending
GET  /admin/approvals/history   → Get history
POST /admin/approvals/{id}/approve → Approve
POST /admin/approvals/{id}/reject  → Reject
```

**Authorization**: 
- Students: `role:student`
- Admins: `role:administrator`

### 6.3 Admin Routes
**File**: [routes/admin.php](routes/admin.php)

**Prefix**: `/admin`  
**Middleware**: `auth` (session-based) and `admin` (checks `isAdmin()`)

**Routes**:
```
GET    /dashboard               → Admin dashboard
GET    /bookings                → List all bookings
POST   /bookings/{id}/approve   → Approve booking
POST   /bookings/{id}/reject    → Reject booking
CRUD   /laboratories            → Laboratory management
CRUD   /equipment               → Equipment management
CRUD   /departments             → Department management
GET    /users                   → List users (students only)
GET    /users/{id}              → View user details
GET    /reports                 → View reports
CRUD   /maintenance-logs        → Maintenance management
CRUD   /equipment-logs          → Equipment log management
CRUD   /roles                   → Role management
CRUD   /permissions             → Permission management
CRUD   /user-roles              → User-role assignment
```

---

## 7. SERVICES & POLICIES

### 7.1 BookingService
**File**: [app/Services/BookingService.php](app/Services/BookingService.php)

**Key Methods**:
- `create(array $data, int $userId)` - Create booking with conflict checking
- `cancel(Booking $booking)` - Cancel booking
- `hasConflict()` - Check for time conflicts

**Features**:
- ✓ Database transactions
- ✓ Time slot conflict detection
- ✓ Equipment association
- ✓ Only checks approved bookings for conflicts

### 7.2 ApprovalService
**File**: [app/Services/ApprovalService.php](app/Services/ApprovalService.php)

**Key Methods**:
- `approve(Booking $booking, int $adminId, string $remarks)` - Approve booking
- `reject(Booking $booking, int $adminId, string $remarks)` - Reject booking

**Features**:
- ✓ Creates approval record
- ✓ Creates user notifications
- ✓ Creates equipment logs on approval
- ✓ Database transactions

### 7.3 BookingPolicy
**File**: [app/Policies/BookingPolicy.php](app/Policies/BookingPolicy.php)

```php
public function delete(User $user, Booking $booking)
{
    return $user->user_id === $booking->user_id && $booking->status === 'pending';
}

public function view(User $user, Booking $booking)
{
    return $user->user_id === $booking->user_id;
}
```

**Authorization Rules**:
- ✓ Users can only view/delete their own bookings
- ✓ Can only delete if booking is still pending
- ✓ Used with `$this->authorize('view|delete', $booking)`

---

## 8. IDENTIFIED PROBLEMS & ROOT CAUSES

### PROBLEM 1: Identical Login Responses
**Severity**: 🔴 CRITICAL

**Description**:
Both web (`POST /login`) and API (`POST /api/login`) routes use the same controller method and return identical JSON responses.

**Evidence**:
- Routes: [routes/web.php#L9](routes/web.php#L9) and [routes/api.php#L5](routes/api.php#L5)
- Controller: [AuthController.php#L65-L113](app/Http/Controllers/Auth/AuthController.php#L65-L113)
- Both call `AuthController::login()` method
- Response structure at lines 101-113 is identical

**Impact**:
- Frontend cannot differentiate between successful web/API login
- Web clients receive token unnecessarily
- API clients might use response for session instead of token
- Creates confusion about authentication method

**User Experience**:
- "I'm getting the same output every time I login"
- Cannot determine if they should use token or session
- No guidance on next steps after login

### PROBLEM 2: No Response Differentiation by User Type
**Severity**: 🔴 CRITICAL

**Description**:
Student and admin logins return nearly identical responses with only the roles array differing.

**Evidence**:
- Same response structure for all users
- Roles array is only differentiator
- No permission list included
- No "access_level" or "user_type" field

**Example Comparison**:

Student Response:
```json
{"roles": ["student"]}
```

Admin Response:
```json
{"roles": ["administrator"]}
```

**Impact**:
- Frontend must manually parse roles to determine dashboard
- No indication of available endpoints
- Permissions not included in response
- Inconsistent with many modern APIs (include permissions array)

### PROBLEM 3: Web Route Returns JSON Instead of Redirecting
**Severity**: 🟠 HIGH

**Description**:
The web login route returns JSON response instead of redirecting to dashboard.

**Evidence**:
- [routes/web.php#L9](routes/web.php#L9): `POST /login` → `AuthController::login()`
- Lines 101-113 in AuthController show JSON response
- No redirect logic for session-based users

**Expected Behavior**:
```php
// Web login should redirect
if ($request->wantsJson()) {
    // API request
    return response()->json([...]);
} else {
    // Web request
    return redirect()->intended('/dashboard');
}
```

**Impact**:
- Web users get JSON response instead of page load
- Session not properly established for subsequent requests
- Cannot track session for CSRF protection
- May cause issues with form submissions

### PROBLEM 4: Single Active Token Per User
**Severity**: 🟠 HIGH

**Description**:
Token revocation deletes ALL tokens, allowing only one active session.

**Evidence**:
- [AuthController.php#L93-95](app/Http/Controllers/Auth/AuthController.php#L93-95):
```php
$user->tokens()->delete();  // Deletes ALL tokens
$token = $user->createToken('auth_token')->plainTextToken;
```

**Impact**:
- ✗ No multi-device support
- ✗ Logging in on mobile logs out desktop
- ✗ No refresh token mechanism
- ✗ Cannot maintain multiple API connections

**Modern Standard**:
- Keep existing tokens active
- Implement token expiration (30 days)
- OR implement refresh token strategy
- OR keep last N tokens

### PROBLEM 5: Registration Also Returns Same Structure
**Severity**: 🟡 MEDIUM

**Description**:
Registration endpoint also returns identical JSON to login.

**Evidence**:
- [routes/web.php#L11](routes/web.php#L11) and [routes/api.php#L4](routes/api.php#L4)
- [AuthController.php#L26-L59](app/Http/Controllers/Auth/AuthController.php#L26-L59)
- Returns token even for web registration
- No differentiation between signup contexts

### PROBLEM 6: Incomplete User Information in Response
**Severity**: 🟡 MEDIUM

**Description**:
Login response doesn't include all relevant user information.

**Missing Information**:
- Permissions array (only roles included)
- Created/updated timestamps
- Account status/flags
- Default dashboard redirect URL

**Should Include**:
```json
{
  "user": {
    "user_id": 1,
    "full_name": "Admin User",
    "roles": ["administrator"],
    "permissions": ["manage-laboratory", "approve-booking", ...],
    "is_admin": true,
    "created_at": "2026-04-22T10:30:00Z"
  },
  "dashboard_url": "/admin/dashboard"  // For web
}
```

### PROBLEM 7: Middleware Registration Works But Not Leveraged
**Severity**: 🟡 MEDIUM

**Description**:
Custom middleware exists but login doesn't use it for response differentiation.

**Evidence**:
- [Kernel.php#L55-L65](app/Http/Kernel.php#L55-L65): Middleware properly registered
- CheckRole, AdminMiddleware, CheckPermission exist
- But login controller doesn't use them to shape response

---

## 9. WHAT IS WORKING CORRECTLY

✓ **Authentication Logic**
- Password hashing with Hash::check()
- Rate limiting (5 attempts/60 seconds)
- User lookup by email
- Error messages appropriate for security

✓ **Sanctum Token Generation**
- Tokens properly created and stored
- Token format correct (id|hash)
- Personal access tokens table properly populated

✓ **Role/Permission System**
- Roles correctly assigned to users
- Permissions properly seeded
- Many-to-many relationships work
- Helper methods functional (hasRole, hasPermission, isAdmin)

✓ **Route Protection**
- Middleware properly prevents unauthorized access
- 403 returned for unauthorized requests
- 401 returned for unauthenticated requests

✓ **Booking System**
- Conflict detection works
- Equipment association correct
- Status transitions logical

✓ **Approval Workflow**
- Approval records created correctly
- Notifications generated
- Equipment logs created on approval

✓ **Database Design**
- Proper relationships
- Foreign keys configured
- Pivot tables correct

✓ **Authorization Policies**
- BookingPolicy works
- Users can only access own bookings
- Status-based deletion rules enforced

---

## 10. RECOMMENDED FIXES (PRIORITY ORDER)

### Fix 1: Separate Web and API Login Controllers (CRITICAL)
Create dedicated login methods for web vs API:

**Location**: `AuthController.php`

**Change**:
```php
// Keep login() for API
public function login(Request $request) { ... }

// Create loginWeb() for web
public function loginWeb(Request $request) { ... }
```

**Route Changes**:
```php
// web.php
Route::post('/login', [AuthController::class, 'loginWeb']);

// api.php
Route::post('/login', [AuthController::class, 'login']);
```

### Fix 2: Add Role-Based Response Differentiation (CRITICAL)
Include permissions and user type in response:

```json
{
  "message": "Logged in successfully.",
  "token": "1|...",
  "user": {
    "user_id": 1,
    "full_name": "Admin User",
    "school_email": "admin@school.edu",
    "roles": ["administrator"],
    "permissions": ["approve-booking", "manage-laboratory", ...],
    "is_admin": true
  },
  "dashboard_url": "/admin/dashboard"
}
```

### Fix 3: Fix Token Revocation Logic (HIGH)
Remove `$user->tokens()->delete()` or implement token management:

```php
// Option 1: Don't delete old tokens (simplest)
// $user->tokens()->delete();  // REMOVE THIS LINE
$token = $user->createToken('auth_token')->plainTextToken;

// Option 2: Set expiration time
$token = $user->createToken('auth_token', ['*'], Carbon::now()->addDays(30))->plainTextToken;

// Option 3: Keep last N tokens
$tokens = $user->tokens()->orderBy('created_at', 'desc')->get();
if ($tokens->count() > 5) {
    $tokens->slice(5)->each->delete();
}
```

### Fix 4: Make Web Login Redirect (HIGH)
Detect request type and respond appropriately:

```php
public function loginWeb(Request $request)
{
    // ... validation and auth logic ...
    
    if ($request->wantsJson()) {
        return response()->json([...], 200);
    }
    
    // For browser requests, redirect
    return redirect()->intended(
        $user->isAdmin() ? '/admin/dashboard' : '/dashboard'
    );
}
```

### Fix 5: Add Logout Clearing (MEDIUM)
Make logout work correctly:

```php
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    
    return response()->json([
        'message' => 'Logged out successfully.',
    ]);
}
```

### Fix 6: Include Full Permission Data (MEDIUM)
Modify login response to include user permissions:

```php
$user->load('roles.permissions');

return response()->json([
    'message' => 'Logged in successfully.',
    'token'   => $token,
    'user'    => [
        'user_id'      => $user->user_id,
        'full_name'    => $user->full_name,
        'school_email' => $user->school_email,
        'roles'        => $user->roles->pluck('name'),
        'permissions'  => $user->getAllPermissions()->pluck('name'),
        'is_admin'     => $user->isAdmin(),
    ],
]);
```

---

## 11. TESTING SCENARIOS

### Test 1: Student Login via API
```bash
POST /api/login
{
  "school_email": "john@school.edu",
  "password": "password123"
}

Expected Response (200):
{
  "message": "Logged in successfully.",
  "token": "1|...",
  "user": {
    "roles": ["student"],
    "permissions": ["create-booking", "view-booking", ...]
  }
}
```

### Test 2: Admin Login via API
```bash
POST /api/login
{
  "school_email": "admin@school.edu",
  "password": "password123"
}

Expected Response (200):
{
  "message": "Logged in successfully.",
  "token": "1|...",
  "user": {
    "roles": ["administrator"],
    "permissions": [all 17 permissions],
    "is_admin": true
  }
}
```

### Test 3: Student Accessing Admin Route
```bash
GET /api/admin/approvals
Authorization: Bearer <student_token>

Expected Response (403):
{
  "message": "Unauthorized. You do not have permission to access this resource."
}
```

### Test 4: Admin Accessing Admin Route
```bash
GET /api/admin/approvals
Authorization: Bearer <admin_token>

Expected Response (200):
[approved bookings list]
```

---

## CONCLUSION

The authentication system is **structurally sound** but suffers from **critical response design flaws**:

1. **Duplicate endpoints** returning identical responses
2. **No user differentiation** in response
3. **Web/API confusion** due to unified controller
4. **Single-token limitation** preventing multi-device use
5. **Incomplete response data** missing permissions

**Priority Actions**:
1. Separate web and API login methods
2. Add role/permission differentiation to responses
3. Fix token revocation strategy
4. Make web login redirect (not return JSON)
5. Include full permission data in responses

These changes will make the system more robust, secure, and aligned with modern API design principles.
