<?php

declare(strict_types=1);

/**
 * ------------------------------------------------------------------------
 * RoleMiddleware.php
 * ------------------------------------------------------------------------
 * SchoolERP
 *
 * Role-based authorization middleware.
 * ------------------------------------------------------------------------
 */

require_once __DIR__ . '/../Helpers/Session.php';

startSecureSession();

/**
 * Restrict access to users with a specific role.
 */
function requireRole(int $requiredRole): void
{
    // Ensure the user is authenticated.
    
    if (
    isset($_SESSION['user_agent']) &&
    ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? ''))
) {

    logoutUser();

    startSecureSession();

    $_SESSION['login_error'] =
        'Your session has expired. Please log in again.';

    header('Location: /SchoolERP/public/auth/login.php');
    exit;
}

    if (!isLoggedIn()) {

        logoutUser();

        startSecureSession();

        $_SESSION['login_error'] = 'Please log in to continue.';

        header('Location: /SchoolERP/public/auth/login.php');
        exit;
    }

    // Check the user's role.
    if (!hasRole($requiredRole)) {

        http_response_code(403);

        include __DIR__ . '/../Views/errors/403.php';

        exit;
    }
}