# School Laboratory Booking & Management System

A comprehensive Laravel 8 application for managing school laboratory bookings with role-based access control for students/faculty (users) and laboratory administrators.

## System Overview

The application provides two distinct user roles:
- **Users (Students/Faculty)**: Can book laboratories, view bookings, and manage their own reservations
- **Admins**: Full system access including lab management, equipment tracking, approvals, and analytics

## Features

### User Features
- **Registration & Login**: Create account with school email and secure password
- **Dashboard**: View upcoming approved bookings and pending requests
- **Book a Laboratory**: Select lab, date/time, purpose, and optional equipment
- **Manage Bookings**: View all bookings and cancel pending ones
- **Equipment Selection**: Request specific equipment for bookings

### Admin Features
- **Dashboard**: Summary cards for pending bookings, labs, equipment, and users
- **Manage Bookings**: Review, approve, or reject bookings with optional remarks
- **Labs Management**: Full CRUD for laboratories, toggle maintenance status
- **Equipment Management**: Track equipment, condition, and quantities
- **Departments**: Manage department information
- **Users Management**: View all users and their booking history
- **Reports**: Analytics including busiest hours, top bookers, approval rates

## Technology Stack

- **Framework**: Laravel 8
- **Database**: MySQL
- **Templating**: Blade
- **Frontend**: Bootstrap 5
- **Authentication**: Laravel's native auth system (customized for school_email)

## Database Schema

### Tables (7 total)
1. **users** - user_id, name, school_email (unique), password, role (enum: user/admin)
2. **departments** - department_id, name, building
3. **laboratories** - laboratory_id, department_id (FK), name, location, capacity, status
4. **equipment** - equipment_id, laboratory_id (FK), name, quantity, condition
5. **bookings** - booking_id, user_id (FK), laboratory_id (FK), purpose, start_time, end_time, status
6. **booking_equipment** - bookingequipment_id, booking_id (FK), equipment_id (FK), quantity_requested
7. **approvals** - approval_id, booking_id (FK), admin_id (FK), decision, remarks, decided_at

## Installation & Setup

### 1. Database Setup
Migrations have been created and run. The database is already populated with:
- 1 admin user
- 2 test users
- 1 department (Physics)
- 2 laboratories
- 3 equipment items

No additional setup needed - just start the server!

### 2. Start the Server
```bash
cd c:\xampp\htdocs\labsystem
php artisan serve --host=127.0.0.1 --port=8000
```

Then visit: **http://127.0.0.1:8000**

## Test Credentials

### Admin Login
- **Email**: admin@school.edu
- **Password**: password123

### Student Login 1
- **Email**: john@school.edu
- **Password**: password123

### Student Login 2
- **Email**: jane@school.edu
- **Password**: password123

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/AuthController.php
│   │   ├── User/DashboardController.php, BookingController.php
│   │   └── Admin/(7 controllers)
│   ├── Middleware/AdminMiddleware.php
│   ├── Requests/(5 form request classes)
│   └── Kernel.php (with AdminMiddleware registered)
├── Models/(7 models with relationships)
├── Services/(BookingService, LaboratoryService, ApprovalService)
├── Policies/BookingPolicy.php

resources/views/
├── layouts/(app.blade.php, admin.blade.php)
├── auth/(login.blade.php, register.blade.php)
├── user/(dashboard, bookings/)
└── admin/(7 management sections)

routes/
├── web.php (public and user routes)
└── admin.php (admin routes with middleware)

