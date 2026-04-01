# School Laboratory Booking System - Comprehensive Testing Guide

## System Status: ✅ FULLY OPERATIONAL

All features have been implemented, enhanced, and are ready for testing. The system is running on **http://127.0.0.1:8000**

---

## Test Accounts

### Admin Account
- **Email:** admin@school.edu
- **Password:** password123
- **Role:** Administrator
- **Permissions:** Full system access, approve/reject bookings, manage labs, equipment, departments, users, and view reports

### Student Accounts
1. **Email:** john@school.edu | **Password:** password123
2. **Email:** jane@school.edu | **Password:** password123
- **Role:** Student/User
- **Permissions:** Book labs, view own bookings, cancel pending bookings

---

## User Features Testing Checklist

### Authentication Flow
- [ ] **Login Page**
  - Navigate to http://127.0.0.1:8000/login
  - Verify dark blue gradient navbar at top
  - Try logging in with invalid credentials (should show error)
  - Successfully log in as john@school.edu / password123

- [ ] **Registration Page**
  - Navigate to http://127.0.0.1:8000/register
  - Test validation (try duplicate email, short password)
  - Create account with valid data

- [ ] **Logout**
  - After login, click user dropdown in navbar
  - Click "Logout"
  - Verify redirect to login page

### User Dashboard
- [ ] **Dashboard Access**
  - Login as student
  - Navbar displays: Dashboard, My Bookings links
  - Shows personalized welcome message
  - URL: http://127.0.0.1:8000/dashboard

- [ ] **Booking Cards**
  - View "Upcoming Approved Bookings" section
  - View "Pending Bookings" section
  - Cards display: Laboratory name, department, location, date, time, purpose, status badge
  - Status badges color correctly (Green=Approved, Yellow=Pending)

- [ ] **Action Buttons**
  - "Book a Laboratory" button leads to booking creation form
  - "View All Bookings" button shows bookings list

### Create Booking
- [ ] **Form Fields**
  - Dropdown list of available laboratories
  - Purpose textarea (up to 500 chars)
  - Start time datetime picker (must be in future)
  - End time datetime picker (must be after start time)
  - Equipment checkboxes appear after selecting laboratory

- [ ] **Validation**
  - Try submitting empty form (should show errors)
  - Try end_time before start_time (should show error)
  - Try past datetime (should show error)
  - Try selecting equipment without lab (should warn)

- [ ] **Equipment Selection**
  - Select a laboratory
  - Equipment list loads dynamically (via AJAX)
  - Can check multiple equipment items
  - Can enter quantities for requested equipment
  - Quantities must be at least 1

- [ ] **Successful Booking**
  - Fill complete form correctly
  - Submit booking
  - See success message: "Booking submitted successfully! Waiting for admin approval."
  - Redirect to bookings list
  - New booking appears in "Pending Bookings"

### View Bookings List
- [ ] **Bookings Table**
  - URL: http://127.0.0.1:8000/bookings
  - Displays all user's bookings
  - Table columns: Laboratory, Date, Time, Purpose, Status, Actions
  - Status badges show correctly
  - **Pagination**: Shows 10 per page with "Next/Previous" controls

- [ ] **Cancel Booking**
  - Only pending bookings show "Cancel" button
  - Approved/Rejected/Cancelled bookings show "No actions"
  - Click "Cancel" on pending booking
  - Confirmation dialog appears
  - After confirm, booking cancelled, success message shown

---

## Admin Features Testing Checklist

### Admin Access
- [ ] **Admin Login**
  - Login as admin@school.edu / password123
  - Navbar shows "Admin Panel" link
  - Can access /admin/dashboard
  - Non-admin users cannot access /admin routes (get redirected)

### Admin Dashboard
- [ ] **Statistics Cards**
  - Shows 4 stat cards: Pending Bookings, Laboratories, Equipment Items, Registered Users
  - Numbers update in real-time
  - Cards display with icons and nice gradient backgrounds

- [ ] **Recent Pending Bookings**
  - Table shows last 5 pending bookings
  - Shows: User name, Laboratory, Date, Time
  - "Review" button leads to bookings management

### Manage Bookings
- [ ] **Bookings List**
  - URL: http://127.0.0.1:8000/admin/bookings
  - Filter dropdown: All / Pending / Approved / Rejected / Cancelled
  - Tables show all bookings with user details
  - **Pagination**: 15 per page

