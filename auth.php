<?php
/**
 * Authentication Helper
 * Week 2: Session-based auth with role-based access control
 */

session_start();

// Sample user database (Week 2 - in-memory)
// In future weeks, this will connect to MySQL
$users = [
    'customer1@email.com' => [
        'password' => password_hash('customer123', PASSWORD_BCRYPT),
        'name' => 'Ahmed Hassan',
        'role' => 'customer'
    ],
    'customer2@email.com' => [
        'password' => password_hash('customer456', PASSWORD_BCRYPT),
        'name' => 'Fatima Ali',
        'role' => 'customer'
    ],
    'barber1@email.com' => [
        'password' => password_hash('barber123', PASSWORD_BCRYPT),
        'name' => 'Ali Khan',
        'role' => 'barber'
    ],
    'barber2@email.com' => [
        'password' => password_hash('barber456', PASSWORD_BCRYPT),
        'name' => 'Usman Raza',
        'role' => 'barber'
    ],
    'admin@email.com' => [
        'password' => password_hash('admin123', PASSWORD_BCRYPT),
        'name' => 'Admin User',
        'role' => 'admin'
    ],
];

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Get current logged-in user
 */
function getCurrentUser() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user's role
 */
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user's name
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Check if user has a specific role
 */
function hasRole($role) {
    return getCurrentRole() === $role;
}

/**
 * Check if user has any of the given roles
 */
function hasAnyRole($roles) {
    $currentRole = getCurrentRole();
    return in_array($currentRole, $roles);
}

/**
 * Require authentication - redirect to login if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Require specific role - redirect to home if user doesn't have role
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Require any of the given roles
 */
function requireAnyRole($roles) {
    requireLogin();
    if (!hasAnyRole($roles)) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Authenticate user with email and password
 */
function authenticateUser($email, $password) {
    global $users;
    
    if (isset($users[$email])) {
        $user = $users[$email];
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $email;
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
    }
    return false;
}

/**
 * Logout user
 */
function logoutUser() {
    session_destroy();
    header('Location: index.php');
    exit;
}

/**
 * Get all users by role (for demo/admin purposes)
 */
function getUsersByRole($role) {
    global $users;
    $result = [];
    foreach ($users as $email => $user) {
        if ($user['role'] === $role) {
            $result[$email] = $user;
        }
    }
    return $result;
}
?>
