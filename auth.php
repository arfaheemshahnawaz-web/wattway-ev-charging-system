<?php
// auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Require user to be logged in
 * 
 * @param string|null $role  The role to check (e.g., 'operator', 'admin', 'driver')
 */
function require_login($role = null) {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    // If role check is required
    if ($role && ($_SESSION['user']['role'] ?? null) !== $role) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user']);
}

/**
 * Get current user
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Logout
 */
function logout() {
    session_unset();
    session_destroy();
}