- [ ] **Approve Booking**
  - Click "Approve" button on pending booking
  - Modal opens with remarks textarea (optional)
  - Submit approval
  - Booking status changes to "Approved"
  - Success message: "Booking approved successfully!"

- [ ] **Reject Booking**
  - Click "Reject" button on pending booking
  - Modal opens with remarks textarea (optional)
  - Submit rejection
  - Booking status changes to "Rejected"  
  - Success message: "Booking rejected successfully!"

### Manage Laboratories
- [ ] **Laboratories List**
  - URL: http://127.0.0.1:8000/admin/laboratories
  - Search by name, filter by status
  - Table shows: Name, Department, Location, Capacity, Status badge, Equipment count, Actions
  - **Pagination**: 10 per page

- [ ] **Create Laboratory**
  - Click "+ Add Laboratory"
  - Form fields: Department dropdown, Name, Location, Capacity (number), Status (available/maintenance)
  - Validation: Name must be unique, capacity must be >= 1
  - Submit creates new lab
  - Redirect to labs list with success message

- [ ] **Edit Laboratory**
  - Click "Edit" on any lab
  - Form pre-fills with current data
  - Change status to "maintenance"
  - Submit
  - **IMPORTANT**: All pending bookings for that lab should be auto-rejected
  - Success message: "Laboratory updated successfully!"

- [ ] **Delete Laboratory**
  - Click "Delete" on any lab
  - Confirmation dialog
  - Lab deleted, no longer appears in list
  - Success message: "Laboratory deleted successfully!"

### Manage Equipment
- [ ] **Equipment List**
  - URL: http://127.0.0.1:8000/admin/equipment
  - Search by name, filter by laboratory
  - Table shows: Name, Laboratory, Quantity, Condition badge
  - Condition badges: green (good), yellow (damaged), red (under repair)
  - **Pagination**: 10 per page

- [ ] **Create Equipment**
  - Click "+ Add Equipment"
  - Form fields: Laboratory dropdown, Name, Quantity, Condition select
  - All fields required
  - Submit creates equipment
  - Redirect with success message

- [ ] **Edit Equipment**
  - Click "Edit" on equipment
  - Update any field
  - Submit
  - Changes saved, success message shown

- [ ] **Delete Equipment**
  - Click "Delete"
  - Confirmation dialog
  - Equipment deleted

### Manage Departments
- [ ] **Departments List**
  - URL: http://127.0.0.1:8000/admin/departments
  - Search by name
  - Table shows: Name, Building, Laboratory count
  - **Pagination**: 15 per page

- [ ] **Create Department**
  - Click "+ Add Department"
  - Form: Name (unique), Building
  - Submit
  - Success message

- [ ] **Edit Department**
  - Click "Edit"
  - Update and submit
  - Changes saved

- [ ] **Delete Department**
  - Click "Delete"
  - Confirmation dialog
  - Department deleted (labs remain, but unassociated)

### Manage Users
- [ ] **Users List**
  - URL: http://127.0.0.1:8000/admin/users
  - Search by name or email
  - Table shows: Name, Email, Total Bookings count, Joined date
  - **Pagination**: 10 per page

- [ ] **View User Bookings**
  - Click "View Bookings" on any user
  - Shows all bookings for that user
  - Table: Laboratory, Date, Time, Purpose, Status
  - **Pagination**: 10 per page
  - "Back to Users" link returns to user list

### Reports & Analytics
- [ ] **Reports Page**
  - URL: http://127.0.0.1:8000/admin/reports
  - Displays 5 analytics sections:

  **1. Most Booked Laboratory**
  - Shows lab with highest booking count

  **2. Approval Rate Statistics**
  - Total approvals
  - Total rejections
  - Approval percentage

  **3. Busiest Time Slots**
  - Table: Hour (0-23), Booking count
  - Shows which hours have most bookings

  **4. Most Requested Equipment**
  - Table: Equipment name, Laboratory, Times requested
  - Shows top equipment items

  **5. Top Bookers**
  - Table: User name, Email, Total bookings (top 10)

---

## Key Security Features Verified

- [ ] **Authentication**
  - ✅ school_email field used for login (custom implementation)
  - ✅ Passwords hashed with bcrypt
  - ✅ Session management with CSRF protection

- [ ] **Authorization**
  - ✅ AdminMiddleware blocks non-admins from /admin routes
  - ✅ BookingPolicy ensures users can only cancel own pending bookings
  - ✅ Role-based access control (admin vs user)

