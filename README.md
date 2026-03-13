# Smart Barber Booking (Week 1-2)

## Week 2 Update: Authentication & Role-Based Access Control

Role-based session authentication has been added with three user roles:

### Files Added/Modified

**New Files:**
- `auth.php` - Core authentication helper with session management
- `login.php` - Login page with demo credentials
- `logout.php` - Logout handler
- Updated `partials/header.php` - Shows user info and role badge

**Modified Files:**
- `customer.php` - Now requires customer role
- `barber.php` - Now requires barber role
- `admin.php` - Now requires admin role
- `index.php` - Shows login status

### Demo Credentials

**Customer:**
- Email: `customer1@email.com`
- Password: `customer123`

**Barber:**
- Email: `barber1@email.com`
- Password: `barber123`

**Admin:**
- Email: `admin@email.com`
- Password: `admin123`

### Authentication Features

- Session-based login system
- Password hashing with bcrypt
- Role-based access control (RBAC)
- Protected routes with role verification
- Logout functionality
- User info display in header

### Helper Functions (auth.php)

- `isLoggedIn()` - Check if user is authenticated
- `getCurrentUser()` - Get current user email
- `getCurrentRole()` - Get current user role
- `getCurrentUserName()` - Get current user name
- `hasRole($role)` - Check if user has specific role
- `hasAnyRole($roles)` - Check if user has any of given roles
- `requireLogin()` - Redirect to login if not authenticated
- `requireRole($role)` - Require specific role or redirect
- `requireAnyRole($roles)` - Require any of given roles

## Original Structure

Minimal starter project based on your proposal using:

- PHP (plain files)
- Tailwind CSS (CDN)
- Static in-memory sample data

No MySQL/database connection is included in this week-1/2 setup.

## Files

- `index.php` - Home page
- `customer.php` - Customer portal (Week 2: authenticated)
- `barber.php` - Barber dashboard (Week 2: authenticated)
- `admin.php` - Admin dashboard (Week 2: authenticated)
- `data.php` - Static sample services/barbers/slots
- `partials/header.php` and `partials/footer.php` - Shared layout

## Run locally

From this folder, run:

```bash
php -S localhost:8000
```

Then open:

http://localhost:8000

## Implementation Timeline (Updated)

- **Week 1:** ✓ Requirements, ERD, system design (completed)
- **Week 2:** ✓ Authentication and role-based access (completed)
- **Week 3:** Barber profiles, specialization, services module
- **Week 4:** Roster setup, slot generation, double-booking prevention
- **Week 5:** Booking workflow, barber dashboard updates
- **Week 6:** Reporting, analytics, and admin features

## Next steps (Week 3+)

1. ✓ Authentication and role-based sessions (Week 2)
2. Add MySQL schema and DB connection
3. Move from static data to dynamic CRUD
4. Implement booking workflow and slot locking
5. Add barber roster and availability management

