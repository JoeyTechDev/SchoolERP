<?php

declare(strict_types=1);


/**
 * authenticate.php
 *
 * Handles the login request.
 */

// -----------------------------------------------------------------------
// Start a secure session.
// -----------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Strict',
    ]);
    session_start();
}

// -----------------------------------------------------------------------
// Include the database configuration.
// -----------------------------------------------------------------------
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$pdo = $database->connect();

/**
 * Redirect back to the login page with an error message.
 *
 * @param string $message
 * @return never
 */
function redirectWithError(string $message): never
{
    $_SESSION['login_error'] = $message;

    header('Location: login.php');

    exit;
}

// -----------------------------------------------------------------------
// Restrict access to POST requests only.
// -----------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('Invalid request method.');
}

// -----------------------------------------------------------------------
// Validate the CSRF token.
// -----------------------------------------------------------------------
$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken   = $_SESSION['csrf_token'] ?? '';

if (
    empty($submittedToken)
    || empty($sessionToken)
    || !hash_equals((string) $sessionToken, (string) $submittedToken)
) {
    redirectWithError('Invalid or expired security token. Please try again.');
}

// -----------------------------------------------------------------------
// Validate email.
// -----------------------------------------------------------------------
if ($email === '') {
    redirectWithError('Email is required.');
}

// -----------------------------------------------------------------------
// Validate password.
// -----------------------------------------------------------------------
if ($password === '') {
    redirectWithError('Password is required.');
}

// -----------------------------------------------------------------------
// Validate email format.
// -----------------------------------------------------------------------
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithError('Please enter a valid email address.');
}

// -----------------------------------------------------------------------
// STEP 15.2
// Database lookup begins here.
// Retrieve the active user account using the submitted email address.
// A prepared statement is used to prevent SQL injection.
// -----------------------------------------------------------------------

try {

    $sql = "
        SELECT
            id,
            role_id,
            first_name,
            last_name,
            email,
            password,
            status,
            last_login
        FROM users
        WHERE email = :email
          AND status = 'active'
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':email' => $email
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // -------------------------------------------------------------------
    // If no active user exists with the supplied email address,
    // return a generic error message.
    // -------------------------------------------------------------------
    if (!$user) {
        redirectWithError('Invalid email or password.');
    }

} catch (PDOException $e) {

    // -------------------------------------------------------------------
    // Log the actual database error for debugging.
    // Never expose database errors to end users.
    // -------------------------------------------------------------------
    error_log('[authenticate.php] Database Error: ' . $e->getMessage());

    redirectWithError(
        'An unexpected error occurred. Please try again later.'
    );

}

// -----------------------------------------------------------------------
// STEP 15.3
// Password verification begins here.
// -----------------------------------------------------------------------

if (!is_array($user)) {
    redirectWithError('Invalid email or password.');
}

// -----------------------------------------------------------------------
// Verify the submitted password against the stored password hash.
// -----------------------------------------------------------------------
if (!password_verify($password, $user['password'])) {
    redirectWithError('Invalid email or password.');
}

// -----------------------------------------------------------------------
// Password verified successfully.
// Regenerate the session ID to prevent session fixation attacks.
// -----------------------------------------------------------------------
session_regenerate_id(true);

// -----------------------------------------------------------------------
// Store authenticated user information in the session.
// -----------------------------------------------------------------------
$_SESSION['user_id']    = $user['id'];
$_SESSION['role_id']    = $user['role_id'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name']  = $user['last_name'];
$_SESSION['email']      = $user['email'];
$_SESSION['status']     = $user['status'];

// -----------------------------------------------------------------------
// The login CSRF token is no longer needed.
// -----------------------------------------------------------------------
unset($_SESSION['csrf_token']);

// -----------------------------------------------------------------------
// Update the user's last login timestamp.
// -----------------------------------------------------------------------
try {

    $updateStmt = $pdo->prepare(
        "UPDATE users
         SET last_login = NOW()
         WHERE id = :id"
    );

    $updateStmt->execute([
        ':id' => $user['id']
    ]);

} catch (PDOException $e) {

    error_log(
        '[authenticate.php] Failed to update last_login: ' .
        $e->getMessage()
    );

}

// -----------------------------------------------------------------------
// STEP 15.4
// Role-based redirection begins here.
// -----------------------------------------------------------------------

// Map each role ID to its dashboard.
$roleDashboards = [
    1 => '/SchoolERP/public/admin/dashboard.php',
    2 => '/SchoolERP/public/teacher/dashboard.php',
    3 => '/SchoolERP/public/student/dashboard.php',
    4 => '/SchoolERP/public/parent/dashboard.php',
    5 => '/SchoolERP/public/accountant/dashboard.php',
    6 => '/SchoolERP/public/librarian/dashboard.php',
];

// -----------------------------------------------------------------------
// Retrieve the authenticated user's role.
// -----------------------------------------------------------------------
$roleId = (int) ($_SESSION['role_id'] ?? 0);

// -----------------------------------------------------------------------
// Redirect authenticated users to their assigned dashboard.
// -----------------------------------------------------------------------
if (array_key_exists($roleId, $roleDashboards)) {
    header('Location: ' . $roleDashboards[$roleId]);
    exit;
}

// -----------------------------------------------------------------------
// Unknown role.
// Destroy the session and deny access.
// -----------------------------------------------------------------------
$_SESSION = [];

if (ini_get('session.use_cookies')) {

    $cookieParams = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $cookieParams['path'],
        $cookieParams['domain'],
        $cookieParams['secure'],
        $cookieParams['httponly']
    );
}

session_destroy();

redirectWithError('Access denied.');