database/
├── migrations/(7 migrations created)
└── seeders/DatabaseSeeder.php
```

## Security Features

✅ **Implemented:**
- Hash::make() for all passwords (never plaintext)
- @csrf token on every form
- auth middleware on all protected routes
- AdminMiddleware for /admin/* routes
- Laravel Policy for booking access control (users can only cancel own pending bookings)
- Form Request validation (all inputs validated server-side)
- Time conflict detection (BookingService checks for overlapping approvals)
- Automatic rejection when lab set to maintenance
- Try-catch blocks with friendly error messages
- Ownership checks before any update/delete

## Color Scheme
- **Dark Blue**: #1a2e4a (navbar, sidebar, buttons, headers)
- **White**: #ffffff (backgrounds, text)
- **Status Badges**: 
  - Pending: Yellow (#ffc107)
  - Approved: Green (#28a745)
  - Rejected: Red (#dc3545)
  - Cancelled: Gray (#6c757d)

## Key Routes

### Public Routes
```
GET  /login                   - Login page
POST /login                   - Process login
GET  /register                - Registration page
POST /register                - Create new account
POST /logout                  - Logout (auth required)
```

### User Routes (auth middleware)
```
GET  /dashboard               - User dashboard
GET  /bookings                - List user's bookings
GET  /bookings/create         - Booking form
POST /bookings                - Submit booking
DELETE /bookings/{id}         - Cancel booking
```

### Admin Routes (auth + admin middleware)
```
GET  /admin/dashboard         - Admin dashboard
GET  /admin/bookings          - All bookings with filter
POST /admin/bookings/{id}/approve  - Approve booking
POST /admin/bookings/{id}/reject   - Reject booking
GET  /admin/laboratories      - List labs
POST /admin/laboratories      - Create lab
PUT  /admin/laboratories/{id} - Update lab
DELETE /admin/laboratories/{id} - Delete lab
... (equipment, departments, users, reports similarly)
```

## Architecture Highlights

### Service Layer Pattern
- **BookingService**: Handles booking creation, cancellation, conflict checking
- **LaboratoryService**: Manages lab status, auto-rejects pending bookings on maintenance
- **ApprovalService**: Handles approval/rejection with approval record creation

### Custom Authentication
- Uses school_email instead of email via User::getAuthIdentifierName()
- Custom AdminMiddleware checks auth()->user()->role === 'admin'

### Form Requests
- Centralized validation in Request classes
- Custom error messages in each request class
- Server-side validation only (no frontend validation)

## Business Logic

### Booking Workflow
1. User selects lab and time slot
2. BookingService checks for time conflicts with approved bookings
3. If no conflict, booking created with status='pending'
4. Admin reviews pending bookings
5. Admin approves/rejects with optional remarks
6. Approval record created, booking status updated
7. User can cancel only if booking is pending

### Lab Maintenance Workflow
1. Admin sets lab status to 'maintenance'
2. LaboratoryService automatically rejects all pending bookings for that lab
3. Students are notified of rejection via e-mail (feature ready for integration)

### Time Conflict Detection
BookingService::hasConflict() checks if:
- Requested time overlaps with any approved booking
- Time range includes entire existing bookings
- Existing bookings include entire requested range

## Notes

- All timestamps use Laravel's standard created_at and updated_at
- Material icons used in UI (via Bootstrap icons CDN)
- Responsive design works on mobile and desktop
- Pagination on all list pages (10-15 per page)
- Session-based flash messages for success/error

## Testing the System

1. **Login as Admin**: admin@school.edu / password123
2. **Add a Department**: Go to Departments, click Add
3. **Add a Laboratory**: Go to Laboratories, assign to department
4. **Add Equipment**: Go to Equipment, select laboratory
5. **Login as User**: john@school.edu / password123
6. **Book a Lab**: Dashboard → Book a Lab → Fill form
7. **Admin Approval**: Login as admin, go to Bookings, approve/reject
8. **View Reports**: As admin, check Reports page for analytics

## Future Enhancements

- Email notifications for booking decisions
- SMS alerts for pending approvals
- Custom time slot templates (e.g., standard lab hours)
- Equipment reservation separate from lab bookings
- Calendar view for available time slots
- Export reports to PDF/Excel
- Recurring bookings
- Student rating system for labs
- Resource usage statistics

---

**Version**: 1.0  
**Created**: March 31, 2026  
**Laravel Version**: 8.x  
**Database**: MySQL with 7 core tables
