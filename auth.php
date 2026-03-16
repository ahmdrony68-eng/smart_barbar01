<?php
/**
 * Authentication Helper
 * Session-based auth with role-based access control using MySQL
 */

require_once __DIR__ . '/database.php';

session_start();

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
 * Get current user's email
 */
function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? null;
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
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, password, name, role FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
    } catch (Exception $e) {
        error_log('Authentication error: ' . $e->getMessage());
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
 * Get all users by role
 */
function getUsersByRole($role) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, name, role, specialization FROM users WHERE role = ? ORDER BY name");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching users: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get user by ID
 */
function getUserById($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, name, role, specialization FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log('Error fetching user: ' . $e->getMessage());
        return null;
    }
}

?>