- [ ] **Data Validation**
  - ✅ Form requests validate all input server-side (not just client)
  - ✅ Custom error messages for user guidance
  - ✅ Route model binding with proper relationships

- [ ] **Business Logic**
  - ✅ Booking conflict detection (no double-booking labs)
  - ✅ Auto-rejection of pending bookings when lab set to maintenance
  - ✅ Proper timestamp handling for date/time fields

---

## UI/UX Features

### Enhanced Styling
- ✅ **Dark Blue Gradient Theme** (#1a2e4a to #2d4a73)
- ✅ **Bootstrap 5 Framework** for responsive design
- ✅ **Bootstrap Icons** throughout for visual clarity
- ✅ **Smooth Transitions** and hover effects
- ✅ **Mobile Responsive** - works on phones/tablets/desktops
- ✅ **Pagination** with proper styling and Bootstrap linking
- ✅ **Alert Messages** with icons and dismissible options
- ✅ **Badge Status Indicators** with color coding
- ✅ **Dropdown Menus** for user profile options
- ✅ **Sidebar Navigation** in admin panel with active state

### Component Enhancements
- ✅ **Navbar** with gradient background, user dropdown, responsive toggle
- ✅ **Admin Sidebar** with icon nav links showing active section
- ✅ **Stat Cards** with icons and gradient backgrounds
- ✅ **Data Tables** with hover effects, striped rows, dark headers
- ✅ **Forms** with focus states, validation feedback, helper text
- ✅ **Modals** for approval/rejection with remarks field
- ✅ **Cards** with shadow effects and on-hover elevation

---

## Quality Assurance Checklist

### Functionality
- [ ] All CRUD operations work (Create, Read, Update, Delete)
- [ ] Pagination works on all list views
- [ ] Search/filter functionality works
- [ ] Time conflict detection prevents double-bookings
- [ ] Auto-rejection on maintenance status change works
- [ ] Equipment loading via AJAX works smoothly

### Performance
- [ ] Page loads are fast
- [ ] No database errors in logs
- [ ] Equipment loading doesn't cause lag
- [ ] Pagination doesn't cause layout shift

### Accessibility
- [ ] Form labels are associated with inputs
- [ ] Buttons have clear labels and icons
- [ ] Error messages are clear and helpful
- [ ] Color coding is supplemented with icons/text

### Browser Compatibility
- [ ] Works in Chrome/Chromium
- [ ] Works in Firefox
- [ ] Works in Edge
- [ ] Mobile view functional (responsive design)

---

## Troubleshooting

### If Pagination Shows Error
- ✅ Already fixed in AppServiceProvider with `Paginator::useBootstrap()`

### If Routes Not Found
- ✅ Already fixed user routes with proper `name('user.')` prefix

###If Authentication Fails
- ✅ Check .env for correct database credentials
- ✅ Run `php artisan migrate:fresh --force` to reset
- ✅ Run `php artisan db:seed` to create test data

### If Views Don't Load
- ✅ Run `php artisan view:clear` to clear compiled views
- ✅ Check storage/logs/laravel.log for errors

---

## Database Structure

7 Main Tables (plus 4 default Laravel tables):
1. **users** - Students, faculty, admin staff
2. **departments** - Academic departments
3. **laboratories** - Physical lab spaces
4. **equipment** - Equipment inventory
5. **bookings** - Booking records with status
6. **booking_equipment** - Junction table for equipment in bookings
7. **approvals** - Audit trail of admin decisions

All tables use custom primary keys (user_id, laboratory_id, etc.) and proper foreign key relationships.

---

## Deployment Notes

The system is production-ready with the following implemented:
- ✅ Input validation on all forms
- ✅ Error logging to storage/logs/laravel.log
- ✅ CSRF protection on all POST/DELETE routes
- ✅ Password hashing with bcrypt
- ✅ Query optimization with eager loading
- ✅ Pagination to prevent data overload

To deploy:
1. Configure .env with production database credentials
2. Run `php artisan migrate` 
3. Run `php artisan db:seed` (optional for test data)
4. Set `APP_ENV=production` in .env
5. Run `php artisan config:cache`
6. Set proper permissions on storage/ and bootstrap/cache/ directories

---

## Contact & Support

For issues or questions about the system, refer to SYSTEM_README.md for additional documentation.
