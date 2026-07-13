<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * Session Helper
 * --------------------------------------------------------------------------
 *
 * Centralized session management for the SchoolERP application.
 *
 * Responsibilities:
 * - Start secure sessions
 * - Log users in
 * - Log users out
 * - Destroy sessions safely
 * - Check authentication status
 * - Retrieve logged-in user information
 * - Perform simple role checks
 *
 * Author: JoeyTech
 * Project: SchoolERP
 * PHP Version: 8.2+
 * --------------------------------------------------------------------------
 */

/**
 * Start a secure PHP session.
 */
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();
    }
}


/**
 * Role constants for user authorization.
 */
const ROLE_ADMIN = 1;
const ROLE_TEACHER = 2;
const ROLE_STUDENT = 3;
const ROLE_PARENT = 4;
const ROLE_ACCOUNTANT = 5;
const ROLE_LIBRARIAN = 6;

/**
 * Session timeout duration in seconds.
 */
const SESSION_TIMEOUT = 1800; // 30 minutes

/**
 * Log a user into the application.
 *
 * @param array $user Authenticated user record.
 */
function loginUser(array $user): void
{
    startSecureSession();

    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['role_id'] = (int) $user['role_id'];
    $_SESSION['first_name'] = (string) $user['first_name'];
    $_SESSION['last_name'] = (string) $user['last_name'];
    $_SESSION['email'] = (string) $user['email'];
    $_SESSION['status'] = (string) $user['status'];

    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();

    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

}

/**
 * Log out the current user.
 */
function logoutUser(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        startSecureSession();
    }

    destroySession();
}

/**
 * Completely destroy the current session.
 */
function destroySession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_unset();

    session_regenerate_id(true);

    session_destroy();
}

/**
 * Determine whether a user is authenticated.
 */
function isLoggedIn(): bool
{
    startSecureSession();

    if (
        !isset(
            $_SESSION['user_id'],
            $_SESSION['role_id'],
            $_SESSION['email']
        )
    ) {
        return false;
    }

    if (isSessionExpired()) {
        destroySession();
        return false;
    }

    refreshSession();

    return true;
}

/**
 * Check whether the current session has expired.
 */
function isSessionExpired(): bool
{
    if (!isset($_SESSION['last_activity'])) {
        return true;
    }

    return (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT;
}

/**
 * Refresh session activity timestamp.
 */
function refreshSession(): void
{
    if (isset($_SESSION['user_id'])) {
        $_SESSION['last_activity'] = time();
    }
}

/**
 * Get the current user's ID.
 */
function currentUserId(): ?int
{
    return isLoggedIn()
        ? (int) $_SESSION['user_id']
        : null;
}

/**
 * Get the current user's role ID.
 */
function currentUserRole(): ?int
{
    return isLoggedIn()
        ? (int) $_SESSION['role_id']
        : null;
}

/**
 * Get the current user's email.
 */
function currentUserEmail(): ?string
{
    return isLoggedIn()
        ? (string) $_SESSION['email']
        : null;
}

/**
 * Get the current user's first name.
 */
function currentUserFirstName(): ?string
{
    return isLoggedIn()
        ? (string) $_SESSION['first_name']
        : null;
}

/**
 * Get the current user's last name.
 */
function currentUserLastName(): ?string
{
    return isLoggedIn()
        ? (string) $_SESSION['last_name']
        : null;
}

/**
 * Get the current user's full name.
 */
function currentUserName(): ?string
{
    if (!isLoggedIn()) {
        return null;
    }

    return trim(
        currentUserFirstName() . ' ' .
        currentUserLastName()
    );
}

/**
 * Get the current user's information as an associative array.
 */
function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => currentUserId(),
        'role_id' => currentUserRole(),
        'first_name' => currentUserFirstName(),
        'last_name' => currentUserLastName(),
        'email' => currentUserEmail(),
        'status' => currentUserStatus(),
    ];
}

/**
 * Get the current user's account status.
 */

function currentUserStatus(): ?string
{
    return isLoggedIn()
        ? (string) $_SESSION['status']
        : null;
}

/**
 * Determine whether the current user has a specific role.
 *
 * Example:
 * hasRole(1) // Administrator
 */
function hasRole(int $role): bool
{
    return currentUserRole() === $role;
}

/**
 * Determine whether the current user is an Administrator.
 */
function isAdmin(): bool
{
    return hasRole(ROLE_ADMIN);
}

/**
 * Determine whether the current user is a Teacher.
 */
function isTeacher(): bool
{
    return hasRole(ROLE_TEACHER);
}

/**
 * Determine whether the current user is a Student.
 */
function isStudent(): bool
{
    return hasRole(ROLE_STUDENT);
}

/**
 * Determine whether the current user is a Parent.
 */
function isParent(): bool
{
    return hasRole(ROLE_PARENT);
}

/**
 * Determine whether the current user is an Accountant.
 */
function isAccountant(): bool
{
    return hasRole(ROLE_ACCOUNTANT);
}

/**
 * Determine whether the current user is a Librarian.
 */
function isLibrarian(): bool
{
    return hasRole(ROLE_LIBRARIAN);
}